<div class="h-[calc(100vh-6rem)] flex flex-col"
     x-data="{
        isOnline: navigator.onLine,
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
                    if ($wire.showPaymentStep) {
                        document.getElementById('btn-checkout')?.click();
                    } else {
                        document.getElementById('btn-to-payment')?.click();
                    }
                } else if (e.key === 'Escape') {
                    if ($wire.showSuccessModal) {
                        $wire.closeSuccessModal();
                    } else if ($wire.showQrisModal) {
                        $wire.cancelQrisPayment();
                    } else if ($wire.showShortcutsModal) {
                        $wire.toggleShortcutsModal();
                    } else if ($wire.showPaymentStep) {
                        $wire.backToCart();
                    }
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
        <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm rounded-xl flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="clearError" class="text-red-500 hover:text-red-800 text-base font-bold cursor-pointer">&times;</button>
        </div>
    @endif

    @if(!$showPaymentStep)
        <!-- ======================================================================== -->
        <!-- STEP 1: PEMILIHAN BARANG (KATALOG & KERANJANG BELANJA)                   -->
        <!-- ======================================================================== -->
        <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-0 overflow-hidden">

            <!-- AREA 1: Product Catalog (Cols 1-7 on Desktop) -->
            <div class="lg:col-span-7 flex flex-col bg-white rounded-2xl border border-slate-200 p-4 overflow-hidden shadow-xs">

                <!-- Search & Barcode Bar (Manual Input on Enter/Click, No auto-recommendations) -->
                <div class="shrink-0 mb-3 space-y-2">
                    <div class="relative flex items-center">
                        <div class="relative flex-1">
                            <input type="text" id="pos-search-input"
                                   wire:model="search"
                                   wire:keydown.enter="handleBarcodeScan"
                                   autocomplete="off"
                                   autocorrect="off"
                                   autocapitalize="off"
                                   spellcheck="false"
                                   placeholder="Ketik nama produk atau scan barcode, lalu tekan Enter / Cari (F2)..."
                                   autofocus
                                   class="w-full pl-10 pr-20 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition min-h-[44px]">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            @if(trim($search) !== '')
                                <button type="button" wire:click="resetSearch"
                                        class="absolute right-20 top-2.5 text-slate-400 hover:text-slate-600 p-1 text-xs cursor-pointer"
                                        title="Hapus filter pencarian">
                                    &times;
                                </button>
                            @endif
                        </div>
                        <button type="button" wire:click="handleBarcodeScan"
                                class="ml-2 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white px-4 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer min-h-[44px] flex items-center space-x-1 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Cari</span>
                        </button>
                    </div>

                    <!-- Category Pill Filter -->
                    <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                        <button type="button" wire:click="selectCategory(null)"
                                class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap transition cursor-pointer {{ is_null($selectedCategoryId) ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            @php
                                $cId = is_object($cat) ? $cat->id : ($cat['id'] ?? null);
                                $cName = is_object($cat) ? $cat->name : ($cat['name'] ?? null);
                            @endphp
                            @if($cId && $cName)
                                <button type="button" wire:click="selectCategory({{ $cId }})"
                                        class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap transition cursor-pointer {{ (int)$selectedCategoryId === (int)$cId ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $cName }}
                                </button>
                            @endif
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
                            <p class="text-xs text-slate-400 mt-1">
                                @if(trim($search) !== '')
                                    Tidak ada produk yang cocok dengan "{{ $search }}".
                                    <button type="button" wire:click="resetSearch" class="text-teal-600 underline font-semibold ml-1 cursor-pointer">Lihat semua</button>
                                @else
                                    Pilih kategori lain atau tambahkan produk baru.
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5">
                            @foreach($products as $product)
                                @php
                                    $retailPrice = $product->retailPrice() ?? 0;
                                    $isLow = $product->isLowStock();
                                    $isOut = $product->stock <= 0;
                                @endphp
                                <button type="button"
                                        wire:key="product-card-{{ $product->id }}"
                                        wire:click="addToCart({{ $product->id }})"
                                        x-data="{ clicking: false }"
                                        @click="clicking = true; setTimeout(() => clicking = false, 120)"
                                        :class="{ 'scale-95 bg-teal-50 border-teal-500': clicking }"
                                        {{ $isOut ? 'disabled' : '' }}
                                        class="group p-2.5 rounded-xl border text-left transition-all duration-75 flex flex-col justify-between cursor-pointer min-h-[110px] select-none {{ $isOut ? 'opacity-50 bg-slate-50 border-slate-200 cursor-not-allowed' : 'bg-white hover:border-teal-500 hover:shadow-md border-slate-200' }}">
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
                                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-xs font-bold text-teal-800">{{ format_rupiah($retailPrice) }}</span>
                                        <span class="text-[10px] text-teal-600 group-hover:translate-x-0.5 transition-transform font-bold">+ Tambah</span>
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

            <!-- AREA 2: Cart Belanja (Cols 8-12 on Desktop) -->
            <div class="lg:col-span-5 flex flex-col bg-white rounded-2xl border border-slate-200 p-4 overflow-hidden shadow-xs">

                <div class="shrink-0 flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Keranjang Belanja</h3>
                            <span class="text-[11px] text-slate-400">{{ count($cart) }} macam barang dipilih</span>
                        </div>
                    </div>
                    @if(count($cart) > 0)
                        <button type="button"
                                @click.prevent="window.customConfirm ? window.customConfirm('Kosongkan semua barang dari keranjang belanja?').then(ok => ok && $wire.clearCart()) : ($wire.clearCart())"
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
                            <p class="font-medium text-slate-700 text-sm">Keranjang belanja kosong</p>
                            <p class="text-xs text-slate-400 mt-1">Pilih barang dari katalog di sebelah kiri untuk memulai</p>
                        </div>
                    @else
                        @foreach($cart as $productId => $item)
                            <div wire:key="cart-row-{{ $productId }}" class="py-2.5 flex flex-col space-y-1.5">
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
                                        <!-- Minus Button -->
                                        <button type="button" wire:click="decrementQuantity({{ $productId }})"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer select-none">
                                            &minus;
                                        </button>
                                        <input type="number" min="1" max="{{ $item['max_stock'] }}"
                                               value="{{ $item['quantity'] }}"
                                               wire:change="updateQuantity({{ $productId }}, $event.target.value)"
                                               class="w-14 text-center py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold outline-none focus:bg-white focus:ring-1 focus:ring-teal-500">
                                        <!-- Plus Button -->
                                        <button type="button" wire:click="incrementQuantity({{ $productId }})"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer select-none">
                                            &#43;
                                        </button>
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

                <!-- Cart Subtotal Summary & Next Step Button -->
                <div class="shrink-0 pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="space-y-1">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Kotor</span>
                            <span class="font-medium">{{ format_rupiah($subtotal) }}</span>
                        </div>
                        @if($totalDiscount > 0)
                            <div class="flex justify-between text-slate-600">
                                <span>Total Diskon</span>
                                <span class="font-medium text-red-600">-{{ format_rupiah($totalDiscount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-900 font-bold text-base pt-1 border-t border-slate-100">
                            <span>Total Belanja</span>
                            <span class="text-teal-800 text-lg">{{ format_rupiah($total) }}</span>
                        </div>
                    </div>

                    <!-- Button: Proceed to Payment Step -->
                    <button type="button" id="btn-to-payment"
                            wire:click="proceedToPayment"
                            {{ count($cart) === 0 ? 'disabled' : '' }}
                            class="w-full py-3.5 px-4 bg-teal-600 hover:bg-teal-700 active:scale-98 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-md transition flex items-center justify-center space-x-2 cursor-pointer min-h-[48px]">
                        <span>LANJUT KE PEMBAYARAN &rarr; (F9)</span>
                    </button>
                </div>
            </div>
        </div>

    @else
        <!-- ======================================================================== -->
        <!-- STEP 2: LAYAR PEMBAYARAN (TAMPIL SAAT SELESAI MEMILIH BARANG)           -->
        <!-- ======================================================================== -->
        <div class="flex-1 flex flex-col bg-white rounded-2xl border border-slate-200 p-5 overflow-hidden shadow-xs">

            <!-- Step Header with Back Button -->
            <div class="shrink-0 flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-3">
                    <button type="button" wire:click="backToCart"
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs flex items-center space-x-1.5 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>&larr; Kembali / Pilih Barang Lagi (Esc)</span>
                    </button>
                    <div>
                        <h2 class="font-bold text-base text-slate-900">Penyelesaian Pembayaran</h2>
                        <p class="text-xs text-slate-500">Periksa rincian belanja dan selesaikan transaksi kasir</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold px-2.5 py-1 bg-teal-100 text-teal-800 rounded-full">Langkah 2: Pembayaran</span>
                </div>
            </div>

            <!-- Main Payment View: 2 Columns (Order Summary Left, Payment Form Right) -->
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0 overflow-y-auto pr-1">

                <!-- Left Column: Compact Order Summary -->
                <div class="lg:col-span-5 flex flex-col bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">Rincian Belanja ({{ count($cart) }} Item)</h4>
                        <button type="button" wire:click="backToCart" class="text-xs text-teal-700 hover:underline font-semibold cursor-pointer">
                            + Tambah / Ubah
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto divide-y divide-slate-200 pr-1 text-xs max-h-80">
                        @foreach($cart as $item)
                            <div class="py-2 flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $item['name'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $item['quantity'] }} {{ $item['unit'] }} &times; {{ format_rupiah($item['price']) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-slate-900">{{ format_rupiah($item['subtotal']) }}</div>
                                    @if(($item['item_discount'] ?? 0) > 0)
                                        <div class="text-[10px] text-red-600">-{{ format_rupiah($item['item_discount']) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-200 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Kotor</span>
                            <span class="font-medium">{{ format_rupiah($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Total Diskon</span>
                            <span class="font-medium text-red-600">-{{ format_rupiah($totalDiscount) }}</span>
                        </div>
                    </div>

                    <!-- Grand Total Banner -->
                    <div class="bg-gradient-to-br from-teal-700 to-teal-800 text-white p-4 rounded-xl shadow-xs mt-auto">
                        <span class="text-xs text-teal-200 font-medium block uppercase tracking-wider">Total Tagihan:</span>
                        <span class="text-3xl font-black tracking-tight block mt-1">{{ format_rupiah($total) }}</span>
                    </div>
                </div>

                <!-- Right Column: Payment Details & Methods -->
                <div class="lg:col-span-7 flex flex-col justify-between space-y-4">
                    <div class="space-y-4 text-xs">

                        <!-- Customer Selection & Invoice Discount in 2 cols -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Cari Pelanggan (Ketik Nama / No. HP)</label>
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    selectedId: @entangle('customerId'),
                                    customers: {{ json_encode($customers) }},
                                    init() {
                                        if (this.selectedId) {
                                            const found = this.customers.find(c => c.id == this.selectedId);
                                            if (found) this.search = found.name;
                                        }
                                    },
                                    get filtered() {
                                        if (!this.search.trim()) return this.customers.slice(0, 15);
                                        const q = this.search.toLowerCase();
                                        return this.customers.filter(c => c.name.toLowerCase().includes(q) || (c.phone && c.phone.includes(q))).slice(0, 15);
                                    },
                                    select(c) {
                                        this.selectedId = c.id;
                                        this.search = c.name + (c.phone ? ' (' + c.phone + ')' : '');
                                        this.open = false;
                                    },
                                    clear() {
                                        this.selectedId = null;
                                        this.search = '';
                                        this.open = true;
                                    }
                                }" class="relative">
                                    <div class="relative">
                                        <input type="text"
                                               x-model="search"
                                               @focus="open = true"
                                               @click="open = true"
                                               placeholder="Ketik manual nama / HP pelanggan..."
                                               autocomplete="off"
                                               class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl outline-none text-xs focus:ring-2 focus:ring-teal-500 pr-8">
                                        <button type="button" x-show="search.length > 0" @click="clear()"
                                                class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">
                                            &times;
                                        </button>
                                    </div>
                                    <div x-show="open && filtered.length > 0"
                                         @click.outside="open = false"
                                         x-cloak
                                         class="absolute z-30 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 text-xs">
                                        <button type="button" @click="clear(); open = false"
                                                class="w-full text-left px-3 py-2 text-slate-500 italic hover:bg-slate-50 font-medium cursor-pointer">
                                            -- Pelanggan Umum (Non-Member) --
                                        </button>
                                        <template x-for="c in filtered" :key="c.id">
                                            <button type="button" @click="select(c)"
                                                    class="w-full text-left px-3 py-2 hover:bg-teal-50 hover:text-teal-900 transition flex justify-between items-center cursor-pointer">
                                                <div>
                                                    <span class="font-bold text-slate-800 text-xs" x-text="c.name"></span>
                                                    <span class="text-[10px] text-slate-400 block" x-show="c.phone" x-text="'HP: ' + c.phone"></span>
                                                </div>
                                                <span class="text-teal-600 font-bold ml-2 shrink-0" x-show="selectedId == c.id">✓ Terpilih</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="font-semibold text-slate-700">Diskon Faktur (Rp) <span class="text-slate-400 font-normal">(F8)</span></label>
                                    @if(auth()->user()->isCashier())
                                        <span class="text-[10px] text-slate-400">Maks. 10%</span>
                                    @endif
                                </div>
                                <input type="number" min="0" step="500" id="invoice-discount-input"
                                       wire:model.live.debounce.400ms="invoiceDiscount"
                                       class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono text-xs focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                        </div>

                        <!-- Payment Method Toggle -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" wire:click="$set('paymentMethod', 'cash')"
                                        class="py-3 px-3 text-center rounded-xl font-bold border transition cursor-pointer select-none {{ $paymentMethod === 'cash' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                    💵 Tunai
                                </button>
                                <button type="button" wire:click="$set('paymentMethod', 'qris')"
                                        class="py-3 px-3 text-center rounded-xl font-bold border transition cursor-pointer select-none {{ $paymentMethod === 'qris' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                    📱 QRIS
                                </button>
                                <button type="button" wire:click="$set('paymentMethod', 'transfer')"
                                        class="py-3 px-3 text-center rounded-xl font-bold border transition cursor-pointer select-none {{ $paymentMethod === 'transfer' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                    🏦 Transfer
                                </button>
                            </div>
                        </div>

                        <!-- Detail Sesuai Metode Pembayaran -->
                        @if($paymentMethod === 'cash')
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                                <div class="flex justify-between items-center">
                                    <label class="font-bold text-slate-800 text-xs">Uang Diterima dari Pembeli (Rp)</label>
                                    <button type="button" wire:click="setExactCash" class="text-teal-700 font-bold hover:underline cursor-pointer">
                                        Uang Pas ({{ format_rupiah($total) }})
                                    </button>
                                </div>
                                <input type="number" min="0" step="1000"
                                       wire:model.live.debounce.250ms="paidAmount"
                                       class="w-full py-3 px-3.5 bg-white border border-slate-300 rounded-xl font-mono text-base font-bold focus:ring-2 focus:ring-teal-500 outline-none">

                                <!-- Quick Cash Buttons -->
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" wire:click="setQuickCash(50000)"
                                            class="py-2 px-2 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg font-semibold text-slate-700 text-xs transition cursor-pointer">
                                        Rp 50.000
                                    </button>
                                    <button type="button" wire:click="setQuickCash(100000)"
                                            class="py-2 px-2 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg font-semibold text-slate-700 text-xs transition cursor-pointer">
                                        Rp 100.000
                                    </button>
                                    <button type="button" wire:click="setQuickCash(200000)"
                                            class="py-2 px-2 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg font-semibold text-slate-700 text-xs transition cursor-pointer">
                                        Rp 200.000
                                    </button>
                                    <button type="button" wire:click="setQuickCash(500000)"
                                            class="py-2 px-2 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg font-semibold text-slate-700 text-xs transition cursor-pointer">
                                        Rp 500.000
                                    </button>
                                </div>

                                <!-- Kembalian Display -->
                                <div class="p-3 bg-white rounded-xl border border-slate-200 flex justify-between items-center">
                                    <span class="text-slate-600 font-semibold">Uang Kembalian:</span>
                                    <span class="text-xl font-black text-slate-900">{{ format_rupiah($change) }}</span>
                                </div>
                            </div>
                        @elseif($paymentMethod === 'qris')
                            <div class="bg-teal-50/70 p-4 rounded-xl border border-teal-200 text-teal-900 space-y-1 text-xs">
                                <div class="font-bold flex items-center space-x-1.5 text-teal-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span>Pembayaran QRIS Otomatis</span>
                                </div>
                                <p class="text-[11px] text-teal-700">
                                    QRIS berlaku selama <strong>5 menit</strong>. Klik tombol "BUAT KODE QRIS" di bawah untuk memunculkan kode QR bagi pembeli.
                                </p>
                            </div>
                        @else
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                                <label class="block font-semibold text-slate-700">No. Rekening / ID Referensi Transfer</label>
                                <input type="text" wire:model="paymentReference"
                                       placeholder="Contoh: Transfer BCA / Bukti Ref #12345"
                                       class="w-full py-2.5 px-3 bg-white border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        @endif

                        <!-- Notes (Optional) -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" wire:model="note"
                                   placeholder="Catatan transaksi..."
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- Action Buttons: Checkout or Back -->
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-3">
                        <button type="button" wire:click="backToCart"
                                class="py-3.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition cursor-pointer min-h-[48px] shrink-0">
                            &larr; Batal
                        </button>
                        <button type="button" id="btn-checkout"
                                wire:click="checkout"
                                :disabled="!isOnline || {{ count($cart) === 0 ? 'true' : 'false' }}"
                                class="flex-1 py-3.5 px-4 bg-teal-600 hover:bg-teal-700 active:scale-98 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-md transition flex items-center justify-center space-x-2 cursor-pointer min-h-[48px]">
                            <span wire:loading.remove wire:target="checkout">
                                @if($paymentMethod === 'qris')
                                    📱 BUAT KODE QRIS (F9)
                                @else
                                    ✓ SELESAIKAN PEMBAYARAN (F9)
                                @endif
                            </span>
                            <span wire:loading wire:target="checkout" class="flex items-center space-x-2">
                                <svg class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memproses Transaksi...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- DYNAMIC BUATQRIS PAYMENT MODAL (5-MINUTE COUNTDOWN TIMER) -->
    @if($showQrisModal)
        <div wire:poll.5s="pollQrisStatus"
             x-data="{
                secondsLeft: 300,
                timer: null,
                init() {
                    this.secondsLeft = 300;
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        if (this.secondsLeft > 0) {
                            this.secondsLeft--;
                        } else {
                            clearInterval(this.timer);
                        }
                    }, 1000);
                },
                get formattedTime() {
                    const m = Math.floor(this.secondsLeft / 60).toString().padStart(2, '0');
                    const s = (this.secondsLeft % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }
             }"
             class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs z-50 flex items-center justify-center p-3 overflow-y-auto" x-cloak>
            <div class="bg-white rounded-2xl max-w-xs sm:max-w-sm w-full p-4 shadow-2xl border border-slate-200 relative text-center my-4">
                <!-- Header -->
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center space-x-2 text-left">
                        <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-900">QRIS Toko Al-Hidayah</h4>
                            <p class="text-[10px] text-slate-400 font-mono">ID: {{ $qrisTransactionId }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="cancelQrisPayment"
                            class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- 5-Minute Expiry Countdown Pill -->
                <div class="mt-2.5 inline-flex items-center space-x-1 px-2.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-full text-[11px] font-semibold">
                    <svg class="w-3 h-3 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Sisa Waktu: <strong class="font-mono" x-text="formattedTime">05:00</strong> (5 Menit)</span>
                </div>

                <!-- Total Amount Banner (COMPACT) -->
                <div class="mt-2.5 p-2.5 bg-gradient-to-r from-teal-700 to-teal-800 text-white rounded-xl shadow-xs">
                    <span class="text-[10px] text-teal-200 block uppercase tracking-wider font-semibold">Total Tagihan:</span>
                    <span class="text-xl font-black tracking-tight block my-0.5">
                        {{ format_rupiah($qrisTotalAmount) }}
                    </span>
                    @if($qrisAmountUniq > 0)
                        <span class="text-[10px] text-teal-100 block font-medium">
                            (Kode unik: <strong>Rp {{ $qrisAmountUniq }}</strong>)
                        </span>
                    @endif
                </div>

                <!-- QR Code Display (COMPACT 144x144 px) -->
                <div class="my-3">
                    <div class="inline-block p-2 bg-white rounded-xl border border-teal-500 shadow-sm">
                        @if(!empty($qrisImage))
                            <img src="{{ $qrisImage }}" alt="QRIS Code" class="w-36 h-36 mx-auto object-contain rounded">
                        @elseif(!empty($qrisQrUrl))
                            <img src="{{ $qrisQrUrl }}" alt="QRIS Code" class="w-36 h-36 mx-auto object-contain rounded">
                        @else
                            <div class="w-36 h-36 flex items-center justify-center text-slate-400 text-[11px]">
                                Memuat QR Code...
                            </div>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">
                        Scan via BCA, BRI, Mandiri, GoPay, DANA, OVO, dll
                    </p>
                </div>

                <!-- Live Status Indicator -->
                <div class="space-y-1.5 mb-3 text-xs">
                    @if($qrisStatus === 'success')
                        <div class="py-1.5 px-2 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-lg text-xs font-bold flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Lunas! Menyimpan...</span>
                        </div>
                    @elseif($qrisStatus === 'expired')
                        <div class="py-1.5 px-2 bg-red-50 border border-red-300 text-red-700 rounded-lg text-xs font-semibold">
                            QRIS kedaluwarsa. Silakan buat ulang.
                        </div>
                    @elseif($qrisStatus === 'failed')
                        <div class="py-1.5 px-2 bg-red-50 border border-red-300 text-red-700 rounded-lg text-xs font-semibold">
                            Pembayaran QRIS gagal.
                        </div>
                    @else
                        <div class="py-1.5 px-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-[11px] font-semibold flex items-center justify-center space-x-1.5 animate-pulse">
                            <svg class="w-3.5 h-3.5 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menunggu pembayaran pelanggan...</span>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="space-y-1.5">
                    <button type="button" wire:click="checkQrisStatus"
                            wire:loading.attr="disabled"
                            class="w-full py-2.5 px-3 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold rounded-xl transition flex items-center justify-center space-x-1.5 cursor-pointer text-xs shadow-xs disabled:opacity-50">
                        <span wire:loading.remove wire:target="checkQrisStatus">
                            🔍 CEK STATUS SEKARANG
                        </span>
                        <span wire:loading wire:target="checkQrisStatus" class="flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memeriksa...</span>
                        </span>
                    </button>

                    @if(config('buatqris.test_mode') || app()->environment('local'))
                        <button type="button" wire:click="simulateSuccessQris"
                                class="w-full py-1.5 px-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl text-[11px] transition cursor-pointer border border-amber-300">
                            ⚡ Simulasi Bayar Lunas (Testing)
                        </button>
                    @endif

                    <button type="button" wire:click="cancelQrisPayment"
                            class="w-full py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl text-[11px] transition cursor-pointer">
                        Batal / Ganti Metode Bayar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- SUCCESS RECEIPT MODAL (DENGAN CETAK STRUK PASTI BISA) -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 my-8">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900">Transaksi Berhasil</h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $lastInvoiceNumber }}</p>
                </div>

                <!-- Printable Receipt Content (Thermal 58mm preview) -->
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

                <!-- Actions: Direct print & Open receipt tab -->
                <div class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="printReceiptDirectly({{ $lastSaleId }})"
                                class="py-2.5 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Struk</span>
                        </button>
                        <a href="{{ $lastSaleId ? route('sales.receipt', $lastSaleId) : '#' }}" target="_blank"
                           class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-xl text-xs flex items-center justify-center space-x-1 transition cursor-pointer min-h-[44px]">
                            <span>Buka Tab Baru &nearr;</span>
                        </a>
                    </div>
                    <button type="button" wire:click="closeSuccessModal"
                            class="w-full py-2.5 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer min-h-[44px]">
                        Tutup & Transaksi Baru (Esc)
                    </button>
                </div>
            </div>
        </div>

        <script>
            window.printReceiptDirectly = function(saleId) {
                if (!saleId) {
                    window.print();
                    return;
                }
                var url = '/sales/' + saleId + '/receipt';
                var iframe = document.getElementById('receipt-print-iframe');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = 'receipt-print-iframe';
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    document.body.appendChild(iframe);
                }
                iframe.src = url;
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch(e) {
                        window.open(url, '_blank');
                    }
                };
            };
        </script>
    @endif

    <!-- KEYBOARD SHORTCUTS MODAL (?) -->
    @if($showShortcutsModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900">Pintasan Keyboard POS</h3>
                    <button type="button" wire:click="toggleShortcutsModal" class="text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer">&times;</button>
                </div>
                <div class="space-y-2 py-3">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Fokus Pencarian Produk</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F2</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Diskon Faktur</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F8</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Lanjut / Selesaikan Pembayaran</span>
                        <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-bold text-slate-800">F9</kbd>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-600">Kembali ke Keranjang / Tutup Dialog</span>
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
