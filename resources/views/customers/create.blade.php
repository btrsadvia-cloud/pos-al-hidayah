<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('customers.index') }}" class="text-xs text-[#881337] hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Data Pelanggan</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Pelanggan Baru</h1>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Contoh: Ibu Hj. Fatimah"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] outline-none text-sm text-slate-900">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-1">Jenis Kelamin</label>
                    <div class="flex items-center space-x-2" x-data="{ gender: '{{ old('gender', '') }}' }">
                        <input type="hidden" name="gender" :value="gender">
                        <button type="button" @click="gender = ''" :class="gender === '' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-semibold transition cursor-pointer">Tidak Dipilih</button>
                        <button type="button" @click="gender = 'P'" :class="gender === 'P' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-semibold transition cursor-pointer">Perempuan</button>
                        <button type="button" @click="gender = 'L'" :class="gender === 'L' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-semibold transition cursor-pointer">Laki-laki</button>
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-800 mb-1">No. HP / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] font-mono text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-800 mb-1">Alamat</label>
                    <textarea id="address" name="address" rows="2"
                              placeholder="Alamat pelanggan"
                              class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] outline-none text-sm text-slate-900">{{ old('address') }}</textarea>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#881337] hover:bg-[#70102e] active:bg-[#5c0d26] text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                        Simpan Pelanggan
                    </button>
                    <a href="{{ route('customers.index') }}"
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

