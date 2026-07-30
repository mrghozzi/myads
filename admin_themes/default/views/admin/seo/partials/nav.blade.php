<div class="seo-nav d-flex flex-wrap align-items-center justify-content-between gap-2">
    <div class="d-flex flex-wrap align-items-center gap-2">
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
        <a href="{{ route('admin.seo.ads_files') }}" class="{{ request()->routeIs('admin.seo.ads_files') ? 'active' : '' }}">
            <i class="feather-file-text"></i> {{ __('messages.seo_ads_files') }}
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 ms-auto">
        <a href="{{ route('admin.settings.performance') }}" class="btn btn-sm btn-soft-primary" title="{{ __('messages.performance_settings') ?? 'Performance Settings' }}">
            <i class="feather-cpu me-1"></i> {{ __('messages.performance') ?? 'Performance' }}
        </a>
        <a href="{{ route('admin.database_cleanup') }}" class="btn btn-sm btn-soft-danger" title="{{ __('messages.database_cleanup') ?? 'Database Cleanup' }}">
            <i class="feather-database me-1"></i> {{ __('messages.cleanup') ?? 'DB Cleanup' }}
        </a>
    </div>
</div>
