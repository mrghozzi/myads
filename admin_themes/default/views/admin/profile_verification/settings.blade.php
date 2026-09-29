@extends('admin::layouts.admin')

@section('title', __('messages.verification_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb"><li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li><li>{{ __('messages.verification_settings') }}</li></ul>
            <div class="admin-hero__eyebrow">{{ __('messages.users') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.verification_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.verification_admin_settings_intro') }}</p>
        </div>
    </section>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ __('messages.please_check_errors') }}</div>@endif

    <form method="POST" action="{{ route('admin.profile_verification.settings.update') }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-body">
                <h5 class="card-title mb-3"><i class="feather-shield text-primary me-2"></i>{{ __('messages.verification_access_rules') }}</h5>
                <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1" {{ old('enabled', $settings['enabled']) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="enabled">{{ __('messages.verification_enable_requests') }}</label><div class="small text-muted">{{ __('messages.verification_enable_help') }}</div></div>
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="require_verified_email" name="require_verified_email" value="1" {{ old('require_verified_email', $settings['require_verified_email']) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="require_verified_email">{{ __('messages.verification_require_email') }}</label></div>
            </div></div></div>
            <div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-body">
                <h5 class="card-title mb-3"><i class="feather-users text-primary me-2"></i>{{ __('messages.verification_activity_thresholds') }}</h5>
                <div class="mb-3"><label for="min_account_age_days" class="form-label">{{ __('messages.verification_min_account_age') }}</label><input id="min_account_age_days" name="min_account_age_days" type="number" min="0" max="36500" required class="form-control" value="{{ old('min_account_age_days', $settings['min_account_age_days']) }}"></div>
                <div><label for="min_followers_count" class="form-label">{{ __('messages.verification_min_followers') }}</label><input id="min_followers_count" name="min_followers_count" type="number" min="0" max="100000000" required class="form-control" value="{{ old('min_followers_count', $settings['min_followers_count']) }}"></div>
            </div></div></div>
        </div>
        <div class="mt-4"><button class="btn btn-primary"><i class="feather-save me-2"></i>{{ __('messages.save_changes') }}</button></div>
    </form>
</div>
@endsection
