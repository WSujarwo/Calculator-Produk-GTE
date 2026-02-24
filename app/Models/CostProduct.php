<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostProduct extends Model
{
    protected $table = 'cost_products';

    protected $fillable = [
        'dlaborno',
        'cost',
        'glaccount',
        'status',
        'accountname',
        'statuse',
        'description',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'status' => 'integer',
    ];
}
