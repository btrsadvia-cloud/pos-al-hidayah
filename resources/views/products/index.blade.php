<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Produk</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola data barang, barcode, stok, dan harga bertingkat</p>
            </div>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Tambah Produk Baru</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                <div class="sm:col-span-5">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau barcode produk..."
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-teal-500 text-xs">
                </div>

                <div class="sm:col-span-4">
                    <select name="category_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-teal-500 text-xs">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center space-x-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition cursor-pointer min-h-[40px]">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'low_stock']))
                        <a href="{{ route('products.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition min-h-[40px] flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Produk</th>
                            <th class="py-3 px-4">Kategori / Satuan</th>
                            <th class="py-3 px-4 text-center">Stok</th>
                            <th class="py-3 px-4 text-right">Harga Eceran</th>
                            <th class="py-3 px-4 text-right">Harga Grosir (Min)</th>
                            <th class="py-3 px-4 text-right">Harga Super Grosir (Min)</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $p)
                            @php
                                $retail = $p->prices->firstWhere('tier', 'retail');
                                $wholesale = $p->prices->firstWhere('tier', 'wholesale');
                                $superWholesale = $p->prices->firstWhere('tier', 'super_wholesale');
                                $isLow = $p->isLowStock();
                                $isOut = $p->stock <= 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        @if($p->image)
                                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-700 font-bold flex items-center justify-center border border-teal-100 shrink-0 text-xs">
                                                {{ substr($p->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', $p) }}" class="font-bold text-slate-900 text-sm hover:text-teal-700 leading-snug block">
                                                {{ $p->name }}
                                            </a>
                                            <span class="text-[11px] font-mono text-slate-400">Barcode: {{ $p->barcode ?: '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-700">{{ $p->category->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $p->unit->name ?? 'Pcs' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($isOut)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Habis</span>
                                    @elseif($isLow)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            {{ $p->stock }} (Min: {{ $p->min_stock }})
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                            {{ $p->stock }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                    {{ $retail ? format_rupiah($retail->price) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($wholesale)
                                        <div class="font-bold text-blue-700">{{ format_rupiah($wholesale->price) }}</div>
                                        <div class="text-[10px] text-slate-400">Min. {{ $wholesale->min_qty }} {{ $p->unit->name ?? 'Pcs' }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($superWholesale)
                                        <div class="font-bold text-purple-700">{{ format_rupiah($superWholesale->price) }}</div>
                                        <div class="text-[10px] text-slate-400">Min. {{ $superWholesale->min_qty }} {{ $p->unit->name ?? 'Pcs' }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('inventory.card', $p) }}" class="text-blue-600 hover:underline font-semibold" title="Kartu Stok">Kartu</a>
                                    <a href="{{ route('products.prices', $p) }}" class="text-amber-600 hover:underline font-semibold" title="Atur 3 Tingkat Harga">Harga</a>
                                    <a href="{{ route('products.edit', $p) }}" class="text-teal-700 hover:underline font-semibold">Ubah</a>
                                    <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    Tidak ada produk yang sesuai dengan pencarian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
