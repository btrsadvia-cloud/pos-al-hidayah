<div class="h-[calc(100vh-6rem)] flex flex-col"
     x-data="{
        isOnline: navigator.onLine,
        activeTab: 'catalog', // for mobile: 'catalog', 'cart', 'pay'
        init() {
            window.addEventListener('online', () => { this.isOnline = true; });
            window.addEventListener('offline', () => { this.isOnline = false; });
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('pos-search-input')?.focus();
                } else if (e.key === 'F8') {
                    e.preventDefault();
                    document.getElementById('invoice-discount-input')?.focus();
                } else if (e.key === 'F9') {
                    e.preventDefault();
                    document.getElementById('btn-checkout')?.click();
                } else if (e.key === 'Escape') {
                    $wire.closeSuccessModal();
                    $wire.showShortcutsModal = false;
                } else if (e.key === '?' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                    $wire.toggleShortcutsModal();
                }
            });
        }
     }">

    <!-- Offline Alert Banner -->
    <div x-show="!isOnline" x-cloak
         class="bg-red-600 text-white px-4 py-2 text-xs sm:text-sm font-semibold flex items-center justify-between shrink-0 mb-2 rounded-lg shadow-md animate-pulse">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21m2.828-12.728a5 5 0 017.072 0m0 0l2.829 2.829" />
            </svg>
            <span>Koneksi internet terputus! Checkout diblokir sampai koneksi pulih kembali.</span>
        </div>
        <span class="text-xs bg-red-700 px-2 py-0.5 rounded">Offline</span>
    </div>

    <!-- Error Alert in Livewire -->
    @if (!empty($errorMessage))
        <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm rounded-lg flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="clearError" class="text-red-500 hover:text-red-800 text-base font-bold">&times;</button>
        </div>
    @endif

    <!-- Mobile Segmented Tabs (< 768px) -->
    <div class="flex md:hidden bg-slate-200 p-1 rounded-xl mb-3 shrink-0 text-xs font-semibold">
        <button type="button" @click="activeTab = 'catalog'"
                :class="activeTab === 'catalog' ? 'bg-white text-teal-700 shadow-xs' : 'text-slate-600'"
                class="flex-1 py-2 rounded-lg text-center transition">
            Katalog
        </button>
        <button type="button" @click="activeTab = 'cart'"
                :class="activeTab === 'cart' ? 'bg-white text-teal-700 shadow-xs' : 'text-slate-600'"
                class="flex-1 py-2 rounded-lg text-center transition relative">
            Keranjang
            @if(count($cart) > 0)
                <span class="inline-block ml-1 px-1.5 py-0.2 bg-teal-600 text-white rounded-full text-[10px]">
                    {{ count($cart) }}
                </span>
            @endif
        </button>
        <button type="button" @click="activeTab = 'pay'"
                :class="activeTab === 'pay' ? 'bg-white text-teal-700 shadow-xs' : 'text-slate-600'"
                class="flex-1 py-2 rounded-lg text-center transition">
            Bayar ({{ format_rupiah($total) }})
        </button>
    </div>

    <!-- 3-Area Desktop Layout -->
    <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4 min-h-0 overflow-hidden">

        <!-- AREA 1: Product Catalog (Cols 1-5 on Desktop) -->
        <div class="md:col-span-6 lg:col-span-5 flex flex-col bg-white rounded-2xl border border-slate-200 p-4 overflow-hidden shadow-xs"
             :class="{ 'hidden md:flex': activeTab !== 'catalog' }">

            <!-- Search & Barcode Bar -->
            <div class="shrink-0 mb-3 space-y-2">
                <div class="relative">
                    <input type="text" id="pos-search-input"
                           wire:model.live.debounce.300ms="search"
                           wire:keydown.enter="handleBarcodeScan"
                           placeholder="Cari nama barang atau scan barcode (F2)..."
                           autofocus
                           class="w-full pl-10 pr-24 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition min-h-[44px]">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <button type="button" wire:click="handleBarcodeScan"
                            class="absolute right-1.5 top-1.5 bg-teal-600 hover:bg-teal-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer">
                        Scan/Enter
                    </button>
                </div>

                <!-- Category Pill Filter -->
                <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                    <button type="button" wire:click="selectCategory(null)"
                            class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap transition cursor-pointer {{ is_null($selectedCategoryId) ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" wire:click="selectCategory({{ $cat->id }})"
                                class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap transition cursor-pointer {{ $selectedCategoryId === $cat->id ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 overflow-y-auto pr-1">
                @if($products->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                        <svg class="w-12 h-12 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="font-medium text-slate-600 text-sm">Produk tidak ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau pilih Semua Kategori</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($products as $product)
                            @php
                                $retailPrice = $product->retailPrice() ?? 0;
                                $isLow = $product->isLowStock();
                                $isOut = $product->stock <= 0;
                            @endphp
                            <button type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    {{ $isOut ? 'disabled' : '' }}
                                    class="group p-2.5 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer min-h-[110px] {{ $isOut ? 'opacity-50 bg-slate-50 border-slate-200 cursor-not-allowed' : 'bg-white hover:border-teal-500 hover:shadow-md border-slate-200' }}">
                                <div>
                                    <div class="flex items-start justify-between gap-1 mb-1">
                                        <span class="text-[10px] font-mono text-slate-400">{{ $product->barcode ?: '-' }}</span>
                                        <!-- Stock Badge -->
                                        @if($isOut)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Habis</span>
                                        @elseif($isLow)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Sisa {{ $product->stock }}</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700">Stok {{ $product->stock }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-semibold text-xs text-slate-800 leading-snug line-clamp-2 group-hover:text-teal-700">
                                        {{ $product->name }}
                                    </h4>
                                </div>
                                <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-xs font-bold text-teal-800">{{ format_rupiah($retailPrice) }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $product->unit->name ?? 'Pcs' }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer Quick Info -->
            <div class="shrink-0 mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $products->count() }} produk tampil</span>
                <button type="button" wire:click="toggleShortcutsModal" class="text-teal-700 hover:underline cursor-pointer flex items-center space-x-1">
                    <span>Pintasan Keyboard (?)</span>
                </button>
            </div>
        </div>

        <!-- AREA 2: Cart (Cols 6-8 on Desktop) -->
        <div class="md:col-span-6 lg:col-span-4 flex flex-col bg-white rounded-2xl border border-slate-200 p-4 overflow-hidden shadow-xs"
             :class="{ 'hidden md:flex': activeTab !== 'cart' }">

            <div class="shrink-0 flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="font-bold text-sm text-slate-900">Keranjang Belanja</h3>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-semibold">{{ count($cart) }}</span>
                </div>
                @if(count($cart) > 0)
                    <button type="button" wire:click="clearCart"
                            onclick="confirm('Kosongkan semua barang dari keranjang?') || event.stopImmediatePropagation()"
                            class="text-xs text-red-600 hover:text-red-800 font-medium cursor-pointer">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 pr-1 py-1">
                @if(empty($cart))
                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-700 text-sm">Keranjang masih kosong</p>
                        <p class="text-xs text-slate-400 mt-1">Pilih produk dari katalog atau scan barcode</p>
                    </div>
                @else
                    @foreach($cart as $productId => $item)
                        <div class="py-2.5 flex flex-col space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-semibold text-slate-900 leading-tight truncate">
                                        {{ $item['name'] }}
                                    </h5>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        <span class="text-[11px] text-slate-500">{{ format_rupiah($item['price']) }}</span>
                                        <!-- Tier Label Badge -->
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold {{ $item['tier'] === 'super_wholesale' ? 'bg-purple-100 text-purple-700' : ($item['tier'] === 'wholesale' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                            {{ tier_label($item['tier']) }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Subtotal per line -->
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-900">{{ format_rupiah($item['subtotal']) }}</div>
                                    @if(($item['item_discount'] ?? 0) > 0)
                                        <div class="text-[10px] text-red-600">-{{ format_rupiah($item['item_discount']) }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Qty Controls & Line Discount -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center space-x-1">
                                    <!-- Minus Button (min touch target 44px) -->
                                    <button type="button" wire:click="decrementQuantity({{ $productId }})"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer min-h-[36px] min-w-[36px]">
                                        &minus;
                                    </button>
                                    <input type="number" min="1" max="{{ $item['max_stock'] }}"
                                           value="{{ $item['quantity'] }}"
                                           wire:change="updateQuantity({{ $productId }}, $event.target.value)"
                                           class="w-14 text-center py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold outline-none focus:bg-white focus:ring-1 focus:ring-teal-500">
                                    <!-- Plus Button -->
                                    <button type="button" wire:click="incrementQuantity({{ $productId }})"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer min-h-[36px] min-w-[36px]">
                                        &#43;
                                    </button>
                                    <span class="text-[11px] text-slate-400 ml-1">{{ $item['unit'] }}</span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <!-- Diskon per item -->
                                    <div class="flex items-center space-x-1 text-[11px] text-slate-500" title="Diskon Item (Rp)">
                                        <span>Disc:</span>
                                        <input type="number" min="0" step="500"
                                               value="{{ $item['item_discount'] ?? 0 }}"
                                               wire:change="updateItemDiscount({{ $productId }}, $event.target.value)"
                                               class="w-16 py-0.5 px-1 bg-slate-50 border border-slate-200 rounded text-right text-[11px] font-mono outline-none focus:bg-white">
                                    </div>

                                    <!-- Delete Item Button -->
                                    <button type="button" wire:click="removeFromCart({{ $productId }})"
                                            class="p-1.5 text-slate-400 hover:text-red-600 transition cursor-pointer" title="Hapus item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Cart Subtotal Summary Footer -->
            <div class="shrink-0 pt-3 border-t border-slate-100 space-y-1 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Kotor</span>
                    <span class="font-medium">{{ format_rupiah($subtotal) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total Potongan</span>
                    <span class="font-medium text-red-600">-{{ format_rupiah($totalDiscount) }}</span>
                </div>
                <div class="flex justify-between text-slate-900 font-bold text-sm pt-1 border-t border-slate-100">
                    <span>Total Keranjang</span>
                    <span class="text-teal-800">{{ format_rupiah($total) }}</span>
                </div>
            </div>
        </div>

        <!-- AREA 3: Payment Panel (Cols 9-12 on Desktop) -->
        <div class="md:col-span-12 lg:col-span-3 flex flex-col bg-white rounded-2xl border border-slate-200 p-4 overflow-hidden shadow-xs"
             :class="{ 'hidden lg:flex': activeTab !== 'pay' && activeTab !== 'cart' }">

            <div class="shrink-0 pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900">Pembayaran</h3>
                <p class="text-[11px] text-slate-500">Pilih metode & selesaikan transaksi</p>
            </div>

            <div class="flex-1 overflow-y-auto py-3 space-y-3 pr-1 text-xs">
                <!-- Customer Selection -->
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Pelanggan</label>
                    <select wire:model.live="customerId"
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl outline-none text-xs focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pembeli Umum (Non-Member) --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?: '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Invoice Discount (Diskon Faktur) -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-medium text-slate-700">Diskon Faktur (Rp) <span class="text-slate-400 font-normal">(F8)</span></label>
                        @if(auth()->user()->isCashier())
                            <span class="text-[10px] text-slate-400">Maks. 10%</span>
                        @endif
                    </div>
                    <input type="number" min="0" step="500" id="invoice-discount-input"
                           wire:model.live.debounce.400ms="invoiceDiscount"
                           class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-teal-500 outline-none">
                </div>

                <!-- Grand Total Display (High Contrast Large Font) -->
                <div class="bg-teal-700 text-white p-3.5 rounded-xl shadow-xs">
                    <span class="text-[11px] text-teal-100 font-medium block">Total Tagihan:</span>
                    <span class="text-2xl font-black tracking-tight block">{{ format_rupiah($total) }}</span>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" wire:click="$set('paymentMethod', 'cash')"
                                class="py-2 px-2 text-center rounded-xl font-semibold border transition cursor-pointer {{ $paymentMethod === 'cash' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            Tunai
                        </button>
                        <button type="button" wire:click="$set('paymentMethod', 'qris')"
                                class="py-2 px-2 text-center rounded-xl font-semibold border transition cursor-pointer {{ $paymentMethod === 'qris' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            QRIS
                        </button>
                        <button type="button" wire:click="$set('paymentMethod', 'transfer')"
                                class="py-2 px-2 text-center rounded-xl font-semibold border transition cursor-pointer {{ $paymentMethod === 'transfer' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            Transfer
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Fields -->
                @if($paymentMethod === 'cash')
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-medium text-slate-700">Nominal Diterima (Rp)</label>
                            <button type="button" wire:click="setExactCash" class="text-teal-700 font-bold hover:underline cursor-pointer">
                                Pas ({{ format_rupiah($total) }})
                            </button>
                        </div>
                        <input type="number" min="0" step="1000"
                               wire:model.live.debounce.300ms="paidAmount"
                               class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono text-sm font-bold focus:ring-2 focus:ring-teal-500 outline-none">

                        <!-- Quick Cash Buttons -->
                        <div class="grid grid-cols-2 gap-1.5 mt-2">
                            <button type="button" wire:click="setQuickCash(50000)"
                                    class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-lg font-medium text-slate-700 transition cursor-pointer">
                                Rp 50.000
                            </button>
                            <button type="button" wire:click="setQuickCash(100000)"
                                    class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-lg font-medium text-slate-700 transition cursor-pointer">
                                Rp 100.000
                            </button>
                            <button type="button" wire:click="setQuickCash(200000)"
                                    class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-lg font-medium text-slate-700 transition cursor-pointer">
                                Rp 200.000
                            </button>
                            <button type="button" wire:click="setQuickCash(500000)"
                                    class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-lg font-medium text-slate-700 transition cursor-pointer">
                                Rp 500.000
                            </button>
                        </div>

                        <!-- Kembalian Display -->
                        <div class="mt-3 p-2.5 bg-slate-100 rounded-xl flex justify-between items-center">
                            <span class="text-slate-600 font-medium">Kembalian:</span>
                            <span class="text-base font-bold text-slate-900">{{ format_rupiah($change) }}</span>
                        </div>
                    </div>
                @elseif($paymentMethod === 'qris')
                    <!-- QRIS BuatQris Info Card -->
                    <div class="p-3 bg-teal-50 border border-teal-200 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-teal-800 font-bold text-xs">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Gateway BuatQris Dinamis</span>
                        </div>
                        <p class="text-[11px] text-teal-700 leading-relaxed">
                            QRIS akan dibuat secara otomatis dengan kode unik verifikasi. Sistem akan mendeteksi pelunasan secara real-time.
                        </p>
                    </div>
                @else
                    <!-- Transfer Reference -->
                    <div>
                        <label class="block font-medium text-slate-700 mb-1">No. Rekening / Bukti Transfer</label>
                        <input type="text" wire:model.live="paymentReference"
                               placeholder="Contoh: No. Bukti Transfer Bank"
                               class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                @endif

                <!-- Notes (Optional) -->
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Catatan Transaksi</label>
                    <input type="text" wire:model.live="note"
                           placeholder="Catatan tambahan (opsional)"
                           class="w-full py-1.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <!-- Checkout Button (min 44px touch) -->
            <div class="shrink-0 pt-3 border-t border-slate-100">
                <button type="button" id="btn-checkout"
                        wire:click="checkout"
                        :disabled="!isOnline || {{ count($cart) === 0 ? 'true' : 'false' }}"
                        class="w-full py-3.5 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-md transition flex items-center justify-center space-x-2 cursor-pointer min-h-[48px]">
                    @if($paymentMethod === 'qris')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>BUAT KODE QRIS (F9)</span>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>BAYAR (F9)</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- DYNAMIC BUATQRIS PAYMENT MODAL -->
    @if($showQrisModal)
        <div wire:poll.5s="pollQrisStatus"
             class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative text-center my-8">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 text-left">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900">QRIS Toko Al-Hidayah</h4>
                            <p class="text-[11px] text-slate-400 font-mono">ID: {{ $qrisTransactionId }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="cancelQrisPayment"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Total Amount Banner (Must pay exact total_amount) -->
                <div class="mt-4 p-3 bg-gradient-to-r from-teal-700 to-teal-800 text-white rounded-2xl shadow-sm">
                    <span class="text-[11px] text-teal-200 block uppercase tracking-wider font-semibold">Total yang Harus Dibayar:</span>
                    <span class="text-3xl font-black tracking-tight block my-0.5">
                        {{ format_rupiah($qrisTotalAmount) }}
                    </span>
                    @if($qrisAmountUniq > 0)
                        <span class="text-[11px] text-teal-100 block font-medium">
                            (Termasuk kode unik <strong>Rp {{ $qrisAmountUniq }}</strong> untuk verifikasi instan)
                        </span>
                    @endif
                </div>

                <!-- QR Code Display -->
                <div class="my-4">
                    <div class="inline-block p-3 bg-white rounded-2xl border-2 border-teal-500 shadow-md">
                        @if(!empty($qrisImage))
                            <img src="{{ $qrisImage }}" alt="QRIS Code" class="w-60 h-60 mx-auto object-contain rounded-lg">
                        @elseif(!empty($qrisQrUrl))
                            <img src="{{ $qrisQrUrl }}" alt="QRIS Code" class="w-60 h-60 mx-auto object-contain rounded-lg">
                        @else
                            <div class="w-60 h-60 flex items-center justify-center text-slate-400 text-xs">
                                Memuat QR Code...
                            </div>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Scan dengan <strong>BCA, BRI, Mandiri, BNI, GoPay, OVO, DANA, ShopeePay</strong>, dll
                    </p>
                </div>

                <!-- Live Status Indicator -->
                <div class="space-y-2 mb-5">
                    @if($qrisStatus === 'success')
                        <div class="py-2.5 px-3 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Pembayaran Lunas! Menyimpan transaksi...</span>
                        </div>
                    @elseif($qrisStatus === 'expired')
                        <div class="py-2 px-3 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-semibold">
                            QRIS telah kedaluwarsa. Silakan batalkan dan buat baru.
                        </div>
                    @elseif($qrisStatus === 'failed')
                        <div class="py-2 px-3 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-semibold">
                            Pembayaran QRIS gagal atau dibatalkan.
                        </div>
                    @else
                        <div class="py-2 px-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-semibold flex items-center justify-center space-x-2 animate-pulse">
                            <svg class="w-4 h-4 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menunggu pembayaran pelanggan...</span>
                        </div>
                    @endif

                    @if(!empty($qrisExpiredAt))
                        <div class="text-[11px] text-slate-400">
                            Berlaku s/d: <span class="font-semibold text-slate-600">{{ $qrisExpiredAt }}</span>
                        </div>
                    @endif

                    @if(!empty($qrisStatusMessage))
                        <p class="text-xs text-slate-600 font-medium px-2">
                            {{ $qrisStatusMessage }}
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button type="button" wire:click="checkQrisStatus"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold rounded-xl transition flex items-center justify-center space-x-2 cursor-pointer shadow-sm disabled:opacity-50">
                        <span wire:loading.remove wire:target="checkQrisStatus">
                            🔍 CEK STATUS SEKARANG
                        </span>
                        <span wire:loading wire:target="checkQrisStatus" class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memeriksa gateway...</span>
                        </span>
                    </button>

                    @if(config('buatqris.test_mode') || app()->environment('local'))
                        <!-- Development Testing Button -->
                        <button type="button" wire:click="simulateSuccessQris"
                                class="w-full py-2 px-3 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl text-xs transition cursor-pointer border border-amber-300 flex items-center justify-center space-x-1">
                            <span>⚡ Simulasi Bayar Lunas (Mode Testing)</span>
                        </button>
                    @endif

                    <button type="button" wire:click="cancelQrisPayment"
                            class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl text-xs transition cursor-pointer">
                        Batal / Ganti Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- SUCCESS RECEIPT MODAL -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900">Transaksi Selesai</h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $lastInvoiceNumber }}</p>
                </div>

                <!-- Printable Receipt Content (Thermal 58mm / 80mm preview) -->
                <div id="thermal-receipt" class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 text-xs font-mono space-y-2 mb-4 text-slate-800">
                    <div class="text-center border-b border-dashed border-slate-300 pb-2">
                        <div class="font-bold text-sm">TOKO AL-HIDAYAH</div>
                        <div class="text-[10px] text-slate-500">Busana Muslimah & Perlengkapan Ibadah</div>
                        <div class="text-[10px] text-slate-500 mt-1">{{ $lastSoldAt }}</div>
                        <div class="text-[10px] text-slate-500">Kasir: {{ auth()->user()->name }} | {{ $lastCustomerName }}</div>
                    </div>

                    <div class="space-y-1 py-1 border-b border-dashed border-slate-300">
                        @foreach($lastSaleItems as $it)
                            <div>
                                <div class="font-bold truncate">{{ $it['name'] }}</div>
                                <div class="flex justify-between text-[11px] text-slate-600">
                                    <span>{{ $it['qty'] }} {{ $it['unit'] }} x {{ number_format($it['price'], 0, ',', '.') }}</span>
                                    <span>{{ number_format($it['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-1 text-right pt-1">
                        <div class="flex justify-between font-bold text-sm">
                            <span>TOTAL:</span>
                            <span>{{ format_rupiah($lastTotal) }}</span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span>Bayar ({{ strtoupper($lastPaymentMethod) }}):</span>
                            <span>{{ format_rupiah($lastPaidAmount) }}</span>
                        </div>
                        @if($lastPaymentMethod === 'cash')
                            <div class="flex justify-between text-[11px] font-bold">
                                <span>Kembali:</span>
                                <span>{{ format_rupiah($lastChangeAmount) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-center pt-2 border-t border-dashed border-slate-300 text-[10px] text-slate-500">
                        Terima kasih atas kunjungan Anda.<br>Semoga berkah & bermanfaat!
                    </div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="window.printReceipt()"
                            class="py-2.5 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Struk</span>
                    </button>
                    <button type="button" wire:click="closeSuccessModal"
                            class="py-2.5 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer min-h-[44px]">
                        Tutup (Esc)
                    </button>
                </div>
            </div>
        </div>

        <script>
            window.printReceipt = function() {
                var printContents = document.getElementById('thermal-receipt').innerHTML;
                var printWindow = window.open('', '_blank', 'width=350,height=600');
                printWindow.document.write('<html><head><title>Struk Belanja - Toko Al-Hidayah</title>');
                printWindow.document.write('<style>@page { size: 58mm auto; margin: 0; } body { font-family: monospace; font-size: 11px; padding: 10px; width: 58mm; margin: 0 auto; } .border-b { border-bottom: 1px dashed #000; padding-bottom: 4px; margin-bottom: 4px; } .border-t { border-top: 1px dashed #000; padding-top: 4px; margin-top: 4px; } .flex { display: flex; justify-content: space-between; } .text-center { text-align: center; } .font-bold { font-weight: bold; } .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            }
        </script>
    @endif

    <!-- KEYBOARD SHORTCUTS MODAL (?) -->
    @if($showShortcutsModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900">Pintasan Keyboard POS</h3>
                    <button type="button" wire:click="toggleShortcutsModal" class="text-slate-400 hover:text-slate-700 text-base font-bold">&times;</button>
                </div>
                <div class="space-y-2 py-3">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Fokus Pencarian / Barcode</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F2</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Diskon Faktur</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F8</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Selesaikan Pembayaran</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F9</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Tutup Dialog / Batal</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">Esc</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600">Buka Bantuan Pintasan</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">?</kbd>
                    </div>
                </div>
                <button type="button" wire:click="toggleShortcutsModal"
                        class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer">
                    Mengerti
                </button>
            </div>
        </div>
    @endif
</div>

