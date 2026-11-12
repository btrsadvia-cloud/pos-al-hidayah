<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('sales.index') }}" class="text-xs text-[#881337] hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Riwayat Penjualan</span>
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $sale->invoice_number }}</h1>
                        @if($sale->status === 'completed')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                        @elseif($sale->status === 'cancelled')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Dibatalkan</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Waktu: {{ $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i:s') : '-' }} &bull; Kasir: {{ $sale->user->name ?? '-' }}
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer min-h-[40px] flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Struk</span>
                    </a>

                    @can('cancel-sale')
                        @if($sale->status === 'completed')
                            <div x-data="{ openModal: false }">
                                <button type="button" @click="openModal = true"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold transition cursor-pointer min-h-[40px]">
                                    Batalkan Penjualan
                                </button>

                                <!-- Cancel Confirmation Modal -->
                                <div x-show="openModal" x-cloak
                                     class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl text-left">
                                        <h3 class="font-bold text-base text-slate-900">Konfirmasi Pembatalan Penjualan</h3>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Seluruh barang pada transaksi ini akan dikembalikan ke stok fisik melalui mutasi stok baru. Penjualan tidak akan dihapus permanen.
                                        </p>

                                        <form method="POST" action="{{ route('sales.cancel', $sale) }}" class="mt-4 space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                                                <textarea name="reason" required rows="2" placeholder="Contoh: Kesalahan input kasir, pelanggan membatalkan barang"
                                                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-red-500"></textarea>
                                            </div>

                                            <div class="flex justify-end space-x-2 pt-2">
                                                <button type="button" @click="openModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold">
                                                    Ya, Batalkan Transaksi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        @if($sale->note)
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                <span class="font-bold">Catatan:</span> {{ $sale->note }}
            </div>
        @endif

        <!-- Customer & Payment Metadata -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-400 font-medium">Pelanggan</span>
                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $sale->customer->name ?? 'Pembeli Umum' }}</div>
                <div class="text-[11px] text-slate-500">{{ $sale->customer->phone ?? '-' }}</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-400 font-medium">Metode Pembayaran</span>
                <div class="font-bold text-slate-900 text-sm mt-0.5 uppercase">{{ $sale->payment_method }}</div>
                @if($sale->payment_reference)
                    <div class="text-[11px] text-slate-500 font-mono">Ref: {{ $sale->payment_reference }}</div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <span class="text-xs text-slate-400 font-medium">Shift Kasir</span>
                <div class="font-bold text-slate-900 text-sm mt-0.5">
                    {{ $sale->shift ? 'Shift #' . $sale->shift->id . ' (' . $sale->shift->opened_at->format('d/m/Y') . ')' : '-' }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900">Daftar Barang (Snapshot Saat Penjualan)</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Produk</th>
                            <th class="py-3 px-4 text-right">Harga Satuan</th>
                            <th class="py-3 px-4 text-center">Jumlah (Qty)</th>
                            <th class="py-3 px-4 text-right">Diskon Item</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sale->items as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $item->product_name }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium text-slate-700">
                                    {{ format_rupiah($item->unit_price) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-900">
                                    {{ $item->quantity }} {{ $item->unit_name }}
                                </td>
                                <td class="py-3 px-4 text-right text-red-600">
                                    {{ $item->item_discount > 0 ? '-' . format_rupiah($item->item_discount) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                    {{ format_rupiah($item->subtotal) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Calculation Summary -->
            <div class="p-4 bg-slate-50/70 border-t border-slate-200 flex flex-col items-end space-y-1.5 text-xs">
                <div class="flex justify-between w-64 text-slate-600">
                    <span>Subtotal Barang:</span>
                    <span class="font-medium text-slate-900">{{ format_rupiah($sale->subtotal) }}</span>
                </div>
                <div class="flex justify-between w-64 text-slate-600">
                    <span>Total Diskon (Item + Faktur):</span>
                    <span class="font-medium text-red-600">-{{ format_rupiah($sale->discount) }}</span>
                </div>
                <div class="flex justify-between w-64 text-slate-900 font-bold text-base pt-1 border-t border-slate-200">
                    <span>TOTAL AKHIR:</span>
                    <span class="text-[#70102e]">{{ format_rupiah($sale->total) }}</span>
                </div>
                <div class="flex justify-between w-64 text-slate-600 pt-1">
                    <span>Jumlah Dibayarkan:</span>
                    <span class="font-medium text-slate-900">{{ format_rupiah($sale->paid_amount) }}</span>
                </div>
                @if($sale->payment_method === 'cash')
                    <div class="flex justify-between w-64 text-slate-600">
                        <span>Kembalian:</span>
                        <span class="font-bold text-slate-900">{{ format_rupiah($sale->change_amount) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

