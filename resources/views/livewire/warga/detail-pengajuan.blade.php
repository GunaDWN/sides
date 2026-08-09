<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('warga.riwayat-pengajuan') }}" wire:navigate class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1 mb-1">&larr; Kembali ke Riwayat</a>
            <h1 class="text-2xl font-bold text-slate-900">Detail Pengajuan Surat</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $pengajuan->nomor_pengajuan }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $pengajuan->status->badgeClass() }}">{{ $pengajuan->status->label() }}</span>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Informasi Pengajuan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Jenis / Perihal Surat</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @if($pengajuan->is_custom)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Kustom</span>
                            @endif
                            <span class="font-bold text-slate-900">{{ $pengajuan->getDisplayName() }}</span>
                        </div>
                    </div>
                    <div><span class="text-xs text-slate-400 block">Kategori</span><span class="text-slate-700">{{ $pengajuan->jenisSurat->kategori ?? ($pengajuan->is_custom ? 'Pengajuan Mandiri' : '-') }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Tanggal Pengajuan</span><span class="text-slate-700">{{ $pengajuan->submitted_at?->format('d M Y, H:i') ?? $pengajuan->created_at->format('d M Y, H:i') }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Tahapan Aktif</span><span class="font-semibold text-slate-800">Tahap {{ $pengajuan->tahapan_aktif }} dari {{ $pengajuan->approvals->count() }}</span></div>
                </div>
                @if($pengajuan->catatan_pemohon)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs"><span class="font-semibold text-slate-700 block">Catatan Pemohon:</span><p class="text-slate-600 mt-0.5">{{ $pengajuan->catatan_pemohon }}</p></div>
                @endif
            </div>

            {{-- Approval Timeline Stepper --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Timeline Persetujuan Pejabat</h3>
                <div class="space-y-4">
                    @foreach($pengajuan->approvals as $approval)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm border-2
                                    {{ $approval->status->value === 'diterima' ? 'bg-emerald-500 text-white border-emerald-600' :
                                       ($approval->status->value === 'ditolak' ? 'bg-rose-500 text-white border-rose-600' :
                                       ($approval->status->value === 'aktif' ? 'bg-sky-500 text-white border-sky-600 animate-pulse' :
                                       ($approval->status->value === 'ulangi' ? 'bg-amber-500 text-white border-amber-600' :
                                       'bg-slate-200 text-slate-500 border-slate-300'))) }}">
                                    {{ $approval->urutan }}
                                </div>
                                @if(!$loop->last)<div class="w-0.5 flex-1 bg-slate-200 my-1"></div>@endif
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $approval->nama_jabatan_snapshot }}</h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $approval->status->badgeClass() }}">{{ $approval->status->label() }}</span>
                                </div>
                                @if($approval->nama_pejabat_snapshot)
                                    <p class="text-xs text-slate-500 mt-0.5">Diproses oleh: {{ $approval->nama_pejabat_snapshot }}</p>
                                @endif
                                @if($approval->processed_at)
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $approval->processed_at->format('d M Y, H:i') }}</p>
                                @endif
                                @if($approval->komentar)
                                    <div class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 italic">"{{ $approval->komentar }}"</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Resubmit Form (if perlu_perbaikan) --}}
            @if($pengajuan->status === \App\Enums\StatusPengajuan::PERLU_PERBAIKAN && auth()->user()->warga_id === $pengajuan->warga_id)
                <div class="bg-white rounded-2xl border-2 border-amber-300 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-amber-900 uppercase tracking-wider border-b border-amber-200 pb-2">⚠️ Dokumen Perlu Diperbaiki</h3>
                    <p class="text-xs text-amber-800">Pejabat meminta Anda mengunggah ulang dokumen yang sudah diperbaiki. Unduh dokumen terbaru, perbaiki sesuai catatan, lalu unggah kembali.</p>

                    <form wire:submit="resubmit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Dokumen Perbaikan (.doc, .docx, .pdf — Max 10MB) <span class="text-rose-500">*</span></label>
                            <input type="file" wire:model="dokumenPerbaikan" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                            @error('dokumenPerbaikan')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror
                            <div wire:loading wire:target="dokumenPerbaikan" class="text-xs text-amber-600 mt-1">Mengunggah...</div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Perbaikan (Opsional)</label>
                            <textarea wire:model="catatanPerbaikan" rows="2" class="w-full text-sm rounded-xl border-slate-300"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md text-sm">Kirim Ulang Dokumen Perbaikan</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar: Documents & Logs --}}
        <div class="space-y-6">
            {{-- Document Versions --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Riwayat Versi Dokumen</h3>
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($pengajuan->dokumens as $doc)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1 {{ $doc->is_latest ? 'ring-2 ring-emerald-300' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">Versi {{ $doc->versi }}</span>
                                @if($doc->is_latest)<span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold">TERBARU</span>@endif
                            </div>
                            <p class="text-slate-500 truncate">{{ $doc->nama_file_asli }}</p>
                            <p class="text-[10px] text-slate-400">{{ ucfirst($doc->sumber) }} • {{ $doc->uploadedBy->name ?? '-' }} • {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('download.dokumen', $doc->id) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 font-semibold mt-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Unduh
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Log Aktivitas</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($pengajuan->logs as $log)
                        <div class="text-xs border-l-2 border-slate-300 pl-3 py-1 space-y-0.5">
                            <p class="font-semibold text-slate-800">{{ $log->action }}</p>
                            <p class="text-slate-500">{{ $log->komentar ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $log->created_at->format('d/m/Y H:i') }} | {{ $log->user->name ?? 'Sistem' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
