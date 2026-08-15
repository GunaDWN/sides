<?php

namespace App\Livewire\Pejabat;

use App\Models\PengajuanApproval;
use App\Services\LetterApprovalService;
use Livewire\Component;

class DetailApprovalSurat extends Component
{
    public PengajuanApproval $approval;

    public $komentar = '';
    public $aksi = ''; // 'setuju', 'tolak', 'perbaiki'

    public function mount(PengajuanApproval $approval)
    {
        $user = auth()->user();
        $warga = $user->warga;

        if (!$warga && !$user->isAdmin()) {
            abort(403);
        }

        // Must be assigned to one of user's active jabatan
        if ($warga) {
            $jabatanIds = $warga->activeWargaJabatan->pluck('jabatan_id');
            if (!$jabatanIds->contains($approval->jabatan_id) && !$user->isAdmin()) {
                abort(403, 'Anda tidak berwenang atas pengajuan ini.');
            }
        }

        $this->approval = $approval->load([
            'pengajuanSurat.jenisSurat.signaturePlacements',
            'pengajuanSurat.warga',
            'pengajuanSurat.desa',
            'pengajuanSurat.approvals.jabatan',
            'pengajuanSurat.latestDokumen',
            'pengajuanSurat.logs.user',
            'jabatan',
        ]);
    }

    public function processApproval(LetterApprovalService $service)
    {
        $this->validate([
            'aksi' => 'required|in:setuju,tolak,perbaiki',
        ]);

        $user = auth()->user();
        $pengajuan = $this->approval->pengajuanSurat;

        // Map UI action to service decision
        $decisionMap = [
            'setuju' => 'terima',
            'tolak' => 'tolak',
            'perbaiki' => 'ulangi',
        ];

        try {
            if (in_array($this->aksi, ['tolak', 'perbaiki'])) {
                $this->validate(
                    ['komentar' => 'required|min:10'],
                    [
                        'komentar.required' => $this->aksi === 'tolak' ? 'Alasan penolakan wajib diisi.' : 'Catatan perbaikan wajib diisi.',
                        'komentar.min' => ($this->aksi === 'tolak' ? 'Alasan penolakan' : 'Catatan perbaikan') . ' minimal 10 karakter.',
                    ]
                );
            }

            $service->processApproval(
                $pengajuan->id,
                $user,
                $decisionMap[$this->aksi],
                null,
                $this->komentar ?: null,
                request()->ip(),
                request()->userAgent()
            );

            $successMessages = [
                'setuju' => 'Pengajuan berhasil disetujui.',
                'tolak' => 'Pengajuan ditolak.',
                'perbaiki' => 'Permintaan perbaikan dikirim ke pemohon.',
            ];

            session()->flash('success', $successMessages[$this->aksi]);
            $this->approval->refresh();
            $this->approval->load(['pengajuanSurat.approvals.jabatan', 'pengajuanSurat.logs.user']);
            $this->reset(['komentar', 'aksi']);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pejabat.detail-approval-surat')->layout('layouts.app');
    }
}
