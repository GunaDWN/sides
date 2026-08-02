<div class="max-w-3xl mx-auto px-4 py-12">
    @if(!$hasAnyDesa)
        <div class="p-8 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-center space-y-4 shadow-sm">
            <svg class="w-12 h-12 text-amber-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-xl font-bold">Registrasi Belum Tersedia</h2>
            <p class="text-sm text-amber-800">Registrasi belum tersedia karena data desa belum dikonfigurasi oleh administrator.</p>
        </div>
    @elseif($registrationComplete)
        <div class="p-8 rounded-2xl bg-white border border-emerald-200 shadow-xl text-center space-y-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Pendaftaran Berhasil Dikirim!</h2>
                <p class="text-sm text-slate-600">Pengajuan registrasi akun Anda telah tercatat dan sedang dalam antrean pemeriksaan petugas/admin desa.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 inline-block text-left space-y-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Registrasi Anda</p>
                <p class="text-2xl font-mono font-bold text-emerald-600 select-all">{{ $registrationCode }}</p>
                <p class="text-xs text-slate-400">Simpan kode registrasi ini untuk mengecek status persetujuan.</p>
            </div>
            <div class="pt-4 flex justify-center gap-4">
                <a href="{{ route('cek-status-registrasi') }}" wire:navigate class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all shadow-md">
                    Cek Status Pendaftaran
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="p-6 bg-slate-900 text-white border-b border-slate-800">
                <h2 class="text-xl font-bold">Form Registrasi Akun Warga</h2>
                <p class="text-xs text-slate-400 mt-1">Isi identitas diri Anda dengan benar sesuai dokumen KTP.</p>
            </div>

            <form wire:submit="register" class="p-6 space-y-6">
                <!-- Select Wilayah -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">1. Pilihan Desa & Wilayah</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-select 
                                wire:model.live="provinsi" 
                                label="Provinsi" 
                                placeholder="-- Pilih Provinsi --"
                                :options="collect($provinsiList)->map(fn($p) => ['id' => $p, 'nama' => $p])->all()"
                                :searchable="true"
                                required
                            />
                        </div>

                        <div>
                            <x-select 
                                wire:model.live="kabupaten" 
                                label="Kabupaten/Kota" 
                                placeholder="-- Pilih Kab/Kota --"
                                :options="collect($kabupatenList)->map(fn($k) => ['id' => $k, 'nama' => $k])->all()"
                                :searchable="true"
                                required
                            />
                        </div>

                        <div>
                            <x-select 
                                wire:model.live="kecamatan" 
                                label="Kecamatan" 
                                placeholder="-- Pilih Kecamatan --"
                                :options="collect($kecamatanList)->map(fn($k) => ['id' => $k, 'nama' => $k])->all()"
                                :searchable="true"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <x-select 
                            wire:model.live="desa_id" 
                            label="Desa/Kelurahan Tujuan" 
                            placeholder="-- Pilih Desa/Kelurahan --"
                            :options="$desasList"
                            :searchable="true"
                            required
                        />

                        @if($kecamatan && $desasList->isEmpty())
                            <div class="mt-2 p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700">
                                Desa atau kelurahan Anda belum tersedia dalam sistem. Silakan hubungi pemerintah desa atau administrator terkait.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Identitas Warga & Upload KTP -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">2. Identitas Diri & Berkas KTP</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nama_lengkap" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Sesuai KTP" {{ !$desa_id ? 'disabled' : '' }}>
                            @error('nama_lengkap') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">NIK (16 Digit) <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nik" maxlength="16" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="16 digit NIK" {{ !$desa_id ? 'disabled' : '' }}>
                            @error('nik') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Aktif <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="email@domain.com" {{ !$desa_id ? 'disabled' : '' }}>
                        @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Dokumen KTP (JPG, PNG, WEBP, PDF - Max 5MB) <span class="text-rose-500">*</span></label>
                        <input type="file" wire:model="ktp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" {{ !$desa_id ? 'disabled' : '' }}>
                        @error('ktp') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="ktp" class="text-xs text-emerald-600 mt-1">Mengunggah file KTP...</div>
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">3. Kata Sandi Akun</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="password" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Minimal 8 karakter" {{ !$desa_id ? 'disabled' : '' }}>
                            @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="password_confirmation" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ulangi password" {{ !$desa_id ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 space-y-4">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="setuju_pernyataan" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mt-0.5" {{ !$desa_id ? 'disabled' : '' }}>
                        <span class="text-xs text-slate-600">Saya menyatakan dengan sesungguhnya bahwa seluruh data dan dokumen KTP yang saya serahkan adalah benar dan sah.</span>
                    </label>
                    @error('setuju_pernyataan') <span class="block text-xs text-rose-500">{{ $message }}</span> @enderror

                    <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-600/20 transition-all text-sm" {{ !$desa_id ? 'disabled' : '' }}>
                        Kirim Pendaftaran Akun
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
