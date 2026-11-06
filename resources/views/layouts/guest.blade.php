<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS Toko Al-Hidayah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 antialiased relative overflow-x-hidden selection:bg-teal-500 selection:text-white">

    <!-- Decorative background glow and geometric pattern -->
    <div class="fixed inset-0 pointer-events-none opacity-20">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-teal-500 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-600 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#14b8a6_1px,transparent_1px)] [background-size:24px_24px] opacity-30"></div>
    </div>

    <div class="w-full max-w-4xl relative z-10">
        {{ $slot }}
    </div>

</body>
</html>
