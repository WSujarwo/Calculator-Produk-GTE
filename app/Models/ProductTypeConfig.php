<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTypeConfig extends Model
{
    protected $table = 'product_type_configs';

    protected $fillable = [
        'product_id',
        'shape_id',     // nullable
        'type_code',
        'type_name',
        'notes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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