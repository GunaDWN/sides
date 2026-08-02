<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIDES') }} - Portal Layanan Desa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ sidebarCollapsed: false, mobileOpen: false }" class="font-sans antialiased text-slate-900 bg-slate-50 min-h-screen flex">
    @php
        $user = auth()->user();
        $isAdmin = $user?->isAdmin();
        $hasAccountApprovePerm = $user?->hasPermissionTo('registrasi-akun.view') || $user?->hasPermissionTo('registrasi-akun.approve');
        $hasLetterApprovePerm = $user?->hasPermissionTo('pengajuan-surat.approve') || ($user?->getActiveJabatans()->count() > 0);
    @endphp

    <!-- Mobile Overlay Backdrop -->
    <div x-show="mobileOpen" 
        @click="mobileOpen = false" 
        x-transition:enter="transition-opacity ease-linear duration-200" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition-opacity ease-linear duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden" 
        style="display: none;"></div>

    <!-- Sidebar Container -->
    <aside 
        :class="{ 
            'w-64': !sidebarCollapsed, 
            'w-20': sidebarCollapsed,
            'translate-x-0': mobileOpen,
            '-translate-x-full md:translate-x-0': !mobileOpen
        }"
        class="fixed md:sticky top-0 left-0 h-screen bg-white text-slate-700 flex-shrink-0 flex flex-col justify-between border-r border-slate-200/80 shadow-xs z-50 transition-all duration-300">
        
        <!-- Top Section: Brand & Nav Links (Scrollable) -->
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
            <!-- Brand Header -->
            <div class="p-4 flex items-center justify-between border-b border-slate-100 flex-shrink-0 h-16"
                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                
                <!-- Expanded State: Logo + Title -->
                <a href="{{ route('dashboard') }}" wire:navigate x-show="!sidebarCollapsed" class="flex items-center gap-3 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="SIDES Logo" class="w-9 h-9 object-contain rounded-xl shadow-xs flex-shrink-0">
                    <div class="truncate">
                        <span class="text-base font-extrabold text-slate-900 tracking-tight block leading-tight">SIDES</span>
                        <span class="text-[10px] block text-emerald-600 font-bold truncate max-w-[130px]">{{ $isAdmin ? 'Super Admin' : ($user?->desa?->nama ? 'Desa ' . $user->desa->nama : 'Portal Desa') }}</span>
                    </div>
                </a>

                <!-- Expanded State: Collapse Button (<<) Inside Sidebar -->
                <button type="button" 
                    x-show="!sidebarCollapsed"
                    @click="sidebarCollapsed = true" 
                    title="Tutup Sidebar"
                    class="hidden md:flex items-center justify-center p-1.5 rounded-xl text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Collapsed State: Logo that swaps to Expand Icon on Hover -->
                <button type="button"
                    x-show="sidebarCollapsed"
                    @click="sidebarCollapsed = false"
                    x-data="{ isHovered: false }"
                    @mouseenter="isHovered = true"
                    @mouseleave="isHovered = false"
                    title="Buka Sidebar"
                    class="hidden md:flex items-center justify-center p-1 rounded-xl transition-all hover:bg-emerald-50 text-emerald-700">
                    <!-- Default: Logo Only -->
                    <img x-show="!isHovered" src="{{ asset('images/logo.png') }}" alt="SIDES Logo" class="w-9 h-9 object-contain rounded-xl shadow-xs flex-shrink-0">
                    <!-- On Hover: Expand Icon (>>) -->
                    <div x-show="isHovered" class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>

                <!-- Mobile Close Button -->
                <button type="button" @click="mobileOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Scrollable Navigation Items -->
            <div class="flex-1 overflow-y-auto p-3 space-y-6">
                <!-- Main Nav -->
                <div class="space-y-1">
                    <div x-show="!sidebarCollapsed" class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>

                    <a href="{{ route('dashboard') }}" wire:navigate 
                        :title="sidebarCollapsed ? 'Dashboard' : ''"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
                    </a>

                    @if(!$isAdmin)
                        <a href="{{ route('warga.ajukan-surat') }}" wire:navigate 
                            :title="sidebarCollapsed ? 'Ajukan Surat Baru' : ''"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('warga.ajukan-surat') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Ajukan Surat Baru</span>
                        </a>
                        <a href="{{ route('warga.riwayat-pengajuan') }}" wire:navigate 
                            :title="sidebarCollapsed ? 'Riwayat Pengajuan' : ''"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('warga.riwayat-pengajuan*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Riwayat Pengajuan</span>
                        </a>
                    @endif
                </div>

                <!-- Pejabat Section -->
                @if($hasLetterApprovePerm)
                    <div class="space-y-1">
                        <div x-show="!sidebarCollapsed" class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tugas Pejabat</div>
                        <a href="{{ route('pejabat.inbox-approval') }}" wire:navigate 
                            :title="sidebarCollapsed ? 'Inbox Approval Surat' : ''"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('pejabat.inbox-approval*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                            <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Inbox Approval Surat</span>
                        </a>
                    </div>
                @endif

                <!-- Admin Section -->
                @if($isAdmin || $hasAccountApprovePerm)
                    <div class="space-y-1">
                        <div x-show="!sidebarCollapsed" class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Administrator</div>
                        @if($hasAccountApprovePerm || $isAdmin)
                            <a href="{{ route('admin.approval-akun') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Approval Akun' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.approval-akun*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Approval Akun</span>
                            </a>
                        @endif

                        @if($isAdmin)
                            <a href="{{ route('admin.desa') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Data Desa' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.desa*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Data Desa</span>
                            </a>
                            <a href="{{ route('admin.user') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Pengelolaan User' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.user*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Pengelolaan User</span>
                            </a>
                            <a href="{{ route('admin.jabatan') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Master Jabatan' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.jabatan*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Master Jabatan</span>
                            </a>
                            <a href="{{ route('admin.hak-akses') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Kelola Hak Akses' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.hak-akses*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Kelola Hak Akses</span>
                            </a>
                            <a href="{{ route('admin.warga') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Data Warga' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.warga*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Data Warga</span>
                            </a>
                            <a href="{{ route('admin.jenis-surat') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Jenis Surat' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.jenis-surat*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Jenis Surat</span>
                            </a>
                            <a href="{{ route('admin.log-aktivitas') }}" wire:navigate 
                                :title="sidebarCollapsed ? 'Log Aktivitas' : ''"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all {{ request()->routeIs('admin.log-aktivitas*') ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/80 shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60' }}" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                                <svg class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">Log Aktivitas</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Sticky Footer (User Profile Card in Sidebar - ALWAYS PINNED AT BOTTOM) -->
        <div class="p-3 border-t border-slate-200/80 bg-slate-50/80 flex-shrink-0">
            <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : 'mb-3'">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow flex-shrink-0" :title="sidebarCollapsed ? '{{ $user->name }} ({{ $user->role }})' : ''">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-emerald-700 font-semibold capitalize truncate">{{ $user->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" :class="sidebarCollapsed ? 'mt-2' : ''">
                @csrf
                <button type="submit" 
                    :title="sidebarCollapsed ? 'Keluar Sistem' : ''"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-slate-200 bg-white">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="!sidebarCollapsed">Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 min-h-screen">
        <!-- Top Navbar (Shadcn Glass Header) -->
        <header class="bg-white border-b border-slate-200/80 h-16 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 shadow-xs flex-shrink-0">
            <div class="flex items-center gap-3">
                <!-- Mobile Toggle Button -->
                <button type="button" @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-500 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 hidden sm:inline">Lokasi / Scope</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold text-xs">
                        {{ $isAdmin ? 'Super Admin (Pengelola Multi-Desa)' : ($user?->desa?->nama ? 'Desa ' . $user->desa->nama : 'Sistem Utama') }}
                    </span>
                </div>
                @if($user?->getActiveJabatans()->count() > 0)
                    <div class="hidden sm:flex gap-1.5">
                        @foreach($user->getActiveJabatans() as $wj)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                {{ $wj->jabatan->nama }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Global Loading Indicator -->
            <div wire:loading class="text-xs font-bold text-emerald-600 flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="hidden sm:inline">Memproses...</span>
            </div>
        </header>

        <!-- Page Content Container -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 font-bold">✓</div>
                        <span class="text-xs font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center flex-shrink-0 font-bold">✕</div>
                        <span class="text-xs font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
