<?php

namespace App\Enums;

enum StatusPengajuan: string
{
    case DRAFT = 'draft';
    case DIAJUKAN = 'diajukan';
    case MENUNGGU_APPROVAL = 'menunggu_approval';
    case PERLU_PERBAIKAN = 'perlu_perbaikan';
    case DITOLAK = 'ditolak';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::DIAJUKAN => 'Diajukan',
            self::MENUNGGU_APPROVAL => 'Menunggu Approval',
            self::PERLU_PERBAIKAN => 'Perlu Perbaikan',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT => 'bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400 border-slate-200 dark:border-slate-800',
            self::DIAJUKAN, self::MENUNGGU_APPROVAL => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
            self::PERLU_PERBAIKAN => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
            self::DITOLAK => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
            self::SELESAI => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
            self::DIBATALKAN => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 border-gray-200 dark:border-gray-800',
        };
    }
}
