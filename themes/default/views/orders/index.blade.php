@extends('theme::layouts.master')

@section('content')
@include('theme::orders.partials.styles')

<div class="section-banner orders-hero" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="orders-icon">
    <p class="section-banner-title">{{ $pageTitle ?? __('messages.order_requests') }}</p>
    <p class="section-banner-text">{{ $pageSubtitle ?? __('messages.browse_latest_orders') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <div class="grid-column">
        <div class="orders-shell">
            <section class="orders-toolbar">
                <div class="orders-toolbar-head">
                    <div>
                        <p class="orders-kicker">{{ __('messages.order_requests') }}</p>
                        <h2 class="orders-toolbar-title">{{ $pageTitle ?? __('messages.order_requests') }}</h2>
                        <p class="orders-toolbar-copy">{{ __('messages.order_marketplace_toolbar_copy') }}</p>
                    </div>
                    @if(($showCreateCta ?? false) && auth()->check())
                        <a href="{{ route('orders.create') }}" class="button primary">{{ __('messages.post_new_order') }}</a>
                    @endif
                </div>

                <form action="{{ $filterAction ?? route('orders.index') }}" method="GET" class="orders-filters">
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="orders-search">{{ __('messages.search') }}</label>
                        <input class="orders-filter-input" id="orders-search" type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('messages.order_search_placeholder') }}">
                    </div>
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="orders-category">{{ __('messages.category') }}</label>
                        <select class="orders-filter-select" id="orders-category" name="category">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption->slug }}" @selected(($filters['category'] ?? '') === $categoryOption->slug)>{{ $categoryOption->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="orders-status">{{ __('messages.status') }}</label>
                        <select class="orders-filter-select" id="orders-status" name="status">
                            @foreach(['all', 'open', 'under_review', 'awarded', 'in_progress', 'delivered', 'completed', 'closed', 'cancelled'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected(($filters['status'] ?? 'all') === $statusOption)>{{ $statusOption === 'all' ? __('messages.all') : __('messages.order_status_' . $statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="orders-sort">{{ __('messages.sort') }}</label>
                        <select class="orders-filter-select" id="orders-sort" name="sort">
                            <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('messages.most_recent') }}</option>
                            <option value="active" @selected(($filters['sort'] ?? '') === 'active')>{{ __('messages.most_active') }}</option>
                            <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>{{ __('messages.order_sort_popular_offers') }}</option>
                            <option value="budget_high" @selected(($filters['sort'] ?? '') === 'budget_high')>{{ __('messages.order_sort_budget_high') }}</option>
                            <option value="budget_low" @selected(($filters['sort'] ?? '') === 'budget_low')>{{ __('messages.order_sort_budget_low') }}</option>
                        </select>
                    </div>
                    <div class="orders-filter-field" style="align-self: end;">
                        <button type="submit" class="button secondary">{{ __('messages.filter') }}</button>
                    </div>
                </form>
            </section>

            <section class="orders-grid">
                @forelse($orders as $order)
                    @include('theme::orders.partials.card', ['order' => $order])
                @empty
                    <div class="orders-empty">
                        <h3 class="orders-card-title">{{ __('messages.no_orders_found') }}</h3>
                        <p class="orders-muted" style="margin-top: 10px;">{{ __('messages.order_empty_state_copy') }}</p>
                    </div>
                @endforelse
            </section>

            <div class="pagination-section">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
