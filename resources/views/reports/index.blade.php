<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Penjualan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Analisis omzet, rekap transaksi, dan ekspor dokumen PDF & Excel</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('reports.pdf', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   class="inline-flex items-center space-x-1.5 bg-red-600 hover:bg-red-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor PDF</span>
                </a>
                <a href="{{ route('reports.excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   class="inline-flex items-center space-x-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs items-end">
                <div class="sm:col-span-4">
                    <label class="block font-medium text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                <div class="sm:col-span-4">
                    <label class="block font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                </div>

                <div class="sm:col-span-4 flex space-x-2">
                    <button type="submit"
                            class="flex-1 py-2 px-4 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition cursor-pointer min-h-[38px]">
                        Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Total Omzet Bersih</span>
                <span class="text-2xl font-bold text-teal-800 mt-1 block">{{ format_rupiah($summary->total_revenue) }}</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Total Transaksi</span>
                <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $summary->total_transactions }} Transaksi</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Total Diskon Diberikan</span>
                <span class="text-2xl font-bold text-red-600 mt-1 block">{{ format_rupiah($summary->total_discount) }}</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Rata-rata / Transaksi</span>
                @php
                    $avg = $summary->total_transactions > 0 ? (int) ($summary->total_revenue / $summary->total_transactions) : 0;
                @endphp
                <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ format_rupiah($avg) }}</span>
            </div>
        </div>

        <!-- Payment Breakdown Cards -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <h3 class="font-bold text-sm text-slate-900 mb-3">Distribusi Metode Pembayaran</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($paymentBreakdown as $pb)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 uppercase block">{{ $pb->payment_method }}</span>
                            <span class="text-[11px] text-slate-500">{{ $pb->count }} Transaksi</span>
                        </div>
                        <span class="font-bold text-sm text-teal-800">{{ format_rupiah($pb->total) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Rincian Faktur Penjualan</h3>
                <span class="text-xs text-slate-400">Periode: {{ date('d/m/Y', strtotime($dateFrom)) }} s/d {{ date('d/m/Y', strtotime($dateTo)) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Tanggal & Waktu</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                            <th class="py-3 px-4 text-right">Diskon</th>
                            <th class="py-3 px-4 text-right">Total Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $s)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono font-bold text-teal-700">
                                    <a href="{{ route('sales.show', $s) }}" class="hover:underline">{{ $s->invoice_number }}</a>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">{{ $s->sold_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $s->user->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $s->customer->name ?? 'Umum' }}</td>
                                <td class="py-3 px-4 uppercase font-semibold text-slate-600">{{ $s->payment_method }}</td>
                                <td class="py-3 px-4 text-right text-slate-700">{{ format_rupiah($s->subtotal) }}</td>
                                <td class="py-3 px-4 text-right text-red-600">{{ $s->discount > 0 ? '-' . format_rupiah($s->discount) : '-' }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">{{ format_rupiah($s->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Tidak ada data transaksi pada rentang tanggal ini
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
