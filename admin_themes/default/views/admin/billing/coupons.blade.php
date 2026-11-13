@extends('admin::layouts.admin')

@section('title', __('messages.billing_coupons_title'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_coupons_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_coupons_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_coupons_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            @if($editingCoupon)
                <a href="{{ route('admin.billing.coupons') }}" class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1.25rem; background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <i class="feather-plus-circle text-primary fs-5"></i>
                    <span>{{ __('messages.billing_create_coupon_title') }}</span>
                </a>
            @else
                <button type="button" onclick="document.getElementById('coupon-code-input')?.focus();" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-plus-circle fs-5"></i>
                    <span>{{ __('messages.billing_create_coupon_title') }}</span>
                </button>
            @endif
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'coupons'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4">
            {{-- Coupons Catalog (Left Column) --}}
            <div class="col-xl-7">
                <div class="admin-panel h-100">
                    <div class="admin-panel__header d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_coupons_tab') }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_coupons_library') }}</h3>
                        </div>
                        <form method="GET" action="{{ route('admin.billing.coupons') }}" class="d-flex align-items-center gap-2">
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
                                        <th class="ps-4">{{ __('messages.billing_coupon_code') }}</th>
                                        <th>{{ __('messages.billing_discount_type') }}</th>
                                        <th>{{ __('messages.billing_plan_label') }}</th>
                                        <th>{{ __('messages.billing_coupon_usage') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($coupons as $coupon)
                                        <tr class="transition-all {{ $editingCoupon && $editingCoupon->id === $coupon->id ? 'bg-soft-primary' : '' }}">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                                                         style="width: 40px; height: 40px; background-color: rgba(97,93,250,0.12); color: #615dfa; border: 1px solid rgba(97,93,250,0.25);">
                                                        <i class="feather-tag fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold font-monospace text-dark fs-14">{{ $coupon->code }}</div>
                                                        @if($coupon->description)
                                                            <small class="text-muted d-block fs-11">{{ $coupon->description }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-success text-success fw-bold px-2 py-1 fs-12 border border-success border-opacity-25 rounded-pill">
                                                    @if($coupon->discount_type === 'percent')
                                                        -{{ $coupon->discount_value }}%
                                                    @else
                                                        -{{ number_format((float) $coupon->discount_value, 2) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-muted fw-semibold fs-13">
                                                @if($coupon->plan)
                                                    <span class="badge bg-soft-info text-info border border-info border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ $coupon->plan->name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-muted border border-secondary border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ __('messages.billing_all_plans') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted fw-medium fs-13">
                                                <span>{{ $coupon->used_count }}</span>
                                                @if($coupon->max_uses)
                                                    <span class="text-muted">/ {{ $coupon->max_uses }}</span>
                                                @else
                                                    <span class="text-muted">/ &infin;</span>
                                                @endif
                                                @if($coupon->expires_at)
                                                    <div class="fs-10 text-muted mt-1">
                                                        <i class="feather-clock me-1"></i>{{ $coupon->expires_at->format('Y-m-d') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$coupon->is_active)
                                                    <span class="badge bg-soft-secondary text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ __('messages.inactive') }}
                                                    </span>
                                                @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                                                    <span class="badge bg-soft-danger text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ __('messages.billing_coupon_expired') }}
                                                    </span>
                                                @elseif($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses)
                                                    <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ __('messages.billing_coupon_exhausted') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-success text-success border border-success border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                        {{ __('messages.active') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <a href="{{ route('admin.billing.coupons', ['edit' => $coupon->id]) }}" class="btn btn-sm btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                                                        <i class="feather-edit-2"></i>
                                                        <span>{{ __('messages.edit') }}</span>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.billing.coupons.delete', $coupon->id) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm d-inline-flex align-items-center" style="border-radius: 8px;" title="{{ __('messages.delete') }}">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                        <i class="feather-tag fs-3"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ __('messages.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($coupons->hasPages())
                            <div class="p-3 border-top d-flex justify-content-center">
                                {{ $coupons->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Coupon Form (Right Column) --}}
            <div class="col-xl-5">
                <form id="billing-coupon-form" action="{{ $editingCoupon ? route('admin.billing.coupons.update', $editingCoupon->id) : route('admin.billing.coupons.store') }}" method="POST" class="d-grid gap-4">
                    @csrf
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ $editingCoupon ? __('messages.edit') : __('messages.add') }}</div>
                                <h3 class="admin-panel__title">{{ $editingCoupon ? __('messages.billing_edit_coupon_title') : __('messages.billing_create_coupon_title') }}</h3>
                            </div>
                            @if($editingCoupon)
                                <a href="{{ route('admin.billing.coupons') }}" class="btn btn-sm btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ __('messages.cancel') }}">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_coupon_code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="coupon-code-input" name="code" class="form-control font-monospace text-uppercase" value="{{ old('code', $editingCoupon->code ?? '') }}" required placeholder="e.g. SUMMER25" style="border-radius: 10px;">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.description') }}
                                    </label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description', $editingCoupon->description ?? '') }}" placeholder="{{ __('messages.billing_coupon_desc_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_discount_type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="discount_type" class="form-select" style="border-radius: 10px;">
                                        @php($type = old('discount_type', $editingCoupon->discount_type ?? 'percent'))
                                        <option value="percent" @selected($type === 'percent')>{{ __('messages.billing_discount_percent') }} (%)</option>
                                        <option value="fixed" @selected($type === 'fixed')>{{ __('messages.billing_discount_fixed') }} ($)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_discount_value') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $editingCoupon->discount_value ?? 10) }}" required style="border-radius: 10px;">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_applicable_plan') }}
                                    </label>
                                    <select name="subscription_plan_id" class="form-select" style="border-radius: 10px;">
                                        @php($selectedPlanId = old('subscription_plan_id', $editingCoupon->subscription_plan_id ?? ''))
                                        <option value="">{{ __('messages.billing_all_plans') }}</option>
                                        @foreach($plans as $p)
                                            <option value="{{ $p->id }}" @selected((string)$selectedPlanId === (string)$p->id)>
                                                {{ $p->name }} ({{ number_format((float) $p->base_price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted fs-11">{{ __('messages.billing_applicable_plan_help') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_max_uses') }}
                                    </label>
                                    <input type="number" min="1" name="max_uses" class="form-control" value="{{ old('max_uses', $editingCoupon->max_uses ?? '') }}" placeholder="{{ __('messages.billing_unlimited') }}" style="border-radius: 10px;">
                                    <small class="text-muted fs-11">{{ __('messages.billing_max_uses_help') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_expires_at') }}
                                    </label>
                                    <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $editingCoupon?->expires_at ? $editingCoupon->expires_at->format('Y-m-d\TH:i') : '') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-12 mt-2">
                                    <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                        <label class="fw-bold text-dark fs-13 mb-0 cursor-pointer" for="is_active">{{ __('messages.active') }}</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input shadow-sm" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $editingCoupon->is_active ?? true))>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel__footer p-4 border-top d-flex align-items-center justify-content-between gap-3">
                            @if($editingCoupon)
                                <a href="{{ route('admin.billing.coupons') }}" class="btn btn-light fw-bold text-dark" style="border-radius: 10px;">
                                    {{ __('messages.cancel') }}
                                </a>
                            @else
                                <div></div>
                            @endif
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 10px;">
                                <i class="feather-check-circle me-1"></i>
                                <span>{{ $editingCoupon ? __('messages.billing_update_coupon_btn') : __('messages.billing_save_coupon_btn') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
