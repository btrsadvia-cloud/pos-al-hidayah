<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('products.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Katalog Produk</span>
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $product->name }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Barcode: <span class="font-mono font-bold text-slate-700">{{ $product->barcode ?: '-' }}</span> &bull; Kategori: {{ $product->category->name ?? '-' }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('inventory.card', $product) }}"
                       class="px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-xs font-semibold hover:bg-blue-100 transition">
                        Lihat Kartu Stok &rarr;
                    </a>
                    <a href="{{ route('products.edit', $product) }}"
                       class="px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-semibold hover:bg-teal-700 transition">
                        Ubah Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Info & Price Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Stock Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Sisa Stok Fisik</span>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-black {{ $product->isLowStock() ? 'text-amber-600' : 'text-slate-900' }}">{{ $product->stock }}</span>
                    <span class="text-sm font-semibold text-slate-500">{{ $product->unit->name ?? 'Pcs' }}</span>
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Batas Minimum: {{ $product->min_stock }} {{ $product->unit->name ?? 'Pcs' }}</span>
            </div>

            <!-- Retail Price -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Harga Eceran (Tingkat 1)</span>
                @php $retail = $product->prices->firstWhere('tier', 'retail'); @endphp
                <div class="text-2xl font-black text-teal-800 mt-2">
                    {{ $retail ? format_rupiah($retail->price) : '-' }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Min. pembelian: {{ $retail->min_qty ?? 1 }} {{ $product->unit->name ?? 'Pcs' }}</span>
            </div>

            <!-- Latest Cost (HPP) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Harga Beli Terakhir (HPP)</span>
                <div class="text-2xl font-black text-slate-800 mt-2">
                    {{ $product->latestCost() ? format_rupiah($product->latestCost()) : 'Belum Ada' }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Berdasarkan mutasi stok masuk terbaru</span>
            </div>
        </div>

        <!-- 3-Tier Prices Overview -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-900 text-sm">Struktur 3 Tingkat Harga</h3>
                <a href="{{ route('products.prices', $product) }}" class="text-xs text-teal-700 hover:underline font-semibold">Atur Harga &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($product->prices as $pPrice)
                    <div class="p-3.5 rounded-xl border {{ $pPrice->tier === 'super_wholesale' ? 'bg-purple-50/50 border-purple-200' : ($pPrice->tier === 'wholesale' ? 'bg-blue-50/50 border-blue-200' : 'bg-slate-50 border-slate-200') }}">
                        <span class="text-[11px] font-bold uppercase tracking-wider block {{ $pPrice->tier === 'super_wholesale' ? 'text-purple-700' : ($pPrice->tier === 'wholesale' ? 'text-blue-700' : 'text-slate-600') }}">
                            {{ tier_label($pPrice->tier) }}
                        </span>
                        <div class="text-lg font-black text-slate-900 mt-1">{{ format_rupiah($pPrice->price) }}</div>
                        <span class="text-xs text-slate-500 mt-0.5 block">Mulai dari &ge; {{ $pPrice->min_qty }} {{ $product->unit->name ?? 'Pcs' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900">Mutasi Stok Terakhir</h3>
                <a href="{{ route('inventory.card', $product) }}" class="text-xs text-teal-700 hover:underline font-semibold">Lihat Kartu Lengkap &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Tanggal</th>
                            <th class="py-2.5 px-4">Jenis</th>
                            <th class="py-2.5 px-4 text-center">Jumlah</th>
                            <th class="py-2.5 px-4">Keterangan / Alasan</th>
                            <th class="py-2.5 px-4">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($product->stockMovements()->latest('id')->limit(5)->get() as $m)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-4 font-mono">{{ $m->movement_date->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-4">
                                    @if($m->type === 'in')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold">Masuk</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-bold">Keluar</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center font-bold {{ $m->type === 'in' ? 'text-emerald-700' : 'text-amber-800' }}">
                                    {{ $m->type === 'in' ? '+' : '-' }}{{ $m->quantity }}
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">{{ $m->reason }}</td>
                                <td class="py-2.5 px-4 text-slate-500">{{ $m->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada mutasi stok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

