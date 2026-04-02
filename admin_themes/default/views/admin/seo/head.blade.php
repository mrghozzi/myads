@extends('admin::layouts.admin')

@section('title', __('messages.seo_head_meta'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="seo-shell">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.seo.index') }}">{{ __('messages.seo_dashboard') }}</a></li>
                <li>{{ __('messages.seo_head_meta') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.seo_head_meta') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.seo_head_management') }}</h1>
            <p class="admin-hero__copy">{{ strip_tags(__('messages.seo_head_intro')) }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-light">
                        <i class="feather-activity me-2"></i>{{ __('messages.seo_nav_dashboard') }}
                    </a>
                    <a href="{{ route('robots.txt') }}" target="_blank" class="btn btn-outline-primary">
                        <i class="feather-shield me-2"></i>{{ __('messages.seo_open') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('admin::admin.seo.partials.nav')
    @include('admin::admin.seo.partials.alerts')

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <form action="{{ route('admin.seo.head.update') }}" method="POST" class="row g-4">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ __('messages.seo_google_verification') }}</label>
                            <input type="text" name="google_site_verification" class="form-control" value="{{ old('google_site_verification', $settings->google_site_verification) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ __('messages.seo_bing_verification') }}</label>
                            <input type="text" name="bing_site_verification" class="form-control" value="{{ old('bing_site_verification', $settings->bing_site_verification) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ __('messages.seo_yandex_verification') }}</label>
                            <input type="text" name="yandex_site_verification" class="form-control" value="{{ old('yandex_site_verification', $settings->yandex_site_verification) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('messages.seo_head_snippets') }}</label>
                            <textarea name="head_snippets" rows="10" class="form-control">{{ old('head_snippets', $settings->head_snippets) }}</textarea>
                            <div class="seo-form-note mt-2">{!! __('messages.seo_head_snippets_help') !!}</div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>{{ __('messages.seo_save_head') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <h5 class="mb-2">{{ __('messages.seo_sanitized_preview') }}</h5>
                    <p class="text-muted">{{ __('messages.seo_sanitized_preview_help') }}</p>
                    <pre class="seo-code mb-0">{{ $sanitizedPreview ?: __('messages.seo_sanitized_preview_empty') }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
