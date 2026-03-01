<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EjmDetailTube extends Model
{
    protected $table = 'ejm_detail_tubes';

    protected $fillable = [
        'pce_item_id',
        'nama_barang',
        'part_number_plate',
        'description_plate',
        'mesin_roll_minute',
        'seam_welding_minute',
        'welding_machine_minute',
        'welding_rod_minute',
        'manpower',
        'penetrant',
        'rate_mesin_roll',
        'rate_seam_welding',
        'rate_welding_machine',
        'rate_welding_rod',
        'harga_material',
        'total',
        'part_number',
        'description',
    ];

    protected $casts = [
        'pce_item_id' => 'integer',
        'mesin_roll_minute' => 'decimal:2',
        'seam_welding_minute' => 'decimal:2',
        'welding_machine_minute' => 'decimal:2',
        'welding_rod_minute' => 'decimal:2',
        'manpower' => 'decimal:2',
        'penetrant' => 'decimal:2',
        'rate_mesin_roll' => 'decimal:2',
        'rate_seam_welding' => 'decimal:2',
        'rate_welding_machine' => 'decimal:2',
        'rate_welding_rod' => 'decimal:2',
        'harga_material' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function pceItem(): BelongsTo
    {
        return $this->belongsTo(PceItem::class, 'pce_item_id');
    }
}
