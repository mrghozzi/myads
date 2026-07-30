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
                <div class="card-header border-bottom-0 pb-0">
                    <h5 class="card-title text-primary"><i class="feather-code me-2"></i>{{ __('messages.seo_head_management') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.seo.head.update') }}" method="POST" class="row g-4">
                        @csrf
                        <div class="col-12">
                            <h6 class="text-primary mb-2"><i class="feather-check-square me-2"></i>Search Console Verification Tags</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="feather-search text-danger me-1"></i> {{ __('messages.seo_google_verification') }}</label>
                            <input type="text" name="google_site_verification" class="form-control" value="{{ old('google_site_verification', $settings->google_site_verification) }}" placeholder="Google verification token">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="feather-globe text-primary me-1"></i> {{ __('messages.seo_bing_verification') }}</label>
                            <input type="text" name="bing_site_verification" class="form-control" value="{{ old('bing_site_verification', $settings->bing_site_verification) }}" placeholder="Bing verification token">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="feather-server text-warning me-1"></i> {{ __('messages.seo_yandex_verification') }}</label>
                            <input type="text" name="yandex_site_verification" class="form-control" value="{{ old('yandex_site_verification', $settings->yandex_site_verification) }}" placeholder="Yandex verification token">
                        </div>

                        <div class="col-12"><hr class="my-0"></div>

                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="feather-file-text me-1 text-primary"></i> {{ __('messages.seo_head_snippets') }}</label>
                            <textarea name="head_snippets" rows="10" class="form-control seo-code" style="min-height: 220px;" placeholder="<meta name=&quot;custom-tag&quot; content=&quot;value&quot; />">{{ old('head_snippets', $settings->head_snippets) }}</textarea>
                            <div class="seo-form-note mt-2">{!! __('messages.seo_head_snippets_help') !!}</div>
                        </div>
                        <div class="col-12 d-flex justify-content-end mt-2">
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
                <div class="card-header border-bottom-0 pb-0">
                    <h5 class="card-title text-success"><i class="feather-eye me-2"></i>{{ __('messages.seo_sanitized_preview') }}</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">{{ __('messages.seo_sanitized_preview_help') }}</p>
                    <pre class="seo-code flex-grow-1 mb-0">{{ $sanitizedPreview ?: __('messages.seo_sanitized_preview_empty') }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
