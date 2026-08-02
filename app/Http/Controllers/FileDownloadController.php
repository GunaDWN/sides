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

        if (!Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($dokumen->file_path);
        return response()->download($fullPath, $dokumen->nama_file_asli);
    }
}
