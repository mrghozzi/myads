<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreDiscountCode extends Model
{
    use HasFactory;

    protected $table = 'store_discount_codes';

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'discount_type',
        'discount_value',
        'applies_to',
        'target_value',
        'start_date',
        'end_date',
        'max_uses',
        'uses',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function redemptions()
    {
        return $this->hasMany(StoreDiscountRedemption::class, 'discount_code_id');
    }

    /**
     * Validate if the discount code is valid for a given product and user.
     */
    public function isValidFor(Product $product, ?User $user)
    {
        if (!$this->is_active) {
            return ['valid' => false, 'error' => 'coupon_inactive'];
        }

        $now = now();
        if ($this->start_date && $this->start_date > $now) {
            return ['valid' => false, 'error' => 'coupon_not_started'];
        }

        if ($this->end_date && $this->end_date < $now) {
            return ['valid' => false, 'error' => 'coupon_expired'];
        }

        if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
            return ['valid' => false, 'error' => 'coupon_limit_reached'];
        }

        // If it's a seller-created discount code, the product must belong to that seller
        if ($this->user_id !== null && (int) $product->o_parent !== (int) $this->user_id) {
            return ['valid' => false, 'error' => 'coupon_invalid_for_product'];
        }

        // If it's a user, check if they've already redeemed this code for this product
        if ($user) {
            $alreadyRedeemed = DB::table('store_discount_redemptions')
                ->where('user_id', $user->id)
                ->where('discount_code_id', $this->id)
                ->where('product_id', $product->id)
                ->exists();
            if ($alreadyRedeemed) {
                return ['valid' => false, 'error' => 'coupon_already_used'];
            }
        }

        // Check applies_to rules
        if ($this->applies_to === 'product') {
            if ((int) $product->id !== (int) $this->target_value) {
                return ['valid' => false, 'error' => 'coupon_invalid_for_product'];
            }
        } elseif ($this->applies_to === 'category') {
            $prodCat = strtolower($product->product_category);
            $targetCat = strtolower($this->target_value);
            if ($prodCat !== $targetCat) {
                return ['valid' => false, 'error' => 'coupon_invalid_for_category'];
            }
        } elseif ($this->applies_to === 'seller') {
            if ((int) $product->o_parent !== (int) $this->target_value) {
                return ['valid' => false, 'error' => 'coupon_invalid_for_seller'];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Calculate discount amount and final price.
     */
    public function calculateDiscount(int $basePrice)
    {
        if ($this->discount_type === 'percent') {
            $amount = (int) round(($basePrice * $this->discount_value) / 100);
        } else {
            $amount = (int) $this->discount_value;
        }

        if ($amount > $basePrice) {
            $amount = $basePrice;
        }

        $finalPrice = $basePrice - $amount;
        if ($finalPrice < 0) {
            $finalPrice = 0;
        }

        return [
            'discount_amount' => $amount,
            'final_price' => $finalPrice
        ];
    }
}
