@extends('theme::layouts.master')

@section('title', __('messages.privacy_policy') . ' - ' . ($site_settings->titer ?? 'MyAds'))

@push('head')
<style>
.legal-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 0;
}
.legal-page h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #1a1a2e;
}
.legal-page .legal-updated {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 30px;
}
.legal-page .legal-intro {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #4b5563;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #f0f0ff, #f5f3ff);
    border-radius: 12px;
    border-left: 4px solid #615dfa;
}
.legal-section {
    margin-bottom: 28px;
}
.legal-section h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f0f0ff;
}
.legal-section p {
    font-size: 0.95rem;
    line-height: 1.7;
    color: #4b5563;
}
/* Dark Mode */
[data-theme="css_d"] .legal-page h1,
[data-theme="css_d"] .legal-section h2 {
    color: #e2e8f0;
}
[data-theme="css_d"] .legal-page .legal-intro {
    background: linear-gradient(135deg, #1e293b, #1a1a2e);
    color: #94a3b8;
    border-left-color: #8b5cf6;
}
[data-theme="css_d"] .legal-section p {
    color: #94a3b8;
}
[data-theme="css_d"] .legal-page .legal-updated {
    color: #64748b;
}
[data-theme="css_d"] .legal-section h2 {
    border-bottom-color: #334155;
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="widget-box">
        <div class="legal-page">
            <h1>{{ __('messages.privacy_policy') }}</h1>
            <p class="legal-updated">{{ __('messages.privacy_last_updated') }}: {{ date('Y-m-d') }}</p>

            <div class="legal-intro">
                {{ __('messages.privacy_intro') }}
            </div>

            <div class="legal-section">
                <h2>1. {{ __('messages.privacy_info_collect') }}</h2>
                <p>{{ __('messages.privacy_info_collect_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>2. {{ __('messages.privacy_how_use') }}</h2>
                <p>{{ __('messages.privacy_how_use_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>3. {{ __('messages.privacy_cookies') }}</h2>
                <p>{{ __('messages.privacy_cookies_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>4. {{ __('messages.privacy_data_sharing') }}</h2>
                <p>{{ __('messages.privacy_data_sharing_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>5. {{ __('messages.privacy_security') }}</h2>
                <p>{{ __('messages.privacy_security_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>6. {{ __('messages.privacy_rights') }}</h2>
                <p>{{ __('messages.privacy_rights_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>7. {{ __('messages.privacy_changes') }}</h2>
                <p>{{ __('messages.privacy_changes_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>8. {{ __('messages.privacy_contact') }}</h2>
                <p>{{ __('messages.privacy_contact_desc') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
