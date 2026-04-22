@extends('theme::layouts.master')

@push('head')
<style>
    .docs-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .docs-card {
        background-color: var(--widget-box-bg);
        border: 1px solid var(--widget-box-border);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 24px;
        color: var(--text-color);
    }
    .docs-section-title {
        font-family: Rajdhani, sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .docs-section-title i {
        color: #615dfa;
    }
    .code-block {
        background-color: #1d2333;
        color: #fff;
        padding: 16px;
        border-radius: 8px;
        position: relative;
        overflow-x: auto;
        margin-bottom: 20px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
    }
    .code-block code {
        display: block;
        white-space: pre;
    }
    .param-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    .param-table th, .param-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--widget-box-border);
    }
    .param-table th {
        font-weight: 700;
        color: #8f91ac;
    }
    .badge-api {
        background: #615dfa;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    [data-theme="css_d"] .docs-card {
        background-color: #1d2333;
        border-color: #2f3749;
    }
    [data-theme="css_d"] .code-block {
        background-color: #161b28;
    }
</style>
@endpush

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="docs-icon">
    <p class="section-banner-title">{{ __('messages.developer_docs') }}</p>
    <p class="section-banner-text">{{ __('messages.developer_page_description', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
</div>

<div class="docs-container news-page">
    <div class="docs-card">
        <h2 class="docs-section-title">
            <i class="fa fa-plug"></i>
            {{ __('messages.share_api') }}
        </h2>
        <p style="margin-bottom: 16px;">
            {{ __('messages.share_api_intro', ['site' => $site_settings->titer ?? 'MYADS']) ?? 'The External Share API allows you to integrate a "Share on '.($site_settings->titer ?? 'MYADS').'" button on your website. When users click this button, they will be redirected to '.($site_settings->titer ?? 'MYADS').' with a post composer pre-filled with your content.' }}
        </p>
        
        <div class="docs-item">
            <h4 style="font-weight: 700; margin-bottom: 10px;">{{ __('messages.api_endpoint') }}</h4>
            <div class="code-block">
                <code>{{ url('/') }}/share</code>
            </div>
        </div>

        <div class="docs-item">
            <h4 style="font-weight: 700; margin-bottom: 10px;">{{ __('messages.parameters') }}</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.type') }}</th>
                        <th>{{ __('messages.description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>text</code></td>
                        <td><span class="badge-api">String</span></td>
                        <td>{{ __('messages.param_text_desc') ?? 'The text content to be pre-filled in the post composer. Can include links, hashtags, and mentions.' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="docs-card">
        <h2 class="docs-section-title">
            <i class="fa fa-code"></i>
            {{ __('messages.usage_examples') }}
        </h2>

        <div class="docs-item">
            <h4 style="font-weight: 700; margin-bottom: 10px;">HTML Link</h4>
            <p style="margin-bottom: 10px;">{{ __('messages.example_html_desc') ?? 'The simplest way to integrate sharing is a standard anchor tag.' }}</p>
            <div class="code-block">
                <code>&lt;a href="{{ url('/') }}/share?text=Check out this site! {{ url('/') }}" target="_blank"&gt;
    Share on {{ $site_settings->titer ?? 'MYADS' }}
&lt;/a&gt;</code>
            </div>
        </div>

        <div class="docs-item">
            <h4 style="font-weight: 700; margin-bottom: 10px;">JavaScript Button</h4>
            <p style="margin-bottom: 10px;">{{ __('messages.example_js_desc') ?? 'You can also use JavaScript to dynamically generate the share URL.' }}</p>
            <div class="code-block">
                <code>function shareOnMyAds(text) {
    const baseUrl = "{{ url('/') }}/share";
    const shareUrl = `${baseUrl}?text=${encodeURIComponent(text)}`;
    window.open(shareUrl, '_blank');
}

// Usage
shareOnMyAds("I love using {{ $site_settings->titer ?? 'MYADS' }}! #Social #AdExchange");</code>
            </div>
        </div>
    </div>

    <div class="docs-card">
        <h2 class="docs-section-title">
            <i class="fa fa-lightbulb"></i>
            {{ __('messages.best_practices') }}
        </h2>
        <ul style="list-style: disc; margin-inline-start: 20px; line-height: 1.6;">
            <li><strong>{{ __('messages.use_hashtags') ?? 'Use Hashtags' }}:</strong> {{ __('messages.best_practice_hashtags') ?? 'Include hashtags to make your content discoverable in the community feed.' }}</li>
            <li><strong>{{ __('messages.include_links') ?? 'Include Links' }}:</strong> {{ __('messages.best_practice_links') ?? 'The system will automatically generate a link preview if a valid URL is included.' }}</li>
            <li><strong>{{ __('messages.encoding') ?? 'URL Encoding' }}:</strong> {{ __('messages.best_practice_encoding') ?? 'Always URL-encode the text parameter to ensure special characters and spaces are handled correctly.' }}</li>
        </ul>
    </div>
</div>
@endsection
