@extends('admin::layouts.admin')

@section('title', __('messages.billing_plans_title'))

@section('content')
@php
    $extraIncludedBenefitsText = old('extra_included_benefits_text', implode(PHP_EOL, (array) ($entitlementDefaults['extra_included_benefits'] ?? [])));
@endphp

<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_plans_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_plans_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_plans_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            @if($editingPlan)
                <a href="{{ route('admin.billing.plans') }}" class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1.25rem; background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <i class="feather-plus-circle text-primary fs-5"></i>
                    <span>{{ __('messages.billing_create_plan_title') }}</span>
                </a>
            @else
                <button type="button" onclick="document.getElementById('plan-name-input')?.focus();" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-plus-circle fs-5"></i>
                    <span>{{ __('messages.billing_create_plan_title') }}</span>
                </button>
            @endif
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'plans'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4">
            {{-- Plans Catalog (Left Column) --}}
            <div class="col-xl-7">
                <div class="admin-panel h-100">
                    <div class="admin-panel__header d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_plans_tab') }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_plans_library') }}</h3>
                        </div>
                        <form method="GET" action="{{ route('admin.billing.plans') }}" class="d-flex align-items-center gap-2">
                            <div class="input-group" style="max-width: 260px;">
                                <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="border-radius: 10px 0 0 10px; font-size: 0.85rem;">
                                <button type="submit" class="btn btn-primary px-3" style="border-radius: 0 10px 10px 0;">
                                    <i class="feather-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="admin-panel__body p-0">
                        <div class="admin-table-wrap">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.billing_duration_label') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plans as $plan)
                                        @php($accent = $plan->accent_color ?: '#615dfa')
                                        <tr class="transition-all {{ $editingPlan && $editingPlan->id === $plan->id ? 'bg-soft-primary' : '' }}">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                                                         style="width: 40px; height: 40px; background-color: {{ $accent }}18; color: {{ $accent }}; border: 1px solid {{ $accent }}40;">
                                                        <i class="feather-layers fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-14">{{ $plan->name }}</div>
                                                        @if($plan->recommended_text)
                                                            <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25 rounded-pill px-2 py-0 mt-1 fs-11">
                                                                <i class="feather-star me-1"></i>{{ $plan->recommended_text }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted fw-semibold fs-13">
                                                {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}
                                            </td>
                                            <td class="fw-bold text-dark fs-14">
                                                {{ number_format((float) $plan->base_price, 2) }}
                                                <small class="text-muted fw-normal fs-11 ms-1">{{ $settings['base_currency_code'] ?? 'USD' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <span class="badge {{ $plan->is_active ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-2 py-1 fs-11">
                                                        {{ $plan->is_active ? __('messages.active') : __('messages.inactive') }}
                                                    </span>
                                                    @if($plan->is_featured)
                                                        <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                            <i class="feather-award me-1"></i>{{ __('messages.billing_featured_plan') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('admin.billing.plans', ['edit' => $plan->id]) }}" class="btn btn-sm btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                                                    <i class="feather-edit-2"></i>
                                                    <span>{{ __('messages.edit') }}</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                        <i class="feather-inbox fs-3"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ __('messages.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($plans->hasPages())
                        <div class="admin-panel__footer d-flex justify-content-center p-3">
                            {{ $plans->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Plan Form (Right Column) --}}
            <div class="col-xl-5">
                <form id="billing-plan-form" action="{{ $editingPlan ? route('admin.billing.plans.update', $editingPlan->id) : route('admin.billing.plans.store') }}" method="POST" class="d-grid gap-4">
                    @csrf
                    {{-- Basic Details --}}
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ $editingPlan ? __('messages.edit') : __('messages.add') }}</div>
                                <h3 class="admin-panel__title">{{ $editingPlan ? __('messages.billing_edit_plan_title') : __('messages.billing_create_plan_title') }}</h3>
                            </div>
                            @if($editingPlan)
                                <a href="{{ route('admin.billing.plans') }}" class="btn btn-sm btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ __('messages.cancel') }}">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="plan-name-input" name="name" class="form-control" value="{{ old('name', $editingPlan->name ?? '') }}" required placeholder="{{ __('messages.billing_plan_name_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.description') }}
                                    </label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="{{ __('messages.billing_plan_desc_placeholder') }}" style="border-radius: 10px;">{{ old('description', $editingPlan->description ?? '') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.amount') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="base_price" class="form-control" value="{{ old('base_price', $editingPlan->base_price ?? 0) }}" required style="border-radius: 10px 0 0 10px;">
                                        <span class="input-group-text bg-white fw-bold text-muted" style="border-radius: 0 10px 10px 0;">{{ $settings['base_currency_code'] ?? 'USD' }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_duration_label') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" min="1" name="duration_days" class="form-control" value="{{ old('duration_days', $editingPlan->duration_days ?? 30) }}" style="border-radius: 10px 0 0 10px;">
                                        <span class="input-group-text bg-white text-muted" style="border-radius: 0 10px 10px 0;">{{ __('messages.days') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_recommended_label') }}
                                    </label>
                                    <input type="text" name="recommended_text" class="form-control" value="{{ old('recommended_text', $editingPlan->recommended_text ?? '') }}" placeholder="{{ __('messages.billing_recommended_badge_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_accent_color_label') }}
                                    </label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" id="plan-color-picker" name="accent_color" class="form-control form-control-color border" value="{{ old('accent_color', $editingPlan->accent_color ?? '#615dfa') }}" style="border-radius: 10px; width: 44px; height: 38px;">
                                        <input type="text" id="plan-color-text" class="form-control font-monospace fs-12" value="{{ old('accent_color', $editingPlan->accent_color ?? '#615dfa') }}" style="border-radius: 10px;" readonly>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_marketing_bullets_label') }}
                                    </label>
                                    <textarea name="marketing_bullets_text" class="form-control" rows="3" placeholder="{{ __('messages.billing_marketing_bullets_placeholder') }}" style="border-radius: 10px;">{{ old('marketing_bullets_text', implode(PHP_EOL, (array) ($editingPlan->marketing_bullets ?? []))) }}</textarea>
                                </div>

                                {{-- Switches --}}
                                <div class="col-12 mt-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-dark fs-12 mb-0 cursor-pointer" for="is_active">{{ __('messages.active') }}</label>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input shadow-sm" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $editingPlan->is_active ?? true))>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-dark fs-12 mb-0 cursor-pointer" for="is_featured">{{ __('messages.billing_featured_plan') }}</label>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input shadow-sm" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $editingPlan->is_featured ?? false))>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-dark fs-12 mb-0 cursor-pointer" for="is_lifetime">{{ __('messages.billing_lifetime') }}</label>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input shadow-sm" type="checkbox" name="is_lifetime" id="is_lifetime" value="1" @checked(old('is_lifetime', $editingPlan->is_lifetime ?? false))>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-2 rounded-3" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-muted small text-uppercase fs-11 mb-1">{{ __('messages.order') }}</label>
                                                <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ old('sort_order', $editingPlan->sort_order ?? 0) }}" style="border-radius: 8px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entitlements & Bonuses --}}
                    <div class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_entitlements_title') }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_plan_benefits_title') }}</h3>
                            </div>
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bonus_pts_field') }}</label>
                                    <input type="number" min="0" name="bonus_pts" class="form-control" value="{{ old('bonus_pts', $entitlementDefaults['bonus_pts'] ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bonus_nvu_field') }}</label>
                                    <input type="number" min="0" name="bonus_nvu" class="form-control" value="{{ old('bonus_nvu', $entitlementDefaults['bonus_nvu'] ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bonus_nlink_field') }}</label>
                                    <input type="number" min="0" name="bonus_nlink" class="form-control" value="{{ old('bonus_nlink', $entitlementDefaults['bonus_nlink'] ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bonus_nsmart_field') }}</label>
                                    <input type="number" min="0" name="bonus_nsmart" class="form-control" value="{{ old('bonus_nsmart', $entitlementDefaults['bonus_nsmart'] ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_discount_field') }}</label>
                                    <input type="number" min="0" max="95" name="status_promotion_discount_pct" class="form-control" value="{{ old('status_promotion_discount_pct', $entitlementDefaults['status_promotion_discount_pct'] ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_badge_label_field') }}</label>
                                    <input type="text" name="profile_badge_label" class="form-control" value="{{ old('profile_badge_label', $entitlementDefaults['profile_badge_label'] ?? '') }}" placeholder="{{ __('messages.billing_profile_badge_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_plan_highlights_title') }}</label>
                                    <textarea name="extra_included_benefits_text" class="form-control" rows="3" placeholder="{{ __('messages.billing_extra_included_benefits_placeholder') }}" style="border-radius: 10px;">{{ $extraIncludedBenefitsText }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel__footer d-flex align-items-center justify-content-between p-3 bg-light">
                            <span class="text-muted fs-12">{{ __('messages.billing_plan_form_note') }}</span>
                            <button type="submit" id="btn-save-plan" class="btn btn-primary fw-bold shadow-sm px-4 d-inline-flex align-items-center gap-2" style="border-radius: 12px;">
                                <i class="feather-save"></i>
                                <span>{{ $editingPlan ? __('messages.save_changes') : __('messages.save') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live color picker sync
    var colorPicker = document.getElementById('plan-color-picker');
    var colorText = document.getElementById('plan-color-text');
    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = colorPicker.value;
        });
    }

    // AJAX Form Submission
    var planForm = document.getElementById('billing-plan-form');
    var planBtn = document.getElementById('btn-save-plan');
    if (planForm && planBtn) {
        planForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var originalHtml = planBtn.innerHTML;
            planBtn.disabled = true;
            planBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("messages.saving") }}';

            var formData = new FormData(planForm);

            fetch(planForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { status: res.status, data: data };
                });
            })
            .then(function(result) {
                planBtn.disabled = false;
                planBtn.innerHTML = originalHtml;

                if (result.status >= 200 && result.status < 300 && result.data.success) {
                    window.showBillingToast(result.data.message || '{{ __("messages.billing_plan_saved") }}', 'success');
                    // Reload clean list after brief delay so user sees toast
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.billing.plans") }}';
                    }, 800);
                } else {
                    var errorMsg = result.data.message || '{{ __("messages.error_occurred") }}';
                    if (result.data.errors) {
                        var firstKey = Object.keys(result.data.errors)[0];
                        if (firstKey && result.data.errors[firstKey][0]) {
                            errorMsg = result.data.errors[firstKey][0];
                        }
                    }
                    window.showBillingToast(errorMsg, 'danger');
                }
            })
            .catch(function(err) {
                planBtn.disabled = false;
                planBtn.innerHTML = originalHtml;
                window.showBillingToast(err.message || '{{ __("messages.error_occurred") }}', 'danger');
            });
        });
    }
});
</script>
@endpush
