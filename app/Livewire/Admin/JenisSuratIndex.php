<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\JenisSurat;
use App\Models\JenisSuratApproval;
use App\Models\JenisSuratSignaturePlacement;
use App\Services\TemplatePreviewService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class JenisSuratIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    public $surat_id = null;
    public $desa_id = '';
    public $nama = '';
    public $kode = '';
    public $kategori = '';
    public $deskripsi = '';
    public $template;
    public $butuh_approval = true;
    public $is_active = true;

    public $approvalSteps = [];
    public $signaturePlacements = [];
    public $showModal = false;
    public $showSignatureEditor = false;
    public $existingTemplatePath = null;
    public $existingTemplateName = null;
    public $existingTemplateSize = null;
    public $existingTemplateExtension = null;
    public $isChangingTemplate = false;

    // Preview data
    public $previewPages = [];
    public $previewPageWidthMm = 210;
    public $previewPageHeightMm = 297;
    public $currentPreviewPage = 1;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset([
            'surat_id', 'desa_id', 'nama', 'kode', 'kategori', 'deskripsi', 'template',
            'approvalSteps', 'signaturePlacements', 'existingTemplatePath',
            'existingTemplateName', 'existingTemplateSize', 'existingTemplateExtension', 'isChangingTemplate',
            'showSignatureEditor', 'previewPages', 'currentPreviewPage',
        ]);
        $this->butuh_approval = true;
        $this->is_active = true;
        $this->previewPageWidthMm = 210;
        $this->previewPageHeightMm = 297;

        if ($id) {
            $surat = JenisSurat::with(['approvals.jabatan', 'signaturePlacements'])->findOrFail($id);
            $this->surat_id = $surat->id;
            $this->desa_id = $surat->desa_id;
            $this->nama = $surat->nama;
            $this->kode = $surat->kode;
            $this->kategori = $surat->kategori;
            $this->deskripsi = $surat->deskripsi;
            $this->butuh_approval = $surat->butuh_approval;
            $this->is_active = $surat->is_active;
            $this->existingTemplatePath = $surat->template_path;

            if ($surat->template_path && Storage::disk('local')->exists($surat->template_path)) {
                $this->existingTemplateName = basename($surat->template_path);
                $this->existingTemplateSize = Storage::disk('local')->size($surat->template_path);
                $this->existingTemplateExtension = strtolower(pathinfo($surat->template_path, PATHINFO_EXTENSION));
            }

            $this->approvalSteps = $surat->approvals->map(fn($a) => [
                'jabatan_id' => $a->jabatan_id,
                'urutan' => $a->urutan,
            ])->toArray();

            // Load existing signature placements
            $this->signaturePlacements = [];
            foreach ($surat->signaturePlacements as $sp) {
                $this->signaturePlacements[$sp->jabatan_id] = [
                    'pos_x' => $sp->pos_x ?? 0,
                    'pos_y' => $sp->pos_y ?? 0,
                    'halaman' => $sp->halaman ?? 1,
                    'lebar' => $sp->lebar ?? 40,
                    'tinggi' => $sp->tinggi ?? 20,
                    'tampilkan_nama' => $sp->tampilkan_nama,
                    'tampilkan_jabatan' => $sp->tampilkan_jabatan,
                    'tampilkan_stempel' => $sp->tampilkan_stempel,
                ];
            }

            // Generate preview if template exists
            if ($surat->template_path) {
                $this->generatePreview($surat->template_path);
            }
        } else {
            $this->desa_id = auth()->user()->desa_id ?? Desa::first()?->id;
        }

        $this->showModal = true;
    }

    /**
     * Generate preview images from the template file.
     */
    public function generatePreview(?string $templatePath = null): void
    {
        $path = $templatePath ?? $this->existingTemplatePath;
        if (!$path) {
            return;
        }

        try {
            $service = app(TemplatePreviewService::class);
            $result = $service->generatePreview($path);
            $this->previewPages = $result['pages'];
            $this->previewPageWidthMm = $result['page_width_mm'];
            $this->previewPageHeightMm = $result['page_height_mm'];
            $this->currentPreviewPage = 1;
        } catch (\Throwable $e) {
            $this->previewPages = [];
            report($e);
        }
    }

    /**
     * Called after template file is uploaded via Livewire.
     */
    public function updatedTemplate(): void
    {
        if (!$this->template) {
            return;
        }

        // Validate the upload
        $this->validate([
            'template' => 'file|mimes:doc,docx,pdf|max:10240',
        ]);

        $this->existingTemplateName = $this->template->getClientOriginalName();
        $this->existingTemplateSize = $this->template->getSize();
        $this->existingTemplateExtension = strtolower($this->template->getClientOriginalExtension());
        $this->isChangingTemplate = false;

        // Store temporarily and generate preview
        $tempPath = $this->template->store('surat-templates-temp', 'local');
        $this->existingTemplatePath = $tempPath;
        $this->generatePreview($tempPath);
    }

    public function removeTemplate()
    {
        $this->template = null;
        $this->existingTemplatePath = null;
        $this->existingTemplateName = null;
        $this->existingTemplateSize = null;
        $this->existingTemplateExtension = null;
        $this->previewPages = [];
        $this->signaturePlacements = [];
        $this->isChangingTemplate = true;
    }

    public function toggleChangeTemplate()
    {
        $this->isChangingTemplate = !$this->isChangingTemplate;
    }

    public function getFormattedTemplateSize(): string
    {
        if (!$this->existingTemplateSize) {
            return '';
        }

        if ($this->existingTemplateSize >= 1048576) {
            return number_format($this->existingTemplateSize / 1048576, 2) . ' MB';
        }

        return number_format($this->existingTemplateSize / 1024, 1) . ' KB';
    }

    public function openSignatureEditor()
    {
        if (empty($this->previewPages)) {
            session()->flash('error', 'Upload template terlebih dahulu untuk membuka editor tanda tangan.');
            return;
        }

        // Initialize placements for jabatan that don't have one yet
        foreach ($this->approvalSteps as $step) {
            if (!empty($step['jabatan_id']) && !isset($this->signaturePlacements[$step['jabatan_id']])) {
                $this->signaturePlacements[$step['jabatan_id']] = [
                    'pos_x' => 20 + (count($this->signaturePlacements) * 50),
                    'pos_y' => $this->previewPageHeightMm - 60,
                    'halaman' => count($this->previewPages), // Last page
                    'lebar' => 40,
                    'tinggi' => 20,
                    'tampilkan_nama' => true,
                    'tampilkan_jabatan' => true,
                    'tampilkan_stempel' => false,
                ];
            }
        }

        $this->showSignatureEditor = true;
    }

    public function closeSignatureEditor()
    {
        $this->showSignatureEditor = false;
    }

    /**
     * Update signature placement position from the visual editor (called via Alpine dispatch).
     */
    public function updatePlacementPosition($jabatanId, $posX, $posY, $halaman, $lebar = null, $tinggi = null)
    {
        if (!isset($this->signaturePlacements[$jabatanId])) {
            $this->signaturePlacements[$jabatanId] = [
                'pos_x' => round((float) $posX, 1),
                'pos_y' => round((float) $posY, 1),
                'halaman' => (int) $halaman,
                'lebar' => $lebar ? round((float) $lebar, 1) : 40,
                'tinggi' => $tinggi ? round((float) $tinggi, 1) : 20,
                'tampilkan_nama' => true,
                'tampilkan_jabatan' => true,
                'tampilkan_stempel' => false,
            ];
            return;
        }

        $this->signaturePlacements[$jabatanId]['pos_x'] = round((float) $posX, 1);
        $this->signaturePlacements[$jabatanId]['pos_y'] = round((float) $posY, 1);
        $this->signaturePlacements[$jabatanId]['halaman'] = (int) $halaman;

        if ($lebar !== null) {
            $this->signaturePlacements[$jabatanId]['lebar'] = round((float) $lebar, 1);
        }
        if ($tinggi !== null) {
            $this->signaturePlacements[$jabatanId]['tinggi'] = round((float) $tinggi, 1);
        }
    }

    public function removePlacementPosition($jabatanId)
    {
        if (isset($this->signaturePlacements[$jabatanId])) {
            unset($this->signaturePlacements[$jabatanId]);
        }
    }

    public function syncAllPlacements(array $placements)
    {
        $this->signaturePlacements = $placements;
    }

    public function addApprovalStep()
    {
        $nextUrutan = count($this->approvalSteps) + 1;
        $this->approvalSteps[] = ['jabatan_id' => '', 'urutan' => $nextUrutan];
    }

    public function removeApprovalStep($index)
    {
        $removedJabatanId = $this->approvalSteps[$index]['jabatan_id'] ?? null;

        unset($this->approvalSteps[$index]);
        $this->approvalSteps = array_values($this->approvalSteps);
        foreach ($this->approvalSteps as $i => &$step) {
            $step['urutan'] = $i + 1;
        }

        // Also remove signature placement for removed jabatan
        if ($removedJabatanId && isset($this->signaturePlacements[$removedJabatanId])) {
            unset($this->signaturePlacements[$removedJabatanId]);
        }
    }

    /**
     * Get file extension of the template (existing or newly uploaded).
     */
    public function getTemplateExtension(): ?string
    {
        if ($this->existingTemplateExtension) {
            return $this->existingTemplateExtension;
        }

        if ($this->template) {
            return strtolower($this->template->getClientOriginalExtension());
        }

        if ($this->existingTemplatePath) {
            return strtolower(pathinfo($this->existingTemplatePath, PATHINFO_EXTENSION));
        }

        return null;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|max:150',
            'desa_id' => 'required|exists:desas,id',
            'template' => 'nullable|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $data = [
            'desa_id' => $this->desa_id,
            'nama' => $this->nama,
            'kode' => $this->kode ?: strtoupper(substr(str_replace(' ', '', $this->nama), 0, 5)),
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'butuh_approval' => $this->butuh_approval,
            'is_active' => $this->is_active,
        ];

        if ($this->template) {
            $data['template_path'] = $this->template->store('surat-templates', 'local');
        } elseif (!$this->existingTemplatePath) {
            $data['template_path'] = null;
        }

        if ($this->surat_id) {
            $surat = JenisSurat::findOrFail($this->surat_id);
            $data['updated_by'] = auth()->id();
            $surat->update($data);
        } else {
            $data['created_by'] = auth()->id();
            $surat = JenisSurat::create($data);
        }

        // Sync approval steps
        JenisSuratApproval::where('jenis_surat_id', $surat->id)->delete();
        if ($this->butuh_approval) {
            foreach ($this->approvalSteps as $step) {
                if (!empty($step['jabatan_id'])) {
                    JenisSuratApproval::create([
                        'jenis_surat_id' => $surat->id,
                        'jabatan_id' => $step['jabatan_id'],
                        'urutan' => $step['urutan'],
                    ]);
                }
            }
        }

        // Sync signature placements (coordinate-based from visual editor)
        JenisSuratSignaturePlacement::where('jenis_surat_id', $surat->id)->delete();
        if ($this->butuh_approval) {
            foreach ($this->signaturePlacements as $jabatanId => $placement) {
                $isInApprovalSteps = collect($this->approvalSteps)
                    ->contains(fn($step) => (int) $step['jabatan_id'] === (int) $jabatanId);

                if (!$isInApprovalSteps) {
                    continue;
                }

                $hasCoordinates = isset($placement['pos_x']) && isset($placement['pos_y']);

                if ($hasCoordinates) {
                    JenisSuratSignaturePlacement::create([
                        'jenis_surat_id' => $surat->id,
                        'jabatan_id' => $jabatanId,
                        'pos_x' => (float) $placement['pos_x'],
                        'pos_y' => (float) $placement['pos_y'],
                        'halaman' => (int) ($placement['halaman'] ?? 1),
                        'lebar' => (float) ($placement['lebar'] ?? 40),
                        'tinggi' => (float) ($placement['tinggi'] ?? 20),
                        'tampilkan_nama' => $placement['tampilkan_nama'] ?? true,
                        'tampilkan_jabatan' => $placement['tampilkan_jabatan'] ?? true,
                        'tampilkan_stempel' => $placement['tampilkan_stempel'] ?? false,
                    ]);
                }
            }
        }

        session()->flash('success', $this->surat_id ? 'Jenis surat berhasil diperbarui!' : 'Jenis surat baru berhasil ditambahkan!');
        $this->showModal = false;
        $this->showSignatureEditor = false;
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = JenisSurat::query()->with(['desa', 'approvals.jabatan', 'signaturePlacements']);

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $surats = $query->latest()->paginate(10);
        $desasList = Desa::where('is_active', true)->get();

        $jabatansByDesa = [];
        if ($this->desa_id) {
            $jabatansByDesa = Jabatan::where('desa_id', $this->desa_id)->where('is_active', true)->orderBy('urutan')->get();
        }

        return view('livewire.admin.jenis-surat-index', [
            'surats' => $surats,
            'desasList' => $desasList,
            'jabatansByDesa' => $jabatansByDesa,
        ])->layout('layouts.app');
    }
}
