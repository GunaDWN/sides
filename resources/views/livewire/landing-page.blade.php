<div class="space-y-16 pb-16 bg-slate-50">
    <!-- Hero Section (Clean White Centered Theme) -->
    <section class="relative bg-gradient-to-b from-white via-emerald-50/40 to-slate-50 text-slate-900 overflow-hidden py-20 lg:py-28 border-b border-slate-200/60">
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-emerald-100/50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold uppercase tracking-wider shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Informasi Desa & Kelurahan Modern
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900">
                Layanan Administrasi Desa <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Cepat, Transparan & Digital</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Ajukan surat keterangan desa, lacak alur persetujuan pejabat secara berurutan, dan dapatkan dokumen resmi digital langsung dari genggaman Anda.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="{{ route('registrasi-warga') }}" wire:navigate class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/25 transition-all text-center text-sm">
                    Daftar Akun Warga
                </a>
                <a href="{{ route('cek-status-registrasi') }}" wire:navigate class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 shadow-xs transition-all text-center text-sm">
                    Cek Status Pendaftaran
                </a>
            </div>

            <!-- Highlights badges -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 border-t border-slate-200/60 max-w-3xl mx-auto text-left">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">⚡</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Proses Berjenjang</p>
                        <p class="text-[11px] text-slate-500">Alur persetujuan pejabat otomatis</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">🔒</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Dokumen Valid</p>
                        <p class="text-[11px] text-slate-500">Stempel & TTD digital resmi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">📱</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Akses 24/7</p>
                        <p class="text-[11px] text-slate-500">Dapat diakses dari mana saja</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-extrabold text-slate-900">Layanan Surat Tersedia</h2>
            <p class="text-sm text-slate-600 max-w-xl mx-auto">Daftar berbagai pengajuan dokumen dan surat keterangan resmi yang dapat diajukan secara langsung.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($jenisSurats as $surat)
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center font-extrabold text-base shadow-xs">
                        {{ strtoupper(substr($surat->nama, 0, 2)) }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $surat->nama }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $surat->deskripsi ?? 'Pengajuan surat keterangan resmi.' }}</p>
                    <div class="pt-3 flex items-center justify-between text-xs border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Kategori: {{ $surat->kategori ?? 'Umum' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center p-8 bg-white rounded-2xl border border-slate-200/80 text-slate-500 text-xs">
                    Belum ada jenis surat yang tersedia.
                </div>
            @endforelse
        </div>
    </section>
</div>
