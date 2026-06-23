@extends('admin::layouts.admin')

@section('title', __('messages.billing_order_details_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-receipt" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>
            
            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-white">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-12 text-uppercase tracking-wider shadow-sm">
                                {{ __('messages.billing_admin_eyebrow') }}
                            </span>
                            <span class="badge bg-white text-dark ms-2 rounded-pill px-3 py-1 fw-bold fs-12 tracking-wider shadow-sm">
                                {{ __('messages.billing_orders_title') }}
                            </span>
                        </div>
                        <h1 class="display-5 fw-black mb-3 animate__animated animate__fadeIn">
                            {{ $order?->order_number ?? __('messages.billing_order_details_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_order_details_help') }}
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

    @if($featureAvailable && $order)
        <div class="row g-4 mt-1">
            <div class="col-xl-7">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm mb-4 h-100" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_order_summary_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ $order->order_number }}</h4>
                        </div>
                        @include('admin::admin.billing.partials.status_badge', ['status' => $order->status])
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-user fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.user') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ $order->user->username ?? ('#' . $order->user_id) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-list fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.plan') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-credit-card fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.gateway') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-dollar-sign fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.amount') }}</div>
                                        <div class="fw-bold text-dark fs-15">{{ number_format((float) $order->display_amount, 2) }} <span class="text-muted ms-1 fs-12">{{ $order->currency_code }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-activity fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_base_amount_label') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ number_format((float) $order->base_amount, 2) }} <span class="text-muted ms-1 fs-12">{{ $order->base_currency_code }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-repeat fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_exchange_rate_label') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ number_format((float) $order->exchange_rate_snapshot, 6) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-calendar fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.date') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ optional($order->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="feather-check-circle fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_paid_at_label') }}</div>
                                        <div class="fw-semibold text-dark fs-14">{{ optional($order->paid_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php($receiptUrl = $order->receiptUrl())
                @if($receiptUrl)
                    <!-- Receipt Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light">
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_receipt_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_receipt_title') }}</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <a href="{{ $receiptUrl }}" target="_blank" class="d-block border border-soft-light rounded-3 overflow-hidden shadow-sm hover-shadow transition-all">
                                    <img src="{{ $receiptUrl }}" alt="{{ __('messages.billing_receipt_title') }}" class="img-fluid w-100 object-fit-cover" style="max-height: 400px;">
                                </a>
                            </div>
                            @if($order->receipt_note)
                                <div class="bg-light p-3 rounded-3 border-soft-light mb-3">
                                    <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_receipt_note_label') }}</div>
                                    <div class="fw-semibold text-dark fs-14">{{ $order->receipt_note }}</div>
                                </div>
                            @endif
                            @if($order->admin_note)
                                <div class="bg-soft-warning p-3 rounded-3 border-warning border opacity-100">
                                    <div class="text-warning small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_admin_note_label') }}</div>
                                    <div class="fw-semibold text-dark fs-14">{{ $order->admin_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Transaction Log -->
                <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light">
                        <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_transactions_title') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_transaction_log_title') }}</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                                    <tr>
                                        <th class="ps-4 py-3">{{ __('messages.date') }}</th>
                                        <th class="py-3">{{ __('messages.billing_transaction_type_label') }}</th>
                                        <th class="py-3">{{ __('messages.status') }}</th>
                                        <th class="py-3">{{ __('messages.amount') }}</th>
                                        <th class="pe-4 py-3">{{ __('messages.billing_external_reference_label') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-13">
                                    @forelse($order->transactions as $transaction)
                                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                            <td class="ps-4 text-muted fw-semibold">{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                            <td class="fw-semibold text-dark">{{ $transaction->transactionTypeLabel() }}</td>
                                            <td>@include('admin::admin.billing.partials.status_badge', ['status' => $transaction->status])</td>
                                            <td class="fw-bold">{{ number_format((float) $transaction->amount, 2) }} <span class="text-muted fw-normal ms-1">{{ $transaction->currency_code }}</span></td>
                                            <td class="pe-4 text-muted font-monospace fs-12">{{ $transaction->external_transaction_id ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
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
                </div>
            </div>

            <div class="col-xl-5">
                <!-- Developer Tools -->
                @if($order->gateway === 'lemon_squeezy' && $order->status === \App\Models\BillingOrder::STATUS_PENDING_CHECKOUT)
                    <div class="card border-primary shadow-sm mb-4 bg-soft-primary" style="border-radius: 20px;">
                        <div class="card-header bg-transparent border-0 p-4 pb-2">
                            <div class="text-uppercase tracking-wider fw-bold text-primary mb-1 fs-11">أدوات التطوير</div>
                            <h4 class="fw-bold mb-0 text-dark">محاكاة إتمام الدفع</h4>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <p class="text-muted fs-13 mb-4">بما أنك تقوم بتجربة النظام محلياً، فلن تصل إشعارات الـ Webhook التلقائية. استخدم هذا الزر لمحاكاة استلام الدفع وتحويل حالة الطلب إلى "مدفوع".</p>
                            <form action="{{ route('admin.billing.orders.simulate_lemon_squeezy', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm hover-scale" style="border-radius: 12px; padding-top: 12px; padding-bottom: 12px;">
                                    <i class="feather-check-circle me-2"></i> تأكيد الدفع (محاكاة محلية)
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Manual Review -->
                @if($order->gateway === 'bank_transfer')
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light">
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_manual_review_title') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_manual_review_title') }}</h4>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted fs-14 mb-4">{{ __('messages.billing_manual_review_help') }}</p>

                            @if(!empty($bankTransferConfig['instructions']))
                                <div class="bg-light p-3 rounded-3 border border-soft-light mb-4 fs-13 lh-base">
                                    {!! nl2br(e((string) $bankTransferConfig['instructions'])) !!}
                                </div>
                            @endif

                            @if($order->isAwaitingManualReview())
                                <form action="{{ route('admin.billing.orders.review', $order->id) }}" method="POST" class="d-grid gap-4">
                                    @csrf
                                    <div>
                                        <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_admin_note_label') }}</label>
                                        <textarea name="admin_note" class="form-control border-soft-light bg-light" rows="4" style="border-radius: 12px;">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    </div>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <button type="submit" name="action" value="approve" class="btn btn-success fw-bold shadow-sm flex-grow-1 hover-scale" style="border-radius: 12px; padding-top: 12px; padding-bottom: 12px;">
                                            <i class="feather-check me-2"></i> {{ __('messages.billing_approve_payment') }}
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger fw-bold shadow-sm flex-grow-1 hover-scale" style="border-radius: 12px; padding-top: 12px; padding-bottom: 12px;">
                                            <i class="feather-x me-2"></i> {{ __('messages.billing_reject_payment') }}
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border border-soft-light mb-0 fw-semibold text-center py-3" style="border-radius: 12px;">
                                    {{ __('messages.billing_order_review_unavailable') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Subscription Details -->
                @if($order->subscription)
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_subscription_details_title') }}</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $order->subscription->plan_name }}</h4>
                            </div>
                            @include('admin::admin.billing.partials.status_badge', ['status' => $order->subscription->status])
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                            <i class="feather-play-circle fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_starts_at_label') }}</div>
                                            <div class="fw-semibold text-dark fs-14">{{ optional($order->subscription->starts_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                            <i class="feather-stop-circle fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_ends_at_label') }}</div>
                                            <div class="fw-semibold text-dark fs-14">{{ optional($order->subscription->ends_at)->format('Y-m-d H:i') ?: __('messages.billing_lifetime') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
    .fs-15 { font-size: 15px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08) !important; transform: translateY(-2px); }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
</style>
@endpush
