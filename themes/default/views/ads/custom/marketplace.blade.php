@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #0ea5e9 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-ads-marketplace">
    <p class="section-banner-title">{{ __('messages.custom_ads_marketplace') }}</p>
    <p class="section-banner-text">{{ __('messages.custom_ads_marketplace_intro') }}</p>
</div>

<div class="custom-ads-toolbar">
    <a class="custom-ads-pill" href="{{ route('ads.custom.index') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.custom_ads') }}</a>
    <a href="{{ route('ads.custom.placements.create') }}" class="button secondary"><i class="fa fa-plus"></i>&nbsp;{{ __('messages.custom_ads_publish_space') }}</a>
</div>

<div class="custom-ads-grid">
    @forelse($placements as $placement)
        <div class="custom-ads-card">
            <div class="custom-ads-pills" style="margin-bottom: 10px;">
                <span class="custom-ads-pill">{{ __('messages.custom_ads_format_' . $placement->format) }}</span>
                <span class="custom-ads-pill">{{ $placement->size }}</span>
            </div>
            <h4>{{ $placement->name }}</h4>
            <p class="custom-ads-muted">{{ \Illuminate\Support\Str::limit($placement->description ?: $placement->site_url, 140) }}</p>
            <div class="custom-ads-muted">{{ __('messages.publisher') }}: {{ $placement->user?->username }}</div>
            <div class="custom-ads-pills" style="margin: 14px 0;">
                <span class="custom-ads-pill"><i class="fa fa-eye"></i>{{ $placement->impressions }}</span>
                <span class="custom-ads-pill"><i class="fa fa-handshake"></i>{{ $placement->active_deals_count }} {{ __('messages.active') }}</span>
            </div>
            <a href="{{ route('ads.custom.placements.request', $placement) }}" class="button secondary" style="width: 100%;">{{ __('messages.custom_ads_request_deal') }}</a>
        </div>
    @empty
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_marketplace_empty_title') }}</h4>
            <p class="custom-ads-muted">{{ __('messages.custom_ads_marketplace_empty_desc') }}</p>
        </div>
    @endforelse
</div>

<div style="margin-top: 22px;">
    {{ $placements->links() }}
</div>
@endsection
