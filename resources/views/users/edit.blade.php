<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('users.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Kelola Pengguna</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Data Pengguna</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $user->name }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                </div>

                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-800 mb-1">Username Login <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-800 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password"
                           placeholder="Minimal 6 karakter jika ingin ganti"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-1">Peran / Hak Akses <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-2" x-data="{ level: '{{ old('level', $user->level) }}' }">
                        <input type="hidden" name="level" :value="level">
                        <button type="button" @click="level = '2'" :class="level == '2' ? 'bg-teal-600 text-white border-teal-600 shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-semibold transition cursor-pointer text-center">
                            <div class="font-bold">Kasir</div>
                            <div class="text-[10px] mt-0.5 opacity-80">POS, Shift, Transaksi, Inventori</div>
                        </button>
                        <button type="button" @click="level = '1'" :class="level == '1' ? 'bg-teal-600 text-white border-teal-600 shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-semibold transition cursor-pointer text-center">
                            <div class="font-bold">Admin / Pemilik</div>
                            <div class="text-[10px] mt-0.5 opacity-80">Akses Penuh Semua Modul</div>
                        </button>
                    </div>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                        Perbarui Pengguna
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

