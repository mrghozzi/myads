<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SeoSetting extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';

    protected $fillable = [
        'default_title',
        'default_description',
        'default_keywords',
        'default_robots',
        'canonical_mode',
        'default_og_image',
        'default_twitter_card',
        'ga4_enabled',
        'ga4_measurement_id',
        'google_site_verification',
        'bing_site_verification',
        'yandex_site_verification',
        'allow_indexing',
        'robots_allow_paths',
        'robots_disallow_paths',
        'robots_extra',
        'head_snippets',
    ];

    protected $casts = [
        'ga4_enabled' => 'boolean',
        'allow_indexing' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'default_title' => config('app.name', 'MyAds'),
            'default_description' => null,
            'default_keywords' => null,
            'default_robots' => 'index,follow,max-image-preview:large',
            'canonical_mode' => 'strip_tracking',
            'default_og_image' => null,
            'default_twitter_card' => 'summary_large_image',
            'ga4_enabled' => false,
            'ga4_measurement_id' => null,
            'google_site_verification' => null,
            'bing_site_verification' => null,
            'yandex_site_verification' => null,
            'allow_indexing' => true,
            'robots_allow_paths' => "/\n",
            'robots_disallow_paths' => "/admin\n/login\n/register\n/password/reset\n/messages\n/notification\n/profile/edit\n/settings\n",
            'robots_extra' => null,
            'head_snippets' => null,
        ];
    }

    public static function current(): self
    {
        if (!Schema::hasTable('seo_settings')) {
            return new static(static::defaults());
        }

        return static::query()->first() ?? new static(static::defaults());
    }

    public static function currentPersisted(): self
    {
        if (!Schema::hasTable('seo_settings')) {
            return new static(static::defaults());
        }

        return static::query()->first() ?? static::query()->create(static::defaults());
    }
}
