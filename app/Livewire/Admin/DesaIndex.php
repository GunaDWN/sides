<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DesaIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // Form fields
    public $desa_id = null;
    public $nama = '';
    public $kode = '';
    public $provinsi = '';
    public $kabupaten = '';
    public $kecamatan = '';
    public $alamat = '';
    public $kode_pos = '';
    public $telepon = '';
    public $email = '';
    public $nama_kepala_desa = '';
    public $is_active = true;

    public $logo;
    public $kop;
    public $stempel;

    public $showModal = false;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['desa_id', 'nama', 'kode', 'provinsi', 'kabupaten', 'kecamatan', 'alamat', 'kode_pos', 'telepon', 'email', 'nama_kepala_desa', 'logo', 'kop', 'stempel']);
        $this->is_active = true;

        if ($id) {
            $desa = Desa::findOrFail($id);
            $this->desa_id = $desa->id;
            $this->nama = $desa->nama;
            $this->kode = $desa->kode;
            $this->provinsi = $desa->provinsi;
            $this->kabupaten = $desa->kabupaten;
            $this->kecamatan = $desa->kecamatan;
            $this->alamat = $desa->alamat;
            $this->kode_pos = $desa->kode_pos;
            $this->telepon = $desa->telepon;
            $this->email = $desa->email;
            $this->nama_kepala_desa = $desa->nama_kepala_desa;
            $this->is_active = $desa->is_active;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|max:100',
            'provinsi' => 'required|max:100',
            'kabupaten' => 'required|max:100',
            'kecamatan' => 'required|max:100',
            'logo' => 'nullable|image|max:2048',
            'kop' => 'nullable|image|max:2048',
            'stempel' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $this->nama,
            'kode' => $this->kode,
            'provinsi' => $this->provinsi,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'alamat' => $this->alamat,
            'kode_pos' => $this->kode_pos,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'nama_kepala_desa' => $this->nama_kepala_desa,
            'is_active' => $this->is_active,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('desas/logos', 'public');
        }
        if ($this->kop) {
            $data['kop_path'] = $this->kop->store('desas/kops', 'public');
        }
        if ($this->stempel) {
            $data['stempel_path'] = $this->stempel->store('desas/stempels', 'public');
        }

        if ($this->desa_id) {
            Desa::findOrFail($this->desa_id)->update($data);
            session()->flash('success', 'Data desa berhasil diperbarui!');
        } else {
            Desa::create($data);
            session()->flash('success', 'Data desa baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function toggleActive($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->update(['is_active' => !$desa->is_active]);
        session()->flash('success', 'Status keaktifan desa berhasil diubah.');
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Desa::query();
        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('kecamatan', 'like', '%' . $this->search . '%')
                ->orWhere('kabupaten', 'like', '%' . $this->search . '%');
        }

        $desas = $query->latest()->paginate(10);

        return view('livewire.admin.desa-index', [
            'desas' => $desas,
        ])->layout('layouts.app');
    }
}
