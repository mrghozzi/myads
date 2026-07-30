@extends('admin::layouts.admin')

@section('title', __('messages.seo_settings'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="seo-shell">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.seo.index') }}">{{ __('messages.seo_dashboard') }}</a></li>
                <li>{{ __('messages.seo_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.seo_nav_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.seo_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.seo_settings_intro') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-light">
                        <i class="feather-activity me-2"></i>{{ __('messages.seo_nav_dashboard') }}
                    </a>
                </div>
            </div>
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_canonical_mode') }}</span>
                    <span class="admin-summary-value">{{ $settings->canonical_mode }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_ga4_enable') }}</span>
                    <span class="admin-summary-value">{{ $settings->ga4_enabled ? __('messages.enabled') : __('messages.disabled') }}</span>
                </div>
            </div>
        </div>
    </section>

    @include('admin::admin.seo.partials.nav')
    @include('admin::admin.seo.partials.alerts')

    <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" style="background: rgba(97, 93, 250, 0.08); color: var(--admin-premium-text);">
        <div>
            <i class="feather-info text-primary me-2 fs-5"></i>
            <span>{{ __('messages.seo_performance_settings_hint') ?? 'Looking to tune server CPU usage or change daily SEO visit log retention?' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.settings.performance') }}" class="btn btn-sm btn-outline-primary">
                <i class="feather-cpu me-1"></i> {{ __('messages.performance_settings') ?? 'Performance Settings' }}
            </a>
            <a href="{{ route('admin.database_cleanup') }}" class="btn btn-sm btn-outline-danger">
                <i class="feather-database me-1"></i> {{ __('messages.database_cleanup') ?? 'Database Cleanup' }}
            </a>
        </div>
    </div>

    <div class="card seo-card">
        <div class="card-header border-bottom-0 pb-0">
            <h5 class="card-title text-primary"><i class="feather-sliders me-2"></i>{{ __('messages.seo_settings') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.seo.settings.update') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_title') }}</label>
                    <input type="text" name="default_title" class="form-control" value="{{ old('default_title', $settings->default_title) }}">
                    <div class="seo-form-note mt-2">{{ __('messages.seo_default_title_help') }}</div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_keywords') }}</label>
                    <input type="text" name="default_keywords" class="form-control" value="{{ old('default_keywords', $settings->default_keywords) }}">
                    <div class="seo-form-note mt-2">{{ __('messages.seo_default_keywords_help') }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_description') }}</label>
                    <textarea name="default_description" rows="4" class="form-control">{{ old('default_description', $settings->default_description) }}</textarea>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_robots') }}</label>
                    <input type="text" name="default_robots" class="form-control" value="{{ old('default_robots', $settings->default_robots) }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_canonical_mode') }}</label>
                    <select name="canonical_mode" class="form-select">
                        @foreach($canonicalModes as $value => $label)
                            <option value="{{ $value }}" @selected(old('canonical_mode', $settings->canonical_mode) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_og_image') }}</label>
                    <input type="text" name="default_og_image" class="form-control" value="{{ old('default_og_image', $settings->default_og_image) }}" placeholder="{{ __('messages.seo_default_og_image_placeholder') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_default_twitter_card') }}</label>
                    <select name="default_twitter_card" class="form-select">
                        @foreach($twitterCards as $value => $label)
                            <option value="{{ $value }}" @selected(old('default_twitter_card', $settings->default_twitter_card) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12"><hr class="my-0"></div>

                <div class="col-12">
                    <h6 class="text-primary mb-3"><i class="feather-bar-chart-2 me-2"></i>Google Analytics 4 (GA4) Integration</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ga4_enabled" name="ga4_enabled" value="1" @checked(old('ga4_enabled', $settings->ga4_enabled))>
                        <label class="form-check-label fw-semibold" for="ga4_enabled">{{ __('messages.seo_ga4_enable') }}</label>
                    </div>
                    <div class="seo-form-note mt-2">{{ __('messages.seo_ga4_help') }}</div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">{{ __('messages.seo_ga4_measurement_id') }}</label>
                    <input type="text" name="ga4_measurement_id" class="form-control" value="{{ old('ga4_measurement_id', $settings->ga4_measurement_id) }}" placeholder="G-XXXXXXXXXX">
                </div>
                <div class="col-lg-6 d-flex align-items-end">
                    <div class="seo-form-note">{!! __('messages.seo_ga4_measurement_help', ['example' => '<code>G-AB12CDEF34</code>']) !!}</div>
                </div>

                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>{{ __('messages.seo_save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
