<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <div>
            <a href="{{ route('inventory.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Mutasi Inventori</span>
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kartu Stok Barang</h1>
                    <p class="text-sm text-slate-600 mt-0.5">
                        Produk: <span class="font-bold text-slate-900">{{ $product->name }}</span>
                        &bull; Barcode: <span class="font-mono text-slate-700">{{ $product->barcode ?: '-' }}</span>
                        &bull; Satuan: <span class="font-medium text-slate-700">{{ $product->unit->name ?? 'Pcs' }}</span>
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="window.print()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer min-h-[40px]">
                        Cetak Kartu Stok
                    </button>
                    <a href="{{ route('products.show', $product) }}"
                       class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold transition min-h-[40px] flex items-center">
                        Detail Produk
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Metric Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium">Sisa Saldo Stok Fisik Saat Ini</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $product->stock }} {{ $product->unit->name ?? 'Pcs' }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium">Batas Minimum Stok</span>
                <div class="text-2xl font-bold text-amber-700 mt-1">{{ $product->min_stock }} {{ $product->unit->name ?? 'Pcs' }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium">Harga Beli Terakhir (HPP)</span>
                <div class="text-2xl font-bold text-teal-800 mt-1">
                    {{ $product->latestCost() ? format_rupiah($product->latestCost()) : '-' }}
                </div>
            </div>
        </div>

        <!-- Stock Ledger Table with Running Balance -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Buku Pembantu Kartu Stok (Saldo Berjalan)</h3>
                <span class="text-xs text-slate-400">{{ count($cardRows) }} transaksi tercatat</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Keterangan / Transaksi</th>
                            <th class="py-3 px-4">Pemasok / Sumber</th>
                            <th class="py-3 px-4 text-center text-emerald-700">Masuk (+)</th>
                            <th class="py-3 px-4 text-center text-amber-700">Keluar (-)</th>
                            <th class="py-3 px-4 text-center font-bold text-slate-900 bg-slate-100">Saldo Akhir</th>
                            <th class="py-3 px-4">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($cardRows as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-4 text-slate-600 whitespace-nowrap">{{ $row->movement_date->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-4 font-sans text-slate-800">{{ $row->reason }}</td>
                                <td class="py-2.5 px-4 font-sans text-slate-500">{{ $row->supplier->name ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-emerald-600">
                                    {{ $row->type === 'in' ? '+' . $row->quantity : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-bold text-amber-600">
                                    {{ $row->type === 'out' ? '-' . $row->quantity : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-bold text-slate-900 bg-slate-50/80">
                                    {{ $row->running_balance }}
                                </td>
                                <td class="py-2.5 px-4 font-sans text-slate-500">{{ $row->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 font-sans">
                                    Belum ada pergerakan stok untuk produk ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
