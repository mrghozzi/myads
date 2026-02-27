@extends('theme::layouts.master')

@section('title', __('messages.terms_conditions') . ' - ' . ($site_settings->titer ?? 'MyAds'))

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
    background: linear-gradient(135deg, #fff7ed, #fef3c7);
    border-radius: 12px;
    border-left: 4px solid #f59e0b;
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
    border-bottom: 2px solid #fef3c7;
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
    border-left-color: #f59e0b;
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
            <h1>{{ __('messages.terms_conditions') }}</h1>
            <p class="legal-updated">{{ __('messages.terms_last_updated') }}: {{ date('Y-m-d') }}</p>

            <div class="legal-intro">
                {{ __('messages.terms_intro') }}
            </div>

            <div class="legal-section">
                <h2>1. {{ __('messages.terms_account') }}</h2>
                <p>{{ __('messages.terms_account_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>2. {{ __('messages.terms_conduct') }}</h2>
                <p>{{ __('messages.terms_conduct_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>3. {{ __('messages.terms_content') }}</h2>
                <p>{{ __('messages.terms_content_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>4. {{ __('messages.terms_points') }}</h2>
                <p>{{ __('messages.terms_points_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>5. {{ __('messages.terms_termination') }}</h2>
                <p>{{ __('messages.terms_termination_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>6. {{ __('messages.terms_liability') }}</h2>
                <p>{{ __('messages.terms_liability_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>7. {{ __('messages.terms_changes') }}</h2>
                <p>{{ __('messages.terms_changes_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>8. {{ __('messages.terms_contact') }}</h2>
                <p>{{ __('messages.terms_contact_desc') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
