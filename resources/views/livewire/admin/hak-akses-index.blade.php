<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Hak Akses (Permissions)</h1>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi hak akses fitur sistem untuk setiap Jabatan dan spesifik per Desa.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.jabatan') }}" wire:navigate class="px-4 py-2 rounded-xl font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 text-xs transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Master Jabatan
            </a>
            <button wire:click="save" class="px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Hak Akses
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <!-- Selector Bar (Desa & Jabatan) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Select Desa -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">1. Pilih Desa</label>
                <x-select 
                    wire:model.live="desa_id" 
                    placeholder="-- Semua Desa / Global --"
                    :options="$desasList"
                    :searchable="true"
                />
                <p class="text-[11px] text-slate-400 mt-1">Pilih Desa spesifik atau biarkan kosong untuk berlaku secara Global.</p>
            </div>

            <!-- Select Jabatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">2. Pilih Jabatan Target <span class="text-rose-500">*</span></label>
                <x-select 
                    wire:model.live="jabatan_id" 
                    placeholder="-- Pilih Jabatan --"
                    :options="$jabatansList"
                    :searchable="true"
                />
                <p class="text-[11px] text-slate-400 mt-1">Pilih struktur Jabatan yang ingin dikonfigurasi hak aksesnya.</p>
            </div>
        </div>
    </div>

    @if($selectedJabatan)
        <!-- Permission Matrix Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-6 p-6">
            <!-- Status Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-slate-900">{{ $selectedJabatan->nama }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $selectedJabatan->kode }}
                        </span>
                        @if($selectedDesa)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Desa {{ $selectedDesa->nama }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Scope Global (Semua Desa)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ count($selectedPermissions) }} permission diaktifkan untuk jabatan ini.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="selectAll" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                        Pilih Semua
                    </button>
                    <button type="button" wire:click="deselectAll" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-all">
                        Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Grouped Permission Checklist -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($permissionsGrouped as $groupName => $perms)
                    <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Modul {{ ucfirst($groupName) }}
                            </span>
                            <span class="text-[10px] font-semibold text-slate-400">
                                {{ collect($perms)->whereIn('id', $selectedPermissions)->count() }} / {{ count($perms) }} Aktif
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($perms as $p)
                                <label class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-white transition-all cursor-pointer border border-transparent hover:border-slate-200 group">
                                    <input type="checkbox" 
                                        wire:model.live="selectedPermissions" 
                                        value="{{ $p->id }}" 
                                        class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <div class="space-y-0.5">
                                        <span class="block text-xs font-semibold text-slate-800 group-hover:text-emerald-800 transition-colors">
                                            {{ $p->label }}
                                        </span>
                                        <span class="block text-[10px] font-mono text-slate-400">
                                            {{ $p->name }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Save Action -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button wire:click="save" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan Hak Akses
                </button>
            </div>
        </div>
    @else
        <div class="p-12 rounded-2xl bg-white border border-slate-200 text-center space-y-3">
            <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <p class="text-sm font-semibold text-slate-700">Silakan Pilih Jabatan Terlebih Dahulu</p>
            <p class="text-xs text-slate-400">Pilih Jabatan dan Desa pada dropdown di atas untuk mengonfigurasi matriks hak akses.</p>
        </div>
    @endif
</div>
