<?php

namespace App\Policies;

use App\Models\PengajuanSurat;
use App\Models\User;

class PengajuanSuratPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PengajuanSurat $pengajuan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Applicant can view own
        if ($user->warga_id && $user->warga_id === $pengajuan->warga_id) {
            return true;
        }

        // Pejabat in same desa
        if ($user->desa_id === $pengajuan->desa_id) {
            $activeJabatans = $user->getActiveJabatans()->pluck('jabatan_id')->toArray();
            $targetJabatanId = $pengajuan->activeApproval?->jabatan_id;

            if ($targetJabatanId && in_array($targetJabatanId, $activeJabatans)) {
                return true;
            }

            if ($user->hasPermissionTo('pengajuan-surat.approve') || $user->hasPermissionTo('pengajuan-surat.download')) {
                return true;
            }
        }

        return false;
    }

    public function approve(User $user, PengajuanSurat $pengajuan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->desa_id !== $pengajuan->desa_id) {
            return false;
        }

        $activeJabatans = $user->getActiveJabatans()->pluck('jabatan_id')->toArray();
        $targetJabatanId = $pengajuan->activeApproval?->jabatan_id;

        if ($targetJabatanId && in_array($targetJabatanId, $activeJabatans)) {
            return true;
        }

        return $user->hasPermissionTo('pengajuan-surat.approve');
    }

    public function downloadDocument(User $user, PengajuanSurat $pengajuan): bool
    {
        return $this->view($user, $pengajuan);
    }
}
