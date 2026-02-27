<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PceItem extends Model
{
    protected $table = 'pce_items';

    protected $fillable = [
        'pce_header_id',
        'line_no',
        'quantity',
        'shape_id',
        'type_config_id',
        'size_nb',
        'noc',
        'validation_id',
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
        'line_no' => 'integer',
        'quantity' => 'integer',
        'shape_id' => 'integer',
        'type_config_id' => 'integer',
        'size_nb' => 'integer',
        'noc' => 'integer',
        'validation_id' => 'integer',
        'id_mm' => 'decimal:3',
        'od_mm' => 'decimal:3',
        'thk_mm' => 'decimal:3',
        'ply' => 'decimal:2',
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
        return $this->belongsTo(DataValidasiEjmExpansionJoint::class, 'validation_id');
    }

    public function materialBellow(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_bellow_id');
    }

    public function materialFlange(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_flange_id');
    }

    public function materialPipeEnd(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_pipe_end_id');
    }
}
