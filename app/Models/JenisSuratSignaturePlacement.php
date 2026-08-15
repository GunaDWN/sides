<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisSuratSignaturePlacement extends Model
{
    protected $fillable = [
        'jenis_surat_id',
        'jabatan_id',
        'placeholder_text',
        'pos_x',
        'pos_y',
        'halaman',
        'lebar',
        'tinggi',
        'tampilkan_nama',
        'tampilkan_jabatan',
        'tampilkan_stempel',
    ];

    protected function casts(): array
    {
        return [
            'pos_x' => 'float',
            'pos_y' => 'float',
            'halaman' => 'integer',
            'lebar' => 'float',
            'tinggi' => 'float',
            'tampilkan_nama' => 'boolean',
            'tampilkan_jabatan' => 'boolean',
            'tampilkan_stempel' => 'boolean',
        ];
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * Determine placement type based on template format.
     * 'placeholder' for DOCX, 'coordinate' for PDF.
     */
    public function getPlacementType(): string
    {
        if (!empty($this->placeholder_text)) {
            return 'placeholder';
        }

        if ($this->pos_x !== null && $this->pos_y !== null) {
            return 'coordinate';
        }

        return 'none';
    }

    /**
     * Check if this placement is properly configured.
     */
    public function isConfigured(): bool
    {
        return $this->getPlacementType() !== 'none';
    }
}
