<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Jabatan & Matriks Hak Akses</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar struktur jabatan desa dan konfigurasi permission fitur bagi pejabat berwenang.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
            + Tambah Jabatan Baru
        </button>
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
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Permission Diberikan</th>
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
                            <td class="px-4 py-3 text-slate-700">{{ $j->desa->nama }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($j->permissions as $p)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $p->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Tanpa permission khusus</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $j->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $j->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openModal({{ $j->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                    Atur Permission
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada data jabatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">
                    {{ $jabatan_id ? 'Ubah Jabatan & Hak Akses' : 'Tambah Jabatan Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Jabatan <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300" placeholder="Contoh: Sekretaris Desa">
                            @error('nama') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Desa <span class="text-rose-500">*</span></label>
                            <select wire:model="desa_id" class="w-full text-sm rounded-lg border-slate-300">
                                @foreach($desasList as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Jabatan</label>
                            <input type="text" wire:model="kode" class="w-full text-sm rounded-lg border-slate-300" placeholder="SEKDES">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Urutan Tampilan <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="urutan" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <!-- Permission Matrix Mapping -->
                    <div class="space-y-3 pt-2">
                        <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-1">Matriks Permission Jabatan</h4>

                        @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                                <span class="font-bold text-slate-800 text-xs block">{{ $groupName }}</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($perms as $p)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $p->id }}" class="rounded text-emerald-600">
                                            <span class="text-slate-700 text-xs">{{ $p->label }} <span class="text-[10px] text-slate-400">({{ $p->name }})</span></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="jabatan_active_cb" class="rounded text-emerald-600">
                        <label for="jabatan_active_cb" class="font-semibold text-slate-700">Jabatan Aktif</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">Simpan Jabatan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
