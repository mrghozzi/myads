<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'widget_left',
        'widget_right',
        'meta_description',
        'meta_keywords',
        'order',
    ];

    protected $casts = [
        'widget_left' => 'boolean',
        'widget_right' => 'boolean',
    ];

    /**
     * Get the widget place ID for the left column.
     * Uses formula: 100 + (id * 2) - 1
     */
    public function getLeftPlaceId(): int
    {
        return 100 + ($this->id * 2) - 1;
    }

    /**
     * Get the widget place ID for the right column.
     * Uses formula: 100 + (id * 2)
     */
    public function getRightPlaceId(): int
    {
        return 100 + ($this->id * 2);
    }

    /**
     * Get the public URL of the page.
     */
    public function getUrl(): string
    {
        return url('/page/' . $this->slug);
    }

    /**
     * Scope to only published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
