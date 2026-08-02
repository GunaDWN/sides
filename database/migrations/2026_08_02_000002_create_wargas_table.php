<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable(); // L / P
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('jenis_warga')->default('warga_biasa'); // warga_biasa, warga_dengan_jabatan
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
            $table->softDeletes();

            $table->index('nik');
            $table->index('email');
            $table->index('desa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};
