<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataValidasiEjmExpansionJoint extends Model
{
    protected $table = 'validasi_dataejm_expansion_joint';

    protected $fillable = [
        'standard_version_id',
        'shape_code',
        'size_code',
        'inch',
        'nb',
        'id_mm',
        'od_mm',
        'thk',
        'ly',
        'noc',
        'is_active',
    ];

    protected $casts = [
        'standard_version_id' => 'integer',
        'inch' => 'integer',
        'nb' => 'integer',
        'id_mm' => 'decimal:2',
        'od_mm' => 'decimal:2',
        'thk' => 'decimal:2',
        'ly' => 'decimal:2',
        'noc' => 'integer',
        'is_active' => 'boolean',
    ];
}
