<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PceHeader extends Model
{
    protected $table = 'pce_headers';

    protected $fillable = [
        'pce_number',
        'project_of_name',
        'end_user_id',
        'area',
        'drawing_no',
        'document_no',
        'revision',
        'date',
        'sales_id',
        'status',
    ];

    protected $casts = [
        'end_user_id' => 'integer',
        'sales_id' => 'integer',
        'date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PceItem::class, 'pce_header_id');
    }

    public function endUser(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'end_user_id');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Marketing::class, 'sales_id');
    }
}
