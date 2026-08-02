<?php

namespace App\Livewire\Pejabat;

use App\Enums\StatusApproval;
use App\Models\PengajuanApproval;
use Livewire\Component;
use Livewire\WithPagination;

class InboxApprovalSurat extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'aktif';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Admin can view all approvals in system
            $query = PengajuanApproval::query()
                ->with(['pengajuanSurat.jenisSurat', 'pengajuanSurat.warga', 'jabatan']);
        } else {
            $warga = $user->warga;
            if (!$warga) {
                abort(403, 'Akun Anda tidak terhubung dengan data warga.');
            }

            $jabatanIds = $warga->activeWargaJabatan->pluck('jabatan_id');
            if ($jabatanIds->isEmpty()) {
                $query = PengajuanApproval::where('id', 0);
            } else {
                $query = PengajuanApproval::whereIn('jabatan_id', $jabatanIds)
                    ->with(['pengajuanSurat.jenisSurat', 'pengajuanSurat.warga', 'jabatan']);
            }
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->whereHas('pengajuanSurat', function ($q) use ($s) {
                $q->where('nomor_pengajuan', 'like', $s)
                    ->orWhereHas('jenisSurat', fn($q2) => $q2->where('nama', 'like', $s));
            });
        }

        $approvals = $query->latest('created_at')->paginate(10);

        return view('livewire.pejabat.inbox-approval-surat', [
            'approvals' => $approvals,
        ])->layout('layouts.app');
    }
}
