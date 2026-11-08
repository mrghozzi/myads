<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DirectoryCategory extends Model
{
    use HasFactory;

    protected $table = 'cat_dir';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sub',
        'ordercat',
        'statu',
        'txt',
        'metakeywords',
    ];

    protected $appends = [
        'display_icon',
    ];

    public function parent()
    {
        return $this->belongsTo(DirectoryCategory::class, 'sub', 'id');
    }

    public function children()
    {
        return $this->hasMany(DirectoryCategory::class, 'sub', 'id')->orderBy('ordercat', 'asc');
    }

    public function directories()
    {
        return $this->hasMany(Directory::class, 'cat', 'id');
    }

    public function listings()
    {
        return $this->directories();
    }

    public function getDisplayIconAttribute(): string
    {
        if (!empty($this->icon)) {
            return $this->icon;
        }

        return (int) $this->sub === 0 ? 'fa-solid fa-folder' : 'fa-solid fa-folder-open';
    }

    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name) ?: ('cat-' . time());
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $category->slug = $slug;
            }
        });
    }
}
