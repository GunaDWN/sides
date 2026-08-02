<?php

namespace App\Models;

use App\Enums\StatusPengajuan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_pengajuan',
        'desa_id',
        'jenis_surat_id',
        'warga_id',
        'is_custom',
        'perihal_surat',
        'status',
        'tahapan_aktif',
        'catatan_pemohon',
        'submitted_at',
        'completed_at',
        'rejected_at',
        'cancelled_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPengajuan::class,
            'is_custom' => 'boolean',
            'tahapan_aktif' => 'integer',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PengajuanApproval::class)->orderBy('urutan', 'asc');
    }

    public function activeApproval(): HasOne
    {
        return $this->hasOne(PengajuanApproval::class)
            ->where('urutan', $this->tahapan_aktif);
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(PengajuanDokumen::class)->orderBy('versi', 'desc');
    }

    public function latestDokumen(): HasOne
    {
        return $this->hasOne(PengajuanDokumen::class)->where('is_latest', true);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PengajuanLog::class)->orderBy('id', 'desc');
    }

    /**
     * Get display name: perihal_surat for custom, jenisSurat->nama for template
     */
    public function getDisplayName(): string
    {
        if ($this->is_custom) {
            return $this->perihal_surat ?? 'Surat Kustom';
        }

        return $this->jenisSurat?->nama ?? 'Surat';
    }
}
