<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('products.index') }}" class="text-xs text-[#881337] hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Katalog Produk</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Data Produk</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $product->name }}</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Section 1: Detail Produk -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <h3 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">Informasi Produk</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] focus:border-[#881337] outline-none text-sm text-slate-900">
                    </div>

                    <div>
                        <label for="barcode" class="block text-sm font-semibold text-slate-800 mb-1">Barcode / SKU</label>
                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] font-mono text-sm text-slate-900 outline-none">
                    </div>

                    <div x-data="{ preview: null }">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Foto Produk (Dari Berkas Pribadi)</label>
                        <div class="flex items-center space-x-3 mb-2">
                            <template x-if="preview">
                                <img :src="preview" class="w-16 h-16 rounded-xl object-cover border-2 border-[#881337] shadow-sm shrink-0">
                            </template>
                            <template x-if="!preview">
                                @if($product->image_url ?? $product->image)
                                    <img src="{{ $product->image_url ?? asset('storage/' . $product->image) }}"
                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                                         class="w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-xs shrink-0">
                                    <div style="display:none;" class="w-16 h-16 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-semibold shrink-0">Tanpa Foto</div>
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-semibold shrink-0">Tanpa Foto</div>
                                @endif
                            </template>
                            <div class="flex-1">
                                <input type="file" id="image" name="image" accept="image/*"
                                       @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = (e) => preview = e.target.result; r.readAsDataURL(f); }"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-[#881337] hover:file:bg-rose-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">Pilih berkas gambar JPG/PNG dari komputer untuk mengganti foto</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Kategori Produk <span class="text-red-500">*</span></label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('category_id', $product->category_id) }}',
                            items: {{ json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
                            init() {
                                if (this.selectedId) {
                                    const found = this.items.find(i => i.id == this.selectedId);
                                    if (found) this.search = found.name;
                                }
                            },
                            get filtered() {
                                if (!this.search.trim()) return this.items;
                                const q = this.search.toLowerCase();
                                return this.items.filter(i => i.name.toLowerCase().includes(q));
                            },
                            select(it) {
                                this.selectedId = it.id;
                                this.search = it.name;
                                this.open = false;
                            },
                            clear() {
                                this.selectedId = '';
                                this.search = '';
                                this.open = true;
                            }
                        }" class="relative">
                            <input type="hidden" id="category_id" name="category_id" :value="selectedId" required>
                            <div class="relative">
                                <input type="text"
                                       x-model="search"
                                       @focus="open = true"
                                       @click="open = true"
                                       placeholder="Ketik manual kategori produk..."
                                       autocomplete="off"
                                       required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] outline-none text-sm text-slate-900 pr-10">
                                <button type="button" x-show="search.length > 0" @click="clear()"
                                        class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">
                                    &times;
                                </button>
                            </div>
                            <div x-show="open && filtered.length > 0"
                                 @click.outside="open = false"
                                 x-cloak
                                 class="absolute z-30 left-0 right-0 mt-1 max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 text-xs">
                                <template x-for="it in filtered" :key="it.id">
                                    <button type="button" @click="select(it)"
                                            class="w-full text-left px-3.5 py-2.5 hover:bg-rose-50 hover:text-[#4a0b1f] transition flex justify-between items-center cursor-pointer">
                                        <span class="font-semibold text-slate-800 text-xs" x-text="it.name"></span>
                                        <span class="text-[#881337] font-bold ml-2 shrink-0" x-show="selectedId == it.id">✓ Terpilih</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="unit_id" value="{{ old('unit_id', $product->unit_id ?? 1) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Stok Saat Ini (Disesuaikan lewat mutasi stok)</label>
                        <div class="px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 font-mono text-sm font-bold text-slate-700">
                            {{ $product->stock }} {{ $product->unit->name ?? 'Pcs' }}
                        </div>
                    </div>

                    <div>
                        <label for="min_stock" class="block text-sm font-semibold text-slate-800 mb-1">Batas Minimum Stok <span class="text-red-500">*</span></label>
                        <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" required min="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] outline-none font-mono text-sm text-slate-900">
                    </div>
                </div>
            </div>

            <!-- Section 2: 3 Tingkat Harga -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="pb-2 border-b border-slate-100">
                    <h3 class="font-bold text-base text-slate-900">3 Tingkat Harga Bertingkat</h3>
                </div>

                <div class="space-y-4">
                    <!-- Retail -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-slate-200 text-slate-800 font-bold text-xs rounded mb-1">Tingkat 1: Eceran</span>
                            <label class="block text-xs font-semibold text-slate-700">Harga Eceran (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_retail" value="{{ old('price_retail', $retail->price ?? 0) }}" required min="0" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-slate-300 font-mono text-sm font-bold text-slate-900 outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mt-6 sm:mt-5">Minimal Qty Eceran <span class="text-red-500">*</span></label>
                            <input type="number" name="min_qty_retail" value="{{ old('min_qty_retail', $retail->min_qty ?? 1) }}" required min="1" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-slate-300 font-mono text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                    </div>

                    <!-- Wholesale -->
                    <div class="p-4 bg-rose-50/40 rounded-xl border border-rose-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-rose-100 text-[#70102e] font-bold text-xs rounded mb-1">Tingkat 2: Grosir</span>
                            <label class="block text-xs font-semibold text-[#70102e]">Harga Grosir (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_wholesale" value="{{ old('price_wholesale', $wholesale->price ?? 0) }}" required min="0" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-rose-300 font-mono text-sm font-bold text-[#70102e] outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#70102e] mt-6 sm:mt-5">Minimal Qty Grosir <span class="text-red-500">*</span></label>
                            <input type="number" name="min_qty_wholesale" value="{{ old('min_qty_wholesale', $wholesale->min_qty ?? 12) }}" required min="2" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-rose-300 font-mono text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                    </div>

                    <!-- Super Wholesale -->
                    <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-900 font-bold text-xs rounded mb-1">Tingkat 3: Super Grosir</span>
                            <label class="block text-xs font-semibold text-amber-900">Harga Super Grosir (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_super_wholesale" value="{{ old('price_super_wholesale', $superWholesale->price ?? 0) }}" required min="0" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-amber-300 font-mono text-sm font-bold text-amber-900 outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-purple-900 mt-6 sm:mt-5">Minimal Qty Super Grosir <span class="text-red-500">*</span></label>
                            <input type="number" name="min_qty_super_wholesale" value="{{ old('min_qty_super_wholesale', $superWholesale->min_qty ?? 36) }}" required min="3" step="1"
                                   class="w-full mt-1 px-3 py-2 bg-white rounded-lg border border-purple-300 font-mono text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#881337]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="px-6 py-3 bg-[#881337] hover:bg-[#70102e] active:bg-[#5c0d26] text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                    Simpan Perubahan
                </button>
                <a href="{{ route('products.index') }}"
                   class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

