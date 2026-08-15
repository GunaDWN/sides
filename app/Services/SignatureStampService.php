<?php

namespace App\Services;

use App\Models\JenisSuratSignaturePlacement;
use App\Models\PengajuanApproval;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanSurat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class SignatureStampService
{
    /**
     * Stamp tanda tangan satu pejabat ke dokumen saat approve.
     * Mengambil dokumen terbaru, stamp tanda tangan, dan simpan sebagai signed version.
     *
     * @return string|null Path file yang sudah di-stamp
     */
    public function stampSignature(PengajuanDokumen $dokumen, PengajuanApproval $approval): ?string
    {
        $pengajuan = $dokumen->pengajuanSurat;
        $jenisSurat = $pengajuan->jenisSurat;

        if (!$jenisSurat) {
            return null; // Custom surat, tidak ada template placement
        }

        // Cari konfigurasi placement untuk jabatan ini
        $placement = JenisSuratSignaturePlacement::where('jenis_surat_id', $jenisSurat->id)
            ->where('jabatan_id', $approval->jabatan_id)
            ->first();

        if (!$placement || !$placement->isConfigured()) {
            return null; // Tidak ada konfigurasi posisi TTD untuk jabatan ini
        }

        // Ambil tanda tangan pejabat dari WargaJabatan (atau fallback ke pejabat aktif desa untuk jabatan ini)
        $wargaJabatan = $approval->wargaJabatan;
        if (!$wargaJabatan) {
            $wargaJabatan = \App\Models\WargaJabatan::where('jabatan_id', $approval->jabatan_id)
                ->where('status', 'aktif')
                ->whereHas('warga', fn($q) => $q->where('desa_id', $pengajuan->desa_id))
                ->first()
                ?? \App\Models\WargaJabatan::where('jabatan_id', $approval->jabatan_id)
                    ->where('status', 'aktif')
                    ->latest()
                    ->first();
        }

        $ttdPath = ($wargaJabatan && $wargaJabatan->tanda_tangan_path)
            ? Storage::disk('local')->path($wargaJabatan->tanda_tangan_path)
            : null;

        if ($ttdPath && !file_exists($ttdPath)) {
            $ttdPath = null;
        }

        // Ambil stempel jika diperlukan
        $stempelPath = null;
        if ($placement->tampilkan_stempel && $wargaJabatan && $wargaJabatan->stempel_path) {
            $checkStempel = Storage::disk('local')->path($wargaJabatan->stempel_path);
            if (file_exists($checkStempel)) {
                $stempelPath = $checkStempel;
            }
        }

        // Gunakan signed_file_path jika sudah ada (dokumen sudah pernah di-stamp sebelumnya),
        // jika tidak, gunakan file_path asli
        $sourceFilePath = $dokumen->signed_file_path
            ? Storage::disk('local')->path($dokumen->signed_file_path)
            : Storage::disk('local')->path($dokumen->file_path);

        if (!file_exists($sourceFilePath)) {
            return null;
        }

        $extension = strtolower($dokumen->file_extension ?? pathinfo($sourceFilePath, PATHINFO_EXTENSION));

        // Data pejabat untuk teks nama/jabatan
        $namaPejabat = $approval->nama_pejabat_snapshot ?: ($wargaJabatan?->warga?->nama ?? null);
        $namaJabatan = $approval->nama_jabatan_snapshot ?: ($placement->jabatan?->nama ?? null);

        try {
            // Untuk DOCX, convert ke PDF dulu menggunakan LibreOffice
            if (in_array($extension, ['docx', 'doc'])) {
                $sourceFilePath = $this->convertDocxToPdf($sourceFilePath);
                $extension = 'pdf';
            }

            if ($extension === 'pdf') {
                $outputPath = $this->stampPdf(
                    $sourceFilePath,
                    $placement,
                    $ttdPath,
                    $stempelPath,
                    $namaPejabat,
                    $namaJabatan
                );
            } else {
                return null; // Format tidak didukung
            }

            // Simpan path relatif terhadap storage
            $relativePath = 'surat-signed/' . basename($outputPath);

            // Pindahkan file ke storage
            $targetFullPath = Storage::disk('local')->path($relativePath);
            $targetDir = dirname($targetFullPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if ($outputPath !== $targetFullPath) {
                rename($outputPath, $targetFullPath);
            }

            // Update dokumen dengan signed_file_path
            $dokumen->update(['signed_file_path' => $relativePath]);

            return $relativePath;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Convert DOCX to PDF using LibreOffice headless.
     */
    private function convertDocxToPdf(string $docxPath): string
    {
        $outputDir = storage_path('app/private/surat-signed');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $command = sprintf(
            'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg($outputDir),
            escapeshellarg($docxPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Failed to convert DOCX to PDF: ' . implode("\n", $output));
        }

        $pdfFilename = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $outputDir . '/' . $pdfFilename;

        if (!file_exists($pdfPath)) {
            throw new \Exception('PDF conversion output not found.');
        }

        return $pdfPath;
    }

    /**
     * Stamp tanda tangan pada dokumen PDF menggunakan koordinat posisi.
     * Overlay gambar tanda tangan pada koordinat (x, y) di halaman tertentu.
     */
    private function stampPdf(
        string $filePath,
        JenisSuratSignaturePlacement $placement,
        string $ttdPath,
        ?string $stempelPath,
        ?string $namaPejabat,
        ?string $namaJabatan
    ): string {
        $pdf = new Fpdi();

        // Import semua halaman dari PDF sumber
        $pageCount = $pdf->setSourceFile($filePath);
        $targetPage = $placement->halaman ?? 1;

        // Pastikan halaman target valid
        if ($targetPage > $pageCount) {
            $targetPage = $pageCount;
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Stamp tanda tangan hanya di halaman target
            if ($pageNo === $targetPage) {
                $posX = $placement->pos_x ?? 0;
                $posY = $placement->pos_y ?? 0;
                $width = $placement->lebar ?? 40;
                $height = $placement->tinggi ?? 20;

                // Overlay stempel terlebih dahulu (di bawah tanda tangan)
                if ($stempelPath && file_exists($stempelPath)) {
                    $stempelWidth = $width * 1.5;
                    $stempelHeight = $height * 1.5;
                    $stempelX = $posX - ($stempelWidth - $width) / 2;
                    $stempelY = $posY - ($stempelHeight - $height) / 2;

                    $pdf->Image($stempelPath, $stempelX, $stempelY, $stempelWidth, $stempelHeight);
                }

                // Overlay gambar tanda tangan
                if ($ttdPath && file_exists($ttdPath)) {
                    $pdf->Image($ttdPath, $posX, $posY, $width, $height);
                }

                // Tambah teks nama pejabat di bawah tanda tangan
                $textY = $posY + $height + 2;

                if ($placement->tampilkan_nama && $namaPejabat) {
                    $pdf->SetFont('Helvetica', 'B', 9);
                    $pdf->SetXY($posX, $textY);
                    $pdf->Cell($width, 4, $namaPejabat, 0, 0, 'C');
                    $textY += 4;
                }

                if ($placement->tampilkan_jabatan && $namaJabatan) {
                    $pdf->SetFont('Helvetica', '', 8);
                    $pdf->SetXY($posX, $textY);
                    $pdf->Cell($width, 4, $namaJabatan, 0, 0, 'C');
                }
            }
        }

        // Simpan output
        $outputFilename = 'signed_' . Str::random(16) . '.pdf';
        $outputPath = storage_path('app/private/surat-signed/' . $outputFilename);
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $pdf->Output($outputPath, 'F');

        return $outputPath;
    }

    /**
     * Stamp semua tanda tangan yang sudah di-approve ke dokumen.
     * Berguna untuk generate ulang dokumen final.
     */
    public function stampAllApproved(PengajuanSurat $pengajuan): ?string
    {
        $latestDokumen = $pengajuan->latestDokumen;
        if (!$latestDokumen) {
            return null;
        }

        $approvedApprovals = $pengajuan->approvals()
            ->where('status', 'diterima')
            ->orderBy('urutan', 'asc')
            ->get();

        $lastPath = null;
        foreach ($approvedApprovals as $approval) {
            $lastPath = $this->stampSignature($latestDokumen, $approval);
            $latestDokumen->refresh();
        }

        return $lastPath;
    }
}
