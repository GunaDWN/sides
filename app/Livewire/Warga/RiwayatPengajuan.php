<?php

namespace App\Livewire\Warga;

use App\Models\PengajuanSurat;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatPengajuan extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = PengajuanSurat::where('warga_id', $user->warga_id)
            ->with(['jenisSurat', 'desa', 'latestDokumen']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nomor_pengajuan', 'like', $s)
                    ->orWhere('perihal_surat', 'like', $s)
                    ->orWhereHas('jenisSurat', fn($q2) => $q2->where('nama', 'like', $s));
            });
        }

        $pengajuans = $query->latest()->paginate(10);

        return view('livewire.warga.riwayat-pengajuan', [
            'pengajuans' => $pengajuans,
        ])->layout('layouts.app');
    }
}
