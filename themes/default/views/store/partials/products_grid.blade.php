@php
    $isScriptSpecific = isset($scriptName) && $scriptName !== 'all';
    $currentView = $viewMode ?? 'grid';
    $hasProducts = $products->count() > 0;
    $filterResetUrl = $isScriptSpecific 
        ? route('store.script_category', [$scriptName, 'all']) 
        : route('store.index');
@endphp

<div class="store-results-wrapper">
    <div class="store-results-meta" id="storeResultsMeta">
        <div class="results-count-box">
            <span class="count-badge">{{ $products->total() }}</span>
            <span class="count-label">
                {{ $products->total() == 1 ? __('messages.product_available') : __('messages.products_available') }}
            </span>
        </div>

        @if(($search ?? '') !== '' || ($category ?? null) || ($sort ?? 'latest') !== 'latest')
            <div class="active-filters-chips">
                @if(($search ?? '') !== '')
                    <span class="filter-chip" data-remove-filter="q">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>"{{ $search }}"</span>
                        <button type="button" class="chip-remove" aria-label="Remove search filter">&times;</button>
                    </span>
                @endif

                @if($category ?? null)
                    <span class="filter-chip" data-remove-filter="category">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>{{ __('messages.' . $category) != 'messages.' . $category ? __('messages.' . $category) : ucfirst($category) }}</span>
                        <button type="button" class="chip-remove" aria-label="Remove category filter">&times;</button>
                    </span>
                @endif

                @if(($sort ?? 'latest') !== 'latest')
                    <span class="filter-chip" data-remove-filter="sort">
                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                        <span>
                            @switch($sort)
                                @case('downloads') {{ __('messages.sort_downloads') ?? 'Most Downloaded' }} @break
                                @case('rating') {{ __('messages.top_rated') ?? 'Highest Rated' }} @break
                                @case('price_asc') {{ __('messages.sort_price_asc') ?? 'Price: Low to High' }} @break
                                @case('price_desc') {{ __('messages.sort_price_desc') ?? 'Price: High to Low' }} @break
                                @case('free') {{ __('messages.sort_free') ?? 'Free' }} @break
                                @case('paid') {{ __('messages.sort_paid') ?? 'Paid' }} @break
                                @case('sale') {{ __('messages.on_sale') ?? 'On Sale' }} @break
                                @default {{ $sort }}
                            @endswitch
                        </span>
                        <button type="button" class="chip-remove" aria-label="Remove sort filter">&times;</button>
                    </span>
                @endif

                <a href="{{ $filterResetUrl }}" class="clear-all-filters-btn" id="clearAllFiltersBtn">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>{{ __('messages.clear_filters') ?? 'Clear Filters' }}</span>
                </a>
            </div>
        @endif
    </div>

    <!-- PRODUCTS GRID / LIST -->
    <div class="modern-product-grid {{ $currentView === 'list' ? 'list-view' : '' }}" id="productCardsContainer">
        @forelse($products as $product)
            @php
                $latestFile = $product->files->sortByDesc('id')->first();
                $owner = $product->user;
                $ownerAvatar = $owner ? $owner->avatarUrl() : asset('upload/_avatar.png');
                $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
                $prodScript = $product->associated_script_name;
                $catName = $product->type ? $product->type->name : null;
                $categoryLink = '#';
                if ($catName) {
                    $targetScript = $isScriptSpecific ? $scriptName : $prodScript;
                    $categoryLink = $targetScript 
                        ? route('store.script_category', [$targetScript, $catName])
                        : route('store.index', ['category' => $catName]);
                }
                $avgRating = (float) ($product->reviews_avg_rating ?? $product->average_rating ?? 0);
                $reviewsCount = (int) ($product->reviews_count ?? 0);
                $downloadsCount = (int) $product->downloads_count;
                $isOnSale = $product->sale && $product->sale->is_active;
                $currentPrice = $isOnSale ? $product->sale->sale_price : $product->o_order;
                $originalPrice = $product->o_order;
                $liveDemoUrl = $product->live_demo_url;
                $isFree = ((int) $product->o_order === 0) && (!$isOnSale || (int) $product->sale->sale_price === 0);
            @endphp

            <article class="modern-product-card" data-product-id="{{ $product->id }}">
                <!-- Media Section -->
                <div class="product-media-wrapper">
                    <a href="{{ route('store.show', $product->name) }}" class="product-thumb-link" aria-label="{{ $product->name }}">
                        <img 
                            src="{{ $productImage }}" 
                            alt="{{ $product->name }}" 
                            class="product-thumb-img" 
                            loading="lazy" 
                            onerror="this.onerror=null;this.src='{{ theme_asset('img/error_plug.png') }}';"
                        >
                        <div class="product-media-overlay">
                            <span class="preview-btn">
                                <i class="fa-solid fa-eye"></i>
                                <span>{{ __('messages.view_details') ?? 'View Item' }}</span>
                            </span>
                        </div>
                    </a>

                    <!-- Top Floating Badges -->
                    <div class="product-floating-badges start-badges">
                        @if($isFree)
                            <span class="status-pill pill-free">
                                <i class="fa-solid fa-gift"></i>
                                <span>{{ __('messages.free') ?? 'FREE' }}</span>
                            </span>
                        @elseif($isOnSale)
                            <span class="status-pill pill-sale">
                                <i class="fa-solid fa-fire"></i>
                                <span class="old-price">{{ $originalPrice }}</span>
                                <span class="sale-price">{{ $product->sale->sale_price }} {{ __('messages.points') ?? 'PTS' }}</span>
                            </span>
                        @else
                            <span class="status-pill pill-price">
                                <i class="fa-solid fa-coins"></i>
                                <span>{{ $product->o_order }} {{ __('messages.points') ?? 'PTS' }}</span>
                            </span>
                        @endif

                        @if($product->is_suspended)
                            <span class="status-pill pill-suspended">
                                <i class="fa-solid fa-ban"></i>
                                <span>{{ __('messages.suspended') ?? 'Suspended' }}</span>
                            </span>
                        @elseif($product->is_pending)
                            <span class="status-pill pill-pending">
                                <i class="fa-solid fa-clock"></i>
                                <span>{{ __('messages.pending_approval') ?? 'Pending' }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="product-floating-badges end-badges">
                        @if($downloadsCount > 0)
                            <span class="status-pill pill-downloads" title="{{ $downloadsCount }} {{ __('messages.download') ?? 'Downloads' }}">
                                <i class="fa-solid fa-download"></i>
                                <span>{{ number_format($downloadsCount) }}</span>
                            </span>
                        @endif

                        @if($liveDemoUrl)
                            <a href="{{ $liveDemoUrl }}" target="_blank" rel="noopener noreferrer" class="status-pill pill-demo" title="{{ __('messages.live_demo') ?? 'Live Demo' }}" onclick="event.stopPropagation();">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                <span>Demo</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Product Information -->
                <div class="product-info-wrapper">
                    <!-- Top Category & Rating Row -->
                    <div class="product-meta-row">
                        @if($catName)
                            <a href="{{ $categoryLink }}" class="product-category-chip" data-category="{{ $catName }}">
                                <i class="fa-solid fa-folder-open"></i>
                                <span>{{ __('messages.' . $catName) != 'messages.' . $catName ? __('messages.' . $catName) : ucfirst($catName) }}</span>
                            </a>
                        @else
                            <span class="product-category-chip">
                                <i class="fa-solid fa-cube"></i>
                                <span>{{ __('messages.product') ?? 'Product' }}</span>
                            </span>
                        @endif

                        <div class="product-rating-box" title="{{ $avgRating > 0 ? $avgRating . ' / 5 (' . $reviewsCount . ')' : 'No reviews yet' }}">
                            @if($avgRating > 0)
                                <div class="rating-stars">
                                    <i class="fa-solid fa-star"></i>
                                    <span class="rating-score">{{ number_format($avgRating, 1) }}</span>
                                </div>
                                <span class="rating-count">({{ $reviewsCount }})</span>
                            @else
                                <span class="rating-new"><i class="fa-regular fa-star"></i> {{ __('messages.new') ?? 'New' }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="product-card-title">
                        <a href="{{ route('store.show', $product->name) }}" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <!-- Description -->
                    <p class="product-card-desc">
                        {{ $product->o_valuer ?: __('messages.no_description') }}
                    </p>

                    <!-- Creator & Version Row -->
                    <div class="product-creator-row">
                        <div class="creator-profile">
                            <a href="{{ $owner ? route('profile.show', $owner->username) : '#' }}" class="creator-avatar" aria-label="{{ $owner ? $owner->username : 'Creator' }}">
                                <img src="{{ $ownerAvatar }}" alt="{{ $owner ? $owner->username : 'User' }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('upload/_avatar.png') }}';">
                            </a>
                            <div class="creator-meta">
                                <span class="creator-label">{{ __('messages.posted_by') ?? 'By' }}</span>
                                <a href="{{ $owner ? route('profile.show', $owner->username) : '#' }}" class="creator-name">
                                    {{ $owner ? $owner->username : __('messages.unknown') }}
                                </a>
                            </div>
                        </div>

                        @if($latestFile && $latestFile->name)
                            <div class="version-tag" title="{{ __('messages.version') ?? 'Version' }}: {{ $latestFile->name }}">
                                <i class="fa-solid fa-tag"></i>
                                <span>{{ $latestFile->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="product-card-footer">
                        <div class="price-display-box">
                            @if($isFree)
                                <span class="price-main free-text">{{ __('messages.free') ?? 'FREE' }}</span>
                            @elseif($isOnSale)
                                <div class="sale-price-group">
                                    <span class="sale-current">{{ $product->sale->sale_price }} <small>{{ __('messages.points') ?? 'PTS' }}</small></span>
                                    <span class="sale-original">{{ $originalPrice }}</span>
                                </div>
                            @else
                                <span class="price-main">{{ $product->o_order }} <small>{{ __('messages.points') ?? 'PTS' }}</small></span>
                            @endif
                        </div>

                        <div class="card-action-buttons">
                            @if($liveDemoUrl)
                                <a href="{{ $liveDemoUrl }}" target="_blank" rel="noopener noreferrer" class="btn-card-action btn-demo-action" title="{{ __('messages.live_demo') ?? 'Live Demo' }}">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span class="action-text">{{ __('messages.demo') ?? 'Demo' }}</span>
                                </a>
                            @endif
                            <a href="{{ route('store.show', $product->name) }}" class="btn-card-action btn-view-action">
                                <span>{{ __('messages.view') ?? 'View' }}</span>
                                <i class="fa-solid fa-arrow-right-long dir-aware-arrow"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="store-empty-state">
                <div class="empty-icon-bubble">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="empty-title">{{ __('messages.no_products_found') ?? 'No products found' }}</h3>
                <p class="empty-text">
                    @if(($search ?? '') !== '')
                        {{ __('messages.no_products_search_match') ?? 'No digital items matched your search criteria. Try different keywords or clear your filters.' }}
                    @else
                        {{ __('messages.no_products_in_category') ?? 'There are no items currently available in this section.' }}
                    @endif
                </p>
                <div class="empty-actions">
                    <a href="{{ $filterResetUrl }}" class="btn-reset-filters">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>{{ __('messages.clear_filters') ?? 'Clear All Filters' }}</span>
                    </a>
                    @auth
                        <a href="{{ route('store.create') }}" class="btn-publish-first">
                            <i class="fa-solid fa-plus"></i>
                            <span>{{ __('messages.add_first_product') ?? 'Publish First Item' }}</span>
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    @if($products->hasPages())
        <div class="store-pagination-wrapper" id="storePaginationContainer">
            {{ $products->links() }}
        </div>
    @endif
</div>
