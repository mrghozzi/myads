<div class="widget-box profile-relationships-summary">
    <div class="profile-relationships-summary__top">
        <a class="profile-relationships-summary__avatar user-avatar small {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
            <div class="user-avatar-border">
                <div class="hexagon-50-56" style="width: 50px; height: 56px; position: relative;"><canvas width="50" height="56"></canvas></div>
            </div>
            <div class="user-avatar-content">
                <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}"><canvas width="30" height="32"></canvas></div>
            </div>
            <div class="user-avatar-progress-border">
                <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;"><canvas width="40" height="44" style="position: absolute; top: 0px; left: 0px;"></canvas></div>
            </div>
            @if($user->hasVerifiedBadge())
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

        <div class="profile-relationships-summary__identity">
            <p class="widget-box-title">
                <a class="profile-relationships-summary__user-link" href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a>
            </p>
            <p class="profile-relationships-summary__meta">{{ __('messages.lastcontact') }} {{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}</p>
        </div>
    </div>

    <div class="profile-relationships-summary__highlight">
        <p class="profile-relationships-summary__highlight-value">{{ number_format($relationshipTotal) }}</p>
        <p class="profile-relationships-summary__highlight-label">{{ $relationshipTitle }}</p>
    </div>

    <div class="profile-relationships-summary__stats">
        <a class="profile-relationships-summary__stat {{ $selectedTab === 'followers' ? 'is-active' : '' }}" href="{{ route('profile.followers', $user->username) }}">
            <span class="profile-relationships-summary__stat-value">{{ number_format($followersCount ?? 0) }}</span>
            <span class="profile-relationships-summary__stat-label">{{ __('messages.Followers') }}</span>
        </a>

        <a class="profile-relationships-summary__stat {{ $selectedTab === 'following' ? 'is-active' : '' }}" href="{{ route('profile.following', $user->username) }}">
            <span class="profile-relationships-summary__stat-value">{{ number_format($followingCount ?? 0) }}</span>
            <span class="profile-relationships-summary__stat-label">{{ __('messages.following') }}</span>
        </a>

        <div class="profile-relationships-summary__stat">
            <span class="profile-relationships-summary__stat-value">{{ number_format($postsCount ?? 0) }}</span>
            <span class="profile-relationships-summary__stat-label">{{ __('messages.Posts') }}</span>
        </div>
    </div>

    <div class="profile-relationships-summary__footer">
        <a class="button white full" href="{{ route('profile.show', $user->username) }}">{{ __('messages.Timeline') }}</a>
    </div>
</div>
