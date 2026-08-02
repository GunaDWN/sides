<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'desa_id',
        'nama',
        'kode',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'jabatan_permissions')
            ->withPivot('desa_id')
            ->withTimestamps();
    }

    public function permissionsForDesa(?int $desaId = null)
    {
        return $this->belongsToMany(Permission::class, 'jabatan_permissions')
            ->where(function ($q) use ($desaId) {
                if ($desaId) {
                    $q->where('jabatan_permissions.desa_id', $desaId)
                      ->orWhereNull('jabatan_permissions.desa_id');
                } else {
                    $q->whereNull('jabatan_permissions.desa_id');
                }
            })
            ->withPivot('desa_id')
            ->withTimestamps();
    }

    public function wargaJabatans(): HasMany
    {
        return $this->hasMany(WargaJabatan::class);
    }

    public function activePejabat()
    {
        $today = now()->format('Y-m-d');
        return $this->hasMany(WargaJabatan::class)
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $today);
            });
    }
}
