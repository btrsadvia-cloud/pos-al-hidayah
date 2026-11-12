<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('products.index') }}" class="text-xs text-[#881337] hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Katalog Produk</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Atur Tingkat Harga Bertingkat</h1>
            <p class="text-sm text-slate-500 mt-0.5">Produk: <span class="font-bold text-slate-800">{{ $product->name }}</span> (Satuan: {{ $product->unit->name ?? 'Pcs' }})</p>
        </div>

        <form method="POST" action="{{ route('products.prices.update', $product) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="space-y-4">
                    <!-- Retail -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-slate-200 text-slate-800 font-bold text-xs rounded mb-1">Tingkat 1: Eceran</span>
                            <label class="block text-xs font-semibold text-slate-700">Harga Eceran (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_retail" value="{{ old('price_retail', $retail->price ?? 0) }}" required min="0" step="500"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-slate-300 font-mono text-sm font-bold text-slate-900 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mt-6 sm:mt-5">Minimal Qty Eceran</label>
                            <input type="number" name="min_qty_retail" value="{{ old('min_qty_retail', $retail->min_qty ?? 1) }}" required min="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-slate-300 font-mono text-sm text-slate-900 outline-none">
                        </div>
                    </div>

                    <!-- Wholesale -->
                    <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 font-bold text-xs rounded mb-1">Tingkat 2: Grosir</span>
                            <label class="block text-xs font-semibold text-blue-900">Harga Grosir (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_wholesale" value="{{ old('price_wholesale', $wholesale->price ?? 0) }}" required min="0" step="500"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-blue-300 font-mono text-sm font-bold text-blue-900 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-blue-900 mt-6 sm:mt-5">Minimal Qty Grosir</label>
                            <input type="number" name="min_qty_wholesale" value="{{ old('min_qty_wholesale', $wholesale->min_qty ?? 12) }}" required min="2"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-blue-300 font-mono text-sm text-slate-900 outline-none">
                        </div>
                    </div>

                    <!-- Super Wholesale -->
                    <div class="p-4 bg-purple-50/60 rounded-xl border border-purple-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-800 font-bold text-xs rounded mb-1">Tingkat 3: Super Grosir</span>
                            <label class="block text-xs font-semibold text-purple-900">Harga Super Grosir (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_super_wholesale" value="{{ old('price_super_wholesale', $superWholesale->price ?? 0) }}" required min="0" step="500"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-purple-300 font-mono text-sm font-bold text-purple-900 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-purple-900 mt-6 sm:mt-5">Minimal Qty Super Grosir</label>
                            <input type="number" name="min_qty_super_wholesale" value="{{ old('min_qty_super_wholesale', $superWholesale->min_qty ?? 36) }}" required min="3"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-purple-300 font-mono text-sm text-slate-900 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="px-6 py-3 bg-[#881337] hover:bg-[#70102e] active:bg-[#5c0d26] text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                    Simpan Perubahan Harga
                </button>
                <a href="{{ route('products.index') }}"
                   class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

