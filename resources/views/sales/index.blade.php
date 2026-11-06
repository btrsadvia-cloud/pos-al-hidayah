<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Penjualan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Daftar transaksi penjualan faktur Toko Al-Hidayah</p>
            </div>
            <a href="{{ route('pos.index') }}"
               class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Buka Kasir POS</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                <div class="sm:col-span-3">
                    <label class="block font-medium text-slate-600 mb-1">Cari Faktur / Pelanggan</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Faktur (INV-...) atau nama..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                @can('admin')
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-slate-600 mb-1">Kasir</label>
                        <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                            <option value="">Semua Kasir</option>
                            @foreach($cashiers as $ck)
                                <option value="{{ $ck->id }}" {{ request('user_id') == $ck->id ? 'selected' : '' }}>{{ $ck->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="sm:col-span-2">
                    <label class="block font-medium text-slate-600 mb-1">Metode Bayar</label>
                    <select name="payment_method" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
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

                <div class="sm:col-span-1 flex items-end">
                    <button type="submit"
                            class="w-full py-2 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition cursor-pointer min-h-[38px]">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Sales Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Waktu Transaksi</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Total Belanja</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $s)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('sales.show', $s) }}" class="text-teal-700 hover:underline">
                                        {{ $s->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $s->sold_at ? $s->sold_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-medium">{{ $s->user->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $s->customer->name ?? 'Umum' }}</td>
                                <td class="py-3 px-4 font-semibold uppercase text-slate-600">{{ $s->payment_method }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">{{ format_rupiah($s->total) }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($s->status === 'completed')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            Selesai
                                        </span>
                                    @elseif($s->status === 'cancelled')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                            {{ $s->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('sales.receipt', $s) }}" target="_blank" class="text-slate-600 hover:text-slate-900 font-semibold" title="Cetak Struk">
                                        Struk
                                    </a>
                                    <a href="{{ route('sales.show', $s) }}" class="text-teal-700 hover:underline font-semibold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">
                                    Belum ada transaksi penjualan yang sesuai dengan filter
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

