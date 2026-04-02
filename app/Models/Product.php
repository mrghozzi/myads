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
     * Override scopeVisible to handle suspension.
     */
    public function scopeVisible(Builder $query, ?User $viewer = null, ?string $column = null): Builder
    {
        $viewer = $viewer ?? Auth::user();
        $authorIdColumn = $column ?? $this->getAuthorIdColumn();

        // 1. If viewer is Admin, they see everything (including suspended)
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

                // Public profile visibility
                $inner->orWhereIn($authorIdColumn, function ($sub) {
                    $sub->select('user_id')
                        ->from('user_privacy_settings')
                        ->where('profile_visibility', 'public');
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

            // 3. AND it must NOT be suspended (unless owner)
            $q->where(function ($s) use ($viewer, $authorIdColumn) {
                $s->whereDoesntHave('statusOptions', function ($sub) {
                    $sub->where('name', 'suspended');
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
}
