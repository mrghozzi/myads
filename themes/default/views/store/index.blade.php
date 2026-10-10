@extends('theme::layouts.master')

@section('content')
<style>
    /* ==========================================================================
       WORLD-CLASS STORE DESIGN SYSTEM (LIGHT & DARK MODE)
       ========================================================================== */
    :root {
        --store-primary: #615dfa;
        --store-primary-hover: #4e4ac8;
        --store-primary-rgb: 97, 93, 250;
        --store-accent: #23d2e2;
        --store-accent-rgb: 35, 210, 226;
        --store-green: #10b981;
        --store-amber: #f59e0b;
        --store-rose: #f43f5e;
        --store-purple: #8b5cf6;
        
        /* Light Theme Tokens */
        --store-bg: #f8faff;
        --store-card-bg: #ffffff;
        --store-card-hover-bg: #ffffff;
        --store-surface: #ffffff;
        --store-surface-soft: #f4f6fc;
        --store-border: rgba(100, 116, 139, 0.12);
        --store-border-subtle: rgba(100, 116, 139, 0.08);
        --store-border-hover: rgba(97, 93, 250, 0.35);
        --store-text: #1e293b;
        --store-text-heading: #0f172a;
        --store-text-muted: #64748b;
        --store-text-subtle: #94a3b8;
        --store-shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
        --store-shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        --store-shadow-lg: 0 20px 40px -10px rgba(15, 23, 42, 0.1);
        --store-shadow-glow: 0 0 35px rgba(97, 93, 250, 0.25);
    }

    body[data-theme="css_d"],
    body.dark-mode {
        --store-bg: #0b1120;
        --store-card-bg: #161f33;
        --store-card-hover-bg: #1b2640;
        --store-surface: #131b2e;
        --store-surface-soft: #1e293b;
        --store-border: rgba(255, 255, 255, 0.08);
        --store-border-subtle: rgba(255, 255, 255, 0.05);
        --store-border-hover: rgba(97, 93, 250, 0.5);
        --store-text: #e2e8f0;
        --store-text-heading: #ffffff;
        --store-text-muted: #94a3b8;
        --store-text-subtle: #64748b;
        --store-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
        --store-shadow-md: 0 12px 30px rgba(0, 0, 0, 0.35);
        --store-shadow-lg: 0 24px 48px rgba(0, 0, 0, 0.5);
        --store-shadow-glow: 0 0 40px rgba(97, 93, 250, 0.35);
    }

    .store-page-root {
        display: flex;
        flex-direction: column;
        gap: 32px;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    /* ==========================================================================
       HERO BANNER (WORLD-CLASS GLOBAL MARKETPLACE STYLE)
       ========================================================================== */
    .store-hero-banner {
        position: relative;
        border-radius: 24px;
        background: linear-gradient(135deg, #4338ca 0%, #615dfa 45%, #7c3aed 100%);
        padding: 44px 36px;
        color: #ffffff;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(79, 70, 229, 0.25);
    }

    .store-hero-banner::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(35, 210, 226, 0.35) 0%, rgba(97, 93, 250, 0.15) 50%, transparent 75%);
        border-radius: 50%;
        filter: blur(40px);
        pointer-events: none;
    }

    .store-hero-banner::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -100px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(244, 63, 94, 0.25) 0%, rgba(139, 92, 246, 0.15) 50%, transparent 75%);
        border-radius: 50%;
        filter: blur(40px);
        pointer-events: none;
    }

    .store-hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .store-hero-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .store-hero-title-group {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .store-hero-icon-bubble {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .store-hero-icon-bubble img {
        width: 44px;
        height: 44px;
        object-fit: contain;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    }

    .store-hero-titles {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .store-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.25);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        width: fit-content;
        backdrop-filter: blur(8px);
    }

    .store-hero-titles h1 {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #ffffff;
        line-height: 1.2;
    }

    .store-hero-titles p {
        margin: 0;
        font-size: 15px;
        color: rgba(255, 255, 255, 0.88);
        max-width: 650px;
        line-height: 1.5;
    }

    .store-hero-actions-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hero-glass-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hero-glass-chip:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .hero-glass-chip.btn-primary-action {
        background: #ffffff;
        color: #4338ca;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .hero-glass-chip.btn-primary-action:hover {
        background: #f8fafc;
        color: #3730a3;
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.2);
    }

    .hero-stats-strip {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .hero-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        background: rgba(0, 0, 0, 0.12);
        padding: 6px 14px;
        border-radius: 999px;
    }

    .hero-stat-pill i {
        color: var(--store-accent);
    }

    /* ==========================================================================
       CATEGORY SHOWCASE SECTION
       ========================================================================== */
    .store-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .section-title-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .section-pretitle {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--store-primary);
        margin: 0;
    }

    .section-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--store-text-heading);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .modern-category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 18px;
    }

    .modern-category-card {
        position: relative;
        border-radius: 20px;
        padding: 24px;
        color: #ffffff;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 2px solid transparent;
        cursor: pointer;
    }

    .modern-category-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 16px 35px rgba(0, 0, 0, 0.2);
        color: #ffffff;
    }

    .modern-category-card.active {
        border-color: #ffffff;
        box-shadow: 0 0 0 4px var(--store-primary), 0 16px 35px rgba(97, 93, 250, 0.4);
        transform: translateY(-4px);
    }

    .modern-category-card.active::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 0 10px #ffffff;
        z-index: 3;
    }

    [dir="rtl"] .modern-category-card.active::before {
        left: auto;
        right: 10px;
    }

    .cat-card-header {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
    }

    .cat-title-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-width: 70%;
    }

    .cat-card-title {
        font-size: 19px;
        font-weight: 800;
        margin: 0;
        line-height: 1.25;
        color: #ffffff;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    }

    .cat-card-desc {
        font-size: 12px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        line-height: 1.35;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .cat-card-badge {
        background: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        flex-shrink: 0;
    }

    .cat-card-bg-img {
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 115px;
        height: 115px;
        opacity: 0.75;
        z-index: 1;
        transform: rotate(-6deg);
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center right;
        filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.25));
        transition: transform 0.35s ease, opacity 0.35s ease;
    }

    [dir="rtl"] .cat-card-bg-img {
        right: auto;
        left: -10px;
        transform: rotate(6deg);
        background-position: center left;
    }

    .modern-category-card:hover .cat-card-bg-img {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.95;
    }

    /* Gradients per category */
    .cat-all { background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%); }
    .cat-script { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); }
    .cat-themes { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
    .cat-plugins { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); }
    .cat-graphics { background: linear-gradient(135deg, #db2777 0%, #9d174d 100%); }
    .cat-audio { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); }
    .cat-video { background: linear-gradient(135deg, #ea580c 0%, #9a3412 100%); }
    .cat-ebooks { background: linear-gradient(135deg, #059669 0%, #065f46 100%); }
    .cat-software { background: linear-gradient(135deg, #d97706 0%, #92400e 100%); }
    .cat-courses { background: linear-gradient(135deg, #0891b2 0%, #155e75 100%); }

    /* ==========================================================================
       INTERACTIVE SMART FILTER & CONTROLS TOOLBAR
       ========================================================================== */
    .store-filter-toolbar {
        position: sticky;
        top: 88px;
        z-index: 100;
        background: var(--store-card-bg);
        border: 1px solid var(--store-border);
        border-radius: 18px;
        padding: 14px 20px;
        box-shadow: var(--store-shadow-md);
        display: flex;
        flex-direction: column;
        gap: 14px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: all 0.25s ease;
    }

    .toolbar-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .toolbar-search-box {
        position: relative;
        flex: 1 1 280px;
        max-width: 480px;
    }

    .toolbar-search-input {
        width: 100%;
        height: 44px;
        padding: 0 42px;
        border-radius: 12px;
        border: 1px solid var(--store-border);
        background: var(--store-surface-soft);
        color: var(--store-text);
        font-size: 14px;
        font-weight: 500;
        outline: none;
        transition: all 0.2s ease;
    }

    .toolbar-search-input:focus {
        border-color: var(--store-primary);
        background: var(--store-card-bg);
        box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
    }

    .search-input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: var(--store-text-muted);
        font-size: 15px;
        pointer-events: none;
    }

    [dir="rtl"] .search-input-icon {
        left: auto;
        right: 14px;
    }

    .search-clear-btn {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: var(--store-text-muted);
        font-size: 14px;
        cursor: pointer;
        padding: 4px;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: color 0.15s ease;
    }

    [dir="rtl"] .search-clear-btn {
        right: auto;
        left: 12px;
    }

    .search-clear-btn:hover {
        color: var(--store-rose);
    }

    .search-spinner {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border: 2px solid rgba(97, 93, 250, 0.2);
        border-top-color: var(--store-primary);
        border-radius: 50%;
        display: none;
        animation: spin 0.7s linear infinite;
    }

    [dir="rtl"] .search-spinner {
        right: auto;
        left: 12px;
    }

    @keyframes spin {
        to { transform: translateY(-50%) rotate(360deg); }
    }

    .toolbar-controls-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .toolbar-select-wrap {
        position: relative;
    }

    .toolbar-select {
        height: 44px;
        padding: 0 36px 0 16px;
        border-radius: 12px;
        border: 1px solid var(--store-border);
        background: var(--store-surface-soft);
        color: var(--store-text);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        min-width: 160px;
        transition: all 0.2s ease;
    }

    [dir="rtl"] .toolbar-select {
        padding: 0 16px 0 36px;
    }

    .toolbar-select:focus {
        border-color: var(--store-primary);
        box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
    }

    .toolbar-select-icon {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        color: var(--store-text-muted);
        pointer-events: none;
        font-size: 11px;
    }

    [dir="rtl"] .toolbar-select-icon {
        right: auto;
        left: 12px;
    }

    /* View Switcher */
    .view-switcher-box {
        display: inline-flex;
        background: var(--store-surface-soft);
        padding: 3px;
        border-radius: 12px;
        border: 1px solid var(--store-border);
    }

    .view-btn {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        border: none;
        background: transparent;
        color: var(--store-text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 15px;
    }

    .view-btn.active {
        background: var(--store-card-bg);
        color: var(--store-primary);
        box-shadow: var(--store-shadow-sm);
    }

    .view-btn:hover:not(.active) {
        color: var(--store-text);
    }

    /* Filter Quick Pills Row */
    .toolbar-quick-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
    }

    .toolbar-quick-pills::-webkit-scrollbar {
        display: none;
    }

    .quick-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: var(--store-surface-soft);
        border: 1px solid var(--store-border);
        color: var(--store-text-muted);
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .quick-pill:hover {
        background: var(--store-card-bg);
        border-color: var(--store-primary);
        color: var(--store-primary);
    }

    .quick-pill.active {
        background: var(--store-primary);
        border-color: var(--store-primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
    }

    /* ==========================================================================
       RESULTS & PRODUCT CARDS (GRID & LIST)
       ========================================================================== */
    .store-results-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .store-results-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .results-count-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .count-badge {
        background: rgba(97, 93, 250, 0.12);
        color: var(--store-primary);
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 13px;
    }

    .count-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--store-text-muted);
    }

    .active-filters-chips {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        background: var(--store-surface-soft);
        border: 1px solid var(--store-border);
        font-size: 12px;
        font-weight: 600;
        color: var(--store-text);
    }

    .filter-chip i {
        color: var(--store-primary);
    }

    .chip-remove {
        background: transparent;
        border: none;
        color: var(--store-text-muted);
        cursor: pointer;
        padding: 0 2px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1;
    }

    .chip-remove:hover {
        color: var(--store-rose);
    }

    .clear-all-filters-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: var(--store-rose);
        text-decoration: none;
        padding: 4px 8px;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }

    .clear-all-filters-btn:hover {
        opacity: 0.8;
        color: var(--store-rose);
    }

    /* Product Grid */
    .modern-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        transition: opacity 0.25s ease;
    }

    /* Card Base */
    .modern-product-card {
        background: var(--store-card-bg);
        border: 1px solid var(--store-border);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        box-shadow: var(--store-shadow-sm);
        position: relative;
    }

    .modern-product-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--store-shadow-md);
        border-color: var(--store-border-hover);
    }

    /* Media Wrapper */
    .product-media-wrapper {
        position: relative;
        width: 100%;
        padding-top: 60%; /* 16:10 */
        background: var(--store-surface-soft);
        overflow: hidden;
    }

    .product-thumb-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
    }

    .product-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modern-product-card:hover .product-thumb-img {
        transform: scale(1.06);
    }

    .product-media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
    }

    .modern-product-card:hover .product-media-overlay {
        opacity: 1;
    }

    .preview-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.95);
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(8px);
        transition: transform 0.25s ease;
    }

    .modern-product-card:hover .preview-btn {
        transform: translateY(0);
    }

    /* Badges */
    .product-floating-badges {
        position: absolute;
        top: 12px;
        display: flex;
        gap: 6px;
        z-index: 3;
    }

    .start-badges { left: 12px; }
    [dir="rtl"] .start-badges { left: auto; right: 12px; }

    .end-badges { right: 12px; }
    [dir="rtl"] .end-badges { right: auto; left: 12px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.3px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }

    .pill-free {
        background: #10b981;
        color: #ffffff;
    }

    .pill-price {
        background: rgba(255, 255, 255, 0.95);
        color: #0f172a;
    }

    body[data-theme="css_d"] .pill-price,
    body.dark-mode .pill-price {
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .pill-sale {
        background: #ef4444;
        color: #ffffff;
    }

    .pill-sale .old-price {
        text-decoration: line-through;
        opacity: 0.75;
        font-size: 10px;
    }

    .pill-downloads {
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
    }

    .pill-demo {
        background: rgba(97, 93, 250, 0.9);
        color: #ffffff;
        transition: background 0.2s ease;
    }

    .pill-demo:hover {
        background: #4e4ac8;
        color: #ffffff;
    }

    .pill-pending {
        background: #f59e0b;
        color: #ffffff;
    }

    .pill-suspended {
        background: #0f172a;
        color: #ffffff;
    }

    /* Product Info Area */
    .product-info-wrapper {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 12px;
    }

    .product-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .product-category-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--store-primary);
        text-decoration: none;
        letter-spacing: 0.5px;
    }

    .product-category-chip:hover {
        color: var(--store-primary-hover);
    }

    .product-rating-box {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }

    .rating-stars {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        color: #f59e0b;
        font-weight: 700;
    }

    .rating-count {
        color: var(--store-text-muted);
        font-size: 11px;
    }

    .rating-new {
        color: var(--store-text-muted);
        font-size: 11px;
        font-weight: 600;
    }

    .product-card-title {
        font-size: 17px;
        font-weight: 800;
        margin: 0;
        line-height: 1.35;
        color: var(--store-text-heading);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-card-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .product-card-title a:hover {
        color: var(--store-primary);
    }

    .product-card-desc {
        font-size: 13px;
        line-height: 1.5;
        color: var(--store-text-muted);
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    /* Creator Profile Row */
    .product-creator-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
        border-top: 1px solid var(--store-border);
    }

    .creator-profile {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .creator-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        background: var(--store-surface-soft);
    }

    .creator-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .creator-meta {
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .creator-label {
        font-size: 11px;
        color: var(--store-text-muted);
    }

    .creator-name {
        font-size: 12px;
        font-weight: 700;
        color: var(--store-text);
        text-decoration: none;
    }

    .creator-name:hover {
        color: var(--store-primary);
    }

    .version-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: var(--store-surface-soft);
        color: var(--store-text-muted);
    }

    /* Card Footer */
    .product-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid var(--store-border);
        margin-top: auto;
    }

    .price-display-box {
        display: flex;
        flex-direction: column;
    }

    .price-main {
        font-size: 18px;
        font-weight: 900;
        color: var(--store-text-heading);
        line-height: 1;
    }

    .price-main small {
        font-size: 12px;
        font-weight: 700;
        color: var(--store-primary);
    }

    .price-main.free-text {
        color: #10b981;
    }

    .sale-price-group {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .sale-current {
        font-size: 18px;
        font-weight: 900;
        color: #ef4444;
        line-height: 1;
    }

    .sale-current small {
        font-size: 12px;
        font-weight: 700;
    }

    .sale-original {
        font-size: 12px;
        color: var(--store-text-muted);
        text-decoration: line-through;
    }

    .card-action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-card-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-view-action {
        background: var(--store-primary);
        color: #ffffff;
    }

    .btn-view-action:hover {
        background: var(--store-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.3);
    }

    .btn-demo-action {
        background: var(--store-surface-soft);
        color: var(--store-text);
        border: 1px solid var(--store-border);
    }

    .btn-demo-action:hover {
        background: var(--store-card-hover-bg);
        border-color: var(--store-primary);
        color: var(--store-primary);
    }

    /* ==========================================================================
       LIST VIEW MODE STYLES
       ========================================================================== */
    .modern-product-grid.list-view {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .modern-product-grid.list-view .modern-product-card {
        flex-direction: row;
        align-items: stretch;
    }

    .modern-product-grid.list-view .product-media-wrapper {
        width: 260px;
        min-width: 260px;
        padding-top: 0;
        height: auto;
    }

    @media (max-width: 768px) {
        .modern-product-grid.list-view .modern-product-card {
            flex-direction: column;
        }
        .modern-product-grid.list-view .product-media-wrapper {
            width: 100%;
            min-width: 100%;
            padding-top: 60%;
        }
    }

    /* ==========================================================================
       EMPTY STATE
       ========================================================================== */
    .store-empty-state {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 60px 24px;
        background: var(--store-card-bg);
        border: 2px dashed var(--store-border);
        border-radius: 24px;
        gap: 16px;
    }

    .empty-icon-bubble {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(97, 93, 250, 0.1);
        color: var(--store-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        margin-bottom: 4px;
    }

    .empty-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--store-text-heading);
        margin: 0;
    }

    .empty-text {
        font-size: 14px;
        color: var(--store-text-muted);
        max-width: 440px;
        margin: 0;
        line-height: 1.6;
    }

    .empty-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .btn-reset-filters,
    .btn-publish-first {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-reset-filters {
        background: var(--store-surface-soft);
        color: var(--store-text);
        border: 1px solid var(--store-border);
    }

    .btn-reset-filters:hover {
        background: var(--store-card-bg);
        border-color: var(--store-primary);
        color: var(--store-primary);
    }

    .btn-publish-first {
        background: var(--store-primary);
        color: #ffffff;
    }

    .btn-publish-first:hover {
        background: var(--store-primary-hover);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* ==========================================================================
       SKELETON SHIMMER LOADER
       ========================================================================== */
    .skeleton-grid {
        display: none;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }

    .skeleton-card {
        background: var(--store-card-bg);
        border: 1px solid var(--store-border);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .skeleton-media {
        width: 100%;
        padding-top: 60%;
        background: var(--store-surface-soft);
        position: relative;
        overflow: hidden;
    }

    .skeleton-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .skeleton-line {
        height: 14px;
        border-radius: 6px;
        background: var(--store-surface-soft);
        position: relative;
        overflow: hidden;
    }

    .skeleton-line.w-40 { width: 40%; }
    .skeleton-line.w-80 { width: 80%; height: 18px; }
    .skeleton-line.w-60 { width: 60%; }
    .skeleton-line.w-100 { width: 100%; }

    .skeleton-shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0,
            rgba(255, 255, 255, 0.2) 20%,
            rgba(255, 255, 255, 0.5) 60%,
            rgba(255, 255, 255, 0)
        );
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    /* ==========================================================================
       PAGINATION
       ========================================================================== */
    .store-pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .store-pagination-wrapper .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }

    .store-pagination-wrapper .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid var(--store-border);
        background: var(--store-card-bg);
        color: var(--store-text);
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .store-pagination-wrapper .page-item.active .page-link {
        background: var(--store-primary);
        border-color: var(--store-primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.3);
    }

    .store-pagination-wrapper .page-item .page-link:hover:not(.active) {
        border-color: var(--store-primary);
        color: var(--store-primary);
        background: var(--store-surface-soft);
    }

    .store-pagination-wrapper .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ==========================================================================
       RTL MIRRORING
       ========================================================================== */
    [dir="rtl"] .dir-aware-arrow {
        transform: scaleX(-1);
    }
</style>

<div class="store-page-root">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 16px; border: none; background: #ecfdf5; color: #065f46; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: inherit;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- ========================================================================
         HERO SHOWCASE BANNER
         ======================================================================== -->
    <header class="store-hero-banner">
        <div class="store-hero-inner">
            <div class="store-hero-header-row">
                <div class="store-hero-title-group">
                    <div class="store-hero-icon-bubble">
                        <img src="{{ theme_asset('img/banner/marketplace-icon.png') }}" alt="Store Marketplace" onerror="this.onerror=null;this.src='{{ asset('upload/app/marketplace.png') }}';">
                    </div>
                    <div class="store-hero-titles">
                        <span class="store-hero-badge">
                            <i class="fa-solid fa-sparkles text-warning"></i>
                            <span>{{ __('messages.marketplace_digital_hub') ?? 'Digital Marketplace Hub' }}</span>
                        </span>
                        <h1>{{ __('messages.store') }}</h1>
                        <p>{{ __('messages.store_banner_desc') ?? 'Discover, purchase, and download premium scripts, themes, templates, and digital assets.' }}</p>
                    </div>
                </div>

                <div class="store-hero-actions-group">
                    @auth
                        <a href="{{ route('store.my_purchases') }}" class="hero-glass-chip">
                            <i class="fa-solid fa-box-open"></i>
                            <span>{{ __('messages.my_purchases') ?? 'My Purchases' }}</span>
                        </a>
                        <div class="hero-glass-chip" title="{{ __('messages.your_points_balance') ?? 'Your Points Balance' }}">
                            <i class="fa-solid fa-coins text-warning"></i>
                            <span>{{ number_format((float) auth()->user()->pts, 2) }} {{ __('messages.points') ?? 'PTS' }}</span>
                        </div>
                        <a href="{{ route('store.create') }}" class="hero-glass-chip btn-primary-action">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>{{ __('messages.add_product') ?? 'Upload Product' }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hero-glass-chip btn-primary-action">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>{{ __('messages.login_to_purchase') ?? 'Join & Purchase' }}</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Stats & Highlights Bar -->
            <div class="hero-stats-strip">
                <div class="hero-stat-pill">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>9 {{ __('messages.specialized_categories') ?? 'Categories' }}</span>
                </div>
                <div class="hero-stat-pill">
                    <i class="fa-solid fa-cubes"></i>
                    <span id="heroTotalProductsCount">+{{ $products->total() }} {{ __('messages.products') ?? 'Items' }}</span>
                </div>
                <div class="hero-stat-pill">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ __('messages.verified_quality') ?? '100% Verified Quality' }}</span>
                </div>
                <div class="hero-stat-pill">
                    <i class="fa-solid fa-bolt-lightning"></i>
                    <span>{{ __('messages.instant_download') ?? 'Instant Downloads' }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================================================
         CATEGORIES CAROUSEL / GRID
         ======================================================================== -->
    <section class="store-categories-section">
        <div class="store-section-header">
            <div class="section-title-wrap">
                <span class="section-pretitle">{{ __('messages.search_what_you_want') ?? 'Explore Categories' }}</span>
                <h2 class="section-title">{{ __('messages.market_categories') ?? 'Market Categories' }}</h2>
            </div>
            @if(isset($category) && $category)
                <a href="{{ route('store.index') }}" class="quick-pill active" id="resetCategoryChip">
                    <i class="fa-solid fa-border-all"></i>
                    <span>{{ __('messages.show_all_categories') ?? 'Show All' }}</span>
                </a>
            @endif
        </div>

        <div class="modern-category-grid" id="categoryShowcaseGrid">
            @php
                $isScriptSpecific = isset($scriptName) && $scriptName !== 'all';
                $allCategories = \App\Support\StoreCategoryCatalog::selectable();
                $categoryImageMap = [
                    'script' => 'script.png',
                    'themes' => 'templates.png',
                    'plugins' => 'plugins.png',
                    'graphics' => 'graphics.png',
                    'audio' => 'audio.png',
                    'video' => 'video.png',
                    'ebooks' => 'ebooks.png',
                    'software' => 'software.png',
                    'courses' => 'courses.png',
                ];
            @endphp

            @foreach($allCategories as $catKey)
                @php
                    $catImage = $categoryImageMap[$catKey] ?? ($catKey . '.png');
                    $catUrl = $isScriptSpecific 
                        ? route('store.script_category', [$scriptName, $catKey]) 
                        : route('store.index', ['category' => $catKey]);
                    $isCatActive = ($category ?? '') === $catKey;
                    $catDescKey = 'messages.cat_desc_' . $catKey;
                    $catDesc = __($catDescKey) !== $catDescKey ? __($catDescKey) : '';
                @endphp
                <a 
                    class="modern-category-card cat-{{ $catKey }} {{ $isCatActive ? 'active' : '' }}" 
                    href="{{ $catUrl }}"
                    data-category-slug="{{ $catKey }}"
                >
                    <div class="cat-card-header">
                        <div class="cat-title-block">
                            <h3 class="cat-card-title">{{ __('messages.' . $catKey) != 'messages.' . $catKey ? __('messages.' . $catKey) : ucfirst($catKey) }}</h3>
                            @if($catDesc)
                                <p class="cat-card-desc">{{ $catDesc }}</p>
                            @endif
                        </div>
                        <span class="cat-card-badge">{{ $categoryCounts[$catKey] ?? 0 }}</span>
                    </div>
                    <div class="cat-card-bg-img" style="background-image: url({{ theme_asset('img/banner/' . $catImage) }});"></div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- ========================================================================
         STICKY SMART FILTER & CONTROLS TOOLBAR
         ======================================================================== -->
    <div class="store-filter-toolbar" id="storeFilterToolbar">
        <div class="toolbar-main-row">
            <!-- Search Input Box -->
            <div class="toolbar-search-box">
                <i class="fa-solid fa-magnifying-glass search-input-icon"></i>
                <input 
                    type="text" 
                    id="storeSearchInput" 
                    class="toolbar-search-input" 
                    value="{{ $search ?? '' }}" 
                    placeholder="{{ __('messages.search_store_placeholder') ?? 'Search products, scripts, themes...' }}"
                    autocomplete="off"
                >
                <button type="button" id="storeSearchClear" class="search-clear-btn" aria-label="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div id="storeSearchSpinner" class="search-spinner"></div>
            </div>

            <!-- Sorting & View Controls -->
            <div class="toolbar-controls-group">
                <!-- Sort Select -->
                <div class="toolbar-select-wrap">
                    <select id="storeSortSelect" class="toolbar-select" aria-label="Sort products">
                        <option value="latest" {{ ($sort ?? 'latest') === 'latest' ? 'selected' : '' }}>{{ __('messages.sort_latest') ?? 'Latest Items' }}</option>
                        <option value="downloads" {{ ($sort ?? '') === 'downloads' ? 'selected' : '' }}>{{ __('messages.sort_downloads') ?? 'Most Downloaded' }}</option>
                        <option value="rating" {{ ($sort ?? '') === 'rating' ? 'selected' : '' }}>{{ __('messages.top_rated') ?? 'Highest Rated' }}</option>
                        <option value="price_asc" {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>{{ __('messages.sort_price_asc') ?? 'Price: Low to High' }}</option>
                        <option value="price_desc" {{ ($sort ?? '') === 'price_desc' ? 'selected' : '' }}>{{ __('messages.sort_price_desc') ?? 'Price: High to Low' }}</option>
                        <option value="free" {{ ($sort ?? '') === 'free' ? 'selected' : '' }}>{{ __('messages.sort_free') ?? 'Free Only' }}</option>
                        <option value="paid" {{ ($sort ?? '') === 'paid' ? 'selected' : '' }}>{{ __('messages.sort_paid') ?? 'Paid Only' }}</option>
                        <option value="sale" {{ ($sort ?? '') === 'sale' ? 'selected' : '' }}>{{ __('messages.on_sale') ?? 'Discounted Items' }}</option>
                    </select>
                    <i class="fa-solid fa-chevron-down toolbar-select-icon"></i>
                </div>

                <!-- Grid / List Switcher -->
                <div class="view-switcher-box">
                    <button type="button" class="view-btn active" id="viewGridBtn" data-view="grid" title="Grid View" aria-label="Grid View">
                        <i class="fa-solid fa-grip"></i>
                    </button>
                    <button type="button" class="view-btn" id="viewListBtn" data-view="list" title="List View" aria-label="List View">
                        <i class="fa-solid fa-list-ul"></i>
                    </button>
                </div>

                @auth
                    <a href="{{ route('store.discounts.index') }}" class="hero-glass-chip" style="background: var(--store-surface-soft); color: var(--store-text); border: 1px solid var(--store-border); padding: 8px 14px;" title="{{ __('messages.discount_codes') ?? 'Discount Codes' }}">
                        <i class="fa-solid fa-tags text-primary"></i>
                        <span class="d-none d-lg-inline">{{ __('messages.discount_codes') ?? 'Discounts' }}</span>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Quick Filter Tabs Row -->
        <div class="toolbar-quick-pills" id="quickFilterPills">
            <button type="button" class="quick-pill {{ ($sort ?? 'latest') === 'latest' && empty($category) ? 'active' : '' }}" data-sort="latest">
                <i class="fa-solid fa-sparkles"></i>
                <span>{{ __('messages.all_items') ?? 'All Products' }}</span>
            </button>
            <button type="button" class="quick-pill {{ ($sort ?? '') === 'free' ? 'active' : '' }}" data-sort="free">
                <i class="fa-solid fa-gift text-success"></i>
                <span>{{ __('messages.sort_free') ?? 'Free Items' }}</span>
            </button>
            <button type="button" class="quick-pill {{ ($sort ?? '') === 'sale' ? 'active' : '' }}" data-sort="sale">
                <i class="fa-solid fa-fire text-danger"></i>
                <span>{{ __('messages.special_deals') ?? 'On Sale' }}</span>
            </button>
            <button type="button" class="quick-pill {{ ($sort ?? '') === 'downloads' ? 'active' : '' }}" data-sort="downloads">
                <i class="fa-solid fa-chart-line-up"></i>
                <span>{{ __('messages.sort_downloads') ?? 'Most Downloaded' }}</span>
            </button>
            <button type="button" class="quick-pill {{ ($sort ?? '') === 'rating' ? 'active' : '' }}" data-sort="rating">
                <i class="fa-solid fa-star text-warning"></i>
                <span>{{ __('messages.top_rated') ?? 'Top Rated' }}</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================
         SKELETON SHIMMER LOADER (SHOWN DURING AJAX REQUESTS)
         ======================================================================== -->
    <div class="skeleton-grid" id="storeSkeletonLoader">
        @for($i = 0; $i < 6; $i++)
            <div class="skeleton-card">
                <div class="skeleton-media skeleton-shimmer"></div>
                <div class="skeleton-body">
                    <div class="skeleton-line w-40 skeleton-shimmer"></div>
                    <div class="skeleton-line w-80 skeleton-shimmer"></div>
                    <div class="skeleton-line w-100 skeleton-shimmer"></div>
                    <div class="skeleton-line w-60 skeleton-shimmer"></div>
                </div>
            </div>
        @endfor
    </div>

    <!-- ========================================================================
         MAIN PRODUCTS CONTAINER (POPULATED VIA BLADE & UPDATED VIA AJAX)
         ======================================================================== -->
    <div id="storeProductsContainer">
        @include('theme::store.partials.products_grid')
    </div>
</div>

{{-- Pass Configuration to JavaScript --}}
<script>
    window.STORE_CONFIG = {
        baseUrl: '{{ route('store.index') }}',
        currentScript: '{{ $scriptName ?? '' }}',
        currentCategory: '{{ $category ?? '' }}',
        currentSort: '{{ $sort ?? 'latest' }}',
        currentSearch: '{{ $search ?? '' }}',
        isScriptSpecific: {{ (isset($scriptName) && $scriptName !== 'all') ? 'true' : 'false' }},
        scriptBaseUrl: '{{ (isset($scriptName) && $scriptName !== 'all') ? url('/store/' . $scriptName) : url('/store') }}'
    };
</script>

<!-- ========================================================================
     HIGH-SPEED AJAX ENGINE & UI INTERACTIVITY
     ======================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const state = {
        q: window.STORE_CONFIG.currentSearch || '',
        category: window.STORE_CONFIG.currentCategory || '',
        sort: window.STORE_CONFIG.currentSort || 'latest',
        view: localStorage.getItem('store_view_mode') || 'grid',
        page: 1,
        script: window.STORE_CONFIG.currentScript || ''
    };

    let abortController = null;
    let debounceTimer = null;

    // DOM Elements
    const searchInput = document.getElementById('storeSearchInput');
    const searchClear = document.getElementById('storeSearchClear');
    const searchSpinner = document.getElementById('storeSearchSpinner');
    const sortSelect = document.getElementById('storeSortSelect');
    const productsContainer = document.getElementById('storeProductsContainer');
    const skeletonLoader = document.getElementById('storeSkeletonLoader');
    const filterToolbar = document.getElementById('storeFilterToolbar');
    const viewGridBtn = document.getElementById('viewGridBtn');
    const viewListBtn = document.getElementById('viewListBtn');
    const categoryCards = document.querySelectorAll('.modern-category-card');
    const quickFilterButtons = document.querySelectorAll('#quickFilterPills .quick-pill');

    // Initial View Mode Setup
    applyViewMode(state.view);

    // Initial Clear Button State
    updateSearchClearVisibility();

    // -------------------------------------------------------------------------
    // Event: Search Input (Live typing with 300ms debounce)
    // -------------------------------------------------------------------------
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            state.q = e.target.value.trim();
            state.page = 1;
            updateSearchClearVisibility();

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchStoreProducts(true);
            }, 300);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(debounceTimer);
                state.q = e.target.value.trim();
                state.page = 1;
                fetchStoreProducts(true);
            }
        });
    }

    if (searchClear) {
        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            state.q = '';
            state.page = 1;
            updateSearchClearVisibility();
            fetchStoreProducts(true);
            searchInput.focus();
        });
    }

    function updateSearchClearVisibility() {
        if (!searchClear) return;
        searchClear.style.display = searchInput && searchInput.value.trim() !== '' ? 'flex' : 'none';
    }

    // -------------------------------------------------------------------------
    // Event: Sort Select Dropdown
    // -------------------------------------------------------------------------
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            state.sort = this.value;
            state.page = 1;
            syncQuickPills(state.sort);
            fetchStoreProducts(true);
        });
    }

    // -------------------------------------------------------------------------
    // Event: Quick Filter Tabs Click
    // -------------------------------------------------------------------------
    quickFilterButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetSort = this.dataset.sort;
            if (!targetSort) return;

            state.sort = targetSort;
            state.page = 1;
            if (sortSelect) sortSelect.value = targetSort;
            syncQuickPills(targetSort);
            fetchStoreProducts(true);
        });
    });

    function syncQuickPills(activeSort) {
        quickFilterButtons.forEach(btn => {
            if (btn.dataset.sort === activeSort) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // -------------------------------------------------------------------------
    // Event: View Switcher (Grid vs List)
    // -------------------------------------------------------------------------
    if (viewGridBtn) {
        viewGridBtn.addEventListener('click', () => applyViewMode('grid'));
    }
    if (viewListBtn) {
        viewListBtn.addEventListener('click', () => applyViewMode('list'));
    }

    function applyViewMode(mode) {
        state.view = mode;
        localStorage.setItem('store_view_mode', mode);

        if (viewGridBtn && viewListBtn) {
            if (mode === 'list') {
                viewListBtn.classList.add('active');
                viewGridBtn.classList.remove('active');
            } else {
                viewGridBtn.classList.add('active');
                viewListBtn.classList.remove('active');
            }
        }

        const cardsContainer = document.getElementById('productCardsContainer');
        if (cardsContainer) {
            if (mode === 'list') {
                cardsContainer.classList.add('list-view');
            } else {
                cardsContainer.classList.remove('list-view');
            }
        }
    }

    // -------------------------------------------------------------------------
    // Event: Category Showcase Click (Ajax Filter)
    // -------------------------------------------------------------------------
    categoryCards.forEach(card => {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const categorySlug = this.dataset.categorySlug;

            // Toggle category if clicking the already active one
            if (state.category === categorySlug) {
                state.category = '';
            } else {
                state.category = categorySlug;
            }

            state.page = 1;
            syncCategoryCards(state.category);
            fetchStoreProducts(true);
        });
    });

    function syncCategoryCards(activeCategory) {
        categoryCards.forEach(card => {
            if (card.dataset.categorySlug === activeCategory) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        const resetBtn = document.getElementById('resetCategoryChip');
        if (resetBtn) {
            resetBtn.style.display = activeCategory ? 'inline-flex' : 'none';
        }
    }

    // -------------------------------------------------------------------------
    // Event Delegation: Pagination Links & Filter Chips
    // -------------------------------------------------------------------------
    productsContainer.addEventListener('click', function (e) {
        // 1. Pagination clicks
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            try {
                const url = new URL(pageLink.href);
                const pageNum = url.searchParams.get('page');
                if (pageNum) {
                    state.page = parseInt(pageNum, 10);
                    fetchStoreProducts(true, true); // Scroll to top of grid
                }
            } catch (err) {
                window.location.href = pageLink.href;
            }
            return;
        }

        // 2. Active filter chip remove buttons
        const chip = e.target.closest('.filter-chip');
        if (chip && e.target.closest('.chip-remove')) {
            const filterType = chip.dataset.removeFilter;
            if (filterType === 'q') {
                state.q = '';
                if (searchInput) searchInput.value = '';
                updateSearchClearVisibility();
            } else if (filterType === 'category') {
                state.category = '';
                syncCategoryCards('');
            } else if (filterType === 'sort') {
                state.sort = 'latest';
                if (sortSelect) sortSelect.value = 'latest';
                syncQuickPills('latest');
            }
            state.page = 1;
            fetchStoreProducts(true);
            return;
        }

        // 3. Clear all filters button
        const clearAllBtn = e.target.closest('#clearAllFiltersBtn, .btn-reset-filters');
        if (clearAllBtn) {
            e.preventDefault();
            state.q = '';
            state.category = '';
            state.sort = 'latest';
            state.page = 1;

            if (searchInput) searchInput.value = '';
            if (sortSelect) sortSelect.value = 'latest';
            updateSearchClearVisibility();
            syncCategoryCards('');
            syncQuickPills('latest');
            fetchStoreProducts(true);
            return;
        }

        // 4. Category chips within cards
        const catChip = e.target.closest('.product-category-chip');
        if (catChip && catChip.dataset.category) {
            e.preventDefault();
            state.category = catChip.dataset.category;
            state.page = 1;
            syncCategoryCards(state.category);
            fetchStoreProducts(true, true);
            return;
        }
    });

    // -------------------------------------------------------------------------
    // Core AJAX Fetch Engine with AbortController
    // -------------------------------------------------------------------------
    function fetchStoreProducts(pushHistory = false, scrollToGrid = false) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        // UI Loading State
        if (searchSpinner) searchSpinner.style.display = 'block';
        if (skeletonLoader && productsContainer) {
            productsContainer.style.opacity = '0.35';
        }

        // Construct Request URL
        let fetchUrl;
        if (state.script && state.script !== 'all' && state.category) {
            fetchUrl = `${window.STORE_CONFIG.baseUrl}/${encodeURIComponent(state.script)}/${encodeURIComponent(state.category)}`;
        } else {
            fetchUrl = window.STORE_CONFIG.baseUrl;
        }

        const params = new URLSearchParams();
        if (state.q) params.set('q', state.q);
        if (state.category && (!state.script || state.script === 'all')) {
            params.set('category', state.category);
        }
        if (state.script && state.script !== 'all') {
            params.set('script', state.script);
        }
        if (state.sort && state.sort !== 'latest') params.set('sort', state.sort);
        if (state.page && state.page > 1) params.set('page', state.page);
        if (state.view) params.set('view', state.view);
        params.set('ajax', '1');

        const requestUri = `${fetchUrl}?${params.toString()}`;

        fetch(requestUri, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            signal: abortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data && data.success && data.html) {
                productsContainer.innerHTML = data.html;
                productsContainer.style.opacity = '1';

                // Re-apply current view mode class to newly injected cards container
                applyViewMode(state.view);

                // Update Hero total count if available
                const heroTotal = document.getElementById('heroTotalProductsCount');
                if (heroTotal && typeof data.total !== 'undefined') {
                    heroTotal.textContent = `+${data.total} ${window.MYADS_I18N?.products || 'Products'}`;
                }

                // Push URL state without reloading
                if (pushHistory) {
                    params.delete('ajax');
                    const cleanQuery = params.toString();
                    const newUrl = cleanQuery ? `${fetchUrl}?${cleanQuery}` : fetchUrl;
                    window.history.pushState({ ...state }, '', newUrl);
                }

                // Smooth Scroll if requested
                if (scrollToGrid && filterToolbar) {
                    filterToolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return; // Request intentionally canceled
            console.error('Store Ajax Fetch Failed:', err);
            productsContainer.style.opacity = '1';
        })
        .finally(() => {
            if (searchSpinner) searchSpinner.style.display = 'none';
        });
    }

    // -------------------------------------------------------------------------
    // Event: Browser Back / Forward Navigation (popstate)
    // -------------------------------------------------------------------------
    window.addEventListener('popstate', function (e) {
        if (e.state) {
            state.q = e.state.q || '';
            state.category = e.state.category || '';
            state.sort = e.state.sort || 'latest';
            state.page = e.state.page || 1;
            state.view = e.state.view || 'grid';

            if (searchInput) searchInput.value = state.q;
            if (sortSelect) sortSelect.value = state.sort;
            updateSearchClearVisibility();
            syncCategoryCards(state.category);
            syncQuickPills(state.sort);
            applyViewMode(state.view);

            fetchStoreProducts(false);
        } else {
            // Restore from URL directly
            const urlParams = new URLSearchParams(window.location.search);
            state.q = urlParams.get('q') || '';
            state.category = urlParams.get('category') || '';
            state.sort = urlParams.get('sort') || 'latest';
            state.page = parseInt(urlParams.get('page') || '1', 10);

            if (searchInput) searchInput.value = state.q;
            if (sortSelect) sortSelect.value = state.sort;
            updateSearchClearVisibility();
            syncCategoryCards(state.category);
            syncQuickPills(state.sort);

            fetchStoreProducts(false);
        }
    });
});
</script>
@endsection
