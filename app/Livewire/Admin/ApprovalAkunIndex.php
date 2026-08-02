<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\RegistrasiAkun;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalAkunIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $desaFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function maskNik(string $nik): string
    {
        if (strlen($nik) < 16) return $nik;
        return substr($nik, 0, 6) . '******' . substr($nik, -4);
    }

    public function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) < 2) return $email;
        $name = $parts[0];
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(1, strlen($name) - 2));
        return $maskedName . '@' . $parts[1];
    }

    public function render()
    {
        $user = auth()->user();

        if (!($user->isAdmin() || $user->hasPermissionTo('registrasi-akun.view'))) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan registrasi akun.');
        }

        $query = RegistrasiAkun::query()->with(['desa', 'diprosesOleh']);
      if (!$user->isAdmin()) {
            $query->where('desa_id', $user->desa_id);
        } elseif ($this->desaFilter) {
            $query->where('desa_id', $this->desaFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('kode_registrasi', 'like', $s)
                    ->orWhere('nama_lengkap', 'like', $s)
                    ->orWhere('nik', 'like', $s)
                    ->orWhere('email', 'like', $s);
            });
        }

        $registrasis = $query->latest()->paginate(10);
        $desasList = $user->isAdmin() ? Desa::where('is_active', true)->get() : collect();

        return view('livewire.admin.approval-akun-index', [
            'registrasis' => $registrasis,
            'desasList' => $desasList,
            'user' => $user,
        ])->layout('layouts.app');
    }
}
