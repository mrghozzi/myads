<article class="orders-card">
    <div class="orders-card-head">
        <div>
            <div class="orders-card-meta" style="margin-bottom: 10px;">
                @include('theme::orders.partials.status-pill', ['status' => $order->derived_workflow_status])
                <span class="orders-meta-pill">{{ $order->displayCategory() }}</span>
                <span class="orders-meta-pill">{{ $order->displayBudget() }}</span>
                <span class="orders-meta-pill">{{ __('messages.offers') }}: {{ $order->offers_count ?? 0 }}</span>
            </div>
            <h3 class="orders-card-title">
                <a href="{{ route('orders.show', $order) }}">{{ $order->title }}</a>
            </h3>
            <p class="orders-card-copy">
                {{ __('messages.posted_by') }} <a href="{{ route('profile.show', $order->user->username) }}">{{ $order->user->username }}</a>
                | {{ $order->date_formatted }}
            </p>
        </div>
        <div class="orders-card-footer">
            <a href="{{ route('orders.show', $order) }}" class="button secondary small">{{ __('messages.view_details') }}</a>
            @auth
                @if((int) auth()->id() === (int) $order->uid && in_array($order->workflow_status, ['open', 'closed'], true))
                    <a href="{{ route('orders.edit', $order) }}" class="button white small">{{ __('messages.edit') }}</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="orders-divider"></div>

    <p class="orders-card-description">
        {{ \Illuminate\Support\Str::limit(trim(strip_tags($order->description)), 240) }}
    </p>

    <div class="orders-card-footer" style="margin-top: 16px;">
        <span class="orders-muted">{{ __('messages.delivery') }}: {{ $order->displayDeliveryWindow() }}</span>
        @if((float) $order->avg_rating > 0)
            <span class="orders-meta-pill">{{ __('messages.rating') }}: {{ number_format((float) $order->avg_rating, 1) }}/5</span>
        @endif
    </div>
</article>
