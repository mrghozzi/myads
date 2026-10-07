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
            <p class="orders-kicker" style="margin-bottom: 20px;">{{ __('messages.client_info') }}</p>
            <div class="user-short-description orders-owner-card">
                <a class="user-avatar medium {{ $order->user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $order->user->username) }}">
                    <div class="user-avatar-border">
                        <div class="hexagon-120-132" style="width: 70px; height: 77px; position: relative;"></div>
                    </div>
                    <div class="user-avatar-content">
                        <div class="hexagon-image-82-90" data-src="{{ $order->user->avatarUrl() }}" style="width: 48px; height: 53px; position: relative;"></div>
                    </div>
                    <div class="user-avatar-progress-border">
                        <div class="hexagon-border-100-110" data-line-color="{{ $order->user->profileBadgeColor() }}" style="width: 58px; height: 64px; position: relative;"></div>
                    </div>

                    @if($order->user->hasVerifiedBadge())
                        <div class="user-avatar-badge">
                            <div class="user-avatar-badge-border">
                                <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                            </div>
                            <div class="user-avatar-badge-content">
                                <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                            </div>
                            <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                        </div>
                    @endif
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
            {{-- Stepper Progress Tracker --}}
            @php
                $stepStates = [
                    'created' => 'completed',
                    'offers' => match($order->workflow_status) {
                        'open' => 'active',
                        'cancelled', 'closed' => '',
                        default => 'completed',
                    },
                    'awarded' => match($order->workflow_status) {
                        'awarded' => 'active',
                        'in_progress', 'delivered', 'completed' => 'completed',
                        default => '',
                    },
                    'in_progress' => match($order->workflow_status) {
                        'in_progress' => 'active',
                        'delivered', 'completed' => 'completed',
                        default => '',
                    },
                    'delivered' => match($order->workflow_status) {
                        'delivered' => 'active',
                        'completed' => 'completed',
                        default => '',
                    },
                    'completed' => match($order->workflow_status) {
                        'completed' => 'completed active',
                        default => '',
                    },
                ];
            @endphp
            <div class="orders-stepper">
                <div class="orders-step {{ $stepStates['created'] }}">
                    <div class="orders-step-circle"><i class="fa fa-check"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_created') }}</span>
                </div>
                <div class="orders-step {{ $stepStates['offers'] }}">
                    <div class="orders-step-circle"><i class="fa fa-handshake"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_offers') }}</span>
                </div>
                <div class="orders-step {{ $stepStates['awarded'] }}">
                    <div class="orders-step-circle"><i class="fa fa-award"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_awarded') }}</span>
                </div>
                <div class="orders-step {{ $stepStates['in_progress'] }}">
                    <div class="orders-step-circle"><i class="fa fa-tools"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_in_progress') }}</span>
                </div>
                <div class="orders-step {{ $stepStates['delivered'] }}">
                    <div class="orders-step-circle"><i class="fa fa-box-open"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_delivered') }}</span>
                </div>
                <div class="orders-step {{ $stepStates['completed'] }}">
                    <div class="orders-step-circle"><i class="fa fa-star"></i></div>
                    <span class="orders-step-label">{{ __('messages.order_stepper_completed') }}</span>
                </div>
            </div>

            {{-- Disclaimer Card --}}
            <div class="orders-disclaimer-card">
                <div class="orders-disclaimer-icon">
                    <i class="fa fa-shield-alt"></i>
                </div>
                <div class="orders-disclaimer-content">
                    <h4 class="orders-disclaimer-title">{{ __('messages.order_disclaimer_title') }}</h4>
                    <p class="orders-disclaimer-text">{{ __('messages.order_disclaimer_notice', ['site' => $site_settings->titer ?? config('app.name', 'MyAds')]) }}</p>
                </div>
            </div>

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

                @if($order->hasAttachment())
                    <div class="orders-attachment-card">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fa fa-paperclip orders-attachment-icon"></i>
                            <div>
                                <div style="font-weight: 700; color: #1f2440; font-size: 0.9rem;">{{ $order->attachment_name ?: __('messages.order_attachment') }}</div>
                                <span class="orders-muted" style="font-size: 0.8rem;">{{ __('messages.order_attachment') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('orders.attachment.download', $order) }}" class="button primary small">
                            <i class="fa fa-download" style="margin-inline-end: 6px;"></i> {{ __('messages.order_download_attachment') }}
                        </a>
                    </div>
                @endif

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
                        @if($order->contract->deadline)
                            <div class="orders-summary-item">
                                <div class="orders-summary-label">{{ __('messages.order_deadline') }}</div>
                                <div class="orders-summary-value" style="{{ $order->contract->isOverdue() ? 'color: #e74c3c; font-weight: 800;' : '' }}">
                                    {{ $order->contract->deadline->diffForHumans() }}
                                    @if($order->contract->isOverdue())
                                        ({{ __('messages.order_overdue') }})
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($order->contract->delivery_note && (
                        (int) auth()->id() === (int) $order->uid || 
                        (int) auth()->id() === (int) $order->contract->provider_user_id || 
                        (auth()->check() && auth()->user()->isAdmin())
                    ))
                        <div style="margin-top: 20px; padding: 15px; background: rgba(63, 154, 229, 0.05); border: 1px dashed #3f9ae5; border-radius: 8px;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #3f9ae5; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                <i class="fa fa-fw fa-box-open"></i> {{ __('messages.notes') }} ({{ __('messages.delivery') }})
                            </h4>
                            <p style="margin: 0; font-size: 14px; color: #3e3f5e; line-height: 1.6; white-space: pre-wrap;">{!! nl2br(e($order->contract->delivery_note)) !!}</p>
                        </div>
                    @endif

                    @if($order->contract->hasDeliveryAttachment() && (
                        (int) auth()->id() === (int) $order->uid || 
                        (int) auth()->id() === (int) $order->contract->provider_user_id || 
                        (auth()->check() && auth()->user()->isAdmin())
                    ))
                        <div class="orders-attachment-card" style="border-color: #23d2e2; background: rgba(35, 210, 226, 0.05);">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fa fa-file-archive orders-attachment-icon" style="color: #23d2e2;"></i>
                                <div>
                                    <div style="font-weight: 700; color: #1f2440; font-size: 0.9rem;">{{ $order->contract->delivery_attachment_name ?: __('messages.order_deliverable_attachment') }}</div>
                                    <span class="orders-muted" style="font-size: 0.8rem;">{{ __('messages.order_deliverable_attachment') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('orders.deliverable.download', $order) }}" class="button secondary small">
                                <i class="fa fa-download" style="margin-inline-end: 6px;"></i> {{ __('messages.order_download_deliverable') }}
                            </a>
                        </div>
                    @endif

                    @if($order->contract->hasRevision())
                        <div class="orders-revision-box">
                            <div style="font-weight: 800; color: #e67e22; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <i class="fa fa-redo"></i> {{ __('messages.order_revision_note') }} ({{ __('messages.order_revision_badge', ['count' => $order->contract->revision_count]) }})
                            </div>
                            <p style="margin: 0; color: #2a2f47; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{!! nl2br(e($order->contract->revision_note)) !!}</p>
                        </div>
                    @endif
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
                                <div class="orders-muted small" style="margin-top: 6px; color: #e67e22; font-weight: 600;">
                                    <i class="fa fa-shield-alt"></i> {{ __('messages.order_disclaimer_short') }}
                                </div>
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
                                            <option value="{{ $pricingModel }}" @selected(old('pricing_model', $viewerOffer?->pricing_model ?? 'fixed') === $pricingModel)>{{ __('messages.order_pricing_model_' . $pricingModel) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="quoted_amount">{{ __('messages.price') }}</label>
                                    <input class="orders-filter-input" type="number" step="0.01" min="0" id="quoted_amount" name="quoted_amount" value="{{ old('quoted_amount', $viewerOffer?->quoted_amount) }}">
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="currency_code">{{ __('messages.currency') }}</label>
                                    <select class="orders-filter-select" name="currency_code" id="currency_code">
                                        @foreach($currencies as $currencyCode => $currencyLabel)
                                            <option value="{{ $currencyCode }}" @selected(old('currency_code', $viewerOffer?->currency_code ?? $order->budget_currency ?? 'USD') === $currencyCode)>{{ $currencyLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label class="orders-filter-label" for="delivery_days">{{ __('messages.delivery') }}</label>
                                    <input class="orders-filter-input" type="number" min="1" max="365" id="delivery_days" name="delivery_days" value="{{ old('delivery_days', $viewerOffer?->delivery_days) }}">
                                </div>
                            </div>

                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="message">{{ __('messages.description') }}</label>
                                <textarea class="orders-textarea" name="message" id="message" required>{{ old('message', $viewerOffer?->message) }}</textarea>
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

                    {{-- Request Revision Form --}}
                    <section class="orders-offer-form" style="margin-top: 18px; border-color: rgba(255, 174, 0, 0.4);">
                        <div class="orders-toolbar-head">
                            <div>
                                <p class="orders-kicker" style="color: #e67e22;">{{ __('messages.order_request_revision') }}</p>
                                <h3 class="orders-card-title">{{ __('messages.order_request_revision') }}</h3>
                                <p class="orders-toolbar-copy">{{ __('messages.order_revision_note') }}</p>
                            </div>
                        </div>

                        <form action="{{ route('orders.revision', $order) }}" method="POST" class="orders-form-layout">
                            @csrf
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="revision_note">{{ __('messages.order_revision_note') }}</label>
                                <textarea class="orders-textarea" name="revision_note" id="revision_note" required placeholder="{{ __('messages.order_revision_note') }}..."></textarea>
                            </div>
                            <button type="submit" class="button secondary">{{ __('messages.order_revision_action') }}</button>
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
                        <form action="{{ route('orders.deliver', $order) }}" method="POST" enctype="multipart/form-data" class="orders-form-layout">
                            @csrf
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="delivery_note">{{ __('messages.notes') }}</label>
                                <textarea class="orders-textarea" name="delivery_note" id="delivery_note" placeholder="{{ __('messages.notes') }}..."></textarea>
                            </div>
                            <div class="orders-filter-field">
                                <label class="orders-filter-label" for="delivery_attachment">{{ __('messages.order_deliverable_attachment') }}</label>
                                <input type="file" name="delivery_attachment" id="delivery_attachment" class="orders-filter-input" style="padding-top: 10px;">
                                <small class="orders-muted" style="margin-top: 6px; font-size: 12px;">{{ __('messages.order_deliverable_help') }}</small>
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
