@extends('theme::layouts.master')

@section('content')
<style>
    /* ==========================================================================
       SUPERDESIGN DESIGN TOKENS & DASHBOARD ARCHITECTURE
       Compatible with Dark Mode, Light Mode, RTL & LTR
       ========================================================================== */
    :root,
    html[data-theme="css"] {
        --sd-primary: #615dfa;
        --sd-primary-hover: #4e4ac8;
        --sd-primary-rgb: 97, 93, 250;
        --sd-primary-soft: rgba(97, 93, 250, 0.1);
        --sd-accent: #23d2e2;
        --sd-accent-rgb: 35, 210, 226;
        --sd-accent-soft: rgba(35, 210, 226, 0.12);
        --sd-success: #10b981;
        --sd-success-soft: rgba(16, 185, 129, 0.12);
        --sd-warning: #f59e0b;
        --sd-warning-soft: rgba(245, 158, 11, 0.12);
        --sd-danger: #ef4444;
        --sd-danger-soft: rgba(239, 68, 68, 0.12);
        --sd-info: #0ea5e9;
        --sd-info-soft: rgba(14, 165, 233, 0.12);

        --sd-surface: #ffffff;
        --sd-surface-elevated: #ffffff;
        --sd-surface-alt: #f8faff;
        --sd-surface-inset: #f1f5f9;
        --sd-border: rgba(15, 23, 42, 0.08);
        --sd-border-strong: rgba(97, 93, 250, 0.2);
        --sd-text-main: #1e293b;
        --sd-text-muted: #64748b;
        --sd-text-light: #94a3b8;
        --sd-shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
        --sd-shadow-md: 0 10px 24px rgba(15, 23, 42, 0.06);
        --sd-shadow-lg: 0 20px 40px rgba(97, 93, 250, 0.08);
        --sd-radius-sm: 10px;
        --sd-radius-md: 16px;
        --sd-radius-lg: 22px;
        --sd-radius-pill: 9999px;
    }

    /* Dark Mode Theme Tokens */
    html[data-theme="css_d"],
    body.dark-mode,
    html.app-skin-dark {
        --sd-surface: #1e2538;
        --sd-surface-elevated: #232c42;
        --sd-surface-alt: #161b29;
        --sd-surface-inset: #131724;
        --sd-border: rgba(255, 255, 255, 0.08);
        --sd-border-strong: rgba(129, 140, 248, 0.28);
        --sd-text-main: #f8fafc;
        --sd-text-muted: #94a3b8;
        --sd-text-light: #64748b;
        --sd-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.25);
        --sd-shadow-md: 0 10px 24px rgba(0, 0, 0, 0.35);
        --sd-shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.5);
    }

    .sd-dashboard {
        display: flex;
        flex-direction: column;
        gap: 28px;
        width: 100%;
        max-width: 1240px;
        margin: 0 auto;
    }

    /* --------------------------------------------------------------------------
       HERO COMMAND PANEL
       -------------------------------------------------------------------------- */
    .sd-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #615dfa 100%);
        border-radius: var(--sd-radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(79, 70, 229, 0.28);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .sd-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        inset-inline-end: -60px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .sd-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        inset-inline-start: 25%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(35, 210, 226, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .sd-hero-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }

    .sd-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: var(--sd-radius-pill);
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 12px;
    }

    .sd-hero-title {
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 800;
        margin: 0 0 8px;
        letter-spacing: -0.02em;
        line-height: 1.25;
    }

    .sd-hero-subtitle {
        font-size: 14.5px;
        margin: 0 0 20px;
        opacity: 0.9;
        line-height: 1.6;
    }

    .sd-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .sd-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: var(--sd-radius-md);
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: none;
    }

    .sd-hero-btn-solid {
        background: #ffffff;
        color: #4f46e5;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .sd-hero-btn-solid:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        color: #4338ca;
    }

    .sd-hero-btn-glass {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }

    .sd-hero-btn-glass:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* Hero Balance Box */
    .sd-hero-balance-box {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(16px);
        border-radius: var(--sd-radius-lg);
        padding: 24px 28px;
        min-width: 260px;
        text-align: center;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .sd-hero-balance-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.08em;
        opacity: 0.85;
        margin: 0 0 6px;
    }

    .sd-hero-balance-val {
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        margin: 0;
        letter-spacing: -0.03em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .sd-hero-balance-currency {
        font-size: 18px;
        font-weight: 700;
        opacity: 0.85;
    }

    .sd-hero-balance-caption {
        font-size: 12px;
        opacity: 0.85;
        margin: 8px 0 0;
    }

    /* --------------------------------------------------------------------------
       QUICK METRICS STRIP
       -------------------------------------------------------------------------- */
    .sd-metrics-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .sd-metric-pill {
        background: var(--sd-surface);
        border: 1px solid var(--sd-border);
        border-radius: var(--sd-radius-md);
        padding: 16px 20px;
        box-shadow: var(--sd-shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .sd-metric-pill:hover {
        transform: translateY(-2px);
        box-shadow: var(--sd-shadow-md);
    }

    .sd-metric-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--sd-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .icon-pts { background: var(--sd-warning-soft); color: var(--sd-warning); }
    .icon-nvu { background: var(--sd-primary-soft); color: var(--sd-primary); }
    .icon-nlink { background: var(--sd-accent-soft); color: var(--sd-accent); }
    .icon-vu { background: var(--sd-success-soft); color: var(--sd-success); }
    .icon-nsmart { background: var(--sd-info-soft); color: var(--sd-info); }

    .sd-metric-info {
        min-width: 0;
    }

    .sd-metric-label {
        font-size: 11.5px;
        color: var(--sd-text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sd-metric-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--sd-text-main);
        margin: 0;
        line-height: 1.2;
    }

    /* --------------------------------------------------------------------------
       CORE KPI GRID
       -------------------------------------------------------------------------- */
    .sd-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .sd-kpi-card {
        background: var(--sd-surface);
        border: 1px solid var(--sd-border);
        border-radius: var(--sd-radius-lg);
        padding: 24px;
        box-shadow: var(--sd-shadow-sm);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s, border-color 0.25s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .sd-kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sd-shadow-lg);
        border-color: var(--sd-border-strong);
    }

    .sd-kpi-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .sd-kpi-chip {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .sd-kpi-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: var(--sd-radius-pill);
        background: var(--sd-surface-alt);
        color: var(--sd-text-muted);
        border: 1px solid var(--sd-border);
    }

    .sd-kpi-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--sd-text-main);
        margin: 0 0 6px;
    }

    .sd-kpi-value-wrap {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 12px;
    }

    .sd-kpi-primary-val {
        font-size: 32px;
        font-weight: 800;
        color: var(--sd-text-main);
        line-height: 1;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .sd-kpi-secondary-val {
        font-size: 13px;
        color: var(--sd-text-muted);
        font-weight: 600;
    }

    .sd-kpi-inventory-badge {
        background: var(--sd-surface-alt);
        border: 1px solid var(--sd-border);
        padding: 8px 12px;
        border-radius: var(--sd-radius-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        color: var(--sd-text-muted);
        margin-bottom: 16px;
    }

    .sd-kpi-inventory-num {
        font-weight: 800;
        color: var(--sd-text-main);
    }

    .sd-kpi-footer-link {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--sd-radius-sm);
        background: var(--sd-surface-alt);
        color: var(--sd-primary);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }

    .sd-kpi-footer-link:hover {
        background: var(--sd-primary);
        color: #ffffff;
    }

    /* --------------------------------------------------------------------------
       POINTS MANAGEMENT & AJAX WORKSPACE
       -------------------------------------------------------------------------- */
    .sd-workspace-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }

    @media (min-width: 992px) {
        .sd-workspace-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .sd-panel {
        background: var(--sd-surface);
        border: 1px solid var(--sd-border);
        border-radius: var(--sd-radius-lg);
        padding: 24px;
        box-shadow: var(--sd-shadow-sm);
        display: flex;
        flex-direction: column;
    }

    .sd-panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .sd-panel-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .sd-panel-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--sd-text-main);
        margin: 0;
    }

    .sd-panel-desc {
        font-size: 13px;
        color: var(--sd-text-muted);
        line-height: 1.5;
        margin: 0 0 20px;
    }

    .sd-form-group {
        margin-bottom: 16px;
    }

    .sd-form-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--sd-text-main);
        margin-bottom: 6px;
    }

    .sd-form-input,
    .sd-form-select {
        width: 100%;
        padding: 11px 15px;
        border-radius: var(--sd-radius-md);
        border: 1px solid var(--sd-border);
        background: var(--sd-surface-alt);
        color: var(--sd-text-main);
        font-size: 13.5px;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .sd-form-input:focus,
    .sd-form-select:focus {
        border-color: var(--sd-primary);
        box-shadow: 0 0 0 3px var(--sd-primary-soft);
        background: var(--sd-surface);
    }

    .sd-quick-presets {
        display: flex;
        gap: 6px;
        margin-top: 8px;
    }

    .sd-preset-btn {
        background: var(--sd-surface-alt);
        border: 1px solid var(--sd-border);
        color: var(--sd-text-muted);
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: var(--sd-radius-pill);
        cursor: pointer;
        transition: all 0.15s;
    }

    .sd-preset-btn:hover {
        background: var(--sd-primary-soft);
        color: var(--sd-primary);
        border-color: var(--sd-primary);
    }

    /* Live Preview Box */
    .sd-preview-box {
        background: var(--sd-surface-alt);
        border: 1px dashed var(--sd-border-strong);
        border-radius: var(--sd-radius-md);
        padding: 12px 16px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sd-preview-icon {
        font-size: 20px;
        color: var(--sd-primary);
    }

    .sd-preview-content {
        min-width: 0;
    }

    .sd-preview-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--sd-text-muted);
        margin: 0 0 2px;
    }

    .sd-preview-value {
        font-size: 14px;
        font-weight: 800;
        color: var(--sd-primary);
        margin: 0;
    }

    .sd-btn-submit {
        width: 100%;
        padding: 12px 20px;
        border-radius: var(--sd-radius-md);
        border: none;
        font-size: 13.5px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, var(--sd-primary) 0%, #4f46e5 100%);
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform 0.18s, box-shadow 0.18s, opacity 0.18s;
        margin-top: auto;
    }

    .sd-btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(97, 93, 250, 0.35);
    }

    .sd-btn-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .sd-btn-secondary {
        background: linear-gradient(135deg, var(--sd-accent) 0%, #06b6d4 100%);
        color: #0f172a;
        box-shadow: 0 4px 12px rgba(35, 210, 226, 0.25);
    }

    .sd-btn-secondary:hover:not(:disabled) {
        box-shadow: 0 8px 20px rgba(35, 210, 226, 0.35);
    }

    /* Vouchers Tabs & Table */
    .sd-voucher-tabs {
        display: flex;
        background: var(--sd-surface-alt);
        border: 1px solid var(--sd-border);
        border-radius: var(--sd-radius-md);
        padding: 4px;
        margin-bottom: 16px;
        gap: 4px;
    }

    .sd-voucher-tab {
        flex: 1;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: var(--sd-text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }

    .sd-voucher-tab.active {
        background: var(--sd-surface);
        color: var(--sd-primary);
        box-shadow: var(--sd-shadow-sm);
    }

    .sd-voucher-pane {
        display: none;
    }

    .sd-voucher-pane.active {
        display: block;
    }

    .sd-vouchers-table-wrap {
        margin-top: 18px;
        border-top: 1px solid var(--sd-border);
        padding-top: 14px;
        max-height: 190px;
        overflow-y: auto;
    }

    .sd-vouchers-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .sd-vouchers-table th {
        text-align: start;
        padding: 6px 8px;
        color: var(--sd-text-muted);
        font-weight: 700;
        border-bottom: 1px solid var(--sd-border);
        text-transform: uppercase;
        font-size: 10.5px;
    }

    .sd-vouchers-table td {
        padding: 8px;
        border-bottom: 1px dashed var(--sd-border);
        color: var(--sd-text-main);
    }

    .sd-voucher-code-badge {
        font-family: monospace;
        font-weight: 700;
        padding: 2px 6px;
        background: var(--sd-surface-alt);
        border: 1px solid var(--sd-border);
        border-radius: 6px;
        color: var(--sd-primary);
        font-size: 11px;
    }

    .sd-voucher-copy-btn {
        background: transparent;
        border: none;
        color: var(--sd-text-muted);
        cursor: pointer;
        padding: 2px 4px;
        font-size: 12px;
        transition: color 0.15s;
    }

    .sd-voucher-copy-btn:hover {
        color: var(--sd-primary);
    }

    /* --------------------------------------------------------------------------
       CAMPAIGN LAUNCHPADS (PROMO SERVICES)
       -------------------------------------------------------------------------- */
    .sd-launchpads-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media (min-width: 992px) {
        .sd-launchpads-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .sd-service-banner {
        border-radius: var(--sd-radius-lg);
        padding: 28px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        box-shadow: var(--sd-shadow-md);
    }

    .sd-service-banner::after {
        content: '';
        position: absolute;
        top: -40px;
        inset-inline-end: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .banner-smart {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    }

    .banner-custom {
        background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    }

    .banner-yt {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .banner-seo {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .sd-service-content {
        position: relative;
        z-index: 2;
    }

    .sd-service-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: var(--sd-radius-pill);
        background: rgba(255, 255, 255, 0.2);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
        backdrop-filter: blur(6px);
    }

    .sd-service-title {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 8px;
        line-height: 1.3;
    }

    .sd-service-desc {
        font-size: 13.5px;
        margin: 0 0 16px;
        opacity: 0.9;
        line-height: 1.5;
    }

    .sd-service-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sd-service-stat-box {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--sd-radius-md);
        padding: 10px 16px;
        backdrop-filter: blur(8px);
    }

    .sd-service-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.8;
        margin: 0 0 2px;
    }

    .sd-service-stat-val {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }

    .sd-service-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .sd-btn-light {
        background: #ffffff;
        color: #1e293b;
        padding: 9px 18px;
        border-radius: var(--sd-radius-md);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .sd-btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        color: #0f172a;
    }

    .sd-btn-trans {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 9px 18px;
        border-radius: var(--sd-radius-md);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(6px);
        transition: background 0.15s, transform 0.15s;
    }

    .sd-btn-trans:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* --------------------------------------------------------------------------
       REFERRAL ACCELERATOR HUB
       -------------------------------------------------------------------------- */
    .sd-referral-card {
        background: var(--sd-surface);
        border: 1px solid var(--sd-border);
        border-radius: var(--sd-radius-lg);
        padding: 28px;
        box-shadow: var(--sd-shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sd-ref-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .sd-ref-title-group {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .sd-ref-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--sd-primary) 0%, #8b5cf6 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 6px 16px rgba(97, 93, 250, 0.3);
    }

    .sd-ref-copy-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sd-ref-input {
        flex: 1;
        min-width: 260px;
        padding: 11px 16px;
        border-radius: var(--sd-radius-md);
        border: 1px solid var(--sd-border);
        background: var(--sd-surface-alt);
        color: var(--sd-text-main);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
    }

    .sd-ref-copy-btn {
        background: linear-gradient(135deg, var(--sd-primary) 0%, #4f46e5 100%);
        color: #ffffff;
        border: none;
        padding: 11px 22px;
        border-radius: var(--sd-radius-md);
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
        transition: transform 0.15s, background 0.2s;
    }

    .sd-ref-copy-btn:hover {
        transform: translateY(-2px);
    }

    /* --------------------------------------------------------------------------
       FLOATING TOAST NOTIFICATIONS (VANILLA JS AJAX FEEDBACK)
       -------------------------------------------------------------------------- */
    .sd-toast-container {
        position: fixed;
        bottom: 24px;
        inset-inline-end: 24px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .sd-toast {
        min-width: 300px;
        max-width: 420px;
        padding: 14px 18px;
        border-radius: var(--sd-radius-md);
        background: var(--sd-surface-elevated);
        border: 1px solid var(--sd-border);
        box-shadow: var(--sd-shadow-lg);
        color: var(--sd-text-main);
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        pointer-events: auto;
        transform: translateY(20px);
        opacity: 0;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }

    .sd-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .sd-toast-success {
        border-inline-start: 4px solid var(--sd-success);
    }
    .sd-toast-success i { color: var(--sd-success); }

    .sd-toast-error {
        border-inline-start: 4px solid var(--sd-danger);
    }
    .sd-toast-error i { color: var(--sd-danger); }

    .sd-toast-info {
        border-inline-start: 4px solid var(--sd-primary);
    }
    .sd-toast-info i { color: var(--sd-primary); }
</style>

<div class="sd-dashboard">
    <!-- TRADITIONAL FLASH MESSAGES FALLBACK -->
    @if(session('errMSG'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--sd-radius-md); box-shadow: var(--sd-shadow-sm);">
            <strong>{{ __('messages.warning') }}:</strong> {{ session('errMSG') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('MSG'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--sd-radius-md); box-shadow: var(--sd-shadow-sm);">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('MSG') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. HERO COMMAND HEADER -->
    <section class="sd-hero">
        <div class="sd-hero-content">
            <div class="sd-hero-badge">
                <i class="fa-solid fa-chart-line"></i> {{ __('messages.dashboard_eyebrow') }}
            </div>
            <h1 class="sd-hero-title">
                {{ __('messages.dashboard_welcome', ['name' => $user->username]) }}
            </h1>
            <p class="sd-hero-subtitle">
                {{ __('messages.dashboard_welcome_subtitle') }}
            </p>
            <div class="sd-hero-actions">
                <a href="#convertPointsSection" class="sd-hero-btn sd-hero-btn-solid">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> {{ __('messages.quick_convert') }}
                </a>
                <a href="#transferPointsSection" class="sd-hero-btn sd-hero-btn-glass">
                    <i class="fa-solid fa-paper-plane"></i> {{ __('messages.quick_transfer') }}
                </a>
                <a href="{{ route('profile.history') }}" class="sd-hero-btn sd-hero-btn-glass">
                    <i class="fa-solid fa-clock-rotate-left"></i> {{ __('messages.view_all_history') }}
                </a>
            </div>
        </div>

        <!-- Live Points Balance Hero Box -->
        <div class="sd-hero-balance-box">
            <div class="sd-hero-balance-label">
                <i class="fa-solid fa-coins me-1"></i> {{ __('messages.pts_balance') }}
            </div>
            <div class="sd-hero-balance-val">
                <span id="heroPtsDisplay">{{ number_format($user->pts, 2) }}</span>
                <span class="sd-hero-balance-currency">PTS</span>
            </div>
            <p class="sd-hero-balance-caption">{{ __('messages.pts_balance_desc') }}</p>
        </div>
    </section>

    <!-- 2. QUICK INVENTORY STRIP -->
    <section class="sd-metrics-strip">
        <!-- Banner Ads Inventory -->
        <div class="sd-metric-pill">
            <div class="sd-metric-icon icon-nvu">
                <i class="fa-solid fa-image"></i>
            </div>
            <div class="sd-metric-info">
                <p class="sd-metric-label">{{ __('messages.banner_ads_inventory') }}</p>
                <p class="sd-metric-val"><span id="pillNvu">{{ number_format($user->nvu) }}</span></p>
            </div>
        </div>

        <!-- Text Ads Inventory -->
        <div class="sd-metric-pill">
            <div class="sd-metric-icon icon-nlink">
                <i class="fa-solid fa-link"></i>
            </div>
            <div class="sd-metric-info">
                <p class="sd-metric-label">{{ __('messages.text_ads_inventory') }}</p>
                <p class="sd-metric-val"><span id="pillNlink">{{ number_format($user->nlink) }}</span></p>
            </div>
        </div>

        <!-- Visits Exchange Inventory -->
        <div class="sd-metric-pill">
            <div class="sd-metric-icon icon-vu">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
            <div class="sd-metric-info">
                <p class="sd-metric-label">{{ __('messages.exchange_visits_inventory') }}</p>
                <p class="sd-metric-val"><span id="pillVu">{{ number_format($user->vu) }}</span></p>
            </div>
        </div>

        <!-- Smart Ads Inventory -->
        <div class="sd-metric-pill">
            <div class="sd-metric-icon icon-nsmart">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="sd-metric-info">
                <p class="sd-metric-label">{{ __('messages.smart_ads_inventory') }}</p>
                <p class="sd-metric-val"><span id="pillNsmart">{{ number_format($user->nsmart, 2) }}</span></p>
            </div>
        </div>
    </section>

    <!-- 3. TOP ADS ADVERTISERS BANNER SLOT -->
    {!! ads_site(2) !!}

    <!-- 4. CORE PERFORMANCE KPI CARDS -->
    <section class="sd-kpi-grid">
        <!-- Banner Ads KPI -->
        <div class="sd-kpi-card">
            <div>
                <div class="sd-kpi-card-header">
                    <div class="sd-kpi-chip icon-nvu">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <span class="sd-kpi-badge">{{ __('messages.bannads') }}</span>
                </div>
                <h3 class="sd-kpi-title">{{ __('messages.views_delivered') }}</h3>
                <div class="sd-kpi-value-wrap">
                    <p class="sd-kpi-primary-val">{{ number_format($bannerStats['vu']) }}</p>
                    <span class="sd-kpi-secondary-val">/ {{ number_format($bannerStats['clik']) }} {{ __('messages.clicks_received') }}</span>
                </div>
                <div class="sd-kpi-inventory-badge">
                    <span>{{ __('messages.available_balance') }}</span>
                    <span class="sd-kpi-inventory-num"><span id="kpiNvu">{{ number_format($user->nvu) }}</span> {{ __('messages.bannads') }}</span>
                </div>
            </div>
            <a href="{{ url('/b_list') }}" class="sd-kpi-footer-link">
                <span>{{ __('messages.manage_banners') }}</span>
                <i class="fa-solid fa-chevron-right fa-rtl-flip"></i>
            </a>
        </div>

        <!-- Text Ads KPI -->
        <div class="sd-kpi-card">
            <div>
                <div class="sd-kpi-card-header">
                    <div class="sd-kpi-chip icon-nlink">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <span class="sd-kpi-badge">{{ __('messages.textads') }}</span>
                </div>
                <h3 class="sd-kpi-title">{{ __('messages.clicks_received') }}</h3>
                <div class="sd-kpi-value-wrap">
                    <p class="sd-kpi-primary-val">{{ number_format($linkStats['clik']) }}</p>
                    <span class="sd-kpi-secondary-val">{{ __('messages.clicks_received') }}</span>
                </div>
                <div class="sd-kpi-inventory-badge">
                    <span>{{ __('messages.available_balance') }}</span>
                    <span class="sd-kpi-inventory-num"><span id="kpiNlink">{{ number_format($user->nlink) }}</span> {{ __('messages.textads') }}</span>
                </div>
            </div>
            <a href="{{ url('/l_list') }}" class="sd-kpi-footer-link">
                <span>{{ __('messages.manage_text_ads') }}</span>
                <i class="fa-solid fa-chevron-right fa-rtl-flip"></i>
            </a>
        </div>

        <!-- Visits Exchange KPI -->
        <div class="sd-kpi-card">
            <div>
                <div class="sd-kpi-card-header">
                    <div class="sd-kpi-chip icon-vu">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <span class="sd-kpi-badge">{{ __('messages.exvisit') }}</span>
                </div>
                <h3 class="sd-kpi-title">{{ __('messages.visits_received') }}</h3>
                <div class="sd-kpi-value-wrap">
                    <p class="sd-kpi-primary-val">{{ number_format($visitStats['vu']) }}</p>
                    <span class="sd-kpi-secondary-val">{{ __('messages.visits_received') }}</span>
                </div>
                <div class="sd-kpi-inventory-badge">
                    <span>{{ __('messages.available_balance') }}</span>
                    <span class="sd-kpi-inventory-num"><span id="kpiVu">{{ number_format($user->vu) }}</span> {{ __('messages.exvisit') }}</span>
                </div>
            </div>
            <a href="{{ url('/v_list') }}" class="sd-kpi-footer-link">
                <span>{{ __('messages.manage_visits') }}</span>
                <i class="fa-solid fa-chevron-right fa-rtl-flip"></i>
            </a>
        </div>

        <!-- Smart Ads KPI -->
        <div class="sd-kpi-card">
            <div>
                <div class="sd-kpi-card-header">
                    <div class="sd-kpi-chip icon-nsmart">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <span class="sd-kpi-badge">{{ __('messages.smart_ads') }}</span>
                </div>
                <h3 class="sd-kpi-title">{{ __('messages.smart_impressions_label') ?? 'Impressions' }}</h3>
                <div class="sd-kpi-value-wrap">
                    <p class="sd-kpi-primary-val">{{ number_format($smartAdStats['impressions']) }}</p>
                    <span class="sd-kpi-secondary-val">/ {{ number_format($smartAdStats['clicks']) }} {{ __('messages.clicks_received') }}</span>
                </div>
                <div class="sd-kpi-inventory-badge">
                    <span>{{ __('messages.available_balance') }}</span>
                    <span class="sd-kpi-inventory-num"><span id="kpiNsmart">{{ number_format($user->nsmart, 2) }}</span> {{ __('messages.smart_ads') }}</span>
                </div>
            </div>
            <a href="{{ route('ads.smart.index') }}" class="sd-kpi-footer-link">
                <span>{{ __('messages.manage_smart_ads') }}</span>
                <i class="fa-solid fa-chevron-right fa-rtl-flip"></i>
            </a>
        </div>
    </section>

    <!-- 5. FINANCIAL & AJAX POINTS HUB (CONVERT, TRANSFER, VOUCHERS) -->
    <section class="sd-workspace-grid">
        
        <!-- CARD 1: POINTS CONVERTER (AJAX + LIVE PREVIEW) -->
        <div class="sd-panel" id="convertPointsSection">
            <div class="sd-panel-header">
                <div class="sd-panel-icon icon-pts">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </div>
                <div>
                    <h3 class="sd-panel-title">{{ __('messages.convert_points_title') }}</h3>
                </div>
            </div>
            <p class="sd-panel-desc">{{ __('messages.convert_points_desc') }}</p>

            <form id="convertPointsForm" action="{{ url('/home') }}" method="POST">
                @csrf
                <div class="sd-form-group">
                    <div class="sd-form-label">
                        <label for="convertPtsInput">{{ __('messages.points_to_convert') }}</label>
                        <span style="font-size: 11px; color: var(--sd-text-muted);">
                            {{ __('messages.available') }}: <strong id="convertAvailablePts">{{ number_format($user->pts, 2) }}</strong> PTS
                        </span>
                    </div>
                    <input type="number" id="convertPtsInput" name="pts" class="sd-form-input" min="1" step="1" required placeholder="{{ __('messages.enter_points_placeholder') }}">
                    <div class="sd-quick-presets">
                        <button type="button" class="sd-preset-btn" onclick="setConvertAmount(10)">+10</button>
                        <button type="button" class="sd-preset-btn" onclick="setConvertAmount(50)">+50</button>
                        <button type="button" class="sd-preset-btn" onclick="setConvertAmount(100)">+100</button>
                        <button type="button" class="sd-preset-btn" onclick="setConvertAll()">{{ __('messages.all_pts') }}</button>
                    </div>
                </div>

                <div class="sd-form-group">
                    <label for="convertToSelect" class="sd-form-label">{{ __('messages.convert_destination') }}</label>
                    <select id="convertToSelect" name="to" class="sd-form-select">
                        <option value="link" data-ratio="0.5">{{ __('messages.destination_text_ads') }}</option>
                        <option value="banners" data-ratio="0.5">{{ __('messages.destination_banners') }}</option>
                        <option value="exchv" data-ratio="0.25">{{ __('messages.destination_visits') }}</option>
                        @php
                            $smartRateFormatted = rtrim(rtrim(number_format(1 / ($smartDivisor ?: 4), 2, '.', ''), '0'), '.');
                        @endphp
                        <option value="smartads" data-ratio="{{ 1 / ($smartDivisor ?: 4) }}">
                            {{ __('messages.destination_smart_ads', ['rate' => $smartRateFormatted]) }}
                        </option>
                    </select>
                </div>

                <!-- Dynamic Live Preview Box -->
                <div class="sd-preview-box">
                    <i class="fa-solid fa-calculator sd-preview-icon"></i>
                    <div class="sd-preview-content">
                        <p class="sd-preview-label">{{ __('messages.convert_preview') }}</p>
                        <p class="sd-preview-value" id="convertPreviewText">{{ __('messages.convert_preview_hint') }}: 0</p>
                    </div>
                </div>

                <button type="submit" id="convertSubmitBtn" class="sd-btn-submit">
                    <i class="fa-solid fa-bolt"></i>
                    <span id="convertBtnText">{{ __('messages.convert_btn') }}</span>
                </button>
            </form>
        </div>

        <!-- CARD 2: TRANSFER POINTS (AJAX) -->
        <div class="sd-panel" id="transferPointsSection">
            <div class="sd-panel-header">
                <div class="sd-panel-icon icon-nlink">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <h3 class="sd-panel-title">{{ __('messages.transfer_points_title') }}</h3>
                </div>
            </div>
            <p class="sd-panel-desc">{{ __('messages.transfer_points_desc') }}</p>

            <form id="transferPtsForm" action="{{ route('dashboard.pts.transfer') }}" method="POST">
                @csrf
                <div class="sd-form-group">
                    <label for="transferUsernameInput" class="sd-form-label">{{ __('messages.recipient_username') }}</label>
                    <input type="text" id="transferUsernameInput" name="username" class="sd-form-input" required placeholder="{{ __('messages.enter_username_placeholder') }}">
                </div>

                <div class="sd-form-group">
                    <div class="sd-form-label">
                        <label for="transferAmountInput">{{ __('messages.transfer_amount') }}</label>
                        <span style="font-size: 11px; color: var(--sd-text-muted);">
                            {{ __('messages.available') }}: <strong id="transferAvailablePts">{{ number_format($user->pts, 2) }}</strong> PTS
                        </span>
                    </div>
                    <input type="number" id="transferAmountInput" name="amount" min="1" step="0.01" class="sd-form-input" required placeholder="{{ __('messages.enter_points_placeholder') }}">
                </div>

                <button type="submit" id="transferSubmitBtn" class="sd-btn-submit sd-btn-secondary" style="margin-top: 48px;">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span id="transferBtnText">{{ __('messages.transfer_btn') }}</span>
                </button>
            </form>
        </div>

        <!-- CARD 3: VOUCHERS HUB (GENERATE & REDEEM AJAX) -->
        <div class="sd-panel" id="vouchersSection">
            <div class="sd-panel-header">
                <div class="sd-panel-icon icon-vu">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div>
                    <h3 class="sd-panel-title">{{ __('messages.voucher_hub_title') }}</h3>
                </div>
            </div>
            <p class="sd-panel-desc">{{ __('messages.voucher_hub_desc') }}</p>

            <!-- Voucher Pill Tabs -->
            <div class="sd-voucher-tabs">
                <button type="button" class="sd-voucher-tab active" onclick="switchVoucherTab('generate', this)">
                    <i class="fa-solid fa-plus-circle me-1"></i> {{ __('messages.generate_voucher_tab') }}
                </button>
                <button type="button" class="sd-voucher-tab" onclick="switchVoucherTab('claim', this)">
                    <i class="fa-solid fa-gift me-1"></i> {{ __('messages.claim_voucher_tab') }}
                </button>
            </div>

            <!-- Tab 1: Generate Voucher -->
            <div id="voucherGeneratePane" class="sd-voucher-pane active">
                <form id="generateVoucherForm" action="{{ route('dashboard.pts.voucher.generate') }}" method="POST">
                    @csrf
                    <div class="sd-form-group">
                        <label for="voucherAmountInput" class="sd-form-label">{{ __('messages.voucher_amount_label') }}</label>
                        <input type="number" id="voucherAmountInput" name="amount" min="1" step="0.01" class="sd-form-input" required placeholder="{{ __('messages.enter_points_placeholder') }}">
                    </div>
                    <button type="submit" id="generateVoucherBtn" class="sd-btn-submit">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span id="generateVoucherBtnText">{{ __('messages.generate_voucher_btn') }}</span>
                    </button>
                </form>
            </div>

            <!-- Tab 2: Redeem Voucher -->
            <div id="voucherClaimPane" class="sd-voucher-pane">
                <form id="claimVoucherForm" action="{{ route('dashboard.pts.voucher.claim') }}" method="POST">
                    @csrf
                    <div class="sd-form-group">
                        <label for="claimCodeInput" class="sd-form-label">{{ __('messages.voucher_code_label') }}</label>
                        <input type="text" id="claimCodeInput" name="code" class="sd-form-input" required placeholder="{{ __('messages.enter_voucher_code_placeholder') }}" style="text-transform: uppercase; font-family: monospace;">
                    </div>
                    <button type="submit" id="claimVoucherBtn" class="sd-btn-submit sd-btn-secondary">
                        <i class="fa-solid fa-circle-check"></i>
                        <span id="claimVoucherBtnText">{{ __('messages.claim_voucher_btn') }}</span>
                    </button>
                </form>
            </div>

            <!-- Recent Vouchers List -->
            <div class="sd-vouchers-table-wrap">
                <table class="sd-vouchers-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.voucher_code') }}</th>
                            <th>{{ __('messages.voucher_value') }}</th>
                            <th>{{ __('messages.voucher_status') }}</th>
                        </tr>
                    </thead>
                    <tbody id="vouchersTableBody">
                        @if(isset($vouchers) && $vouchers->count() > 0)
                            @foreach($vouchers->take(4) as $voucher)
                                <tr id="voucher-row-{{ $voucher->id }}">
                                    <td>
                                        <span class="sd-voucher-code-badge">{{ $voucher->code }}</span>
                                        <button type="button" class="sd-voucher-copy-btn" onclick="copyVoucherCode('{{ $voucher->code }}', this)" title="{{ __('messages.voucher_copy') }}">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </td>
                                    <td><strong>{{ number_format($voucher->amount, 2) }}</strong> PTS</td>
                                    <td>
                                        @if($voucher->is_used)
                                            <span style="color: var(--sd-text-light); font-size: 11px;">
                                                <i class="fa-solid fa-check"></i> {{ __('messages.voucher_used') }}
                                            </span>
                                        @else
                                            <span style="color: var(--sd-success); font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-circle-dot"></i> {{ __('messages.voucher_unused') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="noVouchersRow">
                                <td colspan="3" style="text-align: center; color: var(--sd-text-muted); padding: 12px;">
                                    {{ __('messages.no_vouchers_yet') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <!-- 6. CAMPAIGN LAUNCHPADS & PROMOTIONAL MODULES -->
    <section class="sd-launchpads-grid">
        
        <!-- Smart Ads Studio -->
        <div class="sd-service-banner banner-smart">
            <div class="sd-service-content">
                <div class="sd-service-tag">
                    <i class="fa-solid fa-bolt"></i> {{ __('messages.smart_ads') }}
                </div>
                <h3 class="sd-service-title">{{ __('messages.smart_ads_campaign_pitch') }}</h3>
                <p class="sd-service-desc">{{ __('messages.smart_targeting_intro') }}</p>

                <div class="sd-service-stats">
                    <div class="sd-service-stat-box">
                        <p class="sd-service-stat-label">{{ __('messages.smart_admin_balance') }}</p>
                        <p class="sd-service-stat-val" id="smartBannerBalance">{{ number_format((float) $user->nsmart, 2) }}</p>
                    </div>
                    <div class="sd-service-stat-box">
                        <p class="sd-service-stat-label">{{ __('messages.smart_clicks_label') }}</p>
                        <p class="sd-service-stat-val">{{ number_format($smartAdStats['clicks']) }}</p>
                    </div>
                </div>
            </div>
            <div class="sd-service-actions">
                <a href="{{ route('ads.smart.index') }}" class="sd-btn-trans">
                    <i class="fa-solid fa-list"></i> {{ __('messages.smart_list_ads') }}
                </a>
                <a href="{{ route('ads.smart.create') }}" class="sd-btn-light">
                    <i class="fa-solid fa-plus"></i> {{ __('messages.smart_create_ad') }}
                </a>
                <a href="{{ route('legacy.state', ['ty' => 'smart', 'st' => 'vu']) }}" class="sd-btn-trans">
                    <i class="fa-solid fa-chart-line"></i> {{ __('messages.stats') }}
                </a>
            </div>
        </div>

        <!-- Custom Ads Marketplace -->
        <div class="sd-service-banner banner-custom">
            <div class="sd-service-content">
                <div class="sd-service-tag">
                    <i class="fa-solid fa-bullseye"></i> {{ __('messages.custom_ads') }}
                </div>
                <h3 class="sd-service-title">{{ __('messages.custom_ads_title') }}</h3>
                <p class="sd-service-desc">{{ __('messages.custom_ads_desc') }}</p>
            </div>
            <div class="sd-service-actions">
                <a href="{{ url('/ads/custom') }}" class="sd-btn-light">
                    <i class="fa-solid fa-gauge"></i> {{ __('messages.custom_ads_dashboard') }}
                </a>
                <a href="{{ url('/ads/custom/placements/create') }}" class="sd-btn-trans">
                    <i class="fa-solid fa-plus"></i> {{ __('messages.custom_ads_create') }}
                </a>
                <a href="{{ url('/ads/custom/marketplace') }}" class="sd-btn-trans">
                    <i class="fa-solid fa-store"></i> {{ __('messages.custom_ads_marketplace') }}
                </a>
            </div>
        </div>

        <!-- YouTube Views Exchange -->
        <div class="sd-service-banner banner-yt">
            <div class="sd-service-content">
                <div class="sd-service-tag">
                    <i class="fa-brands fa-youtube"></i> {{ __('messages.yt_exchange') }} <span style="background: #ffffff; color: #dc2626; padding: 1px 6px; border-radius: 4px; font-size: 9px; margin-inline-start: 4px;">HOT</span>
                </div>
                <h3 class="sd-service-title">{{ __('messages.yt_views_exchange') }}</h3>
                <p class="sd-service-desc">{{ __('messages.yt_exchange_desc') }}</p>

                <div class="sd-service-stats">
                    <div class="sd-service-stat-box">
                        <p class="sd-service-stat-label">{{ __('messages.pts_balance') }}</p>
                        <p class="sd-service-stat-val" id="ytBannerBalance">{{ number_format((float) $user->pts, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="sd-service-actions">
                <a href="{{ route('youtube.exchange.index') }}" class="sd-btn-trans">
                    <i class="fa-solid fa-play"></i> {{ __('messages.yt_watch_earn_btn') }}
                </a>
                <a href="{{ route('youtube.advertiser.index') }}" class="sd-btn-light">
                    <i class="fa-solid fa-plus"></i> {{ __('messages.yt_add_campaign') }}
                </a>
            </div>
        </div>

        <!-- SEO Checker Direct Audit -->
        <div class="sd-service-banner banner-seo">
            <div class="sd-service-content">
                <div class="sd-service-tag">
                    <i class="fa-solid fa-magnifying-glass-chart"></i> {{ __('messages.seo_checker') }} <span style="background: #ffffff; color: #059669; padding: 1px 6px; border-radius: 4px; font-size: 9px; margin-inline-start: 4px;">FREE</span>
                </div>
                <h3 class="sd-service-title">{{ __('messages.seo_checker') }}</h3>
                <p class="sd-service-desc">{{ __('messages.seo_checker_desc') }}</p>

                <form action="{{ route('seo_checker.analyze') }}" method="POST" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                    @csrf
                    <input type="url" name="url" placeholder="https://example.com" required style="flex: 1; min-width: 220px; padding: 10px 14px; border-radius: var(--sd-radius-md); border: none; outline: none; font-size: 13.5px; color: #1e293b;">
                    <button type="submit" class="sd-btn-light" style="color: #059669;">
                        <i class="fa-solid fa-bolt"></i> {{ __('messages.seo_analyze_now') }}
                    </button>
                </form>
            </div>
            <div class="sd-service-actions">
                <a href="{{ route('seo_checker.index') }}" class="sd-btn-trans">
                    <i class="fa-solid fa-chart-simple"></i> {{ __('messages.seo_checker') }}
                </a>
            </div>
        </div>

    </section>

    <!-- 7. REFERRAL ACCELERATOR HUB -->
    <section class="sd-referral-card">
        <div class="sd-ref-header">
            <div class="sd-ref-title-group">
                <div class="sd-ref-icon">
                    <i class="fa-solid fa-share-nodes"></i>
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; margin: 0; color: var(--sd-text-main);">
                        {{ __('messages.referral_hub_title') }}
                    </h3>
                    <span style="font-size: 13px; color: var(--sd-text-muted); display: block; margin-top: 2px;">
                        {{ __('messages.referral_hub_desc') }}
                    </span>
                </div>
            </div>
            <span style="background: var(--sd-success-soft); color: var(--sd-success); border: 1px solid rgba(16, 185, 129, 0.25); padding: 6px 14px; border-radius: var(--sd-radius-pill); font-weight: 700; font-size: 12.5px;">
                <i class="fa-solid fa-gift me-1"></i> {{ __('messages.pts_per_referral') }}
            </span>
        </div>

        <!-- Referral Metrics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
            <div style="background: var(--sd-surface-alt); border: 1px solid var(--sd-border); padding: 14px 18px; border-radius: var(--sd-radius-md); text-align: center;">
                <span style="font-size: 11px; color: var(--sd-text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 4px;">
                    {{ __('messages.total_referrals') }}
                </span>
                <strong style="font-size: 24px; color: var(--sd-success);">{{ number_format($referralStats['total'] ?? 0) }}</strong>
            </div>
            <div style="background: var(--sd-surface-alt); border: 1px solid var(--sd-border); padding: 14px 18px; border-radius: var(--sd-radius-md); text-align: center;">
                <span style="font-size: 11px; color: var(--sd-text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 4px;">
                    {{ __('messages.points_earned_referral') }}
                </span>
                <strong style="font-size: 24px; color: var(--sd-warning);">{{ number_format($referralStats['pts'] ?? 0) }} PTS</strong>
            </div>
        </div>

        <!-- 1-Click Referral Link Copy -->
        @php $homeRefUrl = url('/') . '?ref=' . $user->publicRouteIdentifier(); @endphp
        <div class="sd-ref-copy-bar">
            <input type="text" id="sdHomeRefInput" value="{{ $homeRefUrl }}" readonly class="sd-ref-input">
            <button type="button" id="sdHomeCopyBtn" onclick="copyReferralLink()" class="sd-ref-copy-btn">
                <i class="fa-solid fa-copy"></i>
                <span id="sdHomeCopyText">{{ __('messages.copy_link') }}</span>
            </button>
        </div>

        <!-- Secondary Referral Action Buttons -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('ads.referrals') }}" class="sd-btn-light" style="background: var(--sd-primary-soft); color: var(--sd-primary); border: 1px solid var(--sd-border-strong);">
                <i class="fa-solid fa-code"></i> {{ __('messages.referral_banners_codes') }}
            </a>
            <a href="{{ route('legacy.referral') }}" class="sd-btn-light" style="background: var(--sd-surface-alt); color: var(--sd-text-main); border: 1px solid var(--sd-border);">
                <i class="fa-solid fa-users"></i> {{ __('messages.my_referrals_list') }}
            </a>
        </div>
    </section>

    <!-- 8. BOTTOM ADS BANNER SLOT -->
    {!! ads_site(2) !!}
</div>

<!-- TOAST CONTAINER FOR SEAMLESS AJAX FEEDBACK -->
<div id="sdToastContainer" class="sd-toast-container"></div>

<!-- ==========================================================================
     LIGHTWEIGHT CLIENT CONTROLLER (AJAX & INTERACTIVITY)
     Zero Heavy Libraries — Native Fetch API + Responsive DOM Updates
     ========================================================================== -->
<script>
(function() {
    'use strict';

    // State
    const userState = {
        pts: {{ (float) $user->pts }},
        nlink: {{ (float) $user->nlink }},
        nvu: {{ (float) $user->nvu }},
        vu: {{ (float) $user->vu }},
        nsmart: {{ (float) $user->nsmart }},
        smartDivisor: {{ (float) ($smartDivisor ?: 4) }}
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Toast Notification System
    window.showToast = function(type, message) {
        const container = document.getElementById('sdToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `sd-toast sd-toast-${type}`;

        let icon = 'fa-solid fa-circle-check';
        if (type === 'error') icon = 'fa-solid fa-circle-exclamation';
        if (type === 'info') icon = 'fa-solid fa-circle-info';

        toast.innerHTML = `<i class="${icon}"></i> <span>${message}</span>`;
        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    };

    // Update all live DOM counters across the page atomically
    function syncBalances(balances) {
        if (!balances) return;

        if (typeof balances.pts !== 'undefined') {
            userState.pts = parseFloat(balances.pts);
            const formatted = userState.pts.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const heroEl = document.getElementById('heroPtsDisplay');
            if (heroEl) heroEl.innerText = formatted;
            const convertAvailEl = document.getElementById('convertAvailablePts');
            if (convertAvailEl) convertAvailEl.innerText = formatted;
            const transferAvailEl = document.getElementById('transferAvailablePts');
            if (transferAvailEl) transferAvailEl.innerText = formatted;
            const ytBannerEl = document.getElementById('ytBannerBalance');
            if (ytBannerEl) ytBannerEl.innerText = formatted;
        }

        if (typeof balances.nlink !== 'undefined') {
            userState.nlink = parseFloat(balances.nlink);
            const formatted = Math.floor(userState.nlink).toLocaleString();
            const el1 = document.getElementById('pillNlink');
            if (el1) el1.innerText = formatted;
            const el2 = document.getElementById('kpiNlink');
            if (el2) el2.innerText = formatted;
        }

        if (typeof balances.nvu !== 'undefined') {
            userState.nvu = parseFloat(balances.nvu);
            const formatted = Math.floor(userState.nvu).toLocaleString();
            const el1 = document.getElementById('pillNvu');
            if (el1) el1.innerText = formatted;
            const el2 = document.getElementById('kpiNvu');
            if (el2) el2.innerText = formatted;
        }

        if (typeof balances.vu !== 'undefined') {
            userState.vu = parseFloat(balances.vu);
            const formatted = Math.floor(userState.vu).toLocaleString();
            const el1 = document.getElementById('pillVu');
            if (el1) el1.innerText = formatted;
            const el2 = document.getElementById('kpiVu');
            if (el2) el2.innerText = formatted;
        }

        if (typeof balances.nsmart !== 'undefined') {
            userState.nsmart = parseFloat(balances.nsmart);
            const formatted = userState.nsmart.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const el1 = document.getElementById('pillNsmart');
            if (el1) el1.innerText = formatted;
            const el2 = document.getElementById('kpiNsmart');
            if (el2) el2.innerText = formatted;
            const el3 = document.getElementById('smartBannerBalance');
            if (el3) el3.innerText = formatted;
        }

        updateConvertPreview();
    }

    // Live conversion calculator
    function updateConvertPreview() {
        const input = document.getElementById('convertPtsInput');
        const select = document.getElementById('convertToSelect');
        const previewEl = document.getElementById('convertPreviewText');
        if (!input || !select || !previewEl) return;

        const pts = parseFloat(input.value) || 0;
        const selectedOpt = select.options[select.selectedIndex];
        const ratio = parseFloat(selectedOpt?.dataset?.ratio) || 0.5;
        const type = select.value;

        if (pts <= 0) {
            previewEl.innerText = "{{ __('messages.convert_preview_hint') }}: 0";
            return;
        }

        const calculated = pts * ratio;
        let unit = "{{ __('messages.Clicks') }}";
        if (type === 'banners') unit = "{{ __('messages.Views') }}";
        if (type === 'exchv') unit = "{{ __('messages.visits') }}";
        if (type === 'smartads') unit = "{{ __('messages.smart_ads') }} {{ __('messages.credits') }}";

        const formattedCalc = type === 'smartads' ? calculated.toFixed(2) : Math.floor(calculated).toLocaleString();
        previewEl.innerText = `+${formattedCalc} ${unit}`;
    }

    window.setConvertAmount = function(amount) {
        const input = document.getElementById('convertPtsInput');
        if (!input) return;
        const current = parseFloat(input.value) || 0;
        input.value = current + amount;
        updateConvertPreview();
    };

    window.setConvertAll = function() {
        const input = document.getElementById('convertPtsInput');
        if (!input) return;
        input.value = Math.floor(userState.pts);
        updateConvertPreview();
    };

    // Voucher Tabs Switcher
    window.switchVoucherTab = function(tab, button) {
        document.querySelectorAll('.sd-voucher-tab').forEach(b => b.classList.remove('active'));
        if (button) button.classList.add('active');

        const genPane = document.getElementById('voucherGeneratePane');
        const claimPane = document.getElementById('voucherClaimPane');
        if (tab === 'generate') {
            if (genPane) genPane.classList.add('active');
            if (claimPane) claimPane.classList.remove('active');
        } else {
            if (claimPane) claimPane.classList.add('active');
            if (genPane) genPane.classList.remove('active');
        }
    };

    // Copy Voucher Code Helper
    window.copyVoucherCode = function(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('info', "{{ __('messages.voucher_copied') }}: " + code);
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check" style="color: var(--sd-success)"></i>';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            }
        }).catch(() => {
            // Fallback
            const temp = document.createElement('textarea');
            temp.value = code;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            temp.remove();
            showToast('info', "{{ __('messages.voucher_copied') }}: " + code);
        });
    };

    // Copy Referral Link Helper
    window.copyReferralLink = function() {
        const input = document.getElementById('sdHomeRefInput');
        const btn = document.getElementById('sdHomeCopyBtn');
        const text = document.getElementById('sdHomeCopyText');
        if (!input) return;

        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            showToast('success', "{{ __('messages.link_copied') }}");
            if (btn && text) {
                const orig = text.innerText;
                text.innerText = "{{ __('messages.link_copied') }}";
                btn.style.background = 'var(--sd-success)';
                setTimeout(() => {
                    text.innerText = orig;
                    btn.style.background = '';
                }, 2500);
            }
        }).catch(() => {
            document.execCommand('copy');
            showToast('success', "{{ __('messages.link_copied') }}");
        });
    };

    // --------------------------------------------------------------------------
    // AJAX FORM HANDLERS
    // --------------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener for conversion calculation
        const convertPtsInput = document.getElementById('convertPtsInput');
        const convertToSelect = document.getElementById('convertToSelect');
        if (convertPtsInput) convertPtsInput.addEventListener('input', updateConvertPreview);
        if (convertToSelect) convertToSelect.addEventListener('change', updateConvertPreview);

        // 1. AJAX: Convert Points Form
        const convertForm = document.getElementById('convertPointsForm');
        const convertBtn = document.getElementById('convertSubmitBtn');
        const convertBtnText = document.getElementById('convertBtnText');

        if (convertForm) {
            convertForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const pts = parseFloat(convertPtsInput.value) || 0;
                if (pts <= 0) {
                    showToast('error', "{{ __('messages.points_must_be_positive') }}");
                    return;
                }
                if (pts > userState.pts) {
                    showToast('error', "{{ __('messages.insufficient_points', ['current' => number_format($user->pts, 2)]) }}");
                    return;
                }

                // Loading state
                convertBtn.disabled = true;
                const origText = convertBtnText.innerText;
                convertBtnText.innerText = "{{ __('messages.converting') }}";

                const formData = new FormData(convertForm);

                fetch(convertForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    convertBtn.disabled = false;
                    convertBtnText.innerText = origText;

                    if (status >= 200 && status < 300 && body.success) {
                        showToast('success', body.message);
                        convertPtsInput.value = '';
                        updateConvertPreview();
                        syncBalances(body.balances);
                    } else {
                        showToast('error', body.message || "{{ __('messages.error_occurred') }}");
                    }
                })
                .catch(err => {
                    convertBtn.disabled = false;
                    convertBtnText.innerText = origText;
                    showToast('error', "{{ __('messages.error_occurred') }}");
                });
            });
        }

        // 2. AJAX: Transfer Points Form
        const transferForm = document.getElementById('transferPtsForm');
        const transferBtn = document.getElementById('transferSubmitBtn');
        const transferBtnText = document.getElementById('transferBtnText');
        const transferUsernameInput = document.getElementById('transferUsernameInput');
        const transferAmountInput = document.getElementById('transferAmountInput');

        if (transferForm) {
            transferForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const amount = parseFloat(transferAmountInput.value) || 0;
                const recipient = transferUsernameInput.value.trim();

                if (!recipient) {
                    showToast('error', "{{ __('messages.enter_username_placeholder') }}");
                    return;
                }
                if (amount <= 0) {
                    showToast('error', "{{ __('messages.points_must_be_positive') }}");
                    return;
                }
                if (amount > userState.pts) {
                    showToast('error', "{{ __('messages.insufficient_points', ['current' => number_format($user->pts, 2)]) }}");
                    return;
                }

                transferBtn.disabled = true;
                const origText = transferBtnText.innerText;
                transferBtnText.innerText = "{{ __('messages.transferring') }}";

                const formData = new FormData(transferForm);

                fetch(transferForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    transferBtn.disabled = false;
                    transferBtnText.innerText = origText;

                    if (status >= 200 && status < 300 && body.success) {
                        showToast('success', body.message);
                        transferAmountInput.value = '';
                        transferUsernameInput.value = '';
                        syncBalances(body.balances);
                    } else {
                        showToast('error', body.message || "{{ __('messages.error_occurred') }}");
                    }
                })
                .catch(err => {
                    transferBtn.disabled = false;
                    transferBtnText.innerText = origText;
                    showToast('error', "{{ __('messages.error_occurred') }}");
                });
            });
        }

        // 3. AJAX: Generate Voucher Form
        const genForm = document.getElementById('generateVoucherForm');
        const genBtn = document.getElementById('generateVoucherBtn');
        const genBtnText = document.getElementById('generateVoucherBtnText');
        const voucherAmountInput = document.getElementById('voucherAmountInput');

        if (genForm) {
            genForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const amount = parseFloat(voucherAmountInput.value) || 0;
                if (amount <= 0) {
                    showToast('error', "{{ __('messages.points_must_be_positive') }}");
                    return;
                }
                if (amount > userState.pts) {
                    showToast('error', "{{ __('messages.insufficient_points', ['current' => number_format($user->pts, 2)]) }}");
                    return;
                }

                genBtn.disabled = true;
                const origText = genBtnText.innerText;
                genBtnText.innerText = "{{ __('messages.generating') }}";

                const formData = new FormData(genForm);

                fetch(genForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    genBtn.disabled = false;
                    genBtnText.innerText = origText;

                    if (status >= 200 && status < 300 && body.success) {
                        showToast('success', body.message);
                        voucherAmountInput.value = '';
                        syncBalances(body.balances);

                        // Prepend newly generated voucher to table
                        if (body.voucher) {
                            const noRow = document.getElementById('noVouchersRow');
                            if (noRow) noRow.remove();

                            const tbody = document.getElementById('vouchersTableBody');
                            if (tbody) {
                                const tr = document.createElement('tr');
                                tr.id = `voucher-row-${body.voucher.id}`;
                                tr.innerHTML = `
                                    <td>
                                        <span class="sd-voucher-code-badge">${body.voucher.code}</span>
                                        <button type="button" class="sd-voucher-copy-btn" onclick="copyVoucherCode('${body.voucher.code}', this)" title="{{ __('messages.voucher_copy') }}">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </td>
                                    <td><strong>${body.voucher.amount.toFixed(2)}</strong> PTS</td>
                                    <td>
                                        <span style="color: var(--sd-success); font-size: 11px; font-weight: 700;">
                                            <i class="fa-solid fa-circle-dot"></i> {{ __('messages.voucher_unused') }}
                                        </span>
                                    </td>
                                `;
                                tbody.insertBefore(tr, tbody.firstChild);
                            }
                        }
                    } else {
                        showToast('error', body.message || "{{ __('messages.error_occurred') }}");
                    }
                })
                .catch(err => {
                    genBtn.disabled = false;
                    genBtnText.innerText = origText;
                    showToast('error', "{{ __('messages.error_occurred') }}");
                });
            });
        }

        // 4. AJAX: Claim Voucher Form
        const claimForm = document.getElementById('claimVoucherForm');
        const claimBtn = document.getElementById('claimVoucherBtn');
        const claimBtnText = document.getElementById('claimVoucherBtnText');
        const claimCodeInput = document.getElementById('claimCodeInput');

        if (claimForm) {
            claimForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const code = claimCodeInput.value.trim();
                if (!code) {
                    showToast('error', "{{ __('messages.enter_voucher_code_placeholder') }}");
                    return;
                }

                claimBtn.disabled = true;
                const origText = claimBtnText.innerText;
                claimBtnText.innerText = "{{ __('messages.claiming') }}";

                const formData = new FormData(claimForm);

                fetch(claimForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    claimBtn.disabled = false;
                    claimBtnText.innerText = origText;

                    if (status >= 200 && status < 300 && body.success) {
                        showToast('success', body.message);
                        claimCodeInput.value = '';
                        syncBalances(body.balances);

                        // If voucher code is in the table, update its status
                        if (body.code) {
                            document.querySelectorAll('.sd-voucher-code-badge').forEach(el => {
                                if (el.innerText.trim().toUpperCase() === body.code.toUpperCase()) {
                                    const row = el.closest('tr');
                                    if (row) {
                                        const statusTd = row.children[2];
                                        if (statusTd) {
                                            statusTd.innerHTML = `<span style="color: var(--sd-text-light); font-size: 11px;"><i class="fa-solid fa-check"></i> {{ __('messages.voucher_used') }}</span>`;
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        showToast('error', body.message || "{{ __('messages.error_occurred') }}");
                    }
                })
                .catch(err => {
                    claimBtn.disabled = false;
                    claimBtnText.innerText = origText;
                    showToast('error', "{{ __('messages.error_occurred') }}");
                });
            });
        }
    });
})();
</script>
@endsection
