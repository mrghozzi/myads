<?php

namespace App\Http\Controllers;

use App\Models\CustomAdCreative;
use App\Models\CustomAdPlacement;
use App\Services\CustomAds\CustomAdServingService;
use App\Services\SmartAdGeoResolver;
use App\Support\CustomAdsSettings;
use App\Support\SmartAdTargeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomAdsServingController extends Controller
{
    public function embed(CustomAdServingService $serving)
    {
        if (!CustomAdsSettings::enabled() || !$this->hasCustomAdsTables()) {
            return $this->javascriptResponse('// Custom ads are disabled');
        }

        return $this->javascriptResponse($serving->renderLoaderScript());
    }

    public function serve(Request $request, SmartAdGeoResolver $geoResolver, CustomAdServingService $serving)
    {
        $placementKey = trim((string) $request->query('placement', ''));
        $slotId = $this->normalizeSlotId($request->query('slot'));

        if (!CustomAdsSettings::enabled() || !$this->hasCustomAdsTables()) {
            return $this->javascriptResponse('// Custom ads are disabled');
        }

        if ($placementKey === '') {
            return $this->javascriptResponse('// Missing placement key');
        }

        $placement = CustomAdPlacement::query()
            ->where('placement_key', $placementKey)
            ->first();

        if (!$placement) {
            return $this->javascriptResponse('// Custom ad placement not found');
        }

        $countryCode = $geoResolver->resolveCountryCode($request);
        $deviceType = $this->resolveDeviceType($request);
        $deal = $serving->selectDeal($placement);

        if (!$deal || !$deal->creative) {
            return $this->javascriptResponse($serving->renderInsertionScript(
                $serving->renderFallbackMarkup($placement),
                $slotId
            ));
        }

        $serving->recordImpression($placement, $deal, $deal->creative, $request, $countryCode, $deviceType);

        return $this->javascriptResponse($serving->renderInsertionScript(
            $serving->renderMarkup($placement, $deal->creative),
            $slotId
        ));
    }

    public function click(string $token, Request $request, SmartAdGeoResolver $geoResolver, CustomAdServingService $serving)
    {
        if (!$this->hasCustomAdsTables()) {
            return redirect('/');
        }

        $creative = CustomAdCreative::query()
            ->with('deal.placement')
            ->where('token', $token)
            ->first();

        if (!$creative) {
            return redirect('/');
        }

        $deal = $creative->deal;
        if ($deal && $deal->status === \App\Models\CustomAdDeal::STATUS_ACTIVE) {
            $serving->recordClick(
                $creative,
                $request,
                $geoResolver->resolveCountryCode($request),
                $this->resolveDeviceType($request)
            );
        }

        return redirect()->away($creative->target_url);
    }

    private function javascriptResponse(string $script)
    {
        return response($script, 200)
            ->header('Content-Type', 'application/javascript; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    private function normalizeSlotId($value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        return $normalized !== '' ? mb_substr($normalized, 0, 180) : null;
    }

    private function resolveDeviceType(Request $request): string
    {
        $requested = SmartAdTargeting::normalizeDeviceTypes([(string) $request->query('dv', '')]);

        if ($requested !== []) {
            return $requested[0];
        }

        $userAgent = strtolower((string) $request->userAgent());
        $isTablet = preg_match('/ipad|tablet|playbook|silk|android(?!.*mobile)/i', $userAgent) === 1;
        $isMobile = !$isTablet && preg_match('/iphone|ipod|android|mobile/i', $userAgent) === 1;

        if ($isTablet) {
            return 'tablet';
        }

        return $isMobile ? 'mobile' : 'desktop';
    }

    private function hasCustomAdsTables(): bool
    {
        try {
            return Schema::hasTable('custom_ad_placements')
                && Schema::hasTable('custom_ad_deals')
                && Schema::hasTable('custom_ad_creatives')
                && Schema::hasTable('custom_ad_events');
        } catch (\Throwable) {
            return false;
        }
    }
}
