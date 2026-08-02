<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 50)->nullable();
            $table->string('provinsi', 100);
            $table->string('kode_provinsi', 50)->nullable();
            $table->string('kabupaten', 100);
            $table->string('kode_kabupaten', 50)->nullable();
            $table->string('kecamatan', 100);
            $table->string('kode_kecamatan', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('kop_path')->nullable();
            $table->string('stempel_path')->nullable();
            $table->string('nama_kepala_desa', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['provinsi', 'kabupaten', 'kecamatan']);
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
