@props([
    'show' => false,
    'title' => 'Konfirmasi Tindakan',
    'description' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmAction' => null,
    'cancelAction' => null,
    'variant' => 'emerald',
])

@php
    $variantStyles = [
        'emerald' => [
            'iconBg' => 'bg-emerald-100 text-emerald-600',
            'btnConfirm' => 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20 text-white',
            'icon' => 'M5 13l4 4L19 7',
        ],
        'rose' => [
            'iconBg' => 'bg-rose-100 text-rose-600',
            'btnConfirm' => 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20 text-white',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-100 text-amber-600',
            'btnConfirm' => 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/20 text-white',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'sky' => [
            'iconBg' => 'bg-sky-100 text-sky-600',
            'btnConfirm' => 'bg-sky-600 hover:bg-sky-700 shadow-sky-600/20 text-white',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $v = $variantStyles[$variant] ?? $variantStyles['emerald'];
@endphp

@if($show)
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-200">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-5 transform transition-all duration-200 scale-100">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 font-bold shadow-xs {{ $v['iconBg'] }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $v['icon'] }}" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $description }}</p>
                </div>
            </div>

            @if($slot->isNotEmpty())
                <div class="pt-1">
                    {{ $slot }}
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" 
                        @if($cancelAction) wire:click="{{ $cancelAction }}" @endif 
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition-all">
                    {{ $cancelText }}
                </button>
                <button type="button" 
                        @if($confirmAction) wire:click="{{ $confirmAction }}" @endif 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2 {{ $v['btnConfirm'] }}">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
@endif
