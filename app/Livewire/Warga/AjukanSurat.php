<?php

namespace App\Livewire\Warga;

use App\Models\JenisSurat;
use App\Services\LetterApprovalService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AjukanSurat extends Component
{
    use WithFileUploads;

    public $jenis_surat_id = '';
    public $dokumen;
    public $catatan = '';

    public $submitted = false;
    public $nomorPengajuan = '';

    public function submit(LetterApprovalService $service)
    {
        $this->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ], [
            'dokumen.max' => 'Ukuran file dokumen maksimal 10MB.',
        ]);

        $user = auth()->user();
        $jenisSurat = JenisSurat::findOrFail($this->jenis_surat_id);

        $path = $this->dokumen->store('pengajuan-dokumen', 'local');

        $pengajuan = $service->createPengajuan(
            $user,
            $jenisSurat,
            [
                'nama_file_asli' => $this->dokumen->getClientOriginalName(),
                'file_path' => $path,
                'file_extension' => $this->dokumen->getClientOriginalExtension(),
                'mime_type' => $this->dokumen->getClientMimeType(),
                'file_size' => $this->dokumen->getSize(),
            ],
            $this->catatan,
            request()->ip(),
            request()->userAgent()
        );

        $this->nomorPengajuan = $pengajuan->nomor_pengajuan;
        $this->submitted = true;
    }

    public function render()
    {
        $user = auth()->user();
        $jenisSurats = JenisSurat::where('desa_id', $user->desa_id)
            ->where('is_active', true)
            ->whereNotNull('template_path')
            ->with('approvals.jabatan')
            ->get();

        $selectedSurat = $this->jenis_surat_id
            ? $jenisSurats->firstWhere('id', $this->jenis_surat_id)
            : null;

        return view('livewire.warga.ajukan-surat', [
            'jenisSurats' => $jenisSurats,
            'selectedSurat' => $selectedSurat,
        ])->layout('layouts.app');
    }
}
