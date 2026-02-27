@php
    $status = $activity;
    $site = $activity->related_content;
@endphp
<div class="widget-box no-padding post{{ $status->id }}">
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ route('profile.show', $status->user->username) }}">
                    <div class="user-avatar small no-outline {{ $status->user->isOnline() ? 'online' : 'offline' }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $status->user->img ? asset($status->user->img) : theme_asset('img/avatar/default.png') }}"></div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44"></div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ route('profile.show', $status->user->username) }}">{{ $status->user->username }}</a>
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
