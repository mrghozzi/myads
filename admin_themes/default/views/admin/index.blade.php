@extends('admin::layouts.admin')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════════════
   MYADS SUPERDESIGN DASHBOARD STYLING
   ══════════════════════════════════════════════════════════════════ */
.sd-dashboard {
    --sd-primary: #615dfa;
    --sd-primary-hover: #4e49e8;
    --sd-accent: #23d2e2;
    --sd-success: #10b981;
    --sd-warning: #f59e0b;
    --sd-danger: #ef4444;
    --sd-radius-lg: 20px;
    --sd-radius-md: 14px;
    --sd-radius-sm: 10px;
    font-family: inherit;
}

/* Superdesign Hero Banner */
.sd-hero {
    position: relative;
    border-radius: var(--sd-radius-lg);
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
    padding: 2.25rem 2rem;
    color: #ffffff;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(97, 93, 250, 0.2);
    margin-bottom: 1.75rem;
}

.sd-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(35, 210, 226, 0.35) 0%, rgba(35, 210, 226, 0) 70%);
    pointer-events: none;
}

.sd-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(97, 93, 250, 0.4) 0%, rgba(97, 93, 250, 0) 70%);
    pointer-events: none;
}

.sd-hero__content {
    position: relative;
    z-index: 2;
}

.sd-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #23d2e2;
    margin-bottom: 0.75rem;
}

.sd-hero__title {
    font-size: 1.85rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 0.5rem;
    color: #ffffff;
}

.sd-hero__subtitle {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 0;
}

/* Dynamic Admin Tip Card inside Hero Header */
.sd-tip-card {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: var(--sd-radius-md);
    padding: 1.25rem 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.sd-tip-card:hover {
    border-color: rgba(35, 210, 226, 0.4);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
}

.sd-tip-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.6rem;
}

.sd-tip-category-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.sd-tip-rotator-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.sd-tip-rotator-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #23d2e2;
    transform: rotate(15deg);
}

.sd-tip-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.35rem;
}

.sd-tip-body {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.88);
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

/* System Health Status Bar */
.sd-health-bar {
    background: #ffffff;
    border-radius: var(--sd-radius-md);
    padding: 0.85rem 1.25rem;
    border: 1px solid rgba(97, 93, 250, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    margin-bottom: 1.5rem;
}

.app-skin-dark .sd-health-bar {
    background: #1e293b;
    border-color: rgba(255, 255, 255, 0.08);
}

.sd-health-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 600;
}

.sd-health-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
}

.sd-health-dot.pulse {
    animation: sdPulse 2s infinite;
}

@keyframes sdPulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Superdesign Card Base */
.sd-card {
    background: #ffffff;
    border-radius: var(--sd-radius-md);
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
}

.sd-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(97, 93, 250, 0.08);
}

.app-skin-dark .sd-card {
    background: #1e293b;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.2);
}

/* Stat Card Icon Wrappers */
.sd-icon-box {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #ffffff;
    font-size: 22px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

/* Metric Counter typography */
.sd-stat-num {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.5px;
}

/* Reactions Strip */
.sd-reactions-strip {
    background: #ffffff;
    border-radius: var(--sd-radius-md);
    border: 1px solid rgba(97, 93, 250, 0.08);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    padding: 1.25rem 1rem;
}

.app-skin-dark .sd-reactions-strip {
    background: #1e293b;
    border-color: rgba(255, 255, 255, 0.08);
}

.sd-reaction-pill {
    transition: transform 0.2s ease;
    cursor: default;
}

.sd-reaction-pill:hover {
    transform: scale(1.08);
}

/* Skeleton Loading Shimmer */
.sd-skeleton {
    display: inline-block;
    background: linear-gradient(90deg, rgba(97, 93, 250, 0.08) 25%, rgba(97, 93, 250, 0.18) 50%, rgba(97, 93, 250, 0.08) 75%);
    background-size: 200% 100%;
    animation: sd-shimmer 1.5s infinite;
    border-radius: 6px;
}

.app-skin-dark .sd-skeleton {
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.05) 25%, rgba(255, 255, 255, 0.12) 50%, rgba(255, 255, 255, 0.05) 75%);
    background-size: 200% 100%;
}

@keyframes sd-shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.sd-fade-in {
    animation: sdFadeIn 0.35s ease-in forwards;
}

@keyframes sdFadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* GitHub Sponsors Styling */
.sd-github-sponsor-btn {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.sd-github-sponsor-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(234, 74, 170, 0.45) !important;
    filter: brightness(1.05);
}
.sd-sponsor-chip {
    transition: all 0.2s ease;
}
.sd-sponsor-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(234, 74, 170, 0.35);
    color: #ffffff !important;
}
</style>

<div class="sd-dashboard">
    <!-- ═══════════════════ SUPERDESIGN HERO BANNER ═══════════════════ -->
    <section class="sd-hero">
        <div class="row align-items-center g-4">
            <!-- Left Hero Info -->
            <div class="col-lg-6 sd-hero__content">
                <div class="sd-hero__badge">
                    <i class="feather-grid"></i> {{ __('messages.admin_panel') ?? 'MYADS Superdesign Admin Suite' }}
                </div>
                <h1 class="sd-hero__title">{{ __('messages.dashboard') ?? 'Dashboard Hub' }}</h1>
                <p class="sd-hero__subtitle">
                    {{ __('messages.statistics') }} • <span id="hero-stat-users"><span class="sd-skeleton" style="width: 48px; height: 16px; vertical-align: middle;"></span></span> {{ __('messages.users') }} • <span id="hero-stat-posts"><span class="sd-skeleton" style="width: 48px; height: 16px; vertical-align: middle;"></span></span> {{ __('messages.Posts') }}
                </p>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <span class="badge px-3 py-2" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); font-size: 0.8rem;">
                        <i class="feather-wifi text-success me-1"></i> <span id="hero-stat-online"><span class="sd-skeleton" style="width: 28px; height: 14px; vertical-align: middle;"></span></span> {{ __('messages.online') }}
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); font-size: 0.8rem;">
                        <i class="feather-tag me-1" style="color: #23d2e2;"></i> v{{ $currentVersion }}
                    </span>
                </div>
            </div>

            <!-- Right Hero Dynamic Admin Tip Box -->
            <div class="col-lg-6">
                <div class="sd-tip-card" id="admin-tip-container">
                    <div class="sd-tip-header">
                        <span class="sd-tip-category-badge" id="tip-category-badge" style="background: {{ $currentTip['badge_bg'] }};">
                            <i class="{{ $currentTip['icon'] }}" id="tip-category-icon"></i> <span id="tip-category-text">{{ $currentTip['category'] }}</span>
                        </span>
                        <button type="button" class="sd-tip-rotator-btn" id="btn-rotate-tip" title="{{ __('messages.another_tip') ?? 'Another Tip' }}">
                            <i class="feather-refresh-cw"></i> <span>{{ __('messages.another_tip') ?? 'Another Tip' }}</span>
                        </button>
                    </div>
                    <h5 class="sd-tip-title" id="tip-title">💡 {{ $currentTip['title'] }}</h5>
                    <p class="sd-tip-body" id="tip-desc">{{ $currentTip['tip'] }}</p>
                    <div class="d-flex align-items-center justify-content-between pt-1">
                        <span class="small opacity-75" style="font-size: 0.75rem;"><i class="feather-info me-1"></i> {{ __('messages.tips_rotate_on_refresh') ?? 'Tips rotate on page refresh' }}</span>
                        <a href="{{ $currentTip['action_url'] }}" class="btn btn-sm px-3 fw-bold" id="tip-action-link" style="background: #23d2e2; color: #0f172a; border-radius: 20px; font-size: 0.78rem;">
                            {{ $currentTip['action_text'] }} <i class="feather-arrow-left ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SYSTEM HEALTH STATUS BAR ═══════════════════ -->
    <div class="sd-health-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="sd-health-item">
                <span class="sd-health-dot pulse" style="background: #10b981;"></span>
                <span class="text-muted">{{ __('messages.server_database') ?? 'Server & DB:' }}</span> <span class="text-success fw-bold">{{ __('messages.status_excellent') ?? 'Excellent' }}</span>
            </div>
            <div class="sd-health-item">
                <i class="feather-cpu text-primary"></i>
                <span class="text-muted">{{ __('messages.shg_cache_system') ?? 'Cache:' }}</span> <span class="text-primary fw-bold">{{ __('messages.shg_enabled') ?? 'Enabled' }}</span>
            </div>
            <div class="sd-health-item">
                <i class="feather-shield text-info"></i>
                <span class="text-muted">{{ __('messages.security_sessions') ?? 'Security & Sessions:' }}</span> <span class="text-info fw-bold">{{ __('messages.status_protected') ?? 'Protected' }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.system_monitor') }}" class="btn btn-sm btn-light fw-bold border-0 px-3" style="border-radius: 20px; font-size: 0.78rem;">
                <i class="feather-activity me-1 text-primary"></i> {{ __('messages.system_monitor') ?? 'Performance Monitor' }}
            </a>
            <a href="{{ route('admin.database_cleanup') }}" class="btn btn-sm btn-light fw-bold border-0 px-3" style="border-radius: 20px; font-size: 0.78rem;">
                <i class="feather-trash-2 me-1 text-danger"></i> {{ __('messages.database_cleanup') ?? 'Database Cleanup' }}
            </a>
        </div>
    </div>

    <!-- ═══════════════════ UPDATE ALERT CONTAINER ═══════════════════ -->
    <div id="admin-update-alert-container" data-url="{{ route('admin.ajax.version_check') }}"></div>

    <!-- ═══════════════════ TOP KPI STATS ROW (AJAX) ═══════════════════ -->
    <div id="dashboard-kpis-container" data-url="{{ route('admin.ajax.kpis') }}">
        <div class="row g-3 mb-4">
            @for($i = 0; $i < 4; $i++)
            <div class="col-xl-3 col-sm-6">
                <div class="sd-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sd-icon-box" style="background: rgba(97, 93, 250, 0.08);">
                            <div class="spinner-border text-primary" role="status" style="width: 1.25rem; height: 1.25rem; border-width: 0.15rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="sd-skeleton mb-2" style="width: 70px; height: 26px;"></div>
                            <div class="sd-skeleton" style="width: 100px; height: 14px;"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-2 border-top">
                        <div class="sd-skeleton" style="width: 60px; height: 14px;"></div>
                        <div class="sd-skeleton" style="width: 60px; height: 14px;"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- ═══════════════════ MARKETPLACE RECOMMENDATIONS ═══════════════════ -->
    <div id="marketplace-recommendations-container" data-url="{{ route('admin.marketplace_recommendations') }}">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem; border-width: 0.2rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ ANALYTICS CHARTS ROW (AJAX) ═══════════════════ -->
    <div class="row g-3 mb-4" id="dashboard-ad-charts-container" data-url="{{ route('admin.ajax.ad_charts') }}">
        <!-- Ad Distribution Chart -->
        <div class="col-xl-4">
            <div class="sd-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-pie-chart me-2" style="color: #615dfa;"></i> {{ __('messages.ad_statistics') ?? 'Ad Statistics' }}</h6>
                    <span class="badge bg-light text-muted border">{{ __('messages.overview') ?? 'Overview' }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="min-height: 270px; position: relative;">
                    <div id="ad-dist-loader" class="text-center position-absolute">
                        <div class="spinner-border text-primary" role="status" style="width: 1.8rem; height: 1.8rem; border-width: 0.18rem;"></div>
                    </div>
                    <div style="width: 100%; max-width: 260px;">
                        <canvas id="adDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement Bar Chart -->
        <div class="col-xl-8">
            <div class="sd-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-bar-chart-2 me-2" style="color: #23d2e2;"></i> {{ __('messages.views_clicks_engagement') ?? 'Views & Clicks Engagement' }}</h6>
                    <span class="badge bg-light text-muted border">{{ __('messages.platform_total') ?? 'Platform Total' }}</span>
                </div>
                <div class="d-flex align-items-center" style="min-height: 270px; position: relative;">
                    <div id="ad-eng-loader" class="text-center w-100 position-absolute">
                        <div class="spinner-border text-info" role="status" style="width: 1.8rem; height: 1.8rem; border-width: 0.18rem;"></div>
                    </div>
                    <div style="width: 100%;">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ COMMUNITY TREND CHARTS (AJAX) ═══════════════════ -->
    <div class="row g-3 mb-4" id="dashboard-community-charts-container" data-url="{{ route('admin.ajax.community_charts') }}">
        <div class="col-xl-6">
            <div class="sd-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-edit-3 me-2" style="color: #615dfa;"></i> {{ __('messages.Posts') }} (30 {{ __('messages.days') ?? 'days' }})</h6>
                    <span class="badge bg-light text-muted border">{{ __('messages.posts_chart') ?? 'Posts Chart' }}</span>
                </div>
                <div style="height: 330px; position: relative;" class="d-flex align-items-center justify-content-center">
                    <div id="posts-community-loader" class="text-center position-absolute">
                        <div class="spinner-border text-primary" role="status" style="width: 1.8rem; height: 1.8rem; border-width: 0.18rem;"></div>
                    </div>
                    <canvas id="postsCommunityChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="sd-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-message-circle me-2" style="color: #10b981;"></i> {{ __('messages.comments_reactions') ?? 'Comments & Reactions' }} (30 {{ __('messages.days') ?? 'days' }})</h6>
                    <span class="badge bg-light text-muted border">{{ __('messages.engagement_chart') ?? 'Engagement Chart' }}</span>
                </div>
                <div style="height: 330px; position: relative;" class="d-flex align-items-center justify-content-center">
                    <div id="eng-community-loader" class="text-center position-absolute">
                        <div class="spinner-border text-success" role="status" style="width: 1.8rem; height: 1.8rem; border-width: 0.18rem;"></div>
                    </div>
                    <canvas id="engagementCommunityChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ REACTION COUNTERS STRIP (AJAX) ═══════════════════ -->
    <div id="dashboard-reactions-container" data-url="{{ route('admin.ajax.reactions') }}">
        <div class="sd-reactions-strip mb-4">
            <div class="d-flex align-items-center justify-content-between px-2 mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="feather-thumbs-up me-2 text-danger"></i> {{ __('messages.live_member_reactions') ?? 'Live Member Reactions' }}</h6>
                <div class="sd-skeleton" style="width: 90px; height: 20px;"></div>
            </div>
            <div class="d-flex align-items-center justify-content-around flex-wrap gap-3 py-2">
                @for($i = 0; $i < 8; $i++)
                <div class="text-center px-3 py-2 sd-reaction-pill">
                    <div class="mb-2 mx-auto sd-skeleton" style="width: 44px; height: 44px; border-radius: 50%;"></div>
                    <div class="sd-skeleton mb-1" style="width: 40px; height: 18px;"></div>
                    <div class="sd-skeleton" style="width: 35px; height: 10px;"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- ═══════════════════ ACTIVITY & QUICK ACTIONS ROW ═══════════════════ -->
    <div class="row g-3 mb-4">
        <!-- Left Activity Section (AJAX) -->
        <div class="col-xxl-8" id="dashboard-activity-container" data-url="{{ route('admin.ajax.activity') }}">
            <div class="sd-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-activity me-2" style="color: #10b981;"></i> {{ __('messages.activity_engagement') ?? 'Activity & Engagement' }}</h6>
                    <div class="spinner-border spinner-border-sm text-success" role="status" style="width: 1.2rem; height: 1.2rem; border-width: 0.15rem;"></div>
                </div>
                <div class="row g-3">
                    @for($i = 0; $i < 4; $i++)
                    <div class="col-md-6 col-lg-3">
                        <div class="text-center p-3 rounded-3" style="background: rgba(97, 93, 250, 0.03); border: 1px solid rgba(97, 93, 250, 0.08);">
                            <div class="mb-2 mx-auto sd-skeleton" style="width: 54px; height: 54px; border-radius: 50%;"></div>
                            <div class="sd-skeleton mb-1" style="width: 70px; height: 14px;"></div>
                            <div class="sd-skeleton" style="width: 50px; height: 18px;"></div>
                        </div>
                    </div>
                    @endfor
                </div>
                <div class="row g-3 mt-2">
                    @for($i = 0; $i < 3; $i++)
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(97,93,250,0.02); border: 1px solid rgba(97,93,250,0.06);">
                            <div class="sd-skeleton" style="width: 24px; height: 24px; border-radius: 6px;"></div>
                            <div class="flex-grow-1">
                                <div class="sd-skeleton mb-1" style="width: 40px; height: 16px;"></div>
                                <div class="sd-skeleton" style="width: 60px; height: 12px;"></div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="sd-skeleton mb-2" style="width: 160px; height: 16px;"></div>
                    <div class="d-flex flex-wrap gap-2">
                        @for($i = 0; $i < 8; $i++)
                        <div class="sd-skeleton" style="width: 90px; height: 26px; border-radius: 6px;"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Quick Actions & Developer Info -->
        <div class="col-xxl-4">
            <!-- Quick Actions -->
            <div class="sd-card p-4 mb-3">
                <h6 class="fw-bold text-dark mb-3"><i class="feather-zap me-2" style="color: #f59e0b;"></i> {{ __('messages.quick_actions') ?? 'Quick Actions' }}</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.reports') }}" class="btn d-flex justify-content-between align-items-center px-3 py-2" style="background: linear-gradient(135deg, #615dfa, #8b5cf6); color: #fff; border: none; border-radius: 10px;">
                        <span><i class="feather-flag me-2"></i>{{ __('messages.report') }}</span>
                        <span class="badge bg-white" style="color: #615dfa;" id="quick-action-reports-badge"><span class="sd-skeleton" style="width: 18px; height: 12px; vertical-align: middle;"></span></span>
                    </a>
                    
                    <div class="btn-group w-100" role="group">
                        @php $is_rtl = is_locale_rtl(); @endphp
                        <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn w-25 px-3 py-2" style="background: #1e293b; color: #fff; border: none; border-radius: {{ $is_rtl ? '0 10px 10px 0' : '10px 0 0 10px' }};">
                            <i class="feather-external-link"></i>
                        </a>
                        <form action="{{ route('admin.sitemap.generate') }}" method="POST" class="w-75">
                            @csrf
                            <button type="submit" class="btn w-100 px-3 py-2" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; border-radius: {{ $is_rtl ? '10px 0 0 10px' : '0 10px 10px 0' }};">
                                <i class="feather-refresh-cw me-1"></i> {{ __('messages.seo_refresh_sitemap') }}
                            </button>
                        </form>
                    </div>

                    <a href="https://github.com/mrghozzi/myads/wiki/changelogs" target="_blank" class="btn px-3 py-2" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; border-radius: 10px;">
                        <i class="feather-book-open me-1"></i> {{ __('messages.Changelogs') }} <i class="feather-external-link ms-1" style="font-size: 12px;"></i>
                    </a>
                </div>
            </div>

            <!-- Developer & Community Hub -->
            <div class="sd-card p-4 text-center mb-3">
                <div class="mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,0.1);">
                    <i class="feather-code" style="color: #3b82f6; font-size: 20px;"></i>
                </div>
                <h6 class="fw-semibold text-muted mb-1" style="font-size: 0.8rem;">{{ __('messages.developed_by') ?? 'Developed by' }}</h6>
                <a href="https://github.com/mrghozzi" target="_blank" rel="noopener noreferrer" class="fw-bold" style="color: #3b82f6;">MrGhozzi</a>
                
                <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                    <span class="text-muted" style="font-size: 0.8rem;">{{ __('messages.version') }}</span>
                    <span class="badge fw-semibold" style="background: linear-gradient(135deg, #615dfa, #8b5cf6); color: #fff; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px;">v{{ $currentVersion }}</span>
                </div>
                
                <a href="{{ route('admin.updates') }}" class="btn btn-sm w-100 mt-3 py-2 mb-3" style="background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.15); border-radius: 10px;">
                    <i class="feather-refresh-cw me-1"></i> {{ __('messages.check_for_updates') ?? 'Check for Updates' }}
                </a>

                <!-- Community Links -->
                <div class="pt-3 border-top border-dashed d-flex flex-wrap justify-content-center gap-2">
                    <a href="https://github.com/sponsors/mrghozzi" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1 text-white fw-bold sd-sponsor-chip shadow-xs" style="background: linear-gradient(135deg, #ea4aaa, #db61a2); border: none; border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-heart me-1" style="font-size: 11px;"></i> {{ __('messages.sponsor') ?? 'Sponsor' }}
                    </a>
                    <a href="https://github.com/mrghozzi/myads" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1 text-dark fw-bold" style="background: rgba(245,158,11,0.12); color: #d97706; border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-star me-1" style="color: #f59e0b;"></i> ⭐ GitHub
                    </a>
                    <a href="https://github.com/mrghozzi/myads/discussions" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1" style="background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.15); border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-life-buoy me-1"></i> {{ __('about.get_support') ?? 'Support' }}
                    </a>
                    <a href="https://github.com/mrghozzi/myads/wiki" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1" style="background: rgba(14,165,233,0.08); color: #0284c7; border: 1px solid rgba(14,165,233,0.15); border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-book-open me-1"></i> Wiki
                    </a>
                    <a href="https://www.adstn.ovh/kb/myads" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1" style="background: rgba(16,185,129,0.08); color: #059669; border: 1px solid rgba(16,185,129,0.15); border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-globe me-1"></i> KB
                    </a>
                    <a href="https://github.com/mrghozzi/myads/issues" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1" style="background: rgba(239,68,68,0.08); color: #dc2626; border: 1px solid rgba(239,68,68,0.15); border-radius: 8px; font-size: 0.75rem;">
                        <i class="feather-alert-circle me-1"></i> {{ __('about.report_issue') ?? 'Report Issue' }}
                    </a>
                </div>
            </div>

            <!-- Support Project Block -->
            <div class="sd-card p-4 text-center">
                <div class="mb-2">
                    <span class="badge px-3 py-1.5" style="background: rgba(234, 74, 170, 0.12); color: #ea4aaa; border: 1px solid rgba(234, 74, 170, 0.28); border-radius: 20px; font-size: 0.72rem; font-weight: 700;">
                        <i class="feather-check-circle me-1"></i> {{ __('about.github_sponsors_approved_badge') ?? 'Officially Approved by GitHub' }}
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <h6 class="fw-bold text-dark mb-0">{{ __('messages.support_project') ?? 'Support Project' }}</h6>
                    <i class="feather-heart text-danger" style="font-size: 18px;"></i>
                </div>
                <p class="text-muted fs-12 mb-3">{{ __('about.sponsor_tiers_available') ?? 'Flexible one-time and monthly tiers starting from $1' }}</p>

                <!-- Primary GitHub Sponsors CTA -->
                <a href="https://github.com/sponsors/mrghozzi" target="_blank" rel="noopener noreferrer" class="btn w-100 py-2.5 mb-3 fw-bold text-white d-flex align-items-center justify-content-center gap-2 shadow-sm sd-github-sponsor-btn" style="background: linear-gradient(135deg, #ea4aaa 0%, #db61a2 100%); border: none; border-radius: 12px; font-size: 0.85rem; letter-spacing: 0.2px;">
                    <i class="feather-heart" style="font-size: 16px;"></i>
                    <span>{{ __('about.sponsor_on_github') ?? 'Sponsor on GitHub' }}</span>
                    <i class="feather-external-link opacity-75 ms-1" style="font-size: 12px;"></i>
                </a>

                <!-- Alternative Platforms -->
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap pt-2 border-top border-dashed">
                    <a href="https://www.patreon.com/MrGhozzi" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1 fw-semibold text-uppercase" style="background: rgba(30,41,59,0.06); color: #334155; border: 1px solid rgba(30,41,59,0.12); border-radius: 8px; font-size: 0.74rem;">
                        PATREON <i class="feather-heart ms-1" style="font-size: 11px;"></i>
                    </a>
                    <a href="https://ko-fi.com/mrghozzi" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1 fw-semibold text-uppercase" style="background: rgba(245,158,11,0.08); color: #d97706; border: 1px solid rgba(245,158,11,0.18); border-radius: 8px; font-size: 0.74rem;">
                        KO-FI <i class="feather-coffee ms-1" style="font-size: 11px;"></i>
                    </a>
                    <a href="https://www.ba9chich.com/en/mrghozzi" target="_blank" rel="noopener noreferrer" class="btn btn-sm px-2.5 py-1 fw-semibold text-uppercase" style="background: rgba(59,130,246,0.08); color: #2563eb; border: 1px solid rgba(59,130,246,0.18); border-radius: 8px; font-size: 0.74rem;">
                        BA9CHICH <i class="feather-gift ms-1" style="font-size: 11px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Dynamic Admin Tips Engine ──
    var adminTips = {!! json_encode($adminTips, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
    var currentTipId = {{ $currentTip['id'] ?? 1 }};
    var currentTipIdx = Math.max(0, adminTips.findIndex(function(t) { return t.id === currentTipId; }));
    var btnRotateTip = document.getElementById('btn-rotate-tip');

    function renderTip(index) {
        var tip = adminTips[index];
        if (!tip) return;

        var tipContainer = document.getElementById('admin-tip-container');
        if (tipContainer) {
            tipContainer.style.opacity = '0.4';
            setTimeout(function() {
                var badge = document.getElementById('tip-category-badge');
                var icon = document.getElementById('tip-category-icon');
                var catText = document.getElementById('tip-category-text');
                var title = document.getElementById('tip-title');
                var desc = document.getElementById('tip-desc');
                var actionLink = document.getElementById('tip-action-link');

                if (badge) badge.style.background = tip.badge_bg;
                if (icon) icon.className = tip.icon;
                if (catText) catText.textContent = tip.category;
                if (title) title.textContent = '💡 ' + tip.title;
                if (desc) desc.textContent = tip.tip;
                if (actionLink) {
                    actionLink.href = tip.action_url;
                    actionLink.innerHTML = tip.action_text + ' <i class="feather-arrow-left ms-1"></i>';
                }
                tipContainer.style.opacity = '1';
            }, 150);
        }
    }

    if (btnRotateTip && adminTips && adminTips.length > 0) {
        btnRotateTip.addEventListener('click', function() {
            currentTipIdx = (currentTipIdx + 1) % adminTips.length;
            renderTip(currentTipIdx);
        });
    }

    // ── Dark Mode Detection for Charts ──
    var isDark = document.documentElement.classList.contains('app-skin-dark');
    var textColor = isDark ? '#94a3b8' : '#64748b';
    var gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(0,0,0,0.06)';

    // ── Helper to render Ad Charts ──
    function renderAdCharts(chartData) {
        var distLoader = document.getElementById('ad-dist-loader');
        if (distLoader) distLoader.style.display = 'none';

        var distCtx = document.getElementById('adDistributionChart');
        if (distCtx && chartData && chartData.distribution) {
            try {
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.distribution.labels,
                        datasets: [{
                            data: chartData.distribution.data,
                            backgroundColor: [
                                'rgba(97, 93, 250, 0.85)',
                                'rgba(245, 158, 11, 0.85)',
                                'rgba(16, 185, 129, 0.85)',
                                'rgba(139, 92, 246, 0.85)',
                                'rgba(236, 72, 153, 0.85)',
                            ],
                            borderColor: [
                                '#615dfa',
                                '#f59e0b',
                                '#10b981',
                                '#8b5cf6',
                                '#ec4899',
                            ],
                            borderWidth: 2,
                            hoverOffset: 8,
                            spacing: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { size: 12, weight: '500' }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1e293b' : '#fff',
                                titleColor: isDark ? '#e2e8f0' : '#1e293b',
                                bodyColor: isDark ? '#94a3b8' : '#64748b',
                                borderColor: isDark ? '#334155' : '#e2e8f0',
                                borderWidth: 1,
                                cornerRadius: 10,
                                padding: 12,
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Error rendering distribution chart:', e);
            }
        }

        var engLoader = document.getElementById('ad-eng-loader');
        if (engLoader) engLoader.style.display = 'none';

        var engCtx = document.getElementById('engagementChart');
        if (engCtx && chartData && chartData.engagement) {
            try {
                new Chart(engCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.engagement.labels,
                        datasets: [{
                            label: '',
                            data: chartData.engagement.data,
                            backgroundColor: [
                                'rgba(97, 93, 250, 0.75)',
                                'rgba(139, 92, 246, 0.75)',
                                'rgba(245, 158, 11, 0.75)',
                                'rgba(16, 185, 129, 0.75)',
                                'rgba(236, 72, 153, 0.75)',
                                'rgba(35, 210, 226, 0.75)',
                                'rgba(244, 63, 94, 0.75)',
                            ],
                            borderColor: [
                                '#615dfa',
                                '#8b5cf6',
                                '#f59e0b',
                                '#10b981',
                                '#ec4899',
                                '#23d2e2',
                                '#f43f5e',
                            ],
                            borderWidth: 2,
                            borderRadius: 10,
                            borderSkipped: false,
                            barPercentage: 0.55,
                            categoryPercentage: 0.7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDark ? '#1e293b' : '#fff',
                                titleColor: isDark ? '#e2e8f0' : '#1e293b',
                                bodyColor: isDark ? '#94a3b8' : '#64748b',
                                borderColor: isDark ? '#334155' : '#e2e8f0',
                                borderWidth: 1,
                                cornerRadius: 10,
                                padding: 12,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor, font: { size: 11, weight: '500' } },
                                border: { display: false }
                            },
                            y: {
                                grid: { color: gridColor, drawBorder: false },
                                ticks: {
                                    color: textColor,
                                    font: { size: 11 },
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                        if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                        return value;
                                    }
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Error rendering engagement chart:', e);
            }
        }
    }

    // ── Helper to render Community Charts ──
    function renderCommunityCharts(communityChartData) {
        var postsLoader = document.getElementById('posts-community-loader');
        if (postsLoader) postsLoader.style.display = 'none';

        var postsCommunityCtx = document.getElementById('postsCommunityChart');
        if (postsCommunityCtx && communityChartData && communityChartData.labels) {
            try {
                new Chart(postsCommunityCtx, {
                    type: 'line',
                    data: {
                        labels: communityChartData.labels,
                        datasets: [
                            {
                                label: @json(__('messages.post_text') ?? 'Text Posts'),
                                data: communityChartData.posts.text,
                                borderColor: '#615dfa',
                                backgroundColor: 'rgba(97, 93, 250, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3
                            },
                            {
                                label: @json(__('messages.post_link') ?? 'Link Posts'),
                                data: communityChartData.posts.link,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3
                            },
                            {
                                label: @json(__('messages.post_gallery') ?? 'Gallery Posts'),
                                data: communityChartData.posts.gallery,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3
                            },
                            {
                                label: @json(__('messages.post_video') ?? 'Video Posts'),
                                data: communityChartData.posts.video,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3
                            },
                            {
                                label: @json(__('messages.post_clips') ?? 'Clips'),
                                data: communityChartData.posts.clips,
                                borderColor: '#ec4899',
                                backgroundColor: 'rgba(236, 72, 153, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 } } },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                            y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } }
                        }
                    }
                });
            } catch (e) {
                console.error('Error rendering community posts chart:', e);
            }
        }

        var engLoader = document.getElementById('eng-community-loader');
        if (engLoader) engLoader.style.display = 'none';

        var engCommunityCtx = document.getElementById('engagementCommunityChart');
        if (engCommunityCtx && communityChartData && communityChartData.labels) {
            try {
                new Chart(engCommunityCtx, {
                    type: 'line',
                    data: {
                        labels: communityChartData.labels,
                        datasets: [
                            {
                                label: @json(__('messages.forum_comments') ?? 'Forum Comments'),
                                data: communityChartData.comments.forum,
                                borderColor: '#10b981',
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                            {
                                label: @json(__('messages.store_comments') ?? 'Store Comments'),
                                data: communityChartData.comments.store,
                                borderColor: '#3b82f6',
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                            {
                                label: @json(__('messages.follows_count') ?? 'New Follows'),
                                data: communityChartData.reactions.follows,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 } } },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                            y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } }
                        }
                    }
                });
            } catch (e) {
                console.error('Error rendering community engagement chart:', e);
            }
        }
    }

    // ── SEQUENTIAL AJAX LOADER (توالياً) ──
    async function loadDashboardSequentially() {
        // Step 1: Top KPIs & Hero Counters
        try {
            var kpisContainer = document.getElementById('dashboard-kpis-container');
            if (kpisContainer) {
                var res = await fetch(kpisContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var html = await res.text();
                    kpisContainer.innerHTML = html;
                    var kpisRow = document.getElementById('kpis-row');
                    if (kpisRow) {
                        var u = document.getElementById('hero-stat-users');
                        var p = document.getElementById('hero-stat-posts');
                        var o = document.getElementById('hero-stat-online');
                        if (u) u.textContent = kpisRow.getAttribute('data-users') || '0';
                        if (p) p.textContent = kpisRow.getAttribute('data-posts') || '0';
                        if (o) o.textContent = kpisRow.getAttribute('data-online') || '0';
                    }
                }
            }
        } catch (e) {
            console.error('Error loading KPIs:', e);
        }

        // Step 2: Reactions Counters Strip
        try {
            var reactionsContainer = document.getElementById('dashboard-reactions-container');
            if (reactionsContainer) {
                var res = await fetch(reactionsContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var html = await res.text();
                    reactionsContainer.innerHTML = html;
                    reactionsContainer.classList.add('sd-fade-in');
                }
            }
        } catch (e) {
            console.error('Error loading Reactions:', e);
        }

        // Step 3: Activity & Breakdown
        try {
            var activityContainer = document.getElementById('dashboard-activity-container');
            if (activityContainer) {
                var res = await fetch(activityContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var html = await res.text();
                    activityContainer.innerHTML = html;
                    activityContainer.classList.add('sd-fade-in');

                    var card = document.getElementById('dashboard-activity-card');
                    if (card) {
                        var reportsBadge = document.getElementById('quick-action-reports-badge');
                        if (reportsBadge) reportsBadge.textContent = card.getAttribute('data-pending-reports') || '0';
                    }
                }
            }
        } catch (e) {
            console.error('Error loading Activity:', e);
        }

        // Step 4: Ad Analytics Charts
        try {
            var adChartsContainer = document.getElementById('dashboard-ad-charts-container');
            if (adChartsContainer) {
                var res = await fetch(adChartsContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var adData = await res.json();
                    renderAdCharts(adData);
                }
            }
        } catch (e) {
            console.error('Error loading Ad Charts:', e);
        }

        // Step 5: Community Trend Charts (30 days)
        try {
            var commChartsContainer = document.getElementById('dashboard-community-charts-container');
            if (commChartsContainer) {
                var res = await fetch(commChartsContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var commData = await res.json();
                    renderCommunityCharts(commData);
                }
            }
        } catch (e) {
            console.error('Error loading Community Charts:', e);
        }

        // Step 6: Marketplace Recommendations
        try {
            var marketContainer = document.getElementById('marketplace-recommendations-container');
            if (marketContainer) {
                var res = await fetch(marketContainer.getAttribute('data-url'), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                if (res.ok) {
                    var html = await res.text();
                    marketContainer.innerHTML = html;
                } else {
                    marketContainer.innerHTML = '';
                }
            }
        } catch (e) {
            var marketContainer = document.getElementById('marketplace-recommendations-container');
            if (marketContainer) marketContainer.innerHTML = '';
            console.error('Error loading Marketplace Recommendations:', e);
        }

        // Step 7: Background Version Check Alert
        try {
            var updateContainer = document.getElementById('admin-update-alert-container');
            if (updateContainer) {
                var res = await fetch(updateContainer.getAttribute('data-url'), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    var verData = await res.json();
                    if (verData && verData.has_update) {
                        updateContainer.innerHTML = `
                        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 border-0 shadow-sm sd-fade-in" role="alert" style="border-radius: 14px; background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);">
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center;">
                                    <i class="feather-zap text-white" style="font-size: 22px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">${ @json(__('messages.new_version_available') ?? 'New Version Available!') } — v${verData.latest_version}</h6>
                                    <p class="mb-0 small" style="color: #92400e;">${ @json(__('messages.update_available_desc') ?? 'A new version is available for download.') }</p>
                                </div>
                            </div>
                            <a href="${verData.updates_url}" class="btn btn-sm fw-bold px-3 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; border-radius: 8px;">
                                <i class="feather-download-cloud me-1"></i> ${ @json(__('messages.update_now') ?? 'Update Now') }
                            </a>
                        </div>`;
                    }
                }
            }
        } catch (e) {
            console.error('Error checking Version Update:', e);
        }
    }

    // Launch sequential loading
    loadDashboardSequentially();
});
</script>
@endpush
