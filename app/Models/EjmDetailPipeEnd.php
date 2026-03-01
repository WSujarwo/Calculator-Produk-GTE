<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailPipeEnd extends Model
{
    protected $table = 'ejm_detail_pipe_ends';

    protected $fillable = [
        'pce_item_id',
        'length',
        'time_cutting_minute',
        'time_bevel_minute',
        'time_grinding_minute',
        'total_time_minute',
        'raw_material',
        'raw_material_code',
        'price_sqm',
        'cost_raw_material',
        'price_validasi_machine',
        'cost_machine',
        'rate_per_hour',
        'quantity',
        'total_cost',
        'total_price',
        'part_number_pipe_end',
        'description_pipe_end',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}

