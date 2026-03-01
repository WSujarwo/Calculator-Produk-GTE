<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailEjm extends Model
{
    protected $table = 'ejm_detail_ejms';

    protected $fillable = [
        'pce_item_id',
        'material_bellows',
        'material_pipe_end',
        'material_flange',
        'time_assembly_minute',
        'time_painting_minute',
        'time_finishing_minute',
        'manpower_rate_per_hour',
        'total_time_hour',
        'manpower_cost',
        'total_bellows',
        'total_collar',
        'total_metal_bellows',
        'total_pipe_end',
        'total_flange',
        'total',
        'margin_percent',
        'margin_amount',
        'grand_total',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}

