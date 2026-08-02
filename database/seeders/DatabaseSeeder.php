<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\JenisSurat;
use App\Models\JenisSuratApproval;
use App\Models\Permission;
use App\Models\User;
use App\Models\Warga;
use App\Models\WargaJabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            ['name' => 'registrasi-akun.view', 'label' => 'Lihat Daftar Registrasi Akun', 'group' => 'Registrasi Akun'],
            ['name' => 'registrasi-akun.detail', 'label' => 'Lihat Detail Registrasi Akun', 'group' => 'Registrasi Akun'],
            ['name' => 'registrasi-akun.approve', 'label' => 'Menyetujui Registrasi Akun', 'group' => 'Registrasi Akun'],
            ['name' => 'registrasi-akun.reject', 'label' => 'Menolak Registrasi Akun', 'group' => 'Registrasi Akun'],
            ['name' => 'registrasi-akun.download-ktp', 'label' => 'Unduh Dokumen KTP Registrasi', 'group' => 'Registrasi Akun'],

            ['name' => 'jenis-surat.view', 'label' => 'Lihat Jenis Surat', 'group' => 'Jenis Surat'],
            ['name' => 'jenis-surat.create', 'label' => 'Tambah Jenis Surat', 'group' => 'Jenis Surat'],
            ['name' => 'jenis-surat.update', 'label' => 'Ubah Jenis Surat', 'group' => 'Jenis Surat'],
            ['name' => 'jenis-surat.delete', 'label' => 'Hapus Jenis Surat', 'group' => 'Jenis Surat'],

            ['name' => 'pengajuan-surat.approve', 'label' => 'Persetujuan Pengajuan Surat', 'group' => 'Pengajuan Surat'],
            ['name' => 'pengajuan-surat.download', 'label' => 'Unduh Dokumen Surat', 'group' => 'Pengajuan Surat'],

            ['name' => 'warga.view', 'label' => 'Lihat Data Warga', 'group' => 'Data Warga'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // 2. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@sides.desa.id'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Sample Desa
        $desa = Desa::firstOrCreate(
            ['nama' => 'Suka Maju', 'provinsi' => 'Jawa Barat', 'kabupaten' => 'Bekasi', 'kecamatan' => 'Cikarang Pusat'],
            [
                'kode' => '3216010001',
                'kode_provinsi' => '32',
                'kode_kabupaten' => '3216',
                'kode_kecamatan' => '3216010',
                'alamat' => 'Jl. Raya Suka Maju No. 1, Cikarang Pusat, Bekasi',
                'kode_pos' => '17530',
                'telepon' => '021-891001',
                'email' => 'kantor@sukamaju.desa.id',
                'nama_kepala_desa' => 'H. Ahmad Subarkah, S.Sos.',
                'is_active' => true,
            ]
        );

        // Admin is a global Super Admin (desa_id = null) who manages all desas

        // 4. Create Sample Jabatans for Desa Suka Maju
        $jabatans = [
            ['nama' => 'Kepala Seksi Pelayanan', 'kode' => 'KASI_PELAYANAN', 'urutan' => 1],
            ['nama' => 'Sekretaris Desa', 'kode' => 'SEKDES', 'urutan' => 2],
            ['nama' => 'Kepala Desa', 'kode' => 'KADES', 'urutan' => 3],
        ];

        $createdJabatans = [];
        foreach ($jabatans as $j) {
            $createdJabatans[$j['kode']] = Jabatan::firstOrCreate(
                ['desa_id' => $desa->id, 'kode' => $j['kode']],
                [
                    'nama' => $j['nama'],
                    'urutan' => $j['urutan'],
                    'is_active' => true,
                ]
            );
        }

        // Attach permissions to Sekretaris Desa & Kasi Pelayanan
        $allPermIds = Permission::pluck('id')->toArray();
        $createdJabatans['SEKDES']->permissions()->sync($allPermIds);
        $createdJabatans['KASI_PELAYANAN']->permissions()->sync(
            Permission::whereIn('name', ['registrasi-akun.view', 'registrasi-akun.detail', 'pengajuan-surat.approve', 'pengajuan-surat.download', 'warga.view'])->pluck('id')->toArray()
        );

        // 5. Create Pejabat (Warga + User + Active Jabatan)
        $wargaSekdes = Warga::firstOrCreate(
            ['nik' => '3216010101850001'],
            [
                'desa_id' => $desa->id,
                'nama' => 'Budi Santoso, S.AP',
                'email' => 'sekdes@sides.desa.id',
                'jenis_warga' => 'warga_dengan_jabatan',
                'status' => 'aktif',
            ]
        );

        $userSekdes = User::firstOrCreate(
            ['email' => 'sekdes@sides.desa.id'],
            [
                'desa_id' => $desa->id,
                'warga_id' => $wargaSekdes->id,
                'name' => 'Budi Santoso, S.AP',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        WargaJabatan::firstOrCreate(
            ['warga_id' => $wargaSekdes->id, 'jabatan_id' => $createdJabatans['SEKDES']->id],
            [
                'tanggal_mulai' => now()->subYear()->format('Y-m-d'),
                'status' => 'aktif',
            ]
        );

        // Kasi Pelayanan Pejabat
        $wargaKasi = Warga::firstOrCreate(
            ['nik' => '3216010202900002'],
            [
                'desa_id' => $desa->id,
                'nama' => 'Rina Wijaya, S.IP',
                'email' => 'kasi.pelayanan@sides.desa.id',
                'jenis_warga' => 'warga_dengan_jabatan',
                'status' => 'aktif',
            ]
        );

        $userKasi = User::firstOrCreate(
            ['email' => 'kasi.pelayanan@sides.desa.id'],
            [
                'desa_id' => $desa->id,
                'warga_id' => $wargaKasi->id,
                'name' => 'Rina Wijaya, S.IP',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        WargaJabatan::firstOrCreate(
            ['warga_id' => $wargaKasi->id, 'jabatan_id' => $createdJabatans['KASI_PELAYANAN']->id],
            [
                'tanggal_mulai' => now()->subYear()->format('Y-m-d'),
                'status' => 'aktif',
            ]
        );

        // Kades Pejabat
        $wargaKades = Warga::firstOrCreate(
            ['nik' => '3216010303750003'],
            [
                'desa_id' => $desa->id,
                'nama' => 'H. Ahmad Subarkah, S.Sos.',
                'email' => 'kades@sides.desa.id',
                'jenis_warga' => 'warga_dengan_jabatan',
                'status' => 'aktif',
            ]
        );

        $userKades = User::firstOrCreate(
            ['email' => 'kades@sides.desa.id'],
            [
                'desa_id' => $desa->id,
                'warga_id' => $wargaKades->id,
                'name' => 'H. Ahmad Subarkah, S.Sos.',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        WargaJabatan::firstOrCreate(
            ['warga_id' => $wargaKades->id, 'jabatan_id' => $createdJabatans['KADES']->id],
            [
                'tanggal_mulai' => now()->subYear()->format('Y-m-d'),
                'status' => 'aktif',
            ]
        );

        // 6. Sample Warga Biasa
        $wargaBiasa = Warga::firstOrCreate(
            ['nik' => '3216011505950005'],
            [
                'desa_id' => $desa->id,
                'nama' => 'Dedi Kurniawan',
                'email' => 'warga@sides.desa.id',
                'jenis_warga' => 'warga_biasa',
                'status' => 'aktif',
            ]
        );

        User::firstOrCreate(
            ['email' => 'warga@sides.desa.id'],
            [
                'desa_id' => $desa->id,
                'warga_id' => $wargaBiasa->id,
                'name' => 'Dedi Kurniawan',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 7. Sample Jenis Surat
        $jenisSuratDomisili = JenisSurat::firstOrCreate(
            ['desa_id' => $desa->id, 'kode' => 'SKD'],
            [
                'nama' => 'Surat Keterangan Domisili',
                'kategori' => 'Kependudukan',
                'deskripsi' => 'Surat keterangan domisili bagi warga desa Suka Maju.',
                'butuh_approval' => true,
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        // Add 3-step approval sequence for SKD
        JenisSuratApproval::firstOrCreate(['jenis_surat_id' => $jenisSuratDomisili->id, 'urutan' => 1], ['jabatan_id' => $createdJabatans['KASI_PELAYANAN']->id]);
        JenisSuratApproval::firstOrCreate(['jenis_surat_id' => $jenisSuratDomisili->id, 'urutan' => 2], ['jabatan_id' => $createdJabatans['SEKDES']->id]);
        JenisSuratApproval::firstOrCreate(['jenis_surat_id' => $jenisSuratDomisili->id, 'urutan' => 3], ['jabatan_id' => $createdJabatans['KADES']->id]);

        $jenisSuratSKU = JenisSurat::firstOrCreate(
            ['desa_id' => $desa->id, 'kode' => 'SKU'],
            [
                'nama' => 'Surat Keterangan Usaha',
                'kategori' => 'Perekonomian',
                'deskripsi' => 'Surat keterangan kepemilikan dan kelayakan usaha warga.',
                'butuh_approval' => true,
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        JenisSuratApproval::firstOrCreate(['jenis_surat_id' => $jenisSuratSKU->id, 'urutan' => 1], ['jabatan_id' => $createdJabatans['KASI_PELAYANAN']->id]);
        JenisSuratApproval::firstOrCreate(['jenis_surat_id' => $jenisSuratSKU->id, 'urutan' => 2], ['jabatan_id' => $createdJabatans['KADES']->id]);
    }
}
