@extends('theme::layouts.master')

@push('head')
<style>
    .group-show {
        --group-shell-bg: #ffffff;
        --group-shell-soft: #f7f8ff;
        --group-shell-border: #e7eaf5;
        --group-shell-text: #2f3142;
        --group-shell-muted: #8f91ac;
        --group-shell-accent: #ff6b3d;
        --group-shell-accent-2: #23d2e2;
        --group-shell-shadow: 0 22px 44px rgba(47, 49, 66, 0.08);
    }

    body[data-theme="css_d"] .group-show {
        --group-shell-bg: #1f2436;
        --group-shell-soft: #242b3f;
        --group-shell-border: #313951;
        --group-shell-text: #f5f7ff;
        --group-shell-muted: #9aa4bf;
        --group-shell-accent: #4ff461;
        --group-shell-accent-2: #23d2e2;
        --group-shell-shadow: 0 22px 44px rgba(0, 0, 0, 0.28);
    }

    .group-show__hero,
    .group-show__panel,
    .group-show__composer {
        border-radius: 24px;
        border: 1px solid var(--group-shell-border);
        background: linear-gradient(180deg, var(--group-shell-bg) 0%, var(--group-shell-soft) 100%);
        box-shadow: var(--group-shell-shadow);
    }

    .group-show__hero {
        overflow: hidden;
        margin-bottom: 18px;
    }

    .group-show__cover {
        min-height: 170px;
        background: linear-gradient(135deg, rgba(255,107,61,0.92), rgba(35,210,226,0.92));
    }

    .group-show__hero-body {
        padding: 24px;
    }

    .group-show__eyebrow,
    .group-show__privacy {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .group-show__eyebrow {
        background: rgba(255,255,255,0.18);
        color: #fff;
        margin: 18px;
    }

    .group-show__privacy {
        background: rgba(97, 93, 250, 0.12);
        color: #615dfa;
    }

    .group-show__title {
        margin: 14px 0 8px;
        color: var(--group-shell-text);
        font-size: 2rem;
        font-weight: 900;
    }

    .group-show__description {
        margin: 0;
        color: var(--group-shell-muted);
        line-height: 1.8;
    }

    .group-show__stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .group-show__stat {
        min-width: 120px;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(97, 93, 250, 0.08);
    }

    .group-show__stat-value {
        display: block;
        color: var(--group-shell-text);
        font-size: 1.15rem;
        font-weight: 900;
    }

    .group-show__stat-label {
        color: var(--group-shell-muted);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .group-show__actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .group-show__tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .group-show__tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border-radius: 999px;
        border: 1px solid var(--group-shell-border);
        background: var(--group-shell-bg);
        color: var(--group-shell-muted);
        font-weight: 800;
        text-decoration: none;
    }

    .group-show__tab.is-active {
        border-color: transparent;
        background: var(--group-shell-accent);
        color: #fff;
    }

    .group-show__panel {
        padding: 22px;
        margin-bottom: 18px;
    }

    .group-show__panel-title {
        margin: 0 0 12px;
        color: var(--group-shell-text);
        font-size: 1.08rem;
        font-weight: 800;
    }

    .group-show__list {
        display: grid;
        gap: 14px;
    }

    .group-show__discussion {
        padding: 18px;
        border-radius: 18px;
        border: 1px solid var(--group-shell-border);
        background: var(--group-shell-bg);
    }

    .group-show__discussion-title {
        color: var(--group-shell-text);
        font-size: 1rem;
        font-weight: 800;
        text-decoration: none;
    }

    .group-show__discussion-meta,
    .group-show__discussion-text,
    .group-show__member-meta {
        color: var(--group-shell-muted);
        line-height: 1.7;
    }

    .group-show__composer {
        padding: 18px;
        margin-bottom: 18px;
    }

    .group-show__members {
        display: grid;
        gap: 12px;
    }

    .group-show__member {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        border-radius: 18px;
        border: 1px solid var(--group-shell-border);
        background: var(--group-shell-bg);
    }

    .group-show__member-main {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .group-show__member-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
    }

    .group-show__empty {
        margin: 0;
        color: var(--group-shell-muted);
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="group-show">
    <div class="grid grid-3-6-3 mobile-prefer-content">
        <div class="grid-column">
            <x-widget-column side="portal_left" />
        </div>

        <div class="grid-column">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <section class="group-show__hero">
                <div class="group-show__cover">
                    <span class="group-show__eyebrow"><i class="fa fa-users" aria-hidden="true"></i>{{ __('messages.groups_title') }}</span>
                </div>
                <div class="group-show__hero-body">
                    <span class="group-show__privacy">
                        <i class="fa {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? 'fa-globe' : 'fa-lock' }}" aria-hidden="true"></i>
                        {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}
                    </span>

                    @if($group->is_featured)
                        <span class="group-show__privacy" style="margin-inline-start:8px;">
                            <i class="fa fa-star" aria-hidden="true"></i>{{ __('messages.groups_featured') }}
                        </span>
                    @endif

                    <h1 class="group-show__title">{{ $group->name }}</h1>
                    <p class="group-show__description">{{ $group->short_description ?: __('messages.groups_no_description') }}</p>

                    <div class="group-show__stats">
                        <div class="group-show__stat">
                            <span class="group-show__stat-value">{{ $group->members_count }}</span>
                            <span class="group-show__stat-label">{{ __('messages.members') }}</span>
                        </div>
                        <div class="group-show__stat">
                            <span class="group-show__stat-value">{{ $group->posts_count }}</span>
                            <span class="group-show__stat-label">{{ __('messages.posts') }}</span>
                        </div>
                        <div class="group-show__stat">
                            <span class="group-show__stat-value">{{ $group->owner?->username ?? __('messages.unknown_user') }}</span>
                            <span class="group-show__stat-label">{{ __('messages.author') }}</span>
                        </div>
                    </div>

                    <div class="group-show__actions">
                        @auth
                            @if($membership?->status === \App\Models\GroupMembership::STATUS_ACTIVE)
                                @if($membership->role !== \App\Models\GroupMembership::ROLE_OWNER)
                                    <form method="POST" action="{{ route('groups.leave', $group) }}">
                                        @csrf
                                        <button class="button secondary" type="submit">{{ __('messages.groups_leave') }}</button>
                                    </form>
                                @endif
                            @elseif($membership?->status === \App\Models\GroupMembership::STATUS_PENDING)
                                <button class="button secondary disabled" type="button">{{ __('messages.groups_request_pending') }}</button>
                            @elseif($group->status === \App\Models\Group::STATUS_ACTIVE)
                                <form method="POST" action="{{ route('groups.join', $group) }}">
                                    @csrf
                                    <button class="button secondary" type="submit">
                                        {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_join_now') : __('messages.groups_request_join') }}
                                    </button>
                                </form>
                            @endif
                        @else
                            <a class="button secondary" href="{{ route('login') }}">{{ __('messages.login') }}</a>
                        @endauth
                    </div>
                </div>
            </section>

            <div class="group-show__tabs">
                @foreach(['overview' => 'groups_tab_overview', 'feed' => 'groups_tab_feed', 'discussions' => 'groups_tab_discussions', 'members' => 'groups_tab_members'] as $tabKey => $tabLabel)
                    <a class="group-show__tab {{ $tab === $tabKey ? 'is-active' : '' }}" href="{{ route('groups.show', ['group' => $group, 'tab' => $tabKey]) }}">
                        <span>{{ __('messages.' . $tabLabel) }}</span>
                    </a>
                @endforeach
            </div>

            @if(!$canViewContent)
                <div class="group-show__panel">
                    <h2 class="group-show__panel-title">{{ __('messages.groups_private_shell_title') }}</h2>
                    <p class="group-show__description">{{ $group->description ?: __('messages.groups_private_shell_description') }}</p>

                    @if(trim((string) $group->rules_markdown) !== '')
                        <hr>
                        <h3 class="group-show__panel-title">{{ __('messages.groups_rules') }}</h3>
                        <div class="group-show__discussion-text">{!! nl2br(e($group->rules_markdown)) !!}</div>
                    @endif
                </div>
            @else
                @if(in_array($tab, ['overview', 'feed'], true) && $canPostToGroup)
                    <section class="group-show__composer">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_share_with_group') }}</h2>
                        @include('theme::partials.status.add_post', [
                            'composerContext' => [
                                'group' => $group,
                                'group_id' => $group->id,
                                'allowedKinds' => ['text', 'link', 'gallery'],
                                'submitLabelKey' => 'messages.groups_publish_post',
                                'placeholderKey' => 'messages.groups_post_placeholder',
                                'disableDirectoryOnly' => true,
                            ],
                        ])
                    </section>
                @endif

                @if($tab === 'overview')
                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.about') }}</h2>
                        <p class="group-show__description">{{ $group->description ?: __('messages.groups_no_description') }}</p>
                    </section>

                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_rules') }}</h2>
                        <div class="group-show__discussion-text">{!! trim((string) $group->rules_markdown) !== '' ? nl2br(e($group->rules_markdown)) : e(__('messages.groups_rules_empty')) !!}</div>
                    </section>

                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_latest_feed') }}</h2>
                        <div class="group-show__list">
                            @forelse($activities->take(3) as $activity)
                                @include('theme::partials.activity.render', ['activity' => $activity])
                            @empty
                                <p class="group-show__empty">{{ __('messages.groups_feed_empty') }}</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_latest_discussions') }}</h2>
                        <div class="group-show__list">
                            @forelse($discussions->take(4) as $topic)
                                <article class="group-show__discussion">
                                    <a class="group-show__discussion-title" href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a>
                                    <p class="group-show__discussion-meta">{{ $topic->user?->username ?? __('messages.unknown_user') }} &middot; {{ $topic->date ? \Carbon\Carbon::createFromTimestamp($topic->date)->diffForHumans() : '' }}</p>
                                    <p class="group-show__discussion-text">{{ \Illuminate\Support\Str::limit(strip_tags((string) $topic->txt), 180) }}</p>
                                </article>
                            @empty
                                <p class="group-show__empty">{{ __('messages.groups_discussions_empty') }}</p>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($tab === 'feed')
                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_tab_feed') }}</h2>
                        <div class="group-show__list">
                            @forelse($activities as $activity)
                                @include('theme::partials.activity.render', ['activity' => $activity])
                            @empty
                                <p class="group-show__empty">{{ __('messages.groups_feed_empty') }}</p>
                            @endforelse
                        </div>

                        @if($activities->total() > 0)
                            <div style="margin-top:18px;">{{ $activities->appends(['tab' => 'feed'])->links() }}</div>
                        @endif
                    </section>
                @endif

                @if($tab === 'discussions')
                    @if($canPostToGroup)
                        <section class="group-show__composer">
                            <h2 class="group-show__panel-title">{{ __('messages.groups_start_discussion') }}</h2>
                            <form method="POST" action="{{ route('groups.discussions.store', $group) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-item">
                                    <div class="form-input small">
                                        <label for="group-discussion-name">{{ __('messages.subject') }}</label>
                                        <input id="group-discussion-name" type="text" name="name" required>
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-input small">
                                        <label for="group-discussion-text">{{ __('messages.description') }}</label>
                                        <textarea id="group-discussion-text" name="txt" rows="5" required></textarea>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-item">
                                        <label><input type="radio" name="type" value="2" checked> {{ __('messages.text') }}</label>
                                    </div>
                                    <div class="form-item">
                                        <label><input type="radio" name="type" value="4"> {{ __('messages.insertphoto') }}</label>
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-input small">
                                        <label for="group-discussion-image">{{ __('messages.insertphoto') }}</label>
                                        <input id="group-discussion-image" type="file" name="img" accept=".jpg,.jpeg,.png,.gif,.webp">
                                    </div>
                                </div>
                                <button class="button secondary" type="submit">{{ __('messages.groups_publish_discussion') }}</button>
                            </form>
                        </section>
                    @endif

                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_tab_discussions') }}</h2>
                        <div class="group-show__list">
                            @forelse($discussions as $topic)
                                <article class="group-show__discussion">
                                    <a class="group-show__discussion-title" href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a>
                                    <p class="group-show__discussion-meta">{{ $topic->user?->username ?? __('messages.unknown_user') }} &middot; {{ $topic->date ? \Carbon\Carbon::createFromTimestamp($topic->date)->diffForHumans() : '' }}</p>
                                    <p class="group-show__discussion-text">{{ \Illuminate\Support\Str::limit(strip_tags((string) $topic->txt), 220) }}</p>
                                </article>
                            @empty
                                <p class="group-show__empty">{{ __('messages.groups_discussions_empty') }}</p>
                            @endforelse
                        </div>

                        @if($discussions->total() > 0)
                            <div style="margin-top:18px;">{{ $discussions->appends(['tab' => 'discussions'])->links() }}</div>
                        @endif
                    </section>
                @endif

                @if($tab === 'members')
                    <section class="group-show__panel">
                        <h2 class="group-show__panel-title">{{ __('messages.groups_members_title') }}</h2>
                        <div class="group-show__members">
                            @forelse($members as $member)
                                <article class="group-show__member">
                                    <div class="group-show__member-main">
                                        <img class="group-show__member-avatar" src="{{ $member->user?->avatarUrl() ?? asset('upload/_avatar.png') }}" alt="{{ $member->user?->username ?? 'member' }}">
                                        <div>
                                            <strong>{{ $member->user?->username ?? __('messages.unknown_user') }}</strong>
                                            <p class="group-show__member-meta mb-0">{{ __('messages.groups_role_' . $member->role) }}</p>
                                        </div>
                                    </div>
                                    @if($canManageGroup && $member->role !== \App\Models\GroupMembership::ROLE_OWNER)
                                        <form method="POST" action="{{ route('groups.members.role', [$group, $member]) }}">
                                            @csrf
                                            <select name="role" onchange="this.form.submit()">
                                                <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>{{ __('messages.groups_role_member') }}</option>
                                                <option value="moderator" {{ $member->role === 'moderator' ? 'selected' : '' }}>{{ __('messages.groups_role_moderator') }}</option>
                                            </select>
                                        </form>
                                    @endif
                                </article>
                            @empty
                                <p class="group-show__empty">{{ __('messages.groups_members_empty') }}</p>
                            @endforelse
                        </div>

                        @if($members->total() > 0)
                            <div style="margin-top:18px;">{{ $members->appends(['tab' => 'members'])->links() }}</div>
                        @endif
                    </section>

                    @if($canManageGroup)
                        <section class="group-show__panel">
                            <h2 class="group-show__panel-title">{{ __('messages.groups_pending_requests') }}</h2>
                            <div class="group-show__members">
                                @forelse($pendingMemberships as $pending)
                                    <article class="group-show__member">
                                        <div class="group-show__member-main">
                                            <img class="group-show__member-avatar" src="{{ $pending->user?->avatarUrl() ?? asset('upload/_avatar.png') }}" alt="{{ $pending->user?->username ?? 'member' }}">
                                            <div>
                                                <strong>{{ $pending->user?->username ?? __('messages.unknown_user') }}</strong>
                                                <p class="group-show__member-meta mb-0">{{ __('messages.groups_request_pending') }}</p>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                            <form method="POST" action="{{ route('groups.members.approve', [$group, $pending]) }}">
                                                @csrf
                                                <button class="button secondary" type="submit">{{ __('messages.approve') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('groups.members.reject', [$group, $pending]) }}">
                                                @csrf
                                                <button class="button secondary" type="submit">{{ __('messages.reject') }}</button>
                                            </form>
                                        </div>
                                    </article>
                                @empty
                                    <p class="group-show__empty">{{ __('messages.groups_pending_empty') }}</p>
                                @endforelse
                            </div>
                        </section>
                    @endif
                @endif
            @endif
        </div>

        <div class="grid-column">
            <x-widget-column side="portal_right" />
        </div>
    </div>
</div>
@endsection
