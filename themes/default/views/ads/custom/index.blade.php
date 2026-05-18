@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 55%, #38bdf8 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-ads">
    <p class="section-banner-title">{{ __('messages.custom_ads') }}</p>
    <p class="section-banner-text">{{ __('messages.custom_ads_dashboard_intro') }}</p>
</div>

<div class="custom-ads-toolbar">
    <div class="custom-ads-pills">
        <a class="custom-ads-pill" href="{{ route('ads.index') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.advertising') }}</a>
        <a class="custom-ads-pill" href="{{ route('ads.custom.marketplace') }}"><i class="fa fa-store"></i>{{ __('messages.custom_ads_marketplace') }}</a>
    </div>
    <div class="custom-ads-actions">
        <a href="{{ route('ads.custom.placements.create') }}" class="button secondary"><i class="fa fa-plus"></i>&nbsp;{{ __('messages.custom_ads_new_placement') }}</a>
    </div>
</div>

<div class="custom-ads-shell">
    <div class="custom-ads-grid">
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_placements') }}</h4>
            <div class="custom-ads-stat">{{ $placements->count() }}</div>
            <div class="custom-ads-muted">{{ __('messages.custom_ads_placements_help') }}</div>
        </div>
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_publisher_deals') }}</h4>
            <div class="custom-ads-stat">{{ $publisherDeals->count() }}</div>
            <div class="custom-ads-muted">{{ __('messages.custom_ads_publisher_deals_help') }}</div>
        </div>
        <div class="custom-ads-card">
            <h4>{{ __('messages.custom_ads_advertiser_deals') }}</h4>
            <div class="custom-ads-stat">{{ $advertiserDeals->count() }}</div>
            <div class="custom-ads-muted">{{ __('messages.custom_ads_advertiser_deals_help') }}</div>
        </div>
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_placements') }}</p>
        @if($placements->count() > 0)
            <table class="custom-ads-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.type') }}</th>
                        <th>{{ __('messages.stats') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($placements as $placement)
                        <tr>
                            <td>
                                <strong>{{ $placement->name }}</strong>
                                <div class="custom-ads-muted">{{ $placement->site_url ?: $placement->placement_key }}</div>
                            </td>
                            <td>
                                <span class="custom-ads-pill">{{ __('messages.custom_ads_format_' . $placement->format) }}</span>
                                <span class="custom-ads-pill">{{ $placement->size }}</span>
                            </td>
                            <td>
                                <span class="custom-ads-pill"><i class="fa fa-eye"></i>{{ $placement->summary['impressions'] ?? $placement->impressions }}</span>
                                <span class="custom-ads-pill"><i class="fa fa-mouse-pointer"></i>{{ $placement->summary['clicks'] ?? $placement->clicks }}</span>
                                <span class="custom-ads-pill">CTR {{ $placement->summary['ctr'] ?? $placement->ctr() }}%</span>
                            </td>
                            <td><span class="custom-ads-status {{ $placement->status }}">{{ $placement->status }}</span></td>
                            <td>
                                <div class="custom-ads-actions">
                                    <a class="button tertiary" href="{{ route('ads.custom.placements.code', $placement) }}"><i class="fa fa-code"></i></a>
                                    <a class="button tertiary" href="{{ route('ads.custom.placements.edit', $placement) }}"><i class="fa fa-edit"></i></a>
                                    <a class="button secondary" href="{{ route('ads.custom.placements.invite', $placement) }}">{{ __('messages.custom_ads_invite') }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="custom-ads-card">
                <h4>{{ __('messages.custom_ads_empty_placements_title') }}</h4>
                <p class="custom-ads-muted">{{ __('messages.custom_ads_empty_placements_desc') }}</p>
                <a href="{{ route('ads.custom.placements.create') }}" class="button secondary">{{ __('messages.custom_ads_new_placement') }}</a>
            </div>
        @endif
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_publisher_deals') }}</p>
        @include('theme::ads.custom.partials.deals_table', ['deals' => $publisherDeals])
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_advertiser_deals') }}</p>
        @include('theme::ads.custom.partials.deals_table', ['deals' => $advertiserDeals])
    </div>
</div>
@endsection
