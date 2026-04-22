@php
    $status = (string) ($status ?? 'draft');
    $statusIcons = [
        'active' => 'fa-circle-check',
        'draft' => 'fa-pen-to-square',
        'pending_review' => 'fa-clock',
        'rejected' => 'fa-circle-xmark',
        'suspended' => 'fa-ban',
    ];
@endphp

<span class="dev-status-pill is-{{ $status }}">
    <i class="fa {{ $statusIcons[$status] ?? 'fa-circle-info' }}"></i>
    <span>{{ __('messages.app_status_' . $status) }}</span>
</span>
