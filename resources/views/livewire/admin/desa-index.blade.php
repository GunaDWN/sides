<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Data Desa</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola identitas desa, kelurahan, kop surat, dan kades dalam sistem.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
            + Tambah Desa Baru
        </button>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nama desa, kecamatan...">
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Nama Desa</th>
                        <th class="px-4 py-3">Kecamatan & Kab/Kota</th>
                        <th class="px-4 py-3">Status Registrasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($desas as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $d->nama }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $d->kecamatan }}, {{ $d->kabupaten }} ({{ $d->provinsi }})</td>
                            <td class="px-4 py-3">
                                <button wire:click="toggleActive({{ $d->id }})" class="px-2.5 py-1 rounded-full text-xs font-bold border transition-all {{ $d->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200' }}">
                                    {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openModal({{ $d->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada data desa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $desas->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">
                    {{ $desa_id ? 'Ubah Data Desa' : 'Tambah Desa Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Desa <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300">
                            @error('nama') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Desa</label>
                            <input type="text" wire:model="kode" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi <span class="text-rose-500">*</span></label>
                            <x-select 
                                wire:model.live="selectedProvinsiId" 
                                placeholder="-- Pilih Provinsi --"
                                :options="$provincesList"
                                optionValue="id"
                                optionLabel="nama"
                                :searchable="true"
                            />
                            @error('provinsi') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kabupaten/Kota <span class="text-rose-500">*</span></label>
                            <x-select 
                                wire:model.live="selectedKabupatenId" 
                                placeholder="{{ $selectedProvinsiId ? '-- Pilih Kab/Kota --' : '-- Pilih Provinsi Dulu --' }}"
                                :options="$kabupatensList"
                                optionValue="id"
                                optionLabel="nama"
                                :searchable="true"
                            />
                            @error('kabupaten') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                            <x-select 
                                wire:model.live="selectedKecamatanId" 
                                placeholder="{{ $selectedKabupatenId ? '-- Pilih Kecamatan --' : '-- Pilih Kab/Kota Dulu --' }}"
                                :options="$kecamatansList"
                                optionValue="id"
                                optionLabel="nama"
                                :searchable="true"
                            />
                            @error('kecamatan') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Kantor Desa</label>
                        <textarea wire:model="alamat" rows="2" class="w-full text-sm rounded-lg border-slate-300"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Upload Logo Desa</label>
                            <input type="file" wire:model="logo" class="w-full text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Upload Kop Surat</label>
                            <input type="file" wire:model="kop" class="w-full text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Upload Stempel</label>
                            <input type="file" wire:model="stempel" class="w-full text-xs">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="is_active_cb" class="rounded text-emerald-600">
                        <label for="is_active_cb" class="font-semibold text-slate-700">Aktifkan desa ini (Registrasi Warga Publik Aktif)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">Simpan Data Desa</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
