<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDokumen extends Model
{
    protected $fillable = [
        'pengajuan_surat_id',
        'pengajuan_approval_id',
        'versi',
        'nama_file_asli',
        'file_path',
        'signed_file_path',
        'file_extension',
        'mime_type',
        'file_size',
        'sumber',
        'uploaded_by',
        'keterangan',
        'is_latest',
    ];

    protected function casts(): array
    {
        return [
            'versi' => 'integer',
            'is_latest' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function pengajuanSurat(): BelongsTo
    {
        return $this->belongsTo(PengajuanSurat::class);
    }

    public function pengajuanApproval(): BelongsTo
    {
        return $this->belongsTo(PengajuanApproval::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
