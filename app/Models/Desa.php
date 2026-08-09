<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'kode',
        'provinsi',
        'kode_provinsi',
        'kabupaten',
        'kode_kabupaten',
        'kecamatan',
        'kode_kecamatan',
        'alamat',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'logo_path',
        'kop_path',
        'stempel_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function wargas(): HasMany
    {
        return $this->hasMany(Warga::class);
    }

    public function jabatans(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }

    public function jenisSurats(): HasMany
    {
        return $this->hasMany(JenisSurat::class);
    }

    public function registrasiAkuns(): HasMany
    {
        return $this->hasMany(RegistrasiAkun::class);
    }

    public function pengajuanSurats(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
