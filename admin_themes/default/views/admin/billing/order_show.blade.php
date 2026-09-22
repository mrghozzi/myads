@extends('admin::layouts.admin')

@section('title', ($order?->order_number ? '#' . $order->order_number . ' - ' : '') . (__('messages.billing_order_details_title') ?? 'تفاصيل الطلب'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'لوحة التحكم' }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') ?? 'الفوترة' }}</a></li>
                <li><a href="{{ route('admin.billing.orders') }}">{{ __('messages.billing_orders_tab') ?? 'الطلبات' }}</a></li>
                <li>#{{ $order?->order_number ?? '---' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') ?? 'مساحة عمل الإيرادات' }}</div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2 flex-wrap">
                <span>{{ $order?->order_number ?? (__('messages.billing_order_details_title') ?? 'تفاصيل الطلب') }}</span>
                @if($order)
                    <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" onclick="window.copyBillingText('{{ $order->order_number }}', this);" title="{{ __('messages.copy_order_number') ?? 'نسخ رقم الطلب' }}">
                        <i class="feather-copy text-muted"></i>
                    </button>
                @endif
            </h1>
            <p class="admin-hero__copy">{{ __('messages.billing_order_details_help') ?? 'مراجعة الطلب وإثبات الدفع وسجل المعاملات.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div id="order-status-badge-container">
                    @if($order)
                        @include('admin::admin.billing.partials.status_badge', ['status' => $order->status])
                    @endif
                </div>
                <a href="{{ route('admin.billing.orders') }}" class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1.25rem; background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <i class="feather-arrow-right"></i>
                    <span>{{ __('messages.back_to_list') ?? 'العودة للطلبات' }}</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'orders'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable && $order)
        <div class="row g-4">
            {{-- Main Column (Left) --}}
            <div class="col-xl-7">
                {{-- Order Summary Panel --}}
                <div class="admin-panel mb-4">
                    <div class="admin-panel__header d-flex align-items-center justify-content-between">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_orders_tab') ?? 'الطلب' }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_order_summary_title') ?? 'ملخص الطلب' }}</h3>
                        </div>
                        <span class="text-muted fs-12">{{ optional($order->created_at)->format('Y-m-d H:i') }}</span>
                    </div>

                    <div class="admin-panel__body p-4">
                        <div class="row g-4">
                            {{-- User --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-user fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.user') ?? 'المستخدم' }}</div>
                                        <div class="fw-bold text-dark fs-14">{{ $order->user->username ?? ('#' . $order->user_id) }}</div>
                                        @if($order->user?->email)
                                            <div class="text-muted small fs-12">{{ $order->user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Plan --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-layers fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.plan') ?? 'الخطة' }}</div>
                                        <div class="fw-bold text-dark fs-14">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan') ?? 'خطة اشتراك') }}</div>
                                        <div class="text-muted small fs-12">
                                            {{ data_get($order->plan_snapshot, 'is_lifetime') ? (__('messages.billing_lifetime') ?? 'مدى الحياة') : (data_get($order->plan_snapshot, 'duration_days') . ' يوماً') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Gateway --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-credit-card fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.gateway') ?? 'بوابة الدفع' }}</div>
                                        <div class="fw-bold text-dark fs-14">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</div>
                                        <div class="text-muted small fs-12 font-monospace">{{ $order->gateway }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-dollar-sign fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.amount') ?? 'المبلغ المطلوب' }}</div>
                                        <div class="fw-bold text-dark fs-16">
                                            {{ number_format((float) $order->display_amount, 2) }}
                                            <span class="text-muted fs-12 fw-normal">{{ $order->currency_code }}</span>
                                        </div>
                                        <div class="text-muted small fs-12">
                                            {{ number_format((float) $order->base_amount, 2) }} {{ $order->base_currency_code }}
                                            ({{ __('messages.billing_exchange_rate_label') ?? 'سعر الصرف' }}: {{ number_format((float) $order->exchange_rate_snapshot, 4) }})
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-calendar fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.date') ?? 'تاريخ الإنشاء' }}</div>
                                        <div class="fw-semibold text-dark fs-13">{{ optional($order->created_at)->format('Y-m-d H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Paid At --}}
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="feather-check-circle fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_paid_at_label') ?? 'تاريخ الدفع الفعلي' }}</div>
                                        <div id="order-paid-at-text" class="fw-semibold text-dark fs-13">{{ optional($order->paid_at)->format('Y-m-d H:i:s') ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bank Receipt Preview --}}
                @php($receiptUrl = $order->receiptUrl())
                @if($receiptUrl)
                    <div class="admin-panel mb-4">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_receipt_title') ?? 'الإيصال' }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_receipt_title') ?? 'إثبات التحويل البنكي' }}</h3>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#receiptZoomModal" style="border-radius: 8px;">
                                <i class="feather-maximize-2"></i>
                                <span>{{ __('messages.zoom') ?? 'تكبير الصورة' }}</span>
                            </button>
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="text-center mb-3">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#receiptZoomModal" class="d-inline-block rounded-3 overflow-hidden border shadow-sm transition-all" style="max-width: 100%;">
                                    <img src="{{ $receiptUrl }}" alt="Receipt" class="img-fluid" style="max-height: 320px; object-fit: contain; cursor: zoom-in;">
                                </a>
                            </div>

                            @if($order->receipt_note)
                                <div class="p-3 rounded-3 mb-2" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                    <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_receipt_note_label') ?? 'ملاحظة العميل مع الإيصال' }}</div>
                                    <div class="text-dark fs-13">{{ $order->receipt_note }}</div>
                                </div>
                            @endif

                            @if($order->admin_note)
                                <div id="display-admin-note" class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25);">
                                    <div class="text-warning small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_admin_note_label') ?? 'ملاحظة الإدارة المسجلة' }}</div>
                                    <div class="text-dark fs-13">{{ $order->admin_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal for Receipt Zoom --}}
                    <div class="modal fade" id="receiptZoomModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: var(--admin-premium-surface);">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">{{ __('messages.billing_receipt_title') ?? 'إيصال التحويل' }} (#{{ $order->order_number }})</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <img src="{{ $receiptUrl }}" alt="Receipt Full" class="img-fluid rounded-3 border shadow-sm" style="max-height: 75vh; object-fit: contain;">
                                </div>
                                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                                    <a href="{{ $receiptUrl }}" target="_blank" download class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 10px; border: 1px solid var(--admin-premium-border);">
                                        <i class="feather-download"></i>
                                        <span>{{ __('messages.download') ?? 'تحميل الصورة الأصلية' }}</span>
                                    </a>
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.close') ?? 'إغلاق' }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Transactions Ledger --}}
                <div class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_transactions_title') ?? 'المعاملات' }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_transaction_log_title') ?? 'سجل المعاملات والأحداث' }}</h3>
                        </div>
                    </div>
                    <div class="admin-panel__body p-0">
                        <div class="admin-table-wrap">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">{{ __('messages.date') ?? 'التاريخ' }}</th>
                                        <th>{{ __('messages.billing_transaction_type_label') ?? 'نوع الحدث' }}</th>
                                        <th>{{ __('messages.status') ?? 'الحالة' }}</th>
                                        <th>{{ __('messages.amount') ?? 'المبلغ' }}</th>
                                        <th class="pe-4">{{ __('messages.billing_external_reference_label') ?? 'المرجع الخارجي' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->transactions as $tx)
                                        <tr>
                                            <td class="ps-4 text-muted fs-12">{{ optional($tx->processed_at)->format('Y-m-d H:i') }}</td>
                                            <td class="fw-semibold text-dark fs-13">{{ $tx->transactionTypeLabel() }}</td>
                                            <td>@include('admin::admin.billing.partials.status_badge', ['status' => $tx->status])</td>
                                            <td class="fw-bold text-dark fs-13">{{ number_format((float) $tx->amount, 2) }} {{ $tx->currency_code }}</td>
                                            <td class="pe-4 text-muted font-monospace fs-12">
                                                @if($tx->external_transaction_id)
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <span>{{ $tx->external_transaction_id }}</span>
                                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 shadow-none" onclick="window.copyBillingText('{{ $tx->external_transaction_id }}', this);">
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
                                            <td colspan="5" class="text-center text-muted py-4 fs-13">
                                                {{ __('messages.no_data') ?? 'لا توجد معاملات مسجلة بعد' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions & Sidebar Column (Right) --}}
            <div class="col-xl-5">
                {{-- Developer Simulation Box (LemonSqueezy) --}}
                @if($order->gateway === 'lemon_squeezy' && $order->status === \App\Models\BillingOrder::STATUS_PENDING_CHECKOUT)
                    <div id="developer-tools-card" class="admin-panel mb-4" style="background: rgba(97, 93, 250, 0.06); border: 1px solid var(--admin-premium-border-strong);">
                        <div class="admin-panel__header">
                            <div>
                                <div class="admin-panel__eyebrow text-primary">{{ __('messages.billing_dev_tools_eyebrow') ?? 'أدوات التطوير (محلياً)' }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_simulate_webhook_title') ?? 'محاكاة إتمام الدفع (Webhook)' }}</h3>
                            </div>
                        </div>
                        <div class="admin-panel__body p-4">
                            <p class="text-muted fs-12 mb-3">
                                {{ __('messages.billing_simulate_webhook_desc') ?? 'عند الاختبار المحلي دون توفر Webhook خارجي متصل، يمكنك الضغط هنا لمحاكاة تأكيد الدفع بنجاح وترقية حالة الطلب والاشتراك فورياً.' }}
                            </p>
                            <form id="simulate-lemon-form" action="{{ route('admin.billing.orders.simulate_lemon_squeezy', $order->id ?? 0) }}" method="POST">
                                @csrf
                                <button type="submit" id="btn-simulate-lemon" class="btn btn-primary fw-bold w-100 shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; padding: 0.75rem 1.25rem;">
                                    <i class="feather-check-circle fs-5"></i>
                                    <span>{{ __('messages.billing_simulate_webhook_btn') ?? 'تأكيد الدفع (محاكاة محلية عبر AJAX)' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Bank Transfer Review Panel --}}
                @if($order->gateway === 'bank_transfer')
                    <div class="admin-panel mb-4">
                        <div class="admin-panel__header">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_manual_review_title') ?? 'المراجعة' }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_manual_review_title') ?? 'مراجعة التحويل البنكي' }}</h3>
                            </div>
                        </div>

                        <div class="admin-panel__body p-4">
                            <p class="text-muted fs-12 mb-3">{{ __('messages.billing_manual_review_help') ?? 'قم بفحص الإيصال والتأكد من مطابقة الحساب ثم اتخاذ القرار المناسب.' }}</p>

                            @if(!empty($bankTransferConfig['instructions']))
                                <div class="p-3 rounded-3 mb-3 text-muted fs-12 lh-base" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                    <strong class="d-block text-dark mb-1">{{ __('messages.billing_approved_transfer_instructions') ?? 'تعليمات التحويل المعتمدة' }}:</strong>
                                    {!! nl2br(e((string) $bankTransferConfig['instructions'])) !!}
                                </div>
                            @endif

                            <div id="review-action-section">
                                @if($order->isAwaitingManualReview())
                                    <form id="bank-review-form" action="{{ route('admin.billing.orders.review', $order->id ?? 0) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                                {{ __('messages.billing_admin_note_label') ?? 'ملاحظة الإدارة' }}
                                            </label>
                                            <textarea id="review-admin-note-input" name="admin_note" class="form-control" rows="3" placeholder="{{ __('messages.billing_admin_note_placeholder') ?? 'أدخل أي ملاحظات ترغب في توثيقها مع هذا القرار...' }}" style="border-radius: 10px;">{{ old('admin_note', $order->admin_note) }}</textarea>
                                        </div>

                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" onclick="submitBankReview('approve')" id="btn-review-approve" class="btn btn-success fw-bold flex-grow-1 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 py-2" style="border-radius: 12px;">
                                                <i class="feather-check"></i>
                                                <span>{{ __('messages.billing_approve_payment') ?? 'موافقة وتفعيل' }}</span>
                                            </button>
                                            <button type="button" onclick="submitBankReview('reject')" id="btn-review-reject" class="btn btn-danger fw-bold flex-grow-1 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 py-2" style="border-radius: 12px;">
                                                <i class="feather-x"></i>
                                                <span>{{ __('messages.billing_reject_payment') ?? 'رفض الطلب' }}</span>
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-0 fw-semibold text-center justify-content-center py-3" style="border-radius: 12px; background: var(--admin-premium-surface-alt);">
                                        <i class="feather-info text-primary"></i>
                                        <span>{{ __('messages.billing_order_review_unavailable') ?? 'هذا الطلب غير متاح للمراجعة حالياً (تمت معالجته مسبقاً).' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Subscription Details Panel --}}
                @if($order->subscription)
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.billing_subscription_details_title') ?? 'الاشتراك' }}</div>
                                <h3 class="admin-panel__title">{{ $order->subscription->plan_name }}</h3>
                            </div>
                            @include('admin::admin.billing.partials.status_badge', ['status' => $order->subscription->status])
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_starts_at_label') ?? 'تاريخ البدء' }}</div>
                                    <div class="fw-bold text-dark fs-13">{{ optional($order->subscription->starts_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small text-uppercase tracking-wider fw-bold mb-1 fs-11">{{ __('messages.billing_ends_at_label') ?? 'تاريخ الانتهاء' }}</div>
                                    <div class="fw-bold text-dark fs-13">{{ optional($order->subscription->ends_at)->format('Y-m-d H:i') ?: (__('messages.billing_lifetime') ?? 'مدى الحياة') }}</div>
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
<script>
function submitBankReview(actionType) {
    var form = document.getElementById('bank-review-form');
    var noteInput = document.getElementById('review-admin-note-input');
    var btnApprove = document.getElementById('btn-review-approve');
    var btnReject = document.getElementById('btn-review-reject');
    if (!form) return;

    var confirmMsg = actionType === 'approve' 
        ? '{{ __("messages.confirm_approve_payment") ?? "هل أنت متأكد من الموافقة على هذا التحويل وتفعيل الاشتراك للمستخدم؟" }}'
        : '{{ __("messages.confirm_reject_payment") ?? "هل أنت متأكد من رفض هذا التحويل البنكي؟" }}';

    if (!confirm(confirmMsg)) return;

    if (btnApprove) btnApprove.disabled = true;
    if (btnReject) btnReject.disabled = true;

    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', actionType);
    if (noteInput && noteInput.value) {
        formData.append('admin_note', noteInput.value);
    }

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        return res.json().then(function(data) {
            return { status: res.status, data: data };
        });
    })
    .then(function(result) {
        if (result.status >= 200 && result.status < 300 && result.data.success) {
            window.showBillingToast(result.data.message || '{{ __("messages.billing_order_review_saved") ?? "تم حفظ المراجعة بنجاح" }}', 'success');

            // Update status badge dynamically
            var badgeContainer = document.getElementById('order-status-badge-container');
            if (badgeContainer) {
                var newStatus = actionType === 'approve' ? 'paid' : 'rejected';
                var statusLabel = actionType === 'approve' ? '{{ __("messages.billing_status_paid") ?? "مدفوع" }}' : '{{ __("messages.billing_status_rejected") ?? "مرفوض" }}';
                var badgeClass = actionType === 'approve' 
                    ? 'bg-soft-success text-success border border-success border-opacity-25' 
                    : 'bg-soft-danger text-danger border border-danger border-opacity-25';
                var dotColor = actionType === 'approve' ? '#17c666' : '#ea4d4d';

                badgeContainer.innerHTML = `
                    <span class="badge ${badgeClass} rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 fs-12 fw-semibold">
                        <span class="d-inline-block rounded-circle flex-shrink-0" style="width: 6px; height: 6px; background-color: ${dotColor};"></span>
                        <span>${statusLabel}</span>
                    </span>
                `;
            }

            // Update Review section to show processed alert
            var reviewSection = document.getElementById('review-action-section');
            if (reviewSection) {
                var msgApproved = '{{ __("messages.billing_order_processed_approved") ?? "تمت معالجة الطلب والموافقة عليه بنجاح." }}';
                var msgRejected = '{{ __("messages.billing_order_processed_rejected") ?? "تمت معالجة الطلب ورفضه بنجاح." }}';
                reviewSection.innerHTML = `
                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-0 fw-semibold text-center justify-content-center py-3" style="border-radius: 12px; background: var(--admin-premium-surface-alt);">
                        <i class="feather-check-circle text-success"></i>
                        <span>${actionType === 'approve' ? msgApproved : msgRejected}</span>
                    </div>
                `;
            }

            // Update paid_at text if approved
            if (actionType === 'approve') {
                var paidAtText = document.getElementById('order-paid-at-text');
                if (paidAtText) {
                    var now = new Date();
                    paidAtText.innerText = now.toISOString().slice(0, 19).replace('T', ' ');
                }
            }
        } else {
            if (btnApprove) btnApprove.disabled = false;
            if (btnReject) btnReject.disabled = false;
            window.showBillingToast(result.data.message || '{{ __("messages.error_occurred") ?? "حدث خطأ أثناء حفظ المراجعة" }}', 'danger');
        }
    })
    .catch(function(err) {
        if (btnApprove) btnApprove.disabled = false;
        if (btnReject) btnReject.disabled = false;
        window.showBillingToast(err.message || '{{ __("messages.error_occurred") ?? "حدث خطأ غير متوقع" }}', 'danger');
    });
}

// Developer LemonSqueezy Simulate form AJAX
document.addEventListener('DOMContentLoaded', function() {
    var simForm = document.getElementById('simulate-lemon-form');
    var simBtn = document.getElementById('btn-simulate-lemon');
    if (simForm && simBtn) {
        simForm.addEventListener('submit', function(e) {
            e.preventDefault();
            simBtn.disabled = true;
            simBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ' + '{{ __("messages.simulating") ?? "جاري المحاكاة..." }}';

            var formData = new FormData(simForm);
            fetch(simForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { status: res.status, data: data };
                });
            })
            .then(function(result) {
                if (result.status >= 200 && result.status < 300 && result.data.success) {
                    window.showBillingToast(result.data.message, 'success');
                    var devCard = document.getElementById('developer-tools-card');
                    if (devCard) devCard.remove();

                    // Update status badge
                    var badgeContainer = document.getElementById('order-status-badge-container');
                    if (badgeContainer) {
                        badgeContainer.innerHTML = `
                            <span class="badge bg-soft-success text-success border border-success border-opacity-25 rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 fs-12 fw-semibold">
                                <span class="d-inline-block rounded-circle flex-shrink-0" style="width: 6px; height: 6px; background-color: #17c666;"></span>
                                <span>{{ __("messages.billing_status_paid") ?? "مدفوع" }}</span>
                            </span>
                        `;
                    }
                } else {
                    simBtn.disabled = false;
                    simBtn.innerHTML = '<i class="feather-check-circle fs-5"></i> <span>{{ __("messages.billing_simulate_webhook_btn") ?? "تأكيد الدفع (محاكاة محلية عبر AJAX)" }}</span>';
                    window.showBillingToast(result.data.message || '{{ __("messages.simulation_failed") ?? "فشلت المحاكاة" }}', 'danger');
                }
            })
            .catch(function(err) {
                simBtn.disabled = false;
                simBtn.innerHTML = '<i class="feather-check-circle fs-5"></i> <span>{{ __("messages.billing_simulate_webhook_btn") ?? "تأكيد الدفع (محاكاة محلية عبر AJAX)" }}</span>';
                window.showBillingToast(err.message, 'danger');
            });
        });
    }
});
</script>
@endpush
