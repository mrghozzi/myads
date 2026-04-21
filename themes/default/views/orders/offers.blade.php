@extends('theme::layouts.master')

@section('content')
@include('theme::orders.partials.styles')

<div class="section-banner orders-hero" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="offers-icon">
    <p class="section-banner-title">{{ __('messages.order_provider_dashboard_title') }}</p>
    <p class="section-banner-text">{{ __('messages.order_provider_dashboard_subtitle') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <div class="grid-column">
        <div class="orders-grid">
            @forelse($offers as $offer)
                <article class="orders-card">
                    <div class="orders-card-head">
                        <div>
                            <div class="orders-card-meta" style="margin-bottom: 10px;">
                                <span class="orders-meta-pill">{{ $offer->displayQuote() }}</span>
                                <span class="orders-meta-pill">{{ $offer->displayDelivery() }}</span>
                                <span class="orders-meta-pill">{{ $offer->displayStatus() }}</span>
                            </div>
                            <h3 class="orders-card-title">
                                <a href="{{ route('orders.show', $offer->order) }}">{{ $offer->order->title }}</a>
                            </h3>
                            <p class="orders-card-copy">{{ __('messages.client_info') }}: <a href="{{ route('profile.show', $offer->order->user->username) }}">{{ $offer->order->user->username }}</a></p>
                        </div>
                        <a href="{{ route('orders.show', $offer->order) }}" class="button secondary small">{{ __('messages.view_details') }}</a>
                    </div>
                    <div class="orders-divider"></div>
                    <p class="orders-card-description">{{ \Illuminate\Support\Str::limit($offer->message, 240) }}</p>
                </article>
            @empty
                <div class="orders-empty">
                    <h3 class="orders-card-title">{{ __('messages.order_no_offers_dashboard_title') }}</h3>
                    <p class="orders-muted" style="margin-top: 10px;">{{ __('messages.order_no_offers_dashboard_copy') }}</p>
                </div>
            @endforelse
        </div>

        <div class="pagination-section">
            {{ $offers->links() }}
        </div>
    </div>

    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
