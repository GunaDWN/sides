@props([
    'type' => 'success',
    'message' => null,
    'title' => null,
    'timeout' => 4000,
])

@php
    $styles = [
        'success' => [
            'bg' => 'bg-white/95 border-emerald-200/90 text-slate-800 shadow-xl shadow-emerald-900/10 ring-1 ring-emerald-500/10',
            'iconBg' => 'bg-emerald-600 text-white shadow-emerald-600/30',
            'icon' => 'M5 13l4 4L19 7',
        ],
        'error' => [
            'bg' => 'bg-white/95 border-rose-200/90 text-slate-800 shadow-xl shadow-rose-900/10 ring-1 ring-rose-500/10',
            'iconBg' => 'bg-rose-600 text-white shadow-rose-600/30',
            'icon' => 'M6 18L18 6M6 6l12 12',
        ],
        'warning' => [
            'bg' => 'bg-white/95 border-amber-200/90 text-slate-800 shadow-xl shadow-amber-900/10 ring-1 ring-amber-500/10',
            'iconBg' => 'bg-amber-500 text-white shadow-amber-500/30',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'info' => [
            'bg' => 'bg-white/95 border-sky-200/90 text-slate-800 shadow-xl shadow-sky-900/10 ring-1 ring-sky-500/10',
            'iconBg' => 'bg-sky-600 text-white shadow-sky-600/30',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $s = $styles[$type] ?? $styles['info'];
@endphp

<div x-data="{ show: true }"
     x-init="setTimeout(() => show = false, {{ $timeout }})"
     x-show="show"
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="opacity-0 translate-y-2 md:translate-y-0 md:translate-x-8 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0 scale-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="opacity-100 translate-y-0 md:translate-x-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-2 md:translate-x-8 scale-95"
     class="fixed top-6 right-6 z-[100] max-w-sm w-full rounded-2xl border p-4 backdrop-blur-md transition-all duration-300 {{ $s['bg'] }}">
    
    <div class="flex items-start gap-3.5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 font-bold shadow-md {{ $s['iconBg'] }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}" />
            </svg>
        </div>

        <div class="flex-1 min-w-0 pt-0.5">
            @if($title)
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 mb-0.5">{{ $title }}</h4>
            @endif
            <p class="text-xs font-bold text-slate-800 leading-relaxed">{{ $message ?? $slot }}</p>
        </div>

        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
