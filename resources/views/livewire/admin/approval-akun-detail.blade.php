<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.approval-akun') }}" wire:navigate class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1 mb-1">
                &larr; Kembali ke Daftar
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Detail Pengajuan Registrasi</h1>
            <p class="text-xs text-slate-500 font-mono">Kode Registrasi: {{ $registrasi->kode_registrasi }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $registrasi->status->badgeClass() }}">
            {{ $registrasi->status->label() }}
        </span>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Main Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Identitas Warga -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Identitas Pemohon</h3>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Lengkap</span>
                    <span class="font-bold text-slate-900">{{ $registrasi->nama_lengkap }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">NIK</span>
                    <span class="font-mono font-bold text-slate-900 select-all">{{ $registrasi->nik }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Email</span>
                    <span class="font-medium text-slate-800 select-all">{{ $registrasi->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Desa Tujuan</span>
                    <span class="font-medium text-slate-800">{{ $registrasi->desa->nama }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Kecamatan</span>
                    <span class="text-slate-700">{{ $registrasi->kecamatan }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Kabupaten & Provinsi</span>
                    <span class="text-slate-700">{{ $registrasi->kabupaten }}, {{ $registrasi->provinsi }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Waktu Registrasi</span>
                    <span class="text-slate-700">{{ $registrasi->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">IP Address</span>
                    <span class="text-slate-700 font-mono text-xs">{{ $registrasi->ip_address ?? '-' }}</span>
                </div>
            </div>

            <!-- KTP File Downloader -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between bg-slate-50 p-4 rounded-xl">
                <div>
                    <span class="text-xs font-semibold text-slate-700 block">Berkas KTP Lampiran</span>
                    <span class="text-xs text-slate-500">{{ $registrasi->ktp_nama_asli ?? 'KTP File' }} ({{ number_format(($registrasi->ktp_size ?? 0) / 1024, 1) }} KB)</span>
                </div>
                <a href="{{ route('download.ktp', $registrasi->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-all shadow-md inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh KTP
                </a>
            </div>
        </div>

        <!-- Right: Actions & History -->
        <div class="space-y-6">
            @if($registrasi->status === \App\Enums\StatusRegistrasi::MENUNGGU_APPROVAL)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Keputusan Verifikasi</h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea wire:model="catatan" rows="2" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Catatan persetujuan..."></textarea>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <button wire:click="$set('showApproveModal', true)" class="w-full py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all text-sm">
                            Setujui Pendaftaran
                        </button>
                        <button wire:click="$set('showRejectModal', true)" class="w-full py-2.5 rounded-xl font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all text-sm">
                            Tolak Pendaftaran
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3 text-sm">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Status Keputusan</h3>
                    <p><span class="text-xs text-slate-400 block">Diproses Oleh:</span> <span class="font-semibold text-slate-800">{{ $registrasi->diprosesOleh->name ?? '-' }}</span></p>
                    <p><span class="text-xs text-slate-400 block">Diproses Pada:</span> <span class="font-medium text-slate-700">{{ $registrasi->diproses_pada ? $registrasi->diproses_pada->format('d M Y, H:i') : '-' }}</span></p>
                    @if($registrasi->catatan_petugas)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                            <span class="font-semibold text-slate-700 block">Catatan Petugas:</span>
                            <p class="text-slate-600 italic mt-0.5">{{ $registrasi->catatan_petugas }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Audit Trail Log -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Riwayat Log Audit</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($registrasi->logs as $log)
                        <div class="text-xs border-l-2 border-slate-300 pl-3 py-1 space-y-0.5">
                            <p class="font-semibold text-slate-800">{{ $log->action }}</p>
                            <p class="text-slate-500">{{ $log->catatan }}</p>
                            <p class="text-[10px] text-slate-400">{{ $log->created_at->format('d/m/Y H:i') }} | {{ $log->user->name ?? 'Sistem' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <x-confirm-modal 
        :show="$showApproveModal"
        title="Setujui Pendaftaran Akun"
        description="Apakah Anda yakin ingin menyetujui registrasi akun ini? Data Warga dan Akun Pengguna akan otomatis diaktifkan di sistem."
        confirmText="Ya, Setujui Akun"
        cancelText="Batal"
        confirmAction="approve"
        cancelAction="$set('showApproveModal', false)"
        variant="emerald"
    />

    <!-- Reject Modal -->
    <x-confirm-modal 
        :show="$showRejectModal"
        title="Tolak Pendaftaran Akun"
        description="Berikan alasan penolakan yang jelas (alasan ini wajib dan akan ditampilkan kepada pemohon)."
        confirmText="Konfirmasi Tolak"
        cancelText="Batal"
        confirmAction="reject"
        cancelAction="$set('showRejectModal', false)"
        variant="rose"
    >
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
            <textarea wire:model="alasanPenolakan" rows="3" class="w-full text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500" placeholder="Contoh: KTP tidak terbaca / NIK tidak sesuai data kependudukan..."></textarea>
            @error('alasanPenolakan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </x-confirm-modal>
</div>
