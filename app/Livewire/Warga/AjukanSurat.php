<?php

namespace App\Livewire\Warga;

use App\Models\Jabatan;
use App\Models\JenisSurat;
use App\Services\LetterApprovalService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AjukanSurat extends Component
{
    use WithFileUploads;

    // Mode: 'template' | 'custom'
    public $mode = 'template';

    // Form Template
    public $jenis_surat_id = '';
    public $dokumen;
    public $catatan = '';

    // Form Custom
    public $perihalSurat = '';
    public $dokumenCustom;
    public $catatanCustom = '';
    public $selectedJabatanId = '';
    public $approvalSteps = []; // array of jabatan_id

    public $submitted = false;
    public $nomorPengajuan = '';

    public function switchMode($newMode)
    {
        $this->mode = $newMode;
        $this->resetValidation();
    }

    public function addApprovalStep()
    {
        if (!$this->selectedJabatanId) {
            return;
        }

        if (in_array($this->selectedJabatanId, $this->approvalSteps)) {
            $this->addError('selectedJabatanId', 'Jabatan ini sudah ada di dalam alur persetujuan.');
            return;
        }

        $this->approvalSteps[] = (int) $this->selectedJabatanId;
        $this->selectedJabatanId = '';
        $this->resetValidation('selectedJabatanId');
    }

    public function removeApprovalStep($index)
    {
        if (isset($this->approvalSteps[$index])) {
            array_splice($this->approvalSteps, $index, 1);
        }
    }

    public function moveStepUp($index)
    {
        if ($index > 0 && isset($this->approvalSteps[$index])) {
            $temp = $this->approvalSteps[$index - 1];
            $this->approvalSteps[$index - 1] = $this->approvalSteps[$index];
            $this->approvalSteps[$index] = $temp;
        }
    }

    public function moveStepDown($index)
    {
        if ($index < count($this->approvalSteps) - 1 && isset($this->approvalSteps[$index])) {
            $temp = $this->approvalSteps[$index + 1];
            $this->approvalSteps[$index + 1] = $this->approvalSteps[$index];
            $this->approvalSteps[$index] = $temp;
        }
    }

    public function submit(LetterApprovalService $service)
    {
        $user = auth()->user();

        if ($this->mode === 'template') {
            $this->validate([
                'jenis_surat_id' => 'required|exists:jenis_surats,id',
                'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240',
            ], [
                'dokumen.max' => 'Ukuran file dokumen maksimal 10MB.',
            ]);

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

        } else {
            // Mode Custom
            $this->validate([
                'perihalSurat' => 'required|string|min:3|max:255',
                'dokumenCustom' => 'required|file|mimes:doc,docx,pdf|max:10240',
                'approvalSteps' => 'required|array|min:1',
            ], [
                'perihalSurat.required' => 'Perihal atau judul surat kustom wajib diisi.',
                'dokumenCustom.required' => 'File dokumen lampiran wajib diunggah.',
                'dokumenCustom.max' => 'Ukuran file dokumen maksimal 10MB.',
                'approvalSteps.required' => 'Anda harus menentukan setidaknya 1 pejabat penerima persetujuan.',
                'approvalSteps.min' => 'Anda harus menentukan setidaknya 1 pejabat penerima persetujuan.',
            ]);

            $path = $this->dokumenCustom->store('pengajuan-dokumen', 'local');

            $pengajuan = $service->createCustomPengajuan(
                $user,
                $this->perihalSurat,
                [
                    'nama_file_asli' => $this->dokumenCustom->getClientOriginalName(),
                    'file_path' => $path,
                    'file_extension' => $this->dokumenCustom->getClientOriginalExtension(),
                    'mime_type' => $this->dokumenCustom->getClientMimeType(),
                    'file_size' => $this->dokumenCustom->getSize(),
                ],
                $this->approvalSteps,
                $this->catatanCustom,
                request()->ip(),
                request()->userAgent()
            );

            $this->nomorPengajuan = $pengajuan->nomor_pengajuan;
            $this->submitted = true;
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Query jenis surat template yang aktif di desa user
        $jenisSurats = JenisSurat::where('desa_id', $user->desa_id)
            ->where('is_active', true)
            ->whereNotNull('template_path')
            ->with('approvals.jabatan')
            ->get();

        $selectedSurat = $this->jenis_surat_id
            ? $jenisSurats->firstWhere('id', $this->jenis_surat_id)
            : null;

        // Query Jabatan aktif di desa user untuk dropdown persetujuan kustom
        $availableJabatans = Jabatan::where('desa_id', $user->desa_id)
            ->where('is_active', true)
            ->orderBy('urutan', 'asc')
            ->get();

        return view('livewire.warga.ajukan-surat', [
            'jenisSurats' => $jenisSurats,
            'selectedSurat' => $selectedSurat,
            'availableJabatans' => $availableJabatans,
        ])->layout('layouts.app');
    }
}
