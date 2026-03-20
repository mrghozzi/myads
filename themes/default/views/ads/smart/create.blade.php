@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: linear-gradient(135deg, rgba(15,23,42,.96) 0%, rgba(29,78,216,.94) 56%, rgba(14,165,233,.88) 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.smart_create_title') }}</p>
    <p class="section-banner-text">{{ __('messages.smart_create_desc') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content" style="display: grid; gap: 12px;">
                <a href="{{ route('ads.smart.index') }}" class="button primary">{{ __('messages.back') }}</a>
                <div style="padding: 16px; border-radius: 18px; background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);">
                    <p style="margin: 0 0 8px; font-weight: 700; color: #1f2937;">{{ __('messages.smart_ads_credits') }}</p>
                    <p style="margin: 0; font-size: 1.4rem; font-weight: 800; color: #1d4ed8;">{{ number_format((float) auth()->user()->nsmart, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        @include('theme::ads.smart._form', [
            'smartAd' => $smartAd,
            'formAction' => route('ads.smart.store'),
            'formMethod' => 'POST',
            'submitLabel' => __('messages.smart_create_ad'),
            'targetCountries' => $targetCountries,
            'selectedDevices' => $selectedDevices,
            'deviceOptions' => $deviceOptions,
        ])
    </div>
</div>
@endsection
