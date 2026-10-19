@foreach($activities as $activity)
    @php
        $mediaUrl = null;
        if (isset($activity->related_content->attachments) && $activity->related_content->attachments->count() > 0) {
            $mediaUrl = asset($activity->related_content->attachments->first()->file_path);
        }
        $clipTopic = $activity->forumTopic;
        $clipViews = $clipTopic ? (int) ($clipTopic->vu ?? 0) : (int) ($activity->views_count ?? 0);
        $caption = $activity->related_content->txt ?? $activity->txt ?? '';
    @endphp
    @if($mediaUrl)
        <div class="saved-reel-card">
            <a href="{{ route('clips.show', $activity->id) }}" class="saved-reel-link">
                <video class="saved-reel-video" src="{{ $mediaUrl }}#t=0.1" preload="metadata"></video>
                <div class="saved-reel-overlay">
                    <svg class="icon-play" viewBox="0 0 24 24" fill="#fff" width="32" height="32"><path d="M8 5v14l11-7z"/></svg>
                    <div class="saved-reel-stats">
                        <span class="d-inline-flex align-items-center gap-1">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#ff3366"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            {{ $activity->reactions_count }}
                        </span>
                        <span class="d-inline-flex align-items-center gap-1 ms-2">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#fff"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            {{ number_format($clipViews) }}
                        </span>
                    </div>
                </div>
            </a>
            <div class="saved-reel-info">
                <p class="saved-reel-caption">{{ \Illuminate\Support\Str::limit(strip_tags(\App\Support\ContentFormatter::format($caption)), 70) }}</p>
                <a href="{{ route('profile.show', $activity->user->username) }}" class="saved-reel-author">
                    <img src="{{ $activity->user ? $activity->user->avatarUrl() : asset('upload/avatar.png') }}" onerror="this.src='{{ asset('upload/avatar.png') }}'" alt="avatar">
                    <span>{{ $activity->user->username ?? 'User' }}</span>
                </a>
            </div>
        </div>
    @endif
@endforeach
