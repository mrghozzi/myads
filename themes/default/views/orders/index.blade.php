@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="orders-icon">
    <p class="section-banner-title">{{ __('messages.order_requests') }}</p>
    <p class="section-banner-text">{{ __('messages.browse_latest_orders') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div class="section-header-info">
                <h2 class="section-title">{{ __('messages.latest_orders') }}</h2>
            </div>
            
            <div class="section-header-actions" style="display: flex; gap: 16px; align-items: center;">
                <form action="{{ route('orders.index') }}" method="GET" id="sort-form">
                    <div class="form-input small">
                        <select name="sort" onchange="document.getElementById('sort-form').submit()" style="padding: 0 16px; height: 40px; border-radius: 4px; border: 1px solid #dedee9; background-color: #fff; color: #3e3f5e; font-weight: 700;">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('messages.most_recent') }}</option>
                            <option value="active" {{ request('sort') == 'active' ? 'selected' : '' }}>{{ __('messages.most_active') }}</option>
                            <option value="rated" {{ request('sort') == 'rated' ? 'selected' : '' }}>{{ __('messages.highest_rated') }}</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('messages.most_active') }} ({{ __('messages.replies') }})</option>
                        </select>
                    </div>
                </form>

                @auth
                <a href="{{ route('orders.create') }}" class="button primary">{{ __('messages.post_new_order') }}</a>
                @endauth
            </div>
        </div>

        <div id="orders-container" style="display: grid; grid-gap: 16px;">
            @forelse($orders as $order)
                @php
                    // We can wrap the OrderRequest into a temporary Status-like object if we want to reuse the partial,
                    // but since we want it to look like a post, it's better if we have the actual Status record.
                    $activity = $order->statusRecord ?: null;
                @endphp
                
                @if($activity)
                    @include('theme::partials.activity.render', ['activity' => $activity])
                @else
                    {{-- Fallback if no status record (shouldn't happen with new orders) --}}
                    <div class="widget-box">
                        <div class="widget-box-content">
                             <h3><a href="{{ route('orders.show', $order->id) }}">{{ $order->title }}</a></h3>
                             <p>{{ \Illuminate\Support\Str::limit($order->description, 200) }}</p>
                        </div>
                    </div>
                @endif
            @empty
                <div class="widget-box">
                    <div class="widget-box-content">
                        <p class="text-center">{{ __('messages.no_orders_found') }}</p>
                    </div>
                </div>
            @endforelse

            <div class="pagination-section">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
