@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-code">
    <p class="section-banner-title">{{ __('messages.custom_ads_embed_code') }}</p>
    <p class="section-banner-text">{{ $placement->name }}</p>
</div>

<div class="custom-ads-toolbar">
    <a class="custom-ads-pill" href="{{ route('ads.custom.index') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.custom_ads') }}</a>
    <div class="custom-ads-actions">
        <a class="button tertiary" href="{{ route('ads.custom.placements.edit', $placement) }}">{{ __('messages.edit') }}</a>
        <a class="button secondary" href="{{ route('ads.custom.placements.invite', $placement) }}">{{ __('messages.custom_ads_invite') }}</a>
    </div>
</div>

<div class="custom-ads-shell">
    <div class="custom-ads-grid">
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_impressions') }}</h4>
            <div class="custom-ads-stat">{{ number_format($summary['impressions']) }}</div>
        </div>
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_clicks') }}</h4>
            <div class="custom-ads-stat">{{ number_format($summary['clicks']) }}</div>
        </div>
        <div class="custom-ads-card">
            <h4>CTR</h4>
            <div class="custom-ads-stat">{{ $summary['ctr'] }}%</div>
        </div>
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_embed_code') }}</p>
        <p class="custom-ads-muted">{{ __('messages.custom_ads_embed_code_help') }}</p>
        <textarea class="custom-ads-code" readonly onclick="this.select(); document.execCommand('copy');">{!! $embedCode !!}</textarea>
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_hourly_clicks') }}</p>
        @include('theme::partials.ads.mini_heatmap', ['heatmap' => $heatmap])
    </div>
</div>
@endsection
