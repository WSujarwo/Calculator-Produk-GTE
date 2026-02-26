<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ValidasiDataEjmBellowconv extends Model
{
    protected $table = 'validasi_dataejm_bellowconvs';

    protected $fillable = [
        'size',
        'noc',
        'naming',
        'oalb_mm',
        'bl_mm',
    ];

    protected $casts = [
        'size'     => 'integer',
        'noc'      => 'integer',
        'oalb_mm'  => 'decimal:2',
        'bl_mm'    => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    // Scope by Size
    public function scopeBySize(Builder $query, int $size): Builder
    {
        return $query->where('size', $size);
    }

    // Scope by NOC
    public function scopeByNoc(Builder $query, int $noc): Builder
    {
        return $query->where('noc', $noc);
    }

    // Scope combined (paling sering dipakai estimator)
    public function scopeLookup(Builder $query, int $size, int $noc): Builder
    {
        return $query->where('size', $size)
                     ->where('noc', $noc);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Static helper untuk ambil 1 record validasi
     */
    public static function getValidation(int $size, int $noc): ?self
    {
        return self::lookup($size, $noc)->first();
    }

    /**
     * Return OALB otomatis
     */
    public function getOalb(): float
    {
        return (float) $this->oalb_mm;
    }

    /**
     * Return BL otomatis
     */
    public function getBl(): float
    {
        return (float) $this->bl_mm;
    }
}