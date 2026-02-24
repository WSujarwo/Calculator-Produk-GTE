<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    use HasFactory;

    protected $table = 'marketings';

    protected $fillable = [
        'marketing_no',
        'name',
        'email',
        'phone',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'marketing_id');
    }
}