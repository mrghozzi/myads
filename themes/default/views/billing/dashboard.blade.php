@extends('theme::layouts.master')

@section('content')
@php
    $entitlementService = app(\App\Services\Billing\SubscriptionEntitlementService::class);
@endphp
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-wallet" style="font-size: 28px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.billing_dashboard_title') }}</p>
    <p class="section-banner-text">{{ __('messages.billing_dashboard_intro') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        @include('theme::billing.partials.alerts')

        @if($upgradeNotice)
            <div class="alert alert-warning" role="alert" style="margin-bottom: 20px;">{!! $upgradeNotice !!}</div>
        @endif

        @if(!$systemEnabled)
            <div class="alert alert-info" role="alert" style="margin-bottom: 20px;">{{ __('messages.billing_system_disabled_member_notice') }}</div>
        @endif

        <div class="widget-box" style="margin-bottom: 20px;">
            <div class="widget-box-content" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div>
                        <p class="widget-box-title" style="margin-bottom: 6px;">{{ __('messages.billing_dashboard_title') }}</p>
                        <p class="user-status-text">{{ __('messages.billing_dashboard_help') }}</p>
                    </div>
                    @if($systemEnabled)
                        <a href="{{ $plansUrl }}" class="button primary">{{ __('messages.billing_back_to_plans') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div class="widget-box">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_current_subscription_title') }}</p>
                    @if($currentSubscription)
                        @php($currentBenefits = $entitlementService->memberBenefitLines((array) ($currentSubscription->entitlements_snapshot ?? [])))
                        <div style="display: grid; gap: 10px;">
                            <div style="display: flex; justify-content: space-between; gap: 12px; align-items: center;">
                                <p class="user-status-title" style="font-size: 22px;">{{ $currentSubscription->plan_name }}</p>
                                @include('theme::billing.partials.status_badge', ['status' => $currentSubscription->status])
                            </div>
                            <p class="user-status-text">{{ __('messages.billing_starts_at_label') }}: {{ optional($currentSubscription->starts_at)->format('Y-m-d H:i') ?: '-' }}</p>
                            <p class="user-status-text">{{ __('messages.billing_ends_at_label') }}: {{ optional($currentSubscription->ends_at)->format('Y-m-d H:i') ?: __('messages.billing_lifetime') }}</p>
                            @if(!empty($currentBenefits))
                                <div style="margin-top: 8px;">
                                    <p class="widget-box-title" style="font-size: 15px; margin-bottom: 10px;">{{ __('messages.billing_plan_benefits_title') }}</p>
                                    <ul style="padding-left: 18px; margin: 0; display: grid; gap: 8px;">
                                        @foreach($currentBenefits as $benefit)
                                            <li>{{ $benefit }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="user-status-text">{{ __('messages.billing_no_active_subscription') }}</p>
                    @endif
                </div>
            </div>

            <div class="widget-box">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_queued_subscription_title') }}</p>
                    @if($queuedSubscription)
                        @php($queuedBenefits = $entitlementService->memberBenefitLines((array) ($queuedSubscription->entitlements_snapshot ?? [])))
                        <div style="display: grid; gap: 10px;">
                            <div style="display: flex; justify-content: space-between; gap: 12px; align-items: center;">
                                <p class="user-status-title" style="font-size: 22px;">{{ $queuedSubscription->plan_name }}</p>
                                @include('theme::billing.partials.status_badge', ['status' => $queuedSubscription->status])
                            </div>
                            <p class="user-status-text">{{ __('messages.billing_starts_at_label') }}: {{ optional($queuedSubscription->starts_at)->format('Y-m-d H:i') ?: '-' }}</p>
                            <p class="user-status-text">{{ __('messages.billing_ends_at_label') }}: {{ optional($queuedSubscription->ends_at)->format('Y-m-d H:i') ?: __('messages.billing_lifetime') }}</p>
                            @if(!empty($queuedBenefits))
                                <div style="margin-top: 8px;">
                                    <p class="widget-box-title" style="font-size: 15px; margin-bottom: 10px;">{{ __('messages.billing_plan_benefits_title') }}</p>
                                    <ul style="padding-left: 18px; margin: 0; display: grid; gap: 8px;">
                                        @foreach($queuedBenefits as $benefit)
                                            <li>{{ $benefit }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="user-status-text">{{ __('messages.billing_no_queued_subscription') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="widget-box" style="margin-bottom: 20px;">
            <div class="widget-box-content">
                <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_orders_title') }}</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.billing_order_number_label') }}</th>
                                <th>{{ __('messages.plan') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</td>
                                    <td>{{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}</td>
                                    <td>@include('theme::billing.partials.status_badge', ['status' => $order->status])</td>
                                    <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                    <td><a href="{{ route('billing.orders.show', $order->id) }}" class="button secondary small">{{ __('messages.view') }}</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orders->hasPages())
                    <div style="margin-top: 20px;">{{ $orders->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-box-content">
                <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_transactions_title') }}</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.billing_transaction_type_label') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.billing_external_reference_label') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->transactionTypeLabel() }}</td>
                                    <td>{{ number_format((float) $transaction->amount, 2) }} {{ $transaction->currency_code }}</td>
                                    <td>@include('theme::billing.partials.status_badge', ['status' => $transaction->status])</td>
                                    <td>{{ $transaction->external_transaction_id ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div style="margin-top: 20px;">{{ $transactions->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
