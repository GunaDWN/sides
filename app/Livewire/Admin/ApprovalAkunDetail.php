<?php

namespace App\Livewire\Admin;

use App\Enums\StatusRegistrasi;
use App\Models\RegistrasiAkun;
use App\Services\RegistrationService;
use Livewire\Component;

class ApprovalAkunDetail extends Component
{
    public RegistrasiAkun $registrasi;
    public $catatan = '';
    public $alasanPenolakan = '';
    public $showRejectModal = false;

    public function mount(RegistrasiAkun $registrasi)
    {
        $user = auth()->user();
        if ($user->cannot('view', $registrasi)) {
            abort(403, 'Anda tidak berhak mengakses detail pengajuan registrasi akun ini.');
        }

        $this->registrasi = $registrasi->load(['desa', 'diprosesOleh', 'logs.user']);
    }

    public function approve(RegistrationService $service)
    {
        $user = auth()->user();
        if ($user->cannot('approve', $this->registrasi)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk menyetujui akun ini.');
            return;
        }

        try {
            $service->approveRegistration(
                $this->registrasi->id,
                $user,
                $this->catatan,
                request()->ip(),
                request()->userAgent()
            );

            session()->flash('success', 'Pengajuan registrasi akun berhasil disetujui! Akun pengguna dan data warga telah diaktifkan.');
            $this->registrasi->refresh();
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function reject(RegistrationService $service)
    {
        $this->validate([
            'alasanPenolakan' => 'required|min:5',
        ], [
            'alasanPenolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasanPenolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user = auth()->user();
        if ($user->cannot('reject', $this->registrasi)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk menolak akun ini.');
            return;
        }

        try {
            $service->rejectRegistration(
                $this->registrasi->id,
                $user,
                $this->alasanPenolakan,
                request()->ip(),
                request()->userAgent()
            );

            $this->showRejectModal = false;
            session()->flash('success', 'Pengajuan registrasi akun telah ditolak.');
            $this->registrasi->refresh();
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.approval-akun-detail')->layout('layouts.app');
    }
}
