<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengelolaan Pengguna Sistem (Users)</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar pengguna terdaftar, peranan (role), status keaktifan, dan keterhubungan ke data warga.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 text-xs transition-all flex items-center gap-2">
            + Tambah User Baru
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full md:w-72 text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Cari nama atau email...">
        <x-select 
            wire:model.live="roleFilter" 
            placeholder="Semua Role"
            :options="[
                ['id' => 'admin', 'nama' => 'Admin'],
                ['id' => 'warga', 'nama' => 'Warga'],
            ]"
            :searchable="false"
        />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-4 py-3">Nama User</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Terhubung Warga</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase {{ $u->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $u->desa->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $u->warga->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="toggleActive({{ $u->id }})" class="px-2.5 py-1 rounded-full text-xs font-bold border transition-all {{ $u->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openModal({{ $u->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">
                    {{ $user_id ? 'Ubah User' : 'Tambah User Baru' }}
                </h3>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full text-sm rounded-lg border-slate-300">
                        @error('name') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full text-sm rounded-lg border-slate-300">
                        @error('email') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Password {{ $user_id ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
                        <input type="password" wire:model="password" class="w-full text-sm rounded-lg border-slate-300">
                        @error('password') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-select 
                                wire:model="role" 
                                label="Role Utama" 
                                placeholder="-- Pilih Role --"
                                :options="[
                                    ['id' => 'warga', 'nama' => 'Warga'],
                                    ['id' => 'admin', 'nama' => 'Admin'],
                                ]"
                                :searchable="false"
                                required
                            />
                        </div>
                        <div>
                            <x-select 
                                wire:model="desa_id" 
                                label="Desa Terkait" 
                                placeholder="-- Pilih Desa --"
                                :options="$desasList"
                                :searchable="true"
                            />
                        </div>
                    </div>

                    <div>
                        <x-select 
                            wire:model="warga_id" 
                            label="Hubungkan ke Data Warga (Opsional)" 
                            placeholder="-- Tidak Terhubung --"
                            :options="$wargasList"
                            option-label="nama"
                            :searchable="true"
                        />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="user_active_cb" class="rounded text-emerald-600">
                        <label for="user_active_cb" class="font-semibold text-slate-700">Akun Aktif (Dapat Login)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
