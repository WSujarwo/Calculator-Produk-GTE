<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
