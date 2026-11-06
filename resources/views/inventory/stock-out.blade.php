<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('inventory.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Mutasi Inventori</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catat Stok Keluar (Manual)</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pengurangan stok barang di luar penjualan (barang rusak, cacat, hilang, sample pajangan)</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('inventory.stock-out.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="product_id" class="block text-sm font-semibold text-slate-800 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <select id="product_id" name="product_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} (Sisa stok: {{ $p->stock }} {{ $p->unit->name ?? 'Pcs' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-slate-800 mb-1">Jumlah Keluar <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="1"
                               placeholder="Contoh: 2"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                    </div>

                    <div>
                        <label for="movement_date" class="block text-sm font-semibold text-slate-800 mb-1">Tanggal Keluar <span class="text-red-500">*</span></label>
                        <input type="date" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 text-sm text-slate-900 outline-none">
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-slate-800 mb-1">Alasan Pengeluaran Stok <span class="text-red-500">*</span></label>
                    <textarea id="reason" name="reason" required rows="3"
                              placeholder="Wajib diisi: sebutkan alasan rinci, misalnya: Cacat robek pada bagian kerah, sample display etalase, hilang saat opname, dll."
                              class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">{{ old('reason') }}</textarea>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin mengurangi stok barang ini?')"
                            class="px-6 py-3 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                        Kurangi Stok
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
