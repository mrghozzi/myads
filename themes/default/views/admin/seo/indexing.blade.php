@extends('theme::layouts.admin')

@section('title', __('messages.seo_indexing'))

@section('content')
<div class="seo-shell">
    <div class="mb-4">
        <h3 class="mb-1">{{ __('messages.seo_indexing_heading') }}</h3>
        <p class="text-muted mb-0">{{ __('messages.seo_indexing_intro') }}</p>
    </div>

    @include('theme::admin.seo.partials.nav')
    @include('theme::admin.seo.partials.alerts')

    <div class="row g-3 mb-4">
        <div class="col-xl-4">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_robots_status') }}</div>
                <div class="mt-2">
                    <span class="seo-pill {{ app(\App\Services\RobotsTxtService::class)->blocksAll($settings) ? 'bad' : 'ok' }}">
                        {{ app(\App\Services\RobotsTxtService::class)->blocksAll($settings) ? __('messages.seo_blocking_sitewide') : __('messages.seo_health_healthy') }}
                    </span>
                </div>
                <div class="seo-form-note mt-3">{{ __('messages.seo_robots_status_note') }}</div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_sitemap_url') }}</div>
                <div class="value" style="font-size: 1.05rem;">/sitemap.xml</div>
                <div class="mt-3">
                    <a href="{{ route('sitemap.xml') }}" target="_blank" class="btn btn-outline-primary btn-sm">{{ __('messages.seo_preview_sitemap') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_indexable_urls') }}</div>
                <div class="value">{{ number_format($dashboard['summary_cards']['indexable_urls']) }}</div>
                <div class="seo-form-note mt-3">{{ __('messages.seo_indexable_urls_indexing_note') }}</div>
            </div>
        </div>
    </div>

    <div class="card seo-card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.seo.indexing.update') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_indexing" name="allow_indexing" value="1" @checked(old('allow_indexing', $settings->allow_indexing))>
                        <label class="form-check-label fw-semibold" for="allow_indexing">{{ __('messages.seo_allow_indexing') }}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('messages.seo_allow_paths') }}</label>
                    <textarea name="robots_allow_paths" rows="8" class="form-control">{{ old('robots_allow_paths', $settings->robots_allow_paths) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('messages.seo_disallow_paths') }}</label>
                    <textarea name="robots_disallow_paths" rows="8" class="form-control">{{ old('robots_disallow_paths', $settings->robots_disallow_paths) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('messages.seo_extra_robots_lines') }}</label>
                    <textarea name="robots_extra" rows="8" class="form-control">{{ old('robots_extra', $settings->robots_extra) }}</textarea>
                    <div class="seo-form-note mt-2">{!! __('messages.seo_extra_robots_help', ['example' => '<code>Crawl-delay: 5</code>']) !!}</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>{{ __('messages.seo_save_indexing') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">{{ __('messages.seo_robots_preview') }}</h5>
                        <a href="{{ route('robots.txt') }}" target="_blank" class="btn btn-outline-secondary btn-sm">{{ __('messages.seo_open') }}</a>
                    </div>
                    <pre class="seo-code mb-0">{{ $robotsPreview }}</pre>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">{{ __('messages.seo_sitemap_preview') }}</h5>
                        <a href="{{ route('sitemap.xml') }}" target="_blank" class="btn btn-outline-secondary btn-sm">{{ __('messages.seo_open') }}</a>
                    </div>
                    <pre class="seo-code mb-0">{{ $sitemapPreview }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
