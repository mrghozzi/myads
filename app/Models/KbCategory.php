<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KbCategory extends Model
{
    protected $table = 'kb_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    /**
     * Get all knowledgebase articles in this category.
     */
    public function articles()
    {
        return $this->hasMany(Option::class, 'kb_category_id')
            ->where('o_type', 'knowledgebase')
            ->where('o_order', 0);
    }

    /**
     * Auto-generate slug from name on create.
     */
    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);

                // Ensure uniqueness
                $original = $category->slug;
                $counter = 1;
                while (static::where('slug', $category->slug)->exists()) {
                    $category->slug = $original . '-' . $counter++;
                }
            }
        });
    }
}
