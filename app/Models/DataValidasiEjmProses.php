<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataValidasiEjmProses extends Model
{
    protected $table = 'data_validasiejm_proses';

    protected $fillable = [
        'component_type',
        'process_name',
        'nb',
        'tube_inner',
        'price_tube_inner',
        'tube_outer',
        'price_tube_outer',
        'unit',
        'notes',
    ];

    protected $casts = [
        'nb' => 'integer',
        'tube_inner' => 'integer',
        'tube_outer' => 'integer',
        'price_tube_inner' => 'decimal:2',
        'price_tube_outer' => 'decimal:2',
    ];

    /**
     * Scope: filter by component_type
     */
    public function scopeComponent($query, string $componentType)
    {
        return $query->where('component_type', $componentType);
    }

    /**
     * Scope: filter by process_name
     */
    public function scopeProcess($query, string $processName)
    {
        return $query->where('process_name', $processName);
    }

    /**
     * Scope: filter by NB
     */
    public function scopeNb($query, int $nb)
    {
        return $query->where('nb', $nb);
    }
}