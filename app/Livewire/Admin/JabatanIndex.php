<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Permission;
use Livewire\Component;

class JabatanIndex extends Component
{
    public $search = '';

    public $jabatan_id = null;
    public $desa_id = '';
    public $nama = '';
    public $kode = '';
    public $deskripsi = '';
    public $urutan = 1;
    public $is_active = true;
    public $selectedPermissions = [];

    public $showModal = false;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['jabatan_id', 'desa_id', 'nama', 'kode', 'deskripsi', 'urutan', 'selectedPermissions']);
        $this->is_active = true;

        if ($id) {
            $j = Jabatan::with('permissions')->findOrFail($id);
            $this->jabatan_id = $j->id;
            $this->desa_id = $j->desa_id;
            $this->nama = $j->nama;
            $this->kode = $j->kode;
            $this->deskripsi = $j->deskripsi;
            $this->urutan = $j->urutan;
            $this->is_active = $j->is_active;
            $this->selectedPermissions = $j->permissions->pluck('id')->toArray();
        } else {
            $this->desa_id = auth()->user()->desa_id ?? Desa::first()?->id;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|max:100',
            'desa_id' => 'required|exists:desas,id',
            'urutan' => 'required|numeric',
        ]);

        $data = [
            'desa_id' => $this->desa_id,
            'nama' => $this->nama,
            'kode' => $this->kode ?: strtoupper(str_replace(' ', '_', $this->nama)),
            'deskripsi' => $this->deskripsi,
            'urutan' => $this->urutan,
            'is_active' => $this->is_active,
        ];

        if ($this->jabatan_id) {
            $j = Jabatan::findOrFail($this->jabatan_id);
            $j->update($data);
            $j->permissions()->sync($this->selectedPermissions);
            session()->flash('success', 'Jabatan & permission berhasil diperbarui!');
        } else {
            $j = Jabatan::create($data);
            $j->permissions()->sync($this->selectedPermissions);
            session()->flash('success', 'Jabatan baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Jabatan::query()->with(['desa', 'permissions']);

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $jabatans = $query->orderBy('urutan', 'asc')->get();
        $permissionsGrouped = Permission::all()->groupBy('group');
        $desasList = Desa::where('is_active', true)->get();

        return view('livewire.admin.jabatan-index', [
            'jabatans' => $jabatans,
            'permissionsGrouped' => $permissionsGrouped,
            'desasList' => $desasList,
        ])->layout('layouts.app');
    }
}
