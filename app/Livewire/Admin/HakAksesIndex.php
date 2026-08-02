<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class HakAksesIndex extends Component
{
    #[Url]
    public $desa_id = '';

    #[Url]
    public $jabatan_id = '';

    public $selectedPermissions = [];

    public function mount()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // If no desa_id set, pick user's desa if available
        if (!$this->desa_id && auth()->user()->desa_id) {
            $this->desa_id = (string) auth()->user()->desa_id;
        }

        // If no jabatan_id set, pick first available jabatan
        if (!$this->jabatan_id) {
            $firstJabatan = Jabatan::where('is_active', true)->orderBy('urutan', 'asc')->first();
            if ($firstJabatan) {
                $this->jabatan_id = (string) $firstJabatan->id;
            }
        }

        $this->loadPermissions();
    }

    public function updatedDesaId()
    {
        $this->loadPermissions();
    }

    public function updatedJabatanId()
    {
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        if (!$this->jabatan_id) {
            $this->selectedPermissions = [];
            return;
        }

        $query = DB::table('jabatan_permissions')
            ->where('jabatan_id', $this->jabatan_id);

        if ($this->desa_id) {
            $query->where('desa_id', $this->desa_id);
        } else {
            $query->whereNull('desa_id');
        }

        $this->selectedPermissions = $query->pluck('permission_id')->map(fn($id) => (string)$id)->toArray();
    }

    public function selectAll()
    {
        $this->selectedPermissions = Permission::pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function deselectAll()
    {
        $this->selectedPermissions = [];
    }

    public function save()
    {
        $this->validate([
            'jabatan_id' => 'required|exists:jabatans,id',
        ]);

        DB::transaction(function () {
            $q = DB::table('jabatan_permissions')->where('jabatan_id', $this->jabatan_id);
            if ($this->desa_id) {
                $q->where('desa_id', $this->desa_id);
            } else {
                $q->whereNull('desa_id');
            }
            $q->delete();

            foreach ($this->selectedPermissions as $permId) {
                DB::table('jabatan_permissions')->insert([
                    'desa_id' => $this->desa_id ?: null,
                    'jabatan_id' => $this->jabatan_id,
                    'permission_id' => $permId,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        session()->flash('success', 'Hak akses berhasil disimpan!');
    }

    public function render()
    {
        $desasList = Desa::where('is_active', true)->orderBy('nama', 'asc')->get();
        $jabatansList = Jabatan::where('is_active', true)->orderBy('urutan', 'asc')->get();
        $permissionsGrouped = Permission::all()->groupBy('group');

        $selectedJabatan = $this->jabatan_id ? Jabatan::find($this->jabatan_id) : null;
        $selectedDesa = $this->desa_id ? Desa::find($this->desa_id) : null;

        return view('livewire.admin.hak-akses-index', [
            'desasList' => $desasList,
            'jabatansList' => $jabatansList,
            'permissionsGrouped' => $permissionsGrouped,
            'selectedJabatan' => $selectedJabatan,
            'selectedDesa' => $selectedDesa,
        ])->layout('layouts.app');
    }
}
