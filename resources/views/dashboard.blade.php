<x-app-layout>
    <div class="space-y-4">

        <!-- Top Greeting Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-[#70102e] via-[#881337] to-[#5c0d26] rounded-2xl p-4 sm:p-5 text-white shadow-lg animate-fade-up">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-20 -top-10 w-48 h-48 bg-rose-400/10 rounded-full blur-xl pointer-events-none animate-pulse-glow"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Assalamu'alaikum, {{ preg_replace('/\s*\(.*?\)/', '', auth()->user()->name) }}
                    </h1>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('pos.index') }}"
                       class="inline-flex items-center space-x-2 bg-white text-[#881337] hover:bg-rose-50 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-md hover:shadow-lg active:scale-95 transition-all duration-150 cursor-pointer min-h-[40px] animate-shimmer-btn">
                        <svg class="w-4 h-4 text-[#881337]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Buka Kasir (POS)</span>
                    </a>

                    @can('admin')
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center space-x-1 bg-white/15 hover:bg-white/25 text-white border border-white/20 px-3 py-2 rounded-xl text-xs font-semibold backdrop-blur-xs transition cursor-pointer min-h-[40px]">
                            <span>+ Produk Baru</span>
                        </a>
                        <a href="{{ route('inventory.stock-in') }}"
                           class="inline-flex items-center space-x-1 bg-white/15 hover:bg-white/25 text-white border border-white/20 px-3 py-2 rounded-xl text-xs font-semibold backdrop-blur-xs transition cursor-pointer min-h-[40px]">
                            <span>+ Stok Masuk</span>
                        </a>
                    @endcan

                    <a href="{{ route('shift.index') }}"
                       class="inline-flex items-center space-x-1 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-2 rounded-xl text-xs font-semibold backdrop-blur-xs transition cursor-pointer min-h-[40px]">
                        <span>Shift Kasir</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Stat Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            <!-- Omzet Hari Ini -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover-lift transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Hari Ini</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#881337] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black text-slate-900 tracking-tight block">{{ format_rupiah($todayRevenue) }}</span>
                    <div class="flex items-center space-x-1.5 mt-1.5 text-xs">
                        @if($revenueDiffPct >= 0)
                            <span class="text-emerald-700 font-semibold flex items-center">
                                &uarr; {{ $revenueDiffPct }}%
                            </span>
                        @else
                            <span class="text-red-600 font-semibold flex items-center">
                                &darr; {{ abs($revenueDiffPct) }}%
                            </span>
                        @endif
                        <span class="text-slate-400">vs kemarin ({{ format_rupiah($yesterdayRevenue) }})</span>
                    </div>
                </div>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-blue-300 transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi Sukses</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black text-slate-900 tracking-tight block">{{ $todayTransactions }} <span class="text-sm font-semibold text-slate-500">Struk</span></span>
                    <p class="text-xs text-slate-400 mt-1.5">Penjualan lunas tercatat hari ini</p>
                </div>
            </div>

            <!-- Total Produk Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-purple-300 transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Katalog Produk</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black text-slate-900 tracking-tight block">{{ $totalProductsCount }} <span class="text-sm font-semibold text-slate-500">Barang</span></span>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center text-xs text-purple-700 hover:underline font-semibold mt-1.5">
                        <span>Kelola katalog &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Stok Menipis Warning -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-amber-300 transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Perlu Restok</span>
                    <div class="w-10 h-10 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-slate-900' }} tracking-tight block">
                        {{ $lowStockCount }} <span class="text-sm font-semibold text-slate-500">Item</span>
                    </span>
                    @if($lowStockCount > 0)
                        <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="inline-flex items-center text-xs text-amber-700 hover:underline font-bold mt-1.5">
                            <span>Periksa barang menipis &rarr;</span>
                        </a>
                    @else
                        <span class="text-xs text-emerald-600 font-medium block mt-1.5">✓ Seluruh stok aman</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2-Panel Row: Status Shift & Ringkasan Metode Pembayaran Hari Ini -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- Panel 1: Status Shift Kasir Berjalan (Cols 1-5) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $activeShift ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                            <h3 class="font-bold text-sm text-slate-900">Status Shift Kasir</h3>
                        </div>
                        <a href="{{ route('shift.index') }}" class="text-xs text-[#881337] hover:underline font-semibold">
                            Detail Shift &rarr;
                        </a>
                    </div>

                    @if($activeShift)
                        <div class="space-y-2.5 text-xs">
                            <div class="p-3 bg-rose-50/60 rounded-xl border border-teal-100 flex items-center justify-between">
                                <div>
                                    <span class="text-slate-500 block">Kasir Bertugas:</span>
                                    <strong class="text-sm text-slate-900">{{ $activeShift->user->name ?? 'Kasir' }}</strong>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px]">Shift Terbuka</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-slate-500 block text-[11px]">Mulai Shift:</span>
                                    <strong class="text-slate-800 text-xs">{{ $activeShift->opened_at ? $activeShift->opened_at->format('H:i, d M') : '-' }}</strong>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-slate-500 block text-[11px]">Modal Awal:</span>
                                    <strong class="text-slate-800 text-xs">{{ format_rupiah($activeShift->opening_cash) }}</strong>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-6 text-center text-slate-400 space-y-2">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs font-medium text-slate-600">Belum ada shift kasir yang dibuka hari ini</p>
                            <a href="{{ route('shift.index') }}" class="inline-block px-3 py-1.5 bg-[#881337] hover:bg-[#70102e] text-white rounded-lg text-xs font-semibold transition">
                                Buka Shift Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Total Member Terdaftar:</span>
                    <strong class="text-slate-800">{{ $totalCustomersCount }} Pelanggan</strong>
                </div>
            </div>

            <!-- Panel 2: Breakdown Metode Pembayaran Hari Ini (Cols 6-12) -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                        <h3 class="font-bold text-sm text-slate-900">Metode Pembayaran Hari Ini</h3>
                        <span class="text-xs text-slate-400 font-medium">Tunai, QRIS BuatQris, & Transfer</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Tunai -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col justify-between">
                            <div class="flex items-center space-x-1.5 text-xs font-semibold text-slate-700">
                                <span>💵 Tunai</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-base font-black text-slate-900 block">{{ format_rupiah($cashTotal) }}</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">{{ $cashCount }} kali transaksi</span>
                            </div>
                        </div>

                        <!-- QRIS BuatQris -->
                        <div class="p-3.5 rounded-xl bg-rose-50/70 border border-rose-200 flex flex-col justify-between">
                            <div class="flex items-center space-x-1.5 text-xs font-semibold text-[#70102e]">
                                <span>📱 QRIS (BuatQris)</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-base font-black text-[#4a0b1f] block">{{ format_rupiah($qrisTotal) }}</span>
                                <span class="text-[11px] text-[#881337] block mt-0.5">{{ $qrisCount }} kali transaksi</span>
                            </div>
                        </div>

                        <!-- Transfer Bank -->
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 flex flex-col justify-between">
                            <div class="flex items-center space-x-1.5 text-xs font-semibold text-blue-800">
                                <span>🏦 Transfer Bank</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-base font-black text-blue-900 block">{{ format_rupiah($transferTotal) }}</span>
                                <span class="text-[11px] text-blue-700 block mt-0.5">{{ $transferCount }} kali transaksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between text-xs text-slate-500">
                    <span>Semua pembayaran tercatat secara real-time</span>
                    <a href="{{ route('sales.index') }}" class="text-[#881337] hover:underline font-semibold">
                        Lihat Rekap Penjualan &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#881337] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Transaksi Penjualan Terkini</h3>
                        <p class="text-[11px] text-slate-500">Daftar transaksi kasir terbaru hari ini</p>
                    </div>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs text-[#881337] hover:underline font-semibold">
                    Semua Transaksi &rarr;
                </a>
            </div>

            @if(isset($recentSales) && $recentSales->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">No. Faktur</th>
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Pelanggan</th>
                                <th class="py-2.5 px-3">Metode Bayar</th>
                                <th class="py-2.5 px-3 text-right">Total Belanja</th>
                                <th class="py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentSales as $sale)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-2.5 px-3 font-mono font-semibold text-slate-900">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-[#881337] hover:underline">
                                            {{ $sale->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-500">
                                        {{ $sale->sold_at ? $sale->sold_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-medium text-slate-800">
                                        {{ $sale->customer->name ?? 'Pelanggan Umum' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($sale->payment_method === 'qris')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-[#70102e]">QRIS</span>
                                        @elseif($sale->payment_method === 'transfer')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Transfer</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">Tunai</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-black text-slate-900">
                                        {{ format_rupiah($sale->total) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                                               class="p-1 text-[#881337] hover:bg-rose-50 rounded font-medium text-xs transition" title="Cetak Struk">
                                                🖨️ Struk
                                            </a>
                                            <a href="{{ route('sales.show', $sale) }}"
                                               class="p-1 text-slate-500 hover:text-slate-800 text-xs" title="Rincian">
                                                Lihat &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-xs">
                    Belum ada transaksi penjualan selesai hari ini.
                </div>
            @endif
        </div>

        <!-- Admin Analytics (5 Produk Terlaris & Pelanggan Paling Loyal - Compact, No Chart) -->
        @can('admin')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Top 5 Products -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">5 Produk Terlaris</h3>
                                <p class="text-[11px] text-slate-400">Berdasarkan kuantitas terjual (30 hari terakhir)</p>
                            </div>
                            <a href="{{ route('products.index') }}" class="text-xs text-[#881337] hover:underline font-semibold">
                                Katalog &rarr;
                            </a>
                        </div>

                        @if(isset($topProducts) && $topProducts->isNotEmpty())
                            <div class="divide-y divide-slate-100">
                                @foreach($topProducts as $idx => $tp)
                                    <div class="py-2 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 min-w-0">
                                            <span class="w-5 h-5 rounded-full bg-rose-50 text-[#881337] font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ $idx + 1 }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-800 truncate">{{ $tp->product_name }}</span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-bold text-[#881337]">{{ $tp->total_qty }} pcs</span>
                                            <span class="text-[10px] text-slate-400 block">{{ format_rupiah($tp->total_revenue) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-6 text-center text-slate-400 text-xs">
                                Belum ada data penjualan 30 hari terakhir
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Loyal Customers -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Pelanggan Paling Loyal</h3>
                                <p class="text-[11px] text-slate-400">Member dengan nilai belanja tertinggi</p>
                            </div>
                            <a href="{{ route('customers.index') }}" class="text-xs text-[#881337] hover:underline font-semibold">
                                Pelanggan &rarr;
                            </a>
                        </div>

                        @if(isset($loyalCustomers) && $loyalCustomers->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                                        <tr>
                                            <th class="py-2 px-3">Nama Pelanggan</th>
                                            <th class="py-2 px-3 text-center">Transaksi</th>
                                            <th class="py-2 px-3 text-right">Total Belanja</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($loyalCustomers as $lc)
                                            <tr class="hover:bg-slate-50">
                                                <td class="py-2 px-3 font-semibold text-slate-900">{{ $lc->name }}</td>
                                                <td class="py-2 px-3 text-center font-medium">{{ $lc->total_transactions }} kali</td>
                                                <td class="py-2 px-3 text-right font-bold text-[#881337]">{{ format_rupiah($lc->total_spend) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs">
                                Belum ada transaksi member tercatat
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-app-layout>
