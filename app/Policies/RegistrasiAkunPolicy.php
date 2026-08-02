<?php

namespace App\Policies;

use App\Models\RegistrasiAkun;
use App\Models\User;

class RegistrasiAkunPolicy
{
    /**
     * Determine whether user can view list of account registrations.
     */
    public function viewAny(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('registrasi-akun.view');
    }

    /**
     * Determine whether user can view detail of an account registration.
     */
    public function view(User $user, RegistrasiAkun $registrasi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Pejabat check: Must be in same Desa
        if ($user->desa_id !== $registrasi->desa_id) {
            return false;
        }

        return $user->hasPermissionTo('registrasi-akun.detail') || $user->hasPermissionTo('registrasi-akun.view');
    }

    /**
     * Determine whether user can approve registration.
     */
    public function approve(User $user, RegistrasiAkun $registrasi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->desa_id !== $registrasi->desa_id) {
            return false;
        }

        return $user->hasPermissionTo('registrasi-akun.approve');
    }

    /**
     * Determine whether user can reject registration.
     */
    public function reject(User $user, RegistrasiAkun $registrasi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->desa_id !== $registrasi->desa_id) {
            return false;
        }

        return $user->hasPermissionTo('registrasi-akun.reject');
    }

    /**
     * Determine whether user can download KTP file.
     */
    public function downloadKtp(User $user, RegistrasiAkun $registrasi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->desa_id !== $registrasi->desa_id) {
            return false;
        }

        return $user->hasPermissionTo('registrasi-akun.download-ktp') || $user->hasPermissionTo('registrasi-akun.detail');
    }
}
