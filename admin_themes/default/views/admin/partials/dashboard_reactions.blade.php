<!-- ═══════════════════ REACTION COUNTERS STRIP ═══════════════════ -->
<div class="sd-reactions-strip mb-4">
    <div class="d-flex align-items-center justify-content-between px-2 mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="feather-thumbs-up me-2 text-danger"></i> {{ __('messages.live_member_reactions') ?? 'Live Member Reactions' }}</h6>
        <span class="badge bg-soft-primary text-primary fw-bold">{{ number_format($totalReactions ?? 0) }} {{ __('messages.allreactions') ?? 'Reactions' }}</span>
    </div>
    <div class="d-flex align-items-center justify-content-around flex-wrap gap-3">
        @php
            $reactionIcons = [
                'like' => 'like.png',
                'love' => 'love.png',
                'dislike' => 'dislike.png',
                'funny' => 'funny.png',
                'wow' => 'wow.png',
                'sad' => 'sad.png',
                'angry' => 'angry.png',
                'happy' => 'happy.png'
            ];
            $orderedReactions = [];
            foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'sad', 'angry'] as $key) {
                if(isset($reactionsSummary[$key])) $orderedReactions[$key] = $reactionsSummary[$key];
            }
        @endphp
        @foreach($orderedReactions as $type => $count)
            <div class="text-center px-3 py-2 sd-reaction-pill">
                <div class="mb-2">
                    <img src="{{ theme_asset('img/reaction/' . $reactionIcons[$type]) }}" alt="{{ $type }}" style="width: 44px; height: 44px; object-fit: contain;">
                </div>
                <h5 class="fw-bold mb-0 text-dark">{{ number_format($count) }}</h5>
                <small class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $type }}</small>
            </div>
        @endforeach
    </div>
</div>
