@extends('admin::layouts.admin')

@section('title', __('messages.billing_feature_title'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_overview_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_feature_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_admin_overview_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge {{ !empty($settings['enabled']) ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-power me-1"></i>
                    {{ !empty($settings['enabled']) ? __('messages.billing_system_enabled') : __('messages.billing_system_disabled') }}
                </span>
                <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-dollar-sign me-1"></i>
                    {{ $settings['base_currency_code'] ?? 'USD' }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                <a href="{{ route('admin.billing.plans') }}" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-plus-circle fs-5"></i>
                    <span>{{ __('messages.billing_create_plan_title') }}</span>
                </a>
                <a href="{{ route('admin.billing.settings') }}" class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem; background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <i class="feather-sliders fs-5 text-primary"></i>
                    <span>{{ __('messages.billing_settings_tab') }}</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'overview'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        {{-- KPI Stat Strip --}}
        <div class="admin-summary-grid mb-4">
            {{-- Active Plans --}}
            <div class="admin-stat-card d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="admin-stat-label mb-1">{{ __('messages.billing_summary_active_plans') }}</span>
                    <span class="admin-stat-value text-primary">{{ $summary['active_plans'] }}</span>
                </div>
                <div class="bg-soft-primary text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="feather-layers fs-3"></i>
                </div>
            </div>

            {{-- Active Subscriptions --}}
            <div class="admin-stat-card d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="admin-stat-label mb-1">{{ __('messages.billing_summary_active_subscriptions') }}</span>
                    <span class="admin-stat-value text-success">{{ $summary['active_subscriptions'] }}</span>
                </div>
                <div class="bg-soft-success text-success rounded-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="feather-users fs-3"></i>
                </div>
            </div>

            {{-- Pending Transfers --}}
            <div class="admin-stat-card d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="admin-stat-label mb-1">{{ __('messages.billing_summary_pending_transfers') }}</span>
                    <span class="admin-stat-value {{ $summary['pending_bank_transfers'] > 0 ? 'text-warning' : 'text-dark' }}">
                        {{ $summary['pending_bank_transfers'] }}
                    </span>
                </div>
                <div class="bg-soft-warning text-warning rounded-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="feather-clock fs-3"></i>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="admin-stat-card d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="admin-stat-label mb-1">{{ __('messages.billing_summary_monthly_revenue') }}</span>
                    <span class="admin-stat-value text-info fs-4">
                        {{ number_format((float) $summary['monthly_revenue'], 2) }}
                        <small class="fs-12 fw-normal text-muted ms-1">{{ $settings['base_currency_code'] ?? 'USD' }}</small>
                    </span>
                </div>
                <div class="bg-soft-info text-info rounded-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="feather-trending-up fs-3"></i>
                </div>
            </div>
        </div>

        {{-- Split Content Grid --}}
        <div class="row g-4">
            {{-- Recent Activity / Orders Table --}}
            <div class="col-xl-8">
                <div class="admin-panel h-100">
                    <div class="admin-panel__header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_orders_title') }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_recent_activity') }}</h3>
                        </div>
                        <a href="{{ route('admin.billing.orders') }}" class="btn btn-sm btn-light fw-bold text-dark d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 10px; border: 1px solid var(--admin-premium-border);">
                            <span>{{ __('messages.view_all') }}</span>
                            <i class="feather-arrow-left"></i>
                        </a>
                    </div>

                    <div class="admin-panel__body p-0">
                        <div class="admin-table-wrap">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">{{ __('messages.billing_order_number_label') }}</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.plan') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th class="pe-4 text-end">{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr class="transition-all">
                                            <td class="ps-4 fw-bold">
                                                <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="text-primary text-decoration-none d-inline-flex align-items-center gap-2">
                                                    <i class="feather-file-text fs-6 text-muted"></i>
                                                    <span>{{ $order->order_number }}</span>
                                                </a>
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                                        {{ strtoupper(substr($order->user->username ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <span>{{ $order->user->username ?? ('#' . $order->user_id) }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                            <td class="fw-bold text-dark">
                                                {{ $order->display_amount }}
                                                <span class="text-muted fw-normal fs-11 ms-1">{{ $order->currency_code }}</span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                @include('admin::admin.billing.partials.status_badge', ['status' => $order->status])
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
                </div>
            </div>

            {{-- Gateways & Currency Overview --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-4 h-100">
                    {{-- Gateways Snapshot --}}
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_gateways_title') }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_gateway_status') }}</h3>
                            </div>
                            <a href="{{ route('admin.billing.gateways') }}" class="btn btn-sm btn-light fw-bold text-dark shadow-sm" style="border-radius: 10px; border: 1px solid var(--admin-premium-border);">
                                <i class="feather-settings me-1"></i>
                                <span>{{ __('messages.configure') }}</span>
                            </a>
                        </div>
                        <div class="admin-panel__body p-3">
                            <div class="d-flex flex-column gap-2">
                                @foreach($gatewayDefinitions as $gateway)
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 transition-all" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white border rounded-3 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                @if($gateway['key'] === 'stripe')
                                                    <i class="fa-brands fa-stripe fs-4 text-primary"></i>
                                                @elseif($gateway['key'] === 'paypal')
                                                    <i class="fa-brands fa-paypal fs-5 text-info"></i>
                                                @elseif($gateway['key'] === 'apple_pay')
                                                    <i class="fa-brands fa-apple fs-5 text-dark"></i>
                                                @elseif($gateway['key'] === 'bank_transfer')
                                                    <i class="fa-solid fa-building-columns fs-6 text-secondary"></i>
                                                @else
                                                    <i class="fa-solid fa-money-check-dollar fs-6 text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $gateway['label'] }}</div>
                                                <div class="text-muted fs-11 mt-1">{{ implode(', ', $gateway['supported_currencies']) ?: __('messages.billing_all_active_currencies') }}</div>
                                            </div>
                                        </div>
                                        <span class="badge {{ !empty($gateway['config']['enabled']) ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-2 py-1 fw-bold fs-11">
                                            {{ !empty($gateway['config']['enabled']) ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Quick Currency Card --}}
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_currencies_tab') }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_currencies_title') }}</h3>
                            </div>
                            <a href="{{ route('admin.billing.currencies') }}" class="btn btn-sm btn-light fw-bold text-dark shadow-sm" style="border-radius: 10px; border: 1px solid var(--admin-premium-border);">
                                <i class="feather-external-link"></i>
                            </a>
                        </div>
                        <div class="admin-panel__body p-3">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($currencies as $curr)
                                    <span class="admin-chip {{ $curr->is_base ? 'border-primary' : '' }}">
                                        @if($curr->is_base)
                                            <i class="feather-star text-warning"></i>
                                        @endif
                                        <span class="fw-bold">{{ $curr->code }}</span>
                                        <span class="text-muted small">({{ $curr->symbol ?: $curr->code }})</span>
                                    </span>
                                @empty
                                    <span class="text-muted small">{{ __('messages.no_data') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
