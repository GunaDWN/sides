<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Warga;
use App\Models\WargaJabatan;
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

    // Form Assign Jabatan
    public $showJabatanModal = false;
    public $selectedWargaForJabatan = null;
    public $assign_jabatan_id = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $nomor_sk = '';
    public $tanda_tangan;
    public $stempel;

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
        $this->reset(['assign_jabatan_id', 'tanggal_mulai', 'tanggal_selesai', 'nomor_sk', 'tanda_tangan', 'stempel']);
        $this->selectedWargaForJabatan = Warga::with(['desa', 'wargaJabatans.jabatan'])->findOrFail($wargaId);
        $this->tanggal_mulai = now()->format('Y-m-d');
        $this->showJabatanModal = true;
    }

    public function saveJabatanWarga()
    {
        $this->validate([
            'assign_jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanda_tangan' => 'nullable|image|max:2048',
            'stempel' => 'nullable|image|max:2048',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $ttdPath = $this->tanda_tangan ? $this->tanda_tangan->store('signatures', 'local') : null;
        $stempelPath = $this->stempel ? $this->stempel->store('stamps', 'local') : null;

        WargaJabatan::create([
            'warga_id' => $this->selectedWargaForJabatan->id,
            'jabatan_id' => $this->assign_jabatan_id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai ?: null,
            'nomor_sk' => $this->nomor_sk,
            'tanda_tangan_path' => $ttdPath,
            'stempel_path' => $stempelPath,
            'status' => 'aktif',
        ]);

        // Update Warga type to warga_dengan_jabatan
        $this->selectedWargaForJabatan->update(['jenis_warga' => 'warga_dengan_jabatan']);

        session()->flash('success', 'Jabatan berhasil ditetapkan untuk warga!');
        $this->showJabatanModal = false;
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Warga::query()->with(['desa', 'activeWargaJabatan.jabatan']);

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', $s)->orWhere('nik', 'like', $s);
            });
        }

        $wargas = $query->latest()->paginate(10);
        $desasList = Desa::where('is_active', true)->get();

        $availableJabatans = $this->selectedWargaForJabatan
            ? Jabatan::where('desa_id', $this->selectedWargaForJabatan->desa_id)->where('is_active', true)->get()
            : collect();

        return view('livewire.admin.warga-index', [
            'wargas' => $wargas,
            'desasList' => $desasList,
            'availableJabatans' => $availableJabatans,
        ])->layout('layouts.app');
    }
}
