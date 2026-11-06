<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosService
{
    /**
     * Process checkout transaction safely with row-level locks.
     *
     * @param array $data
     * @param User $user
     * @param Shift|null $shift
     * @return Sale
     * @throws ValidationException
     */
    public function checkout(array $data, User $user, ?Shift $shift = null): Sale
    {
        // 1. Shift check for cashiers
        if ($user->isCashier()) {
            if (!$shift || !$shift->isOpen()) {
                throw ValidationException::withMessages([
                    'shift' => 'Kasir wajib memiliki shift yang terbuka sebelum melakukan transaksi.',
                ]);
            }
        }

        // 2. Validate items presence
        $rawItems = $data['items'] ?? [];
        if (empty($rawItems)) {
            throw ValidationException::withMessages([
                'items' => 'Keranjang belanja tidak boleh kosong.',
            ]);
        }

        // 3. Discount validations
        $invoiceDiscount = max(0, (int) ($data['discount'] ?? 0));
        $maxCashierDiscountPct = config('pos.cashier_max_discount_percentage', 10);

        return DB::transaction(function () use ($data, $user, $shift, $rawItems, $invoiceDiscount, $maxCashierDiscountPct) {
            // Group and collect items by product_id to prevent duplicates/deadlocks
            $productIds = collect($rawItems)->pluck('product_id')->unique()->sort()->values();

            // Lock products in sorted order to avoid deadlock
            $lockedProducts = Product::with(['prices', 'unit'])
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $preparedItems = [];
            $rawSubtotal = 0;
            $totalItemDiscount = 0;

            foreach ($rawItems as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) ($item['quantity'] ?? 0);
                $itemDiscount = max(0, (int) ($item['item_discount'] ?? 0));

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Jumlah barang harus lebih dari 0.',
                    ]);
                }

                $product = $lockedProducts->get($productId);
                if (!$product) {
                    throw ValidationException::withMessages([
                        'product' => "Produk dengan ID {$productId} tidak ditemukan.",
                    ]);
                }

                // Revalidate stock
                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Stok produk '{$product->name}' tidak mencukupi (sisa stok: {$product->stock}, diminta: {$quantity}).",
                    ]);
                }

                // Calculate tiered price based on quantity
                $priceInfo = $product->getPriceForQty($quantity);
                $unitPrice = (int) $priceInfo['price'];

                $lineOriginal = $unitPrice * $quantity;
                if ($itemDiscount > $lineOriginal) {
                    $itemDiscount = $lineOriginal;
                }

                $lineSubtotal = $lineOriginal - $itemDiscount;
                $rawSubtotal += $lineOriginal;
                $totalItemDiscount += $itemDiscount;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'item_discount' => $itemDiscount,
                    'line_subtotal' => $lineSubtotal,
                    'price_info' => $priceInfo,
                ];
            }

            $totalAllDiscounts = $totalItemDiscount + $invoiceDiscount;

            // Enforce cashier maximum discount limit
            if ($user->isCashier()) {
                $maxAllowedDiscount = (int) floor(($rawSubtotal * $maxCashierDiscountPct) / 100);
                if ($totalAllDiscounts > $maxAllowedDiscount) {
                    throw ValidationException::withMessages([
                        'discount' => "Kasir dibatasi maksimal diskon {$maxCashierDiscountPct}% (" . format_rupiah($maxAllowedDiscount) . "). Total diskon saat ini: " . format_rupiah($totalAllDiscounts) . ". Hubungi admin untuk diskon lebih besar.",
                    ]);
                }
            }

            $finalTotal = max(0, $rawSubtotal - $totalAllDiscounts);
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $paidAmount = (int) ($data['paid_amount'] ?? 0);

            if ($paymentMethod === 'cash') {
                if ($paidAmount < $finalTotal) {
                    throw ValidationException::withMessages([
                        'paid_amount' => 'Uang tunai yang dibayarkan kurang dari total belanja (Total: ' . format_rupiah($finalTotal) . ', Dibayar: ' . format_rupiah($paidAmount) . ').',
                    ]);
                }
                $changeAmount = $paidAmount - $finalTotal;
            } else {
                // Non-cash (QRIS, transfer)
                $paidAmount = $finalTotal;
                $changeAmount = 0;
            }

            // Generate consecutive invoice number with row-lock
            $invoiceNumber = Sale::generateInvoiceNumber();

            // Create Sale record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $user->id,
                'shift_id' => $shift?->id,
                'subtotal' => $rawSubtotal,
                'discount' => $totalAllDiscounts,
                'total' => $finalTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $paymentMethod,
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => 'completed',
                'note' => $data['note'] ?? null,
                'sold_at' => now(),
            ]);

            // Create SaleItems, decrement product stock, and record StockMovements
            foreach ($preparedItems as $prep) {
                /** @var Product $product */
                $product = $prep['product'];
                $qty = $prep['quantity'];
                $unitCost = $product->latestCost();

                $product->decrement('stock', $qty);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_name' => $product->unit->name ?? 'Pcs',
                    'unit_price' => $prep['unit_price'],
                    'quantity' => $qty,
                    'item_discount' => $prep['item_discount'],
                    'subtotal' => $prep['line_subtotal'],
                    'unit_cost' => $unitCost,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'supplier_id' => null,
                    'reason' => 'Penjualan ' . $sale->invoice_number,
                    'user_id' => $user->id,
                    'movement_date' => now()->toDateString(),
                ]);
            }

            // Write audit log
            AuditLog::record(
                action: 'Transaksi Penjualan Selesai',
                subject: $sale,
                newValues: [
                    'invoice' => $sale->invoice_number,
                    'total' => $sale->total,
                    'payment_method' => $sale->payment_method,
                    'items_count' => count($preparedItems),
                ],
                userId: $user->id
            );

            return $sale;
        });
    }

    /**
     * Cancel sale (admin only), restoring stock with new stock movement.
     *
     * @param Sale $sale
     * @param User $admin
     * @param string $reason
     * @return Sale
     */
    public function cancelSale(Sale $sale, User $admin, string $reason = ''): Sale
    {
        if (!$admin->isAdmin()) {
            abort(403, 'Hanya Admin yang berhak membatalkan penjualan.');
        }

        if ($sale->status !== 'completed') {
            throw new \Exception('Hanya transaksi penjualan berstatus selesai yang dapat dibatalkan.');
        }

        return DB::transaction(function () use ($sale, $admin, $reason) {
            $lockedSale = Sale::with('items')->where('id', $sale->id)->lockForUpdate()->firstOrFail();

            foreach ($lockedSale->items as $item) {
                /** @var Product $product */
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->increment('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                        'supplier_id' => null,
                        'reason' => 'Pembatalan Penjualan ' . $lockedSale->invoice_number . ($reason ? ' (' . $reason . ')' : ''),
                        'user_id' => $admin->id,
                        'movement_date' => now()->toDateString(),
                    ]);
                }
            }

            $lockedSale->update([
                'status' => 'cancelled',
                'note' => trim(($lockedSale->note ? $lockedSale->note . ' | ' : '') . 'Dibatalkan oleh ' . $admin->name . ': ' . $reason),
            ]);

            AuditLog::record(
                action: 'Pembatalan Penjualan',
                subject: $lockedSale,
                oldValues: ['status' => 'completed'],
                newValues: ['status' => 'cancelled', 'reason' => $reason],
                userId: $admin->id
            );

            return $lockedSale;
        });
    }
}

