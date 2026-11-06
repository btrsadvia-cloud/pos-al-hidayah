<x-app-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Pengaturan Gateway BuatQris</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Integrasi pembayaran QRIS dinamis real-time melalui layanan <a href="https://app.buatqris.site" target="_blank" class="text-teal-600 hover:underline font-semibold">BuatQris.site</a>
                </p>
            </div>

            <!-- Status Badge -->
            <div>
                @if($currentConfig['is_configured'])
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <span class="w-2 h-2 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Gateway Terhubung & Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <span class="w-2 h-2 mr-1.5 bg-amber-500 rounded-full"></span>
                        Kredensial Belum Lengkap
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Settings Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <h2 class="text-base font-bold text-slate-900 mb-1">Kredensial Open API</h2>
                    <p class="text-xs text-slate-500 mb-6">
                        Kredensial ini diambil dari dasbor BuatQris di menu <strong>Open API &rarr; Kredensial API</strong>.
                    </p>

                    <form action="{{ route('settings.buatqris.update') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Account ID -->
                        <div>
                            <label for="account_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Account ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="account_id" name="account_id"
                                   value="{{ old('account_id', $currentConfig['account_id']) }}" required
                                   placeholder="Contoh: ID akun dari dasbor BuatQris"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">ID unik akun merchant BuatQris Anda.</p>
                        </div>

                        <!-- Secret Token -->
                        <div>
                            <label for="secret_token" class="block text-xs font-semibold text-slate-700 mb-1">
                                Secret Token <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="secret_token" name="secret_token"
                                   value="{{ old('secret_token', $currentConfig['secret_token']) }}" required
                                   placeholder="sk_live_xxxxxxxxxxxxxxxxxxxxxxxx"
                                   class="w-full px-3 py-2 text-sm font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Kunci rahasia API (diawali <code>sk_live_</code>). Disimpan aman di server.</p>
                        </div>

                        <!-- Signing Secret -->
                        <div>
                            <label for="signing_secret" class="block text-xs font-semibold text-slate-700 mb-1">
                                Webhook Signing Secret (Opsional namun disarankan)
                            </label>
                            <input type="password" id="signing_secret" name="signing_secret"
                                   value="{{ old('signing_secret', $currentConfig['signing_secret']) }}"
                                   placeholder="Signing secret dari kartu Webhook di dasbor"
                                   class="w-full px-3 py-2 text-sm font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">
                                Digunakan untuk memverifikasi keaslian signature <code>X-BuatQris-Signature</code> pada webhook callback.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <!-- Fee By -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Tanggungan Biaya Admin (Fee)
                                </label>
                                <div class="flex items-center space-x-2" x-data="{ fee: '{{ old('fee_by', $currentConfig['fee_by']) }}' }">
                                    <input type="hidden" name="fee_by" :value="fee">
                                    <button type="button" @click="fee = 'user'" :class="fee === 'user' ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-2 py-2 rounded-lg border text-[11px] font-semibold transition cursor-pointer text-center">Saldo Toko</button>
                                    <button type="button" @click="fee = 'buyer'" :class="fee === 'buyer' ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'" class="flex-1 px-2 py-2 rounded-lg border text-[11px] font-semibold transition cursor-pointer text-center">Pembeli</button>
                                </div>
                            </div>

                            <!-- QRIS Method -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Metode QRIS (Opsional)
                                </label>
                                <div x-data="{
                                    open: false,
                                    selected: '{{ old('method', $currentConfig['method']) }}',
                                    items: [
                                        { val: '', label: 'Default Admin BuatQris' },
                                        { val: 'qris_one', label: 'QRIS One' },
                                        { val: 'qris_two', label: 'QRIS Two' },
                                        { val: 'qris_three', label: 'QRIS Three' },
                                        { val: 'qris_four', label: 'QRIS Four' }
                                    ],
                                    get label() { return (this.items.find(i => i.val === this.selected) || this.items[0]).label; }
                                }" class="relative">
                                    <input type="hidden" name="method" :value="selected">
                                    <button type="button" @click="open = !open" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 bg-white text-left flex items-center justify-between cursor-pointer hover:border-teal-500 transition">
                                        <span class="text-xs font-semibold text-slate-800" x-text="label"></span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute z-30 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 text-xs overflow-hidden">
                                        <template x-for="it in items" :key="it.val">
                                            <button type="button" @click="selected = it.val; open = false" class="w-full text-left px-3 py-2.5 hover:bg-teal-50 transition cursor-pointer flex justify-between items-center" :class="selected === it.val && 'bg-teal-50 text-teal-800'">
                                                <span class="font-semibold" x-text="it.label"></span>
                                                <span class="text-teal-600 font-bold" x-show="selected === it.val">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Mode Checkbox -->
                        <div class="pt-2">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" name="test_mode" value="1"
                                       {{ old('test_mode', $currentConfig['test_mode']) ? 'checked' : '' }}
                                       class="mt-1 w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                                <div class="text-xs">
                                    <span class="font-semibold text-slate-800">Mode Sandbox / Uji Coba (test=1)</span>
                                    <p class="text-slate-400">
                                        Jika dicentang, transaksi QRIS dibuat tanpa uang riil (sandbox) dan dana masuk ke saldo uji coba. Hapus centang untuk menerima pembayaran asli.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold text-sm rounded-xl shadow-sm transition cursor-pointer">
                                Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Col: Webhook & Info Guide -->
            <div class="space-y-6">
                <!-- Webhook Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center space-x-2 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h3 class="font-bold text-sm text-slate-900">Webhook / Callback URL</h3>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Salin URL berikut dan masukkan ke dasbor BuatQris pada menu <strong>Open API &rarr; Webhook / Callback</strong> agar sistem otomatis menerima notifikasi pembayaran lunas.
                    </p>

                    <div x-data="{ copied: false }" class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center justify-between gap-2">
                        <input type="text" readonly value="{{ $webhookUrl }}" id="webhook-url-input"
                               class="bg-transparent text-xs font-mono text-slate-800 outline-none w-full select-all">
                        <button type="button"
                                @click="navigator.clipboard.writeText(document.getElementById('webhook-url-input').value); copied = true; setTimeout(() => copied = false, 2500);"
                                class="shrink-0 px-2.5 py-1 bg-white border border-slate-200 hover:bg-slate-100 text-xs font-semibold rounded-lg text-slate-700 shadow-2xs transition cursor-pointer">
                            <span x-show="!copied">Salin URL</span>
                            <span x-show="copied" x-cloak class="text-teal-700 font-bold">✓ Tersalin!</span>
                        </button>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-800 space-y-1">
                        <p class="font-bold">Catatan Localhost:</p>
                        <p>
                            Jika aplikasi berjalan di localhost tanpa domain publik, BuatQris tidak dapat mengirim webhook langsung ke komputer Anda. Namun jangan khawatir! POS Toko Al-Hidayah dilengkapi fitur <strong>Polling Otomatis &amp; Cek Status Real-time</strong> langsung ke gateway BuatQris.
                        </p>
                    </div>
                </div>

                <!-- API Rules & Limits -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3 text-xs">
                    <h3 class="font-bold text-sm text-slate-900">Ketentuan &amp; Batas API</h3>
                    <ul class="space-y-2 text-slate-600 list-disc list-inside">
                        <li><strong>Minimal Transaksi:</strong> Rp 1.000</li>
                        <li><strong>Maksimal Pembuatan:</strong> 60 transaksi per menit</li>
                        <li><strong>Cek Status:</strong> Maksimal 1 kali per 20 detik (sistem kami otomatis mengatur jeda ini agar aman dari 429)</li>
                        <li><strong>Kode Unik:</strong> Nominal transaksi otomatis menyertakan kode unik untuk verifikasi otomatis</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Table: Recent QRIS Transactions -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Riwayat Transaksi QRIS Gateway</h3>
                    <p class="text-xs text-slate-500">Daftar transaksi yang diproses via gateway BuatQris</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">ID Transaksi</th>
                            <th class="px-4 py-3">No. Faktur</th>
                            <th class="px-4 py-3">Nominal + Unik</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Waktu Buat</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-mono font-semibold text-slate-900">
                                    {{ $trx->transaction_id }}
                                    @if($trx->is_test)
                                        <span class="ml-1 text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold">TEST</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-teal-700">
                                    @if($trx->sale_id)
                                        <a href="{{ route('sales.show', $trx->sale_id) }}" class="hover:underline font-bold">
                                            {{ $trx->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ format_rupiah($trx->total_amount) }}</div>
                                    @if($trx->amount_uniq > 0)
                                        <div class="text-[10px] text-slate-400">Asli: {{ format_rupiah($trx->amount) }} (+{{ $trx->amount_uniq }})</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono">
                                    {{ $trx->qris_method ?: '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($trx->status === 'success')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                                    @elseif($trx->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">PENDING</span>
                                    @elseif($trx->status === 'expired')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">EXPIRED</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">GAGAL</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $trx->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <a href="{{ route('settings.buatqris.check-status', $trx->transaction_id) }}"
                                       class="px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold transition cursor-pointer">
                                        Cek Status
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada transaksi QRIS yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

