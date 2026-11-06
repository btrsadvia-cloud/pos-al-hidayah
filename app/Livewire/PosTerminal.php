<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\PosService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PosTerminal extends Component
{
    // Search & Filter
    public string $search = '';
    public ?int $selectedCategoryId = null;

    // Cart: array of items [product_id => ['product_id', 'name', 'barcode', 'unit', 'price', 'tier', 'quantity', 'max_stock', 'item_discount', 'subtotal']]
    public array $cart = [];

    // Transaction parameters
    public ?int $customerId = null;
    public int $invoiceDiscount = 0;
    public string $paymentMethod = 'cash'; // 'cash', 'qris', 'transfer'
    public int $paidAmount = 0;
    public ?string $paymentReference = '';
    public ?string $note = '';

    // UI state
    public bool $showSuccessModal = false;
    public ?int $lastSaleId = null;
    public ?string $lastInvoiceNumber = null;
    public int $lastChangeAmount = 0;
    public int $lastTotal = 0;
    public int $lastPaidAmount = 0;
    public string $lastPaymentMethod = 'cash';
    public array $lastSaleItems = [];
    public ?string $lastCustomerName = null;
    public ?string $lastSoldAt = null;

    public bool $showShortcutsModal = false;
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user->isCashier() && !$user->hasOpenShift()) {
            return redirect()->route('shift.index')
                ->with('warning', 'Buka shift terlebih dahulu sebelum menggunakan POS.');
        }
    }

    public function updatedSearch()
    {
        // When barcode scanner sends Enter after barcode, it can trigger quick-add if exact barcode matches
        $trimmed = trim($this->search);
        if ($trimmed !== '') {
            $exactProduct = Product::where('barcode', $trimmed)->first();
            if ($exactProduct) {
                $this->addToCart($exactProduct->id);
                $this->search = '';
            }
        }
    }

    public function handleBarcodeScan()
    {
        $trimmed = trim($this->search);
        if ($trimmed === '') {
            return;
        }

        $product = Product::where('barcode', $trimmed)
            ->orWhere('name', 'like', "%{$trimmed}%")
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->search = '';
            $this->clearError();
        } else {
            $this->errorMessage = "Produk dengan barcode/nama '{$trimmed}' tidak ditemukan.";
        }
    }

    public function addToCart(int $productId)
    {
        $this->clearError();
        $product = Product::with(['prices', 'unit'])->find($productId);

        if (!$product) {
            $this->errorMessage = 'Produk tidak ditemukan.';
            return;
        }

        if ($product->stock <= 0) {
            $this->errorMessage = "Stok produk '{$product->name}' habis.";
            return;
        }

        $currentQty = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + 1;

        if ($newQty > $product->stock) {
            $this->errorMessage = "Stok '{$product->name}' tidak mencukupi (sisa stok: {$product->stock}).";
            return;
        }

        $priceInfo = $product->getPriceForQty($newQty);

        $this->cart[$productId] = [
            'product_id'    => $product->id,
            'name'          => $product->name,
            'barcode'       => $product->barcode,
            'unit'          => $product->unit->name ?? 'Pcs',
            'price'         => (int) $priceInfo['price'],
            'tier'          => $priceInfo['tier'],
            'quantity'      => $newQty,
            'max_stock'     => (int) $product->stock,
            'item_discount' => isset($this->cart[$productId]) ? $this->cart[$productId]['item_discount'] : 0,
            'subtotal'      => ((int) $priceInfo['price'] * $newQty) - (isset($this->cart[$productId]) ? $this->cart[$productId]['item_discount'] : 0),
        ];

        $this->recalculateAllTiers();
        $this->recalculatePaymentDefaults();
    }

    public function updateQuantity(int $productId, int $newQty)
    {
        $this->clearError();
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($newQty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::with(['prices'])->find($productId);
        if (!$product) {
            $this->removeFromCart($productId);
            return;
        }

        if ($newQty > $product->stock) {
            $this->errorMessage = "Stok '{$product->name}' tidak mencukupi (sisa stok: {$product->stock}, diminta: {$newQty}).";
            $this->cart[$productId]['quantity'] = $product->stock;
            $newQty = $product->stock;
        } else {
            $this->cart[$productId]['quantity'] = $newQty;
        }

        $priceInfo = $product->getPriceForQty($newQty);
        $this->cart[$productId]['price'] = (int) $priceInfo['price'];
        $this->cart[$productId]['tier'] = $priceInfo['tier'];

        $sub = ($this->cart[$productId]['price'] * $newQty) - $this->cart[$productId]['item_discount'];
        $this->cart[$productId]['subtotal'] = max(0, $sub);

        $this->recalculatePaymentDefaults();
    }

    public function incrementQuantity(int $productId)
    {
        if (isset($this->cart[$productId])) {
            $this->updateQuantity($productId, $this->cart[$productId]['quantity'] + 1);
        }
    }

    public function decrementQuantity(int $productId)
    {
        if (isset($this->cart[$productId])) {
            $this->updateQuantity($productId, $this->cart[$productId]['quantity'] - 1);
        }
    }

    public function updateItemDiscount(int $productId, int $discount)
    {
        $this->clearError();
        if (!isset($this->cart[$productId])) {
            return;
        }

        $discount = max(0, $discount);
        $gross = $this->cart[$productId]['price'] * $this->cart[$productId]['quantity'];
        if ($discount > $gross) {
            $discount = $gross;
        }

        $this->cart[$productId]['item_discount'] = $discount;
        $this->cart[$productId]['subtotal'] = $gross - $discount;

        $this->recalculatePaymentDefaults();
    }

    public function removeFromCart(int $productId)
    {
        unset($this->cart[$productId]);
        $this->clearError();
        $this->recalculatePaymentDefaults();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->invoiceDiscount = 0;
        $this->paidAmount = 0;
        $this->paymentReference = '';
        $this->note = '';
        $this->clearError();
    }

    public function setQuickCash(int $amount)
    {
        $this->paidAmount = $amount;
    }

    public function setExactCash()
    {
        $this->paidAmount = $this->calculateTotal();
    }

    public function selectCategory(?int $categoryId)
    {
        $this->selectedCategoryId = $categoryId;
    }

    public function calculateSubtotal(): int
    {
        $sub = 0;
        foreach ($this->cart as $item) {
            $sub += ($item['price'] * $item['quantity']);
        }
        return $sub;
    }

    public function calculateTotalDiscount(): int
    {
        $itemDiscounts = 0;
        foreach ($this->cart as $item) {
            $itemDiscounts += ($item['item_discount'] ?? 0);
        }
        return $itemDiscounts + max(0, (int) $this->invoiceDiscount);
    }

    public function calculateTotal(): int
    {
        $subtotal = $this->calculateSubtotal();
        $discount = $this->calculateTotalDiscount();
        return max(0, $subtotal - $discount);
    }

    public function calculateChange(): int
    {
        if ($this->paymentMethod !== 'cash') {
            return 0;
        }
        $total = $this->calculateTotal();
        return max(0, $this->paidAmount - $total);
    }

    public function recalculateAllTiers()
    {
        foreach ($this->cart as $productId => $item) {
            $product = Product::with('prices')->find($productId);
            if ($product) {
                $priceInfo = $product->getPriceForQty($item['quantity']);
                $this->cart[$productId]['price'] = (int) $priceInfo['price'];
                $this->cart[$productId]['tier'] = $priceInfo['tier'];
                $gross = (int) $priceInfo['price'] * $item['quantity'];
                $this->cart[$productId]['subtotal'] = max(0, $gross - $item['item_discount']);
            }
        }
    }

    public function recalculatePaymentDefaults()
    {
        $total = $this->calculateTotal();
        if ($this->paymentMethod !== 'cash') {
            $this->paidAmount = $total;
        } elseif ($this->paidAmount < $total) {
            $this->paidAmount = $total;
        }
    }

    public function updatedPaymentMethod($value)
    {
        if ($value !== 'cash') {
            $this->paidAmount = $this->calculateTotal();
        }
    }

    public function updatedInvoiceDiscount($value)
    {
        $this->clearError();
        $this->recalculatePaymentDefaults();
    }

    public function checkout(PosService $posService)
    {
        $this->clearError();
        $user = auth()->user();

        if (empty($this->cart)) {
            $this->errorMessage = 'Keranjang belanja masih kosong.';
            return;
        }

        $total = $this->calculateTotal();
        if ($this->paymentMethod === 'cash' && $this->paidAmount < $total) {
            $this->errorMessage = 'Uang tunai pembayaran kurang dari total belanja.';
            return;
        }

        $items = [];
        foreach ($this->cart as $item) {
            $items[] = [
                'product_id'    => $item['product_id'],
                'quantity'      => $item['quantity'],
                'item_discount' => $item['item_discount'] ?? 0,
            ];
        }

        $payload = [
            'items'             => $items,
            'customer_id'       => $this->customerId ?: null,
            'discount'          => $this->invoiceDiscount,
            'payment_method'    => $this->paymentMethod,
            'paid_amount'       => $this->paymentMethod === 'cash' ? $this->paidAmount : $total,
            'payment_reference' => $this->paymentReference,
            'note'              => $this->note,
        ];

        try {
            $sale = $posService->checkout($payload, $user, $user->currentShift());

            // Prepare receipt data
            $this->lastSaleId = $sale->id;
            $this->lastInvoiceNumber = $sale->invoice_number;
            $this->lastTotal = $sale->total;
            $this->lastPaidAmount = $sale->paid_amount;
            $this->lastChangeAmount = $sale->change_amount;
            $this->lastPaymentMethod = $sale->payment_method;
            $this->lastSoldAt = $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
            $this->lastCustomerName = $sale->customer->name ?? 'Pelanggan Umum';

            $sale->load('items');
            $this->lastSaleItems = $sale->items->map(fn($it) => [
                'name'     => $it->product_name,
                'qty'      => $it->quantity,
                'unit'     => $it->unit_name,
                'price'    => $it->unit_price,
                'discount' => $it->item_discount,
                'subtotal' => $it->subtotal,
            ])->toArray();

            $this->showSuccessModal = true;
            $this->clearCart();
            $this->successMessage = "Transaksi {$sale->invoice_number} berhasil diselesaikan!";
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $this->errorMessage = implode(' ', $errors);
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat memproses checkout: ' . $e->getMessage();
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    public function toggleShortcutsModal()
    {
        $this->showShortcutsModal = !$this->showShortcutsModal;
    }

    public function clearError()
    {
        $this->errorMessage = '';
    }

    public function render()
    {
        $productQuery = Product::with(['prices', 'unit'])
            ->whereNull('deleted_at');

        if ($this->selectedCategoryId) {
            $productQuery->where('category_id', $this->selectedCategoryId);
        }

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $productQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        $products = $productQuery->orderBy('name')->limit(24)->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('livewire.pos-terminal', [
            'products'      => $products,
            'categories'    => $categories,
            'customers'     => $customers,
            'subtotal'      => $this->calculateSubtotal(),
            'totalDiscount' => $this->calculateTotalDiscount(),
            'total'         => $this->calculateTotal(),
            'change'        => $this->calculateChange(),
        ]);
    }
}
