@extends('admin::layouts.admin')

@section('title', __('messages.billing_plans_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $extraIncludedBenefitsText = old('extra_included_benefits_text', implode(PHP_EOL, (array) ($entitlementDefaults['extra_included_benefits'] ?? [])));
@endphp
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-layer-group" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>
            
            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-white">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-12 text-uppercase tracking-wider shadow-sm">
                                {{ __('messages.billing_admin_eyebrow') }}
                            </span>
                        </div>
                        <h1 class="display-5 fw-black mb-3 animate__animated animate__fadeIn">
                            {{ __('messages.billing_plans_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_plans_help') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4 pb-5">
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(var(--nxl-white-rgb), 0.8);">
        <div class="card-body p-2">
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'plans'])
        </div>
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4 mt-1">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_plans_tab') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_plans_library') }}</h4>
                        </div>
                        <form method="GET" action="{{ route('admin.billing.plans') }}" class="d-flex align-items-center gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control border-soft-light bg-light" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="border-radius: 10px 0 0 10px;">
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm px-3" style="border-radius: 0 10px 10px 0;">
                                    <i class="feather-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                                    <tr>
                                        <th class="ps-4 py-3">{{ __('messages.name') }}</th>
                                        <th class="py-3">{{ __('messages.billing_duration_label') }}</th>
                                        <th class="py-3">{{ __('messages.amount') }}</th>
                                        <th class="py-3">{{ __('messages.status') }}</th>
                                        <th class="pe-4 py-3 text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-13">
                                    @forelse($plans as $plan)
                                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; background-color: {{ $plan->accent_color ?? '#615dfa' }}20; color: {{ $plan->accent_color ?? '#615dfa' }}; border: 1px solid {{ $plan->accent_color ?? '#615dfa' }}50;">
                                                        <i class="feather-box"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-14">{{ $plan->name }}</div>
                                                        @if($plan->recommended_text)
                                                            <div class="text-muted small fs-11 mt-1"><i class="feather-star text-warning me-1"></i> {{ $plan->recommended_text }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted fw-semibold">
                                                {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}
                                            </td>
                                            <td class="fw-bold fs-14">{{ number_format((float) $plan->base_price, 2) }}</td>
                                            <td>
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <span class="badge {{ $plan->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }} rounded-pill border">
                                                        {{ $plan->is_active ? __('messages.active') : __('messages.inactive') }}
                                                    </span>
                                                    @if($plan->is_featured)
                                                        <span class="badge bg-soft-primary text-primary rounded-pill border"><i class="feather-award me-1"></i> {{ __('messages.billing_featured_plan') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('admin.billing.plans', ['edit' => $plan->id]) }}" class="btn btn-sm btn-primary fw-bold shadow-sm" style="border-radius: 8px;">
                                                    <i class="feather-edit-2"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-soft-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                                        <i class="feather-inbox fs-3 text-secondary"></i>
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
                        <div class="card-footer bg-transparent border-top border-soft-light p-4">
                            {{ $plans->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-5">
                <form action="{{ $editingPlan ? route('admin.billing.plans.update', $editingPlan->id) : route('admin.billing.plans.store') }}" method="POST" class="d-grid gap-4">
                    @csrf
                    <!-- Basic Details -->
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase tracking-wider fw-bold text-primary mb-1 fs-11">{{ $editingPlan ? __('messages.edit') : __('messages.add') }}</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $editingPlan ? __('messages.billing_edit_plan_title') : __('messages.billing_create_plan_title') }}</h4>
                            </div>
                            @if($editingPlan)
                                <a href="{{ route('admin.billing.plans') }}" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control border-soft-light bg-light" value="{{ old('name', $editingPlan->name ?? '') }}" required style="border-radius: 10px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.description') }}</label>
                                    <textarea name="description" class="form-control border-soft-light bg-light" rows="3" style="border-radius: 10px;">{{ old('description', $editingPlan->description ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.amount') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="base_price" class="form-control border-soft-light bg-light" value="{{ old('base_price', $editingPlan->base_price ?? 0) }}" required style="border-radius: 10px 0 0 10px;">
                                        <span class="input-group-text border-soft-light bg-soft-light fw-bold text-muted" style="border-radius: 0 10px 10px 0;">{{ $settings['base_currency_code'] ?? 'USD' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_duration_label') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="duration_days" class="form-control border-soft-light bg-light" value="{{ old('duration_days', $editingPlan->duration_days ?? 30) }}" style="border-radius: 10px 0 0 10px;">
                                        <span class="input-group-text border-soft-light bg-soft-light text-muted" style="border-radius: 0 10px 10px 0;">{{ __('messages.days') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_recommended_label') }}</label>
                                    <input type="text" name="recommended_text" class="form-control border-soft-light bg-light" value="{{ old('recommended_text', $editingPlan->recommended_text ?? '') }}" placeholder="e.g. Most Popular" style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_accent_color_label') }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="accent_color" class="form-control form-control-color border-soft-light bg-light" value="{{ old('accent_color', $editingPlan->accent_color ?? '#615dfa') }}" title="Choose your color" style="border-radius: 10px; width: 50px; height: 42px; padding: 0.375rem;">
                                        <span class="text-muted font-monospace small" id="colorValue">{{ old('accent_color', $editingPlan->accent_color ?? '#615dfa') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_marketing_bullets_label') }}</label>
                                    <textarea name="marketing_bullets_text" class="form-control border-soft-light bg-light" rows="4" placeholder="One per line..." style="border-radius: 10px;">{{ old('marketing_bullets_text', implode(PHP_EOL, (array) ($editingPlan->marketing_bullets ?? []))) }}</textarea>
                                </div>
                                
                                <div class="col-12 mt-2">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100">
                                                <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $editingPlan->is_featured ?? false)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="is_featured">
                                                        {{ __('messages.billing_featured_plan') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100">
                                                <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $editingPlan->is_active ?? true)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="is_active">
                                                        {{ __('messages.active') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100">
                                                <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="is_lifetime" id="is_lifetime" value="1" @checked(old('is_lifetime', $editingPlan->is_lifetime ?? false)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="is_lifetime">
                                                        {{ __('messages.billing_lifetime') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="h-100">
                                                <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.order') }}</label>
                                                <input type="number" name="sort_order" class="form-control border-soft-light bg-light" value="{{ old('sort_order', $editingPlan->sort_order ?? 0) }}" style="border-radius: 10px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entitlements -->
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light">
                            <div class="text-uppercase tracking-wider fw-bold text-info mb-1 fs-11">{{ __('messages.billing_entitlements_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_plan_benefits_title') }}</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-grow-1 border-top border-soft-light"></div>
                                        <div class="px-3 small text-uppercase fw-bold tracking-wider text-muted">{{ __('messages.billing_plan_active_benefits_title') }}</div>
                                        <div class="flex-grow-1 border-top border-soft-light"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_badge_label_field') }}</label>
                                    <input type="text" name="profile_badge_label" class="form-control border-soft-light bg-light" value="{{ old('profile_badge_label', $entitlementDefaults['profile_badge_label'] ?? '') }}" placeholder="e.g. PRO" style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_badge_color_field') }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="profile_badge_color" class="form-control form-control-color border-soft-light bg-light" value="{{ old('profile_badge_color', $entitlementDefaults['profile_badge_color'] ?? '#615dfa') }}" style="border-radius: 10px; width: 50px; height: 42px; padding: 0.375rem;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_discount_field') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="status_promotion_discount_pct" class="form-control border-soft-light bg-light" value="{{ old('status_promotion_discount_pct', $entitlementDefaults['status_promotion_discount_pct'] ?? 0) }}" style="border-radius: 10px 0 0 10px;">
                                        <span class="input-group-text border-soft-light bg-soft-light text-muted" style="border-radius: 0 10px 10px 0;">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">&nbsp;</label>
                                    <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100" style="margin-top: -8px;">
                                        <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                            <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="subscription_verified_badge" id="subscription_verified_badge" value="1" @checked(old('subscription_verified_badge', $entitlementDefaults['subscription_verified_badge'] ?? false)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                            <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="subscription_verified_badge">
                                                {{ __('messages.billing_verified_badge_field') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_extra_included_benefits_field') }}</label>
                                    <textarea name="extra_included_benefits_text" class="form-control border-soft-light bg-light" rows="4" placeholder="{{ __('messages.billing_extra_included_benefits_placeholder') }}" style="border-radius: 10px;">{{ $extraIncludedBenefitsText }}</textarea>
                                    <div class="form-text mt-2"><i class="feather-info me-1"></i> {{ __('messages.billing_extra_included_benefits_help') }}</div>
                                </div>
                                
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-grow-1 border-top border-soft-light"></div>
                                        <div class="px-3 small text-uppercase fw-bold tracking-wider text-muted">{{ __('messages.billing_plan_one_time_bonuses_title') }}</div>
                                        <div class="flex-grow-1 border-top border-soft-light"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_bonus_nvu_field') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-soft-light bg-soft-primary text-primary border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-eye"></i></span>
                                        <input type="number" step="0.01" name="bonus_nvu" class="form-control border-soft-light bg-light border-start-0 ps-0" value="{{ old('bonus_nvu', $entitlementDefaults['bonus_nvu'] ?? 0) }}" style="border-radius: 0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_bonus_nlink_field') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-soft-light bg-soft-info text-info border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-link"></i></span>
                                        <input type="number" step="0.01" name="bonus_nlink" class="form-control border-soft-light bg-light border-start-0 ps-0" value="{{ old('bonus_nlink', $entitlementDefaults['bonus_nlink'] ?? 0) }}" style="border-radius: 0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_bonus_nsmart_field') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-soft-light bg-soft-warning text-warning border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-bolt"></i></span>
                                        <input type="number" step="0.01" name="bonus_nsmart" class="form-control border-soft-light bg-light border-start-0 ps-0" value="{{ old('bonus_nsmart', $entitlementDefaults['bonus_nsmart'] ?? 0) }}" style="border-radius: 0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_bonus_pts_field') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-soft-light bg-soft-success text-success border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fa-solid fa-coins"></i></span>
                                        <input type="number" step="0.01" name="bonus_pts" class="form-control border-soft-light bg-light border-start-0 ps-0" value="{{ old('bonus_pts', $entitlementDefaults['bonus_pts'] ?? 0) }}" style="border-radius: 0 10px 10px 0;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top border-soft-light p-4 d-flex justify-content-between align-items-center gap-3 flex-wrap" style="border-radius: 0 0 20px 20px;">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="feather-info fs-5 text-primary"></i>
                                <span class="fs-14">{{ __('messages.billing_plan_form_note') }}</span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm hover-scale px-5" style="border-radius: 12px;">
                                <i class="feather-save me-2"></i> {{ __('messages.save_changes') }}
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
<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .fw-black { font-weight: 900; }
    .opacity-10 { opacity: 0.1; }
    .opacity-80 { opacity: 0.8; }
    .z-index-1 { z-index: 1; }
    .fs-11 { font-size: 11px; }
    .fs-12 { font-size: 12px; }
    .fs-13 { font-size: 13px; }
    .fs-14 { font-size: 14px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .cursor-pointer { cursor: pointer; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.querySelector('input[name="accent_color"]');
        const colorValue = document.getElementById('colorValue');
        if(colorInput && colorValue) {
            colorInput.addEventListener('input', function() {
                colorValue.textContent = this.value;
            });
        }
    });
</script>
@endpush
