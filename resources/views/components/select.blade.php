@props([
    'label' => null,
    'placeholder' => '-- Pilih --',
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'nama',
    'selected' => null,
    'searchable' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
    $formattedOptions = [];

    if (is_iterable($options)) {
        foreach ($options as $key => $opt) {
            $val = is_object($opt) ? ($opt->{$optionValue} ?? $key) : (is_array($opt) ? ($opt[$optionValue] ?? $key) : $key);
            $lbl = is_object($opt) ? ($opt->{$optionLabel} ?? $opt) : (is_array($opt) ? ($opt[$optionLabel] ?? $opt) : $opt);
            
            $formattedOptions[] = [
                'value' => (string) $val,
                'label' => (string) $lbl,
            ];
        }
    }
    $optionsJson = json_encode($formattedOptions, JSON_HEX_APOS | JSON_HEX_QUOT);
@endphp

<div x-data="{ 
    open: false, 
    search: '',
    options: {{ $optionsJson }},
    getLabel(val) {
        if (!val) return '';
        const found = this.options.find(o => o.value == val);
        return found ? found.label : val;
    },
    filteredOptions() {
        if (!this.search) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => o.label.toLowerCase().includes(q));
    },
    toggleOpen() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        }
    }
}" class="relative w-full" wire:key="{{ $wireModel }}-select-{{ md5($optionsJson) }}">

    @if($label)
        <label class="block text-xs font-semibold text-slate-700 mb-1">
            {{ $label }}
            @if($attributes->has('required')) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <button type="button" @click="toggleOpen()" @click.outside="open = false" 
        {{ $attributes->merge(['class' => 'w-full flex items-center justify-between text-left text-sm rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 shadow-2xs hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all']) }}>
        
        <span class="truncate" :class="{ 'text-slate-900 font-semibold': {{ $wireModel ? '$wire.get(\'' . $wireModel . '\')' : 'false' }}, 'text-slate-400': !{{ $wireModel ? '$wire.get(\'' . $wireModel . '\')' : 'true' }} }">
            @if($wireModel)
                <template x-if="$wire.get('{{ $wireModel }}')">
                    <span x-text="getLabel($wire.get('{{ $wireModel }}'))"></span>
                </template>
                <template x-if="!$wire.get('{{ $wireModel }}')">
                    <span>{{ $placeholder }}</span>
                </template>
            @else
                <span>{{ $placeholder }}</span>
            @endif
        </span>

        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 ml-2 flex-shrink-0" :class="{ 'rotate-180 text-emerald-600': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Floating Options Dropdown Menu -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1.5 w-full rounded-xl bg-white border border-slate-200/90 shadow-xl py-1 max-h-64 overflow-hidden flex flex-col focus:outline-none text-xs" 
        style="display: none;">

        @if($searchable)
            <div class="p-2 border-b border-slate-100 bg-slate-50/50" @click.stop>
                <input type="text" 
                    x-model="search"
                    x-ref="searchInput"
                    placeholder="Cari..." 
                    class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white"
                    @keydown.escape="open = false">
            </div>
        @endif
        
        <div class="overflow-y-auto flex-1 divide-y divide-slate-50">
            <div @click="@if($wireModel) $wire.set('{{ $wireModel }}', ''); @endif open = false; search = ''" class="px-3.5 py-2.5 hover:bg-slate-50 cursor-pointer text-slate-400 border-b border-slate-100">
                {{ $placeholder }}
            </div>

            <template x-for="item in filteredOptions()" :key="item.value">
                <div @click="@if($wireModel) $wire.set('{{ $wireModel }}', item.value); @endif open = false; search = ''" 
                    class="px-3.5 py-2.5 hover:bg-emerald-50 hover:text-emerald-900 cursor-pointer flex items-center justify-between transition-colors"
                    :class="{ 'bg-emerald-50/70 text-emerald-800 font-bold': {{ $wireModel ? '$wire.get(\'' . $wireModel . '\')' : 'false' }} == item.value, 'text-slate-700': {{ $wireModel ? '$wire.get(\'' . $wireModel . '\')' : 'false' }} != item.value }">
                    <span x-text="item.label"></span>
                    <template x-if="{{ $wireModel ? '$wire.get(\'' . $wireModel . '\')' : 'false' }} == item.value">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    </template>
                </div>
            </template>
        </div>
    </div>

    @if($wireModel)
        @error($wireModel)
            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
        @enderror
    @endif
</div>
