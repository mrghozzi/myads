<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BannerImpression;
use App\Models\SmartAd;
use App\Models\SmartAdImpression;
use App\Models\User;
use App\Models\Banner;
use App\Models\Link;
use App\Services\SmartAdGeoResolver;
use App\Support\AdsSettings;
use App\Support\BannerServingSettings;
use App\Support\BannerSizeCatalog;
use App\Support\SmartAdTargeting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdsServingController extends Controller
{
    // Public: Serve Banner Script (bn.php)
    public function bannerScript(Request $request)
    {
        $user_id = $request->query('ID');
        $slotId = $this->normalizeSlotId($request->query('slot'));
        ['placement' => $placementMode, 'size' => $pxValue] = $this->resolveBannerPlacement($request);

        if (!$user_id || !is_numeric($user_id)) {
            return $this->javascriptResponse('// Invalid User ID');
        }

        $user = User::find($user_id);
        if (!$user) {
            return $this->javascriptResponse('// User not found');
        }

        // 1. Update Publisher Points (Matches old bn.php: pts+1, nvu+.5)
        $user->increment('pts', 1);
        $user->increment('nvu', 0.5);

        // 2. Select a Banner
        // Logic: Banner from user who has nvu >= 1 and NOT same user
        $visitorKey = $this->resolveVisitorKey($request);
        $repeatWindowSeconds = BannerServingSettings::repeatWindowMinutes() * 60;

        $bannerQuery = Banner::where('statu', 1)
            ->whereIn('px', BannerSizeCatalog::queryCandidates($pxValue))
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('nvu', '>=', 1)->where('id', '!=', $user_id);
            });

        if ($repeatWindowSeconds > 0 && $this->bannerImpressionsEnabled()) {
            $cutoff = time() - $repeatWindowSeconds;
            $bannerQuery->whereNotExists(function ($query) use ($user_id, $visitorKey, $cutoff) {
                $query->select(DB::raw(1))
                    ->from('banner_impressions')
                    ->whereColumn('banner_impressions.banner_id', 'banner.id')
                    ->where('banner_impressions.publisher_id', (int) $user_id)
                    ->where('banner_impressions.visitor_key', $visitorKey)
                    ->where('banner_impressions.served_at', '>=', $cutoff);
            });
        }

        $banner = $bannerQuery->inRandomOrder()->first();

        if ($banner) {
            // Deduct from Advertiser
            $advertiser = User::find($banner->uid);
            if ($advertiser) {
                $advertiser->decrement('nvu', 1);
            }
            $banner->increment('vu');

            // Log State
            $this->logState($user_id, $banner->id, 'banner', $request);
            $this->recordBannerImpression($banner->id, (int) $user_id, $visitorKey);

            // Return JS to display banner (Matches old bn.php output style)
            $html = $this->renderBannerMarkup($banner, (int) $user_id, $pxValue, $placementMode);

            return $this->javascriptResponse($this->renderHtmlInsertionScript($html, $slotId));
        }

        $fallbackHtml = $this->renderBannerFallbackMarkup((int) $user_id, $pxValue);

        return $this->javascriptResponse($this->renderHtmlInsertionScript($fallbackHtml, $slotId));
    }

    // Public: Serve Link Script (link.php)
    public function linkScript(Request $request)
    {
        $user_id = $request->query('ID');
        $slotId = $this->normalizeSlotId($request->query('slot'));
        $px = $request->query('px');
        $linkPlacement = $this->normalizeLinkPlacement($px);

        if (!$user_id || !is_numeric($user_id)) {
            return $this->javascriptResponse('// Invalid User ID');
        }

        $user = User::find($user_id);
        if (!$user) {
            return $this->javascriptResponse('// User not found');
        }

        // 1. Update Publisher Points (Matches old link.php logic)
        $user->increment('pts', 1);
        // Note: old link.php does NOT increment nlink here, only pts. It increments nlink on CLICK (in show.php/AdsController::redirect).
        
        // 2. Select First Link ($ab)
        // Logic: Link from user who has nlink >= 1 and NOT same user
        $link1 = Link::where('statu', 1)
            ->whereHas('user', function ($query) use ($user_id) {
                $query->where('nlink', '>=', 1)->where('id', '!=', $user_id);
            })
            ->inRandomOrder()
            ->first();

        if (!$link1) {
             return $this->javascriptResponse('// No ads available');
        }

        // 3. Select Second Link ($ab2)
        // Logic: Link from user who has nlink >= 1, NOT same user as publisher, AND NOT same user as first link owner
        // AND NOT the same link as first link.
        $link2 = Link::where('statu', 1)
            ->where('id', '!=', $link1->id)
            ->whereHas('user', function ($query) use ($user_id, $link1) {
                $query->where('nlink', '>=', 1)
                      ->where('id', '!=', $user_id)
                      ->where('id', '!=', $link1->uid);
            })
            ->inRandomOrder()
            ->first();

        // If no second link found, what does old code do?
        // "if ($num_rows = $results2->fetchColumn() == 0) { } else { ... }"
        // It seems it proceeds but maybe logic is slightly different.
        // If we don't have a second link, we might break the template.
        // Let's assume we might reuse link1 or find another one allowing same user but diff link?
        // Or just fail gracefully.
        // For robustness, let's try to find another link even if from same user as link1 (but distinct link), 
        // or just duplicate link1 if really nothing else.
        if (!$link2) {
             // Fallback: Try finding any other link not equal to link1, even from same user
             $link2 = Link::where('statu', 1)
                ->where('id', '!=', $link1->id)
                ->whereHas('user', function ($query) use ($user_id) {
                    $query->where('nlink', '>=', 1)->where('id', '!=', $user_id);
                })
                ->inRandomOrder()
                ->first();
             
             // If still nothing, use link1 again
             if (!$link2) {
                 $link2 = $link1;
             }
        }

        // 4. Log Stats (state table)
        // Log for Link 1
        $this->logState($user_id, $link1->id, 'link', $request);
        
        // Log for Link 2 (if distinct or just log again)
        // Old code: Logs stats for both.
        if ($link2->id != $link1->id) {
            $this->logState($user_id, $link2->id, 'link', $request);
        }

        $viewName = 'theme::ads.serving.link1';
        $viewData = [];

        if ($linkPlacement === 'responsive') {
            $viewName = 'theme::ads.serving.link2';
        } elseif ($linkPlacement === 'responsive2') {
            $viewName = 'theme::ads.serving.link3';
            $viewData['linkLayout'] = $this->resolveResponsive2Layout($request);
        }

        // Publisher ID for references
        $publisherId = $user_id;

        $link1Name = $this->normalizeText($link1->name);
        $link1Txt = $this->normalizeText($link1->txt);
        $link2Name = $this->normalizeText($link2->name);
        $link2Txt = $this->normalizeText($link2->txt);

        $html = view($viewName, array_merge(compact(
            'link1',
            'link2',
            'publisherId',
            'user',
            'link1Name',
            'link1Txt',
            'link2Name',
            'link2Txt'
        ), $viewData))->render();
        
        // Strip newlines to avoid JS errors in document.write
        $html = str_replace(["\r", "\n"], ' ', $html);
        
        return $this->javascriptResponse($this->renderHtmlInsertionScript($html, $slotId));
    }

    // Public: Serve Smart Ads Script (smart.php)
    public function smartScript(Request $request, SmartAdGeoResolver $geoResolver)
    {
        $user_id = $request->query('ID');
        $slotId = $this->normalizeSlotId($request->query('slot'));

        if (!$user_id || !is_numeric($user_id)) {
            return $this->javascriptResponse('// Invalid User ID');
        }

        $user = User::find($user_id);
        if (!$user) {
            return $this->javascriptResponse('// User not found');
        }

        $publisherId = (int) $user_id;
        $slot = $this->resolveSmartSlot($request);
        $visitorKey = $this->resolveVisitorKey($request);
        $countryCode = $geoResolver->resolveCountryCode($request);
        $deviceType = $this->resolveSmartDeviceType($request);
        $contextTokens = SmartAdTargeting::buildTopicTokens([(string) $request->query('ctx', '')], 24);

        $smartAd = $this->selectSmartAdCandidate($publisherId, $countryCode, $deviceType, $contextTokens)
            ?? $this->selectSmartAdCandidate($publisherId, $countryCode, $deviceType, $contextTokens, true);
        $isSelfFallback = $smartAd !== null && (int) $smartAd->uid === $publisherId;

        if (!$isSelfFallback) {
            $user->increment('pts', 1);
            $user->increment('nsmart', 0.5);
        }

        if (!$smartAd) {
            $fallbackHtml = $this->renderSmartFallbackMarkup($publisherId, $slot);

            return $this->javascriptResponse($this->renderHtmlInsertionScript($fallbackHtml, $slotId));
        }

        $placement = $slot['banner_size'] !== null && $smartAd->displayImage() !== null ? 'banner' : 'native';

        if (!$isSelfFallback && $smartAd->user) {
            $smartAd->user->decrement('nsmart', 1);
        }

        if (!$isSelfFallback) {
            $smartAd->increment('impressions');
            $this->logState($publisherId, $smartAd->id, 'smart', $request);
            $this->recordSmartImpression($smartAd->id, $publisherId, $visitorKey, $countryCode, $deviceType, $placement);
        }

        $html = $this->renderSmartMarkup($smartAd, $publisherId, $placement, $slot['banner_size']);

        return $this->javascriptResponse($this->renderHtmlInsertionScript($html, $slotId));
    }

    public function bannerEmbedScript()
    {
        return $this->javascriptResponse($this->renderBannerEmbedLoaderScript());
    }

    public function linkEmbedScript()
    {
        return $this->javascriptResponse($this->renderLinkEmbedLoaderScript());
    }

    public function smartEmbedScript()
    {
        return $this->javascriptResponse($this->renderSmartEmbedLoaderScript());
    }

    // Public: Redirect/Track (show.php)
    public function redirect(Request $request)
    {
        // Banner Click
        if ($request->has('ads') && $request->has('vu')) {
            $bannerId = $request->input('ads');
            $publisherId = $request->input('vu');
            
            // Check if it's a link click passed via 'type' param
            if ($request->input('type') == 'link') {
                return $this->handleLinkClick($bannerId, $publisherId, $request);
            }

            if ($request->input('type') == 'smart') {
                return $this->handleSmartAdClick($bannerId, $publisherId, $request);
            }

            $banner = Banner::find($bannerId);
            if ($banner) {
                $banner->increment('clik');
                
                // Reward Publisher (Matches show.php logic?)
                // Old show.php usually rewards for clicks too.
                // Assuming +2 pts for click.
                User::where('id', $publisherId)->increment('pts', 2);

                // Log Click
                $this->logState($publisherId, $bannerId, 'vu', $request); 

                return redirect($banner->url);
            }
        }
        
        // Link Click (Legacy format support if any)
        if ($request->has('link') && $request->has('clik')) {
            return $this->handleLinkClick($request->input('link'), $request->input('clik'), $request);
        }

        return redirect('/');
    }
    
    private function handleLinkClick($linkId, $publisherId, $request)
    {
        $link = Link::find($linkId);
        if ($link) {
            $link->increment('clik');

            // Reward Publisher
            User::where('id', $publisherId)->increment('pts', 2);
            User::where('id', $publisherId)->increment('nlink', 0.5);

            // Log Click
            $this->logState($publisherId, $linkId, 'clik', $request);

            return redirect($link->url);
        }
        return redirect('/');
    }

    private function handleSmartAdClick($smartAdId, $publisherId, $request)
    {
        $smartAd = SmartAd::find($smartAdId);

        if ($smartAd) {
            if ((int) $smartAd->uid === (int) $publisherId) {
                return redirect($smartAd->landing_url);
            }

            $smartAd->increment('clicks');

            User::where('id', $publisherId)->increment('pts', 2);

            $this->logState($publisherId, $smartAdId, 'smart_click', $request);

            return redirect($smartAd->landing_url);
        }

        return redirect('/');
    }

    private function selectSmartAdCandidate(
        int $publisherId,
        string $countryCode,
        string $deviceType,
        array $contextTokens,
        bool $selfOnly = false
    ): ?SmartAd {
        $requiresAdvertiserCredit = !$selfOnly;

        return SmartAd::with('user')
            ->where('statu', 1)
            ->where('uid', $selfOnly ? '=' : '!=', $publisherId)
            ->get()
            ->filter(function (SmartAd $candidate) use ($countryCode, $deviceType, $requiresAdvertiserCredit) {
                return $this->matchesSmartAdCandidate($candidate, $countryCode, $deviceType, $requiresAdvertiserCredit);
            })
            ->map(function (SmartAd $candidate) use ($contextTokens) {
                return [
                    'ad' => $candidate,
                    'score' => $this->scoreSmartAd($candidate, $contextTokens),
                    'tie' => random_int(0, 100000),
                ];
            })
            ->sort(function (array $left, array $right) {
                if ($left['score'] === $right['score']) {
                    return $right['tie'] <=> $left['tie'];
                }

                return $right['score'] <=> $left['score'];
            })
            ->first()['ad'] ?? null;
    }

    private function matchesSmartAdCandidate(
        SmartAd $candidate,
        string $countryCode,
        string $deviceType,
        bool $requiresAdvertiserCredit = true
    ): bool {
        if (!$candidate->user) {
            return false;
        }

        if ($requiresAdvertiserCredit && (float) $candidate->user->nsmart < 1) {
            return false;
        }

        $targetCountries = $candidate->targetCountries();
        if ($targetCountries !== [] && !in_array($countryCode, $targetCountries, true)) {
            return false;
        }

        $targetDevices = $candidate->targetDevices();
        if ($targetDevices !== [] && !in_array($deviceType, $targetDevices, true)) {
            return false;
        }

        return true;
    }

    private function logState($sid, $pid, $type, $request)
    {
        // Using DB facade as State model might not exist or table 'state' is used directly
        DB::table('state')->insert([
            'sid' => $sid,
            'pid' => $pid,
            't_name' => $type,
            'r_link' => $request->server('HTTP_REFERER') ?? 'N',
            'r_date' => time(),
            'visitor_Agent' => $request->server('HTTP_USER_AGENT'),
            'v_ip' => $request->ip(),
        ]);
    }

    private function resolveVisitorKey(Request $request): string
    {
        $token = trim((string) $request->query('vt', ''));

        if ($token !== '') {
            return 'vt:' . hash('sha256', $token);
        }

        return 'fp:' . hash('sha256', $request->ip() . '|' . ($request->userAgent() ?? ''));
    }

    private function recordBannerImpression(int $bannerId, int $publisherId, string $visitorKey): void
    {
        if (!$this->bannerImpressionsEnabled()) {
            return;
        }

        BannerImpression::create([
            'banner_id' => $bannerId,
            'publisher_id' => $publisherId,
            'visitor_key' => $visitorKey,
            'served_at' => time(),
        ]);
    }

    private function bannerImpressionsEnabled(): bool
    {
        static $enabled;

        if ($enabled !== null) {
            return $enabled;
        }

        try {
            $enabled = Schema::hasTable('banner_impressions');
        } catch (\Throwable) {
            $enabled = false;
        }

        return $enabled;
    }

    private function recordSmartImpression(
        int $smartAdId,
        int $publisherId,
        string $visitorKey,
        string $countryCode,
        string $deviceType,
        string $placement
    ): void {
        if (!$this->smartImpressionsEnabled()) {
            return;
        }

        SmartAdImpression::create([
            'smart_ad_id' => $smartAdId,
            'publisher_id' => $publisherId,
            'visitor_key' => $visitorKey,
            'country_code' => $countryCode,
            'device_type' => $deviceType,
            'placement' => $placement,
            'served_at' => time(),
        ]);
    }

    private function smartImpressionsEnabled(): bool
    {
        static $enabled;

        if ($enabled !== null) {
            return $enabled;
        }

        try {
            $enabled = Schema::hasTable('smart_ad_impressions');
        } catch (\Throwable) {
            $enabled = false;
        }

        return $enabled;
    }

    private function renderBannerMarkup(Banner $banner, int $publisherId, string $pxValue, string $placementMode = 'fixed'): string
    {
        if ($placementMode === 'responsive2') {
            return $this->renderResponsive2BannerMarkup($banner, $publisherId, $pxValue);
        }

        return $this->renderClassicBannerMarkup($banner, $publisherId, $pxValue);
    }

    private function renderClassicBannerMarkup(Banner $banner, int $publisherId, string $pxValue): string
    {
        $width = $this->getWidth($pxValue);
        $height = $this->getHeight($pxValue);
        $bannerId = (int) $banner->id;
        $clickUrl = route('ads.redirect', ['ads' => $bannerId, 'vu' => $publisherId]);
        $refUrl = url('/') . '?ref=' . $publisherId;
        $reportUrl = url('/report') . '?banner=' . $bannerId;
        $bannerName = htmlspecialchars((string) $banner->name, ENT_QUOTES, 'UTF-8');
        $appName = htmlspecialchars(AdsSettings::brandName(), ENT_QUOTES, 'UTF-8');

        return "<style>.banner_{$bannerId}{background-image:url('{$banner->img}');height:{$height}px;width:{$width}px;max-width:100%;margin:0 auto;display:block;background-size:cover;background-position:center;position:relative;overflow:hidden;}.banner_{$bannerId} .banner_click_{$bannerId}{position:absolute;inset:0;display:block;text-decoration:none;z-index:1;}.banner_icon_{$bannerId}{position:absolute;top:0;left:0;display:flex;gap:4px;padding:5px;z-index:2;background-color:rgba(0,0,0,0.5);}.banner_icon_{$bannerId} a{display:inline-flex;align-items:center;justify-content:center;height:auto;width:auto;text-decoration:none;}@media screen and (max-width: {$width}px){.banner_{$bannerId}{width:100%;}}</style><div class='banner_{$bannerId}'><a class='banner_click_{$bannerId}' href='{$clickUrl}' target='_blank' rel='noopener noreferrer' aria-label='{$bannerName}'></a><div class='banner_icon_{$bannerId}'><a href='{$refUrl}' target='_blank' rel='noopener noreferrer'><img src='" . theme_asset('img/logo_w.png') . "' width='16' height='16' alt='{$appName}'></a><a href='{$reportUrl}' target='_blank' rel='noopener noreferrer' aria-label='Report'><img src='" . theme_asset('img/Alert-icon.png') . "' alt=''></a></div></div>";
    }

    private function renderResponsive2BannerMarkup(Banner $banner, int $publisherId, string $pxValue): string
    {
        $width = $this->getWidth($pxValue);
        $height = $this->getHeight($pxValue);
        $bannerId = (int) $banner->id;
        $clickUrl = route('ads.redirect', ['ads' => $bannerId, 'vu' => $publisherId]);
        $refUrl = url('/') . '?ref=' . $publisherId;
        $reportUrl = url('/report') . '?banner=' . $bannerId;
        $bannerName = htmlspecialchars((string) $banner->name, ENT_QUOTES, 'UTF-8');
        $appName = htmlspecialchars(AdsSettings::brandName(), ENT_QUOTES, 'UTF-8');
        $profile = match ($pxValue) {
            '160x600' => 'rail',
            '300x250' => 'card',
            default => 'strip',
        };
        $class = "myads-banner-smart-{$bannerId}";
        $radius = match ($profile) {
            'rail' => 8,
            'card' => 8,
            default => 6,
        };
        $labelHeight = match ($profile) {
            'rail' => 18,
            'card' => 20,
            default => 22,
        };
        $labelFontSize = match ($profile) {
            'rail' => '9px',
            'card' => '10px',
            default => '11px',
        };
        $labelPadding = match ($profile) {
            'rail' => '0 6px',
            'card' => '0 8px',
            default => '0 10px',
        };
        $infoWidth = match ($profile) {
            'rail' => 18,
            'card' => 20,
            default => 22,
        };
        $chipMaxWidth = match ($profile) {
            'rail' => 'calc(100% - 24px)',
            'card' => 'calc(100% - 28px)',
            default => 'calc(100% - 32px)',
        };
        $chipText = htmlspecialchars(__('messages.ads_by_site', ['site' => AdsSettings::brandName()]), ENT_QUOTES, 'UTF-8');
        $reportLabel = htmlspecialchars(__('messages.report'), ENT_QUOTES, 'UTF-8');

        return "<style>.{$class},.{$class} *{box-sizing:border-box;}.{$class}{position:relative;width:{$width}px;height:{$height}px;max-width:100%;margin:0 auto;display:block;overflow:hidden;border-radius:{$radius}px;background:#f1f3f4 url('{$banner->img}') center/cover no-repeat;box-shadow:0 1px 3px rgba(18,24,40,.16);font-family:Arial,'Segoe UI',sans-serif;isolation:isolate;}.{$class}__click{position:absolute;inset:0;display:block;z-index:1;text-decoration:none;}.{$class}__chrome{position:absolute;top:0;right:0;z-index:3;display:flex;align-items:stretch;max-width:{$chipMaxWidth};border-radius:0 0 0 {$radius}px;overflow:hidden;box-shadow:0 1px 2px rgba(18,24,40,.18);}.{$class}__label,.{$class}__info{display:inline-flex;align-items:center;justify-content:center;height:{$labelHeight}px;background:rgba(255,255,255,.96);text-decoration:none;line-height:1;}.{$class}__label{max-width:calc(100% - {$infoWidth}px);padding:{$labelPadding};color:#202124;font-size:{$labelFontSize};font-weight:400;letter-spacing:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;border-inline-end:1px solid #dadce0;}.{$class}__label:hover,.{$class}__info:hover{background:#f8f9fa;text-decoration:none;}.{$class}__info{width:{$infoWidth}px;min-width:{$infoWidth}px;color:#5f6368;}.{$class}__info-mark{display:inline-flex;align-items:center;justify-content:center;width:" . ($profile === 'rail' ? 11 : 13) . "px;height:" . ($profile === 'rail' ? 11 : 13) . "px;border-radius:50%;border:1px solid #5f8def;color:#5f8def;font-size:" . ($profile === 'rail' ? '8px' : '9px') . ";font-weight:700;font-style:normal;font-family:Arial,'Segoe UI',sans-serif;}@media screen and (max-width: {$width}px){.{$class}{width:100%;}}</style><div class='{$class}' data-placement='responsive2' data-size='{$pxValue}' data-profile='{$profile}'><a class='{$class}__click' href='{$clickUrl}' target='_blank' rel='noopener noreferrer' aria-label='{$bannerName}'></a><div class='{$class}__chrome'><a class='{$class}__label' href='{$refUrl}' target='_blank' rel='noopener noreferrer'>{$chipText}</a><a class='{$class}__info' href='{$reportUrl}' target='_blank' rel='noopener noreferrer' aria-label='{$reportLabel}'><span class='{$class}__info-mark'>i</span></a></div></div>";
    }

    private function renderSmartMarkup(SmartAd $smartAd, int $publisherId, string $placement, ?string $bannerSize = null): string
    {
        $viewName = $placement === 'banner'
            ? 'theme::ads.serving.smart_banner'
            : 'theme::ads.serving.smart_native';

        $html = view($viewName, [
            'smartAd' => $smartAd,
            'publisherId' => $publisherId,
            'bannerSize' => $bannerSize,
            'adsBrandName' => AdsSettings::brandName(),
            'clickUrl' => route('ads.redirect', ['ads' => $smartAd->id, 'vu' => $publisherId, 'type' => 'smart']),
            'refUrl' => url('/') . '?ref=' . $publisherId,
            'reportUrl' => url('/report') . '?smart_ad=' . $smartAd->id,
        ])->render();

        return str_replace(["\r", "\n"], ' ', $html);
    }

    private function renderBannerFallbackMarkup(int $publisherId, string $size): string
    {
        $fallbackMap = [
            '160x600' => 'img/banner/160x600.gif',
            '300x250' => 'img/banner/300x250.gif',
            '468x60' => 'img/banner/468x60.gif',
            '728x90' => 'img/banner/728x90.gif',
        ];
        $src = theme_asset($fallbackMap[$size] ?? 'img/banner/300x250.gif');
        $width = $this->getWidth($size);
        $height = $this->getHeight($size);
        $refUrl = url('/') . '?ref=' . $publisherId;

        return "<div style=\"display:block;width:{$width}px;max-width:100%;margin:0 auto;text-align:center;\"><a href='{$refUrl}' target='_blank' rel='noopener noreferrer'><img src='{$src}' width='{$width}' height='{$height}' border='0' style='display:block;max-width:100%;margin:0 auto;'></a></div>";
    }

    private function renderSmartFallbackMarkup(int $publisherId, array $slot): string
    {
        if ($slot['banner_size'] !== null) {
            $size = $slot['banner_size'];
            $fallbackMap = [
                '160x600' => 'img/banner/160x600.gif',
                '300x250' => 'img/banner/300x250.gif',
                '468x60' => 'img/banner/468x60.gif',
                '728x90' => 'img/banner/728x90.gif',
            ];
            $src = theme_asset($fallbackMap[$size] ?? 'img/banner/300x250.gif');

            return "<div style=\"width:" . $this->getWidth($size) . "px;max-width:100%;margin:0 auto;text-align:center;\"><a href='" . url('/') . "?ref={$publisherId}' target='_blank' rel='noopener noreferrer'><img src='{$src}' width='" . $this->getWidth($size) . "' height='" . $this->getHeight($size) . "' border='0' style='display:block;max-width:100%;margin:0 auto;'></a></div>";
        }

        $appName = htmlspecialchars(AdsSettings::brandName(), ENT_QUOTES, 'UTF-8');
        $refUrl = url('/') . '?ref=' . $publisherId;
        $adsByLabel = htmlspecialchars(__('messages.ads_by_site', ['site' => AdsSettings::brandName()]), ENT_QUOTES, 'UTF-8');
        $learnMoreLabel = htmlspecialchars(__('messages.smart_learn_more'), ENT_QUOTES, 'UTF-8');
        $headline = htmlspecialchars(__('messages.smart_fallback_headline', ['site' => AdsSettings::brandName()]), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(__('messages.smart_fallback_description'), ENT_QUOTES, 'UTF-8');

        return "<div style=\"box-sizing:border-box;max-width:420px;margin:0 auto;border:1px solid #e9edf5;border-radius:16px;background:#fff;padding:16px;font-family:Arial,'Segoe UI',sans-serif;box-shadow:0 12px 28px rgba(94,92,154,.08);\"><div style=\"display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;\"><span style=\"display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;background:#f4f7ff;color:#615dfa;font-size:10px;font-weight:700;text-transform:uppercase;\">{$adsByLabel}</span><a href='{$refUrl}' target='_blank' rel='noopener noreferrer' style='font-size:12px;color:#8f94b5;text-decoration:none;'>{$learnMoreLabel}</a></div><h3 style=\"margin:0 0 8px;color:#3e3f5e;font-size:18px;line-height:1.3;\">{$headline}</h3><p style=\"margin:0;color:#8f94b5;font-size:13px;line-height:1.7;\">{$description}</p></div>";
    }

    private function normalizeSlotId($value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        if ($normalized === '') {
            return null;
        }

        return mb_substr($normalized, 0, 180);
    }

    private function renderHtmlInsertionScript(string $html, ?string $slotId = null): string
    {
        $htmlLiteral = $this->toJavaScriptLiteral($html);

        return $this->renderSlotInsertionScript(<<<JS
var html={$htmlLiteral};
renderHtml(html);
JS, $slotId);
    }

    private function renderSlotInsertionScript(string $body, ?string $slotId = null): string
    {
        $slotLiteral = $this->toJavaScriptLiteral($slotId);

        return <<<JS
(function(d){
  var slotId={$slotLiteral};
  function currentScript(){
    return d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  }
  function createTarget(anchor){
    var node=d.createElement('div');
    node.setAttribute('data-myads-slot','1');
    if (anchor && anchor.parentNode && String(anchor.parentNode.tagName || '').toLowerCase() !== 'head') {
      anchor.parentNode.insertBefore(node, anchor);
      return node;
    }
    if (d.body) {
      d.body.insertBefore(node, d.body.firstChild || null);
      return node;
    }
    return null;
  }
  function resolveTarget(){
    if (slotId) {
      var byId=d.getElementById(slotId);
      if (byId) {
        return byId;
      }
    }
    return createTarget(currentScript());
  }
  function renderHtml(html){
    var target=resolveTarget();
    if (!target) {
      return;
    }
    target.innerHTML=html;
  }
  function boot(){
{$body}
  }
  if (!d.body && (!slotId || !d.getElementById(slotId))) {
    d.addEventListener('DOMContentLoaded', boot, false);
    return;
  }
  boot();
})(document);
JS;
    }

    private function toJavaScriptLiteral($value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        );
    }

    private function javascriptResponse(string $content)
    {
        return response($content, 200)
            ->header('Content-Type', 'application/javascript; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function renderBannerEmbedLoaderScript(): string
    {
        $bootstrapUrl = route('ads.script');

        return <<<JS
(function(w,d){
  var current = d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function decode(value){
    try {
      return decodeURIComponent((value || '').replace(/\\+/g, ' '));
    } catch (e) {
      return value || '';
    }
  }
  function getParams(script){
    var params = {};
    var src = script && script.src ? script.src : '';
    var queryIndex = src.indexOf('?');
    if (queryIndex === -1) {
      return params;
    }
    src.slice(queryIndex + 1).split('&').forEach(function(part){
      if (!part) {
        return;
      }
      var pairIndex = part.indexOf('=');
      var key = pairIndex === -1 ? decode(part) : decode(part.slice(0, pairIndex));
      var value = pairIndex === -1 ? '' : decode(part.slice(pairIndex + 1));
      params[key] = value;
    });
    return params;
  }
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-slot','1');
    if (script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head') {
      script.parentNode.insertBefore(slot, script);
      return slot;
    }
    if (d.body) {
      d.body.insertBefore(slot, d.body.firstChild || null);
      return slot;
    }
    return null;
  }
  function measureBox(node){
    var width=0;
    var height=0;
    if (node && node.getBoundingClientRect) {
      var rect=node.getBoundingClientRect();
      width=Math.round(rect.width || 0);
      height=Math.round(rect.height || 0);
    }
    if ((!width || !height) && node) {
      width=width || parseInt(node.clientWidth || node.offsetWidth || 0, 10) || 0;
      height=height || parseInt(node.clientHeight || node.offsetHeight || 0, 10) || 0;
    }
    var parent=node && node.parentNode ? node.parentNode : null;
    if ((!width || !height) && parent && parent.getBoundingClientRect) {
      var parentRect=parent.getBoundingClientRect();
      width=width || Math.round(parentRect.width || 0);
      height=height || Math.round(parentRect.height || 0);
    }
    if ((!width || !height) && parent) {
      width=width || parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
      height=height || parseInt(parent.clientHeight || parent.offsetHeight || 0, 10) || 0;
    }
    if (!width) {
      width=parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    return {width: width, height: height};
  }
  function appendLoader(slot, src){
    var loader=d.createElement('script');
    loader.type='text/javascript';
    loader.src=src;
    if (slot && slot.parentNode) {
      slot.parentNode.insertBefore(loader, slot.nextSibling || null);
      return;
    }
    (d.body || d.head || d.documentElement).appendChild(loader);
  }
  function boot(){
  var params = getParams(current);
  var publisherId = params.ID || params.id || '';
  var px = params.px || '';
  if (!publisherId || !px) {
    return;
  }
  var key='myads_banner_vt';
  var vt='';
  try {
    vt = w.localStorage ? (w.localStorage.getItem(key) || '') : '';
  } catch (e) {}
  if (!vt) {
    var cookieMatch = d.cookie.match(new RegExp('(?:^|; )' + key + '=([^;]*)'));
    if (cookieMatch) {
      vt = decodeURIComponent(cookieMatch[1]);
    }
  }
  if (!vt) {
    vt = 'vt-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    try {
      if (w.localStorage) {
        w.localStorage.setItem(key, vt);
      }
    } catch (e) {}
    try {
      d.cookie = key + '=' + encodeURIComponent(vt) + '; path=/; max-age=31536000; SameSite=Lax';
    } catch (e) {}
  }
  var slot = createSlot(current);
  if (!slot) {
    return;
  }
  var metrics = measureBox(slot);
  if (px === 'responsive') {
    var width = metrics.width;
    if (width >= 728) {
      px = '728x90';
    } else if (width >= 468) {
      px = '468x60';
    } else if (width >= 300) {
      px = '300x250';
    } else if (width >= 160) {
      px = '160x600';
    } else {
      px = '300x250';
    }
  }
  var src='{$bootstrapUrl}?ID=' + encodeURIComponent(publisherId) + '&px=' + encodeURIComponent(px);
  if (px === 'responsive2') {
    if (metrics.width > 0) {
      src += '&cw=' + encodeURIComponent(metrics.width);
    }
    if (metrics.height > 0) {
      src += '&ch=' + encodeURIComponent(metrics.height);
    }
  }
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  src += '&slot=' + encodeURIComponent(slot.id);
  appendLoader(slot, src);
  }
  if (d.body) {
    boot();
    return;
  }
  d.addEventListener('DOMContentLoaded', boot, false);
})(window,document);
JS;
    }

    private function renderLinkEmbedLoaderScript(): string
    {
        $bootstrapUrl = route('ads.link.script');

        return <<<JS
(function(w,d){
  var current = d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function decode(value){
    try {
      return decodeURIComponent((value || '').replace(/\\+/g, ' '));
    } catch (e) {
      return value || '';
    }
  }
  function getParams(script){
    var params = {};
    var src = script && script.src ? script.src : '';
    var queryIndex = src.indexOf('?');
    if (queryIndex === -1) {
      return params;
    }
    src.slice(queryIndex + 1).split('&').forEach(function(part){
      if (!part) {
        return;
      }
      var pairIndex = part.indexOf('=');
      var key = pairIndex === -1 ? decode(part) : decode(part.slice(0, pairIndex));
      var value = pairIndex === -1 ? '' : decode(part.slice(pairIndex + 1));
      params[key] = value;
    });
    return params;
  }
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-slot','1');
    if (script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head') {
      script.parentNode.insertBefore(slot, script);
      return slot;
    }
    if (d.body) {
      d.body.insertBefore(slot, d.body.firstChild || null);
      return slot;
    }
    return null;
  }
  function measureBox(node){
    var width=0;
    if (node && node.getBoundingClientRect) {
      width=Math.round(node.getBoundingClientRect().width || 0);
    }
    if (!width && node) {
      width=parseInt(node.clientWidth || node.offsetWidth || 0, 10) || 0;
    }
    var parent=node && node.parentNode ? node.parentNode : null;
    if (!width && parent && parent.getBoundingClientRect) {
      width=Math.round(parent.getBoundingClientRect().width || 0) || 0;
    }
    if (!width && parent) {
      width=parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
    }
    if (!width) {
      width=parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    return {width: width};
  }
  function appendLoader(slot, src){
    var loader=d.createElement('script');
    loader.type='text/javascript';
    loader.src=src;
    if (slot && slot.parentNode) {
      slot.parentNode.insertBefore(loader, slot.nextSibling || null);
      return;
    }
    (d.body || d.head || d.documentElement).appendChild(loader);
  }
  function boot(){
  var params = getParams(current);
  var publisherId = params.ID || params.id || '';
  var px = params.px || '';
  if (!publisherId || !px) {
    return;
  }
  var slot = createSlot(current);
  if (!slot) {
    return;
  }
  var src='{$bootstrapUrl}?ID=' + encodeURIComponent(publisherId) + '&px=' + encodeURIComponent(px);
  if (px === 'responsive2') {
    var width = measureBox(slot).width;
    if (!width) {
      width = parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    if (width > 0) {
      src += '&cw=' + encodeURIComponent(width);
    }
  }
  src += '&slot=' + encodeURIComponent(slot.id);
  appendLoader(slot, src);
  }
  if (d.body) {
    boot();
    return;
  }
  d.addEventListener('DOMContentLoaded', boot, false);
})(window,document);
JS;
    }

    private function renderSmartEmbedLoaderScript(): string
    {
        $bootstrapUrl = route('ads.smart.script');

        return <<<JS
(function(w,d){
  var current = d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function decode(value){
    try {
      return decodeURIComponent((value || '').replace(/\\+/g, ' '));
    } catch (e) {
      return value || '';
    }
  }
  function getParams(script){
    var params = {};
    var src = script && script.src ? script.src : '';
    var queryIndex = src.indexOf('?');
    if (queryIndex === -1) {
      return params;
    }
    src.slice(queryIndex + 1).split('&').forEach(function(part){
      if (!part) {
        return;
      }
      var pairIndex = part.indexOf('=');
      var key = pairIndex === -1 ? decode(part) : decode(part.slice(0, pairIndex));
      var value = pairIndex === -1 ? '' : decode(part.slice(pairIndex + 1));
      params[key] = value;
    });
    return params;
  }
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-slot','1');
    if (script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head') {
      script.parentNode.insertBefore(slot, script);
      return slot;
    }
    if (d.body) {
      d.body.insertBefore(slot, d.body.firstChild || null);
      return slot;
    }
    return null;
  }
  function measureBox(node){
    var width=0;
    var height=0;
    if (node && node.getBoundingClientRect) {
      var rect=node.getBoundingClientRect();
      width=Math.round(rect.width || 0);
      height=Math.round(rect.height || 0);
    }
    if ((!width || !height) && node) {
      width=width || parseInt(node.clientWidth || node.offsetWidth || 0, 10) || 0;
      height=height || parseInt(node.clientHeight || node.offsetHeight || 0, 10) || 0;
    }
    var parent=node && node.parentNode ? node.parentNode : null;
    if ((!width || !height) && parent && parent.getBoundingClientRect) {
      var parentRect=parent.getBoundingClientRect();
      width=width || Math.round(parentRect.width || 0);
      height=height || Math.round(parentRect.height || 0);
    }
    if ((!width || !height) && parent) {
      width=width || parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
      height=height || parseInt(parent.clientHeight || parent.offsetHeight || 0, 10) || 0;
    }
    if (!width) {
      width=parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    return {width: width, height: height};
  }
  function appendLoader(slot, src){
    var loader=d.createElement('script');
    loader.type='text/javascript';
    loader.src=src;
    if (slot && slot.parentNode) {
      slot.parentNode.insertBefore(loader, slot.nextSibling || null);
      return;
    }
    (d.body || d.head || d.documentElement).appendChild(loader);
  }
  function boot(){
  var params = getParams(current);
  var publisherId = params.ID || params.id || '';
  if (!publisherId) {
    return;
  }
  var key='myads_smart_vt';
  var vt='';
  try {
    vt = w.localStorage ? (w.localStorage.getItem(key) || '') : '';
  } catch (e) {}
  if (!vt) {
    var cookieMatch = d.cookie.match(new RegExp('(?:^|; )' + key + '=([^;]*)'));
    if (cookieMatch) {
      vt = decodeURIComponent(cookieMatch[1]);
    }
  }
  if (!vt) {
    vt = 'vt-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    try {
      if (w.localStorage) {
        w.localStorage.setItem(key, vt);
      }
    } catch (e) {}
    try {
      d.cookie = key + '=' + encodeURIComponent(vt) + '; path=/; max-age=31536000; SameSite=Lax';
    } catch (e) {}
  }
  var slot = createSlot(current);
  if (!slot) {
    return;
  }
  var metrics = measureBox(slot);
  var width = metrics.width;
  var height = metrics.height;
  var ua = (navigator.userAgent || '').toLowerCase();
  var isTablet = /(ipad|tablet|playbook|silk)|(android(?!.*mobile))/i.test(ua);
  var isMobile = !isTablet && /(iphone|ipod|android|mobile)/i.test(ua);
  var deviceType = isTablet ? 'tablet' : (isMobile ? 'mobile' : 'desktop');
  function readMeta(name, attr) {
    var node = d.querySelector('meta[' + attr + '="' + name + '"]');
    return node ? (node.getAttribute('content') || '') : '';
  }
  function normalizeText(value) {
    return (value || '').replace(/\\s+/g, ' ').trim();
  }
  var contextParts = [];
  contextParts.push(normalizeText(d.title || ''));
  contextParts.push(normalizeText(readMeta('keywords', 'name')));
  contextParts.push(normalizeText(readMeta('description', 'name')));
  contextParts.push(normalizeText(readMeta('og:title', 'property')));
  contextParts.push(normalizeText(readMeta('og:description', 'property')));
  var heading = d.querySelector('h1, h2, h3');
  if (heading) {
    contextParts.push(normalizeText(heading.textContent || ''));
  }
  var article = d.querySelector('article, main, [role="main"], .content, .post, .entry, body');
  if (article) {
    contextParts.push(normalizeText((article.textContent || '').slice(0, 320)));
  }
  var context = normalizeText(contextParts.filter(Boolean).join(' | ')).slice(0, 500);
  var src='{$bootstrapUrl}?ID=' + encodeURIComponent(publisherId);
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  if (width > 0) {
    src += '&cw=' + encodeURIComponent(width);
  }
  if (height > 0) {
    src += '&ch=' + encodeURIComponent(height);
  }
  if (deviceType) {
    src += '&dv=' + encodeURIComponent(deviceType);
  }
  if (context) {
    src += '&ctx=' + encodeURIComponent(context);
  }
  src += '&slot=' + encodeURIComponent(slot.id);
  appendLoader(slot, src);
  }
  if (d.body) {
    boot();
    return;
  }
  d.addEventListener('DOMContentLoaded', boot, false);
})(window,document);
JS;
    }

    // Helper for dimensions
    private function getWidth($px)
    {
        return BannerSizeCatalog::width($px);
    }

    private function getHeight($px)
    {
        return BannerSizeCatalog::height($px);
    }

    private function normalizeText($text)
    {
        if (!is_string($text) || $text === '') {
            return $text;
        }
        $encoding = mb_detect_encoding($text, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($text, 'UTF-8', $encoding);
        }
        return $text;
    }

    private function resolveBannerPlacement(Request $request): array
    {
        $placement = strtolower(trim((string) $request->query('px', '')));

        if ($placement === 'responsive2') {
            return [
                'placement' => 'responsive2',
                'size' => $this->resolveResponsive2BannerSize($request),
            ];
        }

        return [
            'placement' => 'fixed',
            'size' => BannerSizeCatalog::normalize($placement) ?? BannerSizeCatalog::default(),
        ];
    }

    private function resolveResponsive2BannerSize(Request $request): string
    {
        $width = $this->sanitizePositiveInt($request->query('cw'));
        $height = $this->sanitizePositiveInt($request->query('ch'));

        if ($width === null) {
            return '300x250';
        }

        if ($width >= 728) {
            return '728x90';
        }

        if ($width >= 468) {
            return '468x60';
        }

        if ($width >= 300) {
            return '300x250';
        }

        if ($width >= 160) {
            return $height !== null && $height >= 600 ? '160x600' : '300x250';
        }

        return '300x250';
    }

    private function sanitizePositiveInt($value): ?int
    {
        $normalized = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        return $normalized === false ? null : $normalized;
    }

    private function normalizeLinkPlacement(null|string|int $value): string
    {
        $normalized = strtolower(trim((string) ($value ?? '')));

        return match ($normalized) {
            '2', 'responsive', '510x320' => 'responsive',
            'responsive2' => 'responsive2',
            default => 'fixed',
        };
    }

    private function resolveResponsive2Layout(Request $request): string
    {
        $width = filter_var(
            $request->query('cw'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($width === false || $width === null) {
            return 'stacked';
        }

        if ($width >= 640) {
            return 'wide';
        }

        if ($width >= 360) {
            return 'stacked';
        }

        return 'compact';
    }

    private function resolveSmartSlot(Request $request): array
    {
        $width = $this->sanitizePositiveInt($request->query('cw'));
        $height = $this->sanitizePositiveInt($request->query('ch'));
        $bannerSize = null;

        if ($width !== null && $width >= 728 && ($height === null || $height >= 90)) {
            $bannerSize = '728x90';
        } elseif ($width !== null && $width >= 468 && ($height === null || $height >= 60)) {
            $bannerSize = '468x60';
        } elseif ($width !== null && $width >= 300 && ($height === null || $height >= 250)) {
            $bannerSize = '300x250';
        } elseif ($width !== null && $width >= 160 && $height !== null && $height >= 600) {
            $bannerSize = '160x600';
        }

        return [
            'width' => $width,
            'height' => $height,
            'banner_size' => $bannerSize,
        ];
    }

    private function resolveSmartDeviceType(Request $request): string
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

        if ($isMobile) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function scoreSmartAd(SmartAd $smartAd, array $contextTokens): int
    {
        if ($contextTokens === []) {
            return 0;
        }

        return count(array_intersect($smartAd->topicTokens(), $contextTokens));
    }
}
