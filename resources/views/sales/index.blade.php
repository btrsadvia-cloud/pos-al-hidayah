<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Penjualan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Daftar transaksi penjualan faktur Toko Al-Hidayah</p>
            </div>
            <a href="{{ route('pos.index') }}"
               class="inline-flex items-center space-x-1.5 bg-[#881337] hover:bg-[#70102e] text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Buka Kasir POS</span>
            </a>
        </div>

        <!-- Filter Bar (Metode Bayar Rapi Bebas Menumpuk) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('sales.index') }}" class="space-y-3 text-xs">
                <!-- Baris 1: Pencarian, Kasir, Tanggal & Tombol Cari -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="{{ auth()->user()->isAdmin() ? 'sm:col-span-4' : 'sm:col-span-5' }}">
                        <label class="block font-medium text-slate-700 mb-1">Cari Faktur / Pelanggan</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="No. Faktur (INV-...) atau nama..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                    </div>

                    @can('admin')
                        <div class="sm:col-span-3">
                            <label class="block font-medium text-slate-700 mb-1">Kasir</label>
                            <div x-data="{
                                open: false,
                                search: '',
                                selectedId: '{{ request('user_id') }}',
                                items: {{ json_encode($cashiers->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
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
                                select(it) { this.selectedId = it.id; this.search = it.name; this.open = false; },
                                clear() { this.selectedId = ''; this.search = ''; this.open = true; }
                            }" class="relative">
                                <input type="hidden" name="user_id" :value="selectedId">
                                <div class="relative">
                                    <input type="text" x-model="search" @focus="open = true" @click="open = true" placeholder="Ketik nama kasir..." autocomplete="off"
                                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-[#881337] text-xs pr-8">
                                    <button type="button" x-show="search.length > 0" @click="clear()" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">&times;</button>
                                </div>
                                <div x-show="open && filtered.length > 0" @click.outside="open = false" x-cloak
                                     class="absolute z-30 left-0 right-0 mt-1 max-h-44 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 text-xs">
                                    <template x-for="it in filtered" :key="it.id">
                                        <button type="button" @click="select(it)" class="w-full text-left px-3 py-2 hover:bg-rose-50 transition cursor-pointer flex justify-between items-center">
                                            <span class="font-semibold text-slate-800" x-text="it.name"></span>
                                            <span class="text-[#881337] font-bold text-[10px]" x-show="selectedId == it.id">✓</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <div class="{{ auth()->user()->isAdmin() ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                        <label class="block font-medium text-slate-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                    </div>

                    <div class="{{ auth()->user()->isAdmin() ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                        <label class="block font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white text-xs">
                    </div>

                    <div class="sm:col-span-1 flex items-end">
                        <button type="submit"
                                class="w-full py-2 px-3 bg-[#881337] hover:bg-[#70102e] text-white font-bold rounded-xl transition cursor-pointer min-h-[38px] shadow-sm">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Baris 2: Metode Pembayaran (Pill Buttons Luas & Bebas Menumpuk) -->
                <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2" x-data="{ method: '{{ request('payment_method') }}' }">
                    <input type="hidden" name="payment_method" :value="method">
                    <span class="text-xs font-semibold text-slate-700 mr-1">Metode Bayar:</span>
                    <button type="button" @click="method = ''; $nextTick(() => $el.closest('form').submit())"
                            :class="method === '' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer">
                        Semua
                    </button>
                    <button type="button" @click="method = 'cash'; $nextTick(() => $el.closest('form').submit())"
                            :class="method === 'cash' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer">
                        💵 Tunai
                    </button>
                    <button type="button" @click="method = 'qris'; $nextTick(() => $el.closest('form').submit())"
                            :class="method === 'qris' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer">
                        📱 QRIS
                    </button>
                    <button type="button" @click="method = 'transfer'; $nextTick(() => $el.closest('form').submit())"
                            :class="method === 'transfer' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer">
                        🏦 Transfer
                    </button>
                    @if(request('search') || request('user_id') || request('payment_method') || request('date_from') || request('date_to'))
                        <a href="{{ route('sales.index') }}" class="ml-auto text-xs text-rose-700 hover:underline font-semibold">
                            Reset Filter &times;
                        </a>
                    @endif
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
                                    <a href="{{ route('sales.show', $s) }}" class="text-[#881337] hover:underline">
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
                                    <a href="{{ route('sales.show', $s) }}" class="text-[#881337] hover:underline font-semibold">
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

