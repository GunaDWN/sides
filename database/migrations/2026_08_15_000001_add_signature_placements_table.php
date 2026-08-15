<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surat_signature_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();

            // Untuk DOCX: placeholder text (e.g. {ttd_kepala_desa})
            $table->string('placeholder_text')->nullable();

            // Untuk PDF: koordinat posisi
            $table->float('pos_x')->nullable();       // posisi X (mm dari kiri)
            $table->float('pos_y')->nullable();       // posisi Y (mm dari atas)
            $table->integer('halaman')->nullable();    // nomor halaman

            // Dimensi gambar tanda tangan (mm)
            $table->float('lebar')->default(40);       // lebar gambar TTD
            $table->float('tinggi')->default(20);      // tinggi gambar TTD

            // Opsi tambahan
            $table->boolean('tampilkan_nama')->default(true);    // tampilkan nama pejabat
            $table->boolean('tampilkan_jabatan')->default(true); // tampilkan nama jabatan
            $table->boolean('tampilkan_stempel')->default(false); // overlay stempel

            $table->timestamps();

            $table->unique(['jenis_surat_id', 'jabatan_id'], 'sig_placement_surat_jabatan_unique');
        });

        Schema::table('pengajuan_dokumens', function (Blueprint $table) {
            $table->string('signed_file_path')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_surat_signature_placements');

        Schema::table('pengajuan_dokumens', function (Blueprint $table) {
            $table->dropColumn('signed_file_path');
        });
    }
};
