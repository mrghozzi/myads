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
                                    <div class="feature-icon-wrapper">
                                        <i class="feather-layout"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_1_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_1_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper">
                                        <i class="feather-zap"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_2_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_2_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper">
                                        <i class="feather-shield"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_3_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_3_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper">
                                        <i class="feather-search"></i>
                                    </div>
                                    <h5 class="fw-bold mb-3">{{ __('about.feature_4_title') }}</h5>
                                    <p class="text-muted mb-0 fs-13">{{ __('about.feature_4_desc') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="feature-card p-4">
                                    <div class="feature-icon-wrapper" style="background: rgba(16,185,129,0.1); color: #10b981;">
                                        <i class="feather-activity"></i>
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
                                    <h6 class="fw-bold mb-1">v4.4.5 <span class="badge bg-soft-primary text-primary ms-2">Current</span></h6>
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
