<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('desa_id');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('jabatan_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['jabatan_id', 'permission_id']);
        });

        Schema::create('warga_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('tanda_tangan_path')->nullable();
            $table->string('stempel_path')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->string('file_sk_path')->nullable();
            $table->string('status')->default('aktif'); // aktif, selesai, nonaktif
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('warga_id');
            $table->index('jabatan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga_jabatans');
        Schema::dropIfExists('jabatan_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('jabatans');
    }
};
