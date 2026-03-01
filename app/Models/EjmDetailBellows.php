<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailBellows extends Model
{
    protected $table = 'ejm_detail_bellows';

    protected $fillable = [
        'pce_item_id',
        'width_inner',
        'width_outer',
        'length_inner',
        'length_outer',
        'square_inner_sqm',
        'square_outer_sqm',
        'time_cutting_inner',
        'time_cutting_outer',
        'time_roll_inner',
        'time_roll_outer',
        'time_welding_inner',
        'time_welding_outer',
        'time_hydroforming_inner',
        'time_hydroforming_outer',
        'total_time_minute',
        'part_number_plate',
        'description_plate',
        'part_number_tube',
        'description_tube',
        'part_number_bellows',
        'description_bellows',
        'raw_material',
        'raw_material_code',
        'raw_material_price_sqm',
        'cost_raw_material',
        'machine_rate_per_minute',
        'machine_cost',
        'total_cost_raw',
        'partner_hour_rate',
        'manpower_qty',
        'total_cost_manpower',
        'total_price',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}
