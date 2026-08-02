<div class="max-w-2xl mx-auto px-4 py-12 space-y-8">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-6 bg-slate-900 text-white border-b border-slate-800">
            <h2 class="text-xl font-bold">Cek Status Pendaftaran Akun</h2>
            <p class="text-xs text-slate-400 mt-1">Masukkan Kode Registrasi dan NIK/Email yang Anda daftarkan.</p>
        </div>

        <form wire:submit="cari" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Registrasi <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="kode_registrasi" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 font-mono" placeholder="Contoh: REG-ABCD-20260802...">
                @error('kode_registrasi') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">NIK atau Email <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="identifier" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="NIK 16 digit atau email terdaftar">
                @error('identifier') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all text-sm">
                Cari Status Registrasi
            </button>
        </form>
    </div>

    @if($notFound)
        <div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-center space-y-2">
            <p class="font-bold">Pengajuan Tidak Ditemukan</p>
            <p class="text-xs">Kombinasi kode registrasi dan NIK/Email tidak cocok atau belum terdaftar.</p>
        </div>
    @endif

    @if($hasilPencarian)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs text-slate-500 font-medium">Kode Registrasi</span>
                    <h3 class="text-lg font-mono font-bold text-slate-900">{{ $hasilPencarian->kode_registrasi }}</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $hasilPencarian->status->badgeClass() }}">
                    {{ $hasilPencarian->status->label() }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Pemohon</span>
                    <span class="font-semibold text-slate-800">{{ $hasilPencarian->nama_lengkap }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Desa Tujuan</span>
                    <span class="font-semibold text-slate-800">{{ $hasilPencarian->desa->nama }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tanggal Registrasi</span>
                    <span class="font-medium text-slate-700">{{ $hasilPencarian->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Tanggal Keputusan</span>
                    <span class="font-medium text-slate-700">{{ $hasilPencarian->diproses_pada ? $hasilPencarian->diproses_pada->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>

            @if($hasilPencarian->catatan_petugas)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <span class="font-semibold text-slate-700 block">Catatan / Alasan Petugas:</span>
                    <p class="text-slate-600 italic">{{ $hasilPencarian->catatan_petugas }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
