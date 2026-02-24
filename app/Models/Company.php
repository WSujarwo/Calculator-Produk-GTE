<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'company_code',
        'company_name',
        'address',
        'city',
        'country',
        'phone',
        'email',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'company_id');
    }
}