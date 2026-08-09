<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Data Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh warga terdaftar, riwayat jabatan, dan pengaturan data kependudukan.</p>
        </div>
        <button wire:click="openWargaModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
            + Tambah Data Warga
        </button>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nama atau NIK warga...">
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Jabatan Aktif</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wargas as $w)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $w->nik }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $w->nama }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $w->desa->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $w->jenis_warga === 'warga_dengan_jabatan' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $w->jenis_warga === 'warga_dengan_jabatan' ? 'Pejabat' : 'Biasa' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($w->activeWargaJabatan && $w->activeWargaJabatan->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($w->activeWargaJabatan as $wj)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $wj->jabatan->nama }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $w->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ ucfirst($w->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button wire:click="openWargaModal({{ $w->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Edit</button>
                                <button wire:click="openJabatanModal({{ $w->id }})" class="px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold border border-purple-200">+ Jabatan</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada data warga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $wargas->links() }}</div>
    </div>

    {{-- Warga Modal --}}
    @if($showWargaModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">{{ $warga_id ? 'Ubah Data Warga' : 'Tambah Warga Baru' }}</h3>
                <form wire:submit="saveWarga" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">NIK <span class="text-rose-500">*</span></label><input type="text" wire:model="nik" maxlength="16" class="w-full text-sm rounded-lg border-slate-300">@error('nik')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Nama <span class="text-rose-500">*</span></label><input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300">@error('nama')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-select 
                                wire:model="desa_id" 
                                label="Desa" 
                                placeholder="-- Pilih Desa --"
                                :options="$desasList"
                                :searchable="true"
                            />
                        </div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label><input type="text" wire:model="tempat_lahir" class="w-full text-sm rounded-lg border-slate-300"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label><input type="date" wire:model="tanggal_lahir" class="w-full text-sm rounded-lg border-slate-300"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-select 
                                wire:model="jenis_kelamin" 
                                label="Jenis Kelamin" 
                                placeholder="-- Pilih --"
                                :options="[
                                    ['id' => 'L', 'nama' => 'Laki-laki'],
                                    ['id' => 'P', 'nama' => 'Perempuan']
                                ]"
                                :searchable="false"
                            />
                        </div>
                        <div><label class="block font-semibold text-slate-700 mb-1">RT</label><input type="text" wire:model="rt" class="w-full text-sm rounded-lg border-slate-300"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1">RW</label><input type="text" wire:model="rw" class="w-full text-sm rounded-lg border-slate-300"></div>
                    </div>
                    <div><label class="block font-semibold text-slate-700 mb-1">Alamat</label><textarea wire:model="alamat" rows="2" class="w-full text-sm rounded-lg border-slate-300"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Telepon</label><input type="text" wire:model="telepon" class="w-full text-sm rounded-lg border-slate-300"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Email</label><input type="email" wire:model="email" class="w-full text-sm rounded-lg border-slate-300"></div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showWargaModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">Simpan Data Warga</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Jabatan Assignment Modal --}}
    @if($showJabatanModal && $selectedWargaForJabatan)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Tetapkan Jabatan: {{ $selectedWargaForJabatan->nama }}</h3>

                @if($selectedWargaForJabatan->wargaJabatans->count() > 0)
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-700 uppercase">Riwayat Jabatan</span>
                        @foreach($selectedWargaForJabatan->wargaJabatans as $wj)
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-xs flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $wj->jabatan->nama }}</span>
                                    <span class="text-slate-500 ml-1">({{ $wj->tanggal_mulai->format('d/m/Y') }} - {{ $wj->tanggal_selesai?->format('d/m/Y') ?? 'Sekarang' }})</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $wj->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($wj->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form wire:submit="saveJabatanWarga" class="space-y-4 text-xs">
                    <div>
                        <x-select 
                            wire:model="assign_jabatan_id" 
                            label="Pilih Jabatan" 
                            placeholder="-- Pilih Jabatan --"
                            :options="$availableJabatans"
                            :searchable="true"
                            required
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label><input type="date" wire:model="tanggal_mulai" class="w-full text-sm rounded-lg border-slate-300">@error('tanggal_mulai')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label><input type="date" wire:model="tanggal_selesai" class="w-full text-sm rounded-lg border-slate-300">@error('tanggal_selesai')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                    </div>
                    <div><label class="block font-semibold text-slate-700 mb-1">Nomor SK</label><input type="text" wire:model="nomor_sk" class="w-full text-sm rounded-lg border-slate-300"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Tanda Tangan (Max 2MB)</label><input type="file" wire:model="tanda_tangan" class="w-full text-xs"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Stempel (Max 2MB)</label><input type="file" wire:model="stempel" class="w-full text-xs"></div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showJabatanModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-md">Tetapkan Jabatan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
