<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('warga_id');
            $table->string('perihal_surat')->nullable()->after('is_custom');

            // Make jenis_surat_id nullable for custom letters
            $table->foreignId('jenis_surat_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            $table->dropColumn(['is_custom', 'perihal_surat']);

            // Revert jenis_surat_id to non-nullable
            $table->foreignId('jenis_surat_id')->nullable(false)->change();
        });
    }
};
