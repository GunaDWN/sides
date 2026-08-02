<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->cascadeOnDelete();
            $table->foreignId('warga_id')->constrained('wargas')->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, diajukan, menunggu_approval, perlu_perbaikan, ditolak, selesai, dibatalkan
            $table->integer('tahapan_aktif')->default(1);
            $table->text('catatan_pemohon')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nomor_pengajuan');
            $table->index('desa_id');
            $table->index('warga_id');
            $table->index('status');
        });

        Schema::create('pengajuan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->constrained('pengajuan_surats')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->foreignId('warga_jabatan_id')->nullable()->constrained('warga_jabatans')->nullOnDelete();
            $table->string('nama_jabatan_snapshot');
            $table->string('nama_pejabat_snapshot')->nullable();
            $table->integer('urutan');
            $table->string('status')->default('menunggu'); // menunggu, aktif, diterima, ditolak, ulangi, dilewati, dibatalkan
            $table->text('komentar')->nullable();
            $table->unsignedBigInteger('dokumen_versi_id')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('pengajuan_surat_id');
            $table->index('jabatan_id');
            $table->index('status');
        });

        Schema::create('pengajuan_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->constrained('pengajuan_surats')->cascadeOnDelete();
            $table->foreignId('pengajuan_approval_id')->nullable()->constrained('pengajuan_approvals')->nullOnDelete();
            $table->integer('versi');
            $table->string('nama_file_asli');
            $table->string('file_path');
            $table->string('file_extension')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('sumber')->default('warga'); // warga, pejabat
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->boolean('is_latest')->default(false);
            $table->timestamps();

            $table->index('pengajuan_surat_id');
            $table->index('is_latest');
        });

        Schema::create('pengajuan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->constrained('pengajuan_surats')->cascadeOnDelete();
            $table->foreignId('pengajuan_approval_id')->nullable()->constrained('pengajuan_approvals')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah')->nullable();
            $table->text('komentar')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('pengajuan_surat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_logs');
        Schema::dropIfExists('pengajuan_dokumens');
        Schema::dropIfExists('pengajuan_approvals');
        Schema::dropIfExists('pengajuan_surats');
    }
};
