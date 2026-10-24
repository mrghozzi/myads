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
    $categoryId = (int) ($config['category_id'] ?? 0);
    $showAuthor = isset($config['show_author']) ? (bool) $config['show_author'] : true;
    $showCategory = isset($config['show_category']) ? (bool) $config['show_category'] : true;
    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : __('messages.see_all');

    $query = \App\Models\ForumTopic::with(['user', 'category'])->latest('id');
    if ($categoryId > 0) {
        $query->where('cat', $categoryId);
    }
    $topics = $query->limit($limit)->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($topics as $topic)
                <div class="user-status request-small">
                    @if($showAuthor)
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
                    @endif
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('forum.topic', $topic->id) }}">{{ Str::limit($topic->name, 25) }}</a>
                    </p>
                    <p class="user-status-text small">
                        @if($showCategory)
                            {{ $topic->category?->name ?? __('messages.forum') }}
                        @endif
                        @if($showCategory && $showDate)
                            |
                        @endif
                        @if($showDate)
                            {{ \Carbon\Carbon::createFromTimestamp($topic->date)->diffForHumans() }}
                        @endif
                    </p>
                </div>
            @endforeach

            @if($topics->isEmpty())
                <p class="text-center small text-muted my-2">{{ __('messages.no_topics_found') }}</p>
            @endif
        </div>
        @if($showButton)
            <a href="{{ route('forum.index') }}" class="button secondary full" style="margin-top: 20px;">{{ $buttonText }}</a>
        @endif
    </div>
</div>
