<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAdditionalBag extends Model
{
    public $incrementing = true;
    
    protected $fillable = [
        'customer_id',
        'additional_bags',
        'remarks',
        'added_by',
    ];

    protected $casts = [
        'additional_bags' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
