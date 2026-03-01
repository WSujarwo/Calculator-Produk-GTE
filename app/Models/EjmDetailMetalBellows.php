<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailMetalBellows extends Model
{
    protected $table = 'ejm_detail_metal_bellows';

    protected $fillable = [
        'pce_item_id',
        'width',
        'length',
        'oal',
        'noc',
        'material',
        'part_number_bellows',
        'description_bellows',
        'part_number_collar',
        'description_collar',
        'welding_rod_qty',
        'mesin_qty',
        'manpower_qty',
        'grinda_poles_qty',
        'disc_poles_qty',
        'harga_bellows',
        'harga_collar',
        'rate_welding_rod',
        'rate_mesin',
        'rate_manpower',
        'rate_grinda_poles',
        'rate_disc_poles',
        'total',
        'grand_total',
        'part_number_metal_bellows',
        'description_metal_bellows',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}

