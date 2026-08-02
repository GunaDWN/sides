<?php

namespace App\Livewire\Warga;

use App\Enums\StatusPengajuan;
use App\Models\PengajuanSurat;
use App\Services\LetterApprovalService;
use Livewire\Component;
use Livewire\WithFileUploads;

class DetailPengajuan extends Component
{
    use WithFileUploads;

    public PengajuanSurat $pengajuan;
    public $dokumenPerbaikan;
    public $catatanPerbaikan = '';

    public function mount(PengajuanSurat $pengajuan)
    {
        $user = auth()->user();

        // Warga can only view own, Admin can view all
        if (!$user->isAdmin() && $user->warga_id !== $pengajuan->warga_id) {
            abort(403, 'Anda tidak berhak mengakses pengajuan ini.');
        }

        $this->pengajuan = $pengajuan->load([
            'jenisSurat',
            'desa',
            'warga',
            'approvals.jabatan',
            'approvals.processedBy',
            'dokumens.uploadedBy',
            'logs.user',
        ]);
    }

    public function resubmit(LetterApprovalService $service)
    {
        $this->validate([
            'dokumenPerbaikan' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $user = auth()->user();
        $path = $this->dokumenPerbaikan->store('pengajuan-dokumen', 'local');

        try {
            $service->resubmitDocument(
                $this->pengajuan,
                $user,
                [
                    'nama_file_asli' => $this->dokumenPerbaikan->getClientOriginalName(),
                    'file_path' => $path,
                    'file_extension' => $this->dokumenPerbaikan->getClientOriginalExtension(),
                    'mime_type' => $this->dokumenPerbaikan->getClientMimeType(),
                    'file_size' => $this->dokumenPerbaikan->getSize(),
                ],
                $this->catatanPerbaikan ?: null,
                request()->ip(),
                request()->userAgent()
            );

            session()->flash('success', 'Dokumen perbaikan berhasil diunggah ulang dan pengajuan dilanjutkan ke pejabat.');
            $this->pengajuan->refresh();
            $this->pengajuan->load(['approvals.jabatan', 'approvals.processedBy', 'dokumens.uploadedBy', 'logs.user']);
            $this->reset(['dokumenPerbaikan', 'catatanPerbaikan']);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal mengunggah ulang: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.warga.detail-pengajuan')->layout('layouts.app');
    }
}
