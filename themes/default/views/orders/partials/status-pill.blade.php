@php
    $status = $status ?? 'open';
@endphp
<span class="orders-status-pill status-{{ $status }}">
    {{ __('messages.order_status_' . $status) }}
</span>
