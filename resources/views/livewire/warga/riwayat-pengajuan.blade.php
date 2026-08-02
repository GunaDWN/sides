<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900">Riwayat Pengajuan Surat</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar seluruh pengajuan surat yang pernah Anda ajukan.</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nomor pengajuan atau jenis surat...">
        <select wire:model.live="statusFilter" class="text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="diajukan">Diajukan</option>
            <option value="menunggu_approval">Menunggu Approval</option>
            <option value="perlu_perbaikan">Perlu Perbaikan</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">No. Pengajuan</th>
                        <th class="px-4 py-3">Jenis Surat</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tahapan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pengajuans as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">{{ $p->nomor_pengajuan }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $p->jenisSurat->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $p->status->badgeClass() }}">{{ $p->status->label() }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">Tahap {{ $p->tahapan_aktif }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $p->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('warga.detail-pengajuan', $p->id) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200 transition-all">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">Anda belum pernah mengajukan surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $pengajuans->links() }}</div>
    </div>
</div>
