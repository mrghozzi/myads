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
        'url',
        'txt',
        'metakeywords',
        'cat',
        'vu',
        'statu',
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
