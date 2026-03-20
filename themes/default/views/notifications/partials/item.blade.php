@php
    $isUnread = in_array($notification->state, [0, 3], true);
@endphp

<article class="notification-card {{ $isUnread ? 'unread' : '' }}" @if($isUnread) data-notification-unread-item @endif>
    <a class="notification-card-link" href="{{ route('notifications.show', $notification->id) }}">
        <span class="notification-card-badge">
            <svg class="icon-{{ $notification->logo ?: 'notification' }}">
                <use xlink:href="#svg-{{ $notification->logo ?: 'notification' }}"></use>
            </svg>
        </span>

        <span class="notification-card-body">
            <span class="notification-card-title">{{ $notification->name }}</span>
            <span class="notification-card-time">{{ \Carbon\Carbon::createFromTimestamp($notification->time)->diffForHumans() }}</span>
        </span>

        <span class="notification-card-icon">
            <svg class="icon-{{ $notification->logo ?: 'notification' }}">
                <use xlink:href="#svg-{{ $notification->logo ?: 'notification' }}"></use>
            </svg>
        </span>
    </a>
</article>
