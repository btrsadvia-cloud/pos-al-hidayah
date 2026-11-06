<x-app-layout>
    <div class="space-y-6">
        <!-- Top Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Ringkasan</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Selamat datang kembali, <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span>!
                    ({{ auth()->user()->isAdmin() ? 'Administrator / Pemilik' : 'Kasir Toko' }})
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('pos.index') }}"
                   class="inline-flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-xs transition min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Mulai Transaksi Kasir</span>
                </a>
            </div>
        </div>

        <!-- 3 Stat Cards (All roles) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <!-- Omzet Hari Ini -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Omzet Hari Ini</span>
                    <span class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 block">{{ format_rupiah($todayRevenue) }}</span>
                </div>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Transaksi Hari Ini</span>
                    <span class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 block">{{ $todayTransactions }} Transaksi</span>
                </div>
            </div>

            <!-- Produk Stok Menipis -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl {{ $lowStockCount > 0 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Stok Menipis</span>
                    <div class="flex items-center space-x-2 mt-0.5">
                        <span class="text-xl sm:text-2xl font-bold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-slate-900' }}">
                            {{ $lowStockCount }} Produk
                        </span>
                        @if($lowStockCount > 0)
                            <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="text-xs text-amber-700 hover:underline font-medium">Lihat &rarr;</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Extra Analytics (Omzet 30 Hari, Top Products, Loyal Customers) -->
        @can('admin')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 30-Day Revenue Trend Chart (Cols 1-2) -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 text-base">Tren Omzet 30 Hari Terakhir</h3>
                        <span class="text-xs text-slate-400 font-medium">Satuan: Rupiah</span>
                    </div>
                    <div class="h-64 sm:h-72">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>

                <!-- Top 5 Products (Col 3) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col">
                    <h3 class="font-bold text-slate-900 text-base mb-3">5 Produk Terlaris (30 Hari)</h3>
                    @if(isset($topProducts) && $topProducts->isNotEmpty())
                        <div class="divide-y divide-slate-100 flex-1">
                            @foreach($topProducts as $idx => $tp)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $idx + 1 }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-800 truncate">{{ $tp->product_name }}</span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-bold text-teal-800">{{ $tp->total_qty }} pcs</span>
                                        <span class="text-[10px] text-slate-400 block">{{ format_rupiah($tp->total_revenue) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-4 text-slate-400">
                            <p class="text-xs">Belum ada data penjualan 30 hari terakhir</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Loyal Customers Row -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                <h3 class="font-bold text-slate-900 text-base mb-3">Pelanggan Paling Loyal</h3>
                @if(isset($loyalCustomers) && $loyalCustomers->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-4">Nama Pelanggan</th>
                                    <th class="py-2.5 px-4 text-center">Total Transaksi</th>
                                    <th class="py-2.5 px-4 text-right">Total Belanja</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($loyalCustomers as $lc)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $lc->name }}</td>
                                        <td class="py-2.5 px-4 text-center font-medium">{{ $lc->total_transactions }} kali</td>
                                        <td class="py-2.5 px-4 text-right font-bold text-teal-800">{{ format_rupiah($lc->total_spend) }}</td>
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

            <!-- Chart.js initialization script -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('revenueTrendChart')?.getContext('2d');
                    if (!ctx) return;

                    const labels = @json($trendLabels ?? []);
                    const data = @json($trendData ?? []);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Omzet Harian (Rp)',
                                data: data,
                                borderColor: '#0d9488', // teal-600
                                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'k';
                                            return 'Rp ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @endcan
    </div>
</x-app-layout>

