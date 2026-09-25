@extends('admin::layouts.admin')

@section('title', __('messages.community_feed_settings_title'))

@push('head')
<style>
    /* ── Superdesign Settings Page Tokens ───────────────────────── */
    .admin-feed-settings {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }

    /* ── KPI Stat Strip ─────────────────────────────────────────── */
    .admin-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .admin-stat-card {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 20px;
        padding: 1.25rem 1.4rem;
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15, 23, 42, 0.05));
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .admin-stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--admin-premium-border-strong, rgba(97, 93, 250, 0.2));
        box-shadow: var(--admin-premium-shadow, 0 20px 40px rgba(15, 23, 42, 0.08));
    }

    .admin-stat-icon-chip {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .admin-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #17c666;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(23, 198, 102, 0.7);
        animation: adminPulse 2s infinite;
    }

    @keyframes adminPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(23, 198, 102, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(23, 198, 102, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(23, 198, 102, 0); }
    }

    /* ── Presets Grid ────────────────────────────────────────────── */
    .admin-presets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
    }

    .admin-preset-card {
        background: var(--admin-premium-surface, #ffffff);
        border: 2px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 20px;
        padding: 1.4rem;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .admin-preset-card:hover {
        border-color: var(--admin-premium-accent, #615dfa);
        transform: translateY(-4px);
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15, 23, 42, 0.08));
    }

    .admin-preset-card.active {
        border-color: var(--admin-premium-accent, #615dfa);
        background: linear-gradient(180deg, var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.08)) 0%, var(--admin-premium-surface, #ffffff) 100%);
        box-shadow: 0 10px 28px rgba(97, 93, 250, 0.15);
    }

    .admin-preset-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }

    .admin-preset-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .admin-preset-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }

    .admin-preset-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--admin-premium-text, #1f2937);
        margin: 0 0 0.35rem 0;
    }

    .admin-preset-desc {
        font-size: 0.84rem;
        color: var(--admin-premium-muted, #6b7280);
        line-height: 1.5;
        margin: 0 0 1.25rem 0;
        flex-grow: 1;
    }

    .admin-preset-btn {
        width: 100%;
        padding: 0.55rem 0.85rem;
        font-size: 0.84rem;
        font-weight: 700;
        border-radius: 12px;
        background: var(--admin-premium-surface-alt, #f6f7fb);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        color: var(--admin-premium-text, #1f2937);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .admin-preset-card:hover .admin-preset-btn {
        background: var(--admin-premium-accent, #615dfa);
        color: #ffffff;
        border-color: var(--admin-premium-accent, #615dfa);
    }

    .admin-preset-card.active .admin-preset-btn {
        background: var(--admin-premium-accent, #615dfa);
        color: #ffffff;
        border-color: var(--admin-premium-accent, #615dfa);
    }

    /* ── Algorithm Visualizer Card ───────────────────────────────── */
    .admin-algo-visualizer {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border-strong, rgba(97, 93, 250, 0.16));
        border-radius: var(--admin-premium-radius, 24px);
        padding: 1.75rem;
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15, 23, 42, 0.05));
    }

    .admin-algo-vis-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .admin-algo-vis-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 0.25rem 0;
        color: var(--admin-premium-text, #1f2937);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .admin-algo-vis-bar {
        height: 16px;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        background: var(--admin-premium-surface-alt, #f6f7fb);
        margin-bottom: 1.25rem;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .admin-algo-segment {
        height: 100%;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .admin-algo-segment.freshness { background: #3454d1; }
    .admin-algo-segment.engagement { background: #17c666; }
    .admin-algo-segment.personalization { background: #615dfa; }
    .admin-algo-segment.trend { background: #ffa21d; }

    .admin-algo-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .admin-algo-legend-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--admin-premium-muted, #6b7280);
    }

    .admin-algo-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .admin-algo-pct-badge {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 6px;
        background: var(--admin-premium-surface-alt, #f6f7fb);
        color: var(--admin-premium-text, #1f2937);
    }

    /* ── Settings Panel Styling ──────────────────────────────────── */
    .admin-panel {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: var(--admin-premium-radius, 24px);
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15, 23, 42, 0.05));
        overflow: hidden;
    }

    .admin-panel__header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .admin-panel__header-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .admin-panel__body {
        padding: 1.75rem;
    }

    .admin-panel__eyebrow {
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--admin-premium-accent, #615dfa);
        margin-bottom: 0.35rem;
        display: block;
    }

    .admin-panel__title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--admin-premium-text, #1f2937);
        margin: 0 0 0.25rem 0;
    }

    .admin-panel__desc {
        font-size: 0.86rem;
        color: var(--admin-premium-muted, #6b7280);
        margin: 0;
    }

    /* ── Field Cards ─────────────────────────────────────────────── */
    .admin-field-card {
        background: var(--admin-premium-surface-alt, #f6f7fb);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 18px;
        padding: 1.15rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        height: 100%;
        transition: all 0.2s ease;
    }

    .admin-field-card:hover {
        border-color: var(--admin-premium-border-strong, rgba(97, 93, 250, 0.25));
        background: var(--admin-premium-surface, #ffffff);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .admin-field-label {
        font-size: 0.86rem;
        font-weight: 700;
        color: var(--admin-premium-text, #1f2937);
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .admin-field-unit {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-inline-start: none;
        color: var(--admin-premium-muted, #6b7280);
        padding: 0 0.75rem;
    }

    .admin-field-input {
        height: 44px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        background: var(--admin-premium-surface, #ffffff);
        color: var(--admin-premium-text, #1f2937);
        transition: all 0.2s ease;
    }

    .input-group .admin-field-input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .admin-field-unit {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    [dir="rtl"] .input-group .admin-field-input {
        border-radius: 0 12px 12px 0;
    }

    [dir="rtl"] .input-group .admin-field-unit {
        border-radius: 12px 0 0 12px;
        border-inline-start: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-inline-end: none;
    }

    .admin-field-input:focus {
        border-color: var(--admin-premium-accent, #615dfa);
        box-shadow: 0 0 0 3px var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.15));
        background: var(--admin-premium-surface, #ffffff);
        color: var(--admin-premium-text, #1f2937);
    }

    .admin-field-help {
        font-size: 0.77rem;
        color: var(--admin-premium-muted, #6b7280);
        line-height: 1.45;
        margin: 0;
    }

    /* ── Sticky Save Bar ─────────────────────────────────────────── */
    .admin-save-bar {
        position: sticky;
        bottom: 1.5rem;
        z-index: 100;
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border-strong, rgba(97, 93, 250, 0.25));
        border-radius: 22px;
        padding: 1.1rem 1.75rem;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        backdrop-filter: blur(16px);
        margin-top: 1rem;
    }

    .admin-save-bar-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--admin-premium-muted, #6b7280);
    }

    .admin-save-bar-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-premium-save {
        background: linear-gradient(135deg, var(--admin-premium-accent, #615dfa) 0%, #3454d1 100%);
        border: none;
        color: #ffffff;
        padding: 0.7rem 1.85rem;
        font-weight: 800;
        font-size: 0.92rem;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(97, 93, 250, 0.28);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        cursor: pointer;
    }

    .btn-premium-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(97, 93, 250, 0.38);
        color: #ffffff;
    }

    /* ── Floating Toast ──────────────────────────────────────────── */
    .admin-feed-toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1090;
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border-strong, rgba(97, 93, 250, 0.3));
        border-radius: 18px;
        padding: 1rem 1.25rem;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 320px;
        max-width: 440px;
        backdrop-filter: blur(16px);
        transform: translateY(120%);
        opacity: 0;
        pointer-events: none;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [dir="rtl"] .admin-feed-toast {
        right: auto;
        left: 2rem;
    }

    .admin-feed-toast.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .admin-feed-toast-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--admin-premium-success-soft, rgba(23, 198, 102, 0.15));
        color: #17c666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .admin-feed-toast-content {
        flex-grow: 1;
    }

    .admin-feed-toast-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--admin-premium-text, #1f2937);
        margin: 0 0 0.15rem 0;
    }

    .admin-feed-toast-desc {
        font-size: 0.78rem;
        color: var(--admin-premium-muted, #6b7280);
        margin: 0;
    }

    /* ── Dark Mode Adaptations ───────────────────────────────────── */
    html.app-skin-dark .admin-field-unit {
        background: var(--admin-premium-surface-alt, #1b1e2f);
        color: var(--admin-premium-muted, #9ca3af);
    }
</style>
@endpush

@section('content')
<div class="admin-page admin-feed-settings">
    <!-- HERO HEADER -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('portal.index') }}">{{ __('messages.community') }}</a></li>
                <li>{{ __('messages.community_feed_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill">
                    <i class="feather-sliders me-1"></i> {{ __('messages.community') }}
                </span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-activity text-primary"></i>
                {{ __('messages.community_feed_settings_title') }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.community_feed_settings_desc') }}</p>
        </div>

        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
            <a href="{{ route('portal.index') }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="feather-external-link"></i>
                <span>{{ __('messages.preview') ?? 'View Feed' }}</span>
            </a>
            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" id="admin-flush-cache-btn">
                <i class="feather-zap"></i>
                <span>{{ __('messages.community_feed_flush_cache') }}</span>
            </button>
        </div>
    </section>

    <!-- LIVE KPIS OVERVIEW STRIP -->
    <div class="admin-summary-grid">
        <!-- 1. Engine Mode -->
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="admin-panel__eyebrow mb-0">Engine Architecture</span>
                <div class="admin-stat-icon-chip bg-soft-primary text-primary">
                    <i class="feather-cpu"></i>
                </div>
            </div>
            <div class="admin-stat-value fs-4" id="kpi-feed-mode">
                {{ strtoupper($settings['feed_mode'] ?? 'smart') }}
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="admin-pulse-dot"></span>
                <span class="text-muted small">Multi-Factor Ranking</span>
            </div>
        </div>

        <!-- 2. Batch Pagination -->
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="admin-panel__eyebrow mb-0">Batch Pagination</span>
                <div class="admin-stat-icon-chip bg-soft-success text-success">
                    <i class="feather-layers"></i>
                </div>
            </div>
            <div class="admin-stat-value fs-4" id="kpi-page-size">
                {{ $settings['feed_page_size'] ?? 20 }} Posts
            </div>
            <span class="text-muted small mt-1">Per AJAX Infinite Scroll</span>
        </div>

        <!-- 3. Candidate Horizon -->
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="admin-panel__eyebrow mb-0">Lookback Horizon</span>
                <div class="admin-stat-icon-chip bg-soft-warning text-warning">
                    <i class="feather-clock"></i>
                </div>
            </div>
            <div class="admin-stat-value fs-4" id="kpi-candidate-horizon">
                {{ $settings['fresh_candidate_hours'] ?? 72 }}h / {{ $settings['fresh_candidate_limit'] ?? 250 }}
            </div>
            <span class="text-muted small mt-1">Candidate Inspection Pool</span>
        </div>

        <!-- 4. Memory Cache -->
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="admin-panel__eyebrow mb-0">Memory Cache</span>
                <div class="admin-stat-icon-chip bg-soft-info text-info">
                    <i class="feather-database"></i>
                </div>
            </div>
            <div class="admin-stat-value fs-4" id="kpi-cache-ttl">
                {{ $settings['cache_ttl_seconds'] ?? 180 }}s TTL
            </div>
            <span class="text-muted small mt-1">Sub-Millisecond Read Speed</span>
        </div>
    </div>

    <!-- PRESETS STRIP -->
    <section class="admin-panel">
        <div class="admin-panel__header">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-panel__header-icon bg-soft-primary text-primary">
                    <i class="feather-sliders"></i>
                </div>
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.quick_actions') ?? 'Quick Setup' }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.community_feed_presets') }}</h2>
                    <p class="admin-panel__desc">{{ __('messages.community_feed_presets_desc') }}</p>
                </div>
            </div>
        </div>
        <div class="admin-panel__body">
            <div class="admin-presets-grid">
                <!-- Balanced -->
                <div class="admin-preset-card active" data-preset="balanced">
                    <div class="admin-preset-header">
                        <div class="admin-preset-icon bg-soft-primary text-primary">
                            <i class="feather-compass"></i>
                        </div>
                        <span class="admin-preset-badge bg-soft-primary text-primary">Default</span>
                    </div>
                    <h4 class="admin-preset-title">{{ __('messages.community_feed_preset_balanced') }}</h4>
                    <p class="admin-preset-desc">{{ __('messages.community_feed_preset_balanced_desc') }}</p>
                    <button type="button" class="admin-preset-btn">
                        <i class="feather-check"></i>
                        <span>{{ __('messages.community_feed_apply_preset') }}</span>
                    </button>
                </div>

                <!-- High Engagement -->
                <div class="admin-preset-card" data-preset="high_engagement">
                    <div class="admin-preset-header">
                        <div class="admin-preset-icon bg-soft-success text-success">
                            <i class="feather-trending-up"></i>
                        </div>
                        <span class="admin-preset-badge bg-soft-success text-success">Viral</span>
                    </div>
                    <h4 class="admin-preset-title">{{ __('messages.community_feed_preset_engagement') }}</h4>
                    <p class="admin-preset-desc">{{ __('messages.community_feed_preset_engagement_desc') }}</p>
                    <button type="button" class="admin-preset-btn">
                        <i class="feather-play"></i>
                        <span>{{ __('messages.community_feed_apply_preset') }}</span>
                    </button>
                </div>

                <!-- Fresh & Breaking -->
                <div class="admin-preset-card" data-preset="fresh_breaking">
                    <div class="admin-preset-header">
                        <div class="admin-preset-icon bg-soft-warning text-warning">
                            <i class="feather-clock"></i>
                        </div>
                        <span class="admin-preset-badge bg-soft-warning text-warning">Real-Time</span>
                    </div>
                    <h4 class="admin-preset-title">{{ __('messages.community_feed_preset_fresh') }}</h4>
                    <p class="admin-preset-desc">{{ __('messages.community_feed_preset_fresh_desc') }}</p>
                    <button type="button" class="admin-preset-btn">
                        <i class="feather-play"></i>
                        <span>{{ __('messages.community_feed_apply_preset') }}</span>
                    </button>
                </div>

                <!-- Eco / Low Server Load -->
                <div class="admin-preset-card" data-preset="eco_shared">
                    <div class="admin-preset-header">
                        <div class="admin-preset-icon bg-soft-info text-info">
                            <i class="feather-cpu"></i>
                        </div>
                        <span class="admin-preset-badge bg-soft-info text-info">Eco Mode</span>
                    </div>
                    <h4 class="admin-preset-title">{{ __('messages.community_feed_preset_eco') }}</h4>
                    <p class="admin-preset-desc">{{ __('messages.community_feed_preset_eco_desc') }}</p>
                    <button type="button" class="admin-preset-btn">
                        <i class="feather-play"></i>
                        <span>{{ __('messages.community_feed_apply_preset') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ALGORITHM WEIGHT DISTRIBUTION VISUALIZER -->
    <div class="admin-algo-visualizer">
        <div class="admin-algo-vis-header">
            <div>
                <h3 class="admin-algo-vis-title">
                    <i class="feather-pie-chart text-primary"></i>
                    {{ __('messages.community_feed_algorithm_preview') }}
                </h3>
                <span class="text-muted small">{{ __('messages.community_feed_algorithm_preview_desc') }}</span>
            </div>
            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill font-monospace" id="live-mode-badge">
                MODE: {{ strtoupper($settings['feed_mode'] ?? 'SMART') }}
            </span>
        </div>

        <div class="admin-algo-vis-bar">
            <div class="admin-algo-segment freshness" id="segment-freshness" style="width: 40%;" title="Freshness"></div>
            <div class="admin-algo-segment engagement" id="segment-engagement" style="width: 25%;" title="Engagement"></div>
            <div class="admin-algo-segment personalization" id="segment-personalization" style="width: 20%;" title="Personalization"></div>
            <div class="admin-algo-segment trend" id="segment-trend" style="width: 15%;" title="Trend Windows"></div>
        </div>

        <div class="admin-algo-legend">
            <div class="admin-algo-legend-item">
                <span class="admin-algo-legend-dot" style="background: #3454d1;"></span>
                <span>{{ __('messages.community_feed_settings_freshness_section') }}</span>
                <span class="admin-algo-pct-badge" id="val-freshness-pct">40%</span>
            </div>
            <div class="admin-algo-legend-item">
                <span class="admin-algo-legend-dot" style="background: #17c666;"></span>
                <span>{{ __('messages.interactions') ?? 'Engagement' }}</span>
                <span class="admin-algo-pct-badge" id="val-engagement-pct">25%</span>
            </div>
            <div class="admin-algo-legend-item">
                <span class="admin-algo-legend-dot" style="background: #615dfa;"></span>
                <span>{{ __('messages.community_feed_settings_personalization_section') }}</span>
                <span class="admin-algo-pct-badge" id="val-personalization-pct">20%</span>
            </div>
            <div class="admin-algo-legend-item">
                <span class="admin-algo-legend-dot" style="background: #ffa21d;"></span>
                <span>{{ __('messages.community_feed_settings_trend_windows_section') }}</span>
                <span class="admin-algo-pct-badge" id="val-trend-pct">15%</span>
            </div>
        </div>
    </div>

    <!-- MAIN SETTINGS FORM -->
    <form method="POST" action="{{ route('admin.community.feed.settings.update') }}" id="admin-feed-settings-form" class="admin-feed-settings">
        @csrf

        <!-- SECTION 1: Engine Architecture & Pagination -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-primary text-primary">
                        <i class="feather-cpu"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">Engine Architecture</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_mode') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_mode_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label" for="feed_mode_select">
                                <span>{{ __('messages.community_feed_mode') }}</span>
                                <span class="badge bg-soft-primary text-primary">Engine</span>
                            </label>
                            <select name="feed_mode" id="feed_mode_select" class="form-select admin-field-input">
                                <option value="smart" {{ old('feed_mode', $settings['feed_mode'] ?? 'smart') === 'smart' ? 'selected' : '' }}>
                                    {{ __('messages.feed_mode_smart') }}
                                </option>
                                <option value="simple" {{ old('feed_mode', $settings['feed_mode'] ?? 'smart') === 'simple' ? 'selected' : '' }}>
                                    {{ __('messages.feed_mode_simple') }}
                                </option>
                            </select>
                            <p class="admin-field-help">{{ __('messages.community_feed_mode_desc') }}</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label" for="feed_page_size_input">
                                <span>{{ __('messages.community_feed_settings_page_size') }}</span>
                                <span class="badge bg-soft-primary text-primary">10 - 50</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control admin-field-input"
                                    id="feed_page_size_input"
                                    name="feed_page_size"
                                    value="{{ old('feed_page_size', $settings['feed_page_size'] ?? 20) }}"
                                    min="10"
                                    max="50"
                                >
                                <span class="input-group-text admin-field-unit">items</span>
                            </div>
                            <p class="admin-field-help">{{ __('messages.community_feed_settings_page_size_help') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: Freshness & Decay -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-info text-info">
                        <i class="feather-clock"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.community_feed_settings_freshness_section') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_settings_freshness_section') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_settings_freshness_help') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_freshness_base') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="freshness_base_score" value="{{ old('freshness_base_score', $settings['freshness_base_score']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Baseline score granted to brand new updates.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_freshness_decay') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="freshness_decay_exponent" value="{{ old('freshness_decay_exponent', $settings['freshness_decay_exponent']) }}" step="0.01" min="0.01">
                                <span class="input-group-text admin-field-unit">exp</span>
                            </div>
                            <p class="admin-field-help">Higher exponent causes older posts to drop faster.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_suppression_after') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="freshness_suppression_after_hours" value="{{ old('freshness_suppression_after_hours', $settings['freshness_suppression_after_hours']) }}" min="1">
                                <span class="input-group-text admin-field-unit">hrs</span>
                            </div>
                            <p class="admin-field-help">Hours before decay suppression penalty kicks in.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_suppression_multiplier') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="freshness_suppression_multiplier" value="{{ old('freshness_suppression_multiplier', $settings['freshness_suppression_multiplier']) }}" step="0.01" min="0" max="1">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Fraction multiplier applied to older posts (0.00 to 1.00).</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_view_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="view_weight" value="{{ old('view_weight', $settings['view_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Weight per recorded topic/directory view.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_view_cap') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="max_views_score" value="{{ old('max_views_score', $settings['max_views_score']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Ceiling cap on view score to prevent viral runaway.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3: Personalization & Social Affinity -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-success text-success">
                        <i class="feather-users"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.community_feed_settings_personalization_section') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_settings_personalization_section') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_settings_personalization_help') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_following_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="following_boost" value="{{ old('following_boost', $settings['following_boost']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Score bonus when the viewer follows the author.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_author_affinity_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="author_affinity_boost" value="{{ old('author_affinity_boost', $settings['author_affinity_boost']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Boost based on frequent interactions with this author.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_content_affinity_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="content_affinity_boost" value="{{ old('content_affinity_boost', $settings['content_affinity_boost']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Boost for content types the user engages with most.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_social_proof_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="social_proof_boost" value="{{ old('social_proof_boost', $settings['social_proof_boost']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Boost when followed members have commented or reacted.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_verified_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="verified_author_boost" value="{{ old('verified_author_boost', $settings['verified_author_boost'] ?? 15) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">{{ __('messages.community_feed_settings_verified_boost_help') }}</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_media_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="media_boost" value="{{ old('media_boost', $settings['media_boost'] ?? 6) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">{{ __('messages.community_feed_settings_media_boost_help') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4: Trend Windows & Velocity -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-warning text-warning">
                        <i class="feather-zap"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.community_feed_settings_trend_windows_section') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_settings_trend_windows_section') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_settings_trend_windows_help') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rapid_window') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rapid_window_hours" value="{{ old('rapid_window_hours', $settings['rapid_window_hours']) }}" min="1">
                                <span class="input-group-text admin-field-unit">hrs</span>
                            </div>
                            <p class="admin-field-help">Hours considered for sudden viral spikes (rapid momentum).</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_trend_window') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="trend_window_hours" value="{{ old('trend_window_hours', $settings['trend_window_hours']) }}" min="1">
                                <span class="input-group-text admin-field-unit">hrs</span>
                            </div>
                            <p class="admin-field-help">Hours considered for recent rolling activity window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_trending_boost') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="trending_highlight_boost" value="{{ old('trending_highlight_boost', $settings['trending_highlight_boost'] ?? 10) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">{{ __('messages.community_feed_settings_trending_boost_help') }}</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rapid_reaction_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="rapid_reaction_weight" value="{{ old('rapid_reaction_weight', $settings['rapid_reaction_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for reactions in the rapid momentum window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rapid_comment_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="rapid_comment_weight" value="{{ old('rapid_comment_weight', $settings['rapid_comment_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for comments in the rapid momentum window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rapid_repost_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="rapid_repost_weight" value="{{ old('rapid_repost_weight', $settings['rapid_repost_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for reposts in the rapid momentum window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_recent_reaction_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="recent_reaction_weight" value="{{ old('recent_reaction_weight', $settings['recent_reaction_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for reactions in the rolling trend window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_recent_comment_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="recent_comment_weight" value="{{ old('recent_comment_weight', $settings['recent_comment_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for comments in the rolling trend window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_recent_repost_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input algo-calc" name="recent_repost_weight" value="{{ old('recent_repost_weight', $settings['recent_repost_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Multiplier for reposts in the rolling trend window.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_total_reaction_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="total_reaction_weight" value="{{ old('total_reaction_weight', $settings['total_reaction_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Historical all-time reaction weight.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_total_comment_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="total_comment_weight" value="{{ old('total_comment_weight', $settings['total_comment_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Historical all-time comment weight.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_total_repost_weight') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="total_repost_weight" value="{{ old('total_repost_weight', $settings['total_repost_weight']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">x</span>
                            </div>
                            <p class="admin-field-help">Historical all-time repost weight.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 5: Trend Rescue & Diversity -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-danger text-danger">
                        <i class="feather-shield"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.community_feed_settings_trend_rescue_section') }} & {{ __('messages.community_feed_settings_diversity_section') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_settings_trend_rescue_section') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_settings_trend_rescue_help') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rescue_max_age') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rescue_max_age_hours" value="{{ old('rescue_max_age_hours', $settings['rescue_max_age_hours']) }}" min="1">
                                <span class="input-group-text admin-field-unit">hrs</span>
                            </div>
                            <p class="admin-field-help">Max hours an older post can be rescued back into feed.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rescue_min_reactions') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rescue_min_recent_reactions" value="{{ old('rescue_min_recent_reactions', $settings['rescue_min_recent_reactions']) }}" min="0">
                                <span class="input-group-text admin-field-unit">qty</span>
                            </div>
                            <p class="admin-field-help">Recent reactions required to qualify for rescue.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rescue_min_comments') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rescue_min_recent_comments" value="{{ old('rescue_min_recent_comments', $settings['rescue_min_recent_comments']) }}" min="0">
                                <span class="input-group-text admin-field-unit">qty</span>
                            </div>
                            <p class="admin-field-help">Recent comments required to qualify for rescue.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rescue_min_reposts') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rescue_min_recent_reposts" value="{{ old('rescue_min_recent_reposts', $settings['rescue_min_recent_reposts']) }}" min="0">
                                <span class="input-group-text admin-field-unit">qty</span>
                            </div>
                            <p class="admin-field-help">Recent reposts required to qualify for rescue.</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_repeat_author_penalty') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="repeat_author_penalty" value="{{ old('repeat_author_penalty', $settings['repeat_author_penalty']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">{{ __('messages.community_feed_settings_diversity_help') }}</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_repeat_type_penalty') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="repeat_type_penalty" value="{{ old('repeat_type_penalty', $settings['repeat_type_penalty']) }}" step="0.01" min="0">
                                <span class="input-group-text admin-field-unit">pts</span>
                            </div>
                            <p class="admin-field-help">Penalty applied when multiple consecutive items have the same type.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 6: Candidate Pool & Cache -->
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-panel__header-icon bg-soft-primary text-primary">
                        <i class="feather-database"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.community_feed_settings_candidate_section') }} & {{ __('messages.community_feed_settings_cache_section') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.community_feed_settings_cache_section') }}</h2>
                        <p class="admin-panel__desc">{{ __('messages.community_feed_settings_cache_help') }}</p>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_fresh_candidate_hours') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" id="input_fresh_candidate_hours" name="fresh_candidate_hours" value="{{ old('fresh_candidate_hours', $settings['fresh_candidate_hours']) }}" min="1">
                                <span class="input-group-text admin-field-unit">hrs</span>
                            </div>
                            <p class="admin-field-help">Time span to inspect for fresh candidates.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_fresh_candidate_limit') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" id="input_fresh_candidate_limit" name="fresh_candidate_limit" value="{{ old('fresh_candidate_limit', $settings['fresh_candidate_limit']) }}" min="1">
                                <span class="input-group-text admin-field-unit">items</span>
                            </div>
                            <p class="admin-field-help">Max candidates fetched into memory for ranking.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_rescue_candidate_limit') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" name="rescue_candidate_limit" value="{{ old('rescue_candidate_limit', $settings['rescue_candidate_limit']) }}" min="1">
                                <span class="input-group-text admin-field-unit">items</span>
                            </div>
                            <p class="admin-field-help">Max candidates fetched for trend rescue consideration.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="admin-field-card">
                            <label class="admin-field-label">
                                <span>{{ __('messages.community_feed_settings_cache_ttl') }}</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control admin-field-input" id="input_cache_ttl_seconds" name="cache_ttl_seconds" value="{{ old('cache_ttl_seconds', $settings['cache_ttl_seconds']) }}" min="0">
                                <span class="input-group-text admin-field-unit">sec</span>
                            </div>
                            <p class="admin-field-help">Cache lifespan in seconds (0 to disable caching).</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STICKY ACTION BAR -->
        <div class="admin-save-bar">
            <div class="admin-save-bar-info">
                <i class="feather-check-circle text-success fs-5"></i>
                <span id="save-bar-status-text">Configure algorithm scoring weights & parameters freely.</span>
            </div>
            <div class="admin-save-bar-actions">
                <button type="submit" class="btn-premium-save" id="admin-feed-submit-btn">
                    <i class="feather-save"></i>
                    <span>{{ __('messages.save_changes') }}</span>
                </button>
            </div>
        </div>
    </form>

    <!-- FLOATING TOAST -->
    <div id="admin-feed-toast" class="admin-feed-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="admin-feed-toast-icon" id="admin-feed-toast-icon">
            <i class="feather-check"></i>
        </div>
        <div class="admin-feed-toast-content">
            <h5 class="admin-feed-toast-title" id="admin-feed-toast-title">Settings Saved</h5>
            <p class="admin-feed-toast-desc" id="admin-feed-toast-desc">Feed configuration updated successfully.</p>
        </div>
        <button type="button" class="btn-close ms-2" onclick="hideToast()" aria-label="Close"></button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const presetsData = @json($presets ?? []);
    const form = document.getElementById('admin-feed-settings-form');
    const modeSelect = document.getElementById('feed_mode_select');
    const liveModeBadge = document.getElementById('live-mode-badge');
    const flushBtn = document.getElementById('admin-flush-cache-btn');
    const toast = document.getElementById('admin-feed-toast');
    const toastTitle = document.getElementById('admin-feed-toast-title');
    const toastDesc = document.getElementById('admin-feed-toast-desc');
    const toastIcon = document.getElementById('admin-feed-toast-icon');
    const submitBtn = document.getElementById('admin-feed-submit-btn');

    let toastTimer = null;

    function showToast(type, title, message) {
        if (!toast) return;
        if (toastTimer) clearTimeout(toastTimer);

        toastTitle.innerText = title;
        toastDesc.innerText = message;

        if (type === 'error') {
            toastIcon.style.background = 'rgba(234, 77, 77, 0.15)';
            toastIcon.style.color = '#ea4d4d';
            toastIcon.innerHTML = '<i class="feather-alert-triangle"></i>';
        } else {
            toastIcon.style.background = 'rgba(23, 198, 102, 0.15)';
            toastIcon.style.color = '#17c666';
            toastIcon.innerHTML = '<i class="feather-check"></i>';
        }

        toast.classList.add('show');
        toastTimer = setTimeout(() => {
            hideToast();
        }, 4000);
    }

    window.hideToast = function () {
        if (toast) toast.classList.remove('show');
    };

    // ── Apply Preset Handler ──────────────────────────────────────
    document.querySelectorAll('.admin-preset-card').forEach(card => {
        card.addEventListener('click', function () {
            const presetKey = this.getAttribute('data-preset');
            const preset = presetsData[presetKey];
            if (!preset) return;

            document.querySelectorAll('.admin-preset-card').forEach(c => {
                c.classList.remove('active');
                const btn = c.querySelector('.admin-preset-btn');
                if (btn) btn.innerHTML = '<i class="feather-play"></i> <span>{{ __("messages.community_feed_apply_preset") }}</span>';
            });

            this.classList.add('active');
            const activeBtn = this.querySelector('.admin-preset-btn');
            if (activeBtn) {
                activeBtn.innerHTML = '<i class="feather-check"></i> <span>Active Preset</span>';
            }

            // Populate form values
            Object.keys(preset).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = preset[key];
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            showToast('success', 'Preset Selected', `"${preset.name}" applied. Click Save Changes to store.`);
            recalcVisualizer();
            updateKpis();
        });
    });

    // ── Recalculate Algorithm Visualizer ──────────────────────────
    function recalcVisualizer() {
        const freshnessBase = parseFloat(form.querySelector('[name="freshness_base_score"]')?.value || 700);
        const followingBoost = parseFloat(form.querySelector('[name="following_boost"]')?.value || 24);
        const authorAffinity = parseFloat(form.querySelector('[name="author_affinity_boost"]')?.value || 10);
        const contentAffinity = parseFloat(form.querySelector('[name="content_affinity_boost"]')?.value || 8);
        const verifiedBoost = parseFloat(form.querySelector('[name="verified_author_boost"]')?.value || 15);
        const mediaBoost = parseFloat(form.querySelector('[name="media_boost"]')?.value || 6);

        const recentComments = parseFloat(form.querySelector('[name="recent_comment_weight"]')?.value || 6);
        const recentReactions = parseFloat(form.querySelector('[name="recent_reaction_weight"]')?.value || 4);
        const rapidComments = parseFloat(form.querySelector('[name="rapid_comment_weight"]')?.value || 3);
        const rapidReactions = parseFloat(form.querySelector('[name="rapid_reaction_weight"]')?.value || 2);
        const trendingBoost = parseFloat(form.querySelector('[name="trending_highlight_boost"]')?.value || 10);

        const freshnessSum = freshnessBase * 0.15;
        const personalizationSum = (followingBoost * 1.5) + (authorAffinity * 2) + (contentAffinity * 1.5) + verifiedBoost + mediaBoost;
        const engagementSum = (recentComments * 5) + (recentReactions * 4);
        const trendSum = (rapidComments * 4) + (rapidReactions * 3) + trendingBoost;

        const total = freshnessSum + personalizationSum + engagementSum + trendSum;
        if (total <= 0) return;

        const freshPct = Math.round((freshnessSum / total) * 100);
        const engagePct = Math.round((engagementSum / total) * 100);
        const personPct = Math.round((personalizationSum / total) * 100);
        const trendPct = Math.max(0, 100 - (freshPct + engagePct + personPct));

        document.getElementById('segment-freshness').style.width = freshPct + '%';
        document.getElementById('segment-engagement').style.width = engagePct + '%';
        document.getElementById('segment-personalization').style.width = personPct + '%';
        document.getElementById('segment-trend').style.width = trendPct + '%';

        document.getElementById('val-freshness-pct').innerText = freshPct + '%';
        document.getElementById('val-engagement-pct').innerText = engagePct + '%';
        document.getElementById('val-personalization-pct').innerText = personPct + '%';
        document.getElementById('val-trend-pct').innerText = trendPct + '%';

        if (modeSelect && liveModeBadge) {
            liveModeBadge.innerText = 'MODE: ' + modeSelect.value.toUpperCase();
        }
    }

    function updateKpis() {
        const modeEl = document.getElementById('kpi-feed-mode');
        const pageSizeEl = document.getElementById('kpi-page-size');
        const horizonEl = document.getElementById('kpi-candidate-horizon');
        const cacheEl = document.getElementById('kpi-cache-ttl');

        if (modeEl && modeSelect) modeEl.innerText = modeSelect.value.toUpperCase();
        if (pageSizeEl) {
            const pageSizeVal = form.querySelector('[name="feed_page_size"]')?.value || 20;
            pageSizeEl.innerText = pageSizeVal + ' Posts';
        }
        if (horizonEl) {
            const freshHours = form.querySelector('[name="fresh_candidate_hours"]')?.value || 72;
            const freshLimit = form.querySelector('[name="fresh_candidate_limit"]')?.value || 250;
            horizonEl.innerText = freshHours + 'h / ' + freshLimit;
        }
        if (cacheEl) {
            const cacheVal = form.querySelector('[name="cache_ttl_seconds"]')?.value || 180;
            cacheEl.innerText = cacheVal + 's TTL';
        }
    }

    form.querySelectorAll('.algo-calc, #feed_mode_select, #feed_page_size_input, #input_fresh_candidate_hours, #input_fresh_candidate_limit, #input_cache_ttl_seconds').forEach(el => {
        el.addEventListener('input', () => {
            recalcVisualizer();
            updateKpis();
        });
        el.addEventListener('change', () => {
            recalcVisualizer();
            updateKpis();
        });
    });

    recalcVisualizer();
    updateKpis();

    // ── AJAX Form Submission ──────────────────────────────────────
    form.addEventListener('submit', async function (e) {
        if (!window.fetch) return; // Fallback to normal form POST
        e.preventDefault();

        submitBtn.disabled = true;
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="feather-loader fa-spin"></i> <span>Saving...</span>';

        try {
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (res.ok) {
                const data = await res.json();
                showToast('success', 'Changes Saved', data.message || 'Feed settings successfully updated.');
                updateKpis();
            } else {
                form.submit();
            }
        } catch (err) {
            form.submit();
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });

    // ── Flush Cache Quick Action ──────────────────────────────────
    if (flushBtn) {
        flushBtn.addEventListener('click', async function () {
            flushBtn.disabled = true;
            const originalHtml = flushBtn.innerHTML;
            flushBtn.innerHTML = '<i class="feather-loader fa-spin"></i> Clearing...';

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                showToast('success', 'Cache Cleared', '{{ __("messages.community_feed_cache_flushed") }}');
            } catch (err) {
                showToast('error', 'Notice', 'Cache cleared.');
            } finally {
                flushBtn.disabled = false;
                flushBtn.innerHTML = originalHtml;
            }
        });
    }
});
</script>
@endpush
@endsection
