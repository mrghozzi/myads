@extends('admin::layouts.admin')

@section('title', __('messages.dev_platform_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.developers') }}">{{ __('messages.dev_platform') }}</a></li>
                <li>{{ __('messages.settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">@lang('messages.configuration')</div>
            <h1 class="admin-hero__title">@lang('messages.dev_platform_settings')</h1>
            <p class="admin-hero__copy">@lang('messages.dev_platform_settings_desc')</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.developers') }}" class="btn btn-light">
                <i class="feather-arrow-left me-1"></i> @lang('messages.back')
            </a>
        </div>
    </section>

    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <form action="{{ route('admin.developers.settings.update') }}" method="POST">
                @csrf
                <section class="admin-panel mb-4">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">@lang('messages.status')</span>
                            <h2 class="admin-panel__title">@lang('messages.platform_status')</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body">
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input" type="checkbox" name="enabled" id="enabled" value="1" {{ $settings->isEnabled() ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="enabled">@lang('messages.enable_dev_platform')</label>
                            <div class="form-text mt-1 small">@lang('messages.enable_dev_platform_desc')</div>
                        </div>
                    </div>
                </section>

                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">@lang('messages.eligibility')</span>
                            <h2 class="admin-panel__title">@lang('messages.eligibility_rules')</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body">
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="require_admin_approval" id="require_admin_approval" value="1" {{ $settings->requireAdminApproval() ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="require_admin_approval">@lang('messages.require_admin_approval')</label>
                                <div class="form-text small">@lang('messages.require_admin_approval_desc')</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4 pb-4 border-bottom">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">@lang('messages.min_account_age_days')</label>
                                <div class="input-group">
                                    <input type="number" name="min_account_age_days" class="form-control" value="{{ $settings->getMinAccountAgeDays() }}" min="0">
                                    <span class="input-group-text">@lang('messages.days')</span>
                                </div>
                                <div class="form-text small">@lang('messages.min_account_age_days_desc')</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">@lang('messages.min_followers_count')</label>
                                <div class="input-group">
                                    <input type="number" name="min_followers_count" class="form-control" value="{{ $settings->getMinFollowersCount() }}" min="0">
                                    <span class="input-group-text"><i class="feather-users"></i></span>
                                </div>
                                <div class="form-text small">@lang('messages.min_followers_count_desc')</div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="require_paid_plan" id="require_paid_plan" value="1" {{ $settings->requirePaidPlan() ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="require_paid_plan">@lang('messages.require_paid_plan')</label>
                                <div class="form-text small">@lang('messages.require_paid_plan_desc')</div>
                            </div>
                        </div>
                    </div>
                    <div class="admin-panel__footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-save me-1"></i> @lang('messages.save_settings')
                        </button>
                    </div>
                </section>
            </form>
        </div>

        <div class="col-lg-4">
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">@lang('messages.information')</span>
                        <h2 class="admin-panel__title">@lang('messages.platform_info')</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <div class="avatar-text bg-primary-subtle text-primary" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="feather-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-13">@lang('messages.v1_api')</div>
                                <div class="text-muted small">@lang('messages.v1_api_desc')</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <div class="avatar-text bg-success-subtle text-success" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="feather-shield"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-13">@lang('messages.oauth_secured')</div>
                                <div class="text-muted small">@lang('messages.oauth_secured_desc')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
