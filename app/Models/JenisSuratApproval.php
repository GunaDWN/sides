<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisSuratApproval extends Model
{
    protected $fillable = [
        'jenis_surat_id',
        'jabatan_id',
        'urutan',
        'wajib',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'wajib' => 'boolean',
        ];
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
}
