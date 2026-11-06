<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('customers.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Data Pelanggan</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Data Pelanggan</h1>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-semibold text-slate-800 mb-1">Jenis Kelamin</label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                        <option value="">-- Pilih (Opsional) --</option>
                        <option value="P" {{ old('gender', $customer->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        <option value="L" {{ old('gender', $customer->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-800 mb-1">No. HP / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 font-mono text-sm text-slate-900 outline-none">
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-800 mb-1">Alamat</label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                        Perbarui Pelanggan
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
