<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'part_number',
        'description',
        'naming',
        'code1',
        'code2',
        'code3',
        'thk',
        'quality',
        'price_sqm',
        'price_kg',
        'price_gram',
        'berat_gr',
        'panjang_meter',
        'berat_per_meter_gr',
    ];

    protected $casts = [
        'price_sqm' => 'decimal:4',
        'price_kg' => 'decimal:4',
        'price_gram' => 'decimal:6',
        'berat_gr' => 'decimal:6',
        'panjang_meter' => 'decimal:6',
        'berat_per_meter_gr' => 'decimal:6',
    ];
}
