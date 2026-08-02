<?php

namespace App\Services;

use App\Enums\StatusApproval;
use App\Enums\StatusPengajuan;
use App\Models\JenisSurat;
use App\Models\PengajuanApproval;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanLog;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LetterApprovalService
{
    /**
     * Create a new pengajuan surat by Warga
     */
    public function createPengajuan(
        User $user,
        JenisSurat $jenisSurat,
        array $dokumenData,
        ?string $catatanPemohon = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): PengajuanSurat {
        return DB::transaction(function () use ($user, $jenisSurat, $dokumenData, $catatanPemohon, $ipAddress, $userAgent) {
            $nomorPengajuan = 'SURAT-' . strtoupper(Str::random(4)) . '-' . date('YmdHis');

            $needsApproval = $jenisSurat->butuh_approval && $jenisSurat->approvals()->count() > 0;
            $initialStatus = $needsApproval ? StatusPengajuan::MENUNGGU_APPROVAL : StatusPengajuan::SELESAI;

            $pengajuan = PengajuanSurat::create([
                'nomor_pengajuan' => $nomorPengajuan,
                'desa_id' => $user->desa_id,
                'jenis_surat_id' => $jenisSurat->id,
                'warga_id' => $user->warga_id,
                'status' => $initialStatus,
                'tahapan_aktif' => 1,
                'catatan_pemohon' => $catatanPemohon,
                'submitted_at' => now(),
                'completed_at' => $needsApproval ? null : now(),
                'created_by' => $user->id,
            ]);

            // Create initial document (v1)
            $dokumen = PengajuanDokumen::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'pengajuan_approval_id' => null,
                'versi' => 1,
                'nama_file_asli' => $dokumenData['nama_file_asli'],
                'file_path' => $dokumenData['file_path'],
                'file_extension' => $dokumenData['file_extension'] ?? null,
                'mime_type' => $dokumenData['mime_type'] ?? null,
                'file_size' => $dokumenData['file_size'] ?? null,
                'sumber' => 'warga',
                'uploaded_by' => $user->id,
                'keterangan' => 'Dokumen awal pengajuan surat.',
                'is_latest' => true,
            ]);

            // Create snapshot of approvals if needed
            if ($needsApproval) {
                $approvals = $jenisSurat->approvals()->orderBy('urutan', 'asc')->get();
                foreach ($approvals as $app) {
                    $isFirstStep = ($app->urutan === 1);
                    PengajuanApproval::create([
                        'pengajuan_surat_id' => $pengajuan->id,
                        'jabatan_id' => $app->jabatan_id,
                        'nama_jabatan_snapshot' => $app->jabatan->nama ?? 'Jabatan',
                        'urutan' => $app->urutan,
                        'status' => $isFirstStep ? StatusApproval::AKTIF : StatusApproval::MENUNGGU,
                        'activated_at' => $isFirstStep ? now() : null,
                    ]);
                }
            }

            // Write Log
            PengajuanLog::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'user_id' => $user->id,
                'action' => 'SUBMIT_PENGAJUAN',
                'status_sebelum' => StatusPengajuan::DRAFT->value,
                'status_sesudah' => $initialStatus->value,
                'komentar' => $catatanPemohon ?? 'Surat berhasil diajukan.',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $pengajuan;
        });
    }

    /**
     * Process Pejabat Decision (Terima, Tolak, Ulangi)
     */
    public function processApproval(
        int $pengajuanId,
        User $pejabatUser,
        string $decision, // 'terima', 'tolak', 'ulangi'
        ?array $newDokumenData = null,
        ?string $komentar = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): PengajuanSurat {
        return DB::transaction(function () use ($pengajuanId, $pejabatUser, $decision, $newDokumenData, $komentar, $ipAddress, $userAgent) {
            // Row lock pengajuan
            $pengajuan = PengajuanSurat::where('id', $pengajuanId)->lockForUpdate()->firstOrFail();

            if (!in_array($pengajuan->status, [StatusPengajuan::DIAJUKAN, StatusPengajuan::MENUNGGU_APPROVAL, StatusPengajuan::PERLU_PERBAIKAN])) {
                throw new \Exception('Pengajuan surat tidak sedang dalam tahap persetujuan aktif.');
            }

            $currentApproval = $pengajuan->approvals()
                ->where('urutan', $pengajuan->tahapan_aktif)
                ->firstOrFail();

            $statusSebelum = $pengajuan->status->value;

            // Handle optional new version of document uploaded by Pejabat
            $dokumenVersiId = null;
            if ($newDokumenData) {
                // Mark previous documents as is_latest = false
                PengajuanDokumen::where('pengajuan_surat_id', $pengajuan->id)->update(['is_latest' => false]);

                $latestVersiNumber = PengajuanDokumen::where('pengajuan_surat_id', $pengajuan->id)->max('versi') ?? 0;
                $newVersiNumber = $latestVersiNumber + 1;

                $newDoc = PengajuanDokumen::create([
                    'pengajuan_surat_id' => $pengajuan->id,
                    'pengajuan_approval_id' => $currentApproval->id,
                    'versi' => $newVersiNumber,
                    'nama_file_asli' => $newDokumenData['nama_file_asli'],
                    'file_path' => $newDokumenData['file_path'],
                    'file_extension' => $newDokumenData['file_extension'] ?? null,
                    'mime_type' => $newDokumenData['mime_type'] ?? null,
                    'file_size' => $newDokumenData['file_size'] ?? null,
                    'sumber' => 'pejabat',
                    'uploaded_by' => $pejabatUser->id,
                    'keterangan' => 'Dokumen hasil persetujuan oleh ' . $currentApproval->nama_jabatan_snapshot,
                    'is_latest' => true,
                ]);

                $dokumenVersiId = $newDoc->id;
            }

            // Get WargaJabatan if present
            $activeWj = $pejabatUser->getActiveJabatans()->where('jabatan_id', $currentApproval->jabatan_id)->first();

            if ($decision === 'terima') {
                $currentApproval->update([
                    'status' => StatusApproval::DITERIMA,
                    'komentar' => $komentar,
                    'dokumen_versi_id' => $dokumenVersiId,
                    'processed_at' => now(),
                    'processed_by' => $pejabatUser->id,
                    'warga_jabatan_id' => $activeWj?->id,
                    'nama_pejabat_snapshot' => $pejabatUser->name,
                ]);

                // Check next approval step
                $nextApproval = $pengajuan->approvals()
                    ->where('urutan', $pengajuan->tahapan_aktif + 1)
                    ->first();

                if ($nextApproval) {
                    // Activate next step
                    $pengajuan->update([
                        'tahapan_aktif' => $nextApproval->urutan,
                        'status' => StatusPengajuan::MENUNGGU_APPROVAL,
                    ]);

                    $nextApproval->update([
                        'status' => StatusApproval::AKTIF,
                        'activated_at' => now(),
                    ]);
                } else {
                    // All steps completed!
                    $pengajuan->update([
                        'status' => StatusPengajuan::SELESAI,
                        'completed_at' => now(),
                    ]);
                }
            } elseif ($decision === 'tolak') {
                if (empty(trim($komentar ?? ''))) {
                    throw new \Exception('Alasan penolakan wajib diisi.');
                }

                $currentApproval->update([
                    'status' => StatusApproval::DITOLAK,
                    'komentar' => $komentar,
                    'processed_at' => now(),
                    'processed_by' => $pejabatUser->id,
                    'warga_jabatan_id' => $activeWj?->id,
                    'nama_pejabat_snapshot' => $pejabatUser->name,
                ]);

                $pengajuan->update([
                    'status' => StatusPengajuan::DITOLAK,
                    'rejected_at' => now(),
                ]);
            } elseif ($decision === 'ulangi') {
                if (empty(trim($komentar ?? ''))) {
                    throw new \Exception('Alasan permintaan perbaikan wajib diisi.');
                }

                $currentApproval->update([
                    'status' => StatusApproval::ULANGI,
                    'komentar' => $komentar,
                    'processed_at' => now(),
                    'processed_by' => $pejabatUser->id,
                    'warga_jabatan_id' => $activeWj?->id,
                    'nama_pejabat_snapshot' => $pejabatUser->name,
                ]);

                $pengajuan->update([
                    'status' => StatusPengajuan::PERLU_PERBAIKAN,
                ]);
            }

            $pengajuan->refresh();

            // Log action
            PengajuanLog::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'pengajuan_approval_id' => $currentApproval->id,
                'user_id' => $pejabatUser->id,
                'action' => 'APPROVAL_DECISION_' . strtoupper($decision),
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $pengajuan->status->value,
                'komentar' => $komentar,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $pengajuan;
        });
    }

    /**
     * Warga re-uploads revised document after 'ulangi'
     */
    public function resubmitDocument(
        PengajuanSurat $pengajuan,
        User $wargaUser,
        array $dokumenData,
        ?string $catatan = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): PengajuanSurat {
        return DB::transaction(function () use ($pengajuan, $wargaUser, $dokumenData, $catatan, $ipAddress, $userAgent) {
            if ($pengajuan->status !== StatusPengajuan::PERLU_PERBAIKAN) {
                throw new \Exception('Pengajuan ini tidak dalam status perlu perbaikan.');
            }

            // Mark previous as non-latest
            PengajuanDokumen::where('pengajuan_surat_id', $pengajuan->id)->update(['is_latest' => false]);

            $latestVersiNumber = PengajuanDokumen::where('pengajuan_surat_id', $pengajuan->id)->max('versi') ?? 0;
            $newVersiNumber = $latestVersiNumber + 1;

            PengajuanDokumen::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'pengajuan_approval_id' => null,
                'versi' => $newVersiNumber,
                'nama_file_asli' => $dokumenData['nama_file_asli'],
                'file_path' => $dokumenData['file_path'],
                'file_extension' => $dokumenData['file_extension'] ?? null,
                'mime_type' => $dokumenData['mime_type'] ?? null,
                'file_size' => $dokumenData['file_size'] ?? null,
                'sumber' => 'warga',
                'uploaded_by' => $wargaUser->id,
                'keterangan' => 'Dokumen perbaikan versi ' . $newVersiNumber . ' oleh warga.',
                'is_latest' => true,
            ]);

            $statusSebelum = $pengajuan->status->value;

            // Reactivate current step for pejabat
            $currentApproval = $pengajuan->approvals()
                ->where('urutan', $pengajuan->tahapan_aktif)
                ->firstOrFail();

            $currentApproval->update([
                'status' => StatusApproval::AKTIF,
                'activated_at' => now(),
            ]);

            $pengajuan->update([
                'status' => StatusPengajuan::MENUNGGU_APPROVAL,
                'catatan_pemohon' => $catatan ?? $pengajuan->catatan_pemohon,
            ]);

            PengajuanLog::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'pengajuan_approval_id' => $currentApproval->id,
                'user_id' => $wargaUser->id,
                'action' => 'RESUBMIT_PERBAIKAN',
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => StatusPengajuan::MENUNGGU_APPROVAL->value,
                'komentar' => $catatan ?? 'Warga telah mengunggah ulang dokumen perbaikan.',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $pengajuan;
        });
    }
}
