@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
@php
    $group = $group ?? null;
    $showForumRoleBadges = (int) ($forumSettings['show_role_badges'] ?? 1) === 1;
    $topicCategoryId = (int) $topic->cat;
    $groupAccess = app(\App\Services\GroupAccessService::class);
    $canManageGroupTopic = $group && auth()->check() ? $groupAccess->canManageGroup($group, auth()->user()) : false;
    $canEditTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('pin_topics', $topicCategoryId));
    $canLockTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('lock_topics', $topicCategoryId));
    $canCommentWhenLocked = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('lock_topics', $topicCategoryId)
    );
    $isModerator = auth()->check() && (
        auth()->user()->isAdmin()
        || $canManageGroupTopic
        || auth()->user()->canModerateForum(null, $topicCategoryId)
        || $canPinTopic
        || $canLockTopic
    );
    $catColor = $topic->category?->color ?: '#615dfa';
    $totalPostsCount = max(1, $topicComments->count() + 1);
@endphp

<main class="forum-modern-forum-wrapper forum-modern-topic-view forum-rdx-topic" id="forumTopicTop">
    <!-- Breadcrumb & ADS -->
    <nav class="forum-modern-breadcrumb-bar mb-3" aria-label="{{ __('messages.breadcrumbs') }}">
        <div class="d-flex align-items-center flex-wrap gap-2 fs-13 text-muted">
            <a href="{{ route('forum.index') }}" class="text-decoration-none text-muted">
                <i class="fa fa-home"></i> {{ __('messages.forum') ?? 'Forum' }}
            </a>
            <span>/</span>
            @if(!empty($group))
                <a href="{{ route('groups.index') }}" class="text-decoration-none text-muted">{{ __('messages.groups_title') }}</a>
                <span>/</span>
                <a href="{{ route('groups.show', $group) }}" class="text-decoration-none text-muted">
                    <i class="fa fa-users"></i> {{ $group->name }}
                </a>
                <span>/</span>
            @elseif($topic->category)
                <a href="{{ route('forum.category', $topic->cat) }}" class="text-decoration-none" style="color: {{ $catColor }}; font-weight: 600;">
                    <span class="forum-modern-tag-bullet" style="background-color: {{ $catColor }};"></span>
                    {{ $topic->category->name }}
                </a>
                <span>/</span>
            @endif
            <span class="text-dark fw-bold text-truncate" style="max-width: 320px;">{{ $topic->name }}</span>
        </div>
    </nav>

    <!-- ADS -->
    @include('theme::partials.ads', ['id' => 5])

    <!-- Topic Hero Header -->
    <section class="forum-modern-hero" style="border-inline-start: 6px solid {{ $catColor }}; margin-bottom: 24px;">
        <div class="forum-modern-hero-copy">
            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                @if($topic->category)
                    <a href="{{ route('forum.category', $topic->cat) }}" class="forum-modern-card-tag" style="background-color: {{ $catColor }};">
                        <span>{{ $topic->category->name }}</span>
                    </a>
                @endif
                @if($group)
                    @include('theme::partials.groups.badge', ['groupBadge' => $group])
                @endif
                @if($topic->is_pinned)
                    <span class="forum-modern-badge-pinned"><i class="fa fa-thumb-tack"></i> {{ __('messages.pinned') ?? 'Pinned' }}</span>
                @endif
                @if($topic->is_locked)
                    <span class="forum-modern-badge-locked"><i class="fa fa-lock"></i> {{ __('messages.locked') ?? 'Locked' }}</span>
                @endif
            </div>

            <h1 class="forum-modern-hero-title mb-2">
                <span>{{ $topic->name }}</span>
            </h1>

            <div class="d-flex align-items-center flex-wrap gap-3 fs-13 text-muted">
                <span>
                    <i class="fa-regular fa-user fa-user-o me-1"></i>
                    @if($topic->user)
                        <a href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}" class="fw-bold text-decoration-none text-dark">
                            {{ $topic->user->username }}
                        </a>
                    @else
                        <span>{{ __('messages.deleted_user') ?? 'Deleted User' }}</span>
                    @endif
                </span>
                <span>
                    <i class="fa-regular fa-clock fa-clock-o me-1"></i>
                    {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                </span>
                <span>
                    <i class="fa fa-eye me-1"></i>
                    {{ number_format($topic->vu) }} {{ __('messages.views') ?? 'views' }}
                </span>
                <span>
                    <i class="fa-regular fa-comment fa-comment-o me-1"></i>
                    {{ number_format($topicComments->count()) }} {{ __('messages.replies') ?? 'replies' }}
                </span>
            </div>
        </div>

        <div class="forum-modern-hero-actions">
            @auth
                @if(!$topic->is_locked || $canCommentWhenLocked)
                    <button type="button" class="forum-modern-start-btn" onclick="focusComment({{ $topic->id }})">
                        <i class="fa fa-reply"></i>
                        <span>{{ __('messages.reply') ?? 'Reply' }}</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="forum-modern-start-btn">
                    <i class="fa fa-sign-in"></i>
                    <span>{{ __('messages.login_to_participate') ?? 'Login to Reply' }}</span>
                </a>
            @endauth
        </div>
    </section>

    <!-- Forum Discussion Layout (Post Stream + Scrubber Sidebar) -->
    <div class="forum-modern-topic-layout">
        <!-- Main Post Stream Column -->
        <div class="forum-modern-stream-container">
            <!-- OP / Original Post (#1) -->
            <article class="forum-modern-post-card post{{ $status->id }}" id="post-1">
                <!-- Post Header -->
                <header class="forum-modern-post-header">
                    <div class="forum-modern-post-author">
                        @if($topic->user)
                            <div class="forum-modern-avatar-wrap">
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
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}" class="forum-modern-post-author-name">
                                        {{ $topic->user->username }}
                                    </a>
                                    @if($topic->user->hasVerifiedBadge())
                                        <i class="fa fa-check-circle text-primary fs-13" title="{{ __('messages.verified') ?? 'Verified' }}"></i>
                                    @endif
                                    @if($showForumRoleBadges)
                                        <span class="forum-modern-post-badge">{{ $topic->user->forumRoleLabel($topicCategoryId) }}</span>
                                    @endif
                                </div>
                                <span class="forum-modern-post-time" title="{{ date('Y-m-d H:i:s', $status->date) }}">
                                    {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                                </span>
                            </div>
                        @else
                            <div class="forum-modern-avatar-wrap">
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
                            </div>
                            <div>
                                <span class="forum-modern-post-author-name">{{ __('messages.deleted_user') ?? 'Deleted User' }}</span>
                                <span class="forum-modern-post-time">{{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="forum-modern-post-number">#1</span>

                        <!-- Post Actions / Moderation Dropdown -->
                        <div class="widget-box-settings ms-2">
                            <div class="post-settings-wrap" style="position: relative;">
                                <div class="post-settings widget-box-post-settings-dropdown-trigger cursor-pointer p-1">
                                    <i class="fa fa-ellipsis-h text-muted fs-16"></i>
                                </div>
                                <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 0; opacity: 0; visibility: hidden; transform: translate(0px, -10px); transition: all 0.2s ease;">
                                    @if($canEditTopic)
                                        <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}">
                                            <i class="fa fa-edit text-primary me-2"></i> {{ __('messages.edit') }}
                                        </a>
                                    @endif
                                    @if($canDeleteTopic)
                                        <p class="simple-dropdown-link cursor-pointer text-danger" onclick="deletePost({{ $topic->id }}, 2, '#post_form{{ $topic->id }}')">
                                            <i class="fa fa-trash me-2"></i> {{ __('messages.delete') }}
                                        </p>
                                    @endif
                                    @include('theme::partials.activity.promotion_link', ['activity' => $status])
                                    @if($canPinTopic)
                                        <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                                            @csrf
                                            <button type="submit" class="simple-dropdown-link w-100 text-start border-0 bg-transparent">
                                                <i class="fa fa-thumb-tack text-warning me-2"></i> {{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($canLockTopic)
                                        <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                                            @csrf
                                            <button type="submit" class="simple-dropdown-link w-100 text-start border-0 bg-transparent">
                                                <i class="fa {{ $topic->is_locked ? 'fa-unlock' : 'fa-lock' }} text-secondary me-2"></i> {{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                                            </button>
                                        </form>
                                    @endif
                                    <p class="simple-dropdown-link cursor-pointer post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)">
                                        <i class="fa fa-flag text-warning me-2"></i> {{ __('messages.report') }}
                                    </p>
                                    @if($topic->uid)
                                        <p class="simple-dropdown-link cursor-pointer author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})">
                                            <i class="fa-regular fa-flag fa-flag-o text-warning me-2"></i> {{ __('messages.report') }} {{ __('messages.author') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Post Body -->
                <div class="forum-modern-post-body forum-topic-content">
                    {!! \App\Support\ContentFormatter::formatForum($topic->txt) !!}

                    @if($topic->imageOption)
                        <div class="mt-3">
                            <img src="{{ asset($topic->imageOption->o_valuer) }}" class="img-fluid rounded-3" style="max-height: 500px; object-fit: contain;" alt="Topic Image">
                        </div>
                    @endif

                    @if($topic->attachments->isNotEmpty())
                        <div class="forum-modern-nav-card mt-3">
                            <div class="forum-modern-nav-title"><i class="fa fa-paperclip me-1"></i> {{ __('messages.topic_attachments') ?? 'Attachments' }}</div>
                            <div class="d-flex flex-column gap-2 p-2">
                                @foreach($topic->attachments as $attachment)
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                        <a href="{{ route('forum.attachment.download', $attachment->id) }}" class="text-decoration-none fw-bold text-primary">
                                            <i class="fa-regular fa-file-lines fa-file-text-o me-2"></i> {{ $attachment->original_name }}
                                        </a>
                                        <span class="fs-12 text-muted">({{ $attachment->human_size }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Post Footer Actions (Reactions & Sharing) -->
                <footer class="forum-modern-post-actions">
                    @auth
                        <!-- Reaction Trigger -->
                        <div class="post-option-wrap position-relative">
                            <button type="button" class="forum-modern-post-action-btn reaction-options-dropdown-trigger" onclick="toggleReactionDropdown(this)">
                                <span id="reaction_image{{ $status->id }}">
                                    @if($viewerReaction)
                                        <img src="{{ theme_asset('img/reaction/'.$viewerReactionType.'.png') }}" width="20" alt="{{ $viewerReactionType }}">
                                    @else
                                        <i class="fa-regular fa-thumbs-up fa-thumbs-o-up text-primary"></i>
                                    @endif
                                </span>
                                <span class="reaction_txt{{ $status->id }} {{ $viewerReaction ? 'text-info fw-bold' : '' }}">
                                    {{ $viewerReaction ? __('messages.' . $viewerReactionType) : __('messages.react') }}
                                </span>
                            </button>

                            <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 42px; left: 0; display: none; background: #fff; padding: 6px 10px; border-radius: 24px; box-shadow: 0 4px 18px rgba(0,0,0,0.15); gap: 6px;">
                                @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                                    <div class="reaction-option cursor-pointer" title="{{ $reaction }}" onclick="postReaction({{ $topic->id }}, '{{ $reaction }}')">
                                        <img src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" width="24" height="24" alt="{{ $reaction }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if(!$topic->is_locked || $canCommentWhenLocked)
                            <button type="button" class="forum-modern-post-action-btn" onclick="focusComment({{ $topic->id }})">
                                <i class="fa fa-reply text-muted"></i>
                                <span>{{ __('messages.reply') ?? 'Reply' }}</span>
                            </button>
                        @endif
                    @endauth

                    <!-- Share Trigger -->
                    <div class="forum-modern-share-container post-option-wrap ms-auto">
                        <button type="button" class="forum-modern-post-action-btn" onclick="toggleForumShare(this, event)">
                            <i class="fa fa-share-alt text-muted"></i>
                            <span>{{ __('messages.share') ?? 'Share' }}</span>
                        </button>
                        <div class="forum-modern-share-dropdown" id="forumTopicShareDropdown">
                            <div class="forum-modern-share-socials">
                                @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                                    <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('forum.topic', $topic->id) }}', '{{ addslashes($topic->name) }}')" class="forum-modern-share-social-btn" title="{{ ucfirst($social) }}">
                                        <img src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}" width="20" height="20" alt="{{ $social }}">
                                    </a>
                                @endforeach
                            </div>
                            <button type="button" class="forum-modern-share-copy-btn" onclick="copyTopicUrl('{{ route('forum.topic', $topic->id) }}', this)">
                                <i class="fa fa-link"></i>
                                <span class="share-copy-text">{{ __('messages.copy_topic_link') ?? 'نسخ الرابط' }}</span>
                            </button>
                        </div>
                    </div>
                </footer>
            </article>

            <!-- Comments Stream List -->
            <div class="forum-modern-discussion-comments-wrapper post-comment-list post-comment-list-{{ $topic->id }} comment_2_{{ $topic->id }} post{{ $status->id }}">
                @include('theme::forum.partials.topic_comments', [
                    'comments' => $topicComments,
                    'id' => $topic->id,
                    'topic' => $topic,
                    'type' => 'forum',
                    'limit' => 100,
                    'hide_form' => $topic->is_locked && !$canCommentWhenLocked,
                    'locked_topic' => (bool) $topic->is_locked,
                    'forum_category_id' => $topicCategoryId
                ])
            </div>
        </div>

        <!-- Right Sidebar: Sticky Forum Timeline Scrubber -->
        <aside class="forum-modern-scrubber-sidebar">
            <div class="forum-modern-scrubber">
                @auth
                    @if(!$topic->is_locked || $canCommentWhenLocked)
                        <button type="button" class="forum-modern-scrubber-reply-btn" onclick="focusComment({{ $topic->id }})">
                            <i class="fa fa-reply"></i>
                            <span>{{ __('messages.reply') ?? 'Reply' }}</span>
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="forum-modern-scrubber-reply-btn text-decoration-none">
                        <i class="fa fa-sign-in"></i>
                        <span>{{ __('messages.login_to_participate') ?? 'Login to Reply' }}</span>
                    </a>
                @endauth

                <!-- Moderator Tools Sidebar Card (تظهر للمشرفين فقط) -->
                @if($isModerator)
                    <div class="forum-modern-mod-tools-card">
                        <div class="forum-modern-mod-tools-header">
                            <div class="forum-modern-mod-tools-title">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>{{ __('messages.moderator_tools') }}</span>
                            </div>
                            <span class="forum-modern-mod-role-badge">{{ auth()->user()->forumRoleLabel($topicCategoryId) }}</span>
                        </div>

                        <div class="forum-modern-mod-tools-list">
                            @if($canPinTopic)
                                <form method="POST" action="{{ route('forum.pin', $topic->id) }}" class="m-0 p-0 w-100">
                                    @csrf
                                    <button type="submit" class="forum-modern-mod-tool-btn {{ $topic->is_pinned ? 'is-active-pin' : '' }}" title="{{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}">
                                        <i class="fa-solid fa-thumbtack"></i>
                                        <span>{{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}</span>
                                    </button>
                                </form>
                            @endif

                            @if($canLockTopic)
                                <form method="POST" action="{{ route('forum.lock', $topic->id) }}" class="m-0 p-0 w-100">
                                    @csrf
                                    <button type="submit" class="forum-modern-mod-tool-btn {{ $topic->is_locked ? 'is-active-lock' : '' }}" title="{{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}">
                                        <i class="fa-solid {{ $topic->is_locked ? 'fa-lock-open text-success' : 'fa-lock text-danger' }}"></i>
                                        <span>{{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}</span>
                                    </button>
                                </form>
                            @endif

                            @if($canEditTopic)
                                <a href="{{ route('forum.edit', $topic->id) }}" class="forum-modern-mod-tool-btn" title="{{ __('messages.edit_topic') }}">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    <span>{{ __('messages.edit_topic') }}</span>
                                </a>
                            @endif

                            @if($canDeleteTopic)
                                <button type="button" class="forum-modern-mod-tool-btn forum-modern-mod-btn-delete" onclick="deleteForumTopic({{ $topic->id }})" title="{{ __('messages.delete_topic') }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>{{ __('messages.delete_topic') }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Timeline Scrubber Track -->
                <div class="forum-modern-scrubber-track">
                    <a href="#post-1" class="forum-modern-scrubber-jump">
                        <i class="fa fa-angle-double-up"></i>
                        <span>{{ __('messages.top') ?? 'Original Post' }}</span>
                    </a>

                    <div class="forum-modern-scrubber-handle">
                        <i class="fa fa-compass me-1"></i>
                        <span>{{ $totalPostsCount }} / {{ $totalPostsCount }}</span>
                    </div>

                    <a href="javascript:void(0);" onclick="focusComment({{ $topic->id }})" class="forum-modern-scrubber-jump">
                        <i class="fa fa-angle-double-down"></i>
                        <span>{{ __('messages.latest') ?? 'Latest Reply' }}</span>
                    </a>
                </div>

                <!-- Discussion Meta Card -->
                <div class="forum-modern-nav-card mt-2">
                    <div class="forum-modern-nav-title">{{ __('messages.info') ?? 'Discussion Info' }}</div>
                    <div class="d-flex flex-column gap-2 p-1 fs-12 text-muted">
                        <div class="d-flex justify-content-between">
                            <span>{{ __('messages.created') ?? 'Created' }}:</span>
                            <strong class="text-dark">{{ \Carbon\Carbon::createFromTimestamp($status->date)->format('M d, Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('messages.replies') ?? 'Replies' }}:</span>
                            <strong class="text-dark">{{ number_format($topicComments->count()) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('messages.views') ?? 'Views' }}:</span>
                            <strong class="text-dark">{{ number_format($topic->vu) }}</strong>
                        </div>
                        @if($topic->category)
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span>{{ __('messages.category') }}:</span>
                                <a href="{{ route('forum.category', $topic->cat) }}" class="forum-modern-card-tag" style="background-color: {{ $catColor }};">
                                    {{ $topic->category->name }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <x-widget-column side="forum_right" />
            </div>
        </aside>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initHexagons === 'function') {
        initHexagons(document);
    }
    if (typeof window.highlightCommentFromHash === 'function') {
        window.highlightCommentFromHash();
    }

    const checkAndFocusReply = function() {
        const hash = (window.location.hash || '').toLowerCase();
        if (hash === '#reply' || hash === '#reply-box' || hash.indexOf('forumreplybox') !== -1) {
            setTimeout(function() {
                if (typeof focusComment === 'function') {
                    focusComment({{ $topic->id }});
                } else {
                    const el = document.getElementById('txt_comment{{ $topic->id }}') || document.getElementById('reply');
                    if (el) {
                        if (typeof el.focus === 'function') el.focus();
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }, 300);
        }
    };

    checkAndFocusReply();
    window.addEventListener('hashchange', checkAndFocusReply);
});

function postReaction(id, reaction) {
    fetch('{{ route('reaction.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id, type: 'forum', reaction: reaction })
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            const reactionImg = document.getElementById('reaction_image' + {{ $status->id }});
            if (reactionImg) reactionImg.innerHTML = data.html;
            let textEl = document.querySelector('.reaction_txt' + {{ $status->id }});
            if (textEl) {
                if (data.action === 'added' || data.action === 'updated') {
                    textEl.classList.add('text-info', 'fw-bold');
                    textEl.innerHTML = '&nbsp;' + reaction.charAt(0).toUpperCase() + reaction.slice(1);
                } else {
                    textEl.classList.remove('text-info', 'fw-bold');
                    textEl.innerHTML = '&nbsp;{{ __("messages.react") }}';
                }
            }
        }
    });
}

function reportPost(id, type) {
    let reason = prompt('{{ __("messages.report_reason") }}');
    if (reason) {
        fetch('{{ route("forum.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tp_id: id, s_type: type, txt: reason })
        })
        .then(response => response.json())
        .then(data => {
            alert('{{ __("messages.report_sent") }}');
        });
    }
}

function toggleReactionDropdown(element) {
    let dropdown = element.nextElementSibling;
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'flex' : 'none';
    }
}

function toggleForumShare(btn, event) {
    if (event) event.stopPropagation();
    const container = btn.closest('.forum-modern-share-container');
    const dropdown = container ? container.querySelector('.forum-modern-share-dropdown') : null;
    if (!dropdown) return;
    const isOpen = dropdown.classList.contains('is-open');
    document.querySelectorAll('.forum-modern-share-dropdown.is-open').forEach(d => d.classList.remove('is-open'));
    if (!isOpen) {
        dropdown.classList.add('is-open');
    }
}

function copyTopicUrl(url, btn) {
    const onCopiedSuccess = function() {
        btn.classList.add('copied');
        const textEl = btn.querySelector('.share-copy-text');
        const oldText = textEl ? textEl.textContent : '';
        if (textEl) textEl.textContent = @json(__('messages.link_copied') ?? 'تم النسخ!');
        setTimeout(() => {
            btn.classList.remove('copied');
            if (textEl) textEl.textContent = oldText;
        }, 2000);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(onCopiedSuccess).catch(() => fallbackCopy(url, onCopiedSuccess));
    } else {
        fallbackCopy(url, onCopiedSuccess);
    }
}

function fallbackCopy(text, callback) {
    const temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.select();
    try {
        document.execCommand('copy');
        if (typeof callback === 'function') callback();
    } catch (e) {
        prompt('Copy link:', text);
    }
    document.body.removeChild(temp);
}

window.deleteForumTopic = function(topicId) {
    const confirmMsg = @json(__('messages.confirm_delete') ?? 'Are you sure you want to delete this topic?');
    const doDelete = function() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
        fetch('{{ route("forum.delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ id: topicId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = @json($topic->category ? route("forum.category", $topic->cat) : route("forum.index"));
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
    };

    if (typeof showConfirmModal === 'function') {
        showConfirmModal(confirmMsg, doDelete);
    } else if (confirm(confirmMsg)) {
        doDelete();
    }
};

document.addEventListener('click', function(e) {
    if (!e.target.closest('.forum-modern-share-container')) {
        document.querySelectorAll('.forum-modern-share-dropdown.is-open').forEach(d => d.classList.remove('is-open'));
    }
});
</script>
@endsection
