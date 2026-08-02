<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900">Log Aktivitas & Audit Trail</h1>
        <p class="text-xs text-slate-500 mt-1">Riwayat seluruh aksi registrasi akun dan pengajuan surat yang tercatat dalam sistem.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex gap-2">
            <button wire:click="switchTab('registrasi')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tabActive === 'registrasi' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Log Registrasi Akun
            </button>
            <button wire:click="switchTab('pengajuan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tabActive === 'pengajuan' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Log Pengajuan Surat
            </button>
        </div>
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full sm:w-64 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari aksi atau catatan...">
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Referensi</th>
                        <th class="px-4 py-3">Pelaku</th>
                        <th class="px-4 py-3">Status Sebelum</th>
                        <th class="px-4 py-3">Status Sesudah</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors text-xs">
                            <td class="px-4 py-3 text-slate-500 font-mono whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                @if($tabActive === 'registrasi')
                                    {{ $log->registrasiAkun->kode_registrasi ?? '-' }}
                                @else
                                    {{ $log->pengajuanSurat->nomor_pengajuan ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold">{{ $log->status_sebelum ?? '-' }}</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold">{{ $log->status_sesudah ?? '-' }}</span></td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $tabActive === 'registrasi' ? ($log->catatan ?? '-') : ($log->komentar ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada log aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
    </div>
</div>
