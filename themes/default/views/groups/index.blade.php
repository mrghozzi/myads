@extends('theme::layouts.master')

@push('head')
<style>
    .groups-hub {
        --groups-card-bg: #ffffff;
        --groups-card-soft: #f7f9fc;
        --groups-border: rgba(97, 93, 250, 0.12);
        --groups-border-hover: rgba(97, 93, 250, 0.35);
        --groups-text: #2f3142;
        --groups-muted: #8f91ac;
        --groups-accent: #615dfa;
        --groups-accent-gradient: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
        --groups-shadow: 0 16px 36px rgba(47, 49, 66, 0.06);
        --groups-shadow-hover: 0 20px 48px rgba(97, 93, 250, 0.16);
    }

    body[data-theme="css_d"] .groups-hub {
        --groups-card-bg: #1f2436;
        --groups-card-soft: #242b3f;
        --groups-border: rgba(255, 255, 255, 0.08);
        --groups-border-hover: rgba(79, 244, 97, 0.35);
        --groups-text: #f5f7ff;
        --groups-muted: #9aa4bf;
        --groups-accent: #4ff461;
        --groups-accent-gradient: linear-gradient(135deg, #4ff461 0%, #23d2e2 100%);
        --groups-shadow: 0 16px 36px rgba(0, 0, 0, 0.32);
        --groups-shadow-hover: 0 20px 48px rgba(0, 0, 0, 0.45);
    }

    /* Superdesign Hero */
    .groups-hub__hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 36px;
        margin-bottom: 24px;
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.08) 100%), var(--groups-card-bg);
        border: 1px solid var(--groups-border);
        box-shadow: var(--groups-shadow);
        backdrop-filter: blur(12px);
    }

    .groups-hub__hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(97, 93, 250, 0.18) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .groups-hub__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(97, 93, 250, 0.12);
        color: var(--groups-accent);
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .groups-hub__title {
        margin: 16px 0 8px;
        color: var(--groups-text);
        font-size: 2.2rem;
        font-weight: 900;
        line-height: 1.2;
    }

    .groups-hub__subtitle {
        margin: 0 0 24px;
        color: var(--groups-muted);
        font-size: 1.05rem;
        line-height: 1.7;
        max-width: 680px;
    }

    /* Search & Filter Bar */
    .groups-hub__toolbar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .groups-hub__search-wrap {
        position: relative;
        flex: 1;
        min-width: 260px;
    }

    .groups-hub__search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--groups-muted);
        pointer-events: none;
    }

    [dir="rtl"] .groups-hub__search-icon {
        left: auto;
        right: 18px;
    }

    .groups-hub__search {
        width: 100%;
        height: 50px;
        padding: 0 16px 0 46px;
        border-radius: 14px;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-bg);
        color: var(--groups-text);
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }

    [dir="rtl"] .groups-hub__search {
        padding: 0 46px 0 16px;
    }

    .groups-hub__search:focus {
        border-color: var(--groups-accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
    }

    .groups-hub__select {
        height: 50px;
        padding: 0 38px 0 16px;
        border-radius: 14px;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-bg);
        color: var(--groups-text);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
    }

    [dir="rtl"] .groups-hub__select {
        padding: 0 16px 0 38px;
    }

    .groups-hub__button-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        height: 50px;
        padding: 0 22px;
        border-radius: 14px;
        background: var(--groups-accent-gradient);
        color: #fff !important;
        font-weight: 800;
        font-size: 0.92rem;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(97, 93, 250, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }

    .groups-hub__button-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(97, 93, 250, 0.35);
    }

    /* Category Filter Pills */
    .groups-hub__categories {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 8px 2px 18px;
        margin-bottom: 16px;
        scrollbar-width: thin;
    }

    .groups-hub__cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 999px;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-bg);
        color: var(--groups-muted);
        font-weight: 800;
        font-size: 0.85rem;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .groups-hub__cat-pill:hover,
    .groups-hub__cat-pill.is-active {
        background: var(--groups-accent);
        border-color: var(--groups-accent);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(97, 93, 250, 0.25);
        transform: translateY(-1px);
    }

    /* Cards Grid */
    .groups-hub__cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .groups-card {
        border-radius: 20px;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-bg);
        box-shadow: var(--groups-shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .groups-card:hover {
        transform: translateY(-4px);
        border-color: var(--groups-border-hover);
        box-shadow: var(--groups-shadow-hover);
    }

    .groups-card__cover {
        height: 110px;
        width: 100%;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .groups-card__cover-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%);
    }

    .groups-card__chips {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        z-index: 2;
    }

    [dir="rtl"] .groups-card__chips {
        right: auto;
        left: 12px;
    }

    .groups-card__chip {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        backdrop-filter: blur(8px);
    }

    .groups-card__chip--privacy {
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
    }

    .groups-card__chip--pts {
        background: #ffb800;
        color: #000;
        box-shadow: 0 4px 12px rgba(255, 184, 0, 0.3);
    }

    .groups-card__chip--featured {
        background: #ff5722;
        color: #fff;
    }

    .groups-card__body {
        padding: 16px 20px 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
        position: relative;
    }

    .groups-card__avatar-wrap {
        margin-top: -46px;
        margin-bottom: 12px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .groups-card__avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        object-fit: cover;
        border: 4px solid var(--groups-card-bg);
        box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        background: #fff;
    }

    .groups-card__category {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--groups-accent);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .groups-card__title {
        margin: 0 0 8px;
        font-size: 1.15rem;
        font-weight: 900;
        line-height: 1.35;
        color: var(--groups-text);
    }

    .groups-card__title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .groups-card__title a:hover {
        color: var(--groups-accent);
    }

    .groups-card__desc {
        color: var(--groups-muted);
        font-size: 0.88rem;
        line-height: 1.6;
        margin-bottom: 16px;
        flex: 1;
    }

    .groups-card__meta {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--groups-border);
        margin-top: auto;
    }

    .groups-card__stat {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--groups-muted);
    }

    .groups-card__stat i {
        color: var(--groups-accent);
    }

    .groups-card__footer {
        padding: 0 20px 20px;
        display: flex;
        gap: 10px;
    }

    .groups-card__btn {
        width: 100%;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 800;
        font-size: 0.88rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-soft);
        color: var(--groups-text);
    }

    .groups-card__btn:hover {
        background: var(--groups-accent);
        border-color: var(--groups-accent);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="groups-hub">
    <!-- Superdesign Hero Banner -->
    <section class="groups-hub__hero">
        <span class="groups-hub__eyebrow">
            <i class="fa fa-users" aria-hidden="true"></i> {{ __('messages.groups_title') }}
        </span>
        <h1 class="groups-hub__title">{{ __('messages.groups_find_your_corner') }}</h1>
        <p class="groups-hub__subtitle">{{ __('messages.groups_index_intro') }}</p>

        <!-- Search & Control Toolbar -->
        <form method="GET" action="{{ route('groups.index') }}" class="groups-hub__toolbar">
            @if(request('category') && request('category') !== 'all')
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <div class="groups-hub__search-wrap">
                <i class="fa fa-search groups-hub__search-icon" aria-hidden="true"></i>
                <input class="groups-hub__search" type="search" name="search" value="{{ $search }}" placeholder="{{ __('messages.groups_search_placeholder') }}">
            </div>

            <select name="sort" class="groups-hub__select" onchange="this.form.submit()">
                <option value="trending" {{ $sort === 'trending' ? 'selected' : '' }}>{{ __('messages.groups_sort_trending') }}</option>
                <option value="active" {{ $sort === 'active' ? 'selected' : '' }}>{{ __('messages.groups_sort_active') }}</option>
                <option value="members" {{ $sort === 'members' ? 'selected' : '' }}>{{ __('messages.groups_sort_members') }}</option>
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>{{ __('messages.groups_sort_newest') }}</option>
            </select>

            <button type="submit" class="button secondary" style="height: 50px; padding: 0 20px; border-radius: 14px;">
                <i class="fa fa-filter me-1" aria-hidden="true"></i> {{ __('messages.search') }}
            </button>

            @auth
                @if($creationEligibility && $creationEligibility['allowed'])
                    <a class="groups-hub__button-cta" href="{{ route('groups.create') }}">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        <span>{{ __('messages.groups_create_title') }}</span>
                    </a>
                @endif
            @endauth
        </form>
    </section>

    <!-- Category Filter Pills -->
    <div class="groups-hub__categories">
        <a href="{{ route('groups.index', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}" 
           class="groups-hub__cat-pill {{ (!request('category') || request('category') === 'all') ? 'is-active' : '' }}">
            <i class="fa fa-compass" aria-hidden="true"></i>
            {{ __('messages.groups_all_categories') }}
        </a>
        @foreach($categories as $catKey => $catLabelKey)
            <a href="{{ route('groups.index', array_merge(request()->except(['category', 'page']), ['category' => $catKey])) }}" 
               class="groups-hub__cat-pill {{ request('category') === $catKey ? 'is-active' : '' }}">
                {{ __($catLabelKey) }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-3-6-3 mobile-prefer-content">
        <div class="grid-column">
            <x-widget-column side="groups_left" />
        </div>

        <div class="grid-column">
            @auth
                @if($myGroups->isNotEmpty() && (!request('search') && (!request('category') || request('category') === 'all')))
                    <div style="margin-bottom: 28px;">
                        <h2 style="font-size: 1.2rem; font-weight: 900; margin-bottom: 16px; color: var(--groups-text);">
                            <i class="fa fa-bookmark text-primary me-2" aria-hidden="true"></i> {{ __('messages.groups_my_groups') }}
                        </h2>
                        <div class="groups-hub__cards" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
                            @foreach($myGroups as $group)
                                <div class="groups-card">
                                    <div class="groups-card__cover" style="background-image: url('{{ $group->coverUrl() }}');">
                                        <div class="groups-card__cover-overlay"></div>
                                        <div class="groups-card__chips">
                                            @if($group->is_featured)
                                                <span class="groups-card__chip groups-card__chip--featured"><i class="fa fa-star me-1"></i> {{ __('messages.groups_featured') }}</span>
                                            @endif
                                            @if($group->join_fee_pts > 0)
                                                <span class="groups-card__chip groups-card__chip--pts"><i class="fa fa-coins me-1"></i> {{ $group->join_fee_pts }} PTS</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="groups-card__body">
                                        <div class="groups-card__avatar-wrap">
                                            <img class="groups-card__avatar" src="{{ $group->avatarUrl() }}" alt="{{ $group->name }}">
                                            <span class="groups-card__category">{{ __(\App\Models\Group::CATEGORIES[$group->category] ?? 'messages.groups_cat_general') }}</span>
                                        </div>
                                        <h3 class="groups-card__title">
                                            <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                        </h3>
                                        <p class="groups-card__desc">
                                            {{ $group->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $group->description), 90) }}
                                        </p>
                                        <div class="groups-card__meta">
                                            <span class="groups-card__stat"><i class="fa fa-users"></i> {{ number_format($group->members_count) }}</span>
                                            <span class="groups-card__stat"><i class="fa fa-comments"></i> {{ number_format($group->posts_count) }}</span>
                                        </div>
                                    </div>
                                    <div class="groups-card__footer">
                                        <a href="{{ route('groups.show', $group) }}" class="groups-card__btn">
                                            {{ __('messages.groups_open_group') }} <i class="fa fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth

            <div style="margin-bottom: 24px;">
                <h2 style="font-size: 1.25rem; font-weight: 900; margin-bottom: 18px; color: var(--groups-text);">
                    <i class="fa fa-globe text-primary me-2" aria-hidden="true"></i>
                    {{ $search !== '' ? __('messages.groups_search_results') : __('messages.groups_discover_groups') }}
                </h2>

                @if($groups->count() > 0)
                    <div class="groups-hub__cards">
                        @foreach($groups as $group)
                            <div class="groups-card">
                                <div class="groups-card__cover" style="background-image: url('{{ $group->coverUrl() }}');">
                                    <div class="groups-card__cover-overlay"></div>
                                    <div class="groups-card__chips">
                                        <span class="groups-card__chip groups-card__chip--privacy">
                                            <i class="fa {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? 'fa-globe' : 'fa-lock' }} me-1"></i>
                                            {{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}
                                        </span>
                                        @if($group->join_fee_pts > 0)
                                            <span class="groups-card__chip groups-card__chip--pts"><i class="fa fa-coins me-1"></i> {{ $group->join_fee_pts }} PTS</span>
                                        @endif
                                        @if($group->is_featured)
                                            <span class="groups-card__chip groups-card__chip--featured"><i class="fa fa-star me-1"></i> {{ __('messages.groups_featured') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="groups-card__body">
                                    <div class="groups-card__avatar-wrap">
                                        <img class="groups-card__avatar" src="{{ $group->avatarUrl() }}" alt="{{ $group->name }}">
                                        <span class="groups-card__category">{{ __(\App\Models\Group::CATEGORIES[$group->category] ?? 'messages.groups_cat_general') }}</span>
                                    </div>
                                    <h3 class="groups-card__title">
                                        <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                    </h3>
                                    <p class="groups-card__desc">
                                        {{ $group->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $group->description), 110) }}
                                    </p>
                                    <div class="groups-card__meta">
                                        <span class="groups-card__stat"><i class="fa fa-users"></i> {{ number_format($group->members_count) }}</span>
                                        <span class="groups-card__stat"><i class="fa fa-comments"></i> {{ number_format($group->posts_count) }}</span>
                                        @if(!empty($group->active_followed_members_count))
                                            <span class="groups-card__stat"><i class="fa fa-user-friends"></i> {{ $group->active_followed_members_count }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="groups-card__footer">
                                    <a href="{{ route('groups.show', $group) }}" class="groups-card__btn">
                                        {{ __('messages.groups_open_group') }} <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 24px;">
                        {{ $groups->links() }}
                    </div>
                @else
                    <div class="widget-box text-center py-5">
                        <div class="widget-box-content">
                            <i class="fa fa-users-slash text-muted" style="font-size: 3rem; margin-bottom: 16px;"></i>
                            <p style="font-size: 1.05rem; font-weight: 700; color: var(--groups-text); margin: 0;">
                                {{ __('messages.groups_empty_state') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid-column">
            <x-widget-column side="groups_right" />
        </div>
    </div>
</div>
@endsection
