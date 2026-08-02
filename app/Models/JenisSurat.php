<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'desa_id',
        'nama',
        'kode',
        'kategori',
        'deskripsi',
        'template_path',
        'butuh_approval',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'butuh_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pengelolas(): BelongsToMany
    {
        return $this->belongsToMany(Jabatan::class, 'jenis_surat_pengelolas')
            ->withTimestamps();
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(JenisSuratApproval::class)->orderBy('urutan', 'asc');
    }

    public function pengajuanSurats(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
