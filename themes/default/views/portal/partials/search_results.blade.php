@php
    $statusesCount = isset($searchedStatuses) ? $searchedStatuses->count() : 0;
    $usersCount = isset($searchedUsers) ? $searchedUsers->count() : 0;
    $groupsCount = isset($searchedGroups) ? $searchedGroups->count() : 0;
    $productsCount = isset($searchedProducts) ? $searchedProducts->count() : 0;
    $forumCommentsCount = isset($searchedCommentsForum) ? $searchedCommentsForum->count() : 0;
    $dirCommentsCount = isset($searchedCommentsDir) ? $searchedCommentsDir->count() : 0;
    $commentsCount = $forumCommentsCount + $dirCommentsCount;
    $totalCount = $statusesCount + $usersCount + $groupsCount + $productsCount + $commentsCount;
@endphp

<div class="portal-search-wrapper" data-search-query="{{ $search }}">
    <!-- Search Results Header Bar -->
    <div class="portal-search-bar-header">
        <div class="portal-search-meta">
            <span class="portal-search-badge">
                <i class="fas fa-search"></i> {{ __('messages.portal_search_results') }}
            </span>
            <h2 class="portal-search-title">
                "{{ $search }}"
                <span class="portal-search-count">({{ $totalCount }})</span>
            </h2>
        </div>
        <button type="button" class="portal-clear-btn" id="portal-clear-search-btn" title="{{ __('messages.portal_clear_search') }}">
            <i class="fas fa-times"></i>
            <span>{{ __('messages.portal_clear_search') }}</span>
        </button>
    </div>

    @if($totalCount > 0)
        <!-- Search Category Filter Tabs -->
        <div class="portal-search-nav">
            <button type="button" class="portal-search-nav-btn active" data-search-filter="all">
                <i class="fas fa-layer-group"></i> {{ __('messages.portal_all_results') }}
                <span class="nav-count">{{ $totalCount }}</span>
            </button>
            @if($statusesCount > 0)
                <button type="button" class="portal-search-nav-btn" data-search-filter="statuses">
                    <i class="fas fa-stream"></i> {{ __('messages.portal_stats_posts') }}
                    <span class="nav-count">{{ $statusesCount }}</span>
                </button>
            @endif
            @if($usersCount > 0)
                <button type="button" class="portal-search-nav-btn" data-search-filter="users">
                    <i class="fas fa-user-friends"></i> {{ __('messages.portal_stats_members') }}
                    <span class="nav-count">{{ $usersCount }}</span>
                </button>
            @endif
            @if($groupsCount > 0)
                <button type="button" class="portal-search-nav-btn" data-search-filter="groups">
                    <i class="fas fa-users"></i> {{ __('messages.portal_stats_groups') }}
                    <span class="nav-count">{{ $groupsCount }}</span>
                </button>
            @endif
            @if($productsCount > 0)
                <button type="button" class="portal-search-nav-btn" data-search-filter="products">
                    <i class="fas fa-shopping-bag"></i> {{ __('messages.portal_stats_products') }}
                    <span class="nav-count">{{ $productsCount }}</span>
                </button>
            @endif
            @if($commentsCount > 0)
                <button type="button" class="portal-search-nav-btn" data-search-filter="comments">
                    <i class="fas fa-comments"></i> {{ __('messages.portal_stats_comments') }}
                    <span class="nav-count">{{ $commentsCount }}</span>
                </button>
            @endif
        </div>

        <!-- Section: Activities / Posts -->
        @if($statusesCount > 0)
            <div class="portal-search-section" data-section="statuses">
                <div class="portal-search-section-header">
                    <h3 class="portal-section-heading">
                        <i class="fas fa-stream"></i> {{ __('messages.activities') }}
                    </h3>
                    <span class="portal-section-pill">{{ $statusesCount }}</span>
                </div>
                <div class="portal-activity-grid">
                    @foreach($searchedStatuses as $activity)
                        @include('theme::partials.activity.render', ['activity' => $activity])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section: Users / Members -->
        @if($usersCount > 0)
            <div class="portal-search-section" data-section="users">
                <div class="portal-search-section-header">
                    <h3 class="portal-section-heading">
                        <i class="fas fa-user-friends"></i> {{ __('messages.members') }}
                    </h3>
                    <span class="portal-section-pill">{{ $usersCount }}</span>
                </div>
                <div class="portal-users-grid">
                    @foreach($searchedUsers as $sUser)
                        <div class="portal-user-card">
                            <div class="portal-user-cover" style="background-image: url('{{ asset('themes/default/assets/img/cover/01.jpg') }}');">
                                <span class="portal-user-status-dot {{ $sUser->isOnline() ? 'online' : 'offline' }}" title="{{ $sUser->isOnline() ? 'Online' : 'Offline' }}"></span>
                            </div>
                            <div class="portal-user-body">
                                <div class="portal-user-avatar-wrapper">
                                    <a href="{{ route('profile.show', $sUser->username) }}" class="user-avatar {{ $sUser->isOnline() ? 'online' : 'offline' }}">
                                        <div class="user-avatar-border"><div class="hexagon-100-110"></div></div>
                                        <div class="user-avatar-content"><div class="hexagon-image-68-74" data-src="{{ $sUser->avatarUrl() }}"></div></div>
                                        <div class="user-avatar-progress-border"><div class="hexagon-border-84-92"></div></div>
                                    </a>
                                </div>
                                <a href="{{ route('profile.show', $sUser->username) }}" class="portal-user-username">
                                    {{ $sUser->username }}
                                    @if($sUser->isAdmin())
                                        <i class="fas fa-check-circle portal-verified-icon" title="Admin"></i>
                                    @endif
                                </a>
                                @if(!empty($sUser->name) && $sUser->name !== $sUser->username)
                                    <span class="portal-user-fullname">{{ $sUser->name }}</span>
                                @endif
                                <a href="{{ route('profile.show', $sUser->username) }}" class="portal-user-profile-btn">
                                    {{ __('messages.view_profile') ?? 'View Profile' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section: Groups -->
        @if($groupsCount > 0)
            <div class="portal-search-section" data-section="groups">
                <div class="portal-search-section-header">
                    <h3 class="portal-section-heading">
                        <i class="fas fa-users"></i> {{ __('messages.groups_title') }}
                    </h3>
                    <span class="portal-section-pill">{{ $groupsCount }}</span>
                </div>
                <div class="portal-groups-grid">
                    @foreach($searchedGroups as $sGroup)
                        <div class="portal-group-card">
                            <div class="portal-group-cover" style="background-image: url('{{ $sGroup->coverUrl() }}');"></div>
                            <div class="portal-group-body">
                                <div class="portal-group-avatar-wrapper">
                                    <a href="{{ route('groups.show', $sGroup) }}" class="user-avatar">
                                        <div class="user-avatar-border"><div class="hexagon-100-110"></div></div>
                                        <div class="user-avatar-content"><div class="hexagon-image-68-74" data-src="{{ $sGroup->avatarUrl() }}"></div></div>
                                        <div class="user-avatar-progress-border"><div class="hexagon-border-84-92"></div></div>
                                    </a>
                                </div>
                                <a href="{{ route('groups.show', $sGroup) }}" class="portal-group-name">{{ $sGroup->name }}</a>
                                <p class="portal-group-meta">
                                    <i class="fas fa-user-friends"></i> {{ $sGroup->members_count }} {{ __('messages.members') }}
                                </p>
                                @if(!empty($sGroup->description))
                                    <p class="portal-group-desc">{{ \Illuminate\Support\Str::limit($sGroup->description, 80) }}</p>
                                @endif
                                <a href="{{ route('groups.show', $sGroup) }}" class="portal-group-join-btn">
                                    {{ __('messages.view_group') ?? 'View Group' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section: Store Products -->
        @if($productsCount > 0)
            <div class="portal-search-section" data-section="products">
                <div class="portal-search-section-header">
                    <h3 class="portal-section-heading">
                        <i class="fas fa-shopping-bag"></i> {{ __('messages.products') }}
                    </h3>
                    <span class="portal-section-pill">{{ $productsCount }}</span>
                </div>
                <div class="portal-products-grid">
                    @foreach($searchedProducts as $product)
                        <div class="portal-product-card">
                            <a href="{{ route('store.show', $product->name) }}" class="portal-product-thumb" style="background-image: url('{{ $product->product_image ?? theme_asset('img/error_plug.png') }}');"></a>
                            <div class="portal-product-body">
                                <a href="{{ route('store.show', $product->name) }}" class="portal-product-title">{{ $product->name }}</a>
                                <p class="portal-product-desc">{{ \Illuminate\Support\Str::limit($product->o_valuer, 80) }}</p>
                                <a href="{{ route('store.show', $product->name) }}" class="portal-product-link">
                                    {{ __('messages.view_product') ?? 'View Details' }} <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section: Comments -->
        @if($commentsCount > 0)
            <div class="portal-search-section" data-section="comments">
                <div class="portal-search-section-header">
                    <h3 class="portal-section-heading">
                        <i class="fas fa-comments"></i> {{ __('messages.comments') }}
                    </h3>
                    <span class="portal-section-pill">{{ $commentsCount }}</span>
                </div>
                <div class="portal-comments-container">
                    @if(isset($searchedCommentsForum))
                        @foreach($searchedCommentsForum as $fComment)
                            <div class="portal-comment-card">
                                <div class="portal-comment-avatar">
                                    <div class="user-avatar small no-outline">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ $fComment->user ? $fComment->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portal-comment-body">
                                    <div class="portal-comment-top">
                                        <span class="portal-comment-author">{{ $fComment->user->username ?? 'Member' }}</span>
                                        <span class="portal-comment-ctx">{{ __('messages.on_forum_topic') ?? 'on Topic' }} #{{ $fComment->tid }}</span>
                                        <span class="portal-comment-date">{{ \Carbon\Carbon::createFromTimestamp($fComment->date)->diffForHumans() }}</span>
                                    </div>
                                    <p class="portal-comment-text">{{ \Illuminate\Support\Str::limit(strip_tags($fComment->txt), 140) }}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($searchedCommentsDir))
                        @foreach($searchedCommentsDir as $dComment)
                            <div class="portal-comment-card">
                                <div class="portal-comment-avatar">
                                    <div class="user-avatar small no-outline">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ asset('upload/_avatar.png') }}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portal-comment-body">
                                    <div class="portal-comment-top">
                                        <span class="portal-comment-author">{{ __('messages.directory_comment') ?? 'Directory Comment' }}</span>
                                        <span class="portal-comment-ctx">#{{ $dComment->o_parent }}</span>
                                    </div>
                                    <p class="portal-comment-text">{{ \Illuminate\Support\Str::limit(strip_tags($dComment->o_valuer), 140) }}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

    @else
        <!-- Modern Empty Search State -->
        <div class="portal-empty-card">
            <div class="portal-empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="portal-empty-title">{{ __('messages.portal_no_results') }}</h3>
            <p class="portal-empty-desc">{{ __('messages.portal_no_results_desc') }}</p>
            <button type="button" class="portal-empty-action" id="portal-empty-clear-btn">
                <i class="fas fa-arrow-left"></i> {{ __('messages.portal_filter_all') }}
            </button>
        </div>
    @endif
</div>
