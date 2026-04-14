@php
    $badgeMap = [
        'paid' => ['background' => '#eafaf1', 'color' => '#1d9c5b', 'label' => __('messages.billing_status_paid')],
        'pending_checkout' => ['background' => '#fff6dd', 'color' => '#b7791f', 'label' => __('messages.billing_status_pending_checkout')],
        'pending_receipt' => ['background' => '#fff6dd', 'color' => '#b7791f', 'label' => __('messages.billing_status_pending_receipt')],
        'pending_review' => ['background' => '#fff6dd', 'color' => '#b7791f', 'label' => __('messages.billing_status_pending_review')],
        'rejected' => ['background' => '#fef1f2', 'color' => '#d6455d', 'label' => __('messages.billing_status_rejected')],
        'failed' => ['background' => '#fef1f2', 'color' => '#d6455d', 'label' => __('messages.billing_status_failed')],
        'cancelled' => ['background' => '#f1f5f9', 'color' => '#64748b', 'label' => __('messages.billing_status_cancelled')],
        'active' => ['background' => '#eafaf1', 'color' => '#1d9c5b', 'label' => __('messages.billing_subscription_status_active')],
        'queued' => ['background' => '#eef6ff', 'color' => '#2563eb', 'label' => __('messages.billing_subscription_status_queued')],
        'expired' => ['background' => '#f1f5f9', 'color' => '#64748b', 'label' => __('messages.billing_subscription_status_expired')],
    ];
    $badge = $badgeMap[$status ?? ''] ?? ['background' => '#f1f5f9', 'color' => '#475569', 'label' => ucfirst(str_replace('_', ' ', (string) ($status ?? 'unknown')))];
@endphp

<span style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 999px; background: {{ $badge['background'] }}; color: {{ $badge['color'] }}; font-size: 12px; font-weight: 700;">
    {{ $badge['label'] }}
</span>
