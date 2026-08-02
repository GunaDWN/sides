<div class="max-w-4xl mx-auto space-y-6">
    @if($submitted)
        <div class="p-8 rounded-2xl bg-white border border-emerald-200 shadow-sm text-center space-y-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Pengajuan Surat Berhasil Dikirim!</h2>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 inline-block">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nomor Pengajuan</p>
                <p class="text-2xl font-mono font-bold text-emerald-600 select-all">{{ $nomorPengajuan }}</p>
            </div>
            <p class="text-sm text-slate-600 max-w-md mx-auto">Dokumen Anda sudah masuk antrean persetujuan pejabat desa. Anda dapat memantau status persetujuan secara berkesinambungan di riwayat pengajuan.</p>
            <a href="{{ route('warga.riwayat-pengajuan') }}" wire:navigate class="inline-block px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-md transition-all">Lihat Riwayat Pengajuan</a>
        </div>
    @else
        <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajukan Surat Baru</h1>
                <p class="text-xs text-slate-500 mt-1">Pilih pengajuan berbasis template resmi desa atau buat pengajuan surat kustom mandiri.</p>
            </div>

            <!-- Tab Mode Selector -->
            <div class="flex p-1 bg-slate-100 rounded-xl border border-slate-200/80 self-start md:self-auto">
                <button type="button" wire:click="switchMode('template')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $mode === 'template' ? 'bg-white text-emerald-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                    📄 Surat Template Official
                </button>
                <button type="button" wire:click="switchMode('custom')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $mode === 'custom' ? 'bg-white text-emerald-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                    ✨ Surat Kustom Mandiri
                </button>
            </div>
        </div>

        @if($mode === 'template')
            <!-- Form Template Official -->
            <form wire:submit="submit" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <!-- Reusable Livewire v4 Select Component with #[Modelable] -->
                <div>
                    <livewire:components.select 
                        label="Pilih Jenis Surat Official" 
                        required="true"
                        wire:model.live="jenis_surat_id" 
                        placeholder="-- Pilih Jenis Surat --"
                        :options="$jenisSurats"
                        optionValue="id"
                        optionLabel="nama"
                        :searchable="true"
                        key="select-template-surat"
                    />
                    @error('jenis_surat_id')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
                </div>

                @if($selectedSurat)
                    <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-emerald-900 text-sm">{{ $selectedSurat->nama }}</h3>
                                <p class="text-xs text-emerald-700 mt-0.5">{{ $selectedSurat->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                            <a href="{{ route('download.template', $selectedSurat->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs flex items-center gap-2 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Unduh Template
                            </a>
                        </div>

                        @if($selectedSurat->butuh_approval && $selectedSurat->approvals->count() > 0)
                            <div class="pt-3 border-t border-emerald-200/80">
                                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Urutan Persetujuan Pejabat Resmi:</span>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    @foreach($selectedSurat->approvals as $i => $app)
                                        <div class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-lg border border-emerald-200 shadow-2xs">
                                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center">{{ $app->urutan }}</span>
                                            <span class="text-xs font-semibold text-slate-800">{{ $app->jabatan->nama }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Dokumen Yang Sudah Diisi (.doc, .docx, .pdf — Max 10MB) <span class="text-rose-500">*</span></label>
                    <input type="file" wire:model="dokumen" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all">
                    @error('dokumen')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
                    <div wire:loading wire:target="dokumen" class="text-xs text-emerald-600 mt-1 font-semibold">Mengunggah dokumen...</div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan untuk Pejabat (Opsional)</label>
                    <textarea wire:model="catatan" rows="2" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tambahkan catatan khusus apabila ada..."></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 shadow-sm transition-all text-sm" {{ !$jenis_surat_id ? 'disabled' : '' }}>
                    Kirim Pengajuan Surat
                </button>
            </form>

        @else

            <!-- Form Surat Kustom Mandiri -->
            <form wire:submit="submit" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <span>ℹ️</span> Informasi Pengajuan Surat Kustom
                    </p>
                    <p class="text-amber-800 leading-relaxed">Gunakan fitur ini jika surat yang Anda butuhkan tidak tersedia di daftar jenis surat template official. Anda melampirkan berkas sendiri dan menentukan sendiri alur pejabat yang akan memproses persetujuan.</p>
                </div>

                <!-- Perihal Surat -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perihal / Judul Surat Kustom <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="perihalSurat" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: Surat Keterangan Usaha Mikro, Surat Rekomendasi Bebas Banjir">
                    @error('perihalSurat')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- File Dokumen -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Berkas / Dokumen Surat (.doc, .docx, .pdf — Max 10MB) <span class="text-rose-500">*</span></label>
                    <input type="file" wire:model="dokumenCustom" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all">
                    @error('dokumenCustom')<span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>@enderror
                    <div wire:loading wire:target="dokumenCustom" class="text-xs text-emerald-600 mt-1 font-semibold">Mengunggah dokumen...</div>
                </div>

                <!-- Builder Alur Persetujuan Pejabat -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-semibold text-slate-700">Tentukan Alur Persetujuan Pejabat Desa <span class="text-rose-500">*</span></label>
                    
                    <div class="flex gap-2 items-start">
                        <!-- Reusable Livewire v4 Select Component with #[Modelable] -->
                        <div class="flex-1">
                            <livewire:components.select 
                                wire:model="selectedJabatanId" 
                                placeholder="-- Pilih Pejabat / Jabatan --"
                                :options="$availableJabatans"
                                optionValue="id"
                                optionLabel="nama"
                                :searchable="true"
                                key="select-pejabat-custom"
                            />
                        </div>

                        <button type="button" wire:click="addApprovalStep" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0">
                            <span>+</span> Tambah Tahap
                        </button>
                    </div>
                    @error('selectedJabatanId')<span class="text-xs text-rose-500 block">{{ $message }}</span>@enderror
                    @error('approvalSteps')<span class="text-xs text-rose-500 block">{{ $message }}</span>@enderror

                    <!-- Dynamic List Steps -->
                    @if(count($approvalSteps) > 0)
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 space-y-3">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Alur Persetujuan Yang Dikonfigurasi:</div>
                            
                            <div class="space-y-2">
                                @foreach($approvalSteps as $index => $jId)
                                    @php
                                        $jabatanObj = $availableJabatans->firstWhere('id', $jId);
                                    @endphp
                                    <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <span class="font-bold text-slate-800 text-xs block">{{ $jabatanObj?->nama ?? 'Jabatan' }}</span>
                                                <span class="text-[10px] text-slate-500 block">Tahap {{ $index + 1 }} Persetujuan</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <button type="button" wire:click="moveStepUp({{ $index }})" {{ $index === 0 ? 'disabled' : '' }} class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 disabled:opacity-30">
                                                ▲
                                            </button>
                                            <button type="button" wire:click="moveStepDown({{ $index }})" {{ $index === count($approvalSteps) - 1 ? 'disabled' : '' }} class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 disabled:opacity-30">
                                                ▼
                                            </button>
                                            <button type="button" wire:click="removeApprovalStep({{ $index }})" class="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 font-bold ml-1 text-xs">
                                                ✕ Hapus
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl border border-dashed border-slate-300 text-center text-xs text-slate-500">
                            Belum ada pejabat yang ditambahkan. Silakan pilih pejabat di atas dan klik <strong>"Tambah Tahap"</strong>.
                        </div>
                    @endif
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Pejabat (Opsional)</label>
                    <textarea wire:model="catatanCustom" rows="2" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tuliskan keterangan tambahan mengenai pengajuan kustom ini..."></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm transition-all text-sm">
                    Kirim Pengajuan Surat Kustom
                </button>
            </form>
        @endif
    @endif
</div>
