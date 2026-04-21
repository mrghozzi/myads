@extends('theme::layouts.master')

@section('content')
@include('theme::orders.partials.styles')

<div class="section-banner orders-hero" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="order-detail-icon">
    <p class="section-banner-title">{{ $order->title }}</p>
    <p class="section-banner-text">{{ __('messages.order_detail_subtitle') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <aside class="orders-panel">
            <p class="orders-kicker">{{ __('messages.client_info') }}</p>
            <div class="user-short-description" style="margin-top: 14px;">
                <a class="user-short-description-avatar user-avatar medium {{ $order->user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $order->user->username) }}">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-68-74" data-src="{{ $order->user->avatarUrl() }}"></div>
                    </div>
                </a>
                <p class="user-short-description-title"><a href="{{ route('profile.show', $order->user->username) }}">{{ $order->user->username }}</a></p>
                <p class="user-short-description-text">{{ $order->user->isOnline() ? __('messages.online') : __('messages.offline') }}</p>
            </div>

            <div class="orders-divider"></div>

            <div class="orders-summary-grid">
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.status') }}</div>
                    <div class="orders-summary-value">{{ $order->displayWorkflowStatus() }}</div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.offers') }}</div>
                    <div class="orders-summary-value">{{ $order->offers_count }}</div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.budget') }}</div>
                    <div class="orders-summary-value">{{ $order->displayBudget() }}</div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.delivery') }}</div>
                    <div class="orders-summary-value">{{ $order->displayDeliveryWindow() }}</div>
                </div>
            </div>

            <div class="orders-divider"></div>

            <div class="orders-detail-actions">
                @auth
                    @if((int) auth()->id() !== (int) $order->uid)
                        <a href="{{ url('/messages/' . \App\Models\Message::encodeConversationRouteKey(auth()->user(), $order->uid)) }}" class="button primary full">{{ __('messages.contact_client') }}</a>
                    @endif
                    @if((int) auth()->id() === (int) $order->uid && in_array($order->workflow_status, ['open', 'closed'], true))
                        <a href="{{ route('orders.edit', $order) }}" class="button secondary full">{{ __('messages.edit') }}</a>
                    @endif
                @endauth
            </div>
        </aside>
    </div>

    <div class="grid-column">
        <div class="orders-layout-main">
            <section class="orders-panel">
                <div class="orders-detail-head">
                    <div>
                        <div class="orders-card-meta" style="margin-bottom: 10px;">
                            @include('theme::orders.partials.status-pill', ['status' => $order->derived_workflow_status])
                            <span class="orders-meta-pill">{{ $order->displayCategory() }}</span>
                            <span class="orders-meta-pill">{{ $order->displayBudget() }}</span>
                            <span class="orders-meta-pill">{{ __('messages.delivery') }}: {{ $order->displayDeliveryWindow() }}</span>
                        </div>
                        <h2 class="orders-detail-title">{{ $order->title }}</h2>
                        <p class="orders-muted">{{ __('messages.posted_by') }} <a href="{{ route('profile.show', $order->user->username) }}">{{ $order->user->username }}</a> | {{ $order->date_formatted }}</p>
                    </div>
                    <div class="orders-detail-actions">
                        @auth
                            @if((int) auth()->id() === (int) $order->uid && $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN)
                                <form action="{{ route('orders.close', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="button white small">{{ __('messages.close_order') }}</button>
                                </form>
                            @endif

                            @if((int) auth()->id() === (int) $order->uid && in_array($order->workflow_status, [\App\Models\OrderRequest::WORKFLOW_AWARDED, \App\Models\OrderRequest::WORKFLOW_IN_PROGRESS, \App\Models\OrderRequest::WORKFLOW_DELIVERED], true))
                                <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="button white small">{{ __('messages.order_cancel_action') }}</button>
                                </form>
                            @endif

                            @if($order->contract && (int) auth()->id() === (int) $order->contract->provider_user_id && $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_AWARDED)
                                <form action="{{ route('orders.start', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="button primary small">{{ __('messages.order_start_action') }}</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="orders-divider"></div>

                <div class="orders-detail-description">{!! nl2br(e($order->description)) !!}</div>

                @if($order->contract)
                    <div class="orders-divider"></div>
                    <div class="orders-summary-grid">
                        <div class="orders-summary-item">
                            <div class="orders-summary-label">{{ __('messages.order_contract_provider') }}</div>
                            <div class="orders-summary-value">{{ optional($order->contract->provider)->username ?? __('messages.unknown_user') }}</div>
                        </div>
                        <div class="orders-summary-item">
                            <div class="orders-summary-label">{{ __('messages.status') }}</div>
                            <div class="orders-summary-value">{{ $order->contract->displayStatus() }}</div>
                        </div>
                    </div>
                @endif
            </section>

            @auth
                @if((int) auth()->id() !== (int) $order->uid && in_array($order->workflow_status, [\App\Models\OrderRequest::WORKFLOW_OPEN], true))
                    <section class="orders-offer-form">
                        <div class="orders-toolbar-head">
                            <div>
                                <p class="orders-kicker">{{ $viewerOffer ? __('messages.order_offer_edit_title') : __('messages.order_offer_form_title') }}</p>
                                <h3 class="orders-card-title">{{ $viewerOffer ? __('messages.order_offer_edit_title') : __('messages.order_offer_form_title') }}</h3>
                                <p class="orders-toolbar-copy">{{ __('messages.order_offer_form_subtitle') }}</p>
                            </div>
                        </div>

                        <form action="{{ $viewerOffer ? route('orders.offers.update', $viewerOffer) : route('orders.offers.store', $order) }}" method="POST" class="orders-form-layout">
                            @csrf
                            @if($viewerOffer)
                                @method('PATCH')
                            @endif

                            <div class="orders-form-grid">
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="pricing_model">{{ __('messages.pricing') }}</label>
                                    <select class="orders-filter-select" name="pricing_model" id="pricing_model">
                                        @foreach(['fixed', 'hourly', 'negotiable'] as $pricingModel)
                                            <option value="{{ $pricingModel }}" @selected(old('pricing_model', $viewerOffer->pricing_model ?? 'fixed') === $pricingModel)>{{ __('messages.order_pricing_model_' . $pricingModel) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="quoted_amount">{{ __('messages.price') }}</label>
                                    <input class="orders-filter-input" type="number" step="0.01" min="0" id="quoted_amount" name="quoted_amount" value="{{ old('quoted_amount', $viewerOffer->quoted_amount) }}">
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="currency_code">{{ __('messages.currency') }}</label>
                                    <select class="orders-filter-select" name="currency_code" id="currency_code">
                                        @foreach($currencies as $currencyCode => $currencyLabel)
                                            <option value="{{ $currencyCode }}" @selected(old('currency_code', $viewerOffer->currency_code ?? $order->budget_currency ?? 'USD') === $currencyCode)>{{ $currencyLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="delivery_days">{{ __('messages.delivery') }}</label>
                                    <input class="orders-filter-input" type="number" min="1" max="365" id="delivery_days" name="delivery_days" value="{{ old('delivery_days', $viewerOffer->delivery_days) }}">
                                </div>
                            </div>

                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="message">{{ __('messages.description') }}</label>
                                <textarea class="orders-textarea" name="message" id="message" required>{{ old('message', $viewerOffer->message) }}</textarea>
                            </div>

                            <div class="orders-detail-actions">
                                <button type="submit" class="button primary">{{ $viewerOffer ? __('messages.save_changes') : __('messages.order_submit_offer') }}</button>
                            </div>
                        </form>
                        @if($viewerOffer && $viewerOffer->isEditable())
                            <form action="{{ route('orders.offers.destroy', $viewerOffer) }}" method="POST" style="margin-top: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button white">{{ __('messages.order_withdraw_offer') }}</button>
                            </form>
                        @endif
                    </section>
                @endif

                @if((int) auth()->id() === (int) $order->uid && (string) $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_DELIVERED)
                    <section class="orders-offer-form">
                        <div class="orders-toolbar-head">
                            <div>
                                <p class="orders-kicker">{{ __('messages.order_complete_action') }}</p>
                                <h3 class="orders-card-title">{{ __('messages.order_complete_action') }}</h3>
                                <p class="orders-toolbar-copy">{{ __('messages.order_complete_help') }}</p>
                            </div>
                        </div>

                        <form action="{{ route('orders.complete', $order) }}" method="POST" class="orders-form-layout">
                            @csrf
                            <div class="orders-form-grid">
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="rating">{{ __('messages.rating') }}</label>
                                    <select class="orders-filter-select" name="rating" id="rating">
                                        <option value="">{{ __('messages.optional') }}</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}">{{ $i }}/5</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="review">{{ __('messages.review') }}</label>
                                <textarea class="orders-textarea" name="review" id="review"></textarea>
                            </div>
                            <button type="submit" class="button primary">{{ __('messages.order_complete_action') }}</button>
                        </form>
                    </section>
                @endif

                @if($order->contract && (int) auth()->id() === (int) $order->contract->provider_user_id && (string) $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_IN_PROGRESS)
                    <section class="orders-offer-form">
                        <div class="orders-toolbar-head">
                            <div>
                                <p class="orders-kicker">{{ __('messages.order_deliver_action') }}</p>
                                <h3 class="orders-card-title">{{ __('messages.order_deliver_action') }}</h3>
                            </div>
                        </div>
                        <form action="{{ route('orders.deliver', $order) }}" method="POST" class="orders-form-layout">
                            @csrf
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="delivery_note">{{ __('messages.notes') }}</label>
                                <textarea class="orders-textarea" name="delivery_note" id="delivery_note"></textarea>
                            </div>
                            <button type="submit" class="button primary">{{ __('messages.order_deliver_action') }}</button>
                        </form>
                    </section>
                @endif

                @if((int) auth()->id() === (int) $order->uid && (string) $order->workflow_status === \App\Models\OrderRequest::WORKFLOW_COMPLETED && !$order->awardedOffer?->client_rating)
                    <section class="orders-offer-form">
                        <div class="orders-toolbar-head">
                            <div>
                                <p class="orders-kicker">{{ __('messages.rate_offer') }}</p>
                                <h3 class="orders-card-title">{{ __('messages.rate_offer') }}</h3>
                            </div>
                        </div>
                        <form action="{{ route('orders.rate', $order) }}" method="POST" class="orders-form-layout">
                            @csrf
                            <div class="orders-form-grid">
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="post-complete-rating">{{ __('messages.rating') }}</label>
                                    <select class="orders-filter-select" name="rating" id="post-complete-rating" required>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}">{{ $i }}/5</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="post-complete-review">{{ __('messages.review') }}</label>
                                <textarea class="orders-textarea" name="review" id="post-complete-review"></textarea>
                            </div>
                            <button type="submit" class="button primary">{{ __('messages.rate_offer') }}</button>
                        </form>
                    </section>
                @endif
            @endauth

            <section class="orders-panel">
                <div class="orders-detail-head">
                    <div>
                        <p class="orders-kicker">{{ __('messages.offers') }}</p>
                        <h3 class="orders-card-title">{{ __('messages.order_offers_title') }}</h3>
                        <p class="orders-toolbar-copy">{{ __('messages.order_offers_subtitle') }}</p>
                    </div>
                </div>

                <div class="orders-divider"></div>

                <div class="orders-offer-stack">
                    @forelse($order->offers as $offer)
                        @include('theme::orders.partials.offer-card', ['offer' => $offer, 'order' => $order])
                    @empty
                        <div class="orders-empty">
                            <h4 class="orders-card-title">{{ __('messages.order_no_offers_title') }}</h4>
                            <p class="orders-muted" style="margin-top: 10px;">{{ __('messages.order_no_offers_copy') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
