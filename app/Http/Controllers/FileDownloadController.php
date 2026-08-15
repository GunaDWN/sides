<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDokumen;
use App\Models\RegistrasiAkun;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileDownloadController extends Controller
{
    public function downloadKtp(Request $request, RegistrasiAkun $registrasi): BinaryFileResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if ($user->cannot('downloadKtp', $registrasi)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen KTP ini.');
        }

        if (!Storage::disk('local')->exists($registrasi->ktp_path)) {
            abort(404, 'File KTP tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($registrasi->ktp_path);
        return response()->download($fullPath, $registrasi->ktp_nama_asli ?? ('KTP-' . $registrasi->nik . '.png'));
    }

    public function downloadTemplate(Request $request, JenisSurat $jenisSurat): BinaryFileResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!$jenisSurat->template_path || !Storage::disk('local')->exists($jenisSurat->template_path)) {
            abort(404, 'Template surat belum diunggah.');
        }

        $fullPath = Storage::disk('local')->path($jenisSurat->template_path);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        return response()->download($fullPath, 'Template_' . $jenisSurat->nama . '.' . $ext);
    }

    public function downloadDokumen(Request $request, PengajuanDokumen $dokumen): BinaryFileResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $pengajuan = $dokumen->pengajuanSurat;
        if ($user->cannot('downloadDocument', $pengajuan)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen ini.');
        }

        $targetPath = ($dokumen->signed_file_path && Storage::disk('local')->exists($dokumen->signed_file_path))
            ? $dokumen->signed_file_path
            : $dokumen->file_path;

        if (!Storage::disk('local')->exists($targetPath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($targetPath);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $baseName = pathinfo($dokumen->nama_file_asli, PATHINFO_FILENAME);
        $downloadName = $dokumen->signed_file_path ? $baseName . '_signed.' . $ext : $dokumen->nama_file_asli;

        return response()->download($fullPath, $downloadName);
    }

    public function templatePreviewImage(Request $request, string $path)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $previewService = app(\App\Services\TemplatePreviewService::class);
        $fullPath = $previewService->getPreviewImagePath($path);

        if (!$fullPath) {
            abort(404, 'Preview image not found.');
        }

        return response()->file($fullPath, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function viewSignature(Request $request, \App\Models\WargaJabatan $wargaJabatan)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!$wargaJabatan->tanda_tangan_path || !Storage::disk('local')->exists($wargaJabatan->tanda_tangan_path)) {
            abort(404, 'File tanda tangan tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($wargaJabatan->tanda_tangan_path);
        $mime = mime_content_type($fullPath) ?: 'image/png';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function viewStamp(Request $request, \App\Models\WargaJabatan $wargaJabatan)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!$wargaJabatan->stempel_path || !Storage::disk('local')->exists($wargaJabatan->stempel_path)) {
            abort(404, 'File stempel tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($wargaJabatan->stempel_path);
        $mime = mime_content_type($fullPath) ?: 'image/png';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
