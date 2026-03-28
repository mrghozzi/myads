@php
    $latestOrders = \App\Models\OrderRequest::where('statu', 1)
        ->with('user')
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
                        </div>
                    </a>
                    <p class="user-status-title"><a class="bold" href="{{ route('orders.show', $order->id) }}">{{ \Illuminate\Support\Str::limit($order->title, 40) }}</a></p>
                    <p class="user-status-text small">{{ $order->category }} @if($order->budget) • {{ $order->budget }} @endif</p>
                    <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($order->date)->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-center">{{ __('messages.no_orders_found') ?? 'No orders found.' }}</p>
            @endforelse
        </div>
        
        <a class="widget-box-button button small white" href="{{ route('orders.index') }}">{{ __('messages.view_all_orders') ?? 'View All Orders' }}</a>
    </div>
</div>
