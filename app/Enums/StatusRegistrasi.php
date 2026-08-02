<?php

namespace App\Enums;

enum StatusRegistrasi: string
{
    case MENUNGGU_APPROVAL = 'menunggu_approval';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::MENUNGGU_APPROVAL => 'Menunggu Approval',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::MENUNGGU_APPROVAL => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
            self::DISETUJUI => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
            self::DITOLAK => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
            self::DIBATALKAN => 'bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400 border-slate-200 dark:border-slate-800',
        };
    }
}
