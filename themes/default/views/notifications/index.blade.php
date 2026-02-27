@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/profile.png') }}) no-repeat 50%;">
    <p class="section-banner-title">{{ __('messages.notifications') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-settings">
                <div class="post-peek-header">
                    <p class="widget-box-title">{{ __('messages.info') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                @if($notifications->count() > 0)
                    <div class="notification-box-list">
                        @foreach($notifications as $notif)
                            @php
                                $ntfread = $notif->state == 1 ? 'read' : ($notif->state == 3 || $notif->state == 0 ? 'unread' : '');
                            @endphp
                            <div class="notification-box">
                                <div class="user-status notification">
                                    <p class="user-status-title">
                                        <a href="{{ route('notifications.show', $notif->id) }}">{{ $notif->name }}</a>
                                    </p>
                                    <p class="user-status-timestamp small-space">{{ \Carbon\Carbon::createFromTimestamp($notif->time)->diffForHumans() }}</p>
                                    <div class="user-status-icon">
                                        <svg class="icon-{{ $notif->logo ?: 'notification' }}">
                                            <use xlink:href="#svg-{{ $notif->logo ?: 'notification' }}"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mark-{{ $ntfread }}-button"></div>
                            </div>
                        @endforeach
                    </div>
                    {{ $notifications->links() }}
                @else
                    <p class="text-center">{{ __('messages.no_notifications') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
