@extends('admin::layouts.admin')

@section('title', __('messages.billing_order_details_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li><a href="{{ route('admin.billing.orders') }}">{{ __('messages.billing_orders_title') }}</a></li>
                <li>{{ __('messages.billing_order_details_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ $order?->order_number ?? __('messages.billing_order_details_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_order_details_help') }}</p>
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

    @if($featureAvailable && $order)
        <div class="row g-3 mt-1">
            <div class="col-xl-7">
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_order_summary_title') }}</span>
                            <h2 class="admin-panel__title">{{ $order->order_number }}</h2>
                        </div>
                        @include('admin::admin.billing.partials.status_badge', ['status' => $order->status])
                    </div>
                    <div class="admin-panel__body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.user') }}</div>
                                <div class="fw-semibold">{{ $order->user->username ?? ('#' . $order->user_id) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.plan') }}</div>
                                <div class="fw-semibold">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.gateway') }}</div>
                                <div class="fw-semibold">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.amount') }}</div>
                                <div class="fw-semibold">{{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.billing_base_amount_label') }}</div>
                                <div class="fw-semibold">{{ number_format((float) $order->base_amount, 2) }} {{ $order->base_currency_code }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.billing_exchange_rate_label') }}</div>
                                <div class="fw-semibold">{{ number_format((float) $order->exchange_rate_snapshot, 6) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.date') }}</div>
                                <div class="fw-semibold">{{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">{{ __('messages.billing_paid_at_label') }}</div>
                                <div class="fw-semibold">{{ optional($order->paid_at)->format('Y-m-d H:i') ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($order->receipt_path)
                    <section class="admin-panel mt-3">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.billing_receipt_title') }}</span>
                                <h2 class="admin-panel__title">{{ __('messages.billing_receipt_title') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="mb-3">
                                <img src="{{ asset($order->receipt_path) }}" alt="{{ __('messages.billing_receipt_title') }}" class="img-fluid rounded border">
                            </div>
                            @if($order->receipt_note)
                                <div class="text-muted small">{{ __('messages.billing_receipt_note_label') }}</div>
                                <div class="fw-semibold">{{ $order->receipt_note }}</div>
                            @endif
                            @if($order->admin_note)
                                <hr>
                                <div class="text-muted small">{{ __('messages.billing_admin_note_label') }}</div>
                                <div class="fw-semibold">{{ $order->admin_note }}</div>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="admin-panel mt-3">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_transactions_title') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.billing_transaction_log_title') }}</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.billing_transaction_type_label') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.billing_external_reference_label') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->transactions as $transaction)
                                        <tr>
                                            <td>{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                            <td>{{ $transaction->transactionTypeLabel() }}</td>
                                            <td>@include('admin::admin.billing.partials.status_badge', ['status' => $transaction->status])</td>
                                            <td>{{ number_format((float) $transaction->amount, 2) }} {{ $transaction->currency_code }}</td>
                                            <td>{{ $transaction->external_transaction_id ?: '-' }}</td>
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

            <div class="col-xl-5">
                @if($order->gateway === 'bank_transfer')
                    <section class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.billing_manual_review_title') }}</span>
                                <h2 class="admin-panel__title">{{ __('messages.billing_manual_review_title') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <p class="text-muted">{{ __('messages.billing_manual_review_help') }}</p>

                            @if(!empty($bankTransferConfig['instructions']))
                                <div class="border rounded-3 p-3 bg-light-subtle mb-3">{!! nl2br(e((string) $bankTransferConfig['instructions'])) !!}</div>
                            @endif

                            @if($order->isAwaitingManualReview())
                                <form action="{{ route('admin.billing.orders.review', $order->id) }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">{{ __('messages.billing_admin_note_label') }}</label>
                                        <textarea name="admin_note" class="form-control" rows="4">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="submit" name="action" value="approve" class="btn btn-success">{{ __('messages.billing_approve_payment') }}</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-outline-danger">{{ __('messages.billing_reject_payment') }}</button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border mb-0">{{ __('messages.billing_order_review_unavailable') }}</div>
                            @endif
                        </div>
                    </section>
                @endif

                @if($order->subscription)
                    <section class="admin-panel mt-3">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.billing_subscription_details_title') }}</span>
                                <h2 class="admin-panel__title">{{ $order->subscription->plan_name }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">{{ __('messages.status') }}</span>
                                @include('admin::admin.billing.partials.status_badge', ['status' => $order->subscription->status])
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('messages.billing_starts_at_label') }}</div>
                                    <div class="fw-semibold">{{ optional($order->subscription->starts_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('messages.billing_ends_at_label') }}</div>
                                    <div class="fw-semibold">{{ optional($order->subscription->ends_at)->format('Y-m-d H:i') ?: __('messages.billing_lifetime') }}</div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
