<?php

namespace App\Models;

use App\Enums\StatusApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanApproval extends Model
{
    protected $fillable = [
        'pengajuan_surat_id',
        'jabatan_id',
        'warga_jabatan_id',
        'nama_jabatan_snapshot',
        'nama_pejabat_snapshot',
        'urutan',
        'status',
        'komentar',
        'dokumen_versi_id',
        'activated_at',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusApproval::class,
            'urutan' => 'integer',
            'activated_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function pengajuanSurat(): BelongsTo
    {
        return $this->belongsTo(PengajuanSurat::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function wargaJabatan(): BelongsTo
    {
        return $this->belongsTo(WargaJabatan::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function dokumenVersi(): BelongsTo
    {
        return $this->belongsTo(PengajuanDokumen::class, 'dokumen_versi_id');
    }
}
