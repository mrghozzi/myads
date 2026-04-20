@extends('theme::layouts.master')

@push('head')
<style>
    .groups-hub {
        --groups-card-bg: #ffffff;
        --groups-card-soft: #f6f8ff;
        --groups-border: #e8ebf5;
        --groups-text: #2f3142;
        --groups-muted: #8f91ac;
        --groups-accent: #ff6b3d;
        --groups-accent-soft: rgba(255, 107, 61, 0.12);
        --groups-shadow: 0 18px 36px rgba(47, 49, 66, 0.08);
    }

    body[data-theme="css_d"] .groups-hub {
        --groups-card-bg: #1f2436;
        --groups-card-soft: #242b3f;
        --groups-border: #313951;
        --groups-text: #f5f7ff;
        --groups-muted: #9aa4bf;
        --groups-accent: #4ff461;
        --groups-accent-soft: rgba(79, 244, 97, 0.14);
        --groups-shadow: 0 18px 36px rgba(0, 0, 0, 0.28);
    }

    .groups-hub__hero,
    .groups-hub__card {
        border-radius: 24px;
        border: 1px solid var(--groups-border);
        background: linear-gradient(180deg, var(--groups-card-bg) 0%, var(--groups-card-soft) 100%);
        box-shadow: var(--groups-shadow);
    }

    .groups-hub__hero {
        padding: 28px;
        margin-bottom: 24px;
    }

    .groups-hub__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: var(--groups-accent-soft);
        color: var(--groups-accent);
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .groups-hub__title {
        margin: 18px 0 10px;
        color: var(--groups-text);
        font-size: 2rem;
        line-height: 1.2;
    }

    .groups-hub__subtitle {
        margin: 0;
        color: var(--groups-muted);
        font-size: 1rem;
        line-height: 1.8;
    }

    .groups-hub__toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        margin-top: 22px;
    }

    .groups-hub__search,
    .groups-hub__button {
        min-height: 52px;
        border-radius: 16px;
    }

    .groups-hub__search {
        width: 100%;
        padding: 0 16px;
        border: 1px solid var(--groups-border);
        background: var(--groups-card-bg);
        color: var(--groups-text);
    }

    .groups-hub__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 0 18px;
        border: 0;
        background: var(--groups-accent);
        color: #fff;
        font-weight: 700;
        text-decoration: none;
    }

    .groups-hub__section {
        margin-bottom: 24px;
    }

    .groups-hub__section-title {
        margin: 0 0 14px;
        color: var(--groups-text);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .groups-hub__cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }

    .groups-hub__card {
        padding: 22px;
    }

    .groups-hub__card-top,
    .groups-hub__card-meta,
    .groups-hub__card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .groups-hub__chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(97, 93, 250, 0.1);
        color: #615dfa;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .groups-hub__chip--accent {
        background: var(--groups-accent-soft);
        color: var(--groups-accent);
    }

    .groups-hub__card-title {
        margin: 16px 0 8px;
        color: var(--groups-text);
        font-size: 1.12rem;
        font-weight: 800;
    }

    .groups-hub__card-title a {
        color: inherit;
        text-decoration: none;
    }

    .groups-hub__card-text {
        margin: 0 0 16px;
        color: var(--groups-muted);
        line-height: 1.75;
    }

    .groups-hub__stat {
        color: var(--groups-muted);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .groups-hub__card-footer a {
        color: var(--groups-accent);
        font-weight: 800;
        text-decoration: none;
    }

    @media (max-width: 680px) {
        .groups-hub__toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="groups-hub">
    <div class="section-banner" style="background: linear-gradient(135deg, rgba(255,107,61,0.95), rgba(35,210,226,0.92));">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/groups-icon.png') }}" alt="groups-icon" onerror="this.style.display='none'">
        <p class="section-banner-title">{{ __('messages.groups_title') }}</p>
        <p class="section-banner-text">{{ __('messages.groups_discover_description') }}</p>
    </div>

    <div class="grid grid-3-6-3 mobile-prefer-content">
        <div class="grid-column">
            <x-widget-column side="groups_left" />
        </div>

        <div class="grid-column">
            <section class="groups-hub__hero">
                <span class="groups-hub__eyebrow"><i class="fa fa-users" aria-hidden="true"></i>{{ __('messages.groups_title') }}</span>
                <h1 class="groups-hub__title">{{ __('messages.groups_find_your_corner') }}</h1>
                <p class="groups-hub__subtitle">{{ __('messages.groups_index_intro') }}</p>

                <form method="GET" action="{{ route('groups.index') }}" class="groups-hub__toolbar">
                    <input class="groups-hub__search" type="search" name="search" value="{{ $search }}" placeholder="{{ __('messages.groups_search_placeholder') }}">
                    @auth
                        @if($creationEligibility && $creationEligibility['allowed'])
                            <a class="groups-hub__button" href="{{ route('groups.create') }}">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                <span>{{ __('messages.groups_create_title') }}</span>
                            </a>
                        @else
                            <button class="groups-hub__button" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <span>{{ __('messages.search') }}</span>
                            </button>
                        @endif
                    @else
                        <button class="groups-hub__button" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <span>{{ __('messages.search') }}</span>
                        </button>
                    @endauth
                </form>
            </section>

            @auth
                @if($myGroups->isNotEmpty())
                    <section class="groups-hub__section">
                        <h2 class="groups-hub__section-title">{{ __('messages.groups_my_groups') }}</h2>
                        <div class="groups-hub__cards">
                            @foreach($myGroups as $group)
                                <article class="groups-hub__card">
                                    <div class="groups-hub__card-top">
                                        <span class="groups-hub__chip"><i class="fa fa-user-circle" aria-hidden="true"></i>{{ $group->owner?->username ?? __('messages.unknown_user') }}</span>
                                        @if($group->is_featured)
                                            <span class="groups-hub__chip groups-hub__chip--accent">{{ __('messages.groups_featured') }}</span>
                                        @endif
                                    </div>
                                    <h3 class="groups-hub__card-title"><a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a></h3>
                                    <p class="groups-hub__card-text">{{ $group->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $group->description), 120) }}</p>
                                    <div class="groups-hub__card-meta">
                                        <span class="groups-hub__stat">{{ $group->members_count }} {{ __('messages.members') }}</span>
                                        <span class="groups-hub__stat">{{ $group->posts_count }} {{ __('messages.posts') }}</span>
                                    </div>
                                    <div class="groups-hub__card-footer">
                                        <span class="groups-hub__stat">{{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}</span>
                                        <a href="{{ route('groups.show', $group) }}">{{ __('messages.groups_open_group') }}</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endauth

            <section class="groups-hub__section">
                <h2 class="groups-hub__section-title">{{ $search !== '' ? __('messages.groups_search_results') : __('messages.groups_discover_groups') }}</h2>

                @if($groups->count() > 0)
                    <div class="groups-hub__cards">
                        @foreach($groups as $group)
                            <article class="groups-hub__card">
                                <div class="groups-hub__card-top">
                                    <span class="groups-hub__chip">{{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}</span>
                                    @if($group->is_featured)
                                        <span class="groups-hub__chip groups-hub__chip--accent">{{ __('messages.groups_featured') }}</span>
                                    @endif
                                </div>
                                <h3 class="groups-hub__card-title"><a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a></h3>
                                <p class="groups-hub__card-text">{{ $group->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $group->description), 120) }}</p>
                                <div class="groups-hub__card-meta">
                                    <span class="groups-hub__stat">{{ $group->members_count }} {{ __('messages.members') }}</span>
                                    <span class="groups-hub__stat">{{ $group->posts_count }} {{ __('messages.posts') }}</span>
                                    @if(!empty($group->active_followed_members_count))
                                        <span class="groups-hub__stat">{{ $group->active_followed_members_count }} {{ __('messages.groups_followed_members_inside') }}</span>
                                    @endif
                                </div>
                                <div class="groups-hub__card-footer">
                                    <span class="groups-hub__stat">{{ $group->owner?->username ?? __('messages.unknown_user') }}</span>
                                    <a href="{{ route('groups.show', $group) }}">{{ __('messages.groups_open_group') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div style="margin-top: 18px;">
                        {{ $groups->links() }}
                    </div>
                @else
                    <div class="widget-box">
                        <div class="widget-box-content">
                            <p style="margin: 0; text-align: center;">{{ __('messages.groups_empty_state') }}</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <div class="grid-column">
            <x-widget-column side="groups_right" />
        </div>
    </div>
</div>
@endsection
