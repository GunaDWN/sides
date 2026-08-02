<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('template_path')->nullable();
            $table->boolean('butuh_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('desa_id');
        });

        Schema::create('jenis_surat_pengelolas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('jenis_surat_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->integer('urutan');
            $table->boolean('wajib')->default(true);
            $table->timestamps();

            $table->unique(['jenis_surat_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_surat_approvals');
        Schema::dropIfExists('jenis_surat_pengelolas');
        Schema::dropIfExists('jenis_surats');
    }
};
