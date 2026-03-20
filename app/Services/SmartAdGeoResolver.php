<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SmartAdGeoResolver
{
    public function resolveCountryCode(Request $request): string
    {
        $headerCountry = strtoupper(trim((string) ($request->header('CF-IPCountry') ?? '')));
        if (preg_match('/^[A-Z]{2}$/', $headerCountry)) {
            return $headerCountry;
        }

        $ip = (string) $request->ip();
        if ($ip === '' || $this->isPrivateIp($ip)) {
            return 'ZZ';
        }

        return Cache::remember('smart_ads_geo:' . $ip, 3600 * 12, function () use ($ip) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'MyAds-SmartAds/1.0',
                    'Accept' => 'application/json',
                ])->timeout(5)->get('https://ipwho.is/' . urlencode($ip));

                if ($response->successful()) {
                    $country = strtoupper(trim((string) $response->json('country_code')));
                    if (preg_match('/^[A-Z]{2}$/', $country)) {
                        return $country;
                    }
                }
            } catch (\Throwable) {
                // Ignore external lookup failures and fall back to unknown.
            }

            return 'ZZ';
        });
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
