<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use App\Traits\HasPrivacy;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class Product extends Model
{
    use HasFactory, HasPrivacy;

    protected $authorIdColumn = 'o_parent';

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

    // Relationship to active/inactive sales
    public function sale()
    {
        return $this->hasOne(StoreSale::class, 'product_id');
    }

    public function getHasActiveSaleAttribute()
    {
        return $this->sale && $this->sale->is_active;
    }

    public function isOnSale()
    {
        return (bool) $this->has_active_sale;
    }

    public function getSalePriceAttribute()
    {
        return $this->has_active_sale ? $this->sale->sale_price : null;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->has_active_sale ? $this->sale->sale_price : $this->o_order;
    }


    /**
     * Relationship to status options (for suspension check).
     */
    public function statusOptions()
    {
        return $this->hasMany(Option::class, 'o_parent', 'id')->where('o_type', 'store_status');
    }

    /**
     * Check if product is suspended.
     */
    public function getIsSuspendedAttribute()
    {
        return $this->statusOptions()->where('name', 'suspended')->exists();
    }

    /**
     * Check if product is pending admin approval.
     */
    public function getIsPendingAttribute()
    {
        return $this->statusOptions()->where('name', 'pending')->exists();
    }

    /**
     * Get computed moderation status string ('suspended', 'pending', or 'active').
     */
    public function getModerationStatusAttribute(): string
    {
        if ($this->is_suspended) {
            return 'suspended';
        }
        if ($this->is_pending) {
            return 'pending';
        }
        return 'active';
    }

    /**
     * Override scopeVisible to handle suspension and pending moderation.
     */
    public function scopeVisible(Builder $query, ?User $viewer = null, ?string $column = null): Builder
    {
        $viewer = $viewer ?? Auth::user();
        $authorIdColumn = $column ?? $this->getAuthorIdColumn();

        // 1. If viewer is Admin, they see everything (including suspended and pending)
        if ($viewer && $viewer->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($viewer, $authorIdColumn) {
            // 2. Original HasPrivacy-like logic for products
            $q->where(function ($inner) use ($viewer, $authorIdColumn) {
                // Owner can always see their own content
                if ($viewer) {
                    $inner->orWhere($authorIdColumn, $viewer->id);
                }

                // Public profile visibility (or fallback to public if no row exists)
                $inner->orWhereNotIn($authorIdColumn, function ($sub) {
                    $sub->select('user_id')
                        ->from('user_privacy_settings')
                        ->where('profile_visibility', '!=', 'public');
                });

                // Followers visibility
                if ($viewer) {
                    $followingIds = Like::where('uid', $viewer->id)
                        ->where('type', 1)
                        ->pluck('sid');
                    if ($followingIds->isNotEmpty()) {
                        $inner->orWhere(function ($q2) use ($authorIdColumn, $followingIds) {
                            $q2->whereIn($authorIdColumn, $followingIds)
                               ->whereIn($authorIdColumn, function ($sub) {
                                   $sub->select('user_id')
                                       ->from('user_privacy_settings')
                                       ->where('profile_visibility', 'followers');
                               });
                        });
                    }
                }
            });

            // 3. AND it must NOT be suspended or pending (unless owner)
            $q->where(function ($s) use ($viewer, $authorIdColumn) {
                $s->whereDoesntHave('statusOptions', function ($sub) {
                    $sub->whereIn('name', ['suspended', 'pending']);
                });

                if ($viewer) {
                    $s->orWhere($authorIdColumn, $viewer->id);
                }
            });
        });
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

    /**
     * Get the name of the script this product is associated with.
     */
    public function getAssociatedScriptNameAttribute(): ?string
    {
        if ($this->type && $this->type->o_mode) {
            // Using Option model directly to bypass any scopes on Product
            return Option::where('o_type', 'store')
                ->where('id', $this->type->o_mode)
                ->value('name');
        }
        return null;
    }

    /**
     * Get total downloads count for this product across all file versions.
     */
    public function getDownloadsCountAttribute(): int
    {
        $fileIds = $this->files()->pluck('id');
        if ($fileIds->isEmpty()) {
            return 0;
        }
        return (int) \App\Models\Short::where('sh_type', 7867)->whereIn('tp_id', $fileIds)->sum('clik');
    }

    /**
     * Relationship to product reviews.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')->latest();
    }

    /**
     * Relationship to product media assets.
     */
    public function media()
    {
        return $this->hasMany(ProductMedia::class, 'product_id', 'id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Screenshots media items.
     */
    public function screenshots()
    {
        return $this->hasMany(ProductMedia::class, 'product_id', 'id')
            ->where('media_type', 'screenshot')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Average 5-star rating (rounded to 1 decimal place).
     */
    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    /**
     * Total number of reviews.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Breakdown of ratings (counts and percentages for 1..5 stars).
     */
    public function getRatingBreakdownAttribute(): array
    {
        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $reviews = $this->reviews;
        $total = $reviews->count();

        foreach ($reviews as $review) {
            $r = (int) $review->rating;
            if (isset($counts[$r])) {
                $counts[$r]++;
            }
        }

        $percentages = [];
        foreach ($counts as $star => $count) {
            $percentages[$star] = $total > 0 ? round(($count / $total) * 100) : 0;
        }

        return [
            'total' => $total,
            'counts' => $counts,
            'percentages' => $percentages,
        ];
    }

    /**
     * Live preview / demo URL.
     */
    public function getLiveDemoUrlAttribute(): ?string
    {
        return $this->media()
            ->where('media_type', 'demo_url')
            ->value('url');
    }

    /**
     * Video preview URL.
     */
    public function getVideoPreviewUrlAttribute(): ?string
    {
        return $this->media()
            ->where('media_type', 'video')
            ->value('url');
    }
}

