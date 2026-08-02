<div class="max-w-3xl mx-auto space-y-6">
    @if($submitted)
        <div class="p-8 rounded-2xl bg-white border border-emerald-200 shadow-xl text-center space-y-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Pengajuan Surat Berhasil!</h2>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 inline-block">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nomor Pengajuan</p>
                <p class="text-2xl font-mono font-bold text-emerald-600 select-all">{{ $nomorPengajuan }}</p>
            </div>
            <p class="text-sm text-slate-600">Dokumen Anda sudah masuk antrean persetujuan pejabat desa. Pantau statusnya di halaman Riwayat Pengajuan.</p>
            <a href="{{ route('warga.riwayat-pengajuan') }}" wire:navigate class="inline-block px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md">Lihat Riwayat Pengajuan</a>
        </div>
    @else
        <div class="border-b border-slate-200 pb-4">
            <h1 class="text-2xl font-bold text-slate-900">Ajukan Surat Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Pilih jenis surat, unduh template, isi dokumen di perangkat Anda, lalu unggah kembali untuk diajukan.</p>
        </div>

        <form wire:submit="submit" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Jenis Surat <span class="text-rose-500">*</span></label>
                <select wire:model.live="jenis_surat_id" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Pilih Jenis Surat --</option>
                    @foreach($jenisSurats as $js)
                        <option value="{{ $js->id }}">{{ $js->nama }} ({{ $js->kategori ?? 'Umum' }})</option>
                    @endforeach
                </select>
                @error('jenis_surat_id')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror
            </div>

            @if($selectedSurat)
                <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-sky-900">{{ $selectedSurat->nama }}</h3>
                            <p class="text-xs text-sky-700">{{ $selectedSurat->deskripsi ?? '-' }}</p>
                        </div>
                        <a href="{{ route('download.template', $selectedSurat->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Unduh Template
                        </a>
                    </div>

                    @if($selectedSurat->butuh_approval && $selectedSurat->approvals->count() > 0)
                        <div class="pt-2 border-t border-sky-200">
                            <span class="text-[10px] font-bold text-sky-800 uppercase tracking-wider">Urutan Persetujuan Pejabat:</span>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @foreach($selectedSurat->approvals as $i => $app)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-sky-600 text-white text-[10px] font-bold flex items-center justify-center">{{ $app->urutan }}</span>
                                        <span class="text-xs font-semibold text-sky-900">{{ $app->jabatan->nama }}</span>
                                    </div>
                                    @if(!$loop->last)
                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Dokumen Yang Sudah Diisi (.doc, .docx, .pdf — Max 10MB) <span class="text-rose-500">*</span></label>
                <input type="file" wire:model="dokumen" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                @error('dokumen')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror
                <div wire:loading wire:target="dokumen" class="text-xs text-emerald-600 mt-1">Mengunggah dokumen...</div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan untuk Petugas (Opsional)</label>
                <textarea wire:model="catatan" rows="2" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Catatan tambahan..."></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 shadow-lg shadow-emerald-600/20 transition-all text-sm" {{ !$jenis_surat_id ? 'disabled' : '' }}>
                Kirim Pengajuan Surat
            </button>
        </form>
    @endif
</div>
