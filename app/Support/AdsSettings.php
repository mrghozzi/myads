<?php

namespace App\Support;

use App\Models\Option;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class AdsSettings
{
    public const OPTION_TYPE = 'ads_settings';
    public const BRAND_NAME = 'brand_name';

    public static function brandName(): string
    {
        try {
            if (Schema::hasTable('options')) {
                $stored = trim((string) Option::where('o_type', self::OPTION_TYPE)
                    ->where('name', self::BRAND_NAME)
                    ->value('o_valuer'));

                if ($stored !== '') {
                    return $stored;
                }
            }

            if (Schema::hasTable('setting')) {
                $siteTitle = trim((string) Setting::query()->value('titer'));
                if ($siteTitle !== '') {
                    return $siteTitle;
                }
            }
        } catch (\Throwable) {
            // Fall through to config fallback for incomplete installs.
        }

        return trim((string) config('app.name', 'MyAds')) ?: 'MyAds';
    }
}
