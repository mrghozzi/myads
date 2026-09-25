@extends('theme::layouts.master')

@push('head')
<style>
    /* ── Portal Design Tokens (.superdesign + Vikinger) ──────────── */
    .portal-page {
        --portal-card-bg: #ffffff;
        --portal-surface-alt: #f6f7fb;
        --portal-text: #283c50;
        --portal-heading: #1f2937;
        --portal-muted: #7587a7;
        --portal-border: #eaeaf5;
        --portal-border-strong: rgba(97, 93, 250, 0.18);
        --portal-accent: #615dfa;
        --portal-accent-hover: #504be8;
        --portal-accent-soft: rgba(97, 93, 250, 0.10);
        --portal-teal: #23d2e2;
        --portal-teal-soft: rgba(35, 210, 226, 0.12);
        --portal-success: #17c666;
        --portal-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        --portal-shadow-hover: 0 16px 36px rgba(97, 93, 250, 0.12);
        --portal-radius: 20px;
        --portal-radius-sm: 12px;
        --portal-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body.dark-mode .portal-page,
    [data-theme="css_d"] .portal-page,
    html.app-skin-dark .portal-page {
        --portal-card-bg: #1d2333;
        --portal-surface-alt: #181d2b;
        --portal-text: #e2e8f0;
        --portal-heading: #ffffff;
        --portal-muted: #94a3b8;
        --portal-border: #2b354d;
        --portal-border-strong: rgba(129, 140, 248, 0.25);
        --portal-accent: #7750f8;
        --portal-accent-hover: #8865ff;
        --portal-accent-soft: rgba(119, 80, 248, 0.16);
        --portal-teal: #00c7d9;
        --portal-teal-soft: rgba(0, 199, 217, 0.15);
        --portal-success: #20c997;
        --portal-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        --portal-shadow-hover: 0 18px 40px rgba(119, 80, 248, 0.22);
    }

    /* ── Hero Banner ─────────────────────────────────────────────── */
    .portal-hero-banner {
        background: linear-gradient(135deg, var(--portal-accent) 0%, #3454d1 50%, var(--portal-teal) 100%);
        border-radius: var(--portal-radius);
        padding: 36px 40px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(97, 93, 250, 0.25);
    }

    .portal-hero-banner::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        top: -120px;
        right: 15%;
        border-radius: 50%;
        pointer-events: none;
    }

    .portal-hero-banner::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: url('{{ theme_asset("img/banner/Newsfeed.png") }}') no-repeat right center;
        opacity: 0.12;
        pointer-events: none;
    }

    .portal-hero-left {
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
        z-index: 2;
        flex: 1;
    }

    .portal-hero-icon-box {
        width: 72px;
        height: 72px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .portal-hero-icon-box:hover {
        transform: rotate(5deg) scale(1.05);
    }

    .portal-hero-icon-box img {
        width: 38px;
        height: auto;
    }

    .portal-hero-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .portal-hero-eyebrow {
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        opacity: 0.9;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .portal-live-indicator {
        width: 8px;
        height: 8px;
        background: #17c666;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 3px rgba(23, 198, 102, 0.4);
        animation: portalPulse 2s infinite;
    }

    @keyframes portalPulse {
        0% { box-shadow: 0 0 0 0 rgba(23, 198, 102, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(23, 198, 102, 0); }
        100% { box-shadow: 0 0 0 0 rgba(23, 198, 102, 0); }
    }

    .portal-hero-title {
        font-size: clamp(1.6rem, 1.2rem + 1vw, 2.2rem);
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .portal-hero-text {
        font-size: 0.96rem;
        font-weight: 500;
        margin: 0;
        opacity: 0.92;
        max-width: 580px;
        line-height: 1.5;
    }

    .portal-hero-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .portal-hero-stat-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: var(--portal-radius-sm);
        padding: 10px 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 90px;
        text-align: center;
    }

    .portal-hero-stat-val {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .portal-hero-stat-lbl {
        font-size: 0.74rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
    }

    /* ── Portal Control Bar (Search + Quick Post + Mode Pill) ─────── */
    .portal-control-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: var(--portal-radius);
        padding: 14px 20px;
        margin-bottom: 24px;
        box-shadow: var(--portal-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .portal-search-box {
        position: relative;
        flex: 1;
        min-width: 240px;
    }

    .portal-search-icon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 16px;
        color: var(--portal-muted);
        font-size: 0.95rem;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    html[dir="rtl"] .portal-search-icon {
        left: auto;
        right: 16px;
    }

    .portal-search-input {
        width: 100%;
        height: 46px;
        background: var(--portal-surface-alt);
        border: 1px solid var(--portal-border);
        border-radius: 24px;
        padding: 0 42px 0 44px;
        font-size: 0.92rem;
        font-weight: 500;
        color: var(--portal-text);
        transition: var(--portal-transition);
        outline: none;
    }

    html[dir="rtl"] .portal-search-input {
        padding: 0 44px 0 42px;
    }

    .portal-search-input:focus {
        border-color: var(--portal-accent);
        box-shadow: 0 0 0 3px var(--portal-accent-soft);
        background: var(--portal-card-bg);
    }

    .portal-search-clear-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 14px;
        background: transparent;
        border: none;
        color: var(--portal-muted);
        cursor: pointer;
        font-size: 0.88rem;
        padding: 4px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: color 0.2s ease;
    }

    html[dir="rtl"] .portal-search-clear-btn {
        right: auto;
        left: 14px;
    }

    .portal-search-clear-btn:hover {
        color: #ea4d4d;
    }

    .portal-search-spinner {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 14px;
        display: none;
        color: var(--portal-accent);
        font-size: 0.9rem;
    }

    html[dir="rtl"] .portal-search-spinner {
        right: auto;
        left: 14px;
    }

    .portal-control-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .portal-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 16px;
        border-radius: 22px;
        background: var(--portal-surface-alt);
        border: 1px solid var(--portal-border);
        color: var(--portal-muted);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--portal-transition);
        text-decoration: none !important;
    }

    .portal-action-pill:hover {
        color: var(--portal-accent);
        border-color: var(--portal-accent-soft);
        background: var(--portal-accent-soft);
    }

    .portal-action-pill.active {
        background: var(--portal-accent);
        border-color: var(--portal-accent);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
    }

    .portal-action-pill.refresh-btn i {
        transition: transform 0.4s ease;
    }

    .portal-action-pill.refresh-btn:hover i {
        transform: rotate(180deg);
    }

    /* ── Tab Navigation ──────────────────────────────────────────── */
    .portal-tabs-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid var(--portal-border);
        padding-bottom: 0;
        margin: 0 0 24px 0;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .portal-tabs-nav::-webkit-scrollbar {
        display: none;
    }

    .portal-tab-btn {
        height: 46px;
        padding: 0 20px;
        color: var(--portal-muted);
        font-size: 0.94rem;
        font-weight: 800;
        cursor: pointer;
        transition: var(--portal-transition);
        position: relative;
        border: none;
        background: transparent;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: var(--portal-radius-sm) var(--portal-radius-sm) 0 0;
        text-decoration: none !important;
    }

    .portal-tab-btn:hover {
        color: var(--portal-accent);
        background: var(--portal-accent-soft);
    }

    .portal-tab-btn.active {
        color: var(--portal-accent);
        background: linear-gradient(180deg, var(--portal-accent-soft) 0%, transparent 100%);
    }

    .portal-tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--portal-accent), var(--portal-teal));
        border-radius: 4px 4px 0 0;
        box-shadow: 0 -2px 8px rgba(97, 93, 250, 0.4);
    }

    /* ── Activity / Feed Container ───────────────────────────────── */
    #infinite-scroll-container {
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-height: 200px;
        transition: opacity 0.2s ease;
    }

    #infinite-scroll-container.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* ── Empty State ─────────────────────────────────────────────── */
    .portal-empty-card {
        background: var(--portal-card-bg);
        border: 1px dashed var(--portal-border);
        border-radius: var(--portal-radius);
        padding: 48px 24px;
        text-align: center;
        box-shadow: var(--portal-shadow);
        margin: 16px 0;
    }

    .portal-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--portal-accent-soft);
        color: var(--portal-accent);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 16px;
    }

    .portal-empty-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--portal-heading);
        margin: 0 0 8px 0;
    }

    .portal-empty-desc {
        font-size: 0.92rem;
        color: var(--portal-muted);
        max-width: 440px;
        margin: 0 auto 20px auto;
        line-height: 1.5;
    }

    .portal-empty-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: var(--portal-accent);
        color: #ffffff !important;
        border: none;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        box-shadow: 0 6px 16px rgba(97, 93, 250, 0.25);
        transition: var(--portal-transition);
        text-decoration: none !important;
    }

    .portal-empty-action:hover {
        background: var(--portal-accent-hover);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(97, 93, 250, 0.35);
    }

    /* ── Manual Load More & Caught Up ────────────────────────────── */
    .portal-caught-up-banner {
        text-align: center;
        padding: 24px 16px;
        background: var(--portal-surface-alt);
        border: 1px dashed var(--portal-border);
        border-radius: var(--portal-radius-sm);
        color: var(--portal-muted);
        font-weight: 600;
        font-size: 0.88rem;
        margin-top: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .portal-caught-up-banner i {
        color: var(--portal-success);
        font-size: 1.4rem;
    }

    .portal-load-more-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        max-width: 320px;
        margin: 16px auto;
        padding: 12px 24px;
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border-strong);
        border-radius: 24px;
        color: var(--portal-accent);
        font-weight: 700;
        font-size: 0.92rem;
        box-shadow: var(--portal-shadow);
        cursor: pointer;
        transition: var(--portal-transition);
    }

    .portal-load-more-btn:hover {
        background: var(--portal-accent);
        color: #ffffff;
        box-shadow: var(--portal-shadow-hover);
        transform: translateY(-2px);
    }

    /* ── Search Styles ───────────────────────────────────────────── */
    .portal-search-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .portal-search-bar-header {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: var(--portal-radius);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: var(--portal-shadow);
    }

    .portal-search-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--portal-accent);
        background: var(--portal-accent-soft);
        padding: 4px 10px;
        border-radius: 12px;
        margin-bottom: 4px;
    }

    .portal-search-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--portal-heading);
        margin: 0;
    }

    .portal-search-count {
        color: var(--portal-muted);
        font-weight: 600;
    }

    .portal-clear-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--portal-surface-alt);
        border: 1px solid var(--portal-border);
        border-radius: 18px;
        color: var(--portal-muted);
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--portal-transition);
    }

    .portal-clear-btn:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }

    .portal-search-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }

    .portal-search-nav::-webkit-scrollbar {
        display: none;
    }

    .portal-search-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 18px;
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        color: var(--portal-muted);
        font-size: 0.86rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--portal-transition);
        white-space: nowrap;
    }

    .portal-search-nav-btn:hover {
        border-color: var(--portal-accent);
        color: var(--portal-accent);
    }

    .portal-search-nav-btn.active {
        background: var(--portal-accent);
        border-color: var(--portal-accent);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(97, 93, 250, 0.25);
    }

    .portal-search-nav-btn .nav-count {
        font-size: 0.74rem;
        background: rgba(0, 0, 0, 0.08);
        padding: 2px 7px;
        border-radius: 10px;
    }

    .portal-search-nav-btn.active .nav-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    .portal-search-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .portal-search-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
    }

    .portal-section-heading {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--portal-heading);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .portal-section-pill {
        font-size: 0.78rem;
        font-weight: 800;
        background: var(--portal-accent-soft);
        color: var(--portal-accent);
        padding: 4px 10px;
        border-radius: 12px;
    }

    .portal-users-grid,
    .portal-groups-grid,
    .portal-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    .portal-user-card,
    .portal-group-card,
    .portal-product-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: var(--portal-radius);
        overflow: hidden;
        box-shadow: var(--portal-shadow);
        transition: var(--portal-transition);
        display: flex;
        flex-direction: column;
    }

    .portal-user-card:hover,
    .portal-group-card:hover,
    .portal-product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--portal-shadow-hover);
        border-color: var(--portal-border-strong);
    }

    .portal-user-cover,
    .portal-group-cover {
        height: 80px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .portal-user-status-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    .portal-user-status-dot.online { background: #17c666; }
    .portal-user-status-dot.offline { background: #94a3b8; }

    .portal-user-body,
    .portal-group-body {
        padding: 0 16px 18px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-grow: 1;
    }

    .portal-user-avatar-wrapper,
    .portal-group-avatar-wrapper {
        margin-top: -32px;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }

    .portal-user-username,
    .portal-group-name {
        font-size: 1rem;
        font-weight: 800;
        color: var(--portal-heading);
        text-decoration: none !important;
        margin-bottom: 2px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .portal-user-username:hover,
    .portal-group-name:hover {
        color: var(--portal-accent);
    }

    .portal-verified-icon {
        color: #23d2e2;
        font-size: 0.85rem;
    }

    .portal-user-fullname,
    .portal-group-meta {
        font-size: 0.82rem;
        color: var(--portal-muted);
        margin: 0 0 12px 0;
    }

    .portal-user-profile-btn,
    .portal-group-join-btn {
        margin-top: auto;
        padding: 6px 16px;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 16px;
        background: var(--portal-surface-alt);
        border: 1px solid var(--portal-border);
        color: var(--portal-text);
        text-decoration: none !important;
        transition: var(--portal-transition);
    }

    .portal-user-profile-btn:hover,
    .portal-group-join-btn:hover {
        background: var(--portal-accent);
        color: #ffffff;
        border-color: var(--portal-accent);
    }

    .portal-product-thumb {
        width: 100%;
        aspect-ratio: 16/10;
        background-size: cover;
        background-position: center;
        border-bottom: 1px solid var(--portal-border);
        display: block;
    }

    .portal-product-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .portal-product-title {
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--portal-heading);
        text-decoration: none !important;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .portal-product-title:hover {
        color: var(--portal-accent);
    }

    .portal-product-desc {
        font-size: 0.84rem;
        color: var(--portal-muted);
        line-height: 1.4;
        margin-bottom: 12px;
        flex-grow: 1;
    }

    .portal-product-link {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--portal-accent);
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .portal-comments-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .portal-comment-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: var(--portal-radius-sm);
        padding: 16px;
        display: flex;
        gap: 14px;
        box-shadow: var(--portal-shadow);
        transition: var(--portal-transition);
    }

    .portal-comment-card:hover {
        border-color: var(--portal-border-strong);
    }

    .portal-comment-body {
        flex: 1;
    }

    .portal-comment-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }

    .portal-comment-author {
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--portal-heading);
    }

    .portal-comment-ctx {
        font-size: 0.82rem;
        color: var(--portal-muted);
    }

    .portal-comment-date {
        font-size: 0.78rem;
        color: var(--portal-muted);
        margin-left: auto;
    }

    html[dir="rtl"] .portal-comment-date {
        margin-left: 0;
        margin-right: auto;
    }

    .portal-comment-text {
        font-size: 0.88rem;
        color: var(--portal-text);
        line-height: 1.5;
        margin: 0;
    }

    /* ── Responsive adjustments ──────────────────────────────────── */
    @media (max-width: 768px) {
        .portal-hero-banner {
            padding: 24px 20px;
            flex-direction: column;
            align-items: flex-start;
        }

        .portal-hero-stats {
            width: 100%;
            justify-content: flex-start;
        }

        .portal-control-card {
            padding: 12px;
            flex-direction: column;
            align-items: stretch;
        }

        .portal-control-actions {
            justify-content: space-between;
        }
    }
</style>
@endpush

@section('content')
<div class="portal-page" id="portal-root-wrapper">
    <!-- MODERN HERO BANNER -->
    <div class="portal-hero-banner">
        <div class="portal-hero-left">
            <div class="portal-hero-icon-box">
                <img src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="Community">
            </div>
            <div class="portal-hero-title-group">
                <span class="portal-hero-eyebrow">
                    <span class="portal-live-indicator"></span>
                    {{ __('messages.portal_active_now') }}
                </span>
                <h1 class="portal-hero-title">{{ __('messages.portal_hero_title') }}</h1>
                <p class="portal-hero-text">{{ __('messages.portal_hero_subtitle') }}</p>
            </div>
        </div>

        @if(isset($portalStats))
            <div class="portal-hero-stats">
                <div class="portal-hero-stat-card">
                    <span class="portal-hero-stat-val">{{ number_format($portalStats['posts_today'] ?? 0) }}</span>
                    <span class="portal-hero-stat-lbl">{{ __('messages.today') ?? 'Today' }}</span>
                </div>
                <div class="portal-hero-stat-card">
                    <span class="portal-hero-stat-val">{{ number_format($portalStats['members_count'] ?? 0) }}</span>
                    <span class="portal-hero-stat-lbl">{{ __('messages.portal_stats_members') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-3-6-3 mobile-prefer-content">
        <!-- LEFT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_left" />
        </div>

        <!-- MAIN FEED COLUMN -->
        <div class="grid-column">
            <!-- CONTROL BAR: LIVE SEARCH & ACTIONS -->
            <div class="portal-control-card">
                <div class="portal-search-box">
                    <i class="fas fa-search portal-search-icon"></i>
                    <input
                        type="text"
                        id="portal-live-search-input"
                        class="portal-search-input"
                        placeholder="{{ __('messages.portal_search_placeholder') }}"
                        value="{{ $search ?? '' }}"
                        autocomplete="off"
                    >
                    <button type="button" id="portal-search-clear-btn" class="portal-search-clear-btn" title="{{ __('messages.portal_clear_search') }}">
                        <i class="fas fa-times-circle"></i>
                    </button>
                    <i class="fas fa-spinner fa-spin portal-search-spinner" id="portal-search-spinner"></i>
                </div>

                <div class="portal-control-actions">
                    <button type="button" class="portal-action-pill refresh-btn" id="portal-refresh-feed-btn" title="{{ __('messages.portal_refresh_feed') }}">
                        <i class="fas fa-sync-alt"></i>
                        <span>{{ __('messages.portal_refresh_feed') }}</span>
                    </button>

                    @php
                        $isSmartMode = ($requestedMode ?? 'smart') === 'smart';
                    @endphp
                    <span class="portal-action-pill" title="{{ $isSmartMode ? __('messages.feed_mode_smart') : __('messages.feed_mode_simple') }}">
                        <i class="fas {{ $isSmartMode ? 'fa-brain' : 'fa-clock' }}"></i>
                        <span>{{ $isSmartMode ? __('messages.portal_mode_smart') : __('messages.portal_mode_latest') }}</span>
                    </span>
                </div>
            </div>

            <!-- SEARCH RESULTS CONTAINER (Rendered via AJAX or fallback) -->
            <div id="portal-search-container" style="{{ empty($search) ? 'display: none;' : '' }}">
                @if(!empty($search))
                    @include('theme::portal.partials.search_results')
                @endif
            </div>

            <!-- FEED CONTAINER (Shown when not actively searching) -->
            <div id="portal-feed-container" style="{{ !empty($search) ? 'display: none;' : '' }}">
                <!-- POST COMPOSER -->
                <div id="quick-post-box">
                    @include('theme::partials.status.add_post')
                </div>

                <!-- TABS NAVIGATION -->
                <nav class="portal-tabs-nav" id="portal-tabs-bar">
                    <a href="{{ route('portal.index', ['filter' => 'all']) }}" class="portal-tab-btn {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}" data-portal-filter="all">
                        <i class="fas fa-globe"></i>
                        <span>{{ __('messages.portal_filter_all') }}</span>
                    </a>

                    @auth
                        <a href="{{ route('portal.index', ['filter' => 'me']) }}" class="portal-tab-btn {{ ($filter ?? '') === 'me' ? 'active' : '' }}" data-portal-filter="me">
                            <i class="fas fa-user-friends"></i>
                            <span>{{ __('messages.portal_filter_following') }}</span>
                        </a>

                        @if(\App\Support\GroupSettings::isEnabled())
                            <a href="{{ route('portal.index', ['filter' => 'groups']) }}" class="portal-tab-btn {{ ($filter ?? '') === 'groups' ? 'active' : '' }}" data-portal-filter="groups">
                                <i class="fas fa-users"></i>
                                <span>{{ __('messages.portal_filter_groups') }}</span>
                            </a>
                        @endif
                    @endauth

                    <a href="{{ route('portal.index', ['filter' => 'media']) }}" class="portal-tab-btn {{ ($filter ?? '') === 'media' ? 'active' : '' }}" data-portal-filter="media">
                        <i class="fas fa-photo-video"></i>
                        <span>{{ __('messages.portal_filter_media') }}</span>
                    </a>
                </nav>

                <!-- FEED STREAM (INFINITE SCROLL) -->
                <div id="infinite-scroll-container">
                    @if(isset($activities) && $activities->count() > 0)
                        @foreach($activities as $activity)
                            @include('theme::partials.activity.render', ['activity' => $activity])
                        @endforeach
                    @else
                        <div class="portal-empty-card">
                            <div class="portal-empty-icon">
                                <i class="far fa-newspaper"></i>
                            </div>
                            <h3 class="portal-empty-title">{{ __('messages.portal_empty_feed') }}</h3>
                            <p class="portal-empty-desc">{{ __('messages.portal_empty_feed_desc') }}</p>
                            @auth
                                <button type="button" class="portal-empty-action" onclick="document.querySelector('#quick-post-box textarea, #quick-post-box input')?.focus();">
                                    <i class="fas fa-edit"></i> {{ __('messages.portal_share_something') }}
                                </button>
                            @endauth
                        </div>
                    @endif

                    @if(isset($activities))
                        @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities->appends(['filter' => $filter ?? 'all'])])
                    @endif
                </div>

                <!-- END OF FEED / CAUGHT UP BANNER -->
                <div id="portal-caught-up" class="portal-caught-up-banner" style="{{ isset($activities) && $activities->hasMorePages() ? 'display: none;' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('messages.portal_caught_up') }}</span>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_right" />
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const portalBaseUrl = "{{ route('portal.index') }}";
    const feedContainer = document.getElementById('portal-feed-container');
    const searchContainer = document.getElementById('portal-search-container');
    const streamContainer = document.getElementById('infinite-scroll-container');
    const searchInput = document.getElementById('portal-live-search-input');
    const clearBtn = document.getElementById('portal-search-clear-btn');
    const searchSpinner = document.getElementById('portal-search-spinner');
    const refreshBtn = document.getElementById('portal-refresh-feed-btn');
    const caughtUpBanner = document.getElementById('portal-caught-up');
    const tabsBar = document.getElementById('portal-tabs-bar');

    let currentFilter = "{{ $filter ?? 'all' }}";
    let searchDebounceTimer = null;
    let isRequestInProgress = false;

    // ── Markdown Parser Setup ─────────────────────────────────────
    const renderer = new marked.Renderer();
    renderer.image = function(href, title, text) {
        return `<img src="${href}" alt="${text}" title="${title || ''}" loading="lazy" style="max-width:100%;height:auto;border-radius:12px;">`;
    };
    marked.setOptions({ renderer: renderer });

    function renderNewsMarkdown(container) {
        const scope = container || document;
        scope.querySelectorAll('.markdown-news-preview').forEach(el => {
            if (!el.getAttribute('data-rendered')) {
                try {
                    const rawContent = el.innerHTML;
                    const markdownText = el.innerText || rawContent;
                    let html = marked.parse(markdownText.trim());
                    const tagUrl = "{{ url('tag') }}";
                    html = html.replace(/(^|\s)#(\w+)/g, `$1<a href="${tagUrl}/$2">#$2</a>`);
                    el.innerHTML = DOMPurify.sanitize(html);
                    el.setAttribute('data-rendered', 'true');
                } catch (e) {
                    console.error('Error rendering news markdown:', e);
                }
            }
        });
    }

    renderNewsMarkdown();
    window.afterInfiniteScrollRender = function(container) {
        renderNewsMarkdown(container);
    };

    // ── Seamless AJAX Feed Loader ─────────────────────────────────
    async function loadFeed(filterName, pushState = true) {
        if (isRequestInProgress) return;
        isRequestInProgress = true;
        currentFilter = filterName;

        // Visual feedback
        streamContainer.classList.add('is-loading');
        updateActiveTab(filterName);

        // Hide search container and reveal feed container
        searchContainer.style.display = 'none';
        feedContainer.style.display = 'block';
        if (searchInput) {
            searchInput.value = '';
            toggleClearButton('');
        }

        const url = `${portalBaseUrl}?filter=${encodeURIComponent(filterName)}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            // Replace stream contents
            streamContainer.innerHTML = data.html || '';

            // Handle infinite scroll trigger
            let existingTrigger = document.getElementById('infinite-scroll-trigger');
            if (existingTrigger) existingTrigger.remove();

            if (data.has_more && data.next_page_url) {
                const triggerHtml = `
                    <div id="infinite-scroll-trigger" data-next-page="${data.next_page_url}" class="text-center w-100 mt-4 mb-4">
                        @include('theme::partials.ajax.skeleton')
                    </div>
                `;
                streamContainer.insertAdjacentHTML('beforeend', triggerHtml);
                if (caughtUpBanner) caughtUpBanner.style.display = 'none';
                initInfiniteScrollTrigger();
            } else {
                if (caughtUpBanner) caughtUpBanner.style.display = 'flex';
            }

            if (pushState) {
                window.history.pushState({ portalType: 'feed', filter: filterName }, '', url);
            }

            renderNewsMarkdown(streamContainer);
        } catch (error) {
            console.error('Feed loading error:', error);
        } finally {
            streamContainer.classList.remove('is-loading');
            isRequestInProgress = false;
        }
    }

    // ── Tab Clicks Handler ────────────────────────────────────────
    if (tabsBar) {
        tabsBar.addEventListener('click', function (e) {
            const tabBtn = e.target.closest('.portal-tab-btn');
            if (!tabBtn) return;
            e.preventDefault();
            const filter = tabBtn.getAttribute('data-portal-filter') || 'all';
            loadFeed(filter, true);
        });
    }

    function updateActiveTab(filterName) {
        document.querySelectorAll('.portal-tab-btn').forEach(btn => {
            if (btn.getAttribute('data-portal-filter') === filterName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // ── Refresh Button ────────────────────────────────────────────
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            const icon = refreshBtn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
            loadFeed(currentFilter, false).finally(() => {
                if (icon) icon.classList.remove('fa-spin');
            });
        });
    }

    // ── Live AJAX Search ──────────────────────────────────────────
    function toggleClearButton(val) {
        if (!clearBtn) return;
        clearBtn.style.display = val.trim().length > 0 ? 'inline-flex' : 'none';
    }

    if (searchInput) {
        toggleClearButton(searchInput.value);

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim();
            toggleClearButton(query);

            clearTimeout(searchDebounceTimer);
            if (query.length === 0) {
                // Return to feed
                searchContainer.style.display = 'none';
                feedContainer.style.display = 'block';
                window.history.pushState({ portalType: 'feed', filter: currentFilter }, '', `${portalBaseUrl}?filter=${encodeURIComponent(currentFilter)}`);
                return;
            }

            searchDebounceTimer = setTimeout(() => {
                performSearch(query, true);
            }, 350);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                const query = searchInput.value.trim();
                if (query.length > 0) {
                    performSearch(query, true);
                }
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            toggleClearButton('');
            searchContainer.style.display = 'none';
            feedContainer.style.display = 'block';
            window.history.pushState({ portalType: 'feed', filter: currentFilter }, '', `${portalBaseUrl}?filter=${encodeURIComponent(currentFilter)}`);
        });
    }

    async function performSearch(query, pushState = true) {
        if (searchSpinner) searchSpinner.style.display = 'inline-block';
        if (clearBtn) clearBtn.style.display = 'none';

        const searchUrl = `${portalBaseUrl}?search=${encodeURIComponent(query)}`;

        try {
            const response = await fetch(searchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Search network error');
            const data = await response.json();

            // Inject and display search results
            feedContainer.style.display = 'none';
            searchContainer.innerHTML = data.html || '';
            searchContainer.style.display = 'block';

            if (pushState) {
                window.history.pushState({ portalType: 'search', query: query }, '', searchUrl);
            }

            bindSearchSectionFilters();
            renderNewsMarkdown(searchContainer);
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            if (searchSpinner) searchSpinner.style.display = 'none';
            toggleClearButton(searchInput.value);
        }
    }

    // ── Search In-Page Category Filters ───────────────────────────
    function bindSearchSectionFilters() {
        const filterNav = searchContainer.querySelector('.portal-search-nav');
        const sections = searchContainer.querySelectorAll('.portal-search-section');
        const clearSearchBtn = searchContainer.querySelector('#portal-clear-search-btn');
        const emptyClearBtn = searchContainer.querySelector('#portal-empty-clear-btn');

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                if (clearBtn) clearBtn.click();
            });
        }

        if (emptyClearBtn) {
            emptyClearBtn.addEventListener('click', () => {
                if (clearBtn) clearBtn.click();
            });
        }

        if (!filterNav) return;

        filterNav.addEventListener('click', function (e) {
            const btn = e.target.closest('.portal-search-nav-btn');
            if (!btn) return;
            const filterType = btn.getAttribute('data-search-filter');

            filterNav.querySelectorAll('.portal-search-nav-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            sections.forEach(sec => {
                if (filterType === 'all' || sec.getAttribute('data-section') === filterType) {
                    sec.style.display = 'flex';
                } else {
                    sec.style.display = 'none';
                }
            });
        });
    }

    bindSearchSectionFilters();

    // ── History Popstate Support ──────────────────────────────────
    window.addEventListener('popstate', function (e) {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        const filterParam = urlParams.get('filter') || 'all';

        if (searchParam) {
            if (searchInput) searchInput.value = searchParam;
            performSearch(searchParam, false);
        } else {
            loadFeed(filterParam, false);
        }
    });

    // ── Infinite Scroll Observer Binding ──────────────────────────
    function initInfiniteScrollTrigger() {
        let trigger = document.getElementById('infinite-scroll-trigger');
        if (!trigger) return;

        let observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isRequestInProgress) {
                const nextUrl = trigger.getAttribute('data-next-page');
                if (!nextUrl) return;

                isRequestInProgress = true;
                fetch(nextUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.html) {
                        trigger.insertAdjacentHTML('beforebegin', data.html);
                        renderNewsMarkdown(streamContainer);
                    }

                    if (data.next_page_url) {
                        trigger.setAttribute('data-next-page', data.next_page_url);
                        isRequestInProgress = false;
                        observer.unobserve(trigger);
                        setTimeout(() => observer.observe(trigger), 100);
                    } else {
                        observer.unobserve(trigger);
                        trigger.remove();
                        if (caughtUpBanner) caughtUpBanner.style.display = 'flex';
                        isRequestInProgress = false;
                    }
                })
                .catch(err => {
                    console.error('Infinite scroll error:', err);
                    isRequestInProgress = false;
                });
            }
        }, { rootMargin: '250px' });

        observer.observe(trigger);
    }
});
</script>
@endpush
@endsection
