@extends('theme::layouts.master')

@section('content')
@include('theme::orders.partials.styles')

<div class="section-banner orders-hero" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="create-order-icon">
    <p class="section-banner-title">{{ $isEditing ? __('messages.order_edit_title') : __('messages.post_new_order') }}</p>
    <p class="section-banner-text">{{ $isEditing ? __('messages.order_edit_subtitle') : __('messages.fill_order_details') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <div class="grid-column">
        <div class="orders-form-layout">
            <form action="{{ $isEditing ? route('orders.update', $order) : route('orders.store') }}" method="POST" class="orders-panel">
                @csrf
                @if($isEditing)
                    @method('PATCH')
                @endif

                <div class="orders-toolbar-head" style="margin-bottom: 18px;">
                    <div>
                        <p class="orders-kicker">{{ __('messages.order_requests') }}</p>
                        <h2 class="orders-toolbar-title">{{ $isEditing ? __('messages.order_edit_title') : __('messages.order_form_title') }}</h2>
                        <p class="orders-toolbar-copy">{{ __('messages.order_form_subtitle') }}</p>
                    </div>
                </div>

                <div class="orders-form-section">
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="title">{{ __('messages.title') }}</label>
                        <input class="orders-filter-input" id="title" type="text" name="title" value="{{ old('title', $order->title) }}" required>
                    </div>
                    <div class="orders-filter-field">
                        <label class="orders-filter-label" for="description">{{ __('messages.description') }}</label>
                        <textarea class="orders-textarea" id="description" name="description" required>{{ old('description', $order->description) }}</textarea>
                    </div>
                </div>

                <div class="orders-form-section">
                    <div class="orders-form-grid">
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="category">{{ __('messages.category') }}</label>
                            <select class="orders-filter-select" id="category" name="category">
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" @selected(old('category', $order->category ?: 'uncategorized') === $category->slug)>{{ $category->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="pricing_model">{{ __('messages.pricing') }}</label>
                            <select class="orders-filter-select" id="pricing_model" name="pricing_model">
                                @foreach(['fixed', 'range', 'negotiable'] as $pricingModel)
                                    <option value="{{ $pricingModel }}" @selected(old('pricing_model', $order->pricing_model ?: 'fixed') === $pricingModel)>{{ __('messages.order_pricing_model_' . $pricingModel) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="budget_min">{{ __('messages.order_budget_min') }}</label>
                            <input class="orders-filter-input" id="budget_min" type="number" step="0.01" min="0" name="budget_min" value="{{ old('budget_min', $order->budget_min) }}">
                        </div>
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="budget_max">{{ __('messages.order_budget_max') }}</label>
                            <input class="orders-filter-input" id="budget_max" type="number" step="0.01" min="0" name="budget_max" value="{{ old('budget_max', $order->budget_max) }}">
                        </div>
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="budget_currency">{{ __('messages.currency') }}</label>
                            <select class="orders-filter-select" id="budget_currency" name="budget_currency">
                                @foreach($currencies as $currencyCode => $currencyLabel)
                                    <option value="{{ $currencyCode }}" @selected(old('budget_currency', $order->budget_currency ?: 'USD') === $currencyCode)>{{ $currencyLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="orders-filter-field">
                            <label class="orders-filter-label" for="delivery_window_days">{{ __('messages.delivery') }}</label>
                            <input class="orders-filter-input" id="delivery_window_days" type="number" min="1" max="365" name="delivery_window_days" value="{{ old('delivery_window_days', $order->delivery_window_days) }}">
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul style="margin: 0; padding-inline-start: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('errMSG'))
                    <div class="alert alert-danger mt-3">{{ session('errMSG') }}</div>
                @endif

                <div class="orders-detail-actions" style="margin-top: 18px;">
                    <button type="submit" class="button primary">{{ $isEditing ? __('messages.save_changes') : __('messages.publish_order') }}</button>
                    <a href="{{ $isEditing ? route('orders.show', $order) : route('orders.index') }}" class="button white">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="grid-column">
        <aside class="orders-form-preview">
            <p class="orders-kicker">{{ __('messages.preview') }}</p>
            <h3 class="orders-card-title">{{ old('title', $order->title ?: __('messages.order_preview_title')) }}</h3>
            <p class="orders-muted" style="margin-top: 8px;">{{ __('messages.order_preview_copy') }}</p>
            <div class="orders-divider"></div>
            <div class="orders-summary-grid">
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.category') }}</div>
                    <div class="orders-summary-value">{{ \App\Support\OrderCategoryOptions::label(old('category', $order->category ?: 'uncategorized')) }}</div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.pricing') }}</div>
                    <div class="orders-summary-value">{{ __('messages.order_pricing_model_' . old('pricing_model', $order->pricing_model ?: 'fixed')) }}</div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.budget') }}</div>
                    <div class="orders-summary-value">
                        @php
                            $previewMin = old('budget_min', $order->budget_min);
                            $previewMax = old('budget_max', $order->budget_max);
                            $previewCurrency = old('budget_currency', $order->budget_currency ?: 'USD');
                            $previewPricing = old('pricing_model', $order->pricing_model ?: 'fixed');
                        @endphp
                        @if($previewPricing === 'negotiable' || ($previewMin === null && $previewMax === null))
                            {{ __('messages.order_budget_negotiable') }}
                        @elseif((float) $previewMin === (float) $previewMax)
                            {{ $previewCurrency }} {{ number_format((float) $previewMin, 2) }}
                        @else
                            {{ __('messages.order_budget_range_value', ['currency' => $previewCurrency, 'min' => number_format((float) $previewMin, 2), 'max' => number_format((float) $previewMax, 2)]) }}
                        @endif
                    </div>
                </div>
                <div class="orders-summary-item">
                    <div class="orders-summary-label">{{ __('messages.delivery') }}</div>
                    <div class="orders-summary-value">
                        @if(old('delivery_window_days', $order->delivery_window_days))
                            {{ __('messages.order_delivery_days_value', ['days' => old('delivery_window_days', $order->delivery_window_days)]) }}
                        @else
                            {{ __('messages.order_delivery_flexible') }}
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
