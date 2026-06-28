<div class="widget-box">
    <!-- WIDGET BOX TITLE -->
    <p class="widget-box-title">{{ $widget->name }}</p>
    <!-- /WIDGET BOX TITLE -->

    <!-- WIDGET BOX CONTENT -->
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                // Fetch recent comments joined with user and topic, ordered by id DESC
                $recentComments = \App\Models\ForumComment::with(['user', 'topic'])
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($recentComments->isEmpty())
                <p class="text-center text-muted mb-0"><small>{{ __('messages.no_comments_found') ?? 'No comments found.' }}</small></p>
            @else
                @foreach($recentComments as $comment)
                    @php
                        $user = $comment->user;
                        if (!$user) continue;
                        $topic = $comment->topic;
                        $topicUrl = $topic ? route('forum.topic', $topic->id) . '#comment-' . $comment->id : '#';
                        $snippet = strip_tags(preg_replace('/\[.*?\]/', '', $comment->txt));
                    @endphp
                    <div class="user-status request-small">
                        <!-- USER STATUS AVATAR -->
                        <a class="user-status-avatar" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">
                            <!-- USER AVATAR -->
                            <div class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : '' }}">
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
                            </div>
                            <!-- /USER AVATAR -->
                        </a>
                        <!-- /USER STATUS AVATAR -->

                        <!-- USER STATUS TITLE -->
                        <p class="user-status-title"><a class="bold" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">{{ Str::limit($user->username, 15) }}</a></p>
                        <!-- /USER STATUS TITLE -->

                        <!-- USER STATUS TEXT -->
                        <p class="user-status-text small">
                            <a href="{{ $topicUrl }}" class="text-muted text-decoration-none">
                                &quot;{{ Str::limit($snippet, 35) }}&quot;
                            </a>
                        </p>
                        <!-- /USER STATUS TEXT -->
                        
                        <!-- ACTION REQUEST LIST -->
                        <div class="action-request-list">
                            <p class="user-status-text small timestamp" style="margin-top: 4px;">{{ \Carbon\Carbon::createFromTimestamp($comment->date)->diffForHumans() }}</p>
                        </div>
                        <!-- /ACTION REQUEST LIST -->
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <!-- /WIDGET BOX CONTENT -->
</div>
