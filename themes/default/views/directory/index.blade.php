@extends('theme::layouts.master')
@include('theme::directory._assets')

@section('content')
<div class="directory-rdx directory-hub-shell">
    <div class="section-banner directory-hub-banner">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="directory-icon">
        <div class="directory-hub-banner-copy">
            <p class="section-banner-title">{{ __('messages.directory') }}</p>
            <p class="section-banner-text">{{ __('messages.landing_community_directory_desc') }}</p>
        </div>
    </div>

    @include('theme::directory.partials.mobile_filters')

    <div class="grid grid-3-6-3 mobile-prefer-content directory-hub-grid">
        <div class="grid-column">
            <div class="widget-box directory-side-card directory-command-card">
                <p class="widget-box-title">{{ __('messages.board') }}</p>

                <div class="widget-box-content">
                    <div class="directory-command-list">
                        <a href="{{ route('directory.index') }}" class="button primary">
                            <i class="fa fa-home" aria-hidden="true"></i>&nbsp;{{ __('messages.directory') }}
                        </a>

                        <a href="{{ route('directory.create') }}" class="button secondary">
                            <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ __('messages.addWebsite') }}
                        </a>
                        
                        <a href="{{ route('seo_checker.index') }}" class="button tertiary" style="background-color: #10b981; color: white; border-color: #10b981;">
                            <i class="fa-solid fa-chart-line" aria-hidden="true"></i>&nbsp;{{ __('messages.seo_checker') }}
                        </a>
                    </div>
                </div>
            </div>

            @include('theme::directory.partials.category_board', ['categoryBoard' => $categoryBoard])

            <x-widget-column side="directory_left" />
        </div>

        <div class="grid-column">
            {{-- Modern Compact Directory Discovery Control Center --}}
            <div class="widget-box directory-feed-shell directory-discovery-shell">
                <div class="widget-box-content">
                    <div class="directory-discovery-topbar">
                        <div class="directory-discovery-title-group">
                            <h2 class="directory-discovery-title">
                                <i class="fa fa-compass text-primary"></i> {{ __('messages.explore_directory') ?? 'استكشاف الدليل' }}
                            </h2>
                        </div>

                        <div class="directory-discovery-stats-pills">
                            <span class="directory-stat-pill">
                                <i class="fa fa-globe"></i> <strong>{{ $directoryStats['listing_count'] }}</strong> {{ __('messages.sites') ?? 'موقع' }}
                            </span>
                            <span class="directory-stat-pill">
                                <i class="fa fa-folder-open"></i> <strong>{{ $directoryStats['category_count'] }}</strong> {{ __('messages.categories') ?? 'أقسام' }}
                            </span>
                        </div>
                    </div>

                    {{-- Live Search Input --}}
                    <div class="directory-discovery-search-wrap">
                        <div class="directory-search-input-box">
                            <i class="fa fa-search directory-search-icon"></i>
                            <input type="text" 
                                   id="directory-search-input" 
                                   class="directory-search-input" 
                                   placeholder="{{ __('messages.directory_search_placeholder') }}" 
                                   value="{{ $filters['search'] ?? '' }}"
                                   autocomplete="off">
                            <button type="button" class="directory-search-clear" id="directory-search-clear" style="{{ empty($filters['search']) ? 'display: none;' : '' }}">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Filter & Sort Toolbar --}}
                    <div class="directory-filter-bar-group">
                        <div class="directory-nav-tabs">
                            <button type="button" class="directory-tab-btn {{ ($filters['sort'] ?? 'latest') === 'trending' ? 'active' : '' }}" data-sort="trending">
                                <i class="fa fa-fire text-warning"></i> {{ __('messages.trending') ?? 'الرائج' }}
                            </button>
                            <button type="button" class="directory-tab-btn {{ ($filters['sort'] ?? 'latest') === 'latest' ? 'active' : '' }}" data-sort="latest">
                                <i class="fa fa-clock-o"></i> {{ __('messages.latest') ?? 'الأحدث' }}
                            </button>
                            <button type="button" class="directory-tab-btn {{ ($filters['sort'] ?? 'latest') === 'popular' ? 'active' : '' }}" data-sort="popular">
                                <i class="fa fa-star text-gold"></i> {{ __('messages.most_visited') ?? 'الأكثر زيارة' }}
                            </button>
                            <button type="button" class="directory-tab-btn {{ ($filters['sort'] ?? 'latest') === 'featured' ? 'active' : '' }}" data-sort="featured">
                                <i class="fa fa-gem text-primary"></i> {{ __('messages.featured') ?? 'المميزة' }}
                            </button>
                        </div>

                        {{-- Pricing Chips --}}
                        <div class="directory-pricing-filter-bar">
                            <span class="directory-pricing-label"><i class="fa fa-tags"></i></span>
                            <button type="button" class="directory-filter-chip {{ empty($filters['pricing']) || $filters['pricing'] === 'all' ? 'active' : '' }}" data-pricing="">
                                {{ __('messages.all') ?? 'الكل' }}
                            </button>
                            <button type="button" class="directory-filter-chip {{ ($filters['pricing'] ?? '') === 'free' ? 'active' : '' }}" data-pricing="free">
                                {{ __('messages.pricing_free') ?? 'مجاني' }}
                            </button>
                            <button type="button" class="directory-filter-chip {{ ($filters['pricing'] ?? '') === 'freemium' ? 'active' : '' }}" data-pricing="freemium">
                                {{ __('messages.pricing_freemium') ?? 'فريميوم' }}
                            </button>
                            <button type="button" class="directory-filter-chip {{ ($filters['pricing'] ?? '') === 'paid' ? 'active' : '' }}" data-pricing="paid">
                                {{ __('messages.pricing_paid') ?? 'مدفوع' }}
                            </button>
                            <button type="button" class="directory-filter-chip {{ ($filters['pricing'] ?? '') === 'open_source' ? 'active' : '' }}" data-pricing="open_source">
                                {{ __('messages.pricing_open_source') ?? 'مفتوح المصدر' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Featured Sites Showcase Carousel / Banner --}}
            @if(isset($featuredSites) && $featuredSites->isNotEmpty() && empty($filters['search']))
                <div class="widget-box directory-featured-strip">
                    <p class="widget-box-title"><i class="fa fa-trophy text-warning"></i> {{ __('messages.featured_sites_of_week') ?? 'أبرز المواقع المتصدرة هذا الأسبوع' }}</p>
                    <div class="widget-box-content directory-featured-strip-content">
                        @foreach($featuredSites as $featuredSite)
                            <a href="{{ route('directory.show', $featuredSite->id) }}" class="directory-featured-mini-card">
                                <div class="directory-featured-mini-media">
                                    <img src="{{ $featuredSite->prominent_image ?: theme_asset('img/dir_image.png') }}" alt="{{ $featuredSite->name }}">
                                </div>
                                <div class="directory-featured-mini-info">
                                    <h4 class="directory-featured-mini-title">{{ $featuredSite->name }}</h4>
                                    <span class="directory-featured-mini-badge">▲ {{ $featuredSite->upvotes_count }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div id="infinite-scroll-container" class="directory-feed-list">
                <div id="timeline-content" class="directory-feed-items">
                    @if($cards->isNotEmpty())
                        @include('theme::directory.partials.feed_items', ['cards' => $cards])
                    @else
                        <div class="widget-box directory-empty-card">
                            <div class="widget-box-content">
                                <p class="no-results">{{ __('messages.no_listings_found') }}</p>
                            </div>
                        </div>
                    @endif

                    @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
                </div>
            </div>
        </div>

        <div class="grid-column">
            <div class="widget-box directory-side-card directory-stats-card">
                <p class="widget-box-title">{{ __('messages.details') }}</p>

                <div class="widget-box-content">
                    <div class="directory-side-stat-list">
                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.latest_sites') }}</span>
                            <strong>{{ $directoryStats['listing_count'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.cat_s') }}</span>
                            <strong>{{ $directoryStats['category_count'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.subcategories') }}</span>
                            <strong>{{ $directoryStats['subcategory_count'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <x-widget-column side="directory_right" />
        </div>
    </div>
</div>
@endsection
