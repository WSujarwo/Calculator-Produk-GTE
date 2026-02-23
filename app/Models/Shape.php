<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shape extends Model
{
    protected $fillable = [
        'shape_code',
        'shape_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productShapes(): HasMany
    {
        return $this->hasMany(ProductShape::class);
    }

    public function typeConfigs(): HasMany
    {
        return $this->hasMany(ProductTypeConfig::class);
    }
}