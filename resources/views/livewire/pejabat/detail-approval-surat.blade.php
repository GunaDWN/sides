<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('pejabat.inbox-approval') }}" wire:navigate class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1 mb-1">&larr; Kembali ke Inbox</a>
            <h1 class="text-2xl font-bold text-slate-900">Proses Persetujuan Surat</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $approval->pengajuanSurat->nomor_pengajuan }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $approval->status->badgeClass() }}">{{ $approval->status->label() }}</span>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Pengajuan Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Detail Pengajuan</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-xs text-slate-400 block">Jenis Surat</span><span class="font-bold text-slate-900">{{ $approval->pengajuanSurat->jenisSurat->nama }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Pemohon</span><span class="font-bold text-slate-900">{{ $approval->pengajuanSurat->warga->nama }}</span></div>
                    <div><span class="text-xs text-slate-400 block">NIK Pemohon</span><span class="font-mono text-slate-700">{{ $approval->pengajuanSurat->warga->nik }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Desa</span><span class="text-slate-700">{{ $approval->pengajuanSurat->desa->nama }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Tanggal Pengajuan</span><span class="text-slate-700">{{ $approval->pengajuanSurat->submitted_at?->format('d M Y, H:i') ?? '-' }}</span></div>
                    <div><span class="text-xs text-slate-400 block">Jabatan Anda</span><span class="font-bold text-purple-700">{{ $approval->nama_jabatan_snapshot }} (Tahap {{ $approval->urutan }})</span></div>
                </div>
            </div>

            {{-- Latest Document --}}
            @if($approval->pengajuanSurat->latestDokumen)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Dokumen Terbaru (Versi {{ $approval->pengajuanSurat->latestDokumen->versi }})</h3>
                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm">
                            <p class="font-semibold text-slate-900">{{ $approval->pengajuanSurat->latestDokumen->nama_file_asli }}</p>
                            <p class="text-[10px] text-slate-400">{{ number_format($approval->pengajuanSurat->latestDokumen->file_size / 1024, 1) }} KB | {{ strtoupper($approval->pengajuanSurat->latestDokumen->file_extension) }}</p>
                        </div>
                        <a href="{{ route('download.dokumen', $approval->pengajuanSurat->latestDokumen->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Unduh Dokumen
                        </a>
                    </div>
                </div>
            @endif

            {{-- Approval Action Form --}}
            @if($approval->status->value === 'aktif')
                <div class="bg-white rounded-2xl border-2 border-sky-300 shadow-sm p-6 space-y-5">
                    <h3 class="text-sm font-bold text-sky-900 uppercase tracking-wider border-b border-sky-200 pb-2">Tindakan Persetujuan</h3>

                    <form wire:submit="processApproval" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $aksi === 'setuju' ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-200' : 'bg-white border-slate-200 hover:bg-emerald-50/50' }}">
                                <input type="radio" wire:model.live="aksi" value="setuju" class="sr-only">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <span class="text-xs font-bold text-emerald-800">Setujui</span>
                            </label>
                            <label class="cursor-pointer flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $aksi === 'perbaiki' ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-200' : 'bg-white border-slate-200 hover:bg-amber-50/50' }}">
                                <input type="radio" wire:model.live="aksi" value="perbaiki" class="sr-only">
                                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <span class="text-xs font-bold text-amber-800">Minta Perbaikan</span>
                            </label>
                            <label class="cursor-pointer flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $aksi === 'tolak' ? 'bg-rose-50 border-rose-400 ring-2 ring-rose-200' : 'bg-white border-slate-200 hover:bg-rose-50/50' }}">
                                <input type="radio" wire:model.live="aksi" value="tolak" class="sr-only">
                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <span class="text-xs font-bold text-rose-800">Tolak</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Komentar / Catatan
                                @if($aksi === 'tolak' || $aksi === 'perbaiki') <span class="text-rose-500">*</span> @endif
                            </label>
                            <textarea wire:model="komentar" rows="3" class="w-full text-sm rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="{{ $aksi === 'tolak' ? 'Tuliskan alasan penolakan...' : ($aksi === 'perbaiki' ? 'Tuliskan apa yang perlu diperbaiki...' : 'Komentar opsional...') }}"></textarea>
                            @error('komentar')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror
                        </div>

                        @error('aksi')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror

                        <button type="submit" {{ !$aksi ? 'disabled' : '' }} class="w-full py-3 rounded-xl font-bold text-white shadow-lg text-sm transition-all disabled:opacity-40 disabled:cursor-not-allowed
                            {{ $aksi === 'setuju' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' :
                               ($aksi === 'tolak' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20' :
                               ($aksi === 'perbaiki' ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/20' :
                               'bg-slate-400')) }}">
                            {{ $aksi === 'setuju' ? 'Konfirmasi Persetujuan' : ($aksi === 'tolak' ? 'Konfirmasi Penolakan' : ($aksi === 'perbaiki' ? 'Kirim Permintaan Perbaikan' : 'Pilih Tindakan di Atas')) }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar: Approval Timeline --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Seluruh Tahapan</h3>
                <div class="space-y-3">
                    @foreach($approval->pengajuanSurat->approvals as $step)
                        <div class="flex gap-3 {{ $step->id === $approval->id ? 'bg-sky-50 -mx-2 px-2 py-1.5 rounded-xl border border-sky-200' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm border flex-shrink-0
                                {{ $step->status->value === 'diterima' ? 'bg-emerald-500 text-white border-emerald-600' :
                                   ($step->status->value === 'ditolak' ? 'bg-rose-500 text-white border-rose-600' :
                                   ($step->status->value === 'aktif' ? 'bg-sky-500 text-white border-sky-600' :
                                   ($step->status->value === 'ulangi' ? 'bg-amber-500 text-white border-amber-600' :
                                   'bg-slate-200 text-slate-500 border-slate-300'))) }}">
                                {{ $step->urutan }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $step->nama_jabatan_snapshot }}</p>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $step->status->badgeClass() }}">{{ $step->status->label() }}</span>
                                @if($step->processed_at)<p class="text-[10px] text-slate-400 mt-0.5">{{ $step->processed_at->format('d/m/Y H:i') }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Log Aktivitas</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($approval->pengajuanSurat->logs as $log)
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
