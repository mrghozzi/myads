<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Option
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('store_type', function (Builder $builder) {
            $builder->where('o_type', 'store_type');
        });
    }
}
