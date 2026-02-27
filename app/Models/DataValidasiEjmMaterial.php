<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataValidasiEjmMaterial extends Model
{
    protected $table = 'ejm_special_materials';

    protected $fillable = [
        'component',
        'material',
        'thk_mm',
        'ply',
        'size_in',
        'sch',
        'type',
        'part_number',
        'description',
        'naming',
        'code1',
        'code2',
        'code3',
        'thk_text',
        'quality',
        'price_sqm',
        'price_kg',
        'price_gram',
        'weight_gr',
        'length_m',
        'weight_per_meter_gr',
        'is_active',
    ];

    protected $casts = [
        'thk_mm' => 'decimal:3',
        'ply' => 'integer',
        'price_sqm' => 'decimal:4',
        'price_kg' => 'decimal:4',
        'price_gram' => 'decimal:6',
        'weight_gr' => 'decimal:4',
        'length_m' => 'decimal:6',
        'weight_per_meter_gr' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'part_number', 'part_number');
    }
}
