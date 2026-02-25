<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    protected $fillable = [
        'quotation_no',
        'quotation_date',
        'revision_no',
        'company_id',
        'marketing_id',
        'attention',
        'delivery_to',
        'delivery_term',
        'payment_days',
        'delivery_time_days',
        'scope_of_work',
        'price_validity_weeks',
        'company_address',
        'status',
        'result_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'revision_no' => 'integer',
        'payment_days' => 'integer',
        'delivery_time_days' => 'integer',
        'price_validity_weeks' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id');
    }

    // (opsional) kalau nanti created_by/updated_by mengarah ke users
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
