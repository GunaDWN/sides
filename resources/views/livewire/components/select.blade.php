<div x-data="{ 
    open: false, 
    search: '',
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
}" class="relative w-full">

    @if($label)
        <label class="block text-xs font-semibold text-slate-700 mb-1">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <!-- Trigger Button -->
    <button type="button" @click="toggleOpen()" @click.outside="open = false" 
        class="w-full flex items-center justify-between text-left text-sm rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 shadow-2xs hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
        
        <span class="truncate {{ $value !== '' && $value !== null ? 'text-slate-900 font-semibold' : 'text-slate-400' }}">
            {{ $selectedLabel ?? $placeholder }}
        </span>

        <div class="flex items-center gap-1.5 ml-2 flex-shrink-0">
            @if($value !== '' && $value !== null)
                <span @click.stop="$wire.clearSelection(); search = ''" class="p-0.5 hover:bg-slate-100 rounded text-slate-400 hover:text-slate-600 transition-colors" title="Bersihkan">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </span>
            @endif
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180 text-emerald-600': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <!-- Floating Options Popover Menu -->
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
            <div wire:click="selectOption('')" @click="open = false; search = ''" 
                class="px-3.5 py-2.5 hover:bg-slate-50 cursor-pointer text-slate-400 font-medium">
                {{ $placeholder }}
            </div>

            @foreach($formattedOptions as $item)
                <div wire:click="selectOption('{{ $item['value'] }}')" @click="open = false; search = ''" 
                    x-show="!search || '{{ strtolower(addslashes($item['label'])) }}'.includes(search.toLowerCase())"
                    class="px-3.5 py-2.5 hover:bg-emerald-50 hover:text-emerald-900 cursor-pointer flex items-center justify-between transition-colors {{ $value == $item['value'] ? 'bg-emerald-50/80 text-emerald-800 font-bold' : 'text-slate-700' }}">
                    <span>{{ $item['label'] }}</span>
                    @if($value == $item['value'])
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
