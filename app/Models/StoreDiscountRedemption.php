<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDiscountRedemption extends Model
{
    use HasFactory;

    protected $table = 'store_discount_redemptions';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'discount_code_id',
        'product_id',
        'points_saved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function discountCode()
    {
        return $this->belongsTo(StoreDiscountCode::class, 'discount_code_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
