<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EjmProcessDefinition extends Model
{
    protected $table = 'ejm_process_definitions';

    protected $fillable = [
        'component_type',
        'process_name',
        'sequence',
        'has_inner_outer',
        'rate_inner_per_hour',
        'rate_outer_per_hour',
        'currency',
        'unit',
        'notes',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'has_inner_outer' => 'boolean',
        'rate_inner_per_hour' => 'decimal:2',
        'rate_outer_per_hour' => 'decimal:2',
    ];

    public function times(): HasMany
    {
        return $this->hasMany(DataValidasiEjmProses::class, 'process_definition_id');
    }
}
