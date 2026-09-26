@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<main class="forum-rdx forum-rdx-index forum-index-page">
    <section class="forum-index-hero" aria-labelledby="forum-index-title">
        <div class="forum-index-hero-copy">
            <span class="forum-index-eyebrow"><i class="fa fa-comments" aria-hidden="true"></i> {{ __('messages.community') }}</span>
            <h1 id="forum-index-title">{{ __('messages.forum') }}</h1>
            <p>{{ __('messages.forum_index_intro') }}</p>
        </div>
        @auth
            <a href="{{ route('forum.create') }}" class="forum-index-primary-action">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>{{ __('messages.w_new_tpc') }}</span>
            </a>
        @endauth
        <div class="forum-index-summary" aria-label="{{ __('messages.forum_statistics') }}">
            <div class="forum-index-summary-item">
                <i class="fa fa-folder-open" aria-hidden="true"></i>
                <span><strong>{{ $categories->count() }}</strong><small>{{ __('messages.cat_s') }}</small></span>
            </div>
            <div class="forum-index-summary-item">
                <i class="fa fa-comments" aria-hidden="true"></i>
                <span><strong>{{ number_format($forumStats['topics']) }}</strong><small>{{ __('messages.topics') }}</small></span>
            </div>
            <div class="forum-index-summary-item">
                <i class="fa fa-users" aria-hidden="true"></i>
                <span><strong>{{ number_format($forumStats['members']) }}</strong><small>{{ __('messages.members') }}</small></span>
            </div>
        </div>
    </section>

    @include('theme::partials.ads', ['id' => 4])

    <section class="forum-index-toolbar" aria-label="{{ __('messages.forum_categories') }}">
        <div>
            <span class="forum-index-eyebrow">{{ __('messages.explore') }}</span>
            <h2>{{ __('messages.forum_categories') }}</h2>
        </div>
        <label class="forum-index-search">
            <i class="fa fa-search" aria-hidden="true"></i>
            <span class="sr-only">{{ __('messages.search_categories') }}</span>
            <input type="search" data-forum-category-search placeholder="{{ __('messages.search_categories') }}" autocomplete="off">
        </label>
    </section>

    @if($categories->isNotEmpty())
        <div class="forum-index-category-grid" data-forum-category-list>
            @foreach($categories as $category)
                @php
                    $latestActivity = $category->latest_status_date
                        ? \Carbon\Carbon::createFromTimestamp((int) $category->latest_status_date)->diffForHumans()
                        : null;
                @endphp
                <article class="forum-index-category-card" data-forum-category-item data-search-text="{{ \Illuminate\Support\Str::lower($category->name . ' ' . strip_tags((string) $category->txt)) }}">
                    <div class="forum-index-category-heading">
                        <a class="forum-index-category-icon" href="{{ route('forum.category', $category->id) }}" aria-label="{{ $category->name }}">
                            <i class="fa {{ $category->icons ?: 'fa-comments' }}" aria-hidden="true"></i>
                        </a>
                        <div class="forum-index-category-title-wrap">
                            <h3><a href="{{ route('forum.category', $category->id) }}">{{ $category->name }}</a></h3>
                            <p>{{ trim(strip_tags((string) $category->txt)) ?: __('messages.forum_category_description_fallback') }}</p>
                        </div>
                        <a class="forum-index-open" href="{{ route('forum.category', $category->id) }}" aria-label="{{ __('messages.open_category', ['category' => $category->name]) }}">
                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="forum-index-category-metrics">
                        <span><strong>{{ number_format($category->topic_count) }}</strong> {{ __('messages.topics') }}</span>
                        <span><strong>{{ number_format($category->reply_count) }}</strong> {{ __('messages.replies') }}</span>
                    </div>

                    <div class="forum-index-latest">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                        @if($category->latest_topic_id)
                            <a href="{{ route('forum.topic', $category->latest_topic_id) }}">{{ $category->latest_topic_name }}</a>
                            <time>{{ $latestActivity }}</time>
                        @else
                            <span>{{ __('messages.forum_no_activity') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <p class="forum-index-empty-search" data-forum-category-empty hidden>{{ __('messages.forum_search_no_results') }}</p>
    @else
        <div class="forum-index-empty" role="status">
            <i class="fa fa-comments-o" aria-hidden="true"></i>
            <h2>{{ __('messages.forum_no_categories') }}</h2>
            <p>{{ __('messages.forum_no_categories_desc') }}</p>
        </div>
    @endif

    @if($forumStats['latest_member'])
        <aside class="forum-index-new-member">
            <span class="forum-index-new-member-icon"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
            <div>
                <span>{{ __('messages.forum_newest_member') }}</span>
                <a href="{{ route('profile.show', $forumStats['latest_member']->username) }}">{{ $forumStats['latest_member']->username }}</a>
            </div>
        </aside>
    @endif
</main>
@endsection
