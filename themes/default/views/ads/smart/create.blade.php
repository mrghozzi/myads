@extends('theme::layouts.master')

@section('content')
<div class="smart-create-shell superdesign-post-container">
    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero">
        <div class="superdesign-hero-header">
            <div class="superdesign-hero-title-wrap">
                <div class="superdesign-hero-icon-badge">
                    <i class="fa fa-magic"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title">
                        {{ __('messages.smart_create_title') }}
                    </h1>
                    <p class="superdesign-hero-subtitle">
                        {{ __('messages.smart_create_desc') }}
                    </p>
                </div>
            </div>
            <div class="superdesign-hero-badges">
                <span class="superdesign-pill-badge">
                    <i class="fa fa-coins"></i>
                    {{ __('messages.smart_ads_credits') }}: {{ number_format((float) auth()->user()->nsmart, 2) }}
                </span>
                <span class="superdesign-pill-badge">
                    <i class="fa fa-bolt"></i>
                    {{ __('messages.smart_ads') ?? 'الإعلانات الذكية' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Grid Architecture -->
    <div class="superdesign-post-grid">
        <!-- Column 1: Sidebar Information & Credits -->
        <div class="superdesign-sidebar-col">
            <!-- Smart Credits Box -->
            <div class="superdesign-sidebar-card">
                <h3 class="superdesign-sidebar-title">
                    <i class="fa fa-wallet"></i>
                    {{ __('messages.smart_ads_credits') }}
                </h3>
                <div style="text-align: center; padding: 12px 0 20px;">
                    <div style="font-size: 32px; font-weight: 800; color: #615dfa; line-height: 1;">
                        {{ number_format((float) auth()->user()->nsmart, 2) }}
                    </div>
                    <span style="font-size: 13px; color: #64748b; font-weight: 600; display: block; margin-top: 6px;">
                        {{ __('messages.available_credits') }}
                    </span>
                </div>
                <a href="{{ route('ads.smart.index') }}" class="superdesign-btn-secondary" style="width: 100%; justify-content: center;">
                    <i class="fa fa-arrow-left"></i>
                    {{ __('messages.back') }}
                </a>
            </div>

            <!-- Smart Features Guidance Tip -->
            <div class="superdesign-pts-tip">
                <div class="superdesign-pts-tip-title">
                    <i class="fa fa-lightbulb"></i>
                    <span>{{ __('messages.smart_banner_output') }}</span>
                </div>
                <p style="margin: 0 0 12px 0; font-size: 13px; color: #475569;">
                    {{ __('messages.smart_banner_output_help') }}
                </p>
                <div class="superdesign-pts-tip-title" style="color: #0f766e;">
                    <i class="fa fa-layer-group"></i>
                    <span>{{ __('messages.smart_native_fallback') }}</span>
                </div>
                <p style="margin: 0; font-size: 13px; color: #475569;">
                    {{ __('messages.smart_native_fallback_help') }}
                </p>
            </div>
        </div>

        <!-- Column 2: Main Smart Form -->
        <div class="superdesign-composer-card">
            @include('theme::ads.smart._form', [
                'smartAd' => $smartAd,
                'formAction' => route('ads.smart.store'),
                'formMethod' => 'POST',
                'submitLabel' => __('messages.smart_create_ad'),
                'targetCountries' => $targetCountries,
                'selectedDevices' => $selectedDevices,
                'deviceOptions' => $deviceOptions,
            ])
        </div>
    </div>
</div>

<style>
/* --- @.superdesign Core Tokens for /ads/smart/create --- */
.superdesign-post-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 15px 60px;
}

/* Glassmorphic Hero Banner */
.superdesign-post-hero {
    position: relative;
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.14) 0%, rgba(35, 210, 226, 0.09) 50%, rgba(27, 200, 219, 0.03) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 28px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.superdesign-post-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(97, 93, 250, 0.25) 0%, rgba(97, 93, 250, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.superdesign-hero-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.superdesign-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.superdesign-hero-icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.35);
    flex-shrink: 0;
}
.superdesign-hero-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px 0;
    color: #1e293b;
    letter-spacing: -0.02em;
}
.superdesign-hero-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}
.superdesign-hero-badges {
    display: flex;
    align-items: center;
    gap: 10px;
}
.superdesign-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(97, 93, 250, 0.25);
    color: #615dfa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

/* Grid Architecture */
.superdesign-post-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 28px;
}
@media (max-width: 991px) {
    .superdesign-post-grid {
        grid-template-columns: 1fr;
    }
}

/* Main Composer Card */
.superdesign-composer-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 28px;
}

/* Sidebar Widgets */
.superdesign-sidebar-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 22px;
    margin-bottom: 20px;
}
.superdesign-sidebar-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.superdesign-sidebar-title i {
    color: #615dfa;
}
.superdesign-pts-tip {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.08) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 16px;
    padding: 18px;
    font-size: 13px;
    color: #475569;
    line-height: 1.5;
}
.superdesign-pts-tip-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    color: #615dfa;
    margin-bottom: 6px;
}
.superdesign-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    text-decoration: none;
    transition: all 0.2s ease;
}
.superdesign-btn-secondary:hover {
    background: #e2e8f0;
    color: #334155;
}

/* --- Dark Mode Parity --- */
body[data-theme="css_d"] .superdesign-post-hero,
html.app-skin-dark .superdesign-post-hero,
.dark-mode .superdesign-post-hero {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.1) 100%), #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
}
body[data-theme="css_d"] .superdesign-hero-title,
html.app-skin-dark .superdesign-hero-title,
.dark-mode .superdesign-hero-title {
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-hero-subtitle,
html.app-skin-dark .superdesign-hero-subtitle,
.dark-mode .superdesign-hero-subtitle {
    color: #94a3b8;
}
body[data-theme="css_d"] .superdesign-pill-badge,
html.app-skin-dark .superdesign-pill-badge,
.dark-mode .superdesign-pill-badge {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(97, 93, 250, 0.35);
    color: #818cf8;
}

body[data-theme="css_d"] .superdesign-composer-card,
body[data-theme="css_d"] .superdesign-sidebar-card,
html.app-skin-dark .superdesign-composer-card,
html.app-skin-dark .superdesign-sidebar-card,
.dark-mode .superdesign-composer-card,
.dark-mode .superdesign-sidebar-card {
    background: #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
body[data-theme="css_d"] .superdesign-sidebar-title,
html.app-skin-dark .superdesign-sidebar-title,
.dark-mode .superdesign-sidebar-title {
    color: #f1f5f9;
}
body[data-theme="css_d"] .superdesign-btn-secondary,
html.app-skin-dark .superdesign-btn-secondary,
.dark-mode .superdesign-btn-secondary {
    background: #334155;
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-btn-secondary:hover,
html.app-skin-dark .superdesign-btn-secondary:hover,
.dark-mode .superdesign-btn-secondary:hover {
    background: #475569;
    color: #ffffff;
}
body[data-theme="css_d"] .superdesign-pts-tip,
html.app-skin-dark .superdesign-pts-tip,
.dark-mode .superdesign-pts-tip {
    background: rgba(97, 93, 250, 0.12);
    border-color: rgba(97, 93, 250, 0.3);
    color: #cbd5e1;
}
</style>
@endsection
