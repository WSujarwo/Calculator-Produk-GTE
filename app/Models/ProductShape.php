<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductShape extends Model
{
    protected $table = 'product_shapes';

    protected $fillable = [
        'product_id',
        'shape_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function shape(): BelongsTo
    {
        return $this->belongsTo(Shape::class);
    }
}