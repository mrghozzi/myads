@extends('admin::layouts.admin')

@section('title', __('messages.billing_feature_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.billing_feature_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_feature_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_admin_overview_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <div class="d-flex flex-column gap-2">
                    <span class="badge {{ !empty($settings['enabled']) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                        {{ !empty($settings['enabled']) ? __('messages.billing_system_enabled') : __('messages.billing_system_disabled') }}
                    </span>
                    <span class="text-muted small">{{ __('messages.billing_base_currency_label') }}: {{ $settings['base_currency_code'] ?? 'USD' }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'overview'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-3 mt-1">
            <div class="col-md-6 col-xl-3">
                <div class="admin-panel">
                    <div class="admin-panel__body">
                        <span class="admin-panel__eyebrow">{{ __('messages.billing_summary_active_plans') }}</span>
                        <h2 class="admin-panel__title">{{ $summary['active_plans'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="admin-panel">
                    <div class="admin-panel__body">
                        <span class="admin-panel__eyebrow">{{ __('messages.billing_summary_active_subscriptions') }}</span>
                        <h2 class="admin-panel__title">{{ $summary['active_subscriptions'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="admin-panel">
                    <div class="admin-panel__body">
                        <span class="admin-panel__eyebrow">{{ __('messages.billing_summary_pending_transfers') }}</span>
                        <h2 class="admin-panel__title">{{ $summary['pending_bank_transfers'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="admin-panel">
                    <div class="admin-panel__body">
                        <span class="admin-panel__eyebrow">{{ __('messages.billing_summary_monthly_revenue') }}</span>
                        <h2 class="admin-panel__title">{{ number_format((float) $summary['monthly_revenue'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-xl-8">
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_orders_title') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.billing_recent_activity') }}</h2>
                        </div>
                        <a href="{{ route('admin.billing.orders') }}" class="btn btn-light">{{ __('messages.view_all') }}</a>
                    </div>
                    <div class="admin-panel__body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.billing_order_number_label') }}</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.plan') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td><a href="{{ route('admin.billing.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                                            <td>{{ $order->user->username ?? ('#' . $order->user_id) }}</td>
                                            <td>{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                            <td>{{ $order->display_amount }} {{ $order->currency_code }}</td>
                                            <td>@include('admin::admin.billing.partials.status_badge', ['status' => $order->status])</td>
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
            </div>

            <div class="col-xl-4">
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_gateways_title') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.billing_gateway_status') }}</h2>
                        </div>
                        <a href="{{ route('admin.billing.gateways') }}" class="btn btn-light">{{ __('messages.configure') }}</a>
                    </div>
                    <div class="admin-panel__body">
                        <div class="d-grid gap-3">
                            @foreach($gatewayDefinitions as $gateway)
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div>
                                            <div class="fw-semibold">{{ $gateway['label'] }}</div>
                                            <div class="text-muted small">{{ implode(', ', $gateway['supported_currencies']) ?: __('messages.billing_all_active_currencies') }}</div>
                                        </div>
                                        <span class="badge {{ !empty($gateway['config']['enabled']) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ !empty($gateway['config']['enabled']) ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endif
</div>
@endsection
