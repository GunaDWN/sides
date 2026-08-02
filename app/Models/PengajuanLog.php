<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'pengajuan_surat_id',
        'pengajuan_approval_id',
        'user_id',
        'action',
        'status_sebelum',
        'status_sesudah',
        'komentar',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
