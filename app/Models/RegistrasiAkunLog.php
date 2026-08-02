<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrasiAkunLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'registrasi_akun_id',
        'user_id',
        'action',
        'status_sebelum',
        'status_sesudah',
        'catatan',
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

    public function registrasiAkun(): BelongsTo
    {
        return $this->belongsTo(RegistrasiAkun::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
