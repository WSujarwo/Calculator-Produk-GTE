<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Marketing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PceHeader extends Model
{
    protected $fillable = [
        'pce_number',
        'project_name',
        'end_user_id',
        'area',
        'drawing_no',
        'document_no',
        'revision',
        'pce_date',
        'sales_user_id',
        'status',
    ];

    protected $casts = [
        'pce_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'end_user_id');
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(Marketing::class, 'sales_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PceItem::class, 'pce_header_id');
    }
}
