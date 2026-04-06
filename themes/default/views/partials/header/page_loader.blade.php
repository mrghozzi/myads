<div class="page-loader">
    <div class="page-loader-decoration">
        <picture>
            <source srcset="{{ theme_asset('img/logo_w.webp') }}" type="image/webp">
            <img src="{{ theme_asset('img/logo_w.png') }}" width="40" height="40" />
        </picture>
    </div>
    <div class="page-loader-info">
        <p class="page-loader-info-title">{{ $site_settings->titer ?? 'MyAds' }}</p>
        <p class="page-loader-info-text">{{ __('messages.loading') }}</p>
    </div>
    <div class="page-loader-indicator loader-bars">
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
    </div>
</div>
