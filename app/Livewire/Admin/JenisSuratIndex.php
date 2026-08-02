<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\JenisSurat;
use App\Models\JenisSuratApproval;
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
    public $showModal = false;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['surat_id', 'desa_id', 'nama', 'kode', 'kategori', 'deskripsi', 'template', 'approvalSteps']);
        $this->butuh_approval = true;
        $this->is_active = true;

        if ($id) {
            $surat = JenisSurat::with(['approvals.jabatan'])->findOrFail($id);
            $this->surat_id = $surat->id;
            $this->desa_id = $surat->desa_id;
            $this->nama = $surat->nama;
            $this->kode = $surat->kode;
            $this->kategori = $surat->kategori;
            $this->deskripsi = $surat->deskripsi;
            $this->butuh_approval = $surat->butuh_approval;
            $this->is_active = $surat->is_active;
            $this->approvalSteps = $surat->approvals->map(fn($a) => [
                'jabatan_id' => $a->jabatan_id,
                'urutan' => $a->urutan,
            ])->toArray();
        } else {
            $this->desa_id = auth()->user()->desa_id ?? Desa::first()?->id;
        }

        $this->showModal = true;
    }

    public function addApprovalStep()
    {
        $nextUrutan = count($this->approvalSteps) + 1;
        $this->approvalSteps[] = ['jabatan_id' => '', 'urutan' => $nextUrutan];
    }

    public function removeApprovalStep($index)
    {
        unset($this->approvalSteps[$index]);
        $this->approvalSteps = array_values($this->approvalSteps);
        foreach ($this->approvalSteps as $i => &$step) {
            $step['urutan'] = $i + 1;
        }
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

        session()->flash('success', $this->surat_id ? 'Jenis surat berhasil diperbarui!' : 'Jenis surat baru berhasil ditambahkan!');
        $this->showModal = false;
    }

    public function render()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = JenisSurat::query()->with(['desa', 'approvals.jabatan']);

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
