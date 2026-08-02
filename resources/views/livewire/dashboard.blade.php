<div class="space-y-8">
    <!-- Welcome Header (Clean White Emerald Theme) -->
    <div class="p-6 lg:p-8 rounded-2xl bg-white border border-emerald-200/80 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-50 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 border border-emerald-200 inline-block mb-2">Portal Sistem Desa</span>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900">Selamat Datang, {{ $user->name }}!</h1>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">
                {{ $user->isAdmin() ? 'Administrator Utama Sistem Informasi Desa' : 'Layanan Mandiri Warga Desa' }}
            </p>
        </div>
        <div class="flex gap-2 relative z-10">
            @if(!$user->isAdmin())
                <a href="{{ route('warga.ajukan-surat') }}" wire:navigate class="px-5 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-700 text-white text-sm transition-all shadow-md shadow-emerald-600/20">
                    + Ajukan Surat Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Admin Stats Cards -->
    @if($user->isAdmin())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Desa</span>
                <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_desa'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Registrasi Pending</span>
                <p class="text-3xl font-extrabold text-amber-600">{{ $stats['registrasi_pending'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengajuan Menunggu</span>
                <p class="text-3xl font-extrabold text-sky-600">{{ $stats['pengajuan_pending'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengajuan Selesai</span>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $stats['pengajuan_selesai'] }}</p>
            </div>
        </div>
    @else
        <!-- Warga & Pejabat Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengajuan</span>
                <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_pengajuan'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</span>
                <p class="text-3xl font-extrabold text-sky-600">{{ $stats['pengajuan_aktif'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Perbaikan</span>
                <p class="text-3xl font-extrabold text-amber-600">{{ $stats['perlu_perbaikan'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Surat Selesai</span>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $stats['selesai'] }}</p>
            </div>
        </div>

        @if(isset($stats['pejabat_approval_pending']) && $stats['pejabat_approval_pending'] > 0)
            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500 text-white font-bold flex items-center justify-center text-lg">
                        {{ $stats['pejabat_approval_pending'] }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-amber-900">Permintaan Approval Surat Aktif</h3>
                        <p class="text-xs text-amber-800">Ada {{ $stats['pejabat_approval_pending'] }} pengajuan surat yang membutuhkan tindakan persetujuan Anda sebagai Pejabat.</p>
                    </div>
                </div>
                <a href="{{ route('pejabat.inbox-approval') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm transition-all shadow-md">
                    Buka Inbox Approval
                </a>
            </div>
        @endif
    @endif

    <!-- Recent Submissions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-6">
        <h3 class="text-lg font-extrabold text-slate-900">Pengajuan Surat Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-4 py-3">No. Pengajuan</th>
                        <th class="px-4 py-3">Jenis Surat</th>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentPengajuans as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">{{ $p->nomor_pengajuan }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $p->jenisSurat->nama }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $p->warga->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $p->status->badgeClass() }}">
                                    {{ $p->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $p->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('warga.detail-pengajuan', $p->id) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada pengajuan surat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
