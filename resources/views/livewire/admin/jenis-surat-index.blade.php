<div class="space-y-6">
    {{-- Global Loading Backdrop Overlays --}}
    <div wire:loading.flex wire:target="openModal, openSignatureEditor" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-[100] items-center justify-center">
        <div class="bg-white rounded-2xl p-5 shadow-2xl border border-slate-200 flex items-center gap-3.5 max-w-sm mx-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                <svg class="animate-spin h-5 w-5 text-violet-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xs">Mempersiapkan Editor</p>
                <p class="text-[11px] text-slate-500">Memuat data dan merender halaman preview...</p>
            </div>
        </div>
    </div>

    <div wire:loading.flex wire:target="save, syncAllPlacements" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-[100] items-center justify-center">
        <div class="bg-white rounded-2xl p-5 shadow-2xl border border-slate-200 flex items-center gap-3.5 max-w-sm mx-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                <svg class="animate-spin h-5 w-5 text-emerald-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xs">Menyimpan Perubahan</p>
                <p class="text-[11px] text-slate-500">Menyimpan konfigurasi surat & posisi tanda tangan...</p>
            </div>
        </div>
    </div>

    <div wire:loading.flex wire:target="removeTemplate" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-[100] items-center justify-center">
        <div class="bg-white rounded-2xl p-5 shadow-2xl border border-slate-200 flex items-center gap-3.5 max-w-sm mx-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                <svg class="animate-spin h-5 w-5 text-rose-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xs">Menghapus Template</p>
                <p class="text-[11px] text-slate-500">Menghapus file template dari sistem...</p>
            </div>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Jenis Surat</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola kategori surat, template dokumen, dan urutan jabatan persetujuan.</p>
        </div>
        <button wire:click="openModal" 
                wire:loading.attr="disabled"
                wire:target="openModal"
                class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-1.5">
            <svg wire:loading.remove wire:target="openModal" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <svg wire:loading wire:target="openModal" class="animate-spin w-4 h-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span>Tambah Jenis Surat</span>
        </button>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Search Filter with Loading Spinner --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="w-full text-sm rounded-xl pl-9 pr-8 border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" 
                   placeholder="Cari jenis surat...">
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="animate-spin h-4 w-4 text-emerald-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Table of Jenis Surat --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Nama Surat</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Approval</th>
                        <th class="px-4 py-3">Urutan Pejabat</th>
                        <th class="px-4 py-3">TTD</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surats as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $s->nama }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $s->kode }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $s->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $s->desa->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->butuh_approval ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $s->butuh_approval ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($s->approvals as $app)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $app->urutan }}. {{ $app->jabatan->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($s->signaturePlacements->count() > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-violet-100 text-violet-800 border border-violet-200">
                                        {{ $s->signaturePlacements->count() }} posisi
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openModal({{ $s->id }})" 
                                        wire:loading.attr="disabled"
                                        wire:target="openModal({{ $s->id }})"
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 text-xs font-semibold flex items-center gap-1 ml-auto transition-all">
                                    <svg wire:loading.remove wire:target="openModal({{ $s->id }})" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <svg wire:loading wire:target="openModal({{ $s->id }})" class="animate-spin w-3.5 h-3.5 text-slate-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span>Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada jenis surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $surats->links() }}</div>
    </div>

    {{-- Modal: Form Jenis Surat --}}
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ $surat_id ? 'Ubah Jenis Surat' : 'Tambah Jenis Surat Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Nama Surat <span class="text-rose-500">*</span></label><input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300">@error('nama')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Kode Surat</label><input type="text" wire:model="kode" class="w-full text-sm rounded-lg border-slate-300"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Kategori</label><input type="text" wire:model="kategori" class="w-full text-sm rounded-lg border-slate-300" placeholder="Kependudukan, Perekonomian..."></div>
                        <div>
                            <x-select 
                                wire:model.live="desa_id" 
                                label="Desa" 
                                placeholder="-- Pilih Desa --"
                                :options="$desasList"
                                :searchable="true"
                                required
                            />
                        </div>
                    </div>
                    <div><label class="block font-semibold text-slate-700 mb-1">Deskripsi</label><textarea wire:model="deskripsi" rows="2" class="w-full text-sm rounded-lg border-slate-300"></textarea></div>
                    
                    {{-- Template Document Card / Upload Section --}}
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">
                            File Template Surat <span class="text-slate-400 font-normal">(.doc, .docx, .pdf — Maks. 10MB)</span>
                        </label>

                        @if($existingTemplatePath && !$isChangingTemplate)
                            @php
                                $ext = $this->getTemplateExtension();
                                $isWord = in_array($ext, ['doc', 'docx']);
                                $isPdf = $ext === 'pdf';
                            @endphp
                            <div class="p-3.5 rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50/90 via-teal-50/50 to-emerald-50/40 shadow-xs space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs
                                            {{ $isWord ? 'bg-blue-600 text-white' : ($isPdf ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white') }}">
                                            @if($isWord)
                                                <span class="font-extrabold text-xs">DOC</span>
                                            @elseif($isPdf)
                                                <span class="font-extrabold text-xs">PDF</span>
                                            @else
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 text-xs truncate" title="{{ $existingTemplateName ?? 'Template Surat' }}">
                                                {{ $existingTemplateName ?? 'Template Surat' }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $isWord ? 'bg-blue-100 text-blue-800' : ($isPdf ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700') }}">
                                                    {{ strtoupper($ext ?? 'FILE') }}
                                                </span>
                                                @if($this->getFormattedTemplateSize())
                                                    <span class="text-[10px] text-slate-500 font-medium">{{ $this->getFormattedTemplateSize() }}</span>
                                                @endif
                                                @if(count($previewPages) > 0)
                                                    <span class="text-slate-300">•</span>
                                                    <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-100/70 px-1.5 py-0.5 rounded">
                                                        {{ count($previewPages) }} Halaman
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex-shrink-0 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                        Template Terpasang
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-emerald-200/60 text-[11px]">
                                    <div class="flex items-center gap-2">
                                        @if($surat_id && $existingTemplatePath)
                                            <a href="{{ route('download.template', $surat_id) }}" target="_blank" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                Unduh File
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                wire:click="toggleChangeTemplate" 
                                                wire:loading.attr="disabled"
                                                wire:target="toggleChangeTemplate"
                                                class="px-2.5 py-1 rounded-lg font-semibold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs">
                                            Ganti Template
                                        </button>
                                        <button type="button" 
                                                wire:click="removeTemplate" 
                                                wire:loading.attr="disabled"
                                                wire:target="removeTemplate"
                                                class="px-2 py-1 rounded-lg font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition-colors flex items-center gap-1">
                                            <svg wire:loading.remove wire:target="removeTemplate" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <svg wire:loading wire:target="removeTemplate" class="animate-spin w-3 h-3 text-rose-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 transition-all bg-slate-50/50 hover:bg-emerald-50/20 text-center">
                                <input type="file" wire:model="template" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="space-y-1.5">
                                    <div class="w-10 h-10 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">
                                        Klik untuk memilih file <span class="font-normal text-slate-500">atau seret file ke sini</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400">Mendukung format Word (.DOCX, .DOC) atau PDF (Maks. 10MB)</p>
                                </div>
                            </div>

                            @if($existingTemplatePath && $isChangingTemplate)
                                <div class="flex justify-end mt-1">
                                    <button type="button" wire:click="toggleChangeTemplate" class="text-[10px] font-semibold text-slate-500 hover:text-slate-800 underline">
                                        Batal Ganti File
                                    </button>
                                </div>
                            @endif

                            <div wire:loading wire:target="template" class="p-3 mt-2 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-emerald-600 flex-shrink-0" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Sedang mengunggah file template dan memproses halaman preview...</span>
                            </div>
                        @endif
                        @error('template')<span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model.live="butuh_approval" class="rounded text-emerald-600"><span class="font-semibold text-slate-700">Membutuhkan Approval Pejabat</span></label>
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_active" class="rounded text-emerald-600"><span class="font-semibold text-slate-700">Surat Aktif</span></label>
                    </div>

                    @if($butuh_approval)
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Urutan Pejabat Approval</h4>
                                <button type="button" wire:click="addApprovalStep" class="px-3 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold border border-sky-200">+ Tambah Tahapan</button>
                            </div>
                            @foreach($approvalSteps as $index => $step)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">{{ $step['urutan'] }}</span>
                                    <div class="flex-1">
                                        <x-select 
                                            wire:model="approvalSteps.{{ $index }}.jabatan_id" 
                                            placeholder="-- Pilih Jabatan --"
                                            :options="$jabatansByDesa"
                                            :searchable="true"
                                        />
                                    </div>
                                    <button type="button" wire:click="removeApprovalStep({{ $index }})" class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold border border-rose-200">&times;</button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Button: Atur Posisi Tanda Tangan --}}
                        @if(count($approvalSteps) > 0 && collect($approvalSteps)->pluck('jabatan_id')->filter()->count() > 0)
                            <div class="pt-3 border-t border-slate-100">
                                <button type="button" 
                                    wire:click="openSignatureEditor"
                                    wire:loading.attr="disabled"
                                    wire:target="openSignatureEditor"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-bold text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed
                                    {{ count($previewPages) > 0 
                                        ? 'bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white shadow-lg shadow-violet-600/20' 
                                        : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                                    <svg wire:loading.remove wire:target="openSignatureEditor" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <svg wire:loading wire:target="openSignatureEditor" class="animate-spin w-5 h-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span wire:loading.remove wire:target="openSignatureEditor">Atur Posisi Tanda Tangan di Template</span>
                                    <span wire:loading wire:target="openSignatureEditor">Mempersiapkan Editor & Preview...</span>
                                    @if(count($signaturePlacements) > 0)
                                        <span wire:loading.remove wire:target="openSignatureEditor" class="px-2 py-0.5 rounded-full bg-white/20 text-[10px]">{{ count($signaturePlacements) }} posisi tersimpan</span>
                                    @endif
                                </button>
                                @if(count($previewPages) === 0)
                                    <p class="text-[10px] text-amber-600 text-center mt-1">Upload template terlebih dahulu untuk mengatur posisi tanda tangan</p>
                                @endif
                            </div>
                        @endif
                    @endif

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 shadow-md flex items-center gap-1.5 transition-all">
                            <svg wire:loading wire:target="save" class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="save">Simpan Jenis Surat</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Visual Signature Placement Editor --}}
    @if($showSignatureEditor)
        <div wire:ignore.self
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[60] flex items-center justify-center p-2 sm:p-4 lg:p-6"
             x-data="signatureEditor({
                pages: @js($previewPages),
                pageWidthMm: {{ $previewPageWidthMm }},
                pageHeightMm: {{ $previewPageHeightMm }},
                placements: @js($signaturePlacements),
                approvalSteps: @js($approvalSteps),
                jabatans: @js($jabatansByDesa),
                previewBaseUrl: '{{ url('/template-preview') }}'
             })"
             x-init="init()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-[1400px] h-[94vh] flex flex-col overflow-hidden">
                {{-- Header & Action Bar --}}
                <div class="px-5 py-3 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-3 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-violet-600/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                Editor Posisi Tanda Tangan
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-violet-50 text-violet-700 border border-violet-200" x-text="`${placedCount} / ${approvalSteps.length} Ditempatkan`"></span>
                            </h3>
                            <p class="text-[11px] text-slate-400">Atur peletakan tanda tangan pejabat di halaman template secara visual.</p>
                        </div>
                    </div>

                    {{-- Toolbar Center: Edit Controls (Cut, Copy, Paste, Delete) & Page Nav --}}
                    <div class="flex items-center gap-2">
                        {{-- Cut, Copy, Paste, Delete Tool Group --}}
                        <div class="flex items-center bg-slate-100/90 rounded-xl p-1 border border-slate-200/80 shadow-2xs">
                            <button type="button" 
                                    @click="cutSignature(selectedJabatan)" 
                                    :disabled="!selectedJabatan || !isPlaced(selectedJabatan)" 
                                    title="Potong Tanda Tangan (Ctrl+X)"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed text-slate-700 hover:bg-white hover:text-violet-700 hover:shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0L12 12zm0 0l-2.879-2.879a3 3 0 10-4.242 4.242 3 3 0 004.242 0L12 12z" /></svg>
                                <span>Potong</span>
                            </button>
                            <button type="button" 
                                    @click="copySignature(selectedJabatan)" 
                                    :disabled="!selectedJabatan || !isPlaced(selectedJabatan)" 
                                    title="Salin Posisi Tanda Tangan (Ctrl+C)"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed text-slate-700 hover:bg-white hover:text-violet-700 hover:shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                <span>Salin</span>
                            </button>
                            <button type="button" 
                                    @click="pasteSignature()" 
                                    :disabled="!clipboard" 
                                    title="Tempel di Halaman Ini (Ctrl+V)"
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                <span>Tempel</span>
                                <template x-if="clipboard">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                </template>
                            </button>
                            <div class="h-4 w-px bg-slate-300 mx-1"></div>
                            <button type="button" 
                                    @click="deleteSignature(selectedJabatan)" 
                                    :disabled="!selectedJabatan || !isPlaced(selectedJabatan)" 
                                    title="Hapus dari Halaman (Del)"
                                    class="flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed text-rose-600 hover:bg-rose-50">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>

                        {{-- Page Navigation Group --}}
                        <div class="flex items-center gap-1 bg-slate-100/90 rounded-xl p-1 border border-slate-200/80 shadow-2xs">
                            <button type="button" @click="prevPage()" :disabled="currentPage <= 1" class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:shadow-2xs disabled:opacity-25 disabled:cursor-not-allowed transition-all" title="Halaman Sebelumnya">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            
                            <div class="flex items-center gap-1 px-1">
                                <select x-model.number="currentPage" 
                                        @change="changeCurrentPage($event.target.value)"
                                        class="text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-lg py-1 px-2 focus:ring-1 focus:ring-violet-500 cursor-pointer shadow-2xs">
                                    <template x-for="p in totalPages" :key="p">
                                        <option :value="p" x-text="`Hal ${p} / ${totalPages}`"></option>
                                    </template>
                                </select>
                            </div>

                            <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages" class="p-1.5 rounded-lg text-slate-600 hover:bg-white hover:shadow-2xs disabled:opacity-25 disabled:cursor-not-allowed transition-all" title="Halaman Selanjutnya">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Actions Right --}}
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="saveAndClose()" 
                                wire:loading.attr="disabled"
                                wire:target="syncAllPlacements, closeSignatureEditor"
                                class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 shadow-md shadow-emerald-600/20 text-xs flex items-center gap-1.5 transition-all">
                            <svg wire:loading.remove wire:target="syncAllPlacements, closeSignatureEditor" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg wire:loading wire:target="syncAllPlacements, closeSignatureEditor" class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="syncAllPlacements, closeSignatureEditor">Simpan Posisi</span>
                            <span wire:loading wire:target="syncAllPlacements, closeSignatureEditor">Menyimpan...</span>
                        </button>
                        <button type="button" wire:click="closeSignatureEditor" class="px-3.5 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 text-xs transition-all">Tutup</button>
                    </div>
                </div>

                {{-- Editor Body --}}
                <div class="flex flex-1 min-h-0 overflow-hidden relative">
                    {{-- Left: Template Preview Canvas Area --}}
                    <div class="flex-1 overflow-auto bg-slate-200/70 p-6 lg:p-8 flex justify-center items-start select-none relative" 
                         x-ref="editorContainer"
                         @click.self="selectedJabatan = null">
                        
                        {{-- Toast Notification Inside Canvas --}}
                        <div x-show="toastMessage" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                             class="absolute top-4 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-full text-xs font-bold shadow-xl border flex items-center gap-2 backdrop-blur-md"
                             :class="{
                                'bg-violet-900/90 text-white border-violet-700': toastType === 'info',
                                'bg-emerald-900/90 text-white border-emerald-700': toastType === 'success',
                                'bg-amber-900/90 text-white border-amber-700': toastType === 'warning'
                             }"
                             style="display: none;">
                            <span x-text="toastMessage"></span>
                            <button type="button" @click="toastMessage = ''" class="ml-1 opacity-70 hover:opacity-100">&times;</button>
                        </div>

                        {{-- The Page Container --}}
                        <div class="relative shadow-2xl bg-white border border-slate-300 rounded-sm transition-all" 
                             x-ref="pageContainer"
                             :style="`width: ${displayWidth}px; height: ${displayHeight}px; flex-shrink: 0;`"
                             @click.self="selectedJabatan = null">
                            
                            {{-- Template Page Image --}}
                            <img :src="currentPageUrl" 
                                 class="w-full h-full object-contain pointer-events-none select-none rounded-sm"
                                 @load="onImageLoad()" 
                                 draggable="false">

                            {{-- Draggable Signature Boxes on Current Page --}}
                            <template x-for="(placement, jabatanId) in currentPagePlacements" :key="jabatanId">
                                <div class="absolute cursor-move select-none transition-shadow"
                                     :class="{
                                        'ring-2 ring-violet-600 ring-offset-2 z-30 shadow-2xl': selectedJabatan == jabatanId,
                                        'z-20 hover:ring-1 hover:ring-violet-400': selectedJabatan != jabatanId
                                     }"
                                     :style="`left: ${mmToPixel(placement.pos_x)}px; top: ${mmToPixel(placement.pos_y)}px; width: ${mmToPixel(placement.lebar)}px; height: ${mmToPixel(placement.tinggi + (placement.tampilkan_nama ? 5.5 : 0) + (placement.tampilkan_jabatan ? 4.5 : 0))}px;`"
                                     @click.stop="selectedJabatan = jabatanId"
                                     @mousedown="startDrag($event, jabatanId)"
                                     @touchstart="startDrag($event, jabatanId)">
                                    
                                    {{-- Ultra-Compact Floating Action Pill on Selected Box --}}
                                    <div x-show="selectedJabatan == jabatanId" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         class="absolute -top-9 left-1/2 -translate-x-1/2 flex items-center gap-0.5 bg-slate-900/95 text-white rounded-lg p-0.5 shadow-2xl z-40 border border-slate-700/80 backdrop-blur-md whitespace-nowrap"
                                         @click.stop>
                                        
                                        <button type="button" @click.stop="cutSignature(jabatanId)" class="w-6 h-6 rounded flex items-center justify-center hover:bg-slate-800 text-slate-300 hover:text-white transition-colors" title="Potong (Ctrl+X)">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0L12 12zm0 0l-2.879-2.879a3 3 0 10-4.242 4.242 3 3 0 004.242 0L12 12z" /></svg>
                                        </button>
                                        
                                        <button type="button" @click.stop="copySignature(jabatanId)" class="w-6 h-6 rounded flex items-center justify-center hover:bg-slate-800 text-slate-300 hover:text-white transition-colors" title="Salin (Ctrl+C)">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                        </button>
                                        
                                        {{-- Move Page Popover --}}
                                        <div class="relative" x-data="{ openPageMenu: false }">
                                            <button type="button" 
                                                    @click.stop="openPageMenu = !openPageMenu" 
                                                    class="h-6 px-1.5 rounded bg-slate-800 hover:bg-slate-700 text-violet-300 font-bold text-[10px] flex items-center gap-0.5 border border-slate-700/80 transition-colors" 
                                                    title="Pindah ke Halaman Lain">
                                                <span>Hal <span x-text="placement.halaman || 1"></span></span>
                                                <svg class="w-2.5 h-2.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                            <div x-show="openPageMenu" 
                                                 @click.outside="openPageMenu = false"
                                                 x-transition
                                                 class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-slate-900 border border-slate-700 rounded-lg p-1 shadow-2xl flex flex-col gap-0.5 min-w-[70px] z-50">
                                                <template x-for="p in totalPages" :key="p">
                                                    <button type="button" 
                                                            @click.stop="moveToPage(jabatanId, p); openPageMenu = false" 
                                                            class="px-2 py-1 rounded text-left text-[10px] font-bold flex items-center justify-between hover:bg-violet-600 hover:text-white transition-colors"
                                                            :class="p === (placement.halaman || 1) ? 'bg-violet-600/30 text-violet-300' : 'text-slate-300'">
                                                        <span x-text="`Hal ${p}`"></span>
                                                        <span x-show="p === (placement.halaman || 1)">✓</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="h-3 w-px bg-slate-700 mx-0.5"></div>
                                        
                                        <button type="button" @click.stop="deleteSignature(jabatanId)" class="w-6 h-6 rounded flex items-center justify-center hover:bg-rose-950/80 text-rose-400 hover:text-rose-200 transition-colors" title="Hapus (Del)">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>

                                    {{-- Signature Area Box --}}
                                    <div class="w-full border-2 border-dashed rounded-lg flex flex-col items-center justify-center relative overflow-hidden transition-colors"
                                         :class="{
                                            'border-violet-600 bg-violet-500/20': selectedJabatan == jabatanId,
                                            'border-violet-400 bg-violet-500/10 group-hover:bg-violet-500/15': selectedJabatan != jabatanId
                                         }"
                                         :style="`height: ${mmToPixel(placement.tinggi)}px;`">
                                        
                                        <div class="flex items-center gap-1 text-violet-700 opacity-90">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            <span class="text-[8px] font-extrabold truncate" x-text="getJabatanNama(jabatanId)"></span>
                                        </div>
                                    </div>

                                    {{-- Name & Title text footer below box --}}
                                    <template x-if="placement.tampilkan_nama || placement.tampilkan_jabatan">
                                        <div class="text-center mt-1 leading-tight px-1">
                                            <template x-if="placement.tampilkan_nama">
                                                <div class="text-[7.5px] font-bold text-slate-800 border-b border-slate-500 inline-block px-1 truncate max-w-full">Nama Pejabat</div>
                                            </template>
                                            <template x-if="placement.tampilkan_jabatan">
                                                <div class="text-[6.5px] font-medium text-slate-600 truncate max-w-full" x-text="getJabatanNama(jabatanId)"></div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Resize Corner Handle (Bottom-Right) --}}
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-violet-600 text-white rounded-tl-md rounded-br-md cursor-se-resize shadow-md flex items-center justify-center transition-opacity"
                                         :class="selectedJabatan == jabatanId ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                                         @mousedown.prevent.stop="startResize($event, jabatanId)"
                                         @touchstart.prevent.stop="startResize($event, jabatanId)">
                                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 22H20V20H22V22ZM22 16H20V18H22V16ZM16 22H18V20H16V22ZM22 12H20V14H22V12ZM12 22H14V20H12V22Z" /></svg>
                                    </div>

                                    {{-- Page Indicator Badge on Box --}}
                                    <div class="absolute -bottom-4 left-0 px-1.5 py-0.2 rounded bg-slate-800/90 text-white text-[7px] font-bold shadow-xs"
                                         x-text="`Hal ${placement.halaman || 1}`">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Right: Sidebar Controls --}}
                    <div class="w-88 border-l border-slate-200 bg-white overflow-y-auto flex-shrink-0 p-5 space-y-4 shadow-sm">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Tanda Tangan Pejabat</h4>
                                <p class="text-[10px] text-slate-400">Pilih pejabat untuk mengatur posisinya</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700" x-text="`${approvalSteps.length} Pejabat`"></span>
                        </div>
                        
                        {{-- List of Pejabat Cards --}}
                        <div class="space-y-3.5">
                            <template x-for="step in approvalSteps" :key="step.jabatan_id">
                                <div class="p-3.5 rounded-2xl border transition-all"
                                     :class="{
                                        'border-violet-500 bg-violet-50/70 shadow-sm ring-1 ring-violet-400': selectedJabatan == step.jabatan_id,
                                        'border-slate-200 bg-slate-50/70 hover:border-violet-300': selectedJabatan != step.jabatan_id && isPlaced(step.jabatan_id),
                                        'border-dashed border-amber-300 bg-amber-50/40': !isPlaced(step.jabatan_id)
                                     }"
                                     @click="selectAndFocus(step.jabatan_id)">
                                    
                                    {{-- Card Header: Number & Full Jabatan Name --}}
                                    <div class="flex items-start justify-between gap-2 mb-2.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-6 h-6 rounded-full bg-violet-600 text-white font-extrabold text-[10px] flex items-center justify-center flex-shrink-0 shadow-xs" x-text="step.urutan"></span>
                                            <span class="text-xs font-bold text-slate-900 leading-snug break-words" x-text="getJabatanNama(step.jabatan_id)"></span>
                                        </div>

                                        {{-- Page Switcher Dropdown in Sidebar --}}
                                        <template x-if="isPlaced(step.jabatan_id)">
                                            <div class="flex items-center gap-1 flex-shrink-0" @click.stop>
                                                <select :value="placements[step.jabatan_id].halaman" 
                                                        @change="moveToPage(step.jabatan_id, $event.target.value)"
                                                        class="text-[10px] font-bold text-violet-800 bg-white border border-violet-300 rounded-lg py-1 px-2 focus:ring-1 focus:ring-violet-500 cursor-pointer shadow-2xs">
                                                    <template x-for="p in totalPages" :key="p">
                                                        <option :value="p" :selected="p === placements[step.jabatan_id].halaman" x-text="`Hal ${p}`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    {{-- Placed State: Coordinates & Options --}}
                                    <template x-if="isPlaced(step.jabatan_id)">
                                        <div class="space-y-3 text-[11px] pt-1" @click.stop>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block font-semibold text-slate-600 text-[10px] mb-1">Posisi X (mm)</label>
                                                    <input type="number" step="0.5" class="w-full text-xs rounded-lg border-slate-300 px-2.5 py-1 font-mono focus:border-violet-500 focus:ring-violet-500 bg-white"
                                                           :value="formatCoord(placements[step.jabatan_id].pos_x)"
                                                           @input="placements[step.jabatan_id].pos_x = cleanNumber($event.target.value)">
                                                </div>
                                                <div>
                                                    <label class="block font-semibold text-slate-600 text-[10px] mb-1">Posisi Y (mm)</label>
                                                    <input type="number" step="0.5" class="w-full text-xs rounded-lg border-slate-300 px-2.5 py-1 font-mono focus:border-violet-500 focus:ring-violet-500 bg-white"
                                                           :value="formatCoord(placements[step.jabatan_id].pos_y)"
                                                           @input="placements[step.jabatan_id].pos_y = cleanNumber($event.target.value)">
                                                </div>
                                                <div>
                                                    <label class="block font-semibold text-slate-600 text-[10px] mb-1">Lebar TTD (mm)</label>
                                                    <input type="number" step="1" min="15" max="100" class="w-full text-xs rounded-lg border-slate-300 px-2.5 py-1 font-mono focus:border-violet-500 focus:ring-violet-500 bg-white"
                                                           :value="formatCoord(placements[step.jabatan_id].lebar)"
                                                           @input="placements[step.jabatan_id].lebar = cleanNumber($event.target.value)">
                                                </div>
                                                <div>
                                                    <label class="block font-semibold text-slate-600 text-[10px] mb-1">Tinggi TTD (mm)</label>
                                                    <input type="number" step="1" min="8" max="60" class="w-full text-xs rounded-lg border-slate-300 px-2.5 py-1 font-mono focus:border-violet-500 focus:ring-violet-500 bg-white"
                                                           :value="formatCoord(placements[step.jabatan_id].tinggi)"
                                                           @input="placements[step.jabatan_id].tinggi = cleanNumber($event.target.value)">
                                                </div>
                                            </div>

                                            {{-- Options Checkboxes --}}
                                            <div class="space-y-1.5 pt-2 border-t border-slate-200/80">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" class="rounded text-violet-600 w-3.5 h-3.5 focus:ring-violet-500"
                                                           x-model="placements[step.jabatan_id].tampilkan_nama">
                                                    <span class="font-medium text-slate-700 text-[10px]">Tampilkan Nama Pejabat</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" class="rounded text-violet-600 w-3.5 h-3.5 focus:ring-violet-500"
                                                           x-model="placements[step.jabatan_id].tampilkan_jabatan">
                                                    <span class="font-medium text-slate-700 text-[10px]">Tampilkan Nama Jabatan</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" class="rounded text-violet-600 w-3.5 h-3.5 focus:ring-violet-500"
                                                           x-model="placements[step.jabatan_id].tampilkan_stempel">
                                                    <span class="font-medium text-slate-700 text-[10px]">Tampilkan Stempel</span>
                                                </label>
                                            </div>

                                            {{-- Actions on card --}}
                                            <div class="flex items-center justify-between pt-2 border-t border-slate-200/80 text-[10px]">
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click.stop="cutSignature(step.jabatan_id)" class="inline-flex items-center gap-1 font-semibold text-slate-700 hover:text-violet-700">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0L12 12zm0 0l-2.879-2.879a3 3 0 10-4.242 4.242 3 3 0 004.242 0L12 12z" /></svg>
                                                        Potong
                                                    </button>
                                                    <span class="text-slate-300">•</span>
                                                    <button type="button" @click.stop="copySignature(step.jabatan_id)" class="inline-flex items-center gap-1 font-semibold text-slate-700 hover:text-violet-700">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                                        Salin
                                                    </button>
                                                </div>
                                                <button type="button" @click.stop="deleteSignature(step.jabatan_id)" class="inline-flex items-center gap-1 font-semibold text-rose-600 hover:text-rose-800">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Unplaced State: Button to Place --}}
                                    <template x-if="!isPlaced(step.jabatan_id)">
                                        <div class="pt-2" @click.stop>
                                            <p class="text-[10px] text-amber-700 font-medium mb-2 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                Belum ditempatkan di dokumen
                                            </p>
                                            <button type="button" 
                                                    @click="placeToCurrentPage(step.jabatan_id)"
                                                    class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20 transition-all">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                <span>Tempatkan di Hal <span x-text="currentPage"></span></span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Keyboard Shortcuts Info Card --}}
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-[10px] text-slate-500">
                            <p class="font-bold text-slate-800 uppercase tracking-wider text-[9px] flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Pintasan Keyboard
                            </p>
                            <div class="grid grid-cols-2 gap-1.5 font-mono">
                                <div><kbd class="bg-white border rounded px-1 text-slate-800 shadow-2xs">Ctrl+X</kbd> Potong</div>
                                <div><kbd class="bg-white border rounded px-1 text-slate-800 shadow-2xs">Ctrl+C</kbd> Salin</div>
                                <div><kbd class="bg-white border rounded px-1 text-slate-800 shadow-2xs">Ctrl+V</kbd> Tempel</div>
                                <div><kbd class="bg-white border rounded px-1 text-slate-800 shadow-2xs">Del</kbd> Hapus</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
(function() {
    function registerSignatureEditor() {
        if (!window.Alpine) return;
        Alpine.data('signatureEditor', (config) => ({
            pages: config.pages || [],
            pageWidthMm: config.pageWidthMm || 210,
            pageHeightMm: config.pageHeightMm || 297,
            placements: JSON.parse(JSON.stringify(config.placements || {})),
            approvalSteps: (config.approvalSteps || []).filter(s => s.jabatan_id),
            jabatanList: config.jabatans || [],
            previewBaseUrl: config.previewBaseUrl,
            
            currentPage: 1,
            totalPages: (config.pages || []).length || 1,
            displayWidth: 600,
            displayHeight: 600,
            scale: 1,
            
            selectedJabatan: null,
            clipboard: null, // { action: 'cut'|'copy', jabatanId, data }
            toastMessage: '',
            toastType: 'info',
            toastTimer: null,

            isDragging: false,
            isResizing: false,
            dragJabatanId: null,
            dragStartX: 0,
            dragStartY: 0,
            dragStartPosX: 0,
            dragStartPosY: 0,
            dragStartWidth: 0,
            dragStartHeight: 0,

            init() {
                const aspectRatio = this.pageWidthMm / this.pageHeightMm;
                this.displayHeight = Math.min(720, window.innerHeight - 190);
                this.displayWidth = Math.round(this.displayHeight * aspectRatio);
                this.scale = this.displayWidth / this.pageWidthMm;

                // Mouse & Touch drag listeners
                document.addEventListener('mousemove', (e) => this.onDrag(e));
                document.addEventListener('mouseup', (e) => this.stopDrag(e));
                document.addEventListener('touchmove', (e) => this.onDrag(e), { passive: false });
                document.addEventListener('touchend', (e) => this.stopDrag(e));

                // Keyboard shortcuts listener
                document.addEventListener('keydown', (e) => this.handleKeyDown(e));
            },

            get placedCount() {
                return Object.values(this.placements).filter(p => p && p.halaman).length;
            },

            get currentPageUrl() {
                if (!this.pages || this.pages.length === 0) return '';
                const pageIndex = Math.min(this.currentPage - 1, this.pages.length - 1);
                return this.previewBaseUrl + '/' + this.pages[pageIndex];
            },

            get currentPagePlacements() {
                const result = {};
                for (const [jabatanId, placement] of Object.entries(this.placements)) {
                    if (placement && parseInt(placement.halaman || 1) === parseInt(this.currentPage)) {
                        result[jabatanId] = placement;
                    }
                }
                return result;
            },

            isPlaced(jabatanId) {
                return !!(this.placements[jabatanId] && this.placements[jabatanId].halaman);
            },

            formatCoord(val) {
                if (val === undefined || val === null || isNaN(val)) return 0;
                return parseFloat(Number(val).toFixed(1));
            },

            cleanNumber(val) {
                const n = parseFloat(val);
                return isNaN(n) ? 0 : parseFloat(n.toFixed(1));
            },

            showToast(message, type = 'info') {
                this.toastMessage = message;
                this.toastType = type;
                if (this.toastTimer) clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => {
                    this.toastMessage = '';
                }, 3500);
            },

            onImageLoad() {},

            mmToPixel(mm) {
                return Math.round((mm || 0) * this.scale);
            },

            pixelToMm(px) {
                return parseFloat(((px || 0) / this.scale).toFixed(1));
            },

            getJabatanNama(jabatanId) {
                const j = this.jabatanList.find(j => j.id == jabatanId);
                return j ? j.nama : 'Jabatan #' + jabatanId;
            },

            prevPage() {
                if (this.currentPage > 1) this.changeCurrentPage(this.currentPage - 1);
            },

            nextPage() {
                if (this.currentPage < this.totalPages) this.changeCurrentPage(this.currentPage + 1);
            },

            changeCurrentPage(page) {
                this.currentPage = parseInt(page);
            },

            selectAndFocus(jabatanId) {
                this.selectedJabatan = jabatanId;
                if (this.isPlaced(jabatanId)) {
                    const targetPage = parseInt(this.placements[jabatanId].halaman || 1);
                    if (this.currentPage !== targetPage) {
                        this.currentPage = targetPage;
                    }
                }
            },

            cutSignature(jabatanId) {
                if (!jabatanId || !this.placements[jabatanId]) return;
                const name = this.getJabatanNama(jabatanId);
                this.clipboard = {
                    action: 'cut',
                    jabatanId: jabatanId,
                    data: JSON.parse(JSON.stringify(this.placements[jabatanId]))
                };
                delete this.placements[jabatanId];
                if (this.selectedJabatan == jabatanId) this.selectedJabatan = null;
                this.showToast(`✂️ Tanda tangan ${name} dipotong. Buka halaman tujuan lalu klik Tempel (Ctrl+V).`, 'info');
            },

            copySignature(jabatanId) {
                if (!jabatanId || !this.placements[jabatanId]) return;
                const name = this.getJabatanNama(jabatanId);
                this.clipboard = {
                    action: 'copy',
                    jabatanId: jabatanId,
                    data: JSON.parse(JSON.stringify(this.placements[jabatanId]))
                };
                this.showToast(`📋 Tanda tangan ${name} disalin. Buka halaman tujuan lalu klik Tempel (Ctrl+V).`, 'info');
            },

            pasteSignature() {
                if (!this.clipboard || !this.clipboard.jabatanId) {
                    this.showToast('Tidak ada tanda tangan di clipboard untuk ditempel.', 'warning');
                    return;
                }

                const jabatanId = this.clipboard.jabatanId;
                const name = this.getJabatanNama(jabatanId);
                const w = this.clipboard.data.lebar || 40;
                const h = this.clipboard.data.tinggi || 20;

                // Center placement on current page
                const posX = Math.max(10, Math.min(this.pageWidthMm - w - 10, (this.pageWidthMm - w) / 2));
                const posY = Math.max(10, Math.min(this.pageHeightMm - h - 10, this.pageHeightMm / 2));

                this.placements[jabatanId] = {
                    ...this.clipboard.data,
                    pos_x: parseFloat(posX.toFixed(1)),
                    pos_y: parseFloat(posY.toFixed(1)),
                    halaman: parseInt(this.currentPage)
                };

                this.selectedJabatan = jabatanId;
                this.showToast(`📥 Tanda tangan ${name} berhasil ditempatkan di Halaman ${this.currentPage}.`, 'success');

                if (this.clipboard.action === 'cut') {
                    this.clipboard = null;
                }
            },

            deleteSignature(jabatanId) {
                if (!jabatanId) return;
                const name = this.getJabatanNama(jabatanId);
                delete this.placements[jabatanId];
                if (this.selectedJabatan == jabatanId) this.selectedJabatan = null;
                this.showToast(`🗑️ Tanda tangan ${name} dihapus dari template.`, 'info');
            },

            moveToPage(jabatanId, targetPage) {
                if (!jabatanId || !this.placements[jabatanId]) return;
                const page = parseInt(targetPage);
                this.placements[jabatanId].halaman = page;
                this.currentPage = page;
                this.selectedJabatan = jabatanId;
                this.showToast(`📄 Tanda tangan dipindahkan ke Halaman ${page}.`, 'success');
            },

            placeToCurrentPage(jabatanId) {
                const w = 40;
                const h = 20;
                const posX = (this.pageWidthMm - w) / 2;
                const posY = this.pageHeightMm - 60;

                this.placements[jabatanId] = {
                    pos_x: parseFloat(posX.toFixed(1)),
                    pos_y: parseFloat(posY.toFixed(1)),
                    halaman: parseInt(this.currentPage),
                    lebar: w,
                    tinggi: h,
                    tampilkan_nama: true,
                    tampilkan_jabatan: true,
                    tampilkan_stempel: false
                };

                this.selectedJabatan = jabatanId;
                this.showToast(`✓ Tanda tangan ${this.getJabatanNama(jabatanId)} ditempatkan di Halaman ${this.currentPage}.`, 'success');
            },

            handleKeyDown(event) {
                // If focused on an input/textarea/select, let normal typing happen
                const tag = event.target.tagName.toLowerCase();
                if (['input', 'textarea', 'select'].includes(tag)) {
                    return;
                }

                const isCtrl = event.ctrlKey || event.metaKey;
                const key = event.key.toLowerCase();

                if (isCtrl && key === 'x' && this.selectedJabatan && this.isPlaced(this.selectedJabatan)) {
                    event.preventDefault();
                    this.cutSignature(this.selectedJabatan);
                } else if (isCtrl && key === 'c' && this.selectedJabatan && this.isPlaced(this.selectedJabatan)) {
                    event.preventDefault();
                    this.copySignature(this.selectedJabatan);
                } else if (isCtrl && key === 'v' && this.clipboard) {
                    event.preventDefault();
                    this.pasteSignature();
                } else if ((event.key === 'Delete' || event.key === 'Backspace') && this.selectedJabatan && this.isPlaced(this.selectedJabatan)) {
                    event.preventDefault();
                    this.deleteSignature(this.selectedJabatan);
                } else if (event.key === 'Escape') {
                    this.selectedJabatan = null;
                }
            },

            startDrag(event, jabatanId) {
                this.isDragging = true;
                this.dragJabatanId = jabatanId;
                this.selectedJabatan = jabatanId;
                
                const point = event.touches ? event.touches[0] : event;
                this.dragStartX = point.clientX;
                this.dragStartY = point.clientY;
                this.dragStartPosX = this.placements[jabatanId]?.pos_x || 0;
                this.dragStartPosY = this.placements[jabatanId]?.pos_y || 0;
            },

            startResize(event, jabatanId) {
                this.isResizing = true;
                this.dragJabatanId = jabatanId;
                this.selectedJabatan = jabatanId;
                
                const point = event.touches ? event.touches[0] : event;
                this.dragStartX = point.clientX;
                this.dragStartY = point.clientY;
                this.dragStartWidth = this.placements[jabatanId]?.lebar || 40;
                this.dragStartHeight = this.placements[jabatanId]?.tinggi || 20;
            },

            onDrag(event) {
                if (!this.isDragging && !this.isResizing) return;
                if (event.cancelable) event.preventDefault();

                const point = event.touches ? event.touches[0] : event;
                const deltaX = point.clientX - this.dragStartX;
                const deltaY = point.clientY - this.dragStartY;
                const deltaMmX = this.pixelToMm(deltaX);
                const deltaMmY = this.pixelToMm(deltaY);

                if (this.isDragging && this.placements[this.dragJabatanId]) {
                    let newX = this.dragStartPosX + deltaMmX;
                    let newY = this.dragStartPosY + deltaMmY;

                    newX = Math.max(0, Math.min(newX, this.pageWidthMm - (this.placements[this.dragJabatanId].lebar || 40)));
                    newY = Math.max(0, Math.min(newY, this.pageHeightMm - (this.placements[this.dragJabatanId].tinggi || 20)));

                    this.placements[this.dragJabatanId].pos_x = parseFloat(newX.toFixed(1));
                    this.placements[this.dragJabatanId].pos_y = parseFloat(newY.toFixed(1));
                }

                if (this.isResizing && this.placements[this.dragJabatanId]) {
                    let newW = this.dragStartWidth + deltaMmX;
                    let newH = this.dragStartHeight + deltaMmY;

                    newW = Math.max(15, Math.min(newW, this.pageWidthMm - (this.placements[this.dragJabatanId].pos_x || 0)));
                    newH = Math.max(8, Math.min(newH, 60));

                    this.placements[this.dragJabatanId].lebar = parseFloat(newW.toFixed(1));
                    this.placements[this.dragJabatanId].tinggi = parseFloat(newH.toFixed(1));
                }
            },

            stopDrag(event) {
                this.isDragging = false;
                this.isResizing = false;
                this.dragJabatanId = null;
            },

            saveAndClose() {
                @this.call('syncAllPlacements', this.placements);
                setTimeout(() => {
                    @this.call('closeSignatureEditor');
                }, 100);
            }
        }));
    }

    if (window.Alpine) {
        registerSignatureEditor();
    } else {
        document.addEventListener('alpine:init', registerSignatureEditor);
    }
})();
</script>
