@foreach($activities as $activity)
    @php
        $mediaUrl = null;
        if (isset($activity->related_content->attachments) && $activity->related_content->attachments->count() > 0) {
            $mediaUrl = asset($activity->related_content->attachments->first()->file_path);
        }
    @endphp
    @if($mediaUrl)
        <div class="reel-item" data-id="{{ $activity->id }}">
            <video class="reel-video" loop muted playsinline src="{{ $mediaUrl }}" preload="metadata"></video>
            
            <div class="reel-overlay">
                <!-- Play/Pause Indicator -->
                <div class="reel-play-indicator">
                    <svg class="icon-play" viewBox="0 0 24 24" width="64" height="64" fill="rgba(255,255,255,0.7)"><path d="M8 5v14l11-7z"/></svg>
                </div>

                <!-- Bottom Info -->
                <div class="reel-info">
                    <a href="{{ route('profile.show', $activity->user->username) }}" class="reel-user">
                        <img src="{{ asset('upload/'.$activity->user->avatar) }}" alt="avatar" class="reel-avatar" onerror="this.src='{{ asset('upload/avatar.png') }}'">
                        <span class="reel-username">
                            {{ $activity->user->username }}
                            @if($activity->user->hasVerifiedBadge())
                                <svg class="verified-icon" viewBox="0 0 24 24" width="16" height="16" fill="#23d2e2" style="vertical-align: middle; margin-left: 2px;"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.4-6.4 1.5 1.5-7.9 7.9z"/></svg>
                            @endif
                        </span>
                    </a>
                    <div class="reel-caption" dir="auto">
                        {!! \App\Support\ContentFormatter::format($activity->txt) !!}
                    </div>
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
                    <button class="reel-action-btn toggle-reaction {{ $hasLiked ? 'active' : '' }}" data-id="{{ $activity->interactionSubjectId() }}" data-type="{{ $reactionType }}" data-reaction="like">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span class="reaction-count">{{ $activity->reactions_count }}</span>
                    </button>

                    <!-- Comment -->
                    <button class="reel-action-btn open-comments" data-id="{{ $activity->id }}">
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
                    <button class="reel-action-btn share-reel" data-url="{{ url('/status/'.$activity->id) }}">
                        <svg class="icon" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>
                        <span>{{ __('messages.share') ?? 'Share' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endforeach
