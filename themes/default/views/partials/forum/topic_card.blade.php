@php
    $viewer = auth()->user();
    $topicCategoryId = (int) $topic->cat;
    $canEditTopic = auth()->check() && (
        (int) auth()->id() === (int) $topic->uid
        || $viewer->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        (int) auth()->id() === (int) $topic->uid
        || $viewer->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && $viewer->canModerateForum('pin_topics', $topicCategoryId);
    $canLockTopic = auth()->check() && $viewer->canModerateForum('lock_topics', $topicCategoryId);
    $showForumRoleBadges = (int) \App\Support\ForumSettings::get('show_role_badges', 1) === 1;

    $reactionsCount = \App\Models\Like::where('sid', $topic->id)->where('type', 2)->count();
    $commentsCount = \App\Models\ForumComment::where('tid', $topic->id)->count();
    $topicExcerpt = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $topic->txt))), 160);
@endphp

<article class="forum-rdx-discussion-row post{{ $status->id }}">
    <div class="forum-rdx-discussion-main">
        <div class="forum-rdx-discussion-title">
            <a href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a>
            <span class="forum-rdx-discussion-badges">
                @if($topic->is_pinned)
                    <span class="badge bg-warning text-dark">{{ __('messages.pinned') }}</span>
                @endif
                @if($topic->is_locked)
                    <span class="badge bg-secondary">{{ __('messages.locked') }}</span>
                @endif
            </span>
        </div>

        @if($topicExcerpt !== '')
            <p class="forum-rdx-discussion-excerpt">{{ $topicExcerpt }}</p>
        @endif

        <p class="forum-rdx-discussion-meta">
            @if($topic->user)
                <span class="forum-rdx-discussion-user">
                    <img
                        class="forum-rdx-discussion-avatar"
                        src="{{ $topic->user->img ? url($topic->user->img) : theme_asset('img/avatar/01.jpg') }}"
                        alt="{{ $topic->user->username }}"
                    >
            <a class="forum-rdx-discussion-username" href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}">
                        {{ $topic->user->username }}
                    </a>
                </span>
                @if($showForumRoleBadges)
                    <span class="forum-rdx-discussion-role">{{ $topic->user->forumRoleLabel($topicCategoryId) }}</span>
                @endif
            @else
                <span>{{ __('messages.deleted_user') }}</span>
            @endif

            <span>&middot;</span>
            <span>{{ $status->date ? \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() : '' }}</span>
            <span>&middot;</span>
            <span>
                <i class="fa {{ optional($topic->category)->icons }}" aria-hidden="true"></i>
                {{ optional($topic->category)->name }}
            </span>
        </p>
        <div id="report{{ $topic->id }}"></div>
        <div id="notif{{ $topic->id }}"></div>
    </div>

    <div class="forum-rdx-discussion-stats">
        <div class="forum-rdx-discussion-stat">
            <span class="value">{{ $commentsCount }}</span>
            <span class="label">{{ __('messages.comments') }}</span>
        </div>
        <div class="forum-rdx-discussion-stat">
            <span class="value">{{ $reactionsCount }}</span>
            <span class="label">{{ __('messages.reactions') }}</span>
        </div>
    </div>

    <div class="forum-rdx-discussion-actions">
        <div class="post-settings-wrap" style="position: relative;">
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
            </div>
            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 0; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @if($canEditTopic)
                    <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}">
                        <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
                    </a>
                @endif

                @if($canDeleteTopic)
                    <p class="simple-dropdown-link post_delete{{ $topic->id }}" onclick="deletePost({{ $topic->id }}, 2)">
                        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                    </p>
                @endif

                @if($canPinTopic)
                    <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                        @csrf
                        <button type="submit" class="simple-dropdown-link" style="width:100%; text-align:left; border:0; background:transparent;">
                            <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;
                            {{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                        </button>
                    </form>
                @endif

                @if($canLockTopic)
                    <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                        @csrf
                        <button type="submit" class="simple-dropdown-link" style="width:100%; text-align:left; border:0; background:transparent;">
                            <i class="fa {{ $topic->is_locked ? 'fa-unlock' : 'fa-lock' }}" aria-hidden="true"></i>&nbsp;
                            {{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                        </button>
                    </form>
                @endif

                <p class="simple-dropdown-link post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)">
                    <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}
                </p>

                <p class="simple-dropdown-link author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})">
                    <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }} {{ __('messages.author') }}
                </p>

                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); var notif = document.getElementById('notif{{ $topic->id }}'); if(notif){ notif.innerHTML = '<div class=&quot;alert alert-success&quot; role=&quot;alert&quot;>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function(){ notif.style.display = 'none'; }, 3000);}">
                    <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
                </p>
            </div>
        </div>
    </div>
</article>
