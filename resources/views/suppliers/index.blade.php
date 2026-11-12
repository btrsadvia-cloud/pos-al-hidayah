<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pemasok (Suppliers)</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola data produsen dan distributor busana & perlengkapan ibadah</p>
            </div>
            <a href="{{ route('suppliers.create') }}"
               class="inline-flex items-center space-x-1.5 bg-[#881337] hover:bg-[#70102e] text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Tambah Pemasok</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100">
                <form method="GET" action="{{ route('suppliers.index') }}" class="max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau kontak pemasok..."
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:bg-white focus:ring-2 focus:ring-[#881337]">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Pemasok</th>
                            <th class="py-3 px-4">Kontak / Telepon</th>
                            <th class="py-3 px-4">Alamat</th>
                            <th class="py-3 px-4">Rekening Bank</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $s)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900 text-sm">{{ $s->name }}</td>
                                <td class="py-3 px-4 text-slate-700 font-mono">{{ $s->contact }}</td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">{{ $s->address }}</td>
                                <td class="py-3 px-4 text-slate-700 font-mono">{{ $s->bank_account ?: '-' }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('suppliers.edit', $s) }}" class="text-[#881337] hover:underline font-semibold">Ubah</a>
                                    <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline" onsubmit="return window.customConfirm(event, 'Hapus data pemasok {{ addslashes($s->name) }}?', 'Hapus Pemasok', 'Ya, Hapus')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data pemasok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

