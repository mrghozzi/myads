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
