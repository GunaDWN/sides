<div class="space-y-6">
    {{-- Global Loading Backdrop --}}
    <div wire:loading.flex wire:target="openWargaModal, openJabatanModal, editJabatan, deleteJabatan, saveJabatanWarga, saveWarga" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-[100] items-center justify-center">
        <div class="bg-white rounded-2xl p-5 shadow-2xl border border-slate-200 flex items-center gap-3.5 max-w-sm mx-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                <svg class="animate-spin h-5 w-5 text-purple-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xs">Memproses Data</p>
                <p class="text-[11px] text-slate-500">Memuat data warga, jabatan, dan tanda tangan...</p>
            </div>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Data Warga & Pejabat</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar warga terdaftar, penetapan jabatan, tanda tangan digital, dan stempel dinas.</p>
        </div>
        <button wire:click="openWargaModal" 
                wire:loading.attr="disabled"
                wire:target="openWargaModal"
                class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
            <svg wire:loading.remove wire:target="openWargaModal" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <svg wire:loading wire:target="openWargaModal" class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span>Tambah Data Warga</span>
        </button>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full text-sm rounded-xl pl-9 pr-8 border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nama atau NIK warga...">
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="animate-spin h-4 w-4 text-emerald-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Table of Warga --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Jabatan & Tanda Tangan</th>
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
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($w->activeWargaJabatan as $wj)
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    {{ $wj->jabatan->nama }}
                                                </span>
                                                
                                                {{-- Signature status badge --}}
                                                @if($wj->tanda_tangan_path)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-violet-100 text-violet-800 border border-violet-200 flex items-center gap-0.5" title="Tanda tangan digital sudah terpasang">
                                                        <svg class="w-2.5 h-2.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                        TTD
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-amber-50 text-amber-700 border border-amber-200" title="Belum ada tanda tangan digital">
                                                        TTD Kosong
                                                    </span>
                                                @endif

                                                {{-- Stamp status badge --}}
                                                @if($wj->stempel_path)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-sky-100 text-sky-800 border border-sky-200 flex items-center gap-0.5" title="Stempel dinas sudah terpasang">
                                                        <svg class="w-2.5 h-2.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                        Stempel
                                                    </span>
                                                @endif
                                            </div>
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
                            <td class="px-4 py-3 text-right space-x-1.5">
                                <button wire:click="openWargaModal({{ $w->id }})" 
                                        wire:loading.attr="disabled"
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                                    Edit Warga
                                </button>
                                <button wire:click="openJabatanModal({{ $w->id }})" 
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold border border-purple-200 transition-all inline-flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <span>Jabatan & TTD</span>
                                </button>
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

    {{-- Modal: Edit Data Warga --}}
    @if($showWargaModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    {{ $warga_id ? 'Ubah Data Warga' : 'Tambah Warga Baru' }}
                </h3>
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
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="saveWarga"
                                class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 shadow-md">
                            Simpan Data Warga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Pengaturan Jabatan, Tanda Tangan & Stempel --}}
    @if($showJabatanModal && $selectedWargaForJabatan)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[92vh] overflow-y-auto p-6 space-y-5">
                
                {{-- Header --}}
                <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white font-black text-base flex items-center justify-center flex-shrink-0 shadow-md shadow-purple-600/20">
                            {{ strtoupper(substr($selectedWargaForJabatan->nama, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">{{ $selectedWargaForJabatan->nama }}</h3>
                            <p class="text-xs text-slate-500 font-mono">NIK: {{ $selectedWargaForJabatan->nik }} • {{ $selectedWargaForJabatan->desa->nama }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showJabatanModal', false)" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
                </div>

                {{-- Jabatan History & Switcher --}}
                @if($selectedWargaForJabatan->wargaJabatans->count() > 0)
                    <div class="space-y-2.5 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-200/80">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Daftar Jabatan Terdaftar</span>
                            <button type="button" 
                                    wire:click="createJabatanForm" 
                                    class="text-[11px] font-bold text-purple-700 hover:text-purple-900 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                + Tambah Jabatan Lain
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($selectedWargaForJabatan->wargaJabatans as $wj)
                                <div class="p-3 rounded-xl border transition-all flex flex-wrap sm:flex-nowrap items-center justify-between gap-3
                                    {{ $warga_jabatan_id === $wj->id 
                                        ? 'bg-purple-50/80 border-purple-300 ring-2 ring-purple-500/20 shadow-xs' 
                                        : 'bg-white border-slate-200 hover:border-purple-200' }}">
                                    
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-900">{{ $wj->jabatan->nama }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold 
                                                {{ $wj->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : ($wj->status === 'selesai' ? 'bg-slate-100 text-slate-600' : 'bg-rose-100 text-rose-800') }}">
                                                {{ ucfirst($wj->status) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-1 text-[10px] text-slate-500">
                                            <span>Periode: {{ $wj->tanggal_mulai?->format('d M Y') }} - {{ $wj->tanggal_selesai?->format('d M Y') ?? 'Sekarang' }}</span>
                                            @if($wj->nomor_sk)
                                                <span>• SK: {{ $wj->nomor_sk }}</span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                • TTD: 
                                                @if($wj->tanda_tangan_path)
                                                    <span class="text-emerald-700 font-bold">✓ Terpasang</span>
                                                @else
                                                    <span class="text-amber-600 font-medium">Belum ada</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button type="button" 
                                                wire:click="editJabatan({{ $wj->id }})" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all
                                                {{ $warga_jabatan_id === $wj->id 
                                                    ? 'bg-purple-600 text-white shadow-xs' 
                                                    : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                                            {{ $warga_jabatan_id === $wj->id ? 'Sedang Diedit' : 'Edit & TTD' }}
                                        </button>
                                        <button type="button" 
                                                wire:click="deleteJabatan({{ $wj->id }})" 
                                                wire:confirm="Yakin ingin menghapus jabatan ini dari riwayat warga?"
                                                class="p-1 text-slate-400 hover:text-rose-600 text-xs transition-colors"
                                                title="Hapus Jabatan">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form Editor Jabatan & Tanda Tangan --}}
                <form wire:submit="saveJabatanWarga" class="space-y-4 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            {{ $is_editing_jabatan ? 'Ubah Data Jabatan & Tanda Tangan' : 'Form Penetapan Jabatan Baru' }}
                        </h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-select 
                                wire:model="assign_jabatan_id" 
                                label="Pilih Jabatan" 
                                placeholder="-- Pilih Jabatan --"
                                :options="$availableJabatans"
                                :searchable="true"
                                required
                            />
                            @error('assign_jabatan_id')<span class="text-rose-500 text-[10px] block mt-1">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Jabatan <span class="text-rose-500">*</span></label>
                            <select wire:model="jabatan_status" class="w-full text-sm rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="aktif">Aktif (Sedang Menjabat)</option>
                                <option value="selesai">Selesai (Masa Jabatan Berakhir)</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="tanggal_mulai" class="w-full text-sm rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                            @error('tanggal_mulai')<span class="text-rose-500 text-[10px] block mt-1">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full text-sm rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                            @error('tanggal_selesai')<span class="text-rose-500 text-[10px] block mt-1">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor SK Pengangkatan</label>
                            <input type="text" wire:model="nomor_sk" class="w-full text-sm rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500" placeholder="141/01/SK/2026">
                        </div>
                    </div>

                    {{-- Upload & Preview: Tanda Tangan & Stempel Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        
                        {{-- Tanda Tangan Section --}}
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-800 text-xs">
                                ✍️ File Tanda Tangan Digital
                            </label>

                            @if($tanda_tangan)
                                {{-- Live Preview from newly uploaded temp file --}}
                                <div class="p-3 rounded-xl border border-purple-200 bg-purple-50/40 text-center space-y-2">
                                    <div class="h-28 flex items-center justify-center bg-white rounded-lg border border-purple-100 p-2 shadow-2xs">
                                        <img src="{{ $tanda_tangan->temporaryUrl() }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-purple-700 font-bold">✓ File Baru Dipilih</span>
                                        <button type="button" wire:click="$set('tanda_tangan', null)" class="text-rose-600 hover:underline font-semibold">Batal</button>
                                    </div>
                                </div>
                            @elseif($existing_tanda_tangan_path && $warga_jabatan_id)
                                {{-- Existing Signature Card with Preview --}}
                                <div class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/40 text-center space-y-2">
                                    <div class="h-28 flex items-center justify-center bg-white rounded-lg border border-emerald-100 p-2 shadow-2xs">
                                        <img src="{{ route('media.signature', $warga_jabatan_id) }}" class="max-h-full max-w-full object-contain" alt="Tanda Tangan">
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] pt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ TTD Tersimpan
                                        </span>
                                        <button type="button" wire:click="removeExistingSignature" class="text-rose-600 hover:underline font-semibold">
                                            Hapus TTD
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Upload Zone --}}
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-purple-500 rounded-xl p-4 transition-all bg-slate-50/50 hover:bg-purple-50/20 text-center">
                                    <input type="file" wire:model="tanda_tangan" accept="image/png,image/jpeg,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="space-y-1">
                                        <div class="w-8 h-8 mx-auto rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Unggah Tanda Tangan</p>
                                        <p class="text-[10px] text-slate-400">PNG Transparan disarankan (Maks 2MB)</p>
                                    </div>
                                </div>
                            @endif

                            <div wire:loading wire:target="tanda_tangan" class="text-[10px] text-purple-600 flex items-center gap-1 font-semibold">
                                <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Mengunggah file tanda tangan...</span>
                            </div>
                            @error('tanda_tangan')<span class="text-rose-500 text-[10px] block">{{ $message }}</span>@enderror
                        </div>

                        {{-- Stempel Section --}}
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-800 text-xs">
                                🏛️ File Stempel Dinas / Instansi
                            </label>

                            @if($stempel)
                                {{-- Live Preview from newly uploaded temp file --}}
                                <div class="p-3 rounded-xl border border-sky-200 bg-sky-50/40 text-center space-y-2">
                                    <div class="h-28 flex items-center justify-center bg-white rounded-lg border border-sky-100 p-2 shadow-2xs">
                                        <img src="{{ $stempel->temporaryUrl() }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-sky-700 font-bold">✓ File Baru Dipilih</span>
                                        <button type="button" wire:click="$set('stempel', null)" class="text-rose-600 hover:underline font-semibold">Batal</button>
                                    </div>
                                </div>
                            @elseif($existing_stempel_path && $warga_jabatan_id)
                                {{-- Existing Stamp Card with Preview --}}
                                <div class="p-3 rounded-xl border border-sky-200 bg-sky-50/40 text-center space-y-2">
                                    <div class="h-28 flex items-center justify-center bg-white rounded-lg border border-sky-100 p-2 shadow-2xs">
                                        <img src="{{ route('media.stamp', $warga_jabatan_id) }}" class="max-h-full max-w-full object-contain" alt="Stempel">
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] pt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">
                                            ✓ Stempel Tersimpan
                                        </span>
                                        <button type="button" wire:click="removeExistingStamp" class="text-rose-600 hover:underline font-semibold">
                                            Hapus Stempel
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Upload Zone --}}
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-sky-500 rounded-xl p-4 transition-all bg-slate-50/50 hover:bg-sky-50/20 text-center">
                                    <input type="file" wire:model="stempel" accept="image/png,image/jpeg,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="space-y-1">
                                        <div class="w-8 h-8 mx-auto rounded-full bg-sky-100 text-sky-600 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Unggah Stempel Dinas</p>
                                        <p class="text-[10px] text-slate-400">PNG Transparan disarankan (Maks 2MB)</p>
                                    </div>
                                </div>
                            @endif

                            <div wire:loading wire:target="stempel" class="text-[10px] text-sky-600 flex items-center gap-1 font-semibold">
                                <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Mengunggah file stempel...</span>
                            </div>
                            @error('stempel')<span class="text-rose-500 text-[10px] block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showJabatanModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Tutup</button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="saveJabatanWarga"
                                class="px-5 py-2.5 rounded-xl font-bold text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50 shadow-md shadow-purple-600/20 flex items-center gap-1.5 transition-all">
                            <svg wire:loading.remove wire:target="saveJabatanWarga" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg wire:loading wire:target="saveJabatanWarga" class="animate-spin w-4 h-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="saveJabatanWarga">{{ $is_editing_jabatan ? 'Simpan Perubahan Jabatan & TTD' : 'Tetapkan Jabatan' }}</span>
                            <span wire:loading wire:target="saveJabatanWarga">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
