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

    .group-show__hero {
        overflow: hidden;
        margin-bottom: 18px;
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
    <div class="profile-header">
        <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url({{ asset($cover) }}) no-repeat scroll center center / cover;">
            <img src="{{ asset($cover) }}" alt="cover-{{ $group->slug }}" style="display: none;">
        </figure>

        <div class="profile-header-info">
            <div class="user-short-description big">
                <div class="user-short-description-avatar user-avatar big">
                    <div class="user-avatar-border">
                        <div class="hexagon-148-164" style="width: 148px; height: 164px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="148" height="164"></canvas></div>
                    </div>
                    <div class="user-avatar-content">
                        <div class="hexagon-image-100-110" data-src="{{ asset($avatar) }}" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                    </div>

                    @if($group->is_featured)
                        <div class="user-avatar-badge">
                            <div class="user-avatar-badge-border">
                                <div class="hexagon-40-44" style="width: 22px; height: 24px; position: relative;"></div>
                            </div>
                            <div class="user-avatar-badge-content">
                                <div class="hexagon-dark-32-34" style="width: 16px; height: 18px; position: relative;"></div>
                            </div>
                            <p class="user-avatar-badge-text"><i class="fa fa-star" style="color: #ffc107; font-size: 10px;"></i></p>
                        </div>
                    @endif
                </div>

                <p class="user-short-description-title" style="color: var(--group-shell-text);">
                    {{ $group->name }}
                </p>
                <p class="user-short-description-text" style="color: var(--group-shell-muted);">
                    <i class="fa {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? 'fa-globe' : 'fa-lock' }}" aria-hidden="true"></i>
                    {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}
                </p>
            </div>

            <div class="user-stats">
                <div class="user-stat big">
                    <p class="user-stat-title">{{ $group->members_count }}</p>
                    <p class="user-stat-text">{{ __('messages.members') }}</p>
                </div>
                <div class="user-stat big">
                    <p class="user-stat-title">{{ $group->posts_count }}</p>
                    <p class="user-stat-text">{{ __('messages.posts') }}</p>
                </div>
                <div class="user-stat big">
                    <p class="user-stat-title">{{ $group->owner?->username ?? __('messages.unknown_user') }}</p>
                    <p class="user-stat-text">{{ __('messages.author') }}</p>
                </div>
            </div>

            <div class="profile-header-info-actions">
                @auth
                    @if($canManageGroup)
                        <a class="profile-header-info-action button secondary" href="{{ route('groups.edit', $group) }}" style="color: #fff;">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if($membership?->status === \App\Models\GroupMembership::STATUS_ACTIVE)
                        @if($membership->role !== \App\Models\GroupMembership::ROLE_OWNER)
                            <form method="POST" action="{{ route('groups.leave', $group) }}" style="display: inline;">
                                @csrf
                                <button class="profile-header-info-action button secondary" type="submit">{{ __('messages.groups_leave') }}</button>
                            </form>
                        @endif
                    @elseif($membership?->status === \App\Models\GroupMembership::STATUS_PENDING)
                        <button class="profile-header-info-action button secondary disabled" type="button">{{ __('messages.groups_request_pending') }}</button>
                    @elseif($group->status === \App\Models\Group::STATUS_ACTIVE)
                        <form method="POST" action="{{ route('groups.join', $group) }}" style="display: inline;">
                            @csrf
                            <button class="profile-header-info-action button secondary" type="submit" style="color: #fff;">
                                {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_join_now') : __('messages.groups_request_join') }}
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="profile-header-info-action button secondary" style="color: #fff;">{{ __('messages.groups_join_now') }}</a>
                @endauth
            </div>
        </div>
    </div>

    <nav class="section-navigation">
        <div id="section-navigation-slider" class="section-menu">
            <a class="section-menu-item {{ $tab === 'overview' ? 'active' : '' }}" href="{{ route('groups.show', [$group, 'tab' => 'overview']) }}">
                <svg class="section-menu-item-icon icon-timeline"><use xlink:href="#svg-timeline"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.overview') }}</p>
            </a>
            <a class="section-menu-item {{ $tab === 'feed' ? 'active' : '' }}" href="{{ route('groups.show', [$group, 'tab' => 'feed']) }}">
                <svg class="section-menu-item-icon icon-blog-posts"><use xlink:href="#svg-blog-posts"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.feed') }}</p>
            </a>
            <a class="section-menu-item {{ $tab === 'discussions' ? 'active' : '' }}" href="{{ route('groups.show', [$group, 'tab' => 'discussions']) }}">
                <svg class="section-menu-item-icon icon-forum"><use xlink:href="#svg-forum"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.discussions') }}</p>
            </a>
            <a class="section-menu-item {{ $tab === 'members' ? 'active' : '' }}" href="{{ route('groups.show', [$group, 'tab' => 'members']) }}">
                <svg class="section-menu-item-icon icon-friend"><use xlink:href="#svg-friend"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.members') }}</p>
            </a>
        </div>
    </nav>

    <div class="grid grid-3-6-3 mobile-prefer-content">
        <div class="grid-column">
            <x-widget-column side="groups_left" />
        </div>

        <div class="grid-column">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif


            @if(!$canViewContent)
                <div class="group-show__panel">
                    <h2 class="group-show__panel-title">{{ __('messages.groups_private_shell_title') }}</h2>
                    <p class="group-show__description">{{ __('messages.groups_private_shell_description') }}</p>
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
                                        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                            <form method="POST" action="{{ route('groups.members.role', [$group, $member]) }}">
                                                @csrf
                                                <select name="role" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 8px; border: 1px solid var(--group-shell-border); background: var(--group-shell-bg); color: var(--group-shell-text);">
                                                    <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>{{ __('messages.groups_role_member') }}</option>
                                                    <option value="moderator" {{ $member->role === 'moderator' ? 'selected' : '' }}>{{ __('messages.groups_role_moderator') }}</option>
                                                </select>
                                            </form>

                                            @if($group->owner_id === Auth::id())
                                                <button class="button secondary" type="button" 
                                                        style="padding: 8px 12px; font-size: 0.85rem;" 
                                                        title="{{ __('messages.groups_transfer_ownership') }}"
                                                        onclick="toggleTransferForm({{ $member->id }})">
                                                    <i class="fa fa-right-left" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </article>

                                @if($group->owner_id === Auth::id() && $member->role !== \App\Models\GroupMembership::ROLE_OWNER)
                                    <div id="transfer-form-{{ $member->id }}" style="display: none; padding: 15px; background: var(--group-shell-soft); border-radius: 12px; margin-top: -10px; margin-bottom: 12px; border: 1px dashed var(--group-shell-accent);">
                                        <form method="POST" action="{{ route('groups.transfer', [$group, $member]) }}">
                                            @csrf
                                            <p style="font-size: 0.85rem; margin-bottom: 10px; color: var(--group-shell-text);">
                                                {{ __('messages.groups_transfer_confirm') }} <strong>{{ $member->user?->username }}</strong>
                                            </p>
                                            <div style="margin-bottom: 15px;">
                                                <input type="password" name="password" required placeholder="{{ __('messages.password') }}" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--group-shell-border); background: var(--group-shell-bg); color: var(--group-shell-text);">
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <button type="submit" class="button secondary" style="flex: 1; background: var(--group-shell-accent); border-color: var(--group-shell-accent); color: #fff; padding: 10px; font-weight: 700;">{{ __('messages.confirm') }}</button>
                                                <button type="button" class="button secondary" onclick="toggleTransferForm({{ $member->id }})" style="flex: 1; padding: 10px; font-weight: 700;">{{ __('messages.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
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
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.about') }}</p>
                <div class="widget-box-content">
                    <p class="group-show__description">{{ $group->description ?: __('messages.groups_no_description') }}</p>
                </div>
            </div>

            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.groups_rules') }}</p>
                <div class="widget-box-content">
                    <div class="group-show__discussion-text">{!! trim((string) $group->rules_markdown) !== '' ? nl2br(e($group->rules_markdown)) : e(__('messages.groups_rules_empty')) !!}</div>
                </div>
            </div>

            <x-widget-column side="groups_right" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initHexagons === 'function') {
            initHexagons();
        }
    });

    function toggleTransferForm(memberId) {
        const form = document.getElementById('transfer-form-' + memberId);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>
@endpush
