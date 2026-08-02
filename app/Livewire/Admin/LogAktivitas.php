<?php

namespace App\Livewire\Admin;

use App\Models\PengajuanLog;
use App\Models\RegistrasiAkunLog;
use Livewire\Component;
use Livewire\WithPagination;

class LogAktivitas extends Component
{
    use WithPagination;

    public $search = '';
    public $tabActive = 'registrasi'; // registrasi | pengajuan

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->tabActive = $tab;
        $this->resetPage();
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($this->tabActive === 'registrasi') {
            $query = RegistrasiAkunLog::query()->with(['registrasiAkun', 'user']);
            if ($this->search) {
                $s = '%' . trim($this->search) . '%';
                $query->where('action', 'like', $s)->orWhere('catatan', 'like', $s);
            }
            $logs = $query->latest('created_at')->paginate(15);
        } else {
            $query = PengajuanLog::query()->with(['pengajuanSurat', 'user']);
            if ($this->search) {
                $s = '%' . trim($this->search) . '%';
                $query->where('action', 'like', $s)->orWhere('komentar', 'like', $s);
            }
            $logs = $query->latest('created_at')->paginate(15);
        }

        return view('livewire.admin.log-aktivitas', [
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
