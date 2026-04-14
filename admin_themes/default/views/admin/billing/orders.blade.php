@extends('admin::layouts.admin')

@section('title', __('messages.billing_orders_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_orders_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_orders_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_orders_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'orders'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <section class="admin-panel mt-4">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.billing_orders_tab') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.billing_orders_title') }}</h2>
                </div>
                <form method="GET" action="{{ route('admin.billing.orders') }}" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}">
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.billing_all_statuses') }}</option>
                            @foreach(['paid', 'pending_checkout', 'pending_receipt', 'pending_review', 'rejected', 'failed', 'cancelled'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                    {{ __('messages.billing_status_' . $statusOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="gateway" class="form-select">
                            <option value="">{{ __('messages.billing_all_gateways') }}</option>
                            @foreach($gateways as $gatewayDefinition)
                                <option value="{{ $gatewayDefinition['key'] }}" @selected($gateway === $gatewayDefinition['key'])>
                                    {{ $gatewayDefinition['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-light">{{ __('messages.search') }}</button>
                    </div>
                </form>
            </div>
            <div class="admin-panel__body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.billing_order_number_label') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.plan') }}</th>
                                <th>{{ __('messages.gateway') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->order_number }}</td>
                                    <td>{{ $order->user->username ?? ('#' . $order->user_id) }}</td>
                                    <td>{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                    <td>{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</td>
                                    <td>{{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}</td>
                                    <td>@include('admin::admin.billing.partials.status_badge', ['status' => $order->status])</td>
                                    <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                    <td><a href="{{ route('admin.billing.orders.show', $order->id) }}" class="btn btn-sm btn-light">{{ __('messages.view') }}</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        @if($orders->hasPages())
            <div class="mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
