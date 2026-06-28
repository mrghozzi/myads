@extends('admin::layouts.admin')

@section('title', __('messages.ads_settings_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li>{{ __('messages.ads_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.ads_settings_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.ads_settings_intro') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form action="{{ route('admin.ads.settings.update') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-12 mb-2">
                    <h5 class="border-bottom pb-2">{{ __('messages.banner_ads_settings') }}</h5>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.ads_brand_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="ads_brand_name" class="form-control" value="{{ old('ads_brand_name', $adsBrandName) }}" placeholder="{{ __('messages.ads_brand_name') }}">
                    <small class="text-muted">{{ __('messages.ads_brand_name_help') }}</small>
                </div>
                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.banner_repeat_window_title') }}</label>
                    <input type="number" min="0" max="525600" name="banner_repeat_window_minutes" class="form-control" value="{{ old('banner_repeat_window_minutes', $bannerRepeatWindowMinutes) }}" placeholder="1440">
                    <small class="text-muted">{{ __('messages.banner_repeat_window_help') }}</small>
                </div>
                <div class="col-lg-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="banner_fallback_to_seen" name="banner_fallback_to_seen" value="1" {{ old('banner_fallback_to_seen', $bannerFallbackToSeen) ? 'checked' : '' }}>
                        <label class="form-check-label" for="banner_fallback_to_seen">{{ __('messages.banner_fallback_to_seen') }}</label>
                    </div>
                    <small class="text-muted">{{ __('messages.banner_fallback_to_seen_help') }}</small>
                </div>
                <div class="col-lg-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="banner_prevent_concurrent_duplicates" name="banner_prevent_concurrent_duplicates" value="1" {{ old('banner_prevent_concurrent_duplicates', $bannerPreventConcurrent) ? 'checked' : '' }}>
                        <label class="form-check-label" for="banner_prevent_concurrent_duplicates">{{ __('messages.banner_prevent_concurrent_duplicates') }}</label>
                    </div>
                    <small class="text-muted">{{ __('messages.banner_prevent_concurrent_duplicates_help') }}</small>
                </div>

                <div class="col-12 mt-4 mb-2">
                    <h5 class="border-bottom pb-2">{{ __('messages.text_ads_settings') }}</h5>
                </div>
                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.link_ads_repeat_window') }}</label>
                    <input type="number" min="0" max="525600" name="link_repeat_window_minutes" class="form-control" value="{{ old('link_repeat_window_minutes', $linkRepeatWindowMinutes) }}" placeholder="60">
                    <small class="text-muted">{{ __('messages.link_ads_repeat_window_help') }}</small>
                </div>

                <div class="col-12 mt-4 mb-2">
                    <h5 class="border-bottom pb-2">{{ __('messages.smart_ads_settings') }}</h5>
                </div>
                <div class="col-lg-12">
                    <label class="form-label">{{ __('messages.smart_admin_points_divisor') }}</label>
                    <input type="number" min="0.1" max="1000" step="0.1" name="smart_ads_points_divisor" class="form-control" value="{{ old('smart_ads_points_divisor', $smartAdsPointsDivisor) }}" placeholder="4">
                    <small class="text-muted">{{ __('messages.smart_admin_points_divisor_help') }}</small>
                </div>

                <div class="col-12 mt-4 mb-2">
                    <h5 class="border-bottom pb-2">{{ __('messages.visit_exchange_settings') }}</h5>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.visit_daily_limit') }}</label>
                    <input type="number" min="1" max="1000" name="visit_daily_limit" class="form-control" value="{{ old('visit_daily_limit', $visitDailyLimit) }}" placeholder="50">
                    <small class="text-muted">{{ __('messages.visit_daily_limit_help') }}</small>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.visit_points_reward') }}</label>
                    <input type="number" min="0" max="1000" step="1" name="visit_points_reward" class="form-control" value="{{ old('visit_points_reward', $visitPointsReward) }}" placeholder="5">
                    <small class="text-muted">{{ __('messages.visit_points_reward_help') }}</small>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.visit_vu_reward') }}</label>
                    <input type="number" min="0" max="100" step="0.1" name="visit_vu_reward" class="form-control" value="{{ old('visit_vu_reward', $visitVuReward) }}" placeholder="0.5">
                    <small class="text-muted">{{ __('messages.visit_vu_reward_help') }}</small>
                </div>

                <div class="col-12 mt-4 mb-2">
                    <h5 class="border-bottom pb-2">{{ __('messages.privacy_settings') }}</h5>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.ip_visibility_title') ?? 'IP Visibility in Stats' }}</label>
                    <select name="ip_visibility" class="form-select">
                        <option value="everyone" {{ $ipVisibility === 'everyone' ? 'selected' : '' }}>{{ __('messages.ip_visibility_everyone') ?? 'Everyone' }}</option>
                        <option value="paid_all" {{ $ipVisibility === 'paid_all' ? 'selected' : '' }}>{{ __('messages.ip_visibility_paid_all') ?? 'All Paid Members' }}</option>
                        @foreach($plans as $plan)
                            <option value="plan_{{ $plan->id }}" {{ $ipVisibility === "plan_{$plan->id}" ? 'selected' : '' }}>
                                {{ __('messages.ip_visibility_plan') ?? 'Only Plan:' }} {{ $plan->name }}
                            </option>
                        @endforeach
                        <option value="admins" {{ $ipVisibility === 'admins' ? 'selected' : '' }}>{{ __('messages.ip_visibility_admins') ?? 'Admins Only' }}</option>
                        <option value="none" {{ $ipVisibility === 'none' ? 'selected' : '' }}>{{ __('messages.ip_visibility_none') ?? 'No One' }}</option>
                    </select>
                    <small class="text-muted">{{ __('messages.ip_visibility_help') ?? 'Choose who can see visitor IP addresses in the statistics pages.' }}</small>
                </div>
                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('messages.ads_settings_save') }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
