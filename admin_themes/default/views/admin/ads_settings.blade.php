@extends('admin::layouts.admin')

@section('title', __('messages.ads_settings_title'))

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li>{{ __('messages.ads_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-settings me-1"></i>{{ __('messages.settings') ?? 'Settings' }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-settings text-primary"></i>
                {{ __('messages.ads_settings_title') }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.ads_settings_intro') }}</p>
        </div>

        <div class="admin-hero__actions">
            <a href="{{ route('admin.ads') }}" class="btn btn-light d-inline-flex align-items-center gap-2">
                <i class="feather-arrow-left"></i>
                <span>{{ __('messages.back') ?? 'Back' }}</span>
            </a>
        </div>
    </section>

    <!-- Settings Form Container -->
    <form id="adsSettingsForm" action="{{ route('admin.ads.settings.update') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <!-- 1. General & Branding -->
            <div class="col-lg-6">
                <div class="card stretch stretch-full shadow-sm h-100">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="feather-shield text-primary"></i>
                            {{ __('messages.banner_ads_settings') }} &bull; {{ __('messages.general') ?? 'General' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.ads_brand_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="ads_brand_name" class="form-control" value="{{ old('ads_brand_name', $adsBrandName) }}" required>
                            <small class="text-muted">{{ __('messages.ads_brand_name_help') }}</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.ip_visibility_title') ?? 'IP Visibility in Stats' }}</label>
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
                    </div>
                </div>
            </div>

            <!-- 2. Banner Serving Rules -->
            <div class="col-lg-6">
                <div class="card stretch stretch-full shadow-sm h-100">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="feather-image text-primary"></i>
                            {{ __('messages.bannads') }} {{ __('messages.rules') ?? 'Rules' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.banner_repeat_window_title') }}</label>
                            <div class="input-group">
                                <input type="number" min="0" max="525600" name="banner_repeat_window_minutes" class="form-control" value="{{ old('banner_repeat_window_minutes', $bannerRepeatWindowMinutes) }}">
                                <span class="input-group-text bg-light text-muted">{{ __('messages.minutes') ?? 'minutes' }}</span>
                            </div>
                            <small class="text-muted">{{ __('messages.banner_repeat_window_help') }}</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="banner_fallback_to_seen" name="banner_fallback_to_seen" value="1" {{ old('banner_fallback_to_seen', $bannerFallbackToSeen) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="banner_fallback_to_seen">{{ __('messages.banner_fallback_to_seen') }}</label>
                            <div class="small text-muted">{{ __('messages.banner_fallback_to_seen_help') }}</div>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="banner_prevent_concurrent_duplicates" name="banner_prevent_concurrent_duplicates" value="1" {{ old('banner_prevent_concurrent_duplicates', $bannerPreventConcurrent) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="banner_prevent_concurrent_duplicates">{{ __('messages.banner_prevent_concurrent_duplicates') }}</label>
                            <div class="small text-muted">{{ __('messages.banner_prevent_concurrent_duplicates_help') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Text Ads Rules -->
            <div class="col-lg-6">
                <div class="card stretch stretch-full shadow-sm h-100">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="feather-file-text text-info"></i>
                            {{ __('messages.text_ads_settings') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.link_ads_repeat_window') }}</label>
                            <div class="input-group">
                                <input type="number" min="0" max="525600" name="link_repeat_window_minutes" class="form-control" value="{{ old('link_repeat_window_minutes', $linkRepeatWindowMinutes) }}">
                                <span class="input-group-text bg-light text-muted">{{ __('messages.minutes') ?? 'minutes' }}</span>
                            </div>
                            <small class="text-muted">{{ __('messages.link_ads_repeat_window_help') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Smart Ads Rules -->
            <div class="col-lg-6">
                <div class="card stretch stretch-full shadow-sm h-100">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="feather-cpu text-warning"></i>
                            {{ __('messages.smart_ads_settings') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_admin_points_divisor') }}</label>
                            <input type="number" min="0.1" max="1000" step="0.1" name="smart_ads_points_divisor" class="form-control" value="{{ old('smart_ads_points_divisor', $smartAdsPointsDivisor) }}">
                            <small class="text-muted">{{ __('messages.smart_admin_points_divisor_help') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Traffic Exchange Settings -->
            <div class="col-12">
                <div class="card stretch stretch-full shadow-sm">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="feather-activity text-success"></i>
                            {{ __('messages.visit_exchange_settings') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">{{ __('messages.visit_daily_limit') }}</label>
                                <input type="number" min="1" max="1000" name="visit_daily_limit" class="form-control" value="{{ old('visit_daily_limit', $visitDailyLimit) }}">
                                <small class="text-muted">{{ __('messages.visit_daily_limit_help') }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">{{ __('messages.visit_points_reward') }}</label>
                                <input type="number" min="0" max="1000" step="1" name="visit_points_reward" class="form-control" value="{{ old('visit_points_reward', $visitPointsReward) }}">
                                <small class="text-muted">{{ __('messages.visit_points_reward_help') }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">{{ __('messages.visit_vu_reward') }}</label>
                                <input type="number" min="0" max="100" step="0.1" name="visit_vu_reward" class="form-control" value="{{ old('visit_vu_reward', $visitVuReward) }}">
                                <small class="text-muted">{{ __('messages.visit_vu_reward_help') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Bar -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('admin.ads') }}" class="btn btn-light px-4">{{ __('messages.cancel') ?? 'Cancel' }}</a>
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5" id="saveAdsSettingsBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="saveSettingsSpinner"></span>
                    <i class="feather-save" id="saveSettingsIcon"></i>
                    <span>{{ __('messages.ads_settings_save') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.getElementById('adsSettingsForm');
    const saveBtn = document.getElementById('saveAdsSettingsBtn');
    const spinner = document.getElementById('saveSettingsSpinner');
    const icon = document.getElementById('saveSettingsIcon');

    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed shadow-lg`;
        toast.style.top = '24px';
        toast.style.right = '24px';
        toast.style.zIndex = '99999';
        toast.style.minWidth = '300px';
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="feather-${type === 'success' ? 'check-circle' : 'alert-circle'} fs-5"></i>
                <div class="fw-semibold">${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    settingsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        const formData = new FormData(settingsForm);

        fetch(settingsForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            if (data.success) {
                showToast(data.message || @json(__('messages.ads_settings_saved')), 'success');
            } else {
                showToast(data.message || @json(__('messages.operation_failed') ?? 'Operation failed.'), 'error');
            }
        })
        .catch(err => {
            console.error('Settings save error:', err);
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
            showToast(@json(__('messages.network_error') ?? 'Network error.'), 'error');
        });
    });
});
</script>
@endpush
