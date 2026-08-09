<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Services\WilayahService;
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
    public $kode_provinsi = '';
    public $kabupaten = '';
    public $kode_kabupaten = '';
    public $kecamatan = '';
    public $kode_kecamatan = '';
    public $alamat = '';
    public $kode_pos = '';
    public $telepon = '';
    public $email = '';
    public $is_active = true;

    // Selected IDs for API cascade
    public $selectedProvinsiId = '';
    public $selectedKabupatenId = '';
    public $selectedKecamatanId = '';

    public $logo;
    public $kop;
    public $stempel;

    public $showModal = false;

    public function updatedSelectedProvinsiId($val)
    {
        $this->selectedKabupatenId = '';
        $this->selectedKecamatanId = '';
        $this->kabupaten = '';
        $this->kode_kabupaten = '';
        $this->kecamatan = '';
        $this->kode_kecamatan = '';

        if ($val) {
            $service = app(WilayahService::class);
            $provinces = $service->getProvinces();
            $matched = collect($provinces)->firstWhere('id', $val);
            if ($matched) {
                $this->provinsi = $matched['nama'];
                $this->kode_provinsi = $val;
            }
        } else {
            $this->provinsi = '';
            $this->kode_provinsi = '';
        }
    }

    public function updatedSelectedKabupatenId($val)
    {
        $this->selectedKecamatanId = '';
        $this->kecamatan = '';
        $this->kode_kecamatan = '';

        if ($val && $this->selectedProvinsiId) {
            $service = app(WilayahService::class);
            $regencies = $service->getRegencies($this->selectedProvinsiId);
            $matched = collect($regencies)->firstWhere('id', $val);
            if ($matched) {
                $this->kabupaten = $matched['nama'];
                $this->kode_kabupaten = $val;
            }
        } else {
            $this->kabupaten = '';
            $this->kode_kabupaten = '';
        }
    }

    public function updatedSelectedKecamatanId($val)
    {
        if ($val && $this->selectedKabupatenId) {
            $service = app(WilayahService::class);
            $districts = $service->getDistricts($this->selectedKabupatenId);
            $matched = collect($districts)->firstWhere('id', $val);
            if ($matched) {
                $this->kecamatan = $matched['nama'];
                $this->kode_kecamatan = $val;
            }
        } else {
            $this->kecamatan = '';
            $this->kode_kecamatan = '';
        }
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset([
            'desa_id', 'nama', 'kode', 
            'provinsi', 'kode_provinsi', 
            'kabupaten', 'kode_kabupaten', 
            'kecamatan', 'kode_kecamatan', 
            'selectedProvinsiId', 'selectedKabupatenId', 'selectedKecamatanId',
            'alamat', 'kode_pos', 'telepon', 'email', 'logo', 'kop', 'stempel'
        ]);
        $this->is_active = true;

        if ($id) {
            $desa = Desa::findOrFail($id);
            $this->desa_id = $desa->id;
            $this->nama = $desa->nama;
            $this->kode = $desa->kode;
            $this->provinsi = $desa->provinsi;
            $this->kode_provinsi = $desa->kode_provinsi;
            $this->kabupaten = $desa->kabupaten;
            $this->kode_kabupaten = $desa->kode_kabupaten;
            $this->kecamatan = $desa->kecamatan;
            $this->kode_kecamatan = $desa->kode_kecamatan;

            $this->selectedProvinsiId = $desa->kode_provinsi ?? '';
            $this->selectedKabupatenId = $desa->kode_kabupaten ?? '';
            $this->selectedKecamatanId = $desa->kode_kecamatan ?? '';

            $this->alamat = $desa->alamat;
            $this->kode_pos = $desa->kode_pos;
            $this->telepon = $desa->telepon;
            $this->email = $desa->email;
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
            'kode_provinsi' => $this->kode_provinsi,
            'kabupaten' => $this->kabupaten,
            'kode_kabupaten' => $this->kode_kabupaten,
            'kecamatan' => $this->kecamatan,
            'kode_kecamatan' => $this->kode_kecamatan,
            'alamat' => $this->alamat,
            'kode_pos' => $this->kode_pos,
            'telepon' => $this->telepon,
            'email' => $this->email,
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

    public function render(WilayahService $service)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Desa::query();
        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where('nama', 'like', $s)
                ->orWhere('kecamatan', 'like', $s)
                ->orWhere('kabupaten', 'like', $s)
                ->orWhere('provinsi', 'like', $s);
        }

        $provincesList = $service->getProvinces();
        $kabupatensList = $this->selectedProvinsiId ? $service->getRegencies($this->selectedProvinsiId) : [];
        $kecamatansList = $this->selectedKabupatenId ? $service->getDistricts($this->selectedKabupatenId) : [];

        return view('livewire.admin.desa-index', [
            'desas' => $query->latest()->paginate(10),
            'provincesList' => $provincesList,
            'kabupatensList' => $kabupatensList,
            'kecamatansList' => $kecamatansList,
        ])->layout('layouts.app');
    }
}
