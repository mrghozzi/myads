@extends('theme::layouts.admin')

@section('title', __('messages.seo_head_meta'))

@section('content')
<div class="seo-shell">
    <div class="mb-4">
        <h3 class="mb-1">{{ __('messages.seo_head_management') }}</h3>
        <p class="text-muted mb-0">{!! __('messages.seo_head_intro') !!}</p>
    </div>

    @include('theme::admin.seo.partials.nav')
    @include('theme::admin.seo.partials.alerts')

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
