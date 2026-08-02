<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WargaJabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warga_id',
        'jabatan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanda_tangan_path',
        'stempel_path',
        'nomor_sk',
        'file_sk_path',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function isCurrentlyActive(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }

        $today = now()->format('Y-m-d');
        if ($this->tanggal_mulai->format('Y-m-d') > $today) {
            return false;
        }

        if ($this->tanggal_selesai && $this->tanggal_selesai->format('Y-m-d') < $today) {
            return false;
        }

        return true;
    }
}
