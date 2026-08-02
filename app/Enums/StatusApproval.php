<?php

namespace App\Enums;

enum StatusApproval: string
{
    case MENUNGGU = 'menunggu';
    case AKTIF = 'aktif';
    case DITERIMA = 'diterima';
    case DITOLAK = 'ditolak';
    case ULANGI = 'ulangi';
    case DILEWATI = 'dilewati';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::MENUNGGU => 'Menunggu Queue',
            self::AKTIF => 'Menunggu Persetujuan',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            self::ULANGI => 'Meminta Perbaikan (Ulangi)',
            self::DILEWATI => 'Dilewati',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::MENUNGGU => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
            self::AKTIF => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400 border-sky-200 dark:border-sky-800',
            self::DITERIMA => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
            self::DITOLAK => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
            self::ULANGI => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
            self::DILEWATI, self::DIBATALKAN => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400 border-gray-200 dark:border-gray-700',
        };
    }
}
