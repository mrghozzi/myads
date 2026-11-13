@extends('theme::layouts.master')

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printable-invoice-card, #printable-invoice-card * {
        visibility: visible !important;
    }
    #printable-invoice-card {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 30px !important;
        background: #fff !important;
        color: #000 !important;
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
    .no-print, header, footer, .section-banner, .grid-column:first-child {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
@php
    $canUploadReceipt = $order->gateway === 'bank_transfer' && in_array($order->status, ['pending_receipt', 'rejected'], true);
    $systemEnabled = \App\Support\SubscriptionSettings::isEnabled();
    $user = auth()->user();
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-file-invoice-dollar" style="font-size: 28px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.billing_order_details_member_title') }}</p>
    <p class="section-banner-text">{{ $order->order_number }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        @include('theme::billing.partials.alerts')

        <div class="widget-box" style="margin-bottom: 20px;">
            <div class="widget-box-content" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div>
                        <p class="widget-box-title" style="margin-bottom: 6px;">{{ __('messages.billing_order_summary_title') }}</p>
                        <p class="user-status-title" style="font-size: 24px;">{{ $order->order_number }}</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        @include('theme::billing.partials.status_badge', ['status' => $order->status])
                        <button type="button" onclick="window.print();" class="button secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-print"></i> {{ __('messages.billing_print_invoice') }}
                        </button>
                        @if($systemEnabled)
                            <a href="{{ route('billing.plans') }}" class="button primary">{{ __('messages.billing_back_to_plans') }}</a>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 24px;">
                    <div>
                        <p class="user-status-text">{{ __('messages.plan') }}</p>
                        <p class="user-status-title">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</p>
                    </div>
                    <div>
                        <p class="user-status-text">{{ __('messages.gateway') }}</p>
                        <p class="user-status-title">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</p>
                    </div>
                    <div>
                        <p class="user-status-text">{{ __('messages.amount') }}</p>
                        <p class="user-status-title">{{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}</p>
                    </div>
                    <div>
                        <p class="user-status-text">{{ __('messages.date') }}</p>
                        <p class="user-status-title">{{ optional($order->created_at)->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Printable Official Invoice Card --}}
        <div id="printable-invoice-card" class="widget-box" style="margin-bottom: 20px; border-top: 4px solid #615dfa;">
            <div class="widget-box-content" style="padding: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 24px; margin-bottom: 24px; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h2 style="font-size: 26px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">{{ config('app.name', 'MYADS') }}</h2>
                        <p style="color: #64748b; font-size: 13px; margin: 0;">{{ __('messages.billing_invoice_title') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: inline-block; padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 13px; {{ $order->status === 'paid' ? 'background: #dcfce7; color: #15803d;' : 'background: #fef3c7; color: #b45309;' }}">
                            {{ $order->status === 'paid' ? __('messages.billing_invoice_status_paid') : __('messages.billing_invoice_status_pending') }}
                        </span>
                        <div style="font-size: 13px; color: #64748b; margin-top: 6px;">{{ __('messages.billing_invoice_number') }}: <strong>#INV-{{ $order->order_number }}</strong></div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">{{ __('messages.billing_invoice_date') }}: {{ optional($order->created_at)->format('Y-m-d') }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px;">
                    <div>
                        <p style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: #94a3b8; margin-bottom: 4px;">{{ __('messages.billing_invoice_bill_to') }}</p>
                        <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">{{ $order->user->username ?? ('#' . $order->user_id) }}</p>
                        @if($order->user?->email)
                            <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">{{ $order->user->email }}</p>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: #94a3b8; margin-bottom: 4px;">{{ __('messages.billing_invoice_payment_method') }}</p>
                        <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</p>
                        <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">{{ $order->currency_code }} (Rate: {{ $order->exchange_rate_snapshot }})</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; color: #475569;">{{ __('messages.description') }}</th>
                                <th style="padding: 12px 16px; text-align: center; font-size: 13px; color: #475569;">{{ __('messages.billing_duration_label') }}</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 13px; color: #475569;">{{ __('messages.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; font-weight: 600; color: #1e293b;">
                                    {{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}
                                </td>
                                <td style="padding: 14px 16px; text-align: center; color: #64748b;">
                                    {{ data_get($order->plan_snapshot, 'is_lifetime') ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => data_get($order->plan_snapshot, 'duration_days')]) }}
                                </td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #1e293b;">
                                    {{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="padding: 16px; text-align: right; font-weight: 700; font-size: 15px; color: #1e293b;">
                                    {{ __('messages.total') }}:
                                </td>
                                <td style="padding: 16px; text-align: right; font-weight: 800; font-size: 18px; color: #615dfa;">
                                    {{ number_format((float) $order->display_amount, 2) }} {{ $order->currency_code }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="border-top: 1px dashed #cbd5e1; padding-top: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <p style="font-size: 12px; color: #94a3b8; margin: 0;">{{ __('messages.billing_plan_form_note') }}</p>
                    <button type="button" onclick="window.print();" class="button secondary no-print" style="padding: 8px 18px; font-size: 13px;">
                        <i class="fa fa-print" style="margin-right: 6px;"></i> {{ __('messages.billing_print_invoice') }}
                    </button>
                </div>
            </div>
        </div>

        @if($order->gateway === 'bank_transfer')
            <div class="widget-box" style="margin-bottom: 20px;">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_bank_transfer_instructions_title') }}</p>
                    @if(!empty($bankTransferConfig['instructions']))
                        <div class="alert alert-info" role="alert">{!! nl2br(e((string) $bankTransferConfig['instructions'])) !!}</div>
                    @endif
                    @if(!empty($bankTransferConfig['note']))
                        <p class="user-status-text" style="margin-top: 12px;">{!! nl2br(e((string) $bankTransferConfig['note'])) !!}</p>
                    @endif
                </div>
            </div>
        @endif

        @if($order->admin_note)
            <div class="widget-box" style="margin-bottom: 20px;">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 8px;">{{ __('messages.billing_admin_note_label') }}</p>
                    <p class="user-status-text">{{ $order->admin_note }}</p>
                </div>
            </div>
        @endif

        @php($receiptUrl = $order->receiptUrl())

        @if($receiptUrl)
            <div class="widget-box" style="margin-bottom: 20px;">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_receipt_current_title') }}</p>
                    <img src="{{ $receiptUrl }}" alt="{{ __('messages.billing_receipt_title') }}" class="img-fluid rounded" style="margin-bottom: 16px;">
                    @if($order->receipt_note)
                        <p class="user-status-text">{{ __('messages.billing_receipt_note_label') }}: {{ $order->receipt_note }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if($canUploadReceipt)
            <div class="widget-box" style="margin-bottom: 20px;">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_upload_receipt_title') }}</p>
                    <form action="{{ route('billing.orders.receipt.update', $order->id) }}" method="POST" enctype="multipart/form-data" style="display: grid; gap: 16px;">
                        @csrf
                        <div>
                            <label class="form-label">{{ __('messages.billing_receipt_title') }}</label>
                            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>
                        <div>
                            <label class="form-label">{{ __('messages.billing_receipt_note_label') }}</label>
                            <textarea name="receipt_note" rows="4" placeholder="{{ __('messages.billing_receipt_note_placeholder') }}">{{ old('receipt_note', $order->receipt_note) }}</textarea>
                        </div>
                        <button type="submit" class="button primary">{{ __('messages.billing_upload_receipt_cta') }}</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="widget-box">
            <div class="widget-box-content" style="padding: 28px;">
                <p class="widget-box-title" style="margin-bottom: 16px;">{{ __('messages.billing_transaction_log_title') }}</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.billing_transaction_type_label') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->transactions as $transaction)
                                <tr>
                                    <td>{{ optional($transaction->processed_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->transactionTypeLabel() }}</td>
                                    <td>@include('theme::billing.partials.status_badge', ['status' => $transaction->status])</td>
                                    <td>{{ number_format((float) $transaction->amount, 2) }} {{ $transaction->currency_code }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
