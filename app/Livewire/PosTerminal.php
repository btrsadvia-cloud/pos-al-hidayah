<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\QrisTransaction;
use App\Models\Sale;
use App\Services\BuatQrisService;
use App\Services\PosService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PosTerminal extends Component
{
    // Search & Filter
    public string $search = '';
    public ?int $selectedCategoryId = null;

    // POS Workflow Step: false = Pemilihan Barang (Katalog & Keranjang), true = Layar Pembayaran
    public bool $showPaymentStep = false;

    // Cart: array of items [product_id => ['product_id', 'name', 'barcode', 'unit', 'price', 'tier', 'quantity', 'max_stock', 'item_discount', 'subtotal']]
    public array $cart = [];

    // Transaction parameters
    public ?int $customerId = null;
    public int $invoiceDiscount = 0;
    public int $memberDiscount = 0;
    public int $memberDiscountPercent = 0;
    public string $paymentMethod = 'cash'; // 'cash', 'qris', 'transfer'
    public int $paidAmount = 0;
    public ?string $paymentReference = '';
    public ?string $note = '';

    // UI state
    public bool $showSuccessModal = false;
    public ?int $lastSaleId = null;
    public ?string $lastInvoiceNumber = null;
    public int $lastSubtotal = 0;
    public int $lastDiscount = 0;
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

    // BuatQris Gateway State
    public bool $showQrisModal = false;
    public ?string $qrisTransactionId = null;
    public ?string $qrisQrUrl = null;
    public ?string $qrisImage = null;
    public ?string $qrisPaymentUrl = null;
    public int $qrisAmount = 0;
    public int $qrisTotalAmount = 0;
    public int $qrisAmountUniq = 0;
    public int $qrisAdminFee = 0;
    public ?string $qrisExpiredAt = null;
    public int $qrisSecondsRemaining = 300;
    public string $qrisStatus = 'pending'; // pending, success, expired, failed
    public string $qrisStatusMessage = '';
    public int $lastCheckTimestamp = 0;
    public int $qrisCooldownSeconds = 0;
    public bool $isCreatingQris = false;

    // In-memory cache for fast POS interactions
    public array $cachedCategories = [];
    public array $cachedCustomers = [];
    // Product cache: keyed by product_id for O(1) lookup in addToCart
    public array $cachedProducts = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user->isCashier() && !$user->hasOpenShift()) {
            return redirect()->route('shift.index')
                ->with('warning', 'Buka shift terlebih dahulu sebelum menggunakan POS.');
        }

        $this->cachedCategories = \App\Models\Category::orderBy('name')->get()->toArray();
        $this->cachedCustomers = Customer::orderBy('name')->get()->toArray();

        // Pre-load all active products with prices & unit into memory cache
        // This eliminates per-click DB queries in addToCart() for near-instant response
        $this->cachedProducts = Product::with(['prices', 'unit'])
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('id')
            ->map(fn ($p) => [
                'id'     => $p->id,
                'name'   => $p->name,
                'barcode'=> $p->barcode,
                'stock'  => $p->stock,
                'unit'   => $p->unit?->name ?? 'Pcs',
                'prices' => $p->prices->map(fn ($pr) => [
                    'min_qty' => $pr->min_qty,
                    'price'   => (int) $pr->price,
                    'tier'    => $pr->tier ?? 'ecer',
                ])->sortByDesc('min_qty')->values()->toArray(),
            ])
            ->toArray();
    }

    public function performSearch()
    {
        $this->handleBarcodeScan();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->clearError();
    }

    public function handleBarcodeScan()
    {
        $trimmed = trim($this->search);
        if ($trimmed === '') {
            $this->clearError();
            return;
        }

        // 1. Exact barcode check: instantly add to cart
        $exactProduct = Product::where('barcode', $trimmed)->first();
        if ($exactProduct) {
            $this->addToCart($exactProduct->id);
            $this->search = '';
            $this->clearError();
            return;
        }

        // 2. Name / category / partial barcode check: filter product list
        $matches = Product::whereNull('deleted_at')
            ->where(function ($q) use ($trimmed) {
                $q->where('name', 'like', "%{$trimmed}%")
                  ->orWhere('barcode', 'like', "%{$trimmed}%")
                  ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$trimmed}%"));
            })->exists();

        if (!$matches) {
            $this->errorMessage = "Produk dengan kata kunci '{$trimmed}' tidak ditemukan.";
        } else {
            $this->clearError();
        }
    }

    public function addToCart(int $productId)
    {
        $this->clearError();

        // Use in-memory cache first to avoid per-click DB queries (instant response)
        $cached = $this->cachedProducts[$productId] ?? null;

        if (!$cached) {
            // Fallback: fetch from DB if not in cache (e.g. product added after page load)
            $product = Product::with(['prices', 'unit'])->find($productId);
            if (!$product) {
                $this->errorMessage = 'Produk tidak ditemukan.';
                return;
            }
            // Rebuild cache entry on the fly
            $cached = [
                'id'     => $product->id,
                'name'   => $product->name,
                'barcode'=> $product->barcode,
                'stock'  => $product->stock,
                'unit'   => $product->unit?->name ?? 'Pcs',
                'prices' => $product->prices->map(fn ($pr) => [
                    'min_qty' => $pr->min_qty,
                    'price'   => (int) $pr->price,
                    'tier'    => $pr->tier ?? 'ecer',
                ])->sortByDesc('min_qty')->values()->toArray(),
            ];
            $this->cachedProducts[$productId] = $cached;
        }

        // Read live stock from cache first for zero-latency local response
        $liveStock = $cached['stock'] ?? (Product::where('id', $productId)->value('stock') ?? 0);

        if ($liveStock <= 0) {
            $this->errorMessage = "Stok produk '{$cached['name']}' habis.";
            // Update cache stock
            $this->cachedProducts[$productId]['stock'] = 0;
            return;
        }

        $currentQty = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + 1;

        if ($newQty > $liveStock) {
            $this->errorMessage = "Stok '{$cached['name']}' tidak mencukupi (sisa stok: {$liveStock}).";
            return;
        }

        // Compute tier price from cached price tiers
        $priceInfo = $this->getPriceFromCache($cached, $newQty);

        $this->cart[$productId] = [
            'product_id'    => $cached['id'],
            'name'          => $cached['name'],
            'barcode'       => $cached['barcode'],
            'unit'          => $cached['unit'],
            'price'         => $priceInfo['price'],
            'tier'          => $priceInfo['tier'],
            'quantity'      => $newQty,
            'max_stock'     => $liveStock,
            'item_discount' => isset($this->cart[$productId]) ? $this->cart[$productId]['item_discount'] : 0,
            'subtotal'      => ($priceInfo['price'] * $newQty) - (isset($this->cart[$productId]) ? $this->cart[$productId]['item_discount'] : 0),
        ];

        $this->recalculatePaymentDefaults();
        $this->dispatch('pos-cart-sound');
    }

    /**
     * Get price info from cached price tiers (avoids DB query).
     */
    private function getPriceFromCache(array $cached, int $qty): array
    {
        // Prices are sorted descending by min_qty, pick first that fits
        foreach ($cached['prices'] as $tier) {
            if ($qty >= $tier['min_qty']) {
                return ['price' => $tier['price'], 'tier' => $tier['tier']];
            }
        }
        // Fallback to last (smallest min_qty) or default
        $last = end($cached['prices']);
        return $last ? ['price' => $last['price'], 'tier' => $last['tier']] : ['price' => 0, 'tier' => 'ecer'];
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

        // Use cache for price info and stock for instant response
        $cached = $this->cachedProducts[$productId] ?? null;
        $liveStock = $cached['stock'] ?? (Product::where('id', $productId)->value('stock') ?? 0);

        if (!$cached) {
            // Fallback: full DB query if not cached
            $product = Product::with(['prices'])->find($productId);
            if (!$product) {
                $this->removeFromCart($productId);
                return;
            }
            $liveStock = $product->stock;
            $priceInfo = $product->getPriceForQty($newQty);
        } else {
            $priceInfo = $this->getPriceFromCache($cached, $newQty);
        }

        if ($newQty > $liveStock) {
            $name = $cached['name'] ?? $this->cart[$productId]['name'];
            $this->errorMessage = "Stok '{$name}' tidak mencukupi (sisa stok: {$liveStock}, diminta: {$newQty}).";
            $newQty = $liveStock;
        }

        $this->cart[$productId]['quantity'] = $newQty;
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
            $this->dispatch('pos-cart-sound');
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

    public function proceedToPayment()
    {
        $this->clearError();
        if (empty($this->cart)) {
            $this->errorMessage = 'Keranjang masih kosong. Pilih minimal satu barang terlebih dahulu.';
            return;
        }
        $this->recalculatePaymentDefaults();
        $this->showPaymentStep = true;
    }

    public function backToCart()
    {
        $this->showPaymentStep = false;
        $this->clearError();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->invoiceDiscount = 0;
        $this->memberDiscount = 0;
        $this->memberDiscountPercent = 0;
        $this->paidAmount = 0;
        $this->paymentReference = '';
        $this->note = '';
        $this->showPaymentStep = false;
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
        return $itemDiscounts + $this->memberDiscount + max(0, (int) $this->invoiceDiscount);
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
        if (empty($this->cart)) {
            return;
        }

        $productIds = array_keys($this->cart);
        $products = Product::with('prices')->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($this->cart as $productId => $item) {
            $product = $products->get($productId);
            if ($product) {
                $priceInfo = $product->getPriceForQty($item['quantity']);
                $this->cart[$productId]['price'] = (int) $priceInfo['price'];
                $this->cart[$productId]['tier'] = $priceInfo['tier'];
                $gross = (int) $priceInfo['price'] * $item['quantity'];
                $this->cart[$productId]['subtotal'] = max(0, $gross - ($item['item_discount'] ?? 0));
            }
        }
    }

    public function updatedCustomerId($value)
    {
        $this->customerId = $value ? (int) $value : null;
        $this->clearError();
        $this->recalculateMemberDiscount();
        $this->recalculatePaymentDefaults();
    }

    public function selectCustomer(?int $id): void
    {
        $this->customerId = $id ? (int) $id : null;
        $this->clearError();
        $this->recalculateMemberDiscount();
        $this->recalculatePaymentDefaults();
    }

    public function isMemberSelected(): bool
    {
        if (!$this->customerId) {
            return false;
        }
        $customer = collect($this->cachedCustomers)->firstWhere('id', $this->customerId);
        if (!$customer) {
            $c = Customer::find($this->customerId);
            $customer = $c ? $c->toArray() : null;
        }
        return $customer ? (!empty($customer['is_member']) && $customer['is_member'] != 0) : false;
    }

    public function recalculateMemberDiscount(): void
    {
        if (!$this->customerId) {
            $this->memberDiscount = 0;
            $this->memberDiscountPercent = 0;
            return;
        }

        $customer = collect($this->cachedCustomers)->firstWhere('id', $this->customerId);
        if (!$customer) {
            $c = Customer::find($this->customerId);
            $customer = $c ? $c->toArray() : null;
        }

        if ($customer && (!empty($customer['is_member']) && $customer['is_member'] != 0)) {
            $this->memberDiscountPercent = (int) ($customer['member_discount_percent'] ?? 10);
            $subtotal = $this->calculateSubtotal();
            $this->memberDiscount = (int) round($subtotal * ($this->memberDiscountPercent / 100));
        } else {
            $this->memberDiscount = 0;
            $this->memberDiscountPercent = 0;
        }
    }

    public function recalculatePaymentDefaults()
    {
        $this->recalculateMemberDiscount();
        $total = $this->calculateTotal();
        $this->paidAmount = $total;
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

        // Always ensure member discount is calculated freshly before checkout
        $this->recalculateMemberDiscount();

        // If paying via QRIS and BuatQris is configured, initiate dynamic QRIS flow
        if ($this->paymentMethod === 'qris') {
            $qrisService = app(BuatQrisService::class);
            if ($qrisService->isConfigured()) {
                $this->initiateQrisPayment($qrisService);
                return;
            }
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

        $note = $this->note ?: '';
        if ($this->memberDiscount > 0) {
            $note = trim($note . " [Diskon Member {$this->memberDiscountPercent}%: " . format_rupiah($this->memberDiscount) . "]");
        }

        $payload = [
            'items'             => $items,
            'customer_id'       => $this->customerId ?: null,
            'discount'          => $this->invoiceDiscount + $this->memberDiscount,
            'payment_method'    => $this->paymentMethod,
            'paid_amount'       => $this->paymentMethod === 'cash' ? $this->paidAmount : $total,
            'payment_reference' => $this->paymentReference,
            'note'              => $note ?: null,
        ];

        try {
            $sale = $posService->checkout($payload, $user, $user->currentShift());

            // Prepare receipt data
            $this->lastSaleId = $sale->id;
            $this->lastInvoiceNumber = $sale->invoice_number;
            $this->lastSubtotal = $sale->subtotal;
            $this->lastDiscount = $sale->discount;
            $this->lastTotal = $sale->total;
            $this->lastPaidAmount = $sale->paid_amount;
            $this->lastChangeAmount = $sale->change_amount;
            $this->lastPaymentMethod = $sale->payment_method;
            $this->lastSoldAt = $sale->sold_at ? $sale->sold_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB';
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
            \Illuminate\Support\Facades\Cache::flush();
            $this->successMessage = "Transaksi {$sale->invoice_number} berhasil diselesaikan!";
            if ($sale->payment_method === 'qris') {
                $this->dispatch('qris-payment-success', amount: (int) $sale->grand_total);
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $this->errorMessage = implode(' ', $errors);
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat memproses checkout: ' . $e->getMessage();
        }
    }

    /**
     * Start BuatQris Dynamic Payment flow.
     */
    public function initiateQrisPayment(BuatQrisService $qrisService)
    {
        $this->clearError();
        $total = $this->calculateTotal();

        if ($total < 1000) {
            $this->errorMessage = 'Nominal pembayaran QRIS minimal Rp 1.000.';
            return;
        }

        $this->isCreatingQris = true;

        $desc = 'Belanja Toko Al-Hidayah - ' . count($this->cart) . ' item';
        $result = $qrisService->createQris($total, $desc, [
            'user_id' => auth()->id(),
        ]);

        $this->isCreatingQris = false;

        if (!$result['success']) {
            $this->errorMessage = $result['message'] ?? 'Gagal membuat transaksi QRIS di gateway BuatQris.';
            return;
        }

        /** @var QrisTransaction $trx */
        $trx = $result['data'];

        $this->qrisTransactionId = $trx->transaction_id;
        $this->qrisQrUrl = $trx->qr_url;
        $this->qrisImage = $trx->qris_image;
        $this->qrisPaymentUrl = $trx->payment_url;
        $this->qrisAmount = $trx->amount;
        $this->qrisTotalAmount = $trx->total_amount;
        $this->qrisAmountUniq = $trx->amount_uniq;
        $this->qrisAdminFee = $trx->admin_fee;
        $this->qrisExpiredAt = $trx->expired_at ? $trx->expired_at->format('H:i') : now()->addMinutes(5)->format('H:i');
        $this->qrisSecondsRemaining = 300;
        $this->qrisStatus = 'pending';
        $this->qrisStatusMessage = 'Scan kode QRIS di bawah ini dengan aplikasi e-wallet atau mobile banking.';
        $this->lastCheckTimestamp = time();
        $this->qrisCooldownSeconds = 0;
        $this->showQrisModal = true;
    }

    /**
     * Poll status periodically while QR modal is open.
     */
    public function pollQrisStatus(BuatQrisService $qrisService, PosService $posService)
    {
        if (!$this->showQrisModal || empty($this->qrisTransactionId) || $this->qrisStatus === 'success') {
            return;
        }

        // 1. Check local DB first (in case webhook already delivered the success notification)
        $dbTrx = QrisTransaction::where('transaction_id', $this->qrisTransactionId)->first();
        if ($dbTrx && $dbTrx->isSuccess()) {
            $this->qrisStatus = 'success';
            $this->completeQrisCheckout($posService);
            return;
        }

        // 2. Poll API only if at least 20 seconds have passed to strictly avoid HTTP 429
        if ((time() - $this->lastCheckTimestamp) >= 20) {
            $this->checkQrisStatus($qrisService, $posService, true);
        }
    }

    /**
     * Check transaction status directly against BuatQris API.
     */
    public function checkQrisStatus(BuatQrisService $qrisService, PosService $posService, bool $isAutomated = false)
    {
        if (empty($this->qrisTransactionId)) {
            return;
        }

        // Enforce 20s cooldown on manual click
        $elapsed = time() - $this->lastCheckTimestamp;
        if (!$isAutomated && $elapsed < 20) {
            $remaining = 20 - $elapsed;
            $this->qrisCooldownSeconds = $remaining;
            $this->qrisStatusMessage = "Harap tunggu {$remaining} detik sebelum cek status kembali (aturan API max 1x / 20 detik).";
            return;
        }

        $res = $qrisService->checkStatus($this->qrisTransactionId);
        $this->lastCheckTimestamp = time();
        $this->qrisCooldownSeconds = 0;

        if ($res['status'] === 'success') {
            $this->qrisStatus = 'success';
            $this->qrisStatusMessage = 'Pembayaran QRIS lunas!';
            $this->completeQrisCheckout($posService);
        } elseif ($res['status'] === 'expired') {
            $this->qrisStatus = 'expired';
            $this->qrisStatusMessage = 'Waktu pembayaran QRIS telah kedaluwarsa. Silakan batalkan transaksi.';
        } elseif ($res['status'] === 'failed') {
            $this->qrisStatus = 'failed';
            $this->qrisStatusMessage = 'Pembayaran QRIS gagal atau dibatalkan.';
        } else {
            $this->qrisStatus = 'pending';
            $this->qrisStatusMessage = $res['message'] ?? 'Menunggu pembayaran dari pelanggan...';
        }
    }

    /**
     * Finalize POS checkout once QRIS payment is confirmed.
     */
    public function completeQrisCheckout(PosService $posService)
    {
        $user = auth()->user();
        $totalToPay = $this->qrisTotalAmount ?: $this->calculateTotal();

        $items = [];
        foreach ($this->cart as $item) {
            $items[] = [
                'product_id'    => $item['product_id'],
                'quantity'      => $item['quantity'],
                'item_discount' => $item['item_discount'] ?? 0,
            ];
        }

        $note = trim(($this->note ?: '') . ($this->memberDiscount > 0 ? " [Diskon Member {$this->memberDiscountPercent}%: " . format_rupiah($this->memberDiscount) . "]" : '') . ' [BuatQris: ' . $this->qrisTransactionId . ']');

        $payload = [
            'items'             => $items,
            'customer_id'       => $this->customerId ?: null,
            'discount'          => $this->invoiceDiscount + $this->memberDiscount,
            'payment_method'    => 'qris',
            'paid_amount'       => $totalToPay,
            'payment_reference' => $this->qrisTransactionId,
            'note'              => $note,
        ];

        try {
            $sale = $posService->checkout($payload, $user, $user->currentShift());

            // Link QrisTransaction with completed Sale
            QrisTransaction::where('transaction_id', $this->qrisTransactionId)->update([
                'sale_id'        => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'status'         => 'success',
            ]);

            // Close QR modal
            $this->showQrisModal = false;

            // Prepare receipt data
            $this->lastSaleId = $sale->id;
            $this->lastInvoiceNumber = $sale->invoice_number;
            $this->lastSubtotal = $sale->subtotal;
            $this->lastDiscount = $sale->discount;
            $this->lastTotal = $sale->total;
            $this->lastPaidAmount = $sale->paid_amount;
            $this->lastChangeAmount = $sale->change_amount;
            $this->lastPaymentMethod = 'qris';
            $this->lastSoldAt = $sale->sold_at ? $sale->sold_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB';
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
            \Illuminate\Support\Facades\Cache::flush();
            $this->successMessage = "Pembayaran QRIS lunas! Transaksi {$sale->invoice_number} berhasil.";
            $this->dispatch('qris-payment-success', amount: (int) $sale->grand_total);
        } catch (\Exception $e) {
            $this->errorMessage = 'Gagal menyimpan transaksi setelah pembayaran lunas: ' . $e->getMessage();
        }
    }

    /**
     * Cancel or dismiss active QRIS modal.
     */
    public function cancelQrisPayment()
    {
        $this->showQrisModal = false;
        $this->qrisTransactionId = null;
        $this->qrisQrUrl = null;
        $this->qrisImage = null;
        $this->qrisStatus = 'pending';
        $this->qrisStatusMessage = '';
    }

    /**
     * Simulate success payment in sandbox / local test mode.
     */
    public function simulateSuccessQris(PosService $posService)
    {
        if (!empty($this->qrisTransactionId)) {
            $trx = QrisTransaction::where('transaction_id', $this->qrisTransactionId)->first();
            if ($trx) {
                $trx->status = 'success';
                $trx->paid_at = now();
                $trx->save();
            }
            $this->qrisStatus = 'success';
            $this->completeQrisCheckout($posService);
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
        $catId = $this->selectedCategoryId;
        $searchStr = trim($this->search);

        $query = Product::with(['prices', 'unit'])->whereNull('deleted_at');

        if ($catId) {
            $query->where('category_id', $catId);
        }

        if ($searchStr !== '') {
            $query->where(function ($q) use ($searchStr) {
                $q->where('name', 'like', "%{$searchStr}%")
                  ->orWhere('barcode', 'like', "%{$searchStr}%")
                  ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$searchStr}%"));
            });
        }

        $products = $query->orderBy('name')->limit(36)->get();

        $categories = !empty($this->cachedCategories)
            ? $this->cachedCategories
            : \App\Models\Category::orderBy('name')->get()->toArray();

        $customers = !empty($this->cachedCustomers)
            ? $this->cachedCustomers
            : Customer::orderBy('name')->get()->toArray();

        $productImages = \Illuminate\Support\Facades\Cache::remember('pos_product_images', 300, function() {
            return Product::whereNull('deleted_at')->get(['id', 'image'])->mapWithKeys(function($p) {
                return [$p->id => $p->image_url];
            })->toArray();
        });

        return view('livewire.pos-terminal', [
            'products'           => $products,
            'categories'         => $categories,
            'customers'          => $customers,
            'productImages'      => $productImages,
            'search'             => $this->search,
            'selectedCategoryId' => $this->selectedCategoryId,
            'showPaymentStep'    => $this->showPaymentStep,
            'showSuccessModal'   => $this->showSuccessModal,
            'showQrisModal'      => $this->showQrisModal,
            'showShortcutsModal' => $this->showShortcutsModal,
            'cart'               => $this->cart,
            'customerId'         => $this->customerId,
            'invoiceDiscount'    => $this->invoiceDiscount,
            'paymentMethod'      => $this->paymentMethod,
            'paidAmount'         => $this->paidAmount,
            'paymentReference'   => $this->paymentReference,
            'note'               => $this->note,
            'errorMessage'       => $this->errorMessage,
            'lastSaleId'         => $this->lastSaleId,
            'lastInvoiceNumber'  => $this->lastInvoiceNumber,
            'lastSoldAt'         => $this->lastSoldAt,
            'lastCustomerName'   => $this->lastCustomerName,
            'lastSaleItems'      => $this->lastSaleItems,
            'lastTotal'          => $this->lastTotal,
            'lastPaidAmount'     => $this->lastPaidAmount,
            'lastPaymentMethod'  => $this->lastPaymentMethod,
            'lastChangeAmount'   => $this->lastChangeAmount,
            'qrisTransactionId'  => $this->qrisTransactionId,
            'qrisImage'          => $this->qrisImage,
            'qrisQrUrl'          => $this->qrisQrUrl,
            'qrisTotalAmount'    => $this->qrisTotalAmount,
            'qrisAmountUniq'     => $this->qrisAmountUniq,
            'qrisStatus'         => $this->qrisStatus,
            'qrisStatusMessage'  => $this->qrisStatusMessage,
            'subtotal'              => $this->calculateSubtotal(),
            'memberDiscount'        => $this->memberDiscount,
            'memberDiscountPercent' => $this->memberDiscountPercent,
            'isMemberSelected'      => $this->isMemberSelected(),
            'totalDiscount'         => $this->calculateTotalDiscount(),
            'total'              => $this->calculateTotal(),
            'change'             => $this->calculateChange(),
        ]);
    }
}

