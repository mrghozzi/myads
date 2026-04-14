@php
    $status = (string) ($status ?? '');
    $badgeMap = [
        'paid' => ['class' => 'bg-success-subtle text-success', 'label' => __('messages.billing_status_paid')],
        'pending_checkout' => ['class' => 'bg-warning-subtle text-warning', 'label' => __('messages.billing_status_pending_checkout')],
        'pending_receipt' => ['class' => 'bg-warning-subtle text-warning', 'label' => __('messages.billing_status_pending_receipt')],
        'pending_review' => ['class' => 'bg-warning-subtle text-warning', 'label' => __('messages.billing_status_pending_review')],
        'rejected' => ['class' => 'bg-danger-subtle text-danger', 'label' => __('messages.billing_status_rejected')],
        'failed' => ['class' => 'bg-danger-subtle text-danger', 'label' => __('messages.billing_status_failed')],
        'cancelled' => ['class' => 'bg-secondary-subtle text-secondary', 'label' => __('messages.billing_status_cancelled')],
        'active' => ['class' => 'bg-success-subtle text-success', 'label' => __('messages.billing_subscription_status_active')],
        'queued' => ['class' => 'bg-primary-subtle text-primary', 'label' => __('messages.billing_subscription_status_queued')],
        'expired' => ['class' => 'bg-secondary-subtle text-secondary', 'label' => __('messages.billing_subscription_status_expired')],
    ];
    $badge = $badgeMap[$status] ?? ['class' => 'bg-light text-dark', 'label' => $status];
@endphp

<span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
