<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS Toko Al-Hidayah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Chart.js for dashboard trends -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col antialiased" x-data="{ sidebarOpen: false, userDropdown: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <!-- Left: Toggle & Brand -->
            <div class="flex items-center space-x-3">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        AH
                    </div>
                    <div class="hidden sm:block">
                        <span class="font-bold text-slate-900 text-base leading-tight block">Toko Al-Hidayah</span>
                        <span class="text-[11px] text-teal-700 font-medium block">Busana Muslimah & Perlengkapan Ibadah</span>
                    </div>
                </a>
            </div>

            <!-- Right: Shift Status & User Menu -->
            <div class="flex items-center space-x-3">
                @php
                    $user = auth()->user();
                    $currentShift = $user ? $user->currentShift() : null;
                @endphp

                <!-- Shift Status Badge -->
                <a href="{{ route('shift.index') }}"
                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-xs font-medium {{ $currentShift ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' }} transition">
                    <span class="w-2 h-2 rounded-full {{ $currentShift ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                    <span>{{ $currentShift ? 'Shift Aktif' : 'Shift Belum Buka' }}</span>
                </a>

                <!-- POS Shortcut Button -->
                <a href="{{ route('pos.index') }}"
                   class="hidden sm:inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium shadow-xs transition min-h-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Kasir POS</span>
                </a>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-slate-100 transition min-h-[44px]">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="hidden md:block text-left text-xs">
                            <div class="font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-slate-500 text-[10px]">{{ auth()->user()->isAdmin() ? 'Pemilik (Admin)' : 'Kasir' }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50 text-xs">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500">@ {{ auth()->user()->username }}</p>
                        </div>
                        <a href="{{ route('shift.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Kelola Shift</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 flex items-center space-x-1.5 cursor-pointer">
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden md:flex flex-col w-64 bg-white border-r border-slate-200 py-4 px-3 shrink-0">
            <nav class="space-y-1 text-sm flex-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('pos.index') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('pos.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Layar Kasir (POS)</span>
                </a>

                <a href="{{ route('sales.index') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('sales.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Riwayat Penjualan</span>
                </a>

                <a href="{{ route('shift.index') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('shift.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Manajemen Shift</span>
                </a>

                <!-- Inventory Group (Accessible by all active users) -->
                <div class="pt-3 pb-1">
                    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Inventori</p>
                </div>

                <a href="{{ route('inventory.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('inventory.index') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Mutasi Stok</span>
                </a>

                <a href="{{ route('inventory.stock-in') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('inventory.stock-in') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Stok Masuk (Beli)</span>
                </a>

                <a href="{{ route('inventory.stock-out') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('inventory.stock-out') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Stok Keluar (Rusak/Lain)</span>
                </a>

                <a href="{{ route('customers.index') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('customers.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Data Pelanggan</span>
                </a>

                <!-- Admin Only Group -->
                @can('admin')
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Master Data (Admin)</p>
                    </div>

                    <a href="{{ route('products.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('products.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Katalog Produk</span>
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('categories.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('suppliers.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Pemasok (Supplier)</span>
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan & Pengaturan</p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Penjualan</span>
                    </a>

                    <a href="{{ route('users.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola Pengguna</span>
                    </a>

                    <a href="{{ route('settings.buatqris') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('settings.buatqris*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Pengaturan QRIS</span>
                    </a>
                @endcan
            </nav>
        </aside>

        <!-- Mobile Drawer Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="fixed inset-0 bg-slate-900/50 z-40 md:hidden backdrop-blur-xs"></div>

        <!-- Mobile Drawer Menu -->
        <div x-show="sidebarOpen" x-cloak
             class="fixed inset-y-0 left-0 w-72 bg-white z-50 p-4 flex flex-col md:hidden overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <span class="font-bold text-slate-900">Menu Al-Hidayah</span>
                <button type="button" @click="sidebarOpen = false" class="p-2 text-slate-500 hover:text-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="mt-4 space-y-1 text-sm flex-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Dashboard</a>
                <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-lg bg-teal-50 text-teal-800 font-semibold">Kasir POS</a>
                <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Riwayat Penjualan</a>
                <a href="{{ route('shift.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Shift Kasir</a>
                <a href="{{ route('inventory.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Mutasi Stok</a>
                <a href="{{ route('inventory.stock-in') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Stok Masuk</a>
                <a href="{{ route('inventory.stock-out') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Stok Keluar</a>
                <a href="{{ route('customers.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Data Pelanggan</a>

                @can('admin')
                    <div class="pt-2 border-t border-slate-100 my-2"></div>
                    <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Produk</a>
                    <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Kategori</a>
                    <a href="{{ route('suppliers.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Pemasok</a>
                    <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Laporan Penjualan</a>
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Pengguna</a>
                    <a href="{{ route('settings.buatqris') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Pengaturan QRIS</a>
                @endcan
            </nav>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-20 md:pb-8">
            <!-- Toast Notifications -->
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-xs flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                </div>
            @endif

            @if (session('warning'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-r-lg shadow-xs flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                    <button @click="show = false" class="text-amber-600 hover:text-amber-900">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-xs">
                    <div class="font-semibold text-sm mb-1">Perhatian:</div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (< 768px) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-30 flex justify-around items-center h-16 shadow-lg">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('dashboard') ? 'text-teal-600 font-semibold' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] mt-0.5">Beranda</span>
        </a>

        <a href="{{ route('pos.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-teal-600 font-semibold relative">
            <div class="w-10 h-10 -mt-5 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-[10px] mt-0.5">Kasir POS</span>
        </a>

        <a href="{{ route('sales.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('sales.*') ? 'text-teal-600 font-semibold' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[10px] mt-0.5">Riwayat</span>
        </a>

        <a href="{{ route('shift.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 {{ request()->routeIs('shift.*') ? 'text-teal-600 font-semibold' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[10px] mt-0.5">Shift</span>
        </a>

        <button @click="sidebarOpen = true" type="button" class="flex flex-col items-center justify-center flex-1 py-1 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span class="text-[10px] mt-0.5">Menu</span>
        </button>
    </nav>

    <!-- Global Modern Confirmation Modal (Replaces native browser 'localhost says' confirm) -->
    <div x-data="{
        confirmOpen: false,
        confirmTitle: 'Konfirmasi Tindakan',
        confirmMessage: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmCallback: null,
        confirmButtonText: 'Ya, Lanjutkan',
        confirmIsDanger: true
    }"
    @open-confirm.window="
        confirmTitle = $event.detail.title || 'Konfirmasi Tindakan';
        confirmMessage = $event.detail.message || 'Apakah Anda yakin?';
        confirmCallback = $event.detail.callback;
        confirmButtonText = $event.detail.buttonText || 'Ya, Lanjutkan';
        confirmIsDanger = $event.detail.isDanger !== false;
        confirmOpen = true;
    "
    x-show="confirmOpen" x-cloak
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center animate-in fade-in zoom-in-95 duration-150">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                 :class="confirmIsDanger ? 'bg-red-50 text-red-600' : 'bg-teal-50 text-teal-600'">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="font-bold text-lg text-slate-900 mb-1" x-text="confirmTitle"></h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed" x-text="confirmMessage"></p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="confirmOpen = false"
                        class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="button"
                        @click="if (confirmCallback) confirmCallback(); confirmOpen = false;"
                        :class="confirmIsDanger ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-teal-600 hover:bg-teal-700 text-white'"
                        class="py-2.5 px-4 font-bold rounded-xl text-xs transition cursor-pointer shadow-xs"
                        x-text="confirmButtonText">
                </button>
            </div>
        </div>
    </div>

    <script>
        window.customConfirm = function(event, message, title = 'Konfirmasi Tindakan', buttonText = 'Ya, Lanjutkan') {
            event.preventDefault();
            const form = event.target.closest('form');
            window.dispatchEvent(new CustomEvent('open-confirm', {
                detail: {
                    title: title,
                    message: message,
                    buttonText: buttonText,
                    callback: () => {
                        if (form) form.submit();
                    }
                }
            }));
            return false;
        };
    </script>

    @livewireScripts
</body>
</html>

