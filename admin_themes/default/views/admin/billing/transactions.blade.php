@extends('admin::layouts.admin')

@section('title', __('messages.billing_transactions_title'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_transactions_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_transactions_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_transactions_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12">
                <i class="feather-file-text me-1"></i>
                {{ $transactions->total() }} {{ __('messages.billing_transactions_tab') }}
            </span>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'transactions'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        {{-- Filter Toolbar --}}
        <div class="admin-toolbar-card mb-4">
            <form id="transactions-filter-form" method="GET" action="{{ route('admin.billing.transactions') }}" class="d-flex flex-wrap align-items-center gap-2 w-100">
                <div class="input-group" style="min-width: 250px; flex: 1 1 300px;">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                        <i class="feather-search"></i>
                    </span>
                    <input type="text" name="search" id="transactions-search-input" class="form-control border-start-0" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="border-radius: 0 12px 12px 0;">
                </div>

                <button type="submit" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-filter"></i>
                    <span>{{ __('messages.search') }}</span>
                </button>

                @if($search !== '')
                    <a href="{{ route('admin.billing.transactions') }}" class="btn btn-light fw-bold text-muted d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 12px; border: 1px solid var(--admin-premium-border);">
                        <i class="feather-x"></i>
                        <span>{{ __('messages.reset') }}</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- Transactions Table Container --}}
        <div id="transactions-table-wrapper" class="admin-panel transition-all">
            <div class="admin-panel__body p-0">
                <div class="admin-table-wrap">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('messages.date') }}</th>
                                <th>{{ __('messages.billing_order_number_label') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.gateway') }}</th>
                                <th>{{ __('messages.billing_transaction_type_label') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="pe-4">{{ __('messages.billing_external_reference_label') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr class="transition-all">
                                    <td class="ps-4 text-muted fs-12">{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                    <td class="fw-bold">
                                        @if($transaction->order)
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('admin.billing.orders.show', $transaction->order->id) }}" class="text-primary text-decoration-none">
                                                    {{ $transaction->order->order_number }}
                                                </a>
                                                <button type="button" class="btn btn-sm btn-link text-muted p-0 shadow-none" onclick="window.copyBillingText('{{ $transaction->order->order_number }}', this);" title="{{ __('messages.copy') }}">
                                                    <i class="feather-copy" style="font-size: 11px;"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                                {{ strtoupper(substr($transaction->user->username ?? 'U', 0, 1)) }}
                                            </div>
                                            <span>{{ $transaction->user->username ?? ('#' . $transaction->user_id) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted fs-13">{{ $transaction->gatewayLabel() }}</td>
                                    <td class="fw-semibold text-dark fs-13">{{ $transaction->transactionTypeLabel() }}</td>
                                    <td class="fw-bold text-dark fs-14">
                                        {{ number_format((float) $transaction->amount, 2) }}
                                        <span class="text-muted fw-normal fs-11 ms-1">{{ $transaction->currency_code }}</span>
                                    </td>
                                    <td>
                                        @include('admin::admin.billing.partials.status_badge', ['status' => $transaction->status])
                                    </td>
                                    <td class="pe-4 text-muted font-monospace fs-12">
                                        @if($transaction->external_transaction_id)
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <span>{{ $transaction->external_transaction_id }}</span>
                                                <button type="button" class="btn btn-sm btn-link text-muted p-0 shadow-none" onclick="window.copyBillingText('{{ $transaction->external_transaction_id }}', this);" title="{{ __('messages.copy_reference') }}">
                                                    <i class="feather-copy" style="font-size: 11px;"></i>
                                                </button>
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
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

            @if($transactions->hasPages())
                <div class="admin-panel__footer d-flex justify-content-center p-3">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('transactions-filter-form');
    var wrapper = document.getElementById('transactions-table-wrapper');
    if (!form || !wrapper) return;

    function fetchTransactions(url) {
        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newWrapper = doc.getElementById('transactions-table-wrapper');
            if (newWrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';

            bindPaginationLinks();
            window.history.pushState(null, '', url);
        })
        .catch(function(err) {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
            window.showBillingToast('{{ __("messages.error_occurred") }}', 'danger');
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var params = new URLSearchParams(new FormData(form)).toString();
        var url = form.action + (params ? '?' + params : '');
        fetchTransactions(url);
    });

    function bindPaginationLinks() {
        var links = wrapper.querySelectorAll('.pagination a');
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var href = this.getAttribute('href');
                if (href) fetchTransactions(href);
            });
        });
    }

    bindPaginationLinks();

    window.addEventListener('popstate', function() {
        fetchTransactions(window.location.href);
    });
});
</script>
@endpush
