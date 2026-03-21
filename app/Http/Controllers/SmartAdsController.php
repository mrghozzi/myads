<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Services\SmartAdAnalyzer;
use App\Support\SmartAdEmbedCode;
use App\Support\SmartAdTargeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartAdsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $smartAds = SmartAd::where('uid', $user->id)->orderBy('id', 'desc')->get();
        $site_settings = Setting::first();

        return view('theme::ads.smart.index', compact('smartAds', 'user', 'site_settings'));
    }

    public function create()
    {
        $site_settings = Setting::first();

        return view('theme::ads.smart.create', [
            'smartAd' => new SmartAd(),
            'site_settings' => $site_settings,
            'deviceOptions' => $this->deviceOptions(),
            'targetCountries' => '',
            'selectedDevices' => [],
        ]);
    }

    public function store(Request $request, SmartAdAnalyzer $analyzer)
    {
        $user = Auth::user();
        $payload = $this->validatedPayload($request);
        ['analysis' => $analysis, 'warning' => $warning] = $this->analyzeDestination($payload['landing_url'], $analyzer);

        SmartAd::create($this->buildSmartAdAttributes($user->id, $payload, $analysis));

        $redirect = redirect()->route('ads.smart.index')
            ->with('success', __('messages.smart_ad_created'));

        if ($warning !== null) {
            $redirect->with('warning', $warning);
        }

        return $redirect;
    }

    public function edit(int $id)
    {
        $user = Auth::user();
        $smartAd = SmartAd::where('uid', $user->id)->findOrFail($id);
        $site_settings = Setting::first();

        return view('theme::ads.smart.edit', [
            'smartAd' => $smartAd,
            'site_settings' => $site_settings,
            'deviceOptions' => $this->deviceOptions(),
            'targetCountries' => implode(', ', $smartAd->targetCountries()),
            'selectedDevices' => $smartAd->targetDevices(),
        ]);
    }

    public function update(Request $request, int $id, SmartAdAnalyzer $analyzer)
    {
        $user = Auth::user();
        $smartAd = SmartAd::where('uid', $user->id)->findOrFail($id);
        $payload = $this->validatedPayload($request);
        ['analysis' => $analysis, 'warning' => $warning] = $this->analyzeDestination($payload['landing_url'], $analyzer);

        $smartAd->update($this->buildSmartAdAttributes($user->id, $payload, $analysis, $smartAd));

        $redirect = redirect()->route('ads.smart.index')
            ->with('success', __('messages.smart_ad_updated'));

        if ($warning !== null) {
            $redirect->with('warning', $warning);
        }

        return $redirect;
    }

    public function destroy(int $id)
    {
        $user = Auth::user();
        $smartAd = SmartAd::where('uid', $user->id)->findOrFail($id);
        $smartAd->delete();

        return redirect()->route('ads.smart.index')->with('success', __('messages.smart_ad_deleted'));
    }

    public function code()
    {
        $user = Auth::user();
        $extensions_code = Option::where('o_type', 'extensions_code')->value('o_valuer');
        $embedCode = SmartAdEmbedCode::build(route('ads.smart.script'), $user->id, $extensions_code ?? '');
        $previewSmartAd = SmartAd::where('uid', $user->id)
            ->where('statu', 1)
            ->latest('id')
            ->first();
        $previewMarkup = $previewSmartAd
            ? $this->renderPreviewMarkup($previewSmartAd, $user->id)
            : null;

        return view('theme::ads.smart.code', compact('user', 'embedCode', 'previewMarkup', 'previewSmartAd', 'extensions_code'));
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'landing_url' => 'required|url|max:2048',
            'headline_override' => 'nullable|string|max:255',
            'description_override' => 'nullable|string|max:600',
            'image' => 'nullable|string|max:2048',
            'countries' => 'nullable|string|max:1000',
            'manual_keywords' => 'nullable|string|max:1000',
            'devices' => 'nullable|array',
            'devices.*' => 'in:desktop,mobile,tablet',
        ]);

        $validated['devices'] = SmartAdTargeting::normalizeDeviceTypes($validated['devices'] ?? []);
        $validated['countries'] = SmartAdTargeting::normalizeCountryCodes($validated['countries'] ?? '');

        return $validated;
    }

    private function analyzeDestination(string $landingUrl, SmartAdAnalyzer $analyzer): array
    {
        try {
            return [
                'analysis' => $analyzer->analyze($landingUrl),
                'warning' => null,
            ];
        } catch (\Throwable) {
            return [
                'analysis' => [
                    'source_title' => parse_url($landingUrl, PHP_URL_HOST) ?: __('messages.smart_ad'),
                    'source_description' => '',
                    'source_image' => null,
                    'extracted_keywords' => '',
                ],
                'warning' => __('messages.smart_ad_analysis_warning'),
            ];
        }
    }

    private function buildSmartAdAttributes(int $userId, array $payload, array $analysis, ?SmartAd $existing = null): array
    {
        return [
            'uid' => $userId,
            'landing_url' => $payload['landing_url'],
            'headline_override' => ($payload['headline_override'] ?? null) ?: null,
            'description_override' => ($payload['description_override'] ?? null) ?: null,
            'image' => ($payload['image'] ?? null) ?: null,
            'countries' => SmartAdTargeting::encodeList($payload['countries']),
            'devices' => SmartAdTargeting::encodeList($payload['devices']),
            'manual_keywords' => trim((string) ($payload['manual_keywords'] ?? '')) ?: null,
            'extracted_keywords' => trim((string) ($analysis['extracted_keywords'] ?? '')) ?: null,
            'source_title' => trim((string) ($analysis['source_title'] ?? '')) ?: ($existing?->source_title ?: null),
            'source_description' => trim((string) ($analysis['source_description'] ?? '')) ?: ($existing?->source_description ?: null),
            'source_image' => trim((string) ($analysis['source_image'] ?? '')) ?: ($existing?->source_image ?: null),
            'statu' => $existing?->statu ?? 1,
        ];
    }

    private function deviceOptions(): array
    {
        return [
            'desktop' => __('messages.smart_device_desktop'),
            'mobile' => __('messages.smart_device_mobile'),
            'tablet' => __('messages.smart_device_tablet'),
        ];
    }

    private function renderPreviewMarkup(SmartAd $smartAd, int $publisherId): string
    {
        $placement = $smartAd->displayImage() !== null ? 'banner' : 'native';
        $viewName = $placement === 'banner'
            ? 'theme::ads.serving.smart_banner'
            : 'theme::ads.serving.smart_native';

        return view($viewName, [
            'smartAd' => $smartAd,
            'publisherId' => $publisherId,
            'bannerSize' => $placement === 'banner' ? '728x90' : null,
            'clickUrl' => route('ads.redirect', ['ads' => $smartAd->id, 'vu' => $publisherId, 'type' => 'smart']),
            'refUrl' => url('/') . '?ref=' . $publisherId,
            'reportUrl' => url('/report') . '?smart_ad=' . $smartAd->id,
        ])->render();
    }
}
