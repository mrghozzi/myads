@php
    $latestOrders = \App\Models\OrderRequest::query()
        ->with('user')
        ->withCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ])
        ->orderBy('date', 'desc')
        ->limit(5)
        ->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name ?? __('messages.latest_orders') }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @forelse($latestOrders as $order)
                <div class="user-status">
                    <a class="user-status-avatar" href="{{ route('profile.show', $order->user->username) }}">
                        <div class="user-avatar small no-outline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $order->user ? $order->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44" data-line-color="{{ $order->user ? $order->user->profileBadgeColor() : '' }}"></div>
                            </div>
                        </div>
                    </a>
                    <p class="user-status-title"><a class="bold" href="{{ route('orders.show', $order) }}">{{ \Illuminate\Support\Str::limit($order->title, 40) }}</a></p>
                    <p class="user-status-text small">{{ $order->displayCategory() }} | {{ $order->displayBudget() }}</p>
                    <p class="user-status-text small">{{ __('messages.offers') }}: {{ $order->offers_count }}</p>
                    <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($order->date)->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-center">{{ __('messages.no_orders_found') }}</p>
            @endforelse
        </div>
        
        <a class="widget-box-button button small white" href="{{ route('orders.index') }}">{{ __('messages.view_all_orders') }}</a>
    </div>
</div>
