<x-guest-layout>
    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden grid grid-cols-1 lg:grid-cols-12 text-slate-800 animate-soft-pop">

        <!-- Left Branding Showcase Panel (5 cols) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-[#70102e] via-[#5c0d26] to-[#2c0411] text-white p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative arch watermark & ambient glow -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 border-8 border-rose-300/10 rounded-full pointer-events-none"></div>
            <div class="absolute top-1/3 -left-12 w-56 h-56 bg-rose-500/20 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
            <div class="absolute top-10 right-10 w-28 h-28 bg-amber-400/15 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Top Header & Store Identity -->
            <div class="relative z-10">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-rose-100 text-[11px] font-semibold mb-4 border border-white/15 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Sistem POS &amp; Inventori Toko</span>
                </div>

                <div class="flex items-center space-x-3 mb-3">
                    <!-- Boutique Store Illustration Badge (Replacing previous text-based AH) -->
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-[#4a0b1f] flex items-center justify-center shadow-lg border border-amber-200/60 shrink-0 transform hover:scale-105 transition-transform duration-200">
                        <svg class="w-7 h-7 text-[#4a0b1f]" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px; max-width: 28px; max-height: 28px;">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                            <circle cx="12" cy="7" r="1.5" fill="#4a0b1f"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white leading-tight">Toko Al-Hidayah</h1>
                        <p class="text-xs text-rose-200 font-medium">Busana Muslimah, Seragam &amp; Ibadah</p>
                    </div>
                </div>
            </div>

            <!-- Center: Store Illustration with Float Animation -->
            <div class="relative z-10 my-4 flex items-center justify-center">
                <div class="w-full max-w-[340px] animate-float drop-shadow-2xl">
                    <img src="{{ asset('images/illustration-store.svg') }}"
                         alt="Ilustrasi Toko Al-Hidayah"
                         class="w-full h-auto max-h-[220px] object-contain select-none pointer-events-none filter drop-shadow-md">
                </div>
            </div>

            <!-- Bottom Tagline (Clean Version Without 'Edisi Maroon') -->
            <div class="relative z-10 pt-4 border-t border-rose-400/20 text-[11px] text-rose-200/90 flex items-center justify-between">
                <span class="font-medium">Versi 1.3.0</span>
                <span class="font-semibold text-amber-200 flex items-center space-x-1">
                    <span>✦</span>
                    <span>Barokah &amp; Amanah</span>
                </span>
            </div>
        </div>

        <!-- Right Form Panel (7 cols) -->
        <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center">

            <div class="max-w-md w-full mx-auto">
                <!-- Heading -->
                <div class="mb-8">
                    <span class="text-xs font-bold text-[#881337] uppercase tracking-wider block">Assalamu'alaikum</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Masuk ke Sistem</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">Silakan masukkan username dan kata sandi akun Anda untuk mengelola kasir dan toko.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center space-x-2 animate-fade-up">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl animate-fade-up">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Username Akun
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] focus:border-[#881337] outline-none text-sm text-slate-900 bg-slate-50/60 focus:bg-white transition"
                                   placeholder="Contoh: admin atau kasir1">
                        </div>
                    </div>

                    <!-- Password Field with View/Hide Toggle -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#881337] focus:border-[#881337] outline-none text-sm text-slate-900 bg-slate-50/60 focus:bg-white transition"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility()" id="btn-toggle-pw"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-[#881337] transition cursor-pointer"
                                    title="Lihat / Sembunyikan Kata Sandi">
                                <!-- Eye Icon (Show password) -->
                                <svg id="eye-icon-show" class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slash Icon (Hide password) -->
                                <svg id="eye-icon-hide" class="w-5 h-5 hidden" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#881337] focus:ring-[#881337] border-slate-300">
                            <span class="text-xs text-slate-600 font-medium">Ingat Saya di Perangkat Ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-[#881337] via-[#9f1239] to-[#881337] hover:from-[#70102e] hover:to-[#70102e] active:scale-[0.98] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2 cursor-pointer animate-shimmer-btn">
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-4 h-4" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <!-- Toggle Password Visibility Pure JavaScript (Guaranteed execution without framework dependencies) -->
    <script>
        function togglePasswordVisibility() {
            var input = document.getElementById('password');
            var eyeShow = document.getElementById('eye-icon-show');
            var eyeHide = document.getElementById('eye-icon-hide');
            if (input.type === 'password') {
                input.type = 'text';
                eyeShow.classList.add('hidden');
                eyeHide.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeShow.classList.remove('hidden');
                eyeHide.classList.add('hidden');
            }
        }

        // Login success slideshow overlay
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (form.checkValidity && !form.checkValidity()) {
                        return; // Field required error, do not show overlay
                    }
                    var overlay = document.getElementById('login-success-overlay');
                    if (overlay) {
                        overlay.style.display = 'flex';
                        setTimeout(function () {
                            overlay.classList.add('overlay-visible');
                        }, 10);
                    }
                });
            }
        });
    </script>

    <!-- Login Success Slideshow Overlay -->
    <div id="login-success-overlay"
         style="display:none; position:fixed; inset:0; z-index:9999; background: linear-gradient(135deg, #2c0411 0%, #70102e 50%, #881337 100%); flex-direction:column; align-items:center; justify-content:center; gap:20px; opacity:0; transition: opacity 0.4s ease;"
         class="">
        <!-- Animated rings -->
        <div style="position:absolute; inset:0; overflow:hidden; pointer-events:none;">
            <div style="position:absolute; top:50%; left:50%; width:600px; height:600px; border:2px solid rgba(255,255,255,0.08); border-radius:50%; transform:translate(-50%,-50%); animation: ringPulse 2s ease-out infinite;"></div>
            <div style="position:absolute; top:50%; left:50%; width:450px; height:450px; border:2px solid rgba(251,191,36,0.15); border-radius:50%; transform:translate(-50%,-50%); animation: ringPulse 2s ease-out 0.3s infinite;"></div>
            <div style="position:absolute; top:50%; left:50%; width:300px; height:300px; border:2px solid rgba(255,255,255,0.1); border-radius:50%; transform:translate(-50%,-50%); animation: ringPulse 2s ease-out 0.6s infinite;"></div>
        </div>

        <!-- Store icon badge -->
        <div style="width:80px; height:80px; border-radius:24px; background:linear-gradient(135deg,#fbbf24,#f59e0b); display:flex; align-items:center; justify-content:center; box-shadow:0 0 40px rgba(251,191,36,0.4); animation: iconBounce 0.6s ease 0.2s both;">
            <svg style="width:44px;height:44px;color:#4a0b1f;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>

        <!-- Welcome text (No waving emoji, clean and professional) -->
        <div style="text-align:center; color:white; animation: textSlideUp 0.5s ease 0.4s both;">
            <div style="font-size:13px; font-weight:600; color:rgba(251,191,36,0.9); letter-spacing:3px; text-transform:uppercase; margin-bottom:8px;">Toko Al-Hidayah</div>
            <div style="font-size:28px; font-weight:900; letter-spacing:-0.5px; margin-bottom:4px;">Selamat Datang</div>
            <div style="font-size:14px; color:rgba(255,255,255,0.7);">Memuat sistem...</div>
        </div>

        <!-- Bouncing dots loader -->
        <div style="display:flex; gap:8px; animation: textSlideUp 0.5s ease 0.6s both;">
            <div style="width:10px;height:10px;background:white;border-radius:50%;animation:dotBounce 1s ease 0s infinite;"></div>
            <div style="width:10px;height:10px;background:rgba(251,191,36,0.9);border-radius:50%;animation:dotBounce 1s ease 0.15s infinite;"></div>
            <div style="width:10px;height:10px;background:white;border-radius:50%;animation:dotBounce 1s ease 0.3s infinite;"></div>
        </div>
    </div>

    <style>
        .overlay-visible { opacity: 1 !important; }
        @keyframes ringPulse {
            0%   { opacity: 1; transform: translate(-50%,-50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%,-50%) scale(1.3); }
        }
        @keyframes iconBounce {
            0%   { opacity: 0; transform: scale(0.4) rotate(-15deg); }
            70%  { transform: scale(1.1) rotate(5deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        @keyframes textSlideUp {
            0%   { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes dotBounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
    </style>
</x-guest-layout>
