<?php

namespace App\Models;

use App\Enums\StatusRegistrasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrasiAkun extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_registrasi',
        'desa_id',
        'nama_lengkap',
        'nik',
        'email',
        'provinsi',
        'kode_provinsi',
        'kabupaten',
        'kode_kabupaten',
        'kecamatan',
        'kode_kecamatan',
        'password',
        'ktp_path',
        'ktp_nama_asli',
        'ktp_mime_type',
        'ktp_size',
        'status',
        'catatan_petugas',
        'diproses_oleh',
        'diproses_pada',
        'user_id',
        'warga_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusRegistrasi::class,
            'diproses_pada' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RegistrasiAkunLog::class);
    }
}
