<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\BannerSizeCatalog;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banner';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'statu',
        'vu',
        'clik',
        'url',
        'img',
        'px',
        'countries',
        'devices',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function getPxAttribute($value)
    {
        return BannerSizeCatalog::normalize($value) ?? $value;
    }

    public function setPxAttribute($value): void
    {
        $this->attributes['px'] = BannerSizeCatalog::normalize($value) ?? $value;
    }

    public function targetCountries(): array
    {
        return \App\Support\SmartAdTargeting::normalizeCountryCodes($this->countries);
    }

    public function targetDevices(): array
    {
        return \App\Support\SmartAdTargeting::normalizeDeviceTypes($this->devices);
    }
}
