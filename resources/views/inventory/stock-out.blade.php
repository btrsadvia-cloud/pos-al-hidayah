<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('inventory.index') }}" class="text-xs text-[#881337] hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Mutasi Inventori</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catat Stok Keluar (Manual)</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pengurangan stok barang di luar penjualan (barang rusak, cacat, hilang, sample pajangan)</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('inventory.stock-out.store') }}" class="space-y-4">
                @csrf

                <!-- Cari Produk (Ketik Manual) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ old('product_id') }}',
                        items: {{ json_encode($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'extra' => 'Barcode: ' . ($p->barcode ?: '-') . ' | Sisa stok: ' . $p->stock . ' ' . ($p->unit->name ?? 'Pcs')])) }},
                        init() {
                            if (this.selectedId) {
                                const found = this.items.find(i => i.id == this.selectedId);
                                if (found) this.search = found.name;
                            }
                        },
                        get filtered() {
                            if (!this.search.trim()) return this.items.slice(0, 20);
                            const q = this.search.toLowerCase();
                            return this.items.filter(i => i.name.toLowerCase().includes(q) || i.extra.toLowerCase().includes(q)).slice(0, 20);
                        },
                        select(it) {
                            this.selectedId = it.id;
                            this.search = it.name;
                            this.open = false;
                        },
                        checkAuto() {
                            const q = this.search.trim().toLowerCase();
                            if (!q) { this.selectedId = ''; return; }
                            const match = this.items.find(i => i.name.toLowerCase() === q);
                            if (match) { this.selectedId = match.id; }
                            else if (this.filtered.length === 1) { this.selectedId = this.filtered[0].id; }
                        },
                        clear() {
                            this.selectedId = '';
                            this.search = '';
                            this.open = true;
                        }
                    }" class="relative">
                        <input type="hidden" id="product_id" name="product_id" :value="selectedId">
                        <div class="relative">
                            <input type="text"
                                   x-model="search"
                                   @focus="open = true"
                                   @click="open = true"
                                   @input="checkAuto()"
                                   @keydown.enter.prevent="if (filtered.length > 0) { select(filtered[0]); }"
                                   placeholder="Ketik manual nama atau barcode produk..."
                                   autocomplete="off"
                                   required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] outline-none text-sm text-slate-900 pr-10">
                            <button type="button" x-show="search.length > 0" @click="clear()"
                                    class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">
                                &times;
                            </button>
                        </div>
                        <div x-show="selectedId" class="mt-1 text-xs text-emerald-700 font-semibold flex items-center space-x-1">
                            <span>✓ Produk terpilih:</span>
                            <span class="font-bold" x-text="items.find(i => i.id == selectedId)?.name"></span>
                        </div>
                        <div x-show="open && filtered.length > 0"
                             @click.outside="open = false"
                             x-cloak
                             class="absolute z-30 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 text-xs">
                            <template x-for="it in filtered" :key="it.id">
                                <button type="button" @click="select(it)"
                                        class="w-full text-left px-3.5 py-2.5 hover:bg-rose-50 hover:text-[#4a0b1f] transition flex justify-between items-center cursor-pointer">
                                    <div>
                                        <span class="font-bold text-slate-800 block text-xs" x-text="it.name"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="it.extra"></span>
                                    </div>
                                    <span class="text-[#881337] font-bold ml-2 shrink-0" x-show="selectedId == it.id">✓ Terpilih</span>
                                </button>
                            </template>
                        </div>
                        <div x-show="open && search.trim() !== '' && filtered.length === 0"
                             @click.outside="open = false"
                             x-cloak
                             class="absolute z-30 left-0 right-0 mt-1 p-3 bg-white border border-slate-200 rounded-xl shadow-lg text-xs text-slate-400 text-center">
                            Produk tidak ditemukan. Silakan ketik nama lain.
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-slate-800 mb-1">Jumlah Keluar <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="1"
                               placeholder="Contoh: 2"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] font-mono text-sm text-slate-900 outline-none">
                    </div>

                    <div>
                        <label for="movement_date" class="block text-sm font-semibold text-slate-800 mb-1">Tanggal Keluar <span class="text-red-500">*</span></label>
                        <input type="date" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] text-sm text-slate-900 outline-none">
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-slate-800 mb-1">Alasan Pengeluaran Stok <span class="text-red-500">*</span></label>
                    <textarea id="reason" name="reason" required rows="3"
                              placeholder="Wajib diisi: sebutkan alasan rinci, misalnya: Cacat robek pada bagian kerah, sample display etalase, hilang saat opname, dll."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] text-sm text-slate-900 outline-none">{{ old('reason') }}</textarea>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                        Simpan Stok Keluar
                    </button>
                    <a href="{{ route('inventory.index') }}"
                       class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
