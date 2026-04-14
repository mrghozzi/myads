@extends('admin::layouts.admin')

@section('title', __('messages.billing_plans_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_plans_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_plans_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_plans_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'plans'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-3 mt-1">
            <div class="col-xl-7">
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_plans_tab') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.billing_plans_library') }}</h2>
                        </div>
                        <form method="GET" action="{{ route('admin.billing.plans') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}">
                            <button type="submit" class="btn btn-light">{{ __('messages.search') }}</button>
                        </form>
                    </div>
                    <div class="admin-panel__body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.billing_duration_label') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plans as $plan)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $plan->name }}</div>
                                                <div class="text-muted small">{{ $plan->recommended_text }}</div>
                                            </td>
                                            <td>
                                                {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}
                                            </td>
                                            <td>{{ number_format((float) $plan->base_price, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $plan->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                    {{ $plan->is_active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                                @if($plan->is_featured)
                                                    <span class="badge bg-primary-subtle text-primary">{{ __('messages.billing_featured_plan') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.billing.plans', ['edit' => $plan->id]) }}" class="btn btn-sm btn-light">{{ __('messages.edit') }}</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                @if($plans->hasPages())
                    <div class="mt-3">
                        {{ $plans->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <div class="col-xl-5">
                <form action="{{ $editingPlan ? route('admin.billing.plans.update', $editingPlan->id) : route('admin.billing.plans.store') }}" method="POST" class="d-grid gap-3">
                    @csrf
                    <section class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ $editingPlan ? __('messages.edit') : __('messages.add') }}</span>
                                <h2 class="admin-panel__title">{{ $editingPlan ? __('messages.billing_edit_plan_title') : __('messages.billing_create_plan_title') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $editingPlan->name ?? '') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('messages.description') }}</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $editingPlan->description ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.amount') }}</label>
                                    <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', $editingPlan->base_price ?? 0) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_duration_label') }}</label>
                                    <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $editingPlan->duration_days ?? 30) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_recommended_label') }}</label>
                                    <input type="text" name="recommended_text" class="form-control" value="{{ old('recommended_text', $editingPlan->recommended_text ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_accent_color_label') }}</label>
                                    <input type="text" name="accent_color" class="form-control" value="{{ old('accent_color', $editingPlan->accent_color ?? '#615dfa') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('messages.billing_marketing_bullets_label') }}</label>
                                    <textarea name="marketing_bullets_text" class="form-control" rows="4">{{ old('marketing_bullets_text', implode(PHP_EOL, (array) ($editingPlan->marketing_bullets ?? []))) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $editingPlan->is_featured ?? false))>
                                        <label class="form-check-label" for="is_featured">{{ __('messages.billing_featured_plan') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $editingPlan->is_active ?? true))>
                                        <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_lifetime" id="is_lifetime" value="1" @checked(old('is_lifetime', $editingPlan->is_lifetime ?? false))>
                                        <label class="form-check-label" for="is_lifetime">{{ __('messages.billing_lifetime') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $editingPlan->sort_order ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.billing_entitlements_title') }}</span>
                                <h2 class="admin-panel__title">{{ __('messages.billing_plan_benefits_title') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_badge_label_field') }}</label>
                                    <input type="text" name="profile_badge_label" class="form-control" value="{{ old('profile_badge_label', $entitlementDefaults['profile_badge_label'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_badge_color_field') }}</label>
                                    <input type="text" name="profile_badge_color" class="form-control" value="{{ old('profile_badge_color', $entitlementDefaults['profile_badge_color'] ?? '#615dfa') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_bonus_pts_field') }}</label>
                                    <input type="number" step="0.01" name="bonus_pts" class="form-control" value="{{ old('bonus_pts', $entitlementDefaults['bonus_pts'] ?? 0) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_discount_field') }}</label>
                                    <input type="number" step="0.01" name="status_promotion_discount_pct" class="form-control" value="{{ old('status_promotion_discount_pct', $entitlementDefaults['status_promotion_discount_pct'] ?? 0) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.billing_bonus_nvu_field') }}</label>
                                    <input type="number" step="0.01" name="bonus_nvu" class="form-control" value="{{ old('bonus_nvu', $entitlementDefaults['bonus_nvu'] ?? 0) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.billing_bonus_nlink_field') }}</label>
                                    <input type="number" step="0.01" name="bonus_nlink" class="form-control" value="{{ old('bonus_nlink', $entitlementDefaults['bonus_nlink'] ?? 0) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.billing_bonus_nsmart_field') }}</label>
                                    <input type="number" step="0.01" name="bonus_nsmart" class="form-control" value="{{ old('bonus_nsmart', $entitlementDefaults['bonus_nsmart'] ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <div class="admin-panel__body d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <p class="text-muted mb-0">{{ __('messages.billing_plan_form_note') }}</p>
                            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
