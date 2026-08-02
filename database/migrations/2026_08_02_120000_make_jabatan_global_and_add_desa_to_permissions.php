<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Make desa_id nullable in jabatans using raw SQL for compatibility
        DB::statement("ALTER TABLE jabatans MODIFY desa_id BIGINT UNSIGNED NULL");

        // 2. Add desa_id to jabatan_permissions if not present
        if (!Schema::hasColumn('jabatan_permissions', 'desa_id')) {
            Schema::table('jabatan_permissions', function (Blueprint $table) {
                $table->foreignId('desa_id')->nullable()->after('id')->constrained('desas')->cascadeOnDelete();
            });
        }

        // 3. Add composite unique constraint if not exists
        try {
            Schema::table('jabatan_permissions', function (Blueprint $table) {
                $table->unique(['desa_id', 'jabatan_id', 'permission_id'], 'jbt_perm_desa_unique');
            });
        } catch (\Throwable $e) {
            // Index might already exist
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jabatan_permissions', 'desa_id')) {
            Schema::table('jabatan_permissions', function (Blueprint $table) {
                try {
                    $table->dropUnique('jbt_perm_desa_unique');
                } catch (\Throwable $e) {}
                $table->dropForeign(['desa_id']);
                $table->dropColumn('desa_id');
            });
        }

        DB::statement("ALTER TABLE jabatans MODIFY desa_id BIGINT UNSIGNED NOT NULL");
    }
};
