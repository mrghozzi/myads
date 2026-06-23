@extends('admin::layouts.admin')

@section('title', __('messages.billing_feature_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-chart-pie" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>
            
            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-white">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-12 text-uppercase tracking-wider shadow-sm">
                                {{ __('messages.billing_admin_eyebrow') }}
                            </span>
                            <span class="badge ms-2 {{ !empty($settings['enabled']) ? 'bg-success text-white' : 'bg-secondary text-white' }} rounded-pill px-3 py-1 fw-bold fs-12 text-uppercase tracking-wider shadow-sm">
                                {{ !empty($settings['enabled']) ? __('messages.billing_system_enabled') : __('messages.billing_system_disabled') }}
                            </span>
                            <span class="badge ms-2 bg-light text-dark rounded-pill px-3 py-1 fw-bold fs-12 tracking-wider shadow-sm border">
                                {{ $settings['base_currency_code'] ?? 'USD' }}
                            </span>
                        </div>
                        <h1 class="display-5 fw-black mb-3 animate__animated animate__fadeIn">
                            {{ __('messages.billing_feature_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_admin_overview_help') }}
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
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'overview'])
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
            <!-- Active Plans -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 transition-all hover-scale" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_summary_active_plans') }}</span>
                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="feather-list fs-4"></i>
                            </div>
                        </div>
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ $summary['active_plans'] }}</h2>
                    </div>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 transition-all hover-scale" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_summary_active_subscriptions') }}</span>
                            <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="feather-users fs-4"></i>
                            </div>
                        </div>
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ $summary['active_subscriptions'] }}</h2>
                    </div>
                </div>
            </div>

            <!-- Pending Transfers -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 transition-all hover-scale" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_summary_pending_transfers') }}</span>
                            <div class="bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="feather-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ $summary['pending_bank_transfers'] }}</h2>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 transition-all hover-scale" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_summary_monthly_revenue') }}</span>
                            <div class="bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="feather-dollar-sign fs-4"></i>
                            </div>
                        </div>
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ number_format((float) $summary['monthly_revenue'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <!-- Recent Activity -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_orders_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_recent_activity') }}</h4>
                        </div>
                        <a href="{{ route('admin.billing.orders') }}" class="btn btn-light fw-bold shadow-sm" style="border-radius: 10px;">{{ __('messages.view_all') }}</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                                    <tr>
                                        <th class="ps-4 py-3">{{ __('messages.billing_order_number_label') }}</th>
                                        <th class="py-3">{{ __('messages.user') }}</th>
                                        <th class="py-3">{{ __('messages.plan') }}</th>
                                        <th class="py-3">{{ __('messages.amount') }}</th>
                                        <th class="pe-4 py-3 text-end">{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-13">
                                    @forelse($recentOrders as $order)
                                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                            <td class="ps-4 fw-bold">
                                                <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="text-primary text-decoration-none hover-underline">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td class="fw-semibold text-dark">{{ $order->user->username ?? ('#' . $order->user_id) }}</td>
                                            <td class="text-muted">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                            <td class="fw-bold">{{ $order->display_amount }} <span class="text-muted fw-normal ms-1">{{ $order->currency_code }}</span></td>
                                            <td class="pe-4 text-end">@include('admin::admin.billing.partials.status_badge', ['status' => $order->status])</td>
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
                </div>
            </div>

            <!-- Gateway Status -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_gateways_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_gateway_status') }}</h4>
                        </div>
                        <a href="{{ route('admin.billing.gateways') }}" class="btn btn-light fw-bold shadow-sm" style="border-radius: 10px;">{{ __('messages.configure') }}</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3">
                            @foreach($gatewayDefinitions as $gateway)
                                <div class="border border-soft-light rounded-3 p-3 bg-light transition-all hover-shadow">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white border rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
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
                                        <span class="badge {{ !empty($gateway['config']['enabled']) ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }} rounded-pill px-3 py-1 fw-bold fs-11 border">
                                            {{ !empty($gateway['config']['enabled']) ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
    .hover-shadow:hover { box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08) !important; transform: translateY(-2px); }
    .hover-scale:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.1) !important; }
    .hover-underline:hover { text-decoration: underline !important; }
</style>
@endpush
