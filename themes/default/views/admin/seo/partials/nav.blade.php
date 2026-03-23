<style>
    .seo-shell .seo-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
    }

    .seo-shell .seo-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .seo-shell .seo-nav a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid rgba(99, 102, 241, 0.14);
        color: #475569;
        background: #fff;
        font-weight: 600;
    }

    .seo-shell .seo-nav a.active {
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        border-color: transparent;
        box-shadow: 0 14px 35px rgba(79, 70, 229, 0.22);
    }

    .seo-shell .seo-stat {
        border-radius: 16px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(255, 255, 255, 1));
        border: 1px solid rgba(148, 163, 184, 0.18);
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .seo-shell .seo-stat .label {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .seo-shell .seo-stat .value {
        color: #0f172a;
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.05;
    }

    .seo-shell .seo-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
    }

    .seo-shell .seo-pill.ok {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .seo-shell .seo-pill.warn {
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
    }

    .seo-shell .seo-pill.bad {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
    }

    .seo-shell .seo-code {
        font-family: Consolas, Monaco, monospace;
        white-space: pre-wrap;
        word-break: break-word;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 16px;
        padding: 1rem;
        min-height: 220px;
    }

    .seo-shell .seo-form-note {
        font-size: 0.88rem;
        color: #64748b;
    }

    .seo-shell .seo-chart-wrap {
        position: relative;
        width: 100%;
        min-height: 320px;
        height: 320px;
    }

    .seo-shell .seo-chart-wrap--tall {
        min-height: 360px;
        height: 360px;
    }

    .seo-shell .seo-chart-wrap canvas {
        width: 100% !important;
        height: 100% !important;
        display: block;
    }
</style>

<div class="seo-nav">
    <a href="{{ route('admin.seo.index') }}" class="{{ request()->routeIs('admin.seo.index') ? 'active' : '' }}">
        <i class="feather-activity"></i> {{ __('messages.seo_nav_dashboard') }}
    </a>
    <a href="{{ route('admin.seo.settings') }}" class="{{ request()->routeIs('admin.seo.settings') ? 'active' : '' }}">
        <i class="feather-sliders"></i> {{ __('messages.seo_nav_settings') }}
    </a>
    <a href="{{ route('admin.seo.head') }}" class="{{ request()->routeIs('admin.seo.head') ? 'active' : '' }}">
        <i class="feather-code"></i> {{ __('messages.seo_head_meta') }}
    </a>
    <a href="{{ route('admin.seo.rules') }}" class="{{ request()->routeIs('admin.seo.rules') ? 'active' : '' }}">
        <i class="feather-shield"></i> {{ __('messages.seo_nav_rules') }}
    </a>
    <a href="{{ route('admin.seo.indexing') }}" class="{{ request()->routeIs('admin.seo.indexing') ? 'active' : '' }}">
        <i class="feather-search"></i> {{ __('messages.seo_indexing') }}
    </a>
</div>
