<x-guest-layout>
    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden grid grid-cols-1 md:grid-cols-12 text-slate-800">

        <!-- Left Branding Showcase Panel (5 cols) -->
        <div class="md:col-span-5 bg-gradient-to-br from-teal-700 via-teal-800 to-emerald-900 text-white p-8 flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative arch watermark -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 border-8 border-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute top-1/2 -left-12 w-48 h-48 bg-teal-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-teal-100 text-[11px] font-semibold mb-6 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem POS & Inventori Siap</span>
                </div>

                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-inner mb-4 border border-white/20">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-black tracking-tight text-white">Toko Al-Hidayah</h1>
                <p class="text-xs text-teal-100/90 mt-1 font-medium leading-relaxed">
                    Busana Muslimah, Seragam Sekolah &amp; Perlengkapan Ibadah
                </p>
            </div>

            <div class="relative z-10 my-8 space-y-3">
                <div class="flex items-center space-x-3 text-xs text-teal-100/90">
                    <div class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-teal-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span>Kasir Cepat &amp; Barcode Scanner</span>
                </div>
                <div class="flex items-center space-x-3 text-xs text-teal-100/90">
                    <div class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-teal-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span>3 Tingkat Harga (Ecer, Grosir, Super)</span>
                </div>
                <div class="flex items-center space-x-3 text-xs text-teal-100/90">
                    <div class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-teal-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span>QRIS Dinamis &amp; Cetak Struk Thermal</span>
                </div>
            </div>

            <div class="relative z-10 pt-4 border-t border-white/15 text-[11px] text-teal-200/80 flex items-center justify-between">
                <span>Versi 1.3.0</span>
                <span>Barokah &bull; Amanah</span>
            </div>
        </div>

        <!-- Right Form Panel (7 cols) -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-between"
             x-data="{
                showPassword: false,
                fillAccount(u, p) {
                    document.getElementById('username').value = u;
                    document.getElementById('password').value = p;
                }
             }">

            <div>
                <!-- Heading -->
                <div class="mb-6">
                    <span class="text-xs font-semibold text-teal-700 uppercase tracking-wider block">Assalamu'alaikum</span>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">Masuk ke Sistem</h2>
                    <p class="text-xs text-slate-500 mt-1">Silakan masukkan username dan kata sandi akun Anda</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3.5 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm text-slate-900 bg-slate-50/50 focus:bg-white transition"
                                   placeholder="Contoh: admin atau kasir1">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm text-slate-900 bg-slate-50/50 focus:bg-white transition"
                                   placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                <template x-if="!showPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                            <span class="text-xs text-slate-600 font-medium">Ingat Saya di Perangkat Ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 active:from-teal-800 active:to-teal-900 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-2 cursor-pointer">
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Quick Demo Login Helper Buttons -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2.5">
                    Akses Cepat Pengujian:
                </span>
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" @click="fillAccount('admin', 'password')"
                            class="p-3 bg-slate-50 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 rounded-xl text-left transition cursor-pointer group">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900 group-hover:text-teal-800">Admin (Pemilik)</span>
                            <span class="text-[10px] bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded font-semibold">Level 1</span>
                        </div>
                        <div class="text-slate-400 text-[11px] font-mono mt-0.5">admin / password</div>
                    </button>

                    <button type="button" @click="fillAccount('kasir1', 'password')"
                            class="p-3 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl text-left transition cursor-pointer group">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900 group-hover:text-blue-800">Kasir Toko</span>
                            <span class="text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded font-semibold">Level 2</span>
                        </div>
                        <div class="text-slate-400 text-[11px] font-mono mt-0.5">kasir1 / password</div>
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-guest-layout>
