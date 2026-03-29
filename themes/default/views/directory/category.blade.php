@extends('theme::layouts.master')
@include('theme::directory._assets')

@section('content')
<div class="directory-rdx directory-category-shell">
    <div class="section-banner directory-hub-banner">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="directory-category-icon">
        <div class="directory-hub-banner-copy">
            <p class="section-banner-title">{{ $category->name }}</p>
            <p class="section-banner-text">{{ $category->txt }}</p>
        </div>
    </div>

    <div class="section-filters-bar v7 directory-breadcrumb-bar">
        <div class="section-filters-bar-actions">
            <div class="section-filters-bar-info">
                <p class="section-filters-bar-title">
                    <a href="{{ route('directory.index') }}">{{ __('messages.directory') }}</a>
                    <span class="separator"></span>
                    <a href="{{ route('directory.category.legacy', $category->id) }}">{{ $category->name }}</a>
                </p>
            </div>
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
                    </div>
                </div>
            </div>

            @if($categorySummary['subcategories']->isNotEmpty())
                <div class="widget-box directory-side-card">
                    <p class="widget-box-title">{{ __('messages.subcategories') }}</p>

                    <div class="widget-box-content">
                        <div class="directory-category-pill-list directory-category-pill-list-compact">
                            @foreach($categorySummary['subcategories'] as $subCategory)
                                <a class="directory-category-pill" href="{{ $subCategory['url'] }}">
                                    {{ $subCategory['category']->name }}
                                    <span>{{ $subCategory['listing_count'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <x-widget-column side="directory_left" />
        </div>

        <div class="grid-column">
            <div class="widget-box directory-feed-shell directory-category-summary-card">
                <div class="widget-box-content">
                    <div class="directory-feed-header">
                        <div class="directory-feed-copy">
                            <p class="directory-feed-eyebrow">{{ __('messages.directory') }}</p>
                            <h2 class="directory-feed-title">{{ $category->name }}</h2>
                            <p class="directory-feed-text">{{ $category->txt ?: __('messages.landing_community_directory_desc') }}</p>
                        </div>

                        <div class="directory-feed-summary">
                            <div class="directory-feed-summary-item">
                                <span>{{ __('messages.latest_sites') }}</span>
                                <strong>{{ $categorySummary['listing_count'] }}</strong>
                            </div>

                            <div class="directory-feed-summary-item">
                                <span>{{ __('messages.subcategories') }}</span>
                                <strong>{{ $categorySummary['subcategory_count'] }}</strong>
                            </div>
                        </div>
                    </div>

                    @if($categorySummary['subcategories']->isNotEmpty())
                        <div class="directory-category-pill-list">
                            @foreach($categorySummary['subcategories'] as $subCategory)
                                <a class="directory-category-pill" href="{{ $subCategory['url'] }}">
                                    {{ $subCategory['category']->name }}
                                    <span>{{ $subCategory['listing_count'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

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
                            <strong>{{ $categorySummary['listing_count'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.subcategories') }}</span>
                            <strong>{{ $categorySummary['subcategory_count'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <x-widget-column side="directory_right" />
        </div>
    </div>
</div>
@endsection
