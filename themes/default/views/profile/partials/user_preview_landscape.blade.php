@php
    // Fetch Cover Photo from Options for this specific user
    $coverOption = \App\Models\Option::where('o_type', 'user')->where('o_order', $targetUser->id)->first();
    $targetCover = $coverOption && $coverOption->o_mode != '0' ? $coverOption->o_mode : 'upload/cover.jpg';
    
    // Stats for this specific user
    $targetPostsCount = \App\Models\ForumTopic::where('uid', $targetUser->id)->count(); // Matching current profile posts count logic
    $targetFollowersCount = \App\Models\Like::where('sid', $targetUser->id)->where('type', 1)->count();
    $targetFollowingCount = \App\Models\Like::where('uid', $targetUser->id)->where('type', 1)->count();

    // Check if the current logged in user follows this specific user
    $isLoggedInUserFollowing = false;
    if (Auth::check()) {
        $isLoggedInUserFollowing = \App\Models\Like::where('uid', Auth::id())
            ->where('sid', $targetUser->id)
            ->where('type', 1)
            ->exists();
    }
@endphp

<div class="user-preview landscape">
    <!-- USER PREVIEW COVER -->
    <figure class="user-preview-cover liquid" style="background: url({{ asset($targetCover) }}) center center / cover no-repeat;">
        <img src="{{ asset($targetCover) }}" alt="cover-{{ $targetUser->username }}" style="display: none;">
    </figure>
    <!-- /USER PREVIEW COVER -->
    
    <!-- USER PREVIEW INFO -->
    <div class="user-preview-info">
        <!-- USER SHORT DESCRIPTION -->
        <div class="user-short-description landscape tiny">
            <!-- USER SHORT DESCRIPTION AVATAR -->
            <a class="user-short-description-avatar user-avatar small {{ $targetUser->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', $targetUser->publicRouteIdentifier()) }}">
                <div class="user-avatar-border">
                    <div class="hexagon-50-56" style="width: 50px; height: 56px; position: relative;"><canvas width="50" height="56"></canvas></div>
                </div>
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ $targetUser->avatarUrl() }}"><canvas width="30" height="32"></canvas></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"><canvas width="40" height="44" style="position: absolute; top: 0px; left: 0px;"></canvas></div>
                </div>
                @if($targetUser->ucheck == 1)
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
            <!-- /USER SHORT DESCRIPTION AVATAR -->
            
            <!-- USER SHORT DESCRIPTION TITLE -->
            <p class="user-short-description-title"><a href="{{ route('profile.short', $targetUser->publicRouteIdentifier()) }}">{{ $targetUser->username }}</a></p>
            <!-- /USER SHORT DESCRIPTION TITLE -->
            
            <!-- USER SHORT DESCRIPTION TEXT -->
            <p class="user-short-description-text">{{ __('messages.lastcontact') }}&nbsp;{{ \Carbon\Carbon::createFromTimestamp($targetUser->online)->diffForHumans() }}</p>
            <!-- /USER SHORT DESCRIPTION TEXT -->
        </div>
        <!-- /USER SHORT DESCRIPTION -->

        <!-- BADGE LIST -->
        <div class="badge-list small">
            @if(isset($actionTime))
                {{ __('messages.ago') }}&nbsp;{{ \Carbon\Carbon::createFromTimestamp($actionTime)->diffForHumans() }}
            @endif
        </div>
        <!-- /BADGE LIST -->

        <!-- USER STATS -->
        <div class="user-stats">
            <!-- USER STAT -->
            <div class="user-stat">
                <p class="user-stat-title">{{ $targetPostsCount }}</p>
                <p class="user-stat-text">{{ __('messages.Posts') }}</p>
            </div>
            <!-- /USER STAT -->

            <!-- USER STAT -->
            <div class="user-stat">
                <p class="user-stat-title">{{ $targetFollowersCount }}</p>
                <p class="user-stat-text">{{ __('messages.Followers') }}</p>
            </div>
            <!-- /USER STAT -->

            <!-- USER STAT -->
            <div class="user-stat">
                <p class="user-stat-title">{{ $targetFollowingCount }}</p>
                <p class="user-stat-text">{{ __('messages.following') }}</p>
            </div>
            <!-- /USER STAT -->
        </div>
        <!-- /USER STATS -->

        <!-- USER PREVIEW ACTIONS -->
        @if(!Auth::check() || Auth::id() != $targetUser->id)
        <div class="user-preview-actions">
            <!-- FOLLOW/UNFOLLOW BUTTON -->
            @if(Auth::check())
                <form action="{{ route('profile.follow', $targetUser->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="profile-header-info-action button {{ $isLoggedInUserFollowing ? 'tertiary' : 'secondary' }}" style="{{ !$isLoggedInUserFollowing ? 'color: #fff;' : '' }}">
                        <svg class="button-icon icon-add-friend"><use xlink:href="#svg-{{ $isLoggedInUserFollowing ? 'remove' : 'add' }}-friend"></use></svg>
                    </button>
                </form>
            @endif
            <!-- MESSAGE BUTTON -->
            @if(Auth::check())
            <a class="profile-header-info-action button primary" href="{{ route('messages.show', \App\Models\Message::encodeConversationRouteKey(auth()->id(), $targetUser)) }}">
                <svg class="button-icon icon-comment"><use xlink:href="#svg-comment"></use></svg>
            </a>
            @endif
        </div>
        @endif
        <!-- /USER PREVIEW ACTIONS -->
    </div>
    <!-- /USER PREVIEW INFO -->
</div>
