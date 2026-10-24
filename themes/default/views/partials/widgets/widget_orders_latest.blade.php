@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $limit = (int) ($config['limit'] ?? 5);
    $showBudget = isset($config['show_budget']) ? (bool) $config['show_budget'] : true;
    $showOffers = isset($config['show_offers']) ? (bool) $config['show_offers'] : true;
    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : __('messages.view_all_orders');

    $latestOrders = \App\Models\OrderRequest::query()
        ->with('user')
        ->withCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ])
        ->orderBy('date', 'desc')
        ->limit($limit)
        ->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name ?? __('messages.latest_orders') }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @forelse($latestOrders as $order)
                <div class="user-status">
                    <a class="user-status-avatar" href="{{ $order->user ? route('profile.show', $order->user->username) : '#' }}">
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
                    @if($showBudget)
                        <p class="user-status-text small">{{ $order->displayCategory() }} | {{ $order->displayBudget() }}</p>
                    @endif
                    @if($showOffers)
                        <p class="user-status-text small">{{ __('messages.offers') }}: {{ $order->offers_count }}</p>
                    @endif
                    @if($showDate)
                        <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($order->date)->diffForHumans() }}</p>
                    @endif
                </div>
            @empty
                <p class="text-center text-muted small my-2">{{ __('messages.no_orders_found') }}</p>
            @endforelse
        </div>
        
        @if($showButton)
            <a class="widget-box-button button small white" href="{{ route('orders.index') }}">{{ $buttonText }}</a>
        @endif
    </div>
</div>
