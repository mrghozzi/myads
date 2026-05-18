@extends('admin::layouts.admin')

@section('title', __('messages.custom_ads_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.custom_ads.index') }}">{{ __('messages.custom_ads') }}</a></li>
                <li>{{ __('messages.custom_ads_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.custom_ads_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.custom_ads_settings_intro') }}</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.custom_ads.index') }}" class="btn btn-light w-100">{{ __('messages.back') }}</a>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form method="POST" action="{{ route('admin.custom_ads.settings.update') }}" class="row g-4">
                @csrf
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="{{ \App\Support\CustomAdsSettings::ENABLED }}" value="1" id="customAdsEnabled" @checked(($settings[\App\Support\CustomAdsSettings::ENABLED] ?? '1') === '1')>
                        <label class="form-check-label" for="customAdsEnabled">{{ __('messages.custom_ads_enabled') }}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="{{ \App\Support\CustomAdsSettings::MARKETPLACE_ENABLED }}" value="1" id="customAdsMarketplace" @checked(($settings[\App\Support\CustomAdsSettings::MARKETPLACE_ENABLED] ?? '1') === '1')>
                        <label class="form-check-label" for="customAdsMarketplace">{{ __('messages.custom_ads_marketplace_enabled') }}</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="{{ \App\Support\CustomAdsSettings::REQUIRE_REVIEW }}" value="1" id="customAdsReview" @checked(($settings[\App\Support\CustomAdsSettings::REQUIRE_REVIEW] ?? '0') === '1')>
                        <label class="form-check-label" for="customAdsReview">{{ __('messages.custom_ads_require_review') }}</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.custom_ads_min_total_pts') }}</label>
                    <input type="number" min="0" step="0.01" class="form-control" name="{{ \App\Support\CustomAdsSettings::MIN_TOTAL_PTS }}" value="{{ old(\App\Support\CustomAdsSettings::MIN_TOTAL_PTS, $settings[\App\Support\CustomAdsSettings::MIN_TOTAL_PTS] ?? 1) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.custom_ads_min_daily_pts') }}</label>
                    <input type="number" min="0" step="0.01" class="form-control" name="{{ \App\Support\CustomAdsSettings::MIN_DAILY_PTS }}" value="{{ old(\App\Support\CustomAdsSettings::MIN_DAILY_PTS, $settings[\App\Support\CustomAdsSettings::MIN_DAILY_PTS] ?? 1) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.custom_ads_max_duration_days') }}</label>
                    <input type="number" min="1" max="365" class="form-control" name="{{ \App\Support\CustomAdsSettings::MAX_DURATION_DAYS }}" value="{{ old(\App\Support\CustomAdsSettings::MAX_DURATION_DAYS, $settings[\App\Support\CustomAdsSettings::MAX_DURATION_DAYS] ?? 30) }}">
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
