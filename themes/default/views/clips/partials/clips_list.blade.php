@foreach($activities as $activity)
    @php
        $mediaUrl = null;
        if (isset($activity->related_content->attachments) && $activity->related_content->attachments->count() > 0) {
            $mediaUrl = asset($activity->related_content->attachments->first()->file_path);
        }
        $clipUser = $activity->user;
        $clipUserAvatar = $clipUser ? $clipUser->avatarUrl() : asset('upload/avatar.png');
        $clipUserBadgeColor = $clipUser ? $clipUser->profileBadgeColor() : '';
        $clipUserHasVerifiedBadge = $clipUser?->hasVerifiedBadge() ?? false;
        $clipCaption = $activity->related_content->txt ?? '';
        $clipCaptionFormatted = \App\Support\ContentFormatter::format($clipCaption);
        $clipCaptionPlain = strip_tags($clipCaptionFormatted);
        $isCaptionLong = mb_strlen($clipCaptionPlain) > 80;
    @endphp
    @if($mediaUrl)
        <!-- SEO Structured Data for Short Video Clip -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "VideoObject",
            "name": "{{ $clipCaptionPlain ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $clipCaptionPlain)), 100) : 'Clip by ' . ($clipUser->username ?? 'User') }}",
            "description": "{{ $clipCaptionPlain ? trim(preg_replace('/\s+/', ' ', $clipCaptionPlain)) : 'Short video clip on MYADS' }}",
            "thumbnailUrl": [
                "{{ $clipUserAvatar }}"
            ],
            "uploadDate": "{{ $activity->created_at?->toIso8601String() ?? now()->toIso8601String() }}",
            "contentUrl": "{{ $mediaUrl }}",
            "url": "{{ url('/clips#' . $activity->id) }}",
            "author": {
                "@@type": "Person",
                "name": "{{ $clipUser->username ?? 'Unknown' }}",
                "url": "{{ $clipUser ? route('profile.show', $clipUser->username) : url('/') }}"
            },
            "interactionStatistic": [
                {
                    "@@type": "InteractionCounter",
                    "interactionType": "https://schema.org/LikeAction",
                    "userInteractionCount": {{ $activity->reactions_count ?? 0 }}
                },
                {
                    "@@type": "InteractionCounter",
                    "interactionType": "https://schema.org/CommentAction",
                    "userInteractionCount": {{ $activity->comments_count ?? 0 }}
                }
            ]
        }
        </script>
        <div class="reel-item" data-id="{{ $activity->id }}" data-tp-id="{{ $activity->tp_id }}" data-s-type="{{ $activity->s_type }}" data-related-id="{{ $activity->related_content->id }}">
            <video class="reel-video" loop muted playsinline src="{{ $mediaUrl }}" preload="auto"></video>
            
            <div class="reel-overlay">
                <!-- Play/Pause Indicator -->
                <div class="reel-play-indicator">
                    <svg class="icon-play" viewBox="0 0 24 24" width="64" height="64" fill="rgba(255,255,255,0.7)"><path d="M8 5v14l11-7z"/></svg>
                </div>

                <!-- Mute Toggle -->
                <div class="reel-mute-toggle">
                    <svg class="icon-mute" viewBox="0 0 24 24" width="24" height="24" fill="#fff">
                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                    </svg>
                    <svg class="icon-unmute" viewBox="0 0 24 24" width="24" height="24" fill="#fff" style="display: none;">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                    </svg>
                </div>

                <!-- Bottom Info -->
                <div class="reel-info">
                    <a href="{{ route('profile.show', $activity->user->username) }}" class="reel-user">
                        <!-- Hexagonal Avatar -->
                        <div class="user-avatar small no-outline reel-hex-avatar {{ $clipUser?->isOnline() ? 'online' : 'offline' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $clipUserAvatar }}" style="width: 30px; height: 32px; position: relative;"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44" data-line-color="{{ $clipUserBadgeColor }}" style="width: 40px; height: 44px; position: relative;"></div>
                            </div>
                            @if($clipUserHasVerifiedBadge)
                                <div class="user-avatar-badge">
                                    <div class="user-avatar-badge-border">
                                        <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                    </div>
                                    <div class="user-avatar-badge-content">
                                        <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                    </div>
                                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                </div>
                            @endif
                        </div>
                        <span class="reel-username">
                            {{ $activity->user->username }}
                            @if($clipUserHasVerifiedBadge)
                                <svg class="verified-icon" viewBox="0 0 24 24" width="16" height="16" fill="#23d2e2" style="vertical-align: middle; margin-left: 2px;"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.4-6.4 1.5 1.5-7.9 7.9z"/></svg>
                            @endif
                        </span>
                    </a>
                    @if(!empty($clipCaption))
                        <div class="reel-caption {{ $isCaptionLong ? 'reel-caption-truncated' : '' }}" dir="auto">
                            {!! $clipCaptionFormatted !!}
                        </div>
                        @if($isCaptionLong)
                            <button class="reel-caption-more" onclick="this.previousElementSibling.classList.toggle('reel-caption-truncated'); this.textContent = this.previousElementSibling.classList.contains('reel-caption-truncated') ? '{{ __("messages.show_more") }}' : '{{ __("messages.show_less") }}';">{{ __('messages.show_more') }}</button>
                        @endif
                    @endif
                </div>

                <!-- Right Actions Sidebar -->
                <div class="reel-actions">
                    <!-- Like -->
                    @php
                        $hasLiked = false;
                        $reactionType = $activity->getReactionType();
                        if (auth()->check()) {
                            $hasLiked = \App\Models\Like::where('uid', auth()->id())->where('sid', $activity->interactionSubjectId())->where('type', $reactionType)->exists();
                        }
                    @endphp
                    <button class="reel-action-btn toggle-reaction {{ $hasLiked ? 'active' : '' }}" data-id="{{ $activity->interactionSubjectId() }}" data-type="clips" data-reaction="like">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span class="reaction-count">{{ $activity->reactions_count }}</span>
                    </button>

                    <!-- Comment -->
                    <button class="reel-action-btn open-comments" data-id="{{ $activity->id }}" data-tp-id="{{ $activity->tp_id }}">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                        <span>{{ $activity->comments_count }}</span>
                    </button>

                    <!-- Save -->
                    @php
                        $hasSaved = false;
                        $savedCount = \Illuminate\Support\Facades\DB::table('saved_statuses')->where('status_id', $activity->id)->count();
                        if (auth()->check()) {
                            $hasSaved = \Illuminate\Support\Facades\DB::table('saved_statuses')->where('user_id', auth()->id())->where('status_id', $activity->id)->exists();
                        }
                    @endphp
                    <button class="reel-action-btn toggle-save {{ $hasSaved ? 'active' : '' }}" data-id="{{ $activity->id }}">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                        <span class="save-count">{{ $savedCount }}</span>
                    </button>

                    <!-- Share -->
                    <button class="reel-action-btn share-reel" data-url="{{ url('/clips#'.$activity->id) }}">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>
                        <span>{{ __('messages.share') ?? 'Share' }}</span>
                    </button>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="reel-progress">
                <div class="reel-progress-filled"></div>
            </div>
        </div>
    @endif
@endforeach
