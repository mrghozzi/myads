@php
    $isAwarded = (int) optional($order->awardedOffer)->id === (int) $offer->id;
    $canAward = auth()->check()
        && (int) auth()->id() === (int) $order->uid
        && $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN
        && $offer->status === \App\Models\OrderOffer::STATUS_ACTIVE;
    $canWithdraw = auth()->check()
        && (int) auth()->id() === (int) $offer->user_id
        && $offer->isEditable();
@endphp

<article class="orders-offer-card {{ $isAwarded ? 'is-awarded' : '' }}" id="offer-{{ $offer->id }}">
    <div class="orders-offer-head">
        <div>
            <div class="orders-card-meta" style="margin-bottom: 10px;">
                <span class="orders-meta-pill">{{ $offer->displayQuote() }}</span>
                <span class="orders-meta-pill">{{ $offer->displayDelivery() }}</span>
                <span class="orders-meta-pill">{{ __('messages.order_pricing_model_' . $offer->pricing_model) }}</span>
                @if($isAwarded)
                    @include('theme::orders.partials.status-pill', ['status' => 'awarded'])
                @else
                    <span class="orders-meta-pill">{{ $offer->displayStatus() }}</span>
                @endif
            </div>
            <h4 class="orders-offer-title">
                <a href="{{ route('profile.show', $offer->user->username) }}">{{ $offer->user->username }}</a>
            </h4>
            <p class="orders-muted">{{ $offer->created_at?->diffForHumans() }}</p>
        </div>

        <div class="orders-offer-actions">
            @if($canAward)
                <form action="{{ route('orders.award', $order) }}" method="POST">
                    @csrf
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                    <button type="submit" class="button primary small">{{ __('messages.order_award_offer') }}</button>
                </form>
            @endif

            @if($canWithdraw)
                <form action="{{ route('orders.offers.destroy', $offer) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button white small">{{ __('messages.order_withdraw_offer') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="orders-divider"></div>

    <div class="orders-offer-message">{{ $offer->message }}</div>

    @if($offer->client_rating)
        <div class="orders-divider"></div>
        <div class="orders-inline-actions">
            <div class="orders-rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa{{ $i <= $offer->client_rating ? 's' : 'r' }} fa-star"></i>
                @endfor
            </div>
            <span class="orders-muted">{{ __('messages.rating_submitted') }}</span>
        </div>
        @if($offer->client_review)
            <p class="orders-muted" style="margin-top: 12px;">{{ $offer->client_review }}</p>
        @endif
    @endif
</article>
