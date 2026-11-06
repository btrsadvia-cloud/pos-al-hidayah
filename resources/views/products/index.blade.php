<x-app-layout>
    <div x-data="{
        showImageModal: false,
        activeProductId: null,
        activeProductName: '',
        currentImagePreview: '',
        newImagePreview: null,
        openImageModal(id, name, imgUrl) {
            this.activeProductId = id;
            this.activeProductName = name;
            this.currentImagePreview = imgUrl;
            this.newImagePreview = null;
            this.showImageModal = true;
        },
        previewFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.newImagePreview = ev.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }" class="space-y-6">
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
                    <input type="text" name="category" value="{{ request('category') }}"
                           placeholder="Ketik kategori produk..."
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-teal-500 text-xs">
                </div>

                <div class="sm:col-span-3 flex items-center space-x-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition cursor-pointer min-h-[40px]">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'category_id', 'low_stock']))
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
                                        <div class="relative group shrink-0 cursor-pointer"
                                             @click="openImageModal({{ $p->id }}, '{{ addslashes($p->name) }}', '{{ $p->image ? asset('storage/' . $p->image) : '' }}')"
                                             title="Klik untuk ganti foto produk ini">
                                            @if($p->image)
                                                <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 font-bold flex items-center justify-center border border-teal-100 text-sm">
                                                    {{ substr($p->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <!-- Camera icon overlay -->
                                            <div class="absolute inset-0 bg-slate-900/60 rounded-xl opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-[9px] font-bold transition">
                                                <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Ganti</span>
                                            </div>
                                        </div>
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
                                        <div class="text-[10px] text-slate-400">Min. {{ $wholesale->min_qty }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($superWholesale)
                                        <div class="font-bold text-purple-700">{{ format_rupiah($superWholesale->price) }}</div>
                                        <div class="text-[10px] text-slate-400">Min. {{ $superWholesale->min_qty }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('inventory.card', $p) }}" class="text-blue-600 hover:underline font-semibold" title="Kartu Stok">Kartu</a>
                                    <a href="{{ route('products.prices', $p) }}" class="text-amber-600 hover:underline font-semibold" title="Atur 3 Tingkat Harga">Harga</a>
                                    <a href="{{ route('products.edit', $p) }}" class="text-teal-700 hover:underline font-semibold">Ubah</a>
                                    <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return window.customConfirm(event, 'Apakah Anda yakin ingin menghapus produk {{ addslashes($p->name) }}?', 'Hapus Produk', 'Ya, Hapus')">
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

        <!-- Quick Image Upload Modal -->
        <div x-show="showImageModal" x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showImageModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Ganti Foto Produk</h3>
                        <p class="text-xs text-slate-500 truncate max-w-[280px]" x-text="activeProductName"></p>
                    </div>
                    <button type="button" @click="showImageModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-lg">&times;</button>
                </div>

                <form :action="'{{ url('/products') }}/' + activeProductId + '/image'" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <!-- Current / New Preview -->
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl">
                        <template x-if="newImagePreview">
                            <img :src="newImagePreview" class="w-40 h-40 object-cover rounded-xl shadow-md border border-teal-500">
                        </template>
                        <template x-if="!newImagePreview && currentImagePreview">
                            <img :src="currentImagePreview" class="w-40 h-40 object-cover rounded-xl shadow-sm border border-slate-200">
                        </template>
                        <template x-if="!newImagePreview && !currentImagePreview">
                            <div class="w-40 h-40 rounded-xl bg-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs">
                                <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Belum ada foto</span>
                            </div>
                        </template>
                        <p class="text-[11px] text-slate-400 mt-2">Pratinjau foto yang akan ditampilkan</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Berkas Foto Baru</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" required
                               @change="previewFile"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 5MB.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showImageModal = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Simpan Foto Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

