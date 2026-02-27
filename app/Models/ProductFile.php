<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductFile extends Option
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('store_file', function (Builder $builder) {
            $builder->where('o_type', 'store_file');
        });
    }
}
