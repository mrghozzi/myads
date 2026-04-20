@extends('admin::layouts.admin')

@section('title', __('messages.admin_groups_settings_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.groups.index') }}">{{ __('messages.admin_groups_title') }}</a></li>
                <li>{{ __('messages.admin_groups_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.admin_groups_settings_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.admin_groups_settings_description') }}</p>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.Settings') ?? 'Settings' }}</span>
                <h2 class="admin-panel__title">{{ __('messages.admin_groups_settings_title') }}</h2>
            </div>
        </div>
        <div class="admin-panel__body">
            @if(!$schemaReady)
                <div class="alert alert-warning mb-4">
                    <i class="feather-alert-triangle me-2"></i>
                    {{ __('messages.groups_feature_disabled') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.groups.settings.update') }}">
                @csrf

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="groups-enabled" name="enabled" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark" for="groups-enabled">{{ __('messages.groups_enabled') }}</label>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" for="creation-policy">{{ __('messages.groups_creation_policy') }}</label>
                    <select id="creation-policy" name="creation_policy" class="form-select">
                        <option value="all_members" {{ $settings['creation_policy'] === 'all_members' ? 'selected' : '' }}>{{ __('messages.groups_policy_all_members') }}</option>
                        <option value="approval" {{ $settings['creation_policy'] === 'approval' ? 'selected' : '' }}>{{ __('messages.groups_policy_approval') }}</option>
                        <option value="paid_plan" {{ $settings['creation_policy'] === 'paid_plan' ? 'selected' : '' }}>{{ __('messages.groups_policy_paid_plan') }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" for="eligible-plans">{{ __('messages.groups_eligible_plans') }}</label>
                    <select id="eligible-plans" name="eligible_plan_ids[]" class="form-select" multiple size="6" {{ !$billingReady ? 'disabled' : '' }}>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ in_array((int) $plan->id, $settings['eligible_plan_ids'], true) ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="feather-info me-1"></i>
                        {{ __('messages.groups_eligible_plans_hint') }}
                    </small>
                </div>

                <div class="admin-panel__footer px-0 pb-0 border-top-0">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="feather-save me-2"></i>
                        {{ __('messages.save_changes') }}
                    </button>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-light ms-2">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
