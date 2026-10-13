@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<main class="forum-modern-forum-wrapper forum-category-board forum-rdx-discussion-shell">
    <!-- Category Hero Banner -->
    @php
        $catColor = $category->color ?: '#615dfa';
    @endphp
    <section class="forum-modern-hero" style="border-inline-start: 6px solid {{ $catColor }};">
        <div class="forum-modern-hero-copy">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="forum-modern-tag-bullet" style="background-color: {{ $catColor }}; width: 14px; height: 14px;"></span>
                <span class="text-uppercase fw-bold fs-12 text-muted" style="letter-spacing: 0.5px;">{{ __('messages.category') ?? 'Category' }}</span>
            </div>
            <h1 class="forum-modern-hero-title">
                <span>{{ $category->name }}</span>
            </h1>
            @if($category->txt)
                <p class="forum-modern-hero-desc">
                    {{ $category->txt }}
                </p>
            @endif
        </div>
        <div class="forum-modern-hero-actions">
            @auth
                <button type="button" class="forum-modern-start-btn" onclick="ForumComposer.open('topic', { catId: {{ $category->id }} })">
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
                <button type="button" class="forum-modern-start-btn d-none d-lg-flex" onclick="ForumComposer.open('topic', { catId: {{ $category->id }} })">
                    <i class="fa fa-plus"></i>
                    <span>{{ __('messages.w_new_tpc') ?? 'Start a Discussion' }}</span>
                </button>
            @endauth

            <div class="forum-modern-nav-card">
                <div class="forum-modern-nav-title">{{ __('messages.categories') ?? 'Tags & Categories' }}</div>
                <ul class="forum-modern-nav-list">
                    <li class="forum-modern-nav-item">
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
                            $cColor = $cat->color ?: '#615dfa';
                            $isActive = $category->id == $cat->id;
                            $hasChildren = $cat->children && $cat->children->isNotEmpty();
                            $isChildActive = $hasChildren && $cat->children->contains('id', $category->id);
                        @endphp
                        <li class="forum-category-board-item {{ $isActive ? 'is-active' : '' }} {{ $isChildActive ? 'has-active-child' : '' }}" data-forum-category-id="{{ $cat->id }}" data-topic-count="{{ $cat->topic_count ?? 0 }}" {!! $isActive ? 'aria-current="page"' : '' !!}>
                            <a href="{{ route('forum.category', $cat->id) }}" data-cat-id="{{ $cat->id }}">
                                <span class="forum-modern-tag-badge">
                                    <span class="forum-modern-tag-bullet" style="background-color: {{ $cColor }};"></span>
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
                                            $subColor = $sub->color ?: $cColor;
                                            $isSubActive = $category->id == $sub->id;
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

            <x-widget-column side="forum_left" />
        </aside>

        <!-- Right Pane: Discussion Stream & Filter Toolbar -->
        <section class="forum-modern-stream-container">
            @if($category->children && $category->children->isNotEmpty())
            <div class="forum-modern-subforums-panel mb-4">
                <div class="forum-modern-subforums-header d-flex align-items-center justify-content-between mb-3">
                    <h3 class="forum-modern-subforums-title fs-15 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa fa-folder-open text-primary"></i>
                        <span>{{ __('messages.sub_forums') ?? 'Sub Forums' }}</span>
                    </h3>
                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-12 fw-bold">
                        {{ $category->children->count() }}
                    </span>
                </div>
                <div class="forum-modern-subforums-grid">
                    @foreach($category->children as $sub)
                        @php
                            $sColor = $sub->color ?: $catColor;
                        @endphp
                        <a href="{{ route('forum.category', $sub->id) }}" class="forum-modern-subforum-card">
                            <div class="forum-modern-subforum-badge" style="background-color: {{ $sColor }};">
                                <i class="fa {{ $sub->icons ?: 'fa-comments' }}"></i>
                            </div>
                            <div class="forum-modern-subforum-body">
                                <div class="forum-modern-subforum-title">{{ $sub->name }}</div>
                                @if($sub->txt)
                                    <div class="forum-modern-subforum-desc">{{ Str::limit($sub->txt, 60) }}</div>
                                @endif
                                <div class="forum-modern-subforum-meta">
                                    <span><i class="fa fa-comments me-1 opacity-75"></i> {{ number_format($sub->topic_count ?? 0) }} {{ __('messages.topics') ?? 'Topics' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
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
                    <input type="search" id="forumSearchInput" class="forum-modern-search-input" placeholder="{{ __('messages.search_forum') ?? 'Search in this category...' }}" autocomplete="off">
                </div>
            </div>

            <!-- Discussion Stream Content -->
            <div id="forumStreamWrapper">
                @include('theme::forum.partials.discussion_stream', ['topics' => $topics])
            </div>
        </section>
    </div>

    <!-- Floating Forum Composer Drawer -->
    @include('theme::forum.partials.discussion_composer', ['categories' => $categories, 'selectedCatId' => $category->id])
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilter = '{{ $currentFilter ?? "latest" }}';
    let currentCatId = {{ $category->id }};
    let searchTimer = null;

    function reloadStream(page = 1, append = false) {
        const streamWrapper = document.getElementById('forumStreamWrapper');
        const q = document.getElementById('forumSearchInput').value.trim();
        const url = new URL(window.location.href);
        url.searchParams.set('filter', currentFilter);
        if (q) url.searchParams.set('q', q);
        else url.searchParams.delete('q');
        if (page > 1) url.searchParams.set('page', page);
        else url.searchParams.delete('page');

        if (!append) {
            streamWrapper.style.opacity = '0.5';
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (append) {
                const list = streamWrapper.querySelector('.forum-modern-stream-list');
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                const newItems = temp.querySelectorAll('.forum-modern-discussion-item, .forum-modern-discussion-card');
                newItems.forEach(item => list.appendChild(item));

                if (typeof initHexagons === 'function') {
                    initHexagons(list);
                }

                const loadMoreBtn = streamWrapper.querySelector('.forum-modern-load-more-btn');
                if (loadMoreBtn) {
                    if (data.has_more) {
                        loadMoreBtn.setAttribute('data-next-page', data.current_page + 1);
                        loadMoreBtn.innerHTML = '<i class="fa fa-refresh"></i> <span>{{ __("messages.load_more") ?? "Load More Discussions" }}</span>';
                        loadMoreBtn.disabled = false;
                    } else {
                        loadMoreBtn.remove();
                    }
                }
            } else {
                streamWrapper.innerHTML = data.html;
                streamWrapper.style.opacity = '1';
                window.history.replaceState({}, '', url.toString());
                if (typeof initHexagons === 'function') {
                    initHexagons(streamWrapper);
                }
            }
        })
        .catch(err => {
            console.error('Failed to load stream', err);
            streamWrapper.style.opacity = '1';
        });
    }

    // Filter Buttons click handler
    document.querySelectorAll('.forum-modern-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.forum-modern-filter-btn').forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');
            currentFilter = this.getAttribute('data-filter');
            reloadStream(1, false);
        });
    });

    // Search Input debouncing
    const searchInput = document.getElementById('forumSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                reloadStream(1, false);
            }, 350);
        });
    }

    // Load More click delegation
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.forum-modern-load-more-btn');
        if (btn) {
            e.preventDefault();
            const nextPage = parseInt(btn.getAttribute('data-next-page') || '2', 10);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>{{ __("messages.loading") ?? "Loading..." }}</span>';
            reloadStream(nextPage, true);
        }
    });

    if (typeof initHexagons === 'function') {
        initHexagons(document.getElementById('forumStreamWrapper'));
    }
});
</script>
@endsection
