<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasi_akuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode_registrasi')->unique();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('nik', 16);
            $table->string('email');
            $table->string('provinsi');
            $table->string('kode_provinsi')->nullable();
            $table->string('kabupaten');
            $table->string('kode_kabupaten')->nullable();
            $table->string('kecamatan');
            $table->string('kode_kecamatan')->nullable();
            $table->string('password');
            $table->string('ktp_path');
            $table->string('ktp_nama_asli')->nullable();
            $table->string('ktp_mime_type')->nullable();
            $table->unsignedBigInteger('ktp_size')->nullable();
            $table->string('status')->default('menunggu_approval'); // menunggu_approval, disetujui, ditolak, dibatalkan
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_pada')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('kode_registrasi');
            $table->index('nik');
            $table->index('email');
            $table->index('status');
            $table->index('desa_id');
        });

        Schema::create('registrasi_akun_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_akun_id')->constrained('registrasi_akuns')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah')->nullable();
            $table->text('catatan')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi_akun_logs');
        Schema::dropIfExists('registrasi_akuns');
    }
};
