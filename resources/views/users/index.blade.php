<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola akun admin dan kasir Toko Al-Hidayah (penonaktifan akun tanpa hapus data transaksi)</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                <span>+ Tambah Pengguna Baru</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4 text-center">Peran (Level)</th>
                            <th class="py-3 px-4 text-center">Status Akun</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-bold text-slate-900 text-sm">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                        <span class="text-[10px] text-teal-700 font-semibold ml-1">(Anda)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600 font-medium">@ {{ $u->username }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($u->level === 1)
                                        <span class="px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full font-bold text-[11px]">
                                            Admin (Pemilik)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-teal-50 text-teal-700 rounded-full font-semibold text-[11px]">
                                            Kasir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($u->is_active)
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[11px]">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-red-100 text-red-800 rounded-full font-bold text-[11px]">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('users.edit', $u) }}" class="text-teal-700 hover:underline font-semibold">Ubah</a>

                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle-active', $u) }}" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif akun {{ $u->name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="font-semibold cursor-pointer {{ $u->is_active ? 'text-amber-600 hover:underline' : 'text-emerald-600 hover:underline' }}">
                                                {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

