@php
    $status = $activity;
    $statusUser = $status->user;
    $statusUserProfileUrl = $statusUser ? route('profile.show', $statusUser->username) : '#';
    $statusUserName = $statusUser?->username ?? __('messages.unknown_user');
    $statusUserAvatar = $statusUser?->img ? asset($statusUser->img) : theme_asset('img/avatar/default.png');
    $statusUserPresence = $statusUser?->isOnline() ? 'online' : 'offline';
    $site = $activity->related_content;
@endphp
<div class="widget-box no-padding post{{ $status->id }}">
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $statusUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $statusUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $statusUserAvatar }}"></div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44"></div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $statusUserProfileUrl }}">{{ $statusUserName }}</a>
                    &nbsp;{{ __('messages.added_new_site') }}
                </p>
                <p class="user-status-timestamp small-space">{{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}</p>
            </div>
            
            <div class="widget-box-status-text">
                <p class="widget-box-status-text-title">{{ $site->name }}</p>
                <p class="widget-box-status-text">{{ $site->txt ?? '' }}</p>
                <a href="{{ $site->url }}" target="_blank" class="button primary small">{{ __('messages.visit_site') }}</a>
            </div>
        </div>
    </div>
</div>
