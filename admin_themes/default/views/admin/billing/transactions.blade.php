@extends('admin::layouts.admin')

@section('title', __('messages.billing_transactions_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-money-bill-transfer" style="font-size: 160px; transform: rotate(-15deg);"></i>
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
                            {{ __('messages.billing_transactions_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_transactions_help') }}
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
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'transactions'])
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
                    <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_transactions_tab') }}</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_transaction_log_title') }}</h4>
                </div>
                <form method="GET" action="{{ route('admin.billing.transactions') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group" style="width: auto;">
                        <input type="text" name="search" class="form-control border-soft-light bg-light" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="border-radius: 10px 0 0 10px;">
                    </div>
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
                                <th class="ps-4 py-3">{{ __('messages.date') }}</th>
                                <th class="py-3">{{ __('messages.billing_order_number_label') }}</th>
                                <th class="py-3">{{ __('messages.user') }}</th>
                                <th class="py-3">{{ __('messages.gateway') }}</th>
                                <th class="py-3">{{ __('messages.billing_transaction_type_label') }}</th>
                                <th class="py-3">{{ __('messages.amount') }}</th>
                                <th class="py-3">{{ __('messages.status') }}</th>
                                <th class="pe-4 py-3">{{ __('messages.billing_external_reference_label') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($transactions as $transaction)
                                <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                    <td class="ps-4 text-muted fw-semibold">{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                    <td class="fw-bold">
                                        @if($transaction->order)
                                            <a href="{{ route('admin.billing.orders.show', $transaction->order->id) }}" class="text-primary text-decoration-none hover-underline">
                                                {{ $transaction->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $transaction->user->username ?? ('#' . $transaction->user_id) }}</td>
                                    <td class="text-muted">{{ $transaction->gatewayLabel() }}</td>
                                    <td class="fw-semibold">{{ $transaction->transactionTypeLabel() }}</td>
                                    <td class="fw-bold">{{ number_format((float) $transaction->amount, 2) }} <span class="text-muted fw-normal ms-1">{{ $transaction->currency_code }}</span></td>
                                    <td>@include('admin::admin.billing.partials.status_badge', ['status' => $transaction->status])</td>
                                    <td class="pe-4 text-muted font-monospace fs-12">{{ $transaction->external_transaction_id ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-soft-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                                <i class="feather-activity fs-3 text-secondary"></i>
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
                <div class="card-footer bg-transparent border-top border-soft-light p-4">
                    {{ $transactions->links('pagination::bootstrap-5') }}
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
    .fs-12 { font-size: 12px; }
    .fs-13 { font-size: 13px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-underline:hover { text-decoration: underline !important; }
</style>
@endpush
