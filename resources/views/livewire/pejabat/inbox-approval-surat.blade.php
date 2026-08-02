<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900">Inbox Persetujuan Surat</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar pengajuan surat yang membutuhkan persetujuan Anda.</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nomor pengajuan atau jenis surat...">
        <div class="flex gap-2">
            <button wire:click="$set('statusFilter', 'aktif')" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $statusFilter === 'aktif' ? 'bg-sky-600 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200' }}">
                Menunggu Proses
            </button>
            <button wire:click="$set('statusFilter', '')" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $statusFilter === '' ? 'bg-slate-800 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200' }}">
                Semua
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">No. Pengajuan</th>
                        <th class="px-4 py-3">Jenis Surat</th>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="px-4 py-3">Jabatan (Anda)</th>
                        <th class="px-4 py-3">Tahap</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tgl Masuk</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($approvals as $ap)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $ap->status->value === 'aktif' ? 'bg-amber-50/50' : '' }}">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">{{ $ap->pengajuanSurat->nomor_pengajuan }}</td>
                            <td class="px-4 py-3 text-slate-900 font-medium">{{ $ap->pengajuanSurat->jenisSurat->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $ap->pengajuanSurat->warga->nama ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">{{ $ap->nama_jabatan_snapshot }}</span></td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $ap->urutan }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ap->status->badgeClass() }}">{{ $ap->status->label() }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $ap->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('pejabat.detail-approval', $ap->id) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200 transition-all">
                                    {{ $ap->status->value === 'aktif' ? 'Proses' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada pengajuan surat di inbox Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $approvals->links() }}</div>
    </div>
</div>
