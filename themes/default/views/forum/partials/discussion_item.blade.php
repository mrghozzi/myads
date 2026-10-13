@php
    $topicExcerpt = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $topic->txt))), 160);
    $categoryColor = $topic->category ? ($topic->category->color ?? '#615dfa') : '#615dfa';
    $commentsCount = (int) ($topic->comments_count ?? $topic->comments_count_calc ?? 0);
    $lastPoster = $topic->lastPoster ?? $topic->user;
    $lastActivityTime = $topic->last_posted_at 
        ? \Carbon\Carbon::createFromTimestamp((int) $topic->last_posted_at)->diffForHumans() 
        : ($topic->date ? \Carbon\Carbon::createFromTimestamp((int) $topic->date)->diffForHumans() : '');
@endphp

<article class="forum-modern-discussion-card forum-rdx-discussion-row {{ $topic->is_pinned ? 'is-pinned' : '' }}" data-topic-id="{{ $topic->id }}">
    <!-- Author Avatar (Vikinger Hexagon) -->
    <div class="forum-modern-avatar-wrap">
        @if($topic->user)
            <a class="user-avatar small no-outline {{ $topic->user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}" title="{{ $topic->user->username }}">
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ $topic->user->avatarUrl() }}" style="background-image: url('{{ $topic->user->avatarUrl() }}'); width: 30px; height: 32px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                    </div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" data-line-color="{{ $topic->user->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                    </div>
                </div>
                @if($topic->user->hasVerifiedBadge())
                    <div class="user-avatar-badge">
                        <div class="user-avatar-badge-border">
                            <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-badge-content">
                            <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas>
                            </div>
                        </div>
                        <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                    </div>
                @endif
            </a>
        @else
            <div class="user-avatar small no-outline offline">
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ asset('upload/_avatar.png') }}" style="background-image: url('{{ asset('upload/_avatar.png') }}'); width: 30px; height: 32px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                    </div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="forum-modern-card-content">
        <div class="forum-modern-card-title-row">
            @if($topic->is_pinned)
                <span class="forum-modern-badge-pinned"><i class="fa fa-thumb-tack me-1"></i>{{ __('messages.pinned') }}</span>
            @endif
            @if($topic->is_locked)
                <span class="forum-modern-badge-locked"><i class="fa fa-lock me-1"></i>{{ __('messages.locked') }}</span>
            @endif

            <a href="{{ route('forum.topic', $topic->id) }}" class="forum-modern-card-title">
                {{ $topic->name }}
            </a>

            @if($topic->category)
                <a href="{{ route('forum.category', $topic->category->id) }}" class="forum-modern-card-tag" style="background-color: {{ $categoryColor }};">
                    <i class="fa {{ $topic->category->icons ?: 'fa-folder' }}"></i>
                    <span>{{ $topic->category->name }}</span>
                </a>
            @endif
        </div>

        @if($topicExcerpt !== '')
            <p class="forum-modern-card-snippet">{{ $topicExcerpt }}</p>
        @endif

        <div class="forum-modern-card-meta">
            @if($topic->user)
                <span>{{ __('messages.started_by') ?? 'Started by' }} <a href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}" class="forum-modern-meta-user">{{ $topic->user->username }}</a></span>
            @endif

            @if($commentsCount > 0 && $lastPoster)
                <span>&bull;</span>
                <span>
                    {{ __('messages.last_reply_by') ?? 'Last reply by' }} 
                    <a href="{{ route('profile.short', $lastPoster->publicRouteIdentifier()) }}" class="forum-modern-meta-user">{{ $lastPoster->username }}</a>
                    <time class="ms-1">{{ $lastActivityTime }}</time>
                </span>
            @else
                <span>&bull;</span>
                <time>{{ $lastActivityTime }}</time>
            @endif
        </div>
    </div>

    <!-- Stats Pill -->
    <div class="forum-modern-card-stats">
        <a href="{{ route('forum.topic', $topic->id) }}" class="forum-modern-stat-pill" title="{{ $commentsCount }} {{ __('messages.replies') }}">
            <i class="fa-regular fa-comment fa-comment-o"></i>
            <span>{{ number_format($commentsCount) }}</span>
        </a>
    </div>
</article>
