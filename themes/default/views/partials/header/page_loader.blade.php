<div class="page-loader">
    <div class="page-loader-decoration">
        <img src="{{ asset('themes/default/assets/img/logo_w.png') }}" width="40" />
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
