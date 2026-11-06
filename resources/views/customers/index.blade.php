<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pelanggan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola data pembeli tetap / member Toko Al-Hidayah</p>
            </div>
            <a href="{{ route('customers.create') }}"
               class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Tambah Pelanggan</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100">
                <form method="GET" action="{{ route('customers.index') }}" class="max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau no. telepon pelanggan..."
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:bg-white focus:ring-2 focus:ring-teal-500">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Pelanggan</th>
                            <th class="py-3 px-4">Jenis Kelamin</th>
                            <th class="py-3 px-4">No. Telepon / WhatsApp</th>
                            <th class="py-3 px-4">Alamat</th>
                            <th class="py-3 px-4 text-center">Total Transaksi</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900 text-sm">{{ $c->name }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $c->gender === 'L' ? 'Laki-laki' : ($c->gender === 'P' ? 'Perempuan' : '-') }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-mono">{{ $c->phone ?: '-' }}</td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">{{ $c->address ?: '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 bg-teal-50 text-teal-700 rounded-full font-bold">
                                        {{ $c->sales()->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('customers.edit', $c) }}" class="text-teal-700 hover:underline font-semibold">Ubah</a>
                                    <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" onsubmit="return window.customConfirm(event, 'Hapus data pelanggan {{ addslashes($c->name) }}?', 'Hapus Pelanggan', 'Ya, Hapus')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data pelanggan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

