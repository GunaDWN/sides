<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'desa_id',
        'warga_id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_active',
        'approved_at',
        'approved_by',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * Get active jabatans for this user (if user is linked to a Warga)
     */
    public function getActiveJabatans()
    {
        if (!$this->warga_id) {
            return collect();
        }

        $today = now()->format('Y-m-d');
        return WargaJabatan::where('warga_id', $this->warga_id)
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $today);
            })
            ->with(['jabatan.permissions'])
            ->get();
    }

    /**
     * Check if user has permission (Admin has all permissions, Warga gets via active Jabatan)
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }

        $activeWargaJabatans = $this->getActiveJabatans();
        foreach ($activeWargaJabatans as $wj) {
            if ($wj->jabatan && $wj->jabatan->is_active) {
                if ($wj->jabatan->permissions->contains('name', $permissionName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
