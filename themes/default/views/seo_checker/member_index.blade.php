@extends('theme::layouts.master')
@include('theme::directory._assets')

@section('content')
<div class="directory-rdx directory-hub-shell">
    <div class="section-banner directory-hub-banner">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="directory-icon">
        <div class="directory-hub-banner-copy">
            <p class="section-banner-title">{{ __('messages.seo_checker') }}</p>
            <p class="section-banner-text">{{ __('messages.seo_checker_desc') }}</p>
        </div>
    </div>

    <div class="grid grid-3-6-3 mobile-prefer-content directory-hub-grid">
        <div class="grid-column">
            <div class="widget-box directory-side-card directory-command-card">
                <p class="widget-box-title">{{ __('Navigation') }}</p>

                <div class="widget-box-content">
                    <div class="directory-command-list">
                        <a href="{{ route('directory.index') }}" class="button primary">
                            <i class="fa fa-home" aria-hidden="true"></i>&nbsp;{{ __('messages.directory') }}
                        </a>
                    </div>
                </div>
            </div>
            <x-widget-column side="directory_left" />
        </div>

        <div class="grid-column">
            <div class="widget-box directory-feed-shell">
                <div class="widget-box-content" style="padding: 30px;">
                    <div class="directory-feed-header">
                        <div class="directory-feed-copy" style="width: 100%; text-align: center;">
                            <h2 class="directory-feed-title" style="margin-bottom: 20px;">{{ __('messages.seo_check_website') }}</h2>
                            <form action="{{ route('seo_checker.analyze') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
                                @csrf
                                <div style="margin-bottom: 20px;">
                                    <input type="url" name="url" placeholder="https://example.com" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.1); background: var(--bg-surface); color: var(--text-main); font-size: 1rem; outline: none;">
                                </div>
                                <button type="submit" class="btn btn-primary" style="padding: 15px 40px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, #615dfa, #23d2e2); border: none;">
                                    <i class="fa-solid fa-magnifying-glass"></i> {{ __('messages.seo_analyze_now') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-column">
            <x-widget-column side="directory_right" />
        </div>
    </div>
</div>
@endsection
