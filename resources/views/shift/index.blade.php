<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Shift Kasir</h1>
            <p class="text-sm text-slate-500 mt-0.5">Buka kas awal sebelum bertransaksi dan hitung fisik kas saat pergantian atau penutupan shift.</p>
        </div>

        <!-- Shift Status Box -->
        @if ($currentShift)
            <!-- Active Shift Card (Tutup Shift) -->
            <div class="bg-white rounded-2xl p-6 border-2 border-emerald-500 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                SHIFT AKTIF
                            </span>
                            <h3 class="font-bold text-slate-900 text-base">Dibuka sejak {{ $currentShift->opened_at->format('d/m/Y H:i') }}</h3>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-500 block">Kas Awal (Modal Kasir):</span>
                        <span class="text-lg font-bold text-slate-900">{{ format_rupiah($currentShift->opening_cash) }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('shift.close') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="counted_cash" class="block text-sm font-semibold text-slate-800 mb-1">
                            Kas Hasil Hitung Fisik (Uang Tunai di Laci Saat Ini)
                        </label>
                        <p class="text-xs text-slate-500 mb-2">
                            Hitung seluruh uang fisik (modal awal + penerimaan tunai hari ini) lalu masukkan nominalnya di bawah ini:
                        </p>
                        <input type="number" id="counted_cash" name="counted_cash" required min="0" step="500"
                               placeholder="Contoh: 1500000"
                               class="w-full max-w-md px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono text-base font-bold text-slate-900 outline-none">
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <button type="submit"
                                onclick="return window.customConfirm(event, 'Tutup shift kasir sekarang dan simpan rekapan kas?', 'Konfirmasi Tutup Shift', 'Ya, Tutup Shift')"
                                class="px-5 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                            Tutup Shift Sekarang
                        </button>
                        <a href="{{ route('pos.index') }}"
                           class="px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-sm shadow-xs transition min-h-[44px]">
                            Lanjut ke Kasir POS &rarr;
                        </a>
                    </div>
                </form>
            </div>
        @else
            <!-- No Active Shift Card (Buka Shift) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Belum Ada Shift yang Terbuka</h3>
                        <p class="text-xs text-slate-500">Buka shift baru dan masukkan modal kas awal untuk mulai menggunakan layar kasir POS.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('shift.open') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="opening_cash" class="block text-sm font-semibold text-slate-800 mb-1">
                            Kas Awal (Modal Uang Kembalian di Laci Kasir)
                        </label>
                        <input type="number" id="opening_cash" name="opening_cash" required min="0" step="500"
                               placeholder="Contoh: 200000"
                               class="w-full max-w-md px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono text-base font-bold text-slate-900 outline-none">
                    </div>

                    <button type="submit"
                            class="px-6 py-3 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold rounded-xl text-sm shadow-md transition cursor-pointer min-h-[44px]">
                        Buka Shift Baru
                    </button>
                </form>
            </div>
        @endif

        <!-- Shift History Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Riwayat Shift Anda</h3>
                <span class="text-xs text-slate-400">10 shift terakhir</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Waktu Buka</th>
                            <th class="py-3 px-4">Waktu Tutup</th>
                            <th class="py-3 px-4 text-right">Kas Awal</th>
                            <th class="py-3 px-4 text-right">Kas Diharapkan</th>
                            <th class="py-3 px-4 text-right">Kas Fisik</th>
                            <th class="py-3 px-4 text-center">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($shifts as $s)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono">{{ $s->opened_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 font-mono">
                                    {{ $s->closed_at ? $s->closed_at->format('d/m/Y H:i') : 'Masih Terbuka' }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium">{{ format_rupiah($s->opening_cash) }}</td>
                                <td class="py-3 px-4 text-right font-medium">
                                    {{ $s->expected_cash !== null ? format_rupiah($s->expected_cash) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium">
                                    {{ $s->counted_cash !== null ? format_rupiah($s->counted_cash) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($s->difference === null)
                                        <span class="text-slate-400">-</span>
                                    @elseif($s->difference === 0)
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-semibold">Pas</span>
                                    @elseif($s->difference > 0)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-semibold">+{{ format_rupiah($s->difference) }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded font-semibold">-{{ format_rupiah(abs($s->difference)) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada riwayat shift</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

