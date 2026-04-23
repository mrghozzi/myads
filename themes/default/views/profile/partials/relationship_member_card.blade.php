@php
    $profileRoute = route('profile.short', $targetUser->publicRouteIdentifier());
    $messageRoute = Auth::check() && Auth::id() !== $targetUser->id
        ? route('messages.show', \App\Models\Message::encodeConversationRouteKey(auth()->id(), $targetUser))
        : null;
@endphp

<article class="widget-box profile-relationship-card">
    <div class="profile-relationship-card__body">
        <div class="profile-relationship-card__user">
            <a class="profile-relationship-card__avatar user-avatar small {{ $targetUser->isOnline() ? 'online' : 'offline' }}" href="{{ $profileRoute }}">
                <div class="user-avatar-border">
                    <div class="hexagon-50-56" style="width: 50px; height: 56px; position: relative;"><canvas width="50" height="56"></canvas></div>
                </div>
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ $targetUser->avatarUrl() }}"><canvas width="30" height="32"></canvas></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" data-line-color="{{ $targetUser->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;"><canvas width="40" height="44" style="position: absolute; top: 0px; left: 0px;"></canvas></div>
                </div>
                @if($targetUser->hasVerifiedBadge())
                    <div class="user-avatar-badge">
                        <div class="user-avatar-badge-border">
                            <div class="hexagon-22-24"></div>
                        </div>
                        <div class="user-avatar-badge-content">
                            <div class="hexagon-dark-16-18"></div>
                        </div>
                        <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                    </div>
                @endif
            </a>

            <div class="profile-relationship-card__content">
                <div class="profile-relationship-card__title">
                    <p class="profile-relationship-card__name">
                        <a href="{{ $profileRoute }}">{{ $targetUser->username }}</a>
                    </p>
                </div>

                <p class="profile-relationship-card__meta">
                    {{ __('messages.lastcontact') }} {{ \Carbon\Carbon::createFromTimestamp($targetUser->online)->diffForHumans() }}
                </p>

                @if(!empty($actionTime))
                    <p class="profile-relationship-card__timestamp">{{ \Carbon\Carbon::createFromTimestamp($actionTime)->diffForHumans() }}</p>
                @endif
            </div>
        </div>

        @if(Auth::check() && Auth::id() !== $targetUser->id)
            <div class="profile-relationship-card__actions">
                <form class="profile-relationship-card__action-form" action="{{ route('profile.follow', $targetUser->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="profile-relationship-card__action button {{ $isViewerFollowingTarget ? 'tertiary' : 'secondary' }}" @if(!$isViewerFollowingTarget) style="color: #fff;" @endif>
                        <i class="fa {{ $isViewerFollowingTarget ? 'fa-user-times' : 'fa-user-plus' }}" aria-hidden="true"></i>
                        <span>{{ $isViewerFollowingTarget ? __('messages.unfollow') : __('messages.follow') }}</span>
                    </button>
                </form>

                @if($messageRoute)
                    <a class="profile-relationship-card__action button white" href="{{ $messageRoute }}">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <span>{{ __('messages.send_message') }}</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</article>
