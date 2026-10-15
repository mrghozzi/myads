<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class Directory extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'directory';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'slug',
        'url',
        'txt',
        'metakeywords',
        'cat',
        'pricing_type',
        'features',
        'vu',
        'upvotes_count',
        'is_featured',
        'is_verified',
        'claimed_by',
        'statu',
        'status_code',
        'last_checked_at',
        'date',
        'prominent_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function category()
    {
        return $this->belongsTo(DirectoryCategory::class, 'cat');
    }

    public function upvotes()
    {
        return $this->hasMany(DirectoryUpvote::class, 'directory_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function claims()
    {
        return $this->hasMany(DirectoryClaim::class, 'directory_id');
    }

    public function isUpvotedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->upvotes()->where('user_id', $userId)->exists();
    }

    public function getProminentImageAttribute()
    {
        // Return stored image immediately if we have one
        if ($this->prominent_image_url) {
            return $this->prominent_image_url;
        }

        // Fallback to cache/fetch if no stored image
        return cache()->remember('directory_image_' . $this->id, 86400, function () {
            $preview = app(\App\Services\LinkPreviewService::class)->fetch($this->url);
            
            if (isset($preview['status_code']) && $preview['status_code'] >= 400) {
                return theme_asset('img/dir_link.png');
            }
            
            return $preview['image_url'] ?? null;
        });
    }
}
