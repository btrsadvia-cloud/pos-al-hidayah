<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('users.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Kelola Pengguna</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Contoh: Siti Aisyah"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                </div>

                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-800 mb-1">Username Login <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                           placeholder="Contoh: kasir2"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-800 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="level" class="block text-sm font-semibold text-slate-800 mb-1">Peran / Hak Akses <span class="text-red-500">*</span></label>
                    <select id="level" name="level" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                        <option value="2" {{ old('level', 2) == 2 ? 'selected' : '' }}>Kasir (Layar POS, Shift, Transaksi Sendiri, Inventori)</option>
                        <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Admin / Pemilik (Akses Penuh Semua Modul & Laporan)</option>
                    </select>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                        Simpan Pengguna
                    </button>
                    <a href="{{ route('users.index') }}"
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
