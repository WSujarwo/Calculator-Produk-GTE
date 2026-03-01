<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailFlange extends Model
{
    protected $table = 'ejm_detail_flanges';

    protected $fillable = [
        'pce_item_id',
        'left_material',
        'left_class',
        'left_type',
        'left_part_number',
        'left_description',
        'left_qty',
        'right_material',
        'right_class',
        'right_type',
        'right_part_number',
        'right_description',
        'right_qty',
        'left_flange_price',
        'left_grinding_painting',
        'left_total',
        'right_flange_price',
        'right_grinding_painting',
        'right_total',
        'rate_per_hour',
        'manpower_qty',
        'total_cost_manpower',
        'total_price',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}

