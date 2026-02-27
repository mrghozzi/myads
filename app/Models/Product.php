<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'options';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'o_valuer',
        'o_type',
        'o_parent',
        'o_order',
        'o_mode',
    ];

    // Global scope to only query store items
    protected static function booted()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            $builder->where('o_type', 'store');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'o_parent');
    }

    // Relationship to get associated files (also in options table)
    public function files()
    {
        return $this->hasMany(ProductFile::class, 'o_parent', 'id');
    }

    // Relationship to get product type (also in options table)
    public function type()
    {
        return $this->hasOne(ProductType::class, 'o_parent', 'id');
    }

    public function getProductImageAttribute()
    {
        if (!$this->o_mode) {
            return null;
        }
        if (Str::startsWith($this->o_mode, ['http://', 'https://'])) {
            return $this->o_mode;
        }
        if (Str::startsWith($this->o_mode, 'upload/')) {
            return asset($this->o_mode);
        }
        return asset('upload/' . ltrim($this->o_mode, '/'));
    }

    public function getProductPriceAttribute()
    {
        return $this->o_order;
    }

    public function getProductDescriptionAttribute()
    {
        return $this->o_valuer;
    }

    public function getProductCategoryAttribute()
    {
        return $this->type ? $this->type->name : '';
    }
}
