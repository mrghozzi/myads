@extends('theme::layouts.master')

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
                        @if($systemEnabled)
                            <a href="{{ route('billing.plans') }}" class="button secondary">{{ __('messages.billing_back_to_plans') }}</a>
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
