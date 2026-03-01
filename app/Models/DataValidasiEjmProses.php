<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataValidasiEjmProses extends Model
{
    protected $table = 'ejm_process_times';

    protected $fillable = [
        'process_definition_id',
        'nb',
        'noc',
        'minutes_inner',
        'minutes_outer',
        'notes',
    ];

    protected $casts = [
        'process_definition_id' => 'integer',
        'nb' => 'integer',
        'noc' => 'integer',
        'minutes_inner' => 'decimal:2',
        'minutes_outer' => 'decimal:2',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(EjmProcessDefinition::class, 'process_definition_id');
    }

    public function scopeComponent($query, string $componentType)
    {
        return $query->whereHas('definition', fn ($inner) => $inner->where('component_type', $componentType));
    }

    public function scopeProcess($query, string $processName)
    {
        return $query->whereHas('definition', fn ($inner) => $inner->where('process_name', $processName));
    }

    public function scopeNb($query, int $nb)
    {
        return $query->where('nb', $nb);
    }
}
