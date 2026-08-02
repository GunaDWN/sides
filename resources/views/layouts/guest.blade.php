<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIDES') }} - Portal Layanan Publik Desa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 min-h-screen flex flex-col justify-between">
    <!-- Navbar Guest -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" wire:navigate class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="SIDES Logo" class="w-10 h-10 object-contain rounded-xl shadow-xs">
                <div>
                    <span class="text-lg font-extrabold text-slate-900 tracking-tight">SIDES</span>
                    <span class="text-[10px] block text-emerald-600 font-bold uppercase tracking-wider">Sistem Informasi Desa</span>
                </div>
            </a>

            <nav class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate class="px-3 py-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Beranda</a>
                <a href="{{ route('cek-status-registrasi') }}" wire:navigate class="px-3 py-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Cek Status Akun</a>
                <a href="{{ route('registrasi-warga') }}" wire:navigate class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors border border-emerald-200">Registrasi Warga</a>
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md transition-all">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition-all">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Body -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} SIDES — Sistem Informasi Desa. Pengajuan Surat Digital & Administrasi Terpadu.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
