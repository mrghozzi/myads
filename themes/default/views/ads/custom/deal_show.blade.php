@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

@php
    $viewer = auth()->user();
    $canAccept = $deal->canBeAcceptedBy($viewer);
    $isPublisher = (int) $deal->publisher_id === (int) $viewer->id;
@endphp

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-deal">
    <p class="section-banner-title">{{ __('messages.custom_ads_deal') }} #{{ $deal->id }}</p>
    <p class="section-banner-text">{{ $deal->placement?->name }}</p>
</div>

<div class="custom-ads-toolbar">
    <a class="custom-ads-pill" href="{{ route('ads.custom.index') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.custom_ads') }}</a>
    <div class="custom-ads-actions">
        @if($canAccept)
            <form method="POST" action="{{ route('ads.custom.deals.accept', $deal) }}">@csrf<button class="button secondary" type="submit">{{ __('messages.accept') }}</button></form>
            <form method="POST" action="{{ route('ads.custom.deals.reject', $deal) }}">@csrf<button class="button tertiary" type="submit">{{ __('messages.reject') }}</button></form>
            @if($deal->status === \App\Models\CustomAdDeal::STATUS_INVITED && (int) $deal->advertiser_id === (int) $viewer->id)
                <a href="{{ route('ads.custom.deals.edit', $deal) }}" class="button secondary">{{ __('messages.edit') }}</a>
            @endif
        @endif
        @if($isPublisher && $deal->status === \App\Models\CustomAdDeal::STATUS_ACTIVE)
            <form method="POST" action="{{ route('ads.custom.deals.pause', $deal) }}">@csrf<button class="button tertiary" type="submit">{{ __('messages.pause') }}</button></form>
        @endif
        @if($isPublisher && $deal->status === \App\Models\CustomAdDeal::STATUS_PAUSED)
            <form method="POST" action="{{ route('ads.custom.deals.resume', $deal) }}">@csrf<button class="button secondary" type="submit">{{ __('messages.resume') }}</button></form>
        @endif
        @if(!in_array($deal->status, [\App\Models\CustomAdDeal::STATUS_CANCELLED, \App\Models\CustomAdDeal::STATUS_COMPLETED, \App\Models\CustomAdDeal::STATUS_REJECTED], true))
            <form method="POST" action="{{ route('ads.custom.deals.cancel', $deal) }}">@csrf<button class="button tertiary" type="submit">{{ __('messages.cancel') }}</button></form>
        @endif
    </div>
</div>

<div class="custom-ads-shell">
    <div class="custom-ads-grid">
        <div class="custom-ads-card">
            <h4>{{ __('messages.status') }}</h4>
            <span class="custom-ads-status {{ $deal->status }}">{{ $deal->status }}</span>
        </div>
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
        <p class="widget-box-title">{{ __('messages.custom_ads_deal_terms') }}</p>
        <div class="custom-ads-grid">
            <div>
                <div class="custom-ads-muted">{{ __('messages.publisher') }}</div>
                <strong>{{ $deal->publisher?->username }}</strong>
            </div>
            <div>
                <div class="custom-ads-muted">{{ __('messages.custom_ads_advertiser') }}</div>
                <strong>{{ $deal->advertiser?->username }}</strong>
            </div>
            <div>
                <div class="custom-ads-muted">{{ __('messages.custom_ads_period') }}</div>
                <strong>{{ optional($deal->starts_at)->format('Y-m-d') }} → {{ optional($deal->ends_at)->format('Y-m-d') }}</strong>
            </div>
            <div>
                <div class="custom-ads-muted">{{ __('messages.custom_ads_payment') }}</div>
                @if($deal->payment_type === \App\Models\CustomAdDeal::PAYMENT_PTS_DAILY)
                    <strong>{{ number_format((float) $deal->daily_pts, 2) }} PTS/{{ __('messages.day') }}</strong>
                    <div class="custom-ads-muted">{{ __('messages.custom_ads_reserved') }} {{ number_format((float) $deal->reserved_pts, 2) }} · {{ __('messages.custom_ads_paid') }} {{ number_format((float) $deal->paid_pts, 2) }} · {{ __('messages.custom_ads_remaining') }} {{ number_format((float) $deal->remainingReservedPts(), 2) }}</div>
                @else
                    <strong>{{ __('messages.custom_ads_external') }}</strong>
                    <div class="custom-ads-muted">{{ $deal->external_amount ? number_format((float) $deal->external_amount, 2) . ' ' . $deal->external_currency : $deal->external_note }}</div>
                @endif
            </div>
        </div>
        @if($deal->terms)
            <p style="margin-top: 16px;">{{ $deal->terms }}</p>
        @endif
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_creative') }}</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            @if($deal->creative)
                <div class="custom-ads-preview">
                    <div class="custom-ads-pills" style="margin-bottom: 10px;">
                        <span class="custom-ads-pill">{{ $deal->creative->status }}</span>
                        <span class="custom-ads-pill">{{ __('messages.custom_ads_format_' . ($deal->creative->format ?: 'banner')) }}</span>
                    </div>
                    <h4>{{ $deal->creative->headline }}</h4>
                    <p class="custom-ads-muted">{{ $deal->creative->body }}</p>
                    <a href="{{ $deal->creative->target_url }}" target="_blank" rel="noopener noreferrer">{{ $deal->creative->target_url }}</a>
                </div>
                <div>
                    <p class="custom-ads-muted" style="margin-bottom: 8px; font-weight: 700;">{{ __('messages.custom_ads_live_preview') }}</p>
                    <div style="border: 1px solid #edf0f7; border-radius: 8px; padding: 10px; background: #fafafa; display: flex; justify-content: center; align-items: center; min-height: 120px;">
                        {!! app(\App\Services\CustomAds\CustomAdServingService::class)->renderMarkup($deal->placement, $deal->creative) !!}
                    </div>
                </div>
            @else
                <div class="custom-ads-preview" style="grid-column: 1 / -1; display: flex; justify-content: center; align-items: center; min-height: 120px; border: 1px dashed #edf0f7; border-radius: 8px;">
                    <span class="custom-ads-muted">{{ __('messages.custom_ads_no_creative') ?? 'No creative details available for this deal.' }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_analytics') }}</p>
        <div class="custom-ads-grid">
            <div class="custom-ads-card">
                <h4>{{ __('messages.custom_ads_hourly_clicks') }}</h4>
                @include('theme::partials.ads.mini_heatmap', ['heatmap' => $heatmap])
            </div>
            <div class="custom-ads-card">
                <h4>{{ __('messages.custom_ads_devices') }}</h4>
                @forelse($devices as $device => $count)
                    <div class="custom-ads-muted">{{ $device }}: {{ $count }}</div>
                @empty
                    <div class="custom-ads-muted">{{ __('messages.no_data') }}</div>
                @endforelse
            </div>
            <div class="custom-ads-card">
                <h4>{{ __('messages.custom_ads_countries') }}</h4>
                @forelse($countries as $country => $count)
                    <div class="custom-ads-muted">{{ $country }}: {{ $count }}</div>
                @empty
                    <div class="custom-ads-muted">{{ __('messages.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="widget-box">
        <p class="widget-box-title">{{ __('messages.custom_ads_payouts') }}</p>
        @if($deal->payouts->count())
            <table class="custom-ads-table">
                <thead><tr><th>{{ __('messages.type') }}</th><th>{{ __('messages.amount') }}</th><th>{{ __('messages.date') }}</th></tr></thead>
                <tbody>
                    @foreach($deal->payouts as $payout)
                        <tr>
                            <td>{{ $payout->type }}</td>
                            <td>{{ number_format((float) $payout->amount, 2) }} PTS</td>
                            <td>{{ optional($payout->payout_date)->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="custom-ads-muted">{{ __('messages.custom_ads_no_payouts') }}</p>
        @endif
    </div>
</div>
@endsection
