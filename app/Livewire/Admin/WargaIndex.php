<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Warga;
use App\Models\WargaJabatan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class WargaIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // Form Warga
    public $warga_id = null;
    public $desa_id = '';
    public $nik = '';
    public $no_kk = '';
    public $nama = '';
    public $tempat_lahir = '';
    public $tanggal_lahir = '';
    public $jenis_kelamin = 'L';
    public $alamat = '';
    public $rt = '';
    public $rw = '';
    public $telepon = '';
    public $email = '';
    public $jenis_warga = 'warga_biasa';
    public $status = 'aktif';

    public $showWargaModal = false;

    // Form Jabatan, Tanda Tangan & Stempel
    public $showJabatanModal = false;
    public $selectedWargaForJabatan = null;
    public $warga_jabatan_id = null;
    public $assign_jabatan_id = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $nomor_sk = '';
    public $jabatan_status = 'aktif';
    public $tanda_tangan;
    public $stempel;
    public $existing_tanda_tangan_path = null;
    public $existing_stempel_path = null;
    public $is_editing_jabatan = false;

    public function openWargaModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['warga_id', 'desa_id', 'nik', 'no_kk', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'rt', 'rw', 'telepon', 'email', 'jenis_warga', 'status']);

        if ($id) {
            $w = Warga::findOrFail($id);
            $this->warga_id = $w->id;
            $this->desa_id = $w->desa_id;
            $this->nik = $w->nik;
            $this->no_kk = $w->no_kk;
            $this->nama = $w->nama;
            $this->tempat_lahir = $w->tempat_lahir;
            $this->tanggal_lahir = $w->tanggal_lahir?->format('Y-m-d');
            $this->jenis_kelamin = $w->jenis_kelamin;
            $this->alamat = $w->alamat;
            $this->rt = $w->rt;
            $this->rw = $w->rw;
            $this->telepon = $w->telepon;
            $this->email = $w->email;
            $this->jenis_warga = $w->jenis_warga;
            $this->status = $w->status;
        } else {
            $this->desa_id = auth()->user()->desa_id ?? Desa::first()?->id;
            $this->tanggal_lahir = '1990-01-01';
        }

        $this->showWargaModal = true;
    }

    public function saveWarga()
    {
        $this->validate([
            'nama' => 'required|max:150',
            'nik' => 'required|numeric|digits:16|unique:wargas,nik,' . $this->warga_id,
            'desa_id' => 'required|exists:desas,id',
        ]);

        $data = [
            'desa_id' => $this->desa_id,
            'nik' => $this->nik,
            'no_kk' => $this->no_kk,
            'nama' => $this->nama,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'jenis_warga' => $this->jenis_warga,
            'status' => $this->status,
        ];

        if ($this->warga_id) {
            Warga::findOrFail($this->warga_id)->update($data);
            session()->flash('success', 'Data warga berhasil diperbarui!');
        } else {
            Warga::create($data);
            session()->flash('success', 'Data warga baru berhasil ditambahkan!');
        }

        $this->showWargaModal = false;
    }

    public function openJabatanModal($wargaId)
    {
        $this->resetValidation();
        $this->selectedWargaForJabatan = Warga::with(['desa', 'wargaJabatans.jabatan'])->findOrFail($wargaId);
        
        $activeJabatan = $this->selectedWargaForJabatan->wargaJabatans->firstWhere('status', 'aktif') 
            ?? $this->selectedWargaForJabatan->wargaJabatans->first();

        if ($activeJabatan) {
            $this->editJabatan($activeJabatan->id);
        } else {
            $this->createJabatanForm();
        }

        $this->showJabatanModal = true;
    }

    public function createJabatanForm()
    {
        $this->resetValidation();
        $this->reset([
            'warga_jabatan_id',
            'assign_jabatan_id',
            'nomor_sk',
            'tanda_tangan',
            'stempel',
            'existing_tanda_tangan_path',
            'existing_stempel_path'
        ]);
        $this->tanggal_mulai = now()->format('Y-m-d');
        $this->tanggal_selesai = '';
        $this->jabatan_status = 'aktif';
        $this->is_editing_jabatan = false;
    }

    public function editJabatan($wargaJabatanId)
    {
        $this->resetValidation();
        $this->reset(['tanda_tangan', 'stempel']);
        
        $wj = WargaJabatan::findOrFail($wargaJabatanId);
        $this->warga_jabatan_id = $wj->id;
        $this->assign_jabatan_id = $wj->jabatan_id;
        $this->tanggal_mulai = $wj->tanggal_mulai?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->tanggal_selesai = $wj->tanggal_selesai?->format('Y-m-d') ?? '';
        $this->nomor_sk = $wj->nomor_sk;
        $this->jabatan_status = $wj->status;
        $this->existing_tanda_tangan_path = $wj->tanda_tangan_path;
        $this->existing_stempel_path = $wj->stempel_path;
        $this->is_editing_jabatan = true;
    }

    public function removeExistingSignature()
    {
        $this->existing_tanda_tangan_path = null;
        $this->tanda_tangan = null;
    }

    public function removeExistingStamp()
    {
        $this->existing_stempel_path = null;
        $this->stempel = null;
    }

    public function saveJabatanWarga()
    {
        $this->validate([
            'assign_jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jabatan_status' => 'required|in:aktif,selesai,nonaktif',
            'tanda_tangan' => 'nullable|image|max:2048',
            'stempel' => 'nullable|image|max:2048',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'assign_jabatan_id.required' => 'Pilih jabatan terlebih dahulu.',
        ]);

        if ($this->warga_jabatan_id) {
            $wj = WargaJabatan::findOrFail($this->warga_jabatan_id);

            $ttdPath = $wj->tanda_tangan_path;
            if ($this->tanda_tangan) {
                if ($ttdPath && Storage::disk('local')->exists($ttdPath)) {
                    Storage::disk('local')->delete($ttdPath);
                }
                $ttdPath = $this->tanda_tangan->store('signatures', 'local');
            } elseif ($this->existing_tanda_tangan_path === null && $ttdPath) {
                if (Storage::disk('local')->exists($ttdPath)) {
                    Storage::disk('local')->delete($ttdPath);
                }
                $ttdPath = null;
            }

            $stempelPath = $wj->stempel_path;
            if ($this->stempel) {
                if ($stempelPath && Storage::disk('local')->exists($stempelPath)) {
                    Storage::disk('local')->delete($stempelPath);
                }
                $stempelPath = $this->stempel->store('stamps', 'local');
            } elseif ($this->existing_stempel_path === null && $stempelPath) {
                if (Storage::disk('local')->exists($stempelPath)) {
                    Storage::disk('local')->delete($stempelPath);
                }
                $stempelPath = null;
            }

            $wj->update([
                'jabatan_id' => $this->assign_jabatan_id,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'nomor_sk' => $this->nomor_sk,
                'status' => $this->jabatan_status,
                'tanda_tangan_path' => $ttdPath,
                'stempel_path' => $stempelPath,
            ]);

            session()->flash('success', 'Data jabatan, tanda tangan & stempel berhasil diperbarui!');
        } else {
            $ttdPath = $this->tanda_tangan ? $this->tanda_tangan->store('signatures', 'local') : null;
            $stempelPath = $this->stempel ? $this->stempel->store('stamps', 'local') : null;

            WargaJabatan::create([
                'warga_id' => $this->selectedWargaForJabatan->id,
                'jabatan_id' => $this->assign_jabatan_id,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'nomor_sk' => $this->nomor_sk,
                'status' => $this->jabatan_status,
                'tanda_tangan_path' => $ttdPath,
                'stempel_path' => $stempelPath,
            ]);

            session()->flash('success', 'Jabatan, tanda tangan & stempel baru berhasil ditetapkan!');
        }

        // Update Warga type to warga_dengan_jabatan if has active jabatan
        $hasActive = WargaJabatan::where('warga_id', $this->selectedWargaForJabatan->id)
            ->where('status', 'aktif')
            ->exists();

        $this->selectedWargaForJabatan->update([
            'jenis_warga' => $hasActive ? 'warga_dengan_jabatan' : 'warga_biasa'
        ]);

        $this->showJabatanModal = false;
    }

    public function deleteJabatan($wargaJabatanId)
    {
        $wj = WargaJabatan::findOrFail($wargaJabatanId);
        $wargaId = $wj->warga_id;
        $wj->delete();

        $hasActive = WargaJabatan::where('warga_id', $wargaId)
            ->where('status', 'aktif')
            ->exists();

        Warga::where('id', $wargaId)->update([
            'jenis_warga' => $hasActive ? 'warga_dengan_jabatan' : 'warga_biasa'
        ]);

        $this->selectedWargaForJabatan = Warga::with(['desa', 'wargaJabatans.jabatan'])->find($wargaId);
        $this->createJabatanForm();
        session()->flash('success', 'Jabatan berhasil dihapus dari riwayat.');
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Warga::query()->with(['desa', 'activeWargaJabatan.jabatan', 'wargaJabatans.jabatan']);

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', $s)->orWhere('nik', 'like', $s);
            });
        }

        $wargas = $query->latest()->paginate(10);
        $desasList = Desa::where('is_active', true)->get();

        $availableJabatans = $this->selectedWargaForJabatan
            ? Jabatan::where('is_active', true)
                ->where(function ($q) {
                    $q->where('desa_id', $this->selectedWargaForJabatan->desa_id)
                      ->orWhereNull('desa_id');
                })
                ->orderBy('urutan')
                ->get()
            : collect();

        return view('livewire.admin.warga-index', [
            'wargas' => $wargas,
            'desasList' => $desasList,
            'availableJabatans' => $availableJabatans,
        ])->layout('layouts.app');
    }
}
