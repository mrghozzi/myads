<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomAdCreative extends Model
{
    use HasFactory;

    public const STATUS_APPROVED = 'approved';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'deal_id',
        'token',
        'format',
        'headline',
        'body',
        'image_url',
        'target_url',
        'button_label',
        'background_color',
        'text_color',
        'accent_color',
        'status',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'impressions' => 'integer',
        'clicks' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (CustomAdCreative $creative) {
            if (trim((string) $creative->token) === '') {
                $creative->token = self::newToken();
            }
        });
    }

    public static function newToken(): string
    {
        do {
            $token = Str::lower(Str::random(40));
        } while (self::query()->where('token', $token)->exists());

        return $token;
    }

    public function deal()
    {
        return $this->belongsTo(CustomAdDeal::class, 'deal_id');
    }

    public function events()
    {
        return $this->hasMany(CustomAdEvent::class, 'creative_id');
    }

    public function ctr(): float
    {
        return (int) $this->impressions > 0
            ? round(((int) $this->clicks / (int) $this->impressions) * 100, 2)
            : 0.0;
    }
}
