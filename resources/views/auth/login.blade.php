<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Masuk ke Sistem</h2>
        <p class="text-sm text-slate-500 mt-1">Silakan masukkan username dan password Anda</p>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm text-slate-900"
                   placeholder="Contoh: admin atau kasir1">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm text-slate-900"
                   placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                <span class="text-xs text-slate-600">Ingat Saya</span>
            </label>
        </div>

        <button type="submit"
                class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-medium rounded-lg shadow-sm transition flex items-center justify-center space-x-2 cursor-pointer min-h-[44px]">
            <span>Masuk</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100">
        <p class="text-xs text-slate-500 font-medium mb-2">Akun Demo Pengujian:</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button"
                    onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='password';"
                    class="p-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-left transition cursor-pointer">
                <div class="font-semibold text-teal-800">Admin (Pemilik)</div>
                <div class="text-slate-500 text-[11px]">admin / password</div>
            </button>
            <button type="button"
                    onclick="document.getElementById('username').value='kasir1'; document.getElementById('password').value='password';"
                    class="p-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-left transition cursor-pointer">
                <div class="font-semibold text-slate-800">Kasir</div>
                <div class="text-slate-500 text-[11px]">kasir1 / password</div>
            </button>
        </div>
    </div>
</x-guest-layout>
