<?php

use App\Http\Controllers\FileDownloadController;
use App\Livewire\Admin;
use App\Livewire\LandingPage;
use App\Livewire\Login;
use App\Livewire\Pejabat;
use App\Livewire\RegistrasiWarga;
use App\Livewire\CekStatusRegistrasi;
use App\Livewire\Dashboard;
use App\Livewire\Warga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ==========================================
// Public Routes (Guest)
// ==========================================
Route::get('/', LandingPage::class)->name('landing');
Route::get('/login', Login::class)->name('login');
Route::get('/registrasi', RegistrasiWarga::class)->name('registrasi-warga');
Route::get('/cek-registrasi', CekStatusRegistrasi::class)->name('cek-status-registrasi');

// Logout (POST only)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// ==========================================
// Authenticated Routes
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // ==========================================
    // Admin Routes
    // ==========================================
    Route::prefix('admin')->group(function () {
        Route::get('/approval-akun', Admin\ApprovalAkunIndex::class)->name('admin.approval-akun');
        Route::get('/approval-akun/{registrasi}', Admin\ApprovalAkunDetail::class)->name('admin.approval-akun-detail');

        Route::get('/desa', Admin\DesaIndex::class)->name('admin.desa');
        Route::get('/user', Admin\UserIndex::class)->name('admin.user');
        Route::get('/jabatan', Admin\JabatanIndex::class)->name('admin.jabatan');
        Route::get('/warga', Admin\WargaIndex::class)->name('admin.warga');
        Route::get('/jenis-surat', Admin\JenisSuratIndex::class)->name('admin.jenis-surat');
        Route::get('/log-aktivitas', Admin\LogAktivitas::class)->name('admin.log-aktivitas');
    });

    // ==========================================
    // Warga Routes
    // ==========================================
    Route::prefix('warga')->group(function () {
        Route::get('/ajukan-surat', Warga\AjukanSurat::class)->name('warga.ajukan-surat');
        Route::get('/riwayat-pengajuan', Warga\RiwayatPengajuan::class)->name('warga.riwayat-pengajuan');
        Route::get('/pengajuan/{pengajuan}', Warga\DetailPengajuan::class)->name('warga.detail-pengajuan');
    });

    // ==========================================
    // Pejabat Routes
    // ==========================================
    Route::prefix('pejabat')->group(function () {
        Route::get('/inbox-approval', Pejabat\InboxApprovalSurat::class)->name('pejabat.inbox-approval');
        Route::get('/approval/{approval}', Pejabat\DetailApprovalSurat::class)->name('pejabat.detail-approval');
    });

    // ==========================================
    // File Downloads (Controller-based)
    // ==========================================
    Route::get('/download/ktp/{registrasi}', [FileDownloadController::class, 'downloadKtp'])->name('download.ktp');
    Route::get('/download/template/{jenisSurat}', [FileDownloadController::class, 'downloadTemplate'])->name('download.template');
    Route::get('/download/dokumen/{dokumen}', [FileDownloadController::class, 'downloadDokumen'])->name('download.dokumen');
});
