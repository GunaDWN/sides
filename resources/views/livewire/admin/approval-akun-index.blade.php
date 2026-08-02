<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengajuan Registrasi Akun</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar pendaftaran akun warga yang membutuhkan verifikasi dan keputusan persetujuan.</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari kode, nama, NIK, email...">
        </div>
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            @if($user->isAdmin())
                <select wire:model.live="desaFilter" class="text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Desa</option>
                    @foreach($desasList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                    @endforeach
                </select>
            @endif

            <select wire:model.live="statusFilter" class="text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="menunggu_approval">Menunggu Approval</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Kode Registrasi</th>
                        <th class="px-4 py-3">Nama Pemohon</th>
                        <th class="px-4 py-3">NIK (Tersamarkan)</th>
                        <th class="px-4 py-3">Email (Tersamarkan)</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrasis as $r)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">{{ $r->kode_registrasi }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $r->nama_lengkap }}</td>
                            <td class="px-4 py-3 font-mono text-slate-600">{{ $this->maskNik($r->nik) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $this->maskEmail($r->email) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $r->desa->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $r->status->badgeClass() }}">
                                    {{ $r->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $r->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.approval-akun-detail', $r->id) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition-all border border-emerald-200">
                                    Periksa Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada data pengajuan registrasi akun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $registrasis->links() }}
        </div>
    </div>
</div>
