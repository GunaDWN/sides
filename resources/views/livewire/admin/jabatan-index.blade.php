<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Master Data Jabatan</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola daftar master struktur jabatan desa yang berdiri sendiri dan dapat dipakai lintas desa.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.hak-akses') }}" wire:navigate class="px-4 py-2 rounded-xl font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-xs transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Kelola Hak Akses
            </a>
            <button wire:click="openModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
                + Tambah Jabatan Baru
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-80 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nama atau kode jabatan...">
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Nama Jabatan</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jabatans as $j)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-500 text-xs">#{{ $j->urutan }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $j->nama }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $j->kode }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600 max-w-xs truncate">{{ $j->deskripsi ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $j->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $j->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.hak-akses', ['jabatan_id' => $j->id]) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200 transition-all">
                                    Hak Akses
                                </a>
                                <button wire:click="openModal({{ $j->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada data master jabatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Master Jabatan -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">
                    {{ $jabatan_id ? 'Ubah Master Jabatan' : 'Tambah Master Jabatan Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: Sekretaris Desa">
                        @error('nama') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Jabatan</label>
                            <input type="text" wire:model="kode" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="SEKDES">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Urutan <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="urutan" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="3" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tugas pokok dan fungsi singkat..."></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="jabatan_active_cb" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <label for="jabatan_active_cb" class="font-semibold text-slate-700">Jabatan Aktif</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 transition-all">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition-all">Simpan Jabatan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
