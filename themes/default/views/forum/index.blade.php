@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<main class="forum-modern-forum-wrapper forum-rdx-index">
    <!-- Hero Banner with Stats -->
    <section class="forum-modern-hero">
        <div class="forum-modern-hero-copy">
            <h1 class="forum-modern-hero-title">
                <i class="fa fa-comments text-primary"></i>
                <span>{{ __('messages.forum') ?? 'Community Discussions' }}</span>
            </h1>
            <p class="forum-modern-hero-desc">
                {{ __('messages.forum_index_intro') ?? 'Browse discussions, participate with webmasters, and share knowledge across digital channels.' }}
            </p>
        </div>
        <div class="forum-modern-hero-actions">
            @auth
                <button type="button" class="forum-modern-start-btn" onclick="ForumComposer.open('topic')">
                    <i class="fa fa-pencil"></i>
                    <span>{{ __('messages.w_new_tpc') ?? 'Start a Discussion' }}</span>
                </button>
            @else
                <a href="{{ route('login') }}" class="forum-modern-start-btn">
                    <i class="fa fa-sign-in"></i>
                    <span>{{ __('messages.login_to_participate') ?? 'Login to Participate' }}</span>
                </a>
            @endauth
        </div>
    </section>

    <!-- Two-Pane Forum Layout -->
    <div class="forum-modern-layout">
        <!-- Left Sidebar: Tags / Categories Navigation -->
        <aside class="forum-modern-sidebar">
            @auth
                <button type="button" class="forum-modern-start-btn d-none d-lg-flex" onclick="ForumComposer.open('topic')">
                    <i class="fa fa-plus"></i>
                    <span>{{ __('messages.w_new_tpc') ?? 'Start a Discussion' }}</span>
                </button>
            @endauth

            <div class="forum-modern-nav-card">
                <div class="forum-modern-nav-title">{{ __('messages.categories') ?? 'Tags & Categories' }}</div>
                <ul class="forum-modern-nav-list">
                    <li class="forum-modern-nav-item {{ !isset($category) ? 'is-active' : '' }}">
                        <a href="{{ route('forum.index') }}">
                            <span class="forum-modern-tag-badge">
                                <i class="fa fa-comments text-primary"></i>
                                <span>{{ __('messages.all_discussions') ?? 'All Discussions' }}</span>
                            </span>
                            <span class="forum-modern-nav-count">{{ number_format($forumStats['topics'] ?? 0) }}</span>
                        </a>
                    </li>
                    @foreach($categories as $cat)
                        @php
                            $catColor = $cat->color ?: '#615dfa';
                            $isActive = isset($category) && $category->id == $cat->id;
                            $hasChildren = $cat->children && $cat->children->isNotEmpty();
                        @endphp
                        <li class="forum-modern-nav-item {{ $isActive ? 'is-active' : '' }} {{ $hasChildren ? 'has-subforums' : '' }}">
                            <a href="{{ route('forum.category', $cat->id) }}" data-cat-id="{{ $cat->id }}">
                                <span class="forum-modern-tag-badge">
                                    <span class="forum-modern-tag-bullet" style="background-color: {{ $catColor }};"></span>
                                    <span>{{ $cat->name }}</span>
                                </span>
                                @if(isset($cat->topic_count))
                                    <span class="forum-modern-nav-count">{{ number_format($cat->topic_count) }}</span>
                                @endif
                            </a>
                            @if($hasChildren)
                                <ul class="forum-modern-subcat-list">
                                    @foreach($cat->children as $sub)
                                        @php
                                            $subColor = $sub->color ?: $catColor;
                                            $isSubActive = isset($category) && $category->id == $sub->id;
                                        @endphp
                                        <li class="forum-modern-subcat-item {{ $isSubActive ? 'is-active' : '' }}">
                                            <a href="{{ route('forum.category', $sub->id) }}" data-cat-id="{{ $sub->id }}">
                                                <span class="forum-modern-tag-badge">
                                                    <span class="forum-modern-tag-bullet" style="background-color: {{ $subColor }};"></span>
                                                    <span>{{ $sub->name }}</span>
                                                </span>
                                                @if(isset($sub->topic_count))
                                                    <span class="forum-modern-nav-count">{{ number_format($sub->topic_count) }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Quick Forum Stats -->
            <div class="forum-modern-nav-card">
                <div class="forum-modern-nav-title">{{ __('messages.forum_statistics') ?? 'Community Stats' }}</div>
                <div class="p-2 d-flex flex-column gap-2 fs-13 text-muted">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-folder-open me-2 text-primary"></i>{{ __('messages.cat_s') }}</span>
                        <strong class="text-dark">{{ $categories->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-comment me-2 text-info"></i>{{ __('messages.topics') }}</span>
                        <strong class="text-dark">{{ number_format($forumStats['topics'] ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-users me-2 text-success"></i>{{ __('messages.members') }}</span>
                        <strong class="text-dark">{{ number_format($forumStats['members'] ?? 0) }}</strong>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Pane: Discussion Stream & Filter Toolbar -->
        <section class="forum-modern-stream-container">
            <!-- Filter Toolbar -->
            <div class="forum-modern-toolbar">
                <div class="forum-modern-filter-tabs">
                    <a href="?filter=latest" class="forum-modern-filter-btn {{ ($currentFilter ?? 'latest') === 'latest' ? 'is-active' : '' }}" data-filter="latest">
                        <i class="fa-regular fa-clock fa-clock-o"></i>
                        <span>{{ __('messages.filter_latest') ?? 'Latest Activity' }}</span>
                    </a>
                    <a href="?filter=top" class="forum-modern-filter-btn {{ ($currentFilter ?? '') === 'top' ? 'is-active' : '' }}" data-filter="top">
                        <i class="fa fa-fire"></i>
                        <span>{{ __('messages.filter_top') ?? 'Top' }}</span>
                    </a>
                    <a href="?filter=newest" class="forum-modern-filter-btn {{ ($currentFilter ?? '') === 'newest' ? 'is-active' : '' }}" data-filter="newest">
                        <i class="fa fa-calendar"></i>
                        <span>{{ __('messages.filter_newest') ?? 'Newest' }}</span>
                    </a>
                    <a href="?filter=unanswered" class="forum-modern-filter-btn {{ ($currentFilter ?? '') === 'unanswered' ? 'is-active' : '' }}" data-filter="unanswered">
                        <i class="fa fa-comment-slash"></i>
                        <span>{{ __('messages.filter_unanswered') ?? 'Unanswered' }}</span>
                    </a>
                </div>

                <div class="forum-modern-search-box">
                    <i class="fa fa-search forum-modern-search-icon"></i>
                    <input type="search" id="forumSearchInput" class="forum-modern-search-input" placeholder="{{ __('messages.search_forum') ?? 'Search discussions...' }}" autocomplete="off">
                </div>
            </div>

            <!-- Discussion Stream Content -->
            <div id="forumStreamWrapper">
                @include('theme::forum.partials.discussion_stream', ['topics' => $topics])
            </div>
        </section>
    </div>

    <!-- Floating Forum Composer Drawer -->
    @include('theme::forum.partials.discussion_composer', ['categories' => $categories])
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilter = '{{ $currentFilter ?? "latest" }}';
    let currentCatId = null;
    let searchTimer = null;

    // Filter switching via Ajax
    document.querySelectorAll('.forum-modern-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.forum-modern-filter-btn').forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');
            currentFilter = this.dataset.filter;
            fetchDiscussions();
        });
    });

    // Live search debounced
    const searchInput = document.getElementById('forumSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchDiscussions();
            }, 300);
        });
    }

    function fetchDiscussions() {
        const streamWrapper = document.getElementById('forumStreamWrapper');
        streamWrapper.style.opacity = '0.5';

        const query = searchInput ? searchInput.value.trim() : '';
        const params = new URLSearchParams();
        if (currentFilter) params.set('filter', currentFilter);
        if (query) params.set('q', query);

        const url = window.location.pathname + '?' + params.toString();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            streamWrapper.innerHTML = data.html;
            streamWrapper.style.opacity = '1';
            window.history.pushState({}, '', url);
            if (typeof initHexagons === 'function') {
                initHexagons(streamWrapper);
            }
            bindLoadMore();
        })
        .catch(err => {
            streamWrapper.style.opacity = '1';
            console.error('Failed to load discussions:', err);
        });
    }

    function bindLoadMore() {
        const loadMoreBtn = document.getElementById('forumLoadMoreBtn');
        if (!loadMoreBtn) return;

        loadMoreBtn.addEventListener('click', function() {
            const nextUrl = this.dataset.nextUrl;
            if (!nextUrl) return;

            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Loading...';

            fetch(nextUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                const newItems = tempDiv.querySelectorAll('.forum-modern-discussion-card');
                const list = document.getElementById('forumDiscussionList');
                newItems.forEach(item => list.appendChild(item));

                if (typeof initHexagons === 'function') {
                    initHexagons(list);
                }

                if (data.has_more) {
                    loadMoreBtn.dataset.nextUrl = data.next_page_url;
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = '<i class="fa fa-chevron-down me-1"></i> Load More Discussions';
                } else {
                    loadMoreBtn.remove();
                }
            })
            .catch(err => {
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Try Again';
            });
        });
    }

    if (typeof initHexagons === 'function') {
        initHexagons(document.getElementById('forumStreamWrapper'));
    }

    bindLoadMore();
});
</script>
@endsection
