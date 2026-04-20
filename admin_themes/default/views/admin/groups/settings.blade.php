@extends('admin::layouts.admin')

@section('title', __('messages.admin_groups_settings_title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ __('messages.admin_groups_settings_title') }}</h1>
                    <p class="text-muted mb-0">{{ __('messages.admin_groups_settings_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(!$schemaReady)
                <div class="alert alert-warning">{{ __('messages.groups_feature_disabled') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.groups.settings.update') }}">
                @csrf

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="groups-enabled" name="enabled" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }}>
                    <label class="form-check-label" for="groups-enabled">{{ __('messages.groups_enabled') }}</label>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="creation-policy">{{ __('messages.groups_creation_policy') }}</label>
                    <select id="creation-policy" name="creation_policy" class="form-select">
                        <option value="all_members" {{ $settings['creation_policy'] === 'all_members' ? 'selected' : '' }}>{{ __('messages.groups_policy_all_members') }}</option>
                        <option value="approval" {{ $settings['creation_policy'] === 'approval' ? 'selected' : '' }}>{{ __('messages.groups_policy_approval') }}</option>
                        <option value="paid_plan" {{ $settings['creation_policy'] === 'paid_plan' ? 'selected' : '' }}>{{ __('messages.groups_policy_paid_plan') }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="eligible-plans">{{ __('messages.groups_eligible_plans') }}</label>
                    <select id="eligible-plans" name="eligible_plan_ids[]" class="form-select" multiple size="6" {{ !$billingReady ? 'disabled' : '' }}>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ in_array((int) $plan->id, $settings['eligible_plan_ids'], true) ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">{{ __('messages.groups_eligible_plans_hint') }}</small>
                </div>

                <button class="btn btn-primary" type="submit">{{ __('messages.save_changes') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
