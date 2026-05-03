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
        'img_b',
        'vu_a',
        'clik_a',
        'vu_b',
        'clik_b',
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

    /**
     * Determines which version (A or B) to serve based on CTR.
     * Returns 'a' or 'b'.
     */
    public function getAbVersionToServe(): string
    {
        if (!$this->img_b) {
            return 'a';
        }

        $totalVu = $this->vu_a + $this->vu_b;

        // Exploration phase (first 100 impressions) - 50/50 split
        if ($totalVu < 100) {
            return rand(0, 1) === 0 ? 'a' : 'b';
        }

        // Exploitation phase (Epsilon-Greedy 80/20)
        $ctrA = $this->vu_a > 0 ? ($this->clik_a / $this->vu_a) : 0;
        $ctrB = $this->vu_b > 0 ? ($this->clik_b / $this->vu_b) : 0;

        $best = ($ctrA >= $ctrB) ? 'a' : 'b';
        $other = ($best === 'a') ? 'b' : 'a';

        // 80% chance for the best version, 20% for the other (to keep testing)
        return (rand(1, 100) <= 80) ? $best : $other;
    }
}
