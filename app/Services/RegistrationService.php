<?php

namespace App\Services;

use App\Enums\StatusRegistrasi;
use App\Models\RegistrasiAkun;
use App\Models\RegistrasiAkunLog;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistrationService
{
    /**
     * Submit a new registration attempt
     */
    public function submitRegistration(array $data, ?string $ipAddress = null, ?string $userAgent = null): RegistrasiAkun
    {
        return DB::transaction(function () use ($data, $ipAddress, $userAgent) {
            $kodeRegistrasi = 'REG-' . strtoupper(Str::random(4)) . '-' . date('YmdHis');

            $registrasi = RegistrasiAkun::create([
                'kode_registrasi' => $kodeRegistrasi,
                'desa_id' => $data['desa_id'],
                'nama_lengkap' => $data['nama_lengkap'],
                'nik' => $data['nik'],
                'email' => $data['email'],
                'provinsi' => $data['provinsi'],
                'kode_provinsi' => $data['kode_provinsi'] ?? null,
                'kabupaten' => $data['kabupaten'],
                'kode_kabupaten' => $data['kode_kabupaten'] ?? null,
                'kecamatan' => $data['kecamatan'],
                'kode_kecamatan' => $data['kode_kecamatan'] ?? null,
                'password' => Hash::make($data['password']),
                'ktp_path' => $data['ktp_path'],
                'ktp_nama_asli' => $data['ktp_nama_asli'] ?? null,
                'ktp_mime_type' => $data['ktp_mime_type'] ?? null,
                'ktp_size' => $data['ktp_size'] ?? null,
                'status' => StatusRegistrasi::MENUNGGU_APPROVAL,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            RegistrasiAkunLog::create([
                'registrasi_akun_id' => $registrasi->id,
                'user_id' => null,
                'action' => 'SUBMIT_REGISTRASI',
                'status_sebelum' => null,
                'status_sesudah' => StatusRegistrasi::MENUNGGU_APPROVAL->value,
                'catatan' => 'Pendaftaran akun warga berhasil diajukan.',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $registrasi;
        });
    }

    /**
     * Approve registration with DB Transaction + Row Locking
     */
    public function approveRegistration(int $registrasiId, User $processedBy, ?string $catatan = null, ?string $ipAddress = null, ?string $userAgent = null): RegistrasiAkun
    {
        return DB::transaction(function () use ($registrasiId, $processedBy, $catatan, $ipAddress, $userAgent) {
            // Row lock
            $registrasi = RegistrasiAkun::where('id', $registrasiId)->lockForUpdate()->firstOrFail();

            if ($registrasi->status !== StatusRegistrasi::MENUNGGU_APPROVAL) {
                throw new \Exception('Registrasi ini telah diproses sebelumnya.');
            }

            // Check duplicate NIK or Email in active Warga / User
            if (Warga::where('nik', $registrasi->nik)->exists()) {
                throw new \Exception('NIK ini sudah terdaftar sebagai warga aktif.');
            }

            if (User::where('email', $registrasi->email)->exists()) {
                throw new \Exception('Email ini sudah terdaftar sebagai pengguna aktif.');
            }

            // 1. Create Warga
            $warga = Warga::create([
                'desa_id' => $registrasi->desa_id,
                'nik' => $registrasi->nik,
                'nama' => $registrasi->nama_lengkap,
                'email' => $registrasi->email,
                'jenis_warga' => 'warga_biasa',
                'status' => 'aktif',
            ]);

            // 2. Create User
            $user = User::create([
                'desa_id' => $registrasi->desa_id,
                'warga_id' => $warga->id,
                'name' => $registrasi->nama_lengkap,
                'email' => $registrasi->email,
                'password' => $registrasi->password, // Already hashed
                'role' => 'warga',
                'status' => 'active',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => $processedBy->id,
                'email_verified_at' => now(),
            ]);

            // 3. Update RegistrasiAkun
            $statusSebelum = $registrasi->status->value;
            $registrasi->update([
                'status' => StatusRegistrasi::DISETUJUI,
                'catatan_petugas' => $catatan,
                'diproses_oleh' => $processedBy->id,
                'diproses_pada' => now(),
                'user_id' => $user->id,
                'warga_id' => $warga->id,
            ]);

            // 4. Log audit
            RegistrasiAkunLog::create([
                'registrasi_akun_id' => $registrasi->id,
                'user_id' => $processedBy->id,
                'action' => 'APPROVE_REGISTRASI',
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => StatusRegistrasi::DISETUJUI->value,
                'catatan' => $catatan ?? 'Pendaftaran akun disetujui.',
                'metadata' => [
                    'user_id_created' => $user->id,
                    'warga_id_created' => $warga->id,
                ],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $registrasi;
        });
    }

    /**
     * Reject registration with DB Transaction + Row Locking
     */
    public function rejectRegistration(int $registrasiId, User $processedBy, string $alasanPenolakan, ?string $ipAddress = null, ?string $userAgent = null): RegistrasiAkun
    {
        return DB::transaction(function () use ($registrasiId, $processedBy, $alasanPenolakan, $ipAddress, $userAgent) {
            $registrasi = RegistrasiAkun::where('id', $registrasiId)->lockForUpdate()->firstOrFail();

            if ($registrasi->status !== StatusRegistrasi::MENUNGGU_APPROVAL) {
                throw new \Exception('Registrasi ini telah diproses sebelumnya.');
            }

            $statusSebelum = $registrasi->status->value;
            $registrasi->update([
                'status' => StatusRegistrasi::DITOLAK,
                'catatan_petugas' => $alasanPenolakan,
                'diproses_oleh' => $processedBy->id,
                'diproses_pada' => now(),
            ]);

            RegistrasiAkunLog::create([
                'registrasi_akun_id' => $registrasi->id,
                'user_id' => $processedBy->id,
                'action' => 'REJECT_REGISTRASI',
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => StatusRegistrasi::DITOLAK->value,
                'catatan' => $alasanPenolakan,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $registrasi;
        });
    }
}
