@extends('theme::layouts.admin')

@section('title', __('messages.seo_settings'))

@section('content')
<div class="seo-shell">
    <div class="mb-4">
        <h3 class="mb-1">{{ __('messages.seo_settings') }}</h3>
        <p class="text-muted mb-0">{{ __('messages.seo_settings_intro') }}</p>
    </div>

    @include('theme::admin.seo.partials.nav')
    @include('theme::admin.seo.partials.alerts')

    <div class="card seo-card">
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

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>{{ __('messages.seo_save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
