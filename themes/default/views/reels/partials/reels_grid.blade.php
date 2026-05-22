@foreach($activities as $activity)
    @php
        $mediaUrl = null;
        if (isset($activity->related_content->attachments) && $activity->related_content->attachments->count() > 0) {
            $mediaUrl = asset($activity->related_content->attachments->first()->file_path);
        }
    @endphp
    @if($mediaUrl)
        <div class="saved-reel-card">
            <a href="{{ route('reels.index') }}#{{ $activity->id }}" class="saved-reel-link">
                <video class="saved-reel-video" src="{{ $mediaUrl }}#t=0.1" preload="metadata"></video>
                <div class="saved-reel-overlay">
                    <svg class="icon-play" viewBox="0 0 24 24" fill="#fff" width="32" height="32"><path d="M8 5v14l11-7z"/></svg>
                    <div class="saved-reel-stats">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="#fff"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        <span>{{ $activity->reactions_count }}</span>
                    </div>
                </div>
            </a>
            <div class="saved-reel-info">
                <p class="saved-reel-caption">{{ strip_tags(\App\Support\ContentFormatter::format($activity->txt)) }}</p>
                <a href="{{ route('profile.show', $activity->user->username) }}" class="saved-reel-author">
                    <img src="{{ asset('upload/'.$activity->user->avatar) }}" onerror="this.src='{{ asset('upload/avatar.png') }}'" alt="avatar">
                    <span>{{ $activity->user->username }}</span>
                </a>
            </div>
        </div>
    @endif
@endforeach
