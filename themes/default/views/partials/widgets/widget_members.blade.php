@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $limit = (int) ($config['limit'] ?? 5);
    $orderBy = $config['order_by'] ?? 'random';
    $verifiedOnly = !empty($config['verified_only']);
    $showStats = isset($config['show_stats']) ? (bool) $config['show_stats'] : true;
    $showFollowBtn = isset($config['show_follow_btn']) ? (bool) $config['show_follow_btn'] : true;

    $userId = auth()->id() ?? 0;
    $query = \App\Models\User::query()
        ->whereNotIn('id', function($q) use ($userId) {
            $q->select('sid')->from('like')->where('uid', $userId)->where('type', 1);
        })
        ->where('id', '!=', $userId);

    if ($verifiedOnly) {
        $query->where('verify', 1);
    }

    if ($orderBy === 'popular') {
        $query->orderBy('pts', 'desc');
    } elseif ($orderBy === 'newest') {
        $query->orderBy('id', 'desc');
    } elseif ($orderBy === 'active') {
        $query->orderBy('online', 'desc');
    } else {
        $query->inRandomOrder();
    }

    $users = $query->limit($limit)->get();
@endphp

<div class="widget-box">
    <!-- WIDGET BOX TITLE -->
    <p class="widget-box-title">{{ $widget->name }}</p>
    <!-- /WIDGET BOX TITLE -->

    <!-- WIDGET BOX CONTENT -->
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($users as $user)
                @php
                     $followersCount = \Illuminate\Support\Facades\DB::table('like')->where('sid', $user->id)->where('type', 1)->count();
                     $followingCount = \Illuminate\Support\Facades\DB::table('like')->where('uid', $user->id)->where('type', 1)->count();
                     $postsCount = \App\Models\ForumTopic::where('uid', $user->id)->count();
                     $isOnline = $user->isOnline();
                @endphp
                <div class="user-status request-small">
                    <!-- USER STATUS AVATAR -->
                    <a class="user-status-avatar" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">
                        <!-- USER AVATAR -->
                        <div class="user-avatar small no-outline {{ $isOnline ? 'online' : '' }}">
                            <!-- USER AVATAR CONTENT -->
                            <div class="user-avatar-content">
                                <!-- HEXAGON -->
                                <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}"></div>
                                <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR CONTENT -->

                            <!-- USER AVATAR PROGRESS BORDER -->
                            <div class="user-avatar-progress-border">
                                <!-- HEXAGON -->
                                <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                                <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR PROGRESS BORDER -->

                            @if($user->hasVerifiedBadge())
                                <!-- USER AVATAR BADGE -->
                                <div class="user-avatar-badge">
                                    <!-- USER AVATAR BADGE BORDER -->
                                    <div class="user-avatar-badge-border">
                                        <!-- HEXAGON -->
                                        <div class="hexagon-22-24"></div>
                                        <!-- /HEXAGON -->
                                    </div>
                                    <!-- /USER AVATAR BADGE BORDER -->

                                    <!-- USER AVATAR BADGE CONTENT -->
                                    <div class="user-avatar-badge-content">
                                        <!-- HEXAGON -->
                                        <div class="hexagon-dark-16-18"></div>
                                        <!-- /HEXAGON -->
                                    </div>
                                    <!-- /USER AVATAR BADGE CONTENT -->

                                    <!-- USER AVATAR BADGE TEXT -->
                                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                    <!-- /USER AVATAR BADGE TEXT -->
                                </div>
                                <!-- /USER AVATAR BADGE -->
                            @endif
                        </div>
                        <!-- /USER AVATAR -->
                    </a>
                    <!-- /USER STATUS AVATAR -->

                    <!-- USER STATUS TITLE -->
                    <p class="user-status-title"><a class="bold" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">{{ Str::limit($user->username, 15) }}</a></p>
                    <!-- /USER STATUS TITLE -->

                    @if($showStats)
                        <!-- USER STATUS TEXT -->
                        <p class="user-status-text small">{{ __('messages.Followers') }}&nbsp;{{ $followersCount }}&nbsp;|&nbsp;{{ __('messages.following') }}&nbsp;{{ $followingCount }}&nbsp;|&nbsp;{{ __('messages.Posts') }}&nbsp;{{ $postsCount }}</p>
                        <!-- /USER STATUS TEXT -->
                    @endif

                    @if($showFollowBtn)
                        <!-- ACTION REQUEST LIST -->
                        <div class="action-request-list">
                            <!-- ACTION REQUEST -->
                            <form action="{{ route('profile.follow', $user->username) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="action-request accept" style="border:none; background:none; cursor:pointer;" title="{{ __('messages.follow') ?? 'Follow' }}">
                                    <!-- ACTION REQUEST ICON -->
                                    <svg class="action-request-icon icon-add-friend">
                                        <use xlink:href="#svg-add-friend"></use>
                                    </svg>
                                    <!-- /ACTION REQUEST ICON -->
                                </button>
                            </form>
                            <!-- /ACTION REQUEST -->
                        </div>
                        <!-- ACTION REQUEST LIST -->
                    @endif
                </div>
            @endforeach

            @if($users->isEmpty())
                <p class="text-center text-muted small my-2">{{ __('messages.no_results_found') ?? 'No suggestions right now.' }}</p>
            @endif
        </div>
    </div>
    <!-- /WIDGET BOX CONTENT -->
</div>
