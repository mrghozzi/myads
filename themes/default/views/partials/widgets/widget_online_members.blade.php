<div class="widget-box">
    <!-- WIDGET BOX TITLE -->
    <p class="widget-box-title">{{ $widget->name }}</p>
    <!-- /WIDGET BOX TITLE -->

    <!-- WIDGET BOX CONTENT -->
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $userId = auth()->id() ?? 0;
                $activeThreshold = time() - 900; // 15 minutes
                
                $onlineUsers = \App\Models\User::where('online', '>', $activeThreshold)
                    ->where('id', '!=', $userId)
                    ->orderBy('online', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($onlineUsers->isEmpty())
                <p class="text-center text-muted mb-0"><small>{{ __('messages.no_users_online_right_now') ?? 'No users online right now.' }}</small></p>
            @else
                @foreach($onlineUsers as $user)
                    @php
                        $followersCount = \Illuminate\Support\Facades\DB::table('like')->where('sid', $user->id)->where('type', 1)->count();
                        $postsCount = \App\Models\ForumTopic::where('uid', $user->id)->count();
                    @endphp
                    <div class="user-status request-small">
                        <!-- USER STATUS AVATAR -->
                        <a class="user-status-avatar" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">
                            <!-- USER AVATAR -->
                            <div class="user-avatar small no-outline online">
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

                        <!-- USER STATUS TEXT -->
                        <p class="user-status-text small">{{ __('messages.Followers') }}&nbsp;{{ $followersCount }}&nbsp;|&nbsp;{{ __('messages.Posts') }}&nbsp;{{ $postsCount }}</p>
                        <!-- /USER STATUS TEXT -->

                        <!-- ACTION REQUEST LIST -->
                        <div class="action-request-list">
                            <!-- ACTION REQUEST -->
                            <a href="{{ route('messages.show', $user->username) }}" class="action-request accept" style="border:none; cursor:pointer;" title="{{ __('messages.Message') }}">
                                <i class="fa-regular fa-envelope"></i>
                            </a>
                            <!-- /ACTION REQUEST -->
                        </div>
                        <!-- ACTION REQUEST LIST -->
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <!-- /WIDGET BOX CONTENT -->
</div>
