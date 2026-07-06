@extends('theme::layouts.master')
@include('theme::directory._assets')

@section('content')
<div class="directory-rdx directory-hub-shell">
    <div class="section-banner directory-hub-banner">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="directory-icon">
        <div class="directory-hub-banner-copy">
            <p class="section-banner-title">{{ __('messages.seo_checker') }}</p>
            <p class="section-banner-text">{{ __('messages.seo_analysis_complete', ['url' => $results['url']]) }}</p>
        </div>
    </div>

    <div class="grid grid-3-6-3 mobile-prefer-content directory-hub-grid">
        <div class="grid-column">
            <div class="widget-box directory-side-card directory-command-card">
                <p class="widget-box-title">{{ __('Navigation') }}</p>
                <div class="widget-box-content">
                    <div class="directory-command-list">
                        <a href="{{ route('seo_checker.index') }}" class="button primary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;{{ __('messages.seo_new_check') }}
                        </a>
                        <a href="{{ route('directory.index') }}" class="button secondary">
                            <i class="fa fa-home" aria-hidden="true"></i>&nbsp;{{ __('messages.directory') }}
                        </a>
                    </div>
                </div>
            </div>
            <x-widget-column side="directory_left" />
        </div>

        <div class="grid-column">
            <div class="widget-box directory-feed-shell">
                <div class="widget-box-content" style="padding: 20px;">
                    @include('theme::seo_checker._results_content', ['results' => $results, 'settings' => $settings, 'userRole' => $userRole])
                </div>
            </div>
        </div>

        <div class="grid-column">
            <x-widget-column side="directory_right" />
        </div>
    </div>
</div>
@endsection
