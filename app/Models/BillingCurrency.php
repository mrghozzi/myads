<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCurrency extends Model
{
    use HasFactory;

    protected $table = 'billing_currencies';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'decimal_places',
        'is_active',
        'is_base',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'decimal_places' => 'integer',
        'is_active' => 'boolean',
        'is_base' => 'boolean',
        'sort_order' => 'integer',
    ];
}
