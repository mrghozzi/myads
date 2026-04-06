@extends('theme::layouts.master')

@push('head')
<style>
    .share-page {
        --share-card-bg: #615dfa;
        --share-text: #ffffff;
    }

    [data-theme="css_d"] .share-page {
        --share-card-bg: #4ff461;
        --share-text: #1d2333;
    }

    .share-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .share-title {
        font-family: Rajdhani, sans-serif;
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .share-subtitle {
        color: #8f91ac;
        font-size: 1rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="share-icon">
    <p class="section-banner-title">{{ __('messages.share') ?? 'Share' }}</p>
    <p class="section-banner-text">{{ __('messages.share_with_community') ?? 'Spread content across the network' }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content news-page">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <!-- MAIN COLUMN -->
    <div class="grid-column">
        <div class="share-header">
            <h1 class="share-title">{{ __('messages.share_to_community') ?? 'Share to Community' }}</h1>
            <p class="share-subtitle">{{ __('messages.share_page_hint') ?? 'Your post will be visible to everyone in the community feed.' }}</p>
        </div>

        @include('theme::partials.status.add_post')

        <div class="widget-box" style="margin-top: 24px;">
            <div class="widget-box-content">
                <p class="text-center" style="font-size: 0.9rem; color: #8f91ac;">
                    <i class="fa fa-info-circle" style="margin-inline-end: 8px;"></i>
                    {{ __('messages.share_redirect_hint') ?? 'After sharing, you will be redirected to your new post.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
