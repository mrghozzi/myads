<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportCategory extends Model
{
    use HasFactory;

    protected $table = 'support_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'topics_count',
        'replies_count',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'topics_count' => 'integer',
        'replies_count' => 'integer',
    ];

    /**
     * Localized name accessor based on category slug.
     */
    public function getNameAttribute($value)
    {
        if (!empty($this->slug)) {
            $key = 'support.cat_' . str_replace('-', '_', $this->slug);
            if (\Illuminate\Support\Facades\Lang::has($key)) {
                return __($key);
            }
        }
        return $value;
    }

    /**
     * Localized description accessor based on category slug.
     */
    public function getDescriptionAttribute($value)
    {
        if (!empty($this->slug)) {
            $key = 'support.cat_desc_' . str_replace('-', '_', $this->slug);
            if (\Illuminate\Support\Facades\Lang::has($key)) {
                return __($key);
            }
        }
        return $value;
    }

    public function topics()
    {
        return $this->hasMany(SupportTopic::class, 'category_id')->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}
