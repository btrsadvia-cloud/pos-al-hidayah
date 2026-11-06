<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mutasi Inventori Stok</h1>
                <p class="text-sm text-slate-500 mt-0.5">Riwayat lengkap barang masuk (pembelian) dan barang keluar (penjualan, rusak, hilang)</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('inventory.stock-in') }}"
                   class="inline-flex items-center space-x-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Catat Stok Masuk</span>
                </a>
                <a href="{{ route('inventory.stock-out') }}"
                   class="inline-flex items-center space-x-1.5 bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                    <span>- Catat Stok Keluar</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                <div class="sm:col-span-3">
                    <label class="block font-medium text-slate-600 mb-1">Cari Produk</label>
                    <input type="text" name="product" value="{{ request('product') }}"
                           placeholder="Ketik nama atau barcode barang..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-600 mb-1">Jenis Mutasi</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                        <option value="">Semua (In/Out)</option>
                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stok Masuk (In)</option>
                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stok Keluar (Out)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                <div class="sm:col-span-3 flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 py-2 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition cursor-pointer min-h-[38px]">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['product_id', 'type', 'date_from', 'date_to']))
                        <a href="{{ route('inventory.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition min-h-[38px] flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Movements Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Produk</th>
                            <th class="py-3 px-4 text-center">Jenis</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4 text-right">Harga Beli (HPP)</th>
                            <th class="py-3 px-4">Pemasok / Sumber</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($movements as $m)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">{{ $m->movement_date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    <a href="{{ route('inventory.card', $m->product) }}" class="hover:text-teal-700 hover:underline">
                                        {{ $m->product->name ?? 'Produk dihapus' }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($m->type === 'in')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold">MASUK</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-bold">KELUAR</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-bold {{ $m->type === 'in' ? 'text-emerald-700' : 'text-amber-800' }}">
                                    {{ $m->type === 'in' ? '+' : '-' }}{{ $m->quantity }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium">
                                    {{ $m->unit_cost ? format_rupiah($m->unit_cost) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $m->supplier->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 max-w-xs truncate">{{ $m->reason }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $m->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">Belum ada mutasi stok yang sesuai</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

