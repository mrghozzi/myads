<div class="user-popover-content">
    <div class="user-popover-header">
        <a class="user-status-avatar" href="{{ route('profile.show', $user->username) }}" style="position: absolute; top: -30px; {{ is_locale_rtl() ? 'right: 24px;' : 'left: 24px;' }}">
            <div class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : 'offline' }}">
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}"></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
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
            </div>
        </a>
        <div class="user-popover-actions">
            @auth
                @if(auth()->id() !== $user->id)
                    @php
                        $isFollowing = \App\Models\Like::where('uid', auth()->id())->where('sid', $user->id)->where('type', 1)->exists();
                    @endphp
                    <a href="#" class="button {{ $isFollowing ? 'secondary' : 'primary' }} small" style="padding: 0 16px; height: 32px; line-height: 32px;" onclick="toggleFollow({{ $user->id }}, this); return false;">
                        {{ $isFollowing ? __('messages.unfollow') : __('messages.Follow') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="user-popover-body">
        <a href="{{ route('profile.show', $user->username) }}" class="user-popover-title">{{ $user->username }}</a>
        <p class="user-popover-role">{{ $user->forumRoleLabel() }}</p>

        @if($user->o_descr)
        <p class="user-popover-bio">{{ \Illuminate\Support\Str::limit(strip_tags($user->o_descr), 80) }}</p>
        @endif

        <div class="user-popover-stats">
            <div class="user-popover-stat">
                <span class="stat-value">{{ $postsCount }}</span>
                <span class="stat-label">{{ __('messages.Posts') }}</span>
            </div>
            <div class="user-popover-stat">
                <span class="stat-value">{{ $followersCount }}</span>
                <span class="stat-label">{{ __('messages.Followers') }}</span>
            </div>
            <div class="user-popover-stat">
                <span class="stat-value">{{ $followingCount }}</span>
                <span class="stat-label">{{ __('messages.following') }}</span>
            </div>
        </div>

        @if($showOnlineStatus)
        <div class="user-popover-footer">
            <p class="user-popover-last-seen">
                @if($user->isOnline())
                    <span style="color: var(--color-success);"><i class="fas fa-circle" style="font-size: 8px; vertical-align: middle; margin-right: 4px;"></i>{{ __('messages.online') }}</span>
                @else
                    {{ __('messages.last_seen') }}: {{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
