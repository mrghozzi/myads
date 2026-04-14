@extends('admin::layouts.admin')

@section('title', __('messages.billing_transactions_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_transactions_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_transactions_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_transactions_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'transactions'])
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
                    <span class="admin-panel__eyebrow">{{ __('messages.billing_transactions_tab') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.billing_transaction_log_title') }}</h2>
                </div>
                <form method="GET" action="{{ route('admin.billing.transactions') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}">
                    <button type="submit" class="btn btn-light">{{ __('messages.search') }}</button>
                </form>
            </div>
            <div class="admin-panel__body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.billing_order_number_label') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.gateway') }}</th>
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
                                    <td>
                                        @if($transaction->order)
                                            <a href="{{ route('admin.billing.orders.show', $transaction->order->id) }}">{{ $transaction->order->order_number }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $transaction->user->username ?? ('#' . $transaction->user_id) }}</td>
                                    <td>{{ $transaction->gatewayLabel() }}</td>
                                    <td>{{ $transaction->transactionTypeLabel() }}</td>
                                    <td>{{ number_format((float) $transaction->amount, 2) }} {{ $transaction->currency_code }}</td>
                                    <td>@include('admin::admin.billing.partials.status_badge', ['status' => $transaction->status])</td>
                                    <td>{{ $transaction->external_transaction_id ?: '-' }}</td>
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

        @if($transactions->hasPages())
            <div class="mt-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
