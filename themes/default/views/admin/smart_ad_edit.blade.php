@extends('theme::layouts.admin')

@section('title', __('messages.smart_edit_title', ['id' => $smartAd->id]))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.smart_ads') }}">{{ __('messages.smart_ads') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.smart_ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.smart_edit_title', ['id' => $smartAd->id]) }}</h1>
            <p class="admin-hero__copy">{{ $smartAd->displayTitle() }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <a href="{{ route('admin.smart_ads') }}" class="btn btn-light w-100">{{ __('messages.back') }}</a>
            </div>
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.smart_impressions_label') }}</span>
                    <span class="admin-summary-value">{{ number_format($smartAd->impressions) }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.smart_clicks_label') }}</span>
                    <span class="admin-summary-value">{{ number_format($smartAd->clicks) }}</span>
                </div>
            </div>
        </div>
    </section>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.smart_admin_details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.smart_ads.update', $smartAd->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.smart_form_landing_url') }}</label>
                            <input type="url" name="landing_url" class="form-control" value="{{ old('landing_url', $smartAd->landing_url) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.smart_form_headline_override') }}</label>
                            <input type="text" name="headline_override" class="form-control" value="{{ old('headline_override', $smartAd->headline_override) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.smart_form_image_override') }}</label>
                            <input type="text" name="image" class="form-control" value="{{ old('image', $smartAd->image) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.smart_form_description_override') }}</label>
                            <textarea name="description_override" rows="4" class="form-control">{{ old('description_override', $smartAd->description_override) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.smart_form_target_countries') }}</label>
                            <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <select name="statu" class="form-select">
                                <option value="1" {{ (int) old('statu', $smartAd->statu) === 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                <option value="0" {{ (int) old('statu', $smartAd->statu) === 0 ? 'selected' : '' }}>{{ __('messages.smart_status_paused') }}</option>
                                <option value="2" {{ (int) old('statu', $smartAd->statu) === 2 ? 'selected' : '' }}>{{ __('messages.smart_status_blocked') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.smart_form_target_devices') }}</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($deviceOptions as $value => $label)
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="devices[]" value="{{ $value }}" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.smart_form_manual_keywords') }}</label>
                            <textarea name="manual_keywords" rows="4" class="form-control">{{ old('manual_keywords', $smartAd->manual_keywords) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        <a href="{{ route('admin.smart_ads') }}" class="btn btn-light">{{ __('messages.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.smart_performance') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted d-block fs-12 text-uppercase">{{ __('messages.smart_admin_owner') }}</span>
                    <strong>{{ $smartAd->user?->username ?? __('messages.unknown') }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block fs-12 text-uppercase">{{ __('messages.smart_impressions_label') }}</span>
                    <strong>{{ $smartAd->impressions }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block fs-12 text-uppercase">{{ __('messages.smart_clicks_label') }}</span>
                    <strong>{{ $smartAd->clicks }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block fs-12 text-uppercase">{{ __('messages.smart_admin_extracted_topic') }}</span>
                    <div class="small text-muted">{{ $smartAd->extracted_keywords ?: __('messages.smart_no_extracted_keywords') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
