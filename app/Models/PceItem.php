<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PceItem extends Model
{
    protected $table = 'pce_items';

    protected $fillable = [
        'pce_header_id',
        'plat_number',
        'description',
        'qty',
        'shape_id',
        'type_config_id',
        'nb',
        'noc',
        'expansion_joint_validation_id',
        'id_mm',
        'od_mm',
        'thk_mm',
        'ply',
        'material_bellow_id',
        'material_flange_id',
        'material_pipe_end_id',
        'status',
    ];

    protected $casts = [
        'pce_header_id' => 'integer',
        'qty' => 'integer',
        'shape_id' => 'integer',
        'type_config_id' => 'integer',
        'nb' => 'integer',
        'noc' => 'integer',
        'expansion_joint_validation_id' => 'integer',
        'id_mm' => 'decimal:3',
        'od_mm' => 'decimal:3',
        'thk_mm' => 'decimal:3',
        'ply' => 'integer',
        'material_bellow_id' => 'integer',
        'material_flange_id' => 'integer',
        'material_pipe_end_id' => 'integer',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(PceHeader::class, 'pce_header_id');
    }

    public function shape(): BelongsTo
    {
        return $this->belongsTo(Shape::class);
    }

    public function typeConfig(): BelongsTo
    {
        return $this->belongsTo(ProductTypeConfig::class, 'type_config_id');
    }

    public function validation(): BelongsTo
    {
        return $this->belongsTo(DataValidasiEjmExpansionJoint::class, 'expansion_joint_validation_id');
    }

    public function materialBellow(): BelongsTo
    {
        return $this->belongsTo(DataValidasiEjmMaterial::class, 'material_bellow_id');
    }

    public function materialFlange(): BelongsTo
    {
        return $this->belongsTo(DataValidasiEjmMaterial::class, 'material_flange_id');
    }

    public function materialPipeEnd(): BelongsTo
    {
        return $this->belongsTo(DataValidasiEjmMaterial::class, 'material_pipe_end_id');
    }

    public function detailTube(): HasOne
    {
        return $this->hasOne(EjmDetailTube::class, 'pce_item_id');
    }

    public function detailBellows(): HasOne
    {
        return $this->hasOne(EjmDetailBellows::class, 'pce_item_id');
    }

    public function detailCollar(): HasOne
    {
        return $this->hasOne(EjmDetailCollar::class, 'pce_item_id');
    }

    public function detailMetalBellows(): HasOne
    {
        return $this->hasOne(EjmDetailMetalBellows::class, 'pce_item_id');
    }

    public function detailPipeEnd(): HasOne
    {
        return $this->hasOne(EjmDetailPipeEnd::class, 'pce_item_id');
    }

    public function detailFlange(): HasOne
    {
        return $this->hasOne(EjmDetailFlange::class, 'pce_item_id');
    }

    public function detailEjm(): HasOne
    {
        return $this->hasOne(EjmDetailEjm::class, 'pce_item_id');
    }
}
