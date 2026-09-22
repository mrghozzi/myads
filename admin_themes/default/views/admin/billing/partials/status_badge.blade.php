@php
    $status = (string) ($status ?? '');
    $badgeMap = [
        'paid' => [
            'class' => 'bg-soft-success text-success border border-success border-opacity-25',
            'dot' => '#17c666',
            'label' => __('messages.billing_status_paid'),
        ],
        'pending_checkout' => [
            'class' => 'bg-soft-warning text-warning border border-warning border-opacity-25',
            'dot' => '#ffa21d',
            'label' => __('messages.billing_status_pending_checkout'),
        ],
        'pending_receipt' => [
            'class' => 'bg-soft-warning text-warning border border-warning border-opacity-25',
            'dot' => '#ffa21d',
            'label' => __('messages.billing_status_pending_receipt'),
        ],
        'pending_review' => [
            'class' => 'bg-soft-warning text-warning border border-warning border-opacity-25',
            'dot' => '#ffa21d',
            'label' => __('messages.billing_status_pending_review'),
        ],
        'rejected' => [
            'class' => 'bg-soft-danger text-danger border border-danger border-opacity-25',
            'dot' => '#ea4d4d',
            'label' => __('messages.billing_status_rejected'),
        ],
        'failed' => [
            'class' => 'bg-soft-danger text-danger border border-danger border-opacity-25',
            'dot' => '#ea4d4d',
            'label' => __('messages.billing_status_failed'),
        ],
        'cancelled' => [
            'class' => 'bg-soft-secondary text-secondary border border-secondary border-opacity-25',
            'dot' => '#94a3b8',
            'label' => __('messages.billing_status_cancelled'),
        ],
        'active' => [
            'class' => 'bg-soft-success text-success border border-success border-opacity-25',
            'dot' => '#17c666',
            'label' => __('messages.billing_subscription_status_active'),
        ],
        'queued' => [
            'class' => 'bg-soft-primary text-primary border border-primary border-opacity-25',
            'dot' => '#3454d1',
            'label' => __('messages.billing_subscription_status_queued'),
        ],
        'expired' => [
            'class' => 'bg-soft-secondary text-secondary border border-secondary border-opacity-25',
            'dot' => '#94a3b8',
            'label' => __('messages.billing_subscription_status_expired'),
        ],
    ];
    $badge = $badgeMap[$status] ?? [
        'class' => 'bg-soft-secondary text-secondary border border-secondary border-opacity-25',
        'dot' => '#94a3b8',
        'label' => $status,
    ];
@endphp

<span class="badge {{ $badge['class'] }} rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1 fs-12 fw-semibold" style="letter-spacing: 0.02em;">
    <span class="d-inline-block rounded-circle flex-shrink-0" style="width: 6px; height: 6px; background-color: {{ $badge['dot'] }};"></span>
    <span>{{ $badge['label'] }}</span>
</span>
