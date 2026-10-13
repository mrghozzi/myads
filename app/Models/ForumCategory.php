<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $table = 'f_cat';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icons',
        'color',
        'txt',
        'ordercat',
        'visibility',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'ordercat' => 'integer',
        'visibility' => 'integer',
    ];

    public function setTxtAttribute($value): void
    {
        // Legacy schemas may keep f_cat.txt as NOT NULL, so normalize empty input.
        $this->attributes['txt'] = $value === null ? '' : (string) $value;
    }

    public function parent()
    {
        return $this->belongsTo(ForumCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumCategory::class, 'parent_id')->orderBy('ordercat', 'asc');
    }

    public function subCategories()
    {
        return $this->children();
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubCategory(): bool
    {
        return !is_null($this->parent_id);
    }

    public function depth(): int
    {
        $depth = 0;
        $current = $this->parent;
        while ($current) {
            $depth++;
            $current = $current->parent;
        }
        return $depth;
    }

    public function allChildIds(): array
    {
        $ids = [];
        $children = $this->relationLoaded('children') ? $this->children : $this->children()->get();
        foreach ($children as $child) {
            $ids[] = (int) $child->id;
            $ids = array_merge($ids, $child->allChildIds());
        }
        return $ids;
    }

    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;
        while ($current) {
            array_unshift($ancestors, $current);
            $current = $current->parent;
        }
        return $ancestors;
    }

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'cat');
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class, 'cat')->forumOnly();
    }
}
