<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <a href="{{ route('suppliers.index') }}" class="text-xs text-teal-700 hover:underline flex items-center space-x-1 mb-2 font-medium">
                <span>&larr; Kembali ke Daftar Pemasok</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Data Pemasok</h1>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Pemasok <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                </div>

                <div>
                    <label for="contact" class="block text-sm font-semibold text-slate-800 mb-1">Kontak / Telepon <span class="text-red-500">*</span></label>
                    <input type="text" id="contact" name="contact" value="{{ old('contact', $supplier->contact) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none font-mono text-sm text-slate-900">
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-800 mb-1">Alamat <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" required rows="3"
                              class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <div>
                    <label for="bank_account" class="block text-sm font-semibold text-slate-800 mb-1">Nomor Rekening Bank</label>
                    <input type="text" id="bank_account" name="bank_account" value="{{ old('bank_account', $supplier->bank_account) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 outline-none text-sm text-slate-900">
                </div>

                <div class="flex items-center space-x-3 pt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold rounded-xl text-sm shadow-xs transition cursor-pointer min-h-[44px]">
                        Perbarui Pemasok
                    </button>
                    <a href="{{ route('suppliers.index') }}"
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition min-h-[44px] flex items-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
