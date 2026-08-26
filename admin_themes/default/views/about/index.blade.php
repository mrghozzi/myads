@extends('admin::layouts.admin')

@section('title', __('about.title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<style>
    /* Superdesign specific styles for About Page */
    .about-hero {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
        border-radius: 1rem;
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    .app-skin-dark .about-hero {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(124, 58, 237, 0.05) 100%);
    }
    .about-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.1) 0%, transparent 60%);
        opacity: 0.5;
        animation: rotateBg 20s linear infinite;
        z-index: 0;
    }
    @keyframes rotateBg {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .about-hero-content {
        position: relative;
        z-index: 1;
    }
    .text-gradient {
        background: linear-gradient(to right, #4f46e5, #9333ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .app-skin-dark .text-gradient {
        background: linear-gradient(to right, #818cf8, #c084fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .feature-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 1rem;
        overflow: hidden;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }
    .app-skin-dark .feature-card {
        background: rgba(30, 31, 34, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: rgba(79, 70, 229, 0.3);
    }
    .feature-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
        color: #6366f1;
        transition: all 0.3s ease;
    }
    .feature-card:hover .feature-icon-wrapper {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        transform: scale(1.1) rotate(5deg);
    }
    
    .about-nav-tabs {
        border-bottom: 2px solid rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .app-skin-dark .about-nav-tabs {
        border-bottom-color: rgba(255,255,255,0.05);
    }
    .about-nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        font-weight: 600;
        padding: 1rem 1.5rem;
        position: relative;
        background: transparent;
    }
    .app-skin-dark .about-nav-tabs .nav-link {
        color: #9ca3af;
    }
    .about-nav-tabs .nav-link.active {
        color: #4f46e5;
        background: transparent;
    }
    .app-skin-dark .about-nav-tabs .nav-link.active {
        color: #818cf8;
    }
    .about-nav-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #4f46e5;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    .app-skin-dark .about-nav-tabs .nav-link::after {
        background: #818cf8;
    }
    .about-nav-tabs .nav-link.active::after {
        transform: scaleX(1);
    }
    .about-nav-tabs .nav-link:hover:not(.active) {
        color: #111827;
    }
    .app-skin-dark .about-nav-tabs .nav-link:hover:not(.active) {
        color: #f3f4f6;
    }
    
    .changelog-list {
        list-style: none;
        padding: 0;
    }
    .changelog-list li {
        padding: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: flex-start;
    }
    .app-skin-dark .changelog-list li {
        border-bottom-color: rgba(255,255,255,0.05);
    }
    .changelog-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        margin-right: 15px;
        min-width: 80px;
        text-align: center;
    }
    html[dir="rtl"] .changelog-badge {
        margin-right: 0;
        margin-left: 15px;
    }
    .badge-feature { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-fix { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .badge-optimization { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    
    /* Timeline & Stats Styles */
    .timeline {
        position: relative;
        padding-left: 3rem;
        margin: 2rem 0;
    }
    html[dir="rtl"] .timeline {
        padding-left: 0;
        padding-right: 3rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(79, 70, 229, 0.2);
    }
    html[dir="rtl"] .timeline::before {
        left: auto;
        right: 15px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-icon {
        position: absolute;
        left: -3rem;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: white;
        border: 2px solid #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        z-index: 1;
    }
    html[dir="rtl"] .timeline-icon {
        left: auto;
        right: -3rem;
    }
    .app-skin-dark .timeline-icon {
        background: #1e1f22;
    }
    .stat-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 100%);
        border: 1px solid rgba(255,255,255,0.4);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s ease;
    }
    .app-skin-dark .stat-card {
        background: linear-gradient(135deg, rgba(30,31,34,0.8) 0%, rgba(30,31,34,0.4) 100%);
        border-color: rgba(255,255,255,0.05);
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .app-skin-dark .stat-value {
        background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .env-widget {
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        background: rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .app-skin-dark .env-widget {
        background: rgba(255,255,255,0.02);
        border-color: rgba(255,255,255,0.05);
    }
    .env-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 1rem;
    }
    html[dir="rtl"] .env-icon {
        margin-right: 0;
        margin-left: 1rem;
    }
    
    .github-community-card {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(245, 158, 11, 0.05) 100%);
        border: 1px solid rgba(79, 70, 229, 0.15) !important;
        border-radius: 1rem;
    }
    .app-skin-dark .github-community-card {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(245, 158, 11, 0.1) 100%);
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .sub-github-card {
        background: rgba(255, 255, 255, 0.85);
        border-radius: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .app-skin-dark .sub-github-card {
        background: rgba(30, 31, 34, 0.65);
    }
    .sub-github-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
</style>

<div class="nxl-content">

    <!-- Main Content -->
    <div class="main-content">
        <div class="row">
            <div class="col-12">
                <!-- Hero Section -->
                <div class="about-hero">
                    <div class="about-hero-content">
                        <img src="{{ admin_asset('admin-duralux/images/logo-abbr.png') }}" alt="MYADS Logo" style="height: 80px; margin-bottom: 1.5rem;">
                        <h1 class="display-5 fw-bold mb-3"><span class="text-gradient">MYADS</span> v{{ $currentVersion }}</h1>
                        <p class="lead text-muted mx-auto" style="max-width: 600px;">
                            {{ __('about.subtitle', ['version' => $currentVersion]) }}
                        </p>
                        <div class="mt-4 d-flex flex-wrap gap-2 justify-content-center align-items-center">
                            <a href="{{ route('admin.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="feather-airplay me-2"></i> {{ __('about.return_dashboard') }}
                            </a>
                            <a href="https://github.com/mrghozzi/myads" target="_blank" rel="noopener noreferrer" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm">
                                <i class="feather-star me-2"></i> {{ __('about.star_on_github') }}
                            </a>
                            <a href="https://github.com/mrghozzi/myads/discussions" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm">
                                <i class="feather-life-buoy me-2"></i> {{ __('about.get_support') }}
                            </a>
                            <a href="https://github.com/mrghozzi/myads/wiki" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info rounded-pill px-3 py-2 shadow-sm">
                                <i class="feather-book-open me-2"></i> {{ __('about.docs_wiki') }}
                            </a>
                            <a href="https://www.adstn.ovh/kb/myads" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success rounded-pill px-3 py-2 shadow-sm">
                                <i class="feather-globe me-2"></i> {{ __('about.online_kb') }}
                            </a>
                            <a href="https://github.com/mrghozzi/myads/issues" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger rounded-pill px-3 py-2 shadow-sm">
                                <i class="feather-alert-circle me-2"></i> {{ __('about.report_issue') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs about-nav-tabs justify-content-center" id="aboutTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="whats-new-tab" data-bs-toggle="tab" data-bs-target="#whats-new" type="button" role="tab" aria-controls="whats-new" aria-selected="true">
                            <i class="feather-star me-2"></i> {{ __('about.tab_whats_new') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="changelog-tab" data-bs-toggle="tab" data-bs-target="#changelog" type="button" role="tab" aria-controls="changelog" aria-selected="false">
                            <i class="feather-list me-2"></i> {{ __('about.tab_changelog') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="about-sys-tab" data-bs-toggle="tab" data-bs-target="#about-sys" type="button" role="tab" aria-controls="about-sys" aria-selected="false">
                            <i class="feather-info me-2"></i> {{ __('about.tab_about') }}
                        </button>
                    </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content" id="aboutTabsContent">
                    
                    <!-- What's New Tab -->
                    <div class="tab-pane fade show active" id="whats-new" role="tabpanel" aria-labelledby="whats-new-tab">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%); color: #6366f1;">
                                        <i class="feather-sliders"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_1_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_1_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i class="feather-radio"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_2_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_2_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: rgba(16,185,129,0.1); color: #10b981;">
                                        <i class="feather-shield"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_3_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_3_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                        <i class="feather-cpu"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_4_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_4_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                        <i class="feather-lock"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_5_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_5_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Changelog Tab -->
                    <div class="tab-pane fade" id="changelog" role="tabpanel" aria-labelledby="changelog-tab">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-4">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-icon"><i class="feather-box fs-12"></i></div>
                                    <h6 class="fw-bold mb-1">v4.5.3 <span class="badge bg-soft-success text-success ms-2">Stable (Current)</span></h6>
                                    <p class="text-muted fs-13 mb-3">Architecture & Dynamic Extensibility Release — Live Theme Customizer & Dynamic CSS Variables Engine, Real-Time Events SSE Stream, Anti-Fraud Shield & Ad Quality Engine v2, Smart Micro-Caching & High-Traffic Indexes, Zero-Hardcoding Modular Plugin Architecture, and Comprehensive Security Audit & Vulnerability Hardening.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Added interactive visual <strong>Live Theme Customizer</strong> at <code>/admin/themes/customizer</code> powered by <code>ThemeCustomizerService</code> with responsive split-screen preview (Desktop, Tablet, Mobile), brand colors, Google & Arabic fonts (Inter, Cairo, Tajawal, Roboto, Outfit, System), border radius, and instant CSS compilation.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Implemented high-performance <strong>Real-Time Events Engine (SSE Live Stream)</strong> at <code>/live/stream</code> and <code>/api/live/stream</code> for instant notification & message badge updates, live feed toasts, and non-blocking session handling.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-security mt-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Security</span>
                                            <span class="text-muted fs-13">Upgraded <strong>Anti-Fraud Shield & Ad Quality Engine v2</strong> with IAB Viewability Standard (>= 50% visibility >= 1s), real-time human behavior fingerprinting (mouse trajectory, touch duration, WebGL/bot checks), async view beacons, and advertiser PTS protection with flag reason tags.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Optimization</span>
                                            <span class="text-muted fs-13">Implemented <strong>Smart Micro-Caching & High-Traffic Compound Indexes</strong> reducing ad serving database load by >80% with 20s TTL candidate pools in-memory caching and compound indexes on <code>status</code>, <code>smart_ads</code>, <code>banner</code>, <code>link</code>, and <code>custom_ad_events</code>.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Completely refactored <strong>Zero-Hardcoding Dynamic Plugin Architecture</strong> in <code>PluginServiceProvider</code> with zero-config filesystem auto-discovery in testing and dynamic slug-to-directory resolution for production plugins.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-security mt-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Security</span>
                                            <span class="text-muted fs-13">Executed <strong>Comprehensive Security Audit & Vulnerability Hardening</strong>: eliminated Stored SVG XSS in editor uploads, patched OAuth authorization open redirects, enforced default session encryption and HTTPS cookies, constant-time <code>hash_equals()</code> comparison, SHA-256 update script verification, and strict production SSL enforcement (<code>http_secure()</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-security mt-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Security</span>
                                            <span class="text-muted fs-13">Patched <strong>league/commonmark Quadratic-Time DoS (CVE-2026-71488)</strong> by upgrading to <code>v2.9.0</code>, eliminating algorithmic complexity resource exhaustion on crafted multibyte Markdown.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Synchronized <strong>Developer Platform & REST API v1</strong> schema queries (<code>Status</code>, <code>Message</code>, <code>Notification</code>, <code>BannerImpression</code>, <code>OrderRequest</code>) and resolved application scopes persistence in <code>/developer/apps/{app}</code>.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Update</span>
                                            <span class="text-muted fs-13">Synchronized dependencies, upgrading <strong>nette/utils</strong> to <code>v4.1.5</code>, <strong>laravel/framework</strong> to <code>v12.61.1</code>, and regenerating optimized autoload mappings.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Quality</span>
                                            <span class="text-muted fs-13">Added comprehensive automated PHPUnit test suites covering Live Stream Events, Ad Quality Shield, High-Traffic Micro-Caching, and Theme Customizer (100% pass rate).</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon"><i class="feather-box fs-12"></i></div>
                                    <h6 class="fw-bold mb-1">v4.5.2 <span class="badge bg-soft-secondary text-secondary ms-2">Stable</span></h6>
                                    <p class="text-muted fs-13 mb-3">Maintenance & Extension Extensibility Release — Modular Rich Text Editor Plugin (<code>myads-tinymce-editor</code>) Integration, Admin Hooks Inspector, Continuous Floating Audio Player, Referral System Overhaul, Developer Platform Modernization, Admin Maintenance & FFmpeg Diagnostic Terminals, and Comprehensive Automated Testing.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Integrated <strong>TinyMCE 7 Rich Text Editor Plugin</strong> (<code>plugins/myads-tinymce-editor</code>) leveraging <code>RichTextEditorService</code> hook engine for dynamic selection in <code>/admin/settings</code> with automatic graceful fallback to Quill editor.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Developed <strong>Admin Plugins & Hooks Inspector</strong> at <code>/admin/plugins/inspector</code> for real-time action & filter hook diagnostics, and added dynamic widget injection via <code>registered_plugin_widgets</code> hook.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Integrated <strong>Continuous Floating Audio Player Bar</strong> with spinning disc animation, scrubber controls, and persistent playback state across page navigations via <code>sessionStorage</code>.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Complete <strong>Referral System Overhaul</strong> (<code>/ads/referrals</code>, <code>/referral</code>, <code>/home</code>) with <code>@.superdesign</code> glassmorphism, 1-click clipboard copy, multi-size banner selectors, and 7-network social sharing including ADStn Network.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Enforced <strong>Video Feed Content Isolation</strong> (<code>s_type</code> scoping), Suggested Videos isolation, and Flutter Mobile App Video Hub parity (<code>v1.7.5+17</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Modernized <strong>Developer Platform & Guides</strong> with 27 granular OAuth 2.0 scopes across 7 categories, REST API v1 endpoint directory, parameter schemas, and multi-language SDK code samples.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Overhauled <strong>Admin Maintenance Center</strong> (<code>/admin/maintenance</code>) with Developer IP Whitelist (<code>allowed_ips</code>), Secret Emergency Bypass Token (<code>emergency_token</code>), and 1-click session/log/DB repair tools.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Redesigned <strong>Admin FFmpeg Video Processing Console</strong> (<code>/admin/settings/ffmpeg</code>) with live interactive diagnostic test terminal, codec detection, multi-hosting guides, and binary presets.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-security mt-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Security</span>
                                            <span class="text-muted fs-13">Patched <strong>Public Member IDs Leakage</strong> across user routes, ad serving identifiers, referral cookies, and popovers when <code>public_member_ids_enabled</code> is active.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Resolved critical non-orphan content reaction deletion in <code>OrphanCleanupService</code>, reaction toggle SMTP timeouts with asynchronous termination dispatch, and profile navigation tab horizontal auto-centering in RTL mode.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Resolved Admin Panel 500 error on slow external networks by implementing a 3.0s time budget cap and non-blocking error resilience in plugin and theme update checks.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Quality</span>
                                            <span class="text-muted fs-13">Comprehensive automated test suites added covering Orphan Cleanup safety, Proposals features, Update checks timeouts, TinyMCE plugin, Referral system, and Video hub isolation (100% pass rate).</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon"><i class="feather-box fs-12"></i></div>
                                    <h6 class="fw-bold mb-1">v4.5.1 <span class="badge bg-soft-secondary text-secondary ms-2">Stable</span></h6>
                                    <p class="text-muted fs-13 mb-3">Rich Text Editor Engine (Quill.js & Extensibility Hooks), Database Index & Health Diagnostic Panel, Selective Cache Warmup Engine, Mini Floating PIP Video Player, Shorts Touch Swipe & Audio Tagging, Anti-Click-Farm Security & Ad Quality Index, and Comprehensive Frontend @.superdesign Overhauls.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Integrated <strong>Quill.js Rich Text Editor</strong> with server-side AJAX image uploads into <code>upload/</code>, Quill editor switcher option in <code>/admin/settings</code>, and dynamic plugin hooks (<code>RichTextEditorService</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Added <strong>Database Index & Table Size Health Check</strong> to <code>/admin/system-monitor</code> featuring 10-min cached table KPIs, space overhead warnings, and one-click <code>OPTIMIZE TABLE</code> execution.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Optimization</span>
                                            <span class="text-muted fs-13">Developed <strong>Selective Cache Warmup Engine</strong> (<code>CacheWarmupService</code>) to automatically pre-fill core site settings, system options, and primary menus immediately after admin cache flushes.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Integrated <strong>Mini Floating Picture-in-Picture (PIP) Video Player</strong> on watch pages (<code>/t{id}</code>) powered by <code>IntersectionObserver</code> for non-disruptive scrolling.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Enhanced <strong>Shorts & Clips</strong> with native mobile touch swipe gestures, mouse wheel reel switching, original audio tag pills, and spinning audio disc keyframe animation.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-security mt-1" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Security</span>
                                            <span class="text-muted fs-13">Upgraded <strong>Custom Ads Anti-Click-Farm Engine</strong> with 24h visitor fingerprint rate limiting, 1.5s dwell time verification, suspicious click flagging (<code>is_flagged</code>), and <strong>Ad Quality Index</strong> calculation.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Completed <strong>Frontend @.superdesign Overhauls</strong> across Topic Creation/Edit (<code>/post</code>, <code>/editor/{id}</code>), Directory Submission/Edit (<code>/add-site.html</code>), Service Requests (<code>/orders/create</code>), Ad Promotion (<code>/ads/promote</code>), and Smart Ads (<code>/ads/smart/create</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Resolved Database Cleanup 500 error on <code>options</code> table column names, log cleanup status feedback, video thumbnail fallbacks, and PostCSS transitive security vulnerability (GHSA-r28c-9q8g-f849).</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon"><i class="feather-box fs-12"></i></div>
                                    <h6 class="fw-bold mb-1">v4.5.0 <span class="badge bg-soft-secondary text-secondary ms-2">Stable</span></h6>
                                    <p class="text-muted fs-13 mb-3">YouTube-Style Watch Page & Video Hub, Gamification Badges & Quests, Admin SEO Suite & Knowledgebase Power Overhaul, 95%+ DB Aggregation Engine, Smart Widgets & Flutter App Parity.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Built dedicated <strong>YouTube-Style Video Watch Page</strong> (<code>/t{id}</code>) with HTML5 custom video player, Hexagon publisher avatars, standalone popover flyouts, 4-column suggested video filtering, Video Title & Cover Thumbnail composer, 256MB PHP/Apache upload capacity, and dedicated <strong>YouTube Shorts & Video Hub</strong> (<code>/video</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Expanded Gamification Engine with <strong>5 New Multimedia Badges</strong> (<em>Video Star, Clips Master, Audio Maestro, Resource Vault, Multimedia Pioneer</em>) and <strong>5 New Daily & Weekly Quests</strong> across all 14 supported locales with full test coverage.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Complete <strong>Superdesign UI Overhaul</strong> of all 7 Admin SEO Suite views (<code>/admin/seo/*</code>) with real-time performance & DB cleanup retention panel, and reimagined <strong>Admin Knowledgebase Hub</strong> (<code>/admin/knowledgebase</code>) with single-row filter bar and live KPI statistics.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Optimization</span>
                                            <span class="text-muted fs-13">Optimized Admin Dashboard (<code>/admin</code>) load times by replacing 300+ loop database queries with <strong>Single-Pass SQL Aggregations</strong> (95%+ reduction), added composite DB B-Tree indexes on reaction/option tables, and unified atomic <code>ReactionService</code>.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Introduced permission-gated <strong>Smart Widget Management Prompt</strong> (<code>&lt;x-widget-column&gt;</code>) and redesigned <strong>Admin Widgets Hub</strong> (<code>/admin/widgets</code>) with place pre-selection, location filter chips, and drag-and-drop row reordering.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Released <strong>Flutter Mobile App v1.6.0+11</strong> featuring native YouTube-style video watch screen (<code>post_details_screen.dart</code>), video title & thumbnail composer support, and vertical Hexagon avatars.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Resolved installer migration foreign key mismatches on <code>user_blocks</code> and <code>developer_apps</code> tables, store editor client validation blocking, video hub trending 500 error, and reaction debouncer duplicate network calls.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon"><i class="feather-box fs-12"></i></div>
                                    <h6 class="fw-bold mb-1">v4.4.6</h6>
                                    <p class="text-muted fs-13 mb-3">Unified Admin Settings Control Panel, Dedicated Mobile App Settings Panel, API Key Security Masking, Log Bloat & Server Storage Optimization, New Language Packs, Marketplace & Store Enhancements.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Merged <code>/admin/settings/system</code> into <strong>Unified Admin Settings Control Panel</strong> (<code>/admin/settings</code>) and overhauled S3, DigitalOcean, Google Cloud, and FTP storage settings with <strong>Superdesign</strong> UI and diagnostic tools.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Created <strong>Dedicated Mobile App Settings</strong> (<code>/admin/settings/mobile</code>) with official Flutter repository setup guide, secret API key masking/regeneration, and standardized media upload array key parity (<code>images[]</code>, <code>videos[]</code>, <code>audios[]</code>, <code>files[]</code>).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Optimization</span>
                                            <span class="text-muted fs-13">Converted logging stack to <strong>Daily Log Rotation</strong> (<code>LOG_STACK=daily</code>), implemented automated <code>myads:log-cleanup</code> Artisan command, probabilistic log pruning for shared hosting, and a <strong>Log Files Monitor</strong> on <code>/admin/database-cleanup</code>.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Added <strong>5 New Language Packs</strong> for Russian (<code>ru</code>), Serbian (<code>sr</code>), Japanese (<code>ja</code>), Simplified Chinese (<code>zh_CN</code>), and Traditional Chinese (<code>zh_TW</code>) across 3,625+ system keys (14 total languages).</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Expanded <strong>Store Market Grid</strong> to display all 9 product categories with product counts, minimalist 3D category illustrations, and full-width header layouts.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Resolved PTS Activities 500 error, Bootstrap modal backdrop stacking, slow network FOUC, Knowledgebase engine fallbacks, and Admin Mail SMTP environment fallbacks.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Update</span>
                                            <span class="text-muted fs-13">Bumped <strong>guzzlehttp/guzzle</strong> to 7.15.1 and <strong>shell-quote</strong> to 1.9.0.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.5</h6>
                                    <p class="text-muted fs-13 mb-3">Stable Release with Mobile API Community Feed Enhancements, UI Overhaul, and Authorization Fixes.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Redesigned Member Dashboard, Marketplace catalog, and Community Feed search results with <strong>Superdesign</strong> aesthetic and responsive layouts.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Feature</span>
                                            <span class="text-muted fs-13">Enhanced <strong>Mobile API</strong> to handle paginated API endpoints and fixed aggressive header stripping on shared hosts.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Optimization</span>
                                            <span class="text-muted fs-13">Added <strong>Shared Hosting Guide</strong> and <strong>Server Pressure Sources</strong> sections in the Admin panel to help diagnose and reduce server load.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Fix</span>
                                            <span class="text-muted fs-13">Fixed Mobile API relationship loading, Store product view counts, and Forum category visibility mapping.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Update</span>
                                            <span class="text-muted fs-13">Bumped <strong>axios</strong> from 1.16.0 to 1.18.0.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.4</h6>
                                    <p class="text-muted fs-13 mb-2">Stable release with Automated Database Maintenance, Smart Ads Memory Fix, and Performance Toggles.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Introduced <strong>Automated Database Maintenance</strong> with probabilistic garbage collection that prunes old records on shared hosting without cron.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Resolved a severe memory leak in Smart Ads by replacing eager-loading with ID plucking and batch limits.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Introduced a <strong>Simple Chronological Feed Mode</strong> for the community portal, reducing CPU usage by over 90% for shared hosting.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added <strong>Resource-Heavy Features</strong> toggles to disable User Online Status and SEO Daily Metrics tracking.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.3</h6>
                                    <p class="text-muted fs-13 mb-2">Stable release with Ad Serving Performance Fix and BBCode Emails.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added support for BBCode email formatting (<code>[email=...]</code>) in community posts and forum topics.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Resolved severe CPU and RAM consumption bottlenecks in ad serving endpoints using direct database JSON targeting.</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.2</h6>
                                    <p class="text-muted fs-13 mb-2">Stable release introducing the Database Cleanup Tool and Admin UI Enhancements.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added a new <strong>Database Cleanup</strong> tool to manually prune large analytics and tracking tables.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Reorganized the admin sidebar navigation by introducing a unified <strong>System</strong> menu.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Fix</span>
                                            <span class="text-muted fs-13">Fixed a 500 Internal Server Error on the Database Cleanup page and enforced multilingual support.</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.1</h6>
                                    <p class="text-muted fs-13 mb-2">Stable release with Pin Post to Profile, BBCode URL formatting, and critical security and performance fixes.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added <strong>Pin Post to Profile</strong> feature, allowing members to pin a single post to the top of their personal profile page.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added BBCode URL formatting support with dynamic domain blockage filtering.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Fix</span>
                                            <span class="text-muted fs-13">Fixed a 500 Internal Server Error in community feed/member profiles caused by malformed Blade directives.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Resolved CPU consumption on ad serving endpoints by adding missing database indexes to the state table.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Fix</span>
                                            <span class="text-muted fs-13">Resolved a critical 500 error on Knowledgebase pages due to an orphaned tablespace.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Security</span>
                                            <span class="text-muted fs-13">Allowed stackedit.io iframe in the Content-Security-Policy (CSP) to enable StackEdit Markdown editor.</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-icon" style="border-color: #6b7280; color: #6b7280;"><i class="feather-check fs-12"></i></div>
                                    <h6 class="fw-bold mb-1 text-muted">v4.4.0</h6>
                                    <p class="text-muted fs-13 mb-2">Major release with Superdesign aesthetics, Performance Settings, System Monitor, and Free SEO Checker.</p>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added Free SEO Checker with role-based access gating and a premium "Superdesign" UI.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Added System Monitor dashboard for real-time overview of server resource consumption.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-feature mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Feature</span>
                                            <span class="text-muted fs-13">Implemented Skeleton Loaders (Shimmer Effect) in the community feed for a premium loading state.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-optimization mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Optimization</span>
                                            <span class="text-muted fs-13">Eliminated severe N+1 database queries on the community feed by implementing bulk eager-loading.</span>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <span class="changelog-badge badge-fix mt-1" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">Security</span>
                                            <span class="text-muted fs-13">Patched path traversal vulnerability in Admin Media Manager to prevent arbitrary file renaming.</span>
                                        </div>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                    </div>

                    <!-- About Tab -->
                    <div class="tab-pane fade" id="about-sys" role="tabpanel" aria-labelledby="about-sys-tab">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card border-0 shadow-sm rounded-4 p-5 mb-4 text-center">
                                    <img src="{{ admin_asset('admin-duralux/images/logo-full.png') }}" alt="MYADS" style="height: 45px; margin-bottom: 2rem;" class="mx-auto">
                                    <p class="lead text-muted mb-4" style="max-width: 800px; margin: 0 auto;">
                                        {{ __('about.about_description') }}
                                    </p>
                                    
                                    <!-- Platform Stats -->
                                    <h6 class="fw-bold mb-3 mt-4 text-muted text-uppercase fs-11">{{ __('about.platform_stats') }}</h6>
                                    <div class="row g-3 justify-content-center mb-4">
                                        <div class="col-md-4">
                                            <div class="stat-card">
                                                <div class="stat-value counter" data-target="{{ $totalUsers ?? 0 }}">0</div>
                                                <div class="text-muted fs-13 mt-1 fw-bold">{{ __('about.stat_users') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-card">
                                                <div class="stat-value counter" data-target="{{ $totalPosts ?? 0 }}">0</div>
                                                <div class="text-muted fs-13 mt-1 fw-bold">{{ __('about.stat_posts') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-card">
                                                <div class="stat-value counter" data-target="{{ $totalProducts ?? 0 }}">0</div>
                                                <div class="text-muted fs-13 mt-1 fw-bold">{{ __('about.stat_products') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- System Environment -->
                                    <h6 class="fw-bold mb-3 mt-4 text-muted text-uppercase fs-11">{{ __('about.system_environment') }}</h6>
                                    <div class="row g-3 justify-content-center">
                                        <div class="col-md-4">
                                            <div class="env-widget text-start">
                                                <div class="env-icon bg-soft-primary text-primary"><i class="feather-code"></i></div>
                                                <div>
                                                    <div class="fs-12 text-muted mb-1">{{ __('about.env_php') }}</div>
                                                    <div class="fw-bold fs-14">{{ $phpVersion ?? 'Unknown' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="env-widget text-start">
                                                <div class="env-icon bg-soft-danger text-danger"><i class="feather-box"></i></div>
                                                <div>
                                                    <div class="fs-12 text-muted mb-1">{{ __('about.env_laravel') }}</div>
                                                    <div class="fw-bold fs-14">v{{ $laravelVersion ?? 'Unknown' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="env-widget text-start">
                                                <div class="env-icon bg-soft-info text-info"><i class="feather-database"></i></div>
                                                <div>
                                                    <div class="fs-12 text-muted mb-1">{{ __('about.env_mysql') }}</div>
                                                    <div class="fw-bold fs-14 text-truncate" style="max-width: 120px;" title="{{ $mysqlVersion ?? 'Unknown' }}">{{ $mysqlVersion ?? 'Unknown' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="fw-bold mb-4 text-center">{{ __('about.core_features') }}</h5>
                                
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-users"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_social_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_social_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-activity"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_exchange_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_exchange_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-shopping-cart"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_store_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_store_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-briefcase"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_services_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_services_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-message-square"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_forum_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_forum_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px;">
                                                <i class="feather-award"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_gamification_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_gamification_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px; background: rgba(16,185,129,0.1); color: #10b981;">
                                                <i class="feather-activity"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_seo_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_seo_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card p-4 d-flex align-items-start">
                                            <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 22px; background: rgba(99,102,241,0.1); color: #6366f1;">
                                                <i class="feather-globe"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-2">{{ __('about.feat_i18n_title') }}</h6>
                                                <p class="text-muted mb-0 fs-13">{{ __('about.feat_i18n_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="github-community-card p-4 mb-4 mt-5">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="feature-icon-wrapper flex-shrink-0 me-3 mb-0" style="width: 50px; height: 50px; font-size: 24px; background: rgba(79, 70, 229, 0.15); color: #4f46e5;">
                                            <i class="feather-github"></i>
                                        </div>
                                        <div class="text-start">
                                            <h5 class="fw-bold mb-1">{{ __('about.github_community_title') }}</h5>
                                            <p class="text-muted mb-0 fs-13">{{ __('about.github_community_subtitle') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <!-- Star Project -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card h-100 border-0 p-4 shadow-none text-center sub-github-card">
                                                <div class="text-warning mb-2 fs-28"><i class="feather-star"></i></div>
                                                <h6 class="fw-bold mb-2">{{ __('about.star_project_title') }}</h6>
                                                <p class="text-muted fs-13 mb-3">{{ __('about.star_project_desc') }}</p>
                                                <a href="https://github.com/mrghozzi/myads" target="_blank" rel="noopener noreferrer" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm mt-auto">
                                                    <i class="feather-star me-1"></i> {{ __('about.star_on_github') }}
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Support Discussions -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card h-100 border-0 p-4 shadow-none text-center sub-github-card">
                                                <div class="text-primary mb-2 fs-28"><i class="feather-life-buoy"></i></div>
                                                <h6 class="fw-bold mb-2">{{ __('about.support_title') }}</h6>
                                                <p class="text-muted fs-13 mb-3">{{ __('about.support_desc') }}</p>
                                                <a href="https://github.com/mrghozzi/myads/discussions" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-3 py-2 shadow-sm mt-auto">
                                                    <i class="feather-message-square me-1"></i> {{ __('about.get_support') }}
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Lessons & Docs -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card h-100 border-0 p-4 shadow-none text-center sub-github-card">
                                                <div class="text-info mb-2 fs-28"><i class="feather-book-open"></i></div>
                                                <h6 class="fw-bold mb-2">{{ __('about.docs_title') }}</h6>
                                                <p class="text-muted fs-13 mb-3">{{ __('about.docs_desc') }}</p>
                                                <div class="d-flex flex-column gap-2 mt-auto">
                                                    <a href="https://github.com/mrghozzi/myads/wiki" target="_blank" rel="noopener noreferrer" class="btn btn-info text-white rounded-pill px-2 py-1 fs-12 shadow-sm">
                                                        <i class="feather-book me-1"></i> {{ __('about.docs_wiki') }}
                                                    </a>
                                                    <a href="https://www.adstn.ovh/kb/myads" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info rounded-pill px-2 py-1 fs-12 shadow-sm">
                                                        <i class="feather-globe me-1"></i> {{ __('about.online_kb') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Report Bug -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card h-100 border-0 p-4 shadow-none text-center sub-github-card">
                                                <div class="text-danger mb-2 fs-28"><i class="feather-alert-triangle"></i></div>
                                                <h6 class="fw-bold mb-2">{{ __('about.report_issue_title') }}</h6>
                                                <p class="text-muted fs-13 mb-3">{{ __('about.report_issue_desc') }}</p>
                                                <a href="https://github.com/mrghozzi/myads/issues" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger rounded-pill px-3 py-2 shadow-sm mt-auto">
                                                    <i class="feather-bug me-1"></i> {{ __('about.report_issue') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center pt-3 border-top border-dashed border-secondary">
                                    <a href="https://github.com/mrghozzi/myads" target="_blank" rel="noopener noreferrer" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm mb-3">
                                        <i class="feather-github me-2"></i> {{ __('about.github_repo') }}
                                    </a>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.made_with_love') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Confetti Animation on Load
        var duration = 3 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);

        // Animated Counters
        const counters = document.querySelectorAll('.counter');
        const speed = 200; // lower = faster

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        });
    });
</script>
@endpush
