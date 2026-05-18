<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomAdPlacement extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_DISABLED = 'disabled';

    public const FORMAT_BANNER = 'banner';
    public const FORMAT_TEXT = 'text';
    public const FORMAT_NATIVE = 'native';

    protected $fillable = [
        'user_id',
        'name',
        'placement_key',
        'format',
        'size',
        'site_url',
        'description',
        'is_public',
        'status',
        'background_color',
        'text_color',
        'accent_color',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'impressions' => 'integer',
        'clicks' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (CustomAdPlacement $placement) {
            if (trim((string) $placement->placement_key) === '') {
                $placement->placement_key = self::newPlacementKey();
            }
        });
    }

    public static function newPlacementKey(): string
    {
        do {
            $key = 'cap_' . Str::lower(Str::random(28));
        } while (self::query()->where('placement_key', $key)->exists());

        return $key;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deals()
    {
        return $this->hasMany(CustomAdDeal::class, 'placement_id');
    }

    public function activeDeals()
    {
        return $this->hasMany(CustomAdDeal::class, 'placement_id')
            ->where('status', CustomAdDeal::STATUS_ACTIVE);
    }

    public function events()
    {
        return $this->hasMany(CustomAdEvent::class, 'placement_id');
    }

    public function isActive(): bool
    {
        return (string) $this->status === self::STATUS_ACTIVE;
    }

    public function ctr(): float
    {
        return (int) $this->impressions > 0
            ? round(((int) $this->clicks / (int) $this->impressions) * 100, 2)
            : 0.0;
    }
}
