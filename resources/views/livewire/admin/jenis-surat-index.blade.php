<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Jenis Surat</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola kategori surat, template dokumen, dan urutan jabatan persetujuan.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all">+ Tambah Jenis Surat</button>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari jenis surat...">
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Nama Surat</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Approval</th>
                        <th class="px-4 py-3">Urutan Pejabat</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surats as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $s->nama }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $s->kode }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $s->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $s->desa->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->butuh_approval ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $s->butuh_approval ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($s->approvals as $app)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $app->urutan }}. {{ $app->jabatan->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openModal({{ $s->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada jenis surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $surats->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">{{ $surat_id ? 'Ubah Jenis Surat' : 'Tambah Jenis Surat Baru' }}</h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Nama Surat <span class="text-rose-500">*</span></label><input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-slate-300">@error('nama')<span class="text-rose-500">{{ $message }}</span>@enderror</div>
                        <div><label class="block font-semibold text-slate-700 mb-1">Kode Surat</label><input type="text" wire:model="kode" class="w-full text-sm rounded-lg border-slate-300"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1">Kategori</label><input type="text" wire:model="kategori" class="w-full text-sm rounded-lg border-slate-300" placeholder="Kependudukan, Perekonomian..."></div>
                        <div>
                            <x-select 
                                wire:model.live="desa_id" 
                                label="Desa" 
                                placeholder="-- Pilih Desa --"
                                :options="$desasList"
                                :searchable="true"
                                required
                            />
                        </div>
                    </div>
                    <div><label class="block font-semibold text-slate-700 mb-1">Deskripsi</label><textarea wire:model="deskripsi" rows="2" class="w-full text-sm rounded-lg border-slate-300"></textarea></div>
                    <div><label class="block font-semibold text-slate-700 mb-1">Upload Template (.doc, .docx, .pdf — Max 10MB)</label><input type="file" wire:model="template" class="w-full text-xs"></div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model.live="butuh_approval" class="rounded text-emerald-600"><span class="font-semibold text-slate-700">Membutuhkan Approval Pejabat</span></label>
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_active" class="rounded text-emerald-600"><span class="font-semibold text-slate-700">Surat Aktif</span></label>
                    </div>

                    @if($butuh_approval)
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Urutan Pejabat Approval</h4>
                                <button type="button" wire:click="addApprovalStep" class="px-3 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold border border-sky-200">+ Tambah Tahapan</button>
                            </div>
                            @foreach($approvalSteps as $index => $step)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">{{ $step['urutan'] }}</span>
                                    <div class="flex-1">
                                        <x-select 
                                            wire:model="approvalSteps.{{ $index }}.jabatan_id" 
                                            placeholder="-- Pilih Jabatan --"
                                            :options="$jabatansByDesa"
                                            :searchable="true"
                                        />
                                    </div>
                                    <button type="button" wire:click="removeApprovalStep({{ $index }})" class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold border border-rose-200">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">Simpan Jenis Surat</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
