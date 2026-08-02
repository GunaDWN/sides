<?php

namespace App\Livewire;

use App\Enums\StatusApproval;
use App\Enums\StatusPengajuan;
use App\Enums\StatusRegistrasi;
use App\Models\Desa;
use App\Models\JenisSurat;
use App\Models\PengajuanApproval;
use App\Models\PengajuanSurat;
use App\Models\RegistrasiAkun;
use App\Models\User;
use App\Models\Warga;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $stats = [];

        if ($user->isAdmin()) {
            $stats['total_desa'] = Desa::count();
            $stats['total_warga'] = Warga::count();
            $stats['total_user'] = User::where('is_active', true)->count();
            $stats['registrasi_pending'] = RegistrasiAkun::where('status', StatusRegistrasi::MENUNGGU_APPROVAL)->count();
            $stats['total_jenis_surat'] = JenisSurat::count();
            $stats['pengajuan_hari_ini'] = PengajuanSurat::whereDate('created_at', now())->count();
            $stats['pengajuan_pending'] = PengajuanSurat::whereIn('status', [StatusPengajuan::DIAJUKAN, StatusPengajuan::MENUNGGU_APPROVAL])->count();
            $stats['pengajuan_selesai'] = PengajuanSurat::where('status', StatusPengajuan::SELESAI)->count();

            $recentPengajuans = PengajuanSurat::with(['warga', 'jenisSurat', 'desa'])->latest()->take(5)->get();
            $recentRegistrasis = RegistrasiAkun::with('desa')->latest()->take(5)->get();
        } else {
            // Warga stats
            $wargaId = $user->warga_id;
            $stats['total_pengajuan'] = PengajuanSurat::where('warga_id', $wargaId)->count();
            $stats['pengajuan_aktif'] = PengajuanSurat::where('warga_id', $wargaId)->whereIn('status', [StatusPengajuan::DIAJUKAN, StatusPengajuan::MENUNGGU_APPROVAL])->count();
            $stats['perlu_perbaikan'] = PengajuanSurat::where('warga_id', $wargaId)->where('status', StatusPengajuan::PERLU_PERBAIKAN)->count();
            $stats['selesai'] = PengajuanSurat::where('warga_id', $wargaId)->where('status', StatusPengajuan::SELESAI)->count();
            $stats['ditolak'] = PengajuanSurat::where('warga_id', $wargaId)->where('status', StatusPengajuan::DITOLAK)->count();

            $recentPengajuans = PengajuanSurat::where('warga_id', $wargaId)->with(['jenisSurat'])->latest()->take(5)->get();
            $recentRegistrasis = collect();

            // Pejabat inbox stats if applicable
            $activeJabatans = $user->getActiveJabatans()->pluck('jabatan_id')->toArray();
            if (count($activeJabatans) > 0) {
                $stats['pejabat_approval_pending'] = PengajuanApproval::whereIn('jabatan_id', $activeJabatans)
                    ->where('status', StatusApproval::AKTIF)
                    ->whereHas('pengajuanSurat', function ($q) use ($user) {
                        $q->where('desa_id', $user->desa_id);
                    })->count();
            }
        }

        return view('livewire.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentPengajuans' => $recentPengajuans,
            'recentRegistrasis' => $recentRegistrasis,
        ])->layout('layouts.app');
    }
}
