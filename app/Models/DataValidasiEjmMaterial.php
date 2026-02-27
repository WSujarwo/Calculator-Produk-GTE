<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataValidasiEjmMaterial extends Model
{
    protected $table = 'validasi_dataejm_materials';

    protected $fillable = [
        'material_role',
        'material_name',
        'thk_mm',
        'jumlah_ply',
        'size_in',
        'sch',
        'type',
        'material_id',
        'part_number',
        'description',
        'naming',
        'quality',
        'price_sqm',
        'price_kg',
        'price_gram',
        'is_active',
    ];

    protected $casts = [
        'thk_mm' => 'decimal:3',
        'jumlah_ply' => 'integer',
        'material_id' => 'integer',
        'price_sqm' => 'decimal:4',
        'price_kg' => 'decimal:4',
        'price_gram' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
