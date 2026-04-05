@php
    $size = $bannerSize ?? '300x250';
    [$width, $height] = array_map('intval', explode('x', $size));
    $image = $smartAd->displayImage();
@endphp
<style>
    .myads-smart-banner,
    .myads-smart-banner * {
        box-sizing: border-box;
    }
    .myads-smart-banner {
        position: relative;
        width: {{ $width }}px;
        max-width: 100%;
        margin: 0 auto;
        display: block;
        height: {{ $height }}px;
        overflow: hidden;
        border-radius: {{ $size === '300x250' ? '16px' : '10px' }};
        background: #0f172a;
        font-family: Arial, 'Segoe UI', sans-serif;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
    }
    .myads-smart-banner__image,
    .myads-smart-banner__click {
        position: absolute;
        inset: 0;
    }
    .myads-smart-banner__image {
        background: linear-gradient(180deg, rgba(15,23,42,.18) 0%, rgba(15,23,42,.52) 100%), url('{{ $image }}') center/cover no-repeat;
    }
    .myads-smart-banner__click {
        z-index: 1;
        display: block;
        text-decoration: none;
    }
    .myads-smart-banner__meta {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 2;
        padding: {{ $size === '160x600' ? '16px 12px 12px' : '16px' }};
        color: #fff;
        background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,.85) 100%);
    }
    .myads-smart-banner__title {
        margin: 0 0 6px;
        font-size: {{ $size === '728x90' ? '16px' : ($size === '468x60' ? '13px' : '18px') }};
        line-height: 1.2;
        font-weight: 700;
        color: #fff;
    }
    .myads-smart-banner__description {
        margin: 0;
        font-size: {{ $size === '160x600' ? '12px' : '13px' }};
        line-height: 1.45;
        color: rgba(255,255,255,.84);
        display: {{ in_array($size, ['468x60', '728x90'], true) ? 'none' : 'block' }};
    }
    .myads-smart-banner__chip {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 3;
        display: inline-flex;
        align-items: stretch;
        border-radius: 0 0 0 12px;
        overflow: hidden;
        background: rgba(255,255,255,.96);
        box-shadow: 0 1px 2px rgba(15,23,42,.16);
    }
    .myads-smart-banner__brand,
    .myads-smart-banner__info {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 22px;
        text-decoration: none;
    }
    .myads-smart-banner__brand {
        padding: 0 10px;
        color: #202124;
        font-size: 11px;
        border-right: 1px solid #dadce0;
        max-width: calc(100% - 22px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .myads-smart-banner__info {
        width: 22px;
        color: #5f6368;
        font-weight: 700;
    }
    @media screen and (max-width: {{ $width }}px) {
        .myads-smart-banner {
            width: 100%;
        }
    }
</style>
<div class="myads-smart-banner" data-placement="smart-banner" data-size="{{ $size }}">
    <a class="myads-smart-banner__click" href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $smartAd->displayTitle() }}"></a>
    <div class="myads-smart-banner__image"></div>
    <div class="myads-smart-banner__chip">
        <a class="myads-smart-banner__brand" href="{{ $refUrl }}" target="_blank" rel="noopener noreferrer">{{ __('messages.ads_by_site', ['site' => $adsBrandName ?? \App\Support\AdsSettings::brandName()]) }}</a>
        <a class="myads-smart-banner__info" href="{{ $reportUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('messages.report') }}">i</a>
    </div>
    <div class="myads-smart-banner__meta">
        <p class="myads-smart-banner__title">{{ \Illuminate\Support\Str::limit($smartAd->displayTitle(), in_array($size, ['468x60', '728x90'], true) ? 42 : 68) }}</p>
        <p class="myads-smart-banner__description">{{ \Illuminate\Support\Str::limit($smartAd->displayDescription(), $size === '160x600' ? 120 : 160) }}</p>
    </div>
</div>
