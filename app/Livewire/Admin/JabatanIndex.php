<?php

namespace App\Livewire\Admin;

use App\Models\Jabatan;
use Livewire\Component;

class JabatanIndex extends Component
{
    public $search = '';

    public $jabatan_id = null;
    public $nama = '';
    public $kode = '';
    public $deskripsi = '';
    public $urutan = 1;
    public $is_active = true;

    public $showModal = false;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['jabatan_id', 'nama', 'kode', 'deskripsi', 'urutan']);
        $this->is_active = true;

        if ($id) {
            $j = Jabatan::findOrFail($id);
            $this->jabatan_id = $j->id;
            $this->nama = $j->nama;
            $this->kode = $j->kode;
            $this->deskripsi = $j->deskripsi;
            $this->urutan = $j->urutan;
            $this->is_active = $j->is_active;
        } else {
            $this->urutan = Jabatan::max('urutan') + 1;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|max:100',
            'urutan' => 'required|numeric',
        ]);

        $data = [
            'nama' => $this->nama,
            'kode' => $this->kode ?: strtoupper(str_replace(' ', '_', $this->nama)),
            'deskripsi' => $this->deskripsi,
            'urutan' => $this->urutan,
            'is_active' => $this->is_active,
        ];

        if ($this->jabatan_id) {
            $j = Jabatan::findOrFail($this->jabatan_id);
            $j->update($data);
            session()->flash('success', 'Master Jabatan berhasil diperbarui!');
        } else {
            Jabatan::create($data);
            session()->flash('success', 'Master Jabatan baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Jabatan::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kode', 'like', '%' . $this->search . '%');
            });
        }

        $jabatans = $query->orderBy('urutan', 'asc')->get();

        return view('livewire.admin.jabatan-index', [
            'jabatans' => $jabatans,
        ])->layout('layouts.app');
    }
}
