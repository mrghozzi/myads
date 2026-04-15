<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $topics = \App\Models\ForumTopic::with(['user', 'category'])->latest('id')->limit(5)->get();
            @endphp

            @foreach($topics as $topic)
                <div class="user-status request-small">
                <a class="user-status-avatar" href="{{ $topic->user ? route('profile.short', $topic->user->publicRouteIdentifier()) : '#' }}">
                        <div class="user-avatar small no-outline {{ ($topic->user?->isOnline()) ? 'online' : '' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44" data-line-color="{{ $topic->user ? $topic->user->profileBadgeColor() : '' }}"></div>
                            </div>
                        </div>
                    </a>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('forum.topic', $topic->id) }}">{{ Str::limit($topic->name, 25) }}</a>
                    </p>
                    <p class="user-status-text small">
                        {{ $topic->category?->name ?? __('messages.forum') }} | {{ \Carbon\Carbon::createFromTimestamp($topic->date)->diffForHumans() }}
                    </p>
                </div>
            @endforeach

            @if($topics->isEmpty())
                <p class="text-center small">{{ __('messages.no_topics_found') }}</p>
            @endif
        </div>
        <a href="{{ route('forum.index') }}" class="button secondary full" style="margin-top: 20px;">{{ __('messages.see_all') }}</a>
    </div>
</div>
