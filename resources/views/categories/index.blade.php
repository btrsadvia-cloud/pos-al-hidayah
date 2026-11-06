<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kategori Produk</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola pengelompokan produk Toko Al-Hidayah</p>
            </div>
            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Tambah Kategori</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4 text-center">Jumlah Produk</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $idx => $c)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900 text-sm">{{ $c->name }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full font-medium">
                                        {{ $c->products()->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('categories.edit', $c) }}" class="text-teal-700 hover:underline font-semibold">Ubah</a>
                                    <form method="POST" action="{{ route('categories.destroy', $c) }}" class="inline" onsubmit="return window.customConfirm(event, 'Hapus kategori {{ addslashes($c->name) }}?', 'Hapus Kategori', 'Ya, Hapus')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">Belum ada kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

