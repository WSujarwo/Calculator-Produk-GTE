<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailCollar extends Model
{
    protected $table = 'ejm_detail_collars';

    protected $fillable = [
        'pce_item_id',
        'qty_kanan_kiri',
        'width',
        'length',
        'square_sqm',
        'time_cutting_minute',
        'time_roll_minute',
        'time_welding_minute',
        'total_time_minute',
        'part_number_plate',
        'description_plate',
        'part_number_collar',
        'description_collar',
        'raw_material',
        'raw_material_code',
        'price_sqm',
        'cost_raw_material',
        'price_validasi_machine',
        'cost_machine_material',
        'rate_per_hour',
        'quantity',
        'total_cost_manpower',
        'total_price',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}
