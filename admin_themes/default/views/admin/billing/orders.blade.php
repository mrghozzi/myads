@extends('admin::layouts.admin')

@section('title', __('messages.billing_orders_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 160px; transform: rotate(-15deg);"></i>
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
                            {{ __('messages.billing_orders_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_orders_help') }}
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
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'orders'])
        </div>
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
            <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_orders_tab') }}</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_orders_title') }}</h4>
                </div>
                <form method="GET" action="{{ route('admin.billing.orders') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group" style="width: auto;">
                        <input type="text" name="search" class="form-control border-soft-light bg-light" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="border-radius: 10px 0 0 10px;">
                    </div>
                    <select name="status" class="form-select border-soft-light bg-light" style="width: auto; border-radius: 10px;">
                        <option value="">{{ __('messages.billing_all_statuses') }}</option>
                        @foreach(['paid', 'pending_checkout', 'pending_receipt', 'pending_review', 'rejected', 'failed', 'cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                {{ __('messages.billing_status_' . $statusOption) }}
                            </option>
                        @endforeach
                    </select>
                    <select name="gateway" class="form-select border-soft-light bg-light" style="width: auto; border-radius: 10px;">
                        <option value="">{{ __('messages.billing_all_gateways') }}</option>
                        @foreach($gateways as $gatewayDefinition)
                            <option value="{{ $gatewayDefinition['key'] }}" @selected($gateway === $gatewayDefinition['key'])>
                                {{ $gatewayDefinition['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-3" style="border-radius: 10px;">
                        <i class="feather-search"></i>
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                            <tr>
                                <th class="ps-4 py-3">{{ __('messages.billing_order_number_label') }}</th>
                                <th class="py-3">{{ __('messages.user') }}</th>
                                <th class="py-3">{{ __('messages.plan') }}</th>
                                <th class="py-3">{{ __('messages.gateway') }}</th>
                                <th class="py-3">{{ __('messages.amount') }}</th>
                                <th class="py-3">{{ __('messages.status') }}</th>
                                <th class="py-3">{{ __('messages.date') }}</th>
                                <th class="pe-4 py-3 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($orders as $order)
                                <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                    <td class="ps-4 fw-bold">
                                        <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="text-primary text-decoration-none hover-underline">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $order->user->username ?? ('#' . $order->user_id) }}</td>
                                    <td class="text-muted">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                    <td class="text-muted">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</td>
                                    <td class="fw-bold">{{ number_format((float) $order->display_amount, 2) }} <span class="text-muted fw-normal ms-1">{{ $order->currency_code }}</span></td>
                                    <td>@include('admin::admin.billing.partials.status_badge', ['status' => $order->status])</td>
                                    <td class="text-muted">{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="btn btn-sm btn-light fw-bold shadow-sm" style="border-radius: 8px;">
                                            <i class="feather-eye me-1"></i> {{ __('messages.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
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
            @if($orders->hasPages())
                <div class="card-footer bg-transparent border-top border-soft-light p-4">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
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
    .fs-13 { font-size: 13px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-underline:hover { text-decoration: underline !important; }
</style>
@endpush
