<?php

namespace App\Models;

use App\Support\SmartAdTargeting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAd extends Model
{
    use HasFactory;

    protected $table = 'smart_ads';

    protected $fillable = [
        'uid',
        'landing_url',
        'headline_override',
        'description_override',
        'image',
        'countries',
        'devices',
        'manual_keywords',
        'extracted_keywords',
        'source_title',
        'source_description',
        'source_image',
        'impressions',
        'clicks',
        'statu',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function targetCountries(): array
    {
        return SmartAdTargeting::normalizeCountryCodes($this->countries);
    }

    public function targetDevices(): array
    {
        return SmartAdTargeting::normalizeDeviceTypes($this->devices);
    }

    public function topicTokens(): array
    {
        return SmartAdTargeting::buildTopicTokens([
            $this->headline_override,
            $this->description_override,
            $this->manual_keywords,
            $this->extracted_keywords,
            $this->source_title,
            $this->source_description,
        ]);
    }

    public function displayTitle(): string
    {
        $title = trim((string) ($this->headline_override ?: $this->source_title));

        if ($title !== '') {
            return $title;
        }

        $host = parse_url((string) $this->landing_url, PHP_URL_HOST);

        return $host ? (string) $host : __('messages.smart_ad');
    }

    public function displayDescription(): string
    {
        $description = trim((string) ($this->description_override ?: $this->source_description ?: $this->manual_keywords));

        if ($description !== '') {
            return $description;
        }

        return __('messages.smart_default_description');
    }

    public function displayImage(): ?string
    {
        $image = trim((string) ($this->image ?: $this->source_image));

        return $image !== '' ? $image : null;
    }
}
