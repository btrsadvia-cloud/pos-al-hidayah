<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('inventory.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Mutasi Inventori</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catat Stok Masuk (Pembelian)</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tambah stok barang masuk dari pemasok beserta pencatatan harga beli (HPP)</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('inventory.stock-in.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="product_id" class="block text-sm font-semibold text-slate-800 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <select id="product_id" name="product_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} (Stok saat ini: {{ $p->stock }} {{ $p->unit->name ?? 'Pcs' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-semibold text-slate-800 mb-1">Pemasok (Supplier) <span class="text-red-500">*</span></label>
                    <select id="supplier_id" name="supplier_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-slate-800 mb-1">Jumlah Masuk <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="1"
                               placeholder="Contoh: 50"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                    </div>

                    <div>
                        <label for="unit_cost" class="block text-sm font-semibold text-slate-800 mb-1">Harga Beli / Satuan (HPP) <span class="text-red-500">*</span></label>
                        <input type="number" id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" required min="0" step="500"
                               placeholder="Contoh: 95000"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                    </div>
                </div>

                <div>
                    <label for="movement_date" class="block text-sm font-semibold text-slate-800 mb-1">Tanggal Masuk / Terima <span class="text-red-500">*</span></label>
                    <input type="date" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-slate-800 mb-1">Keterangan / No. Surat Jalan</label>
                    <input type="text" id="reason" name="reason" value="{{ old('reason') }}"
                           placeholder="Contoh: Pembelian Invoice #INV-PO-20260301"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 text-sm text-slate-900 outline-none">
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                        Simpan Stok Masuk
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

