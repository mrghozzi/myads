@extends('theme::layouts.master')

@section('content')
@include('theme::orders.partials.styles')

<div class="orders-form-shell superdesign-post-container">
    
    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero">
        <div class="superdesign-hero-header">
            <div class="superdesign-hero-title-wrap">
                <div class="superdesign-hero-icon-badge">
                    <i class="fa fa-briefcase"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title">
                        {{ $isEditing ? __('messages.order_edit_title') : __('messages.post_new_order') }}
                    </h1>
                    <p class="superdesign-hero-subtitle">
                        {{ $isEditing ? __('messages.order_edit_subtitle') : __('messages.fill_order_details') }}
                    </p>
                </div>
            </div>
            <div class="superdesign-hero-badges">
                <span class="superdesign-pill-badge">
                    <i class="fa fa-tag"></i>
                    {{ $isEditing ? (__('messages.edit') ?? 'تعديل') : (__('messages.new') ?? 'طلب جديد') }}
                </span>
                <span class="superdesign-pill-badge">
                    <i class="fa fa-shopping-bag"></i>
                    {{ __('messages.services_marketplace') ?? 'سوق الخدمات' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Grid Architecture -->
    <div class="superdesign-post-grid">
        <!-- Column 1: Main Order Form -->
        <div class="superdesign-composer-card">
            <form action="{{ $isEditing ? route('orders.update', $order) : route('orders.store') }}" method="POST">
                @csrf
                @if($isEditing)
                    @method('PATCH')
                @endif

                <div class="superdesign-field-group">
                    <label for="title" class="superdesign-field-label">
                        <i class="fa fa-heading"></i>
                        {{ __('messages.title') }}
                    </label>
                    <div class="superdesign-input-wrapper">
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="superdesign-input" 
                            value="{{ old('title', $order->title) }}" 
                            required 
                            placeholder="{{ __('messages.order_title_placeholder') ?? 'عنوان الطلب بشكل واصل ومختصر...' }}"
                        >
                    </div>
                </div>

                <div class="superdesign-field-group">
                    <label for="description" class="superdesign-field-label">
                        <i class="fa fa-align-left"></i>
                        {{ __('messages.description') }}
                    </label>
                    <div class="superdesign-input-wrapper">
                        <textarea 
                            id="description" 
                            name="description" 
                            class="superdesign-textarea" 
                            rows="6" 
                            required 
                            placeholder="{{ __('messages.order_desc_placeholder') ?? 'اكتب تفاصيل طلبك والمتطلبات المرجوة بدقة لتلقي أفضل العروض...' }}"
                        >{{ old('description', $order->description) }}</textarea>
                    </div>
                </div>

                <div class="superdesign-fields-row">
                    <div class="superdesign-field-group">
                        <label for="category" class="superdesign-field-label">
                            <i class="fa fa-folder-open"></i>
                            {{ __('messages.category') }}
                        </label>
                        <select id="category" name="category" class="superdesign-select">
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(old('category', $order->category ?: 'uncategorized') === $category->slug)>
                                    {{ $category->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="superdesign-field-group">
                        <label for="pricing_model" class="superdesign-field-label">
                            <i class="fa fa-calculator"></i>
                            {{ __('messages.pricing') }}
                        </label>
                        <select id="pricing_model" name="pricing_model" class="superdesign-select">
                            @foreach(['fixed', 'range', 'negotiable'] as $pricingModel)
                                <option value="{{ $pricingModel }}" @selected(old('pricing_model', $order->pricing_model ?: 'fixed') === $pricingModel)>
                                    {{ __('messages.order_pricing_model_' . $pricingModel) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="superdesign-fields-row">
                    <div class="superdesign-field-group">
                        <label for="budget_min" class="superdesign-field-label">
                            <i class="fa fa-dollar-sign"></i>
                            {{ __('messages.order_budget_min') }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                id="budget_min" 
                                name="budget_min" 
                                class="superdesign-input" 
                                value="{{ old('budget_min', $order->budget_min) }}"
                            >
                        </div>
                    </div>

                    <div class="superdesign-field-group">
                        <label for="budget_max" class="superdesign-field-label">
                            <i class="fa fa-coins"></i>
                            {{ __('messages.order_budget_max') }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                id="budget_max" 
                                name="budget_max" 
                                class="superdesign-input" 
                                value="{{ old('budget_max', $order->budget_max) }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="superdesign-fields-row">
                    <div class="superdesign-field-group">
                        <label for="budget_currency" class="superdesign-field-label">
                            <i class="fa fa-money-bill-wave"></i>
                            {{ __('messages.currency') }}
                        </label>
                        <select id="budget_currency" name="budget_currency" class="superdesign-select">
                            @foreach($currencies as $currencyCode => $currencyLabel)
                                <option value="{{ $currencyCode }}" @selected(old('budget_currency', $order->budget_currency ?: 'USD') === $currencyCode)>
                                    {{ $currencyLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="superdesign-field-group">
                        <label for="delivery_window_days" class="superdesign-field-label">
                            <i class="fa fa-calendar-alt"></i>
                            {{ __('messages.delivery') }} (أيام)
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="number" 
                                min="1" 
                                max="365" 
                                id="delivery_window_days" 
                                name="delivery_window_days" 
                                class="superdesign-input" 
                                value="{{ old('delivery_window_days', $order->delivery_window_days) }}" 
                                placeholder="30"
                            >
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger mb-4" role="alert" style="border-radius: 14px;">
                        <ul style="margin: 0; padding-inline-start: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('errMSG'))
                    <div class="alert alert-danger mb-4" style="border-radius: 14px;">{{ session('errMSG') }}</div>
                @endif

                <!-- Action Buttons Bar -->
                <div class="superdesign-actions-bar">
                    <a href="{{ $isEditing ? route('orders.show', $order) : route('orders.index') }}" class="superdesign-btn-secondary">
                        <i class="fa fa-times"></i>
                        {{ __('messages.cancel') }}
                    </a>

                    <button type="submit" class="superdesign-btn-primary">
                        <i class="fa fa-paper-plane"></i>
                        {{ $isEditing ? __('messages.save_changes') : __('messages.publish_order') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 2: Sidebar Live Preview & Widgets -->
        <div class="superdesign-sidebar-col">
            <!-- Live Order Preview Card -->
            <div class="superdesign-sidebar-card">
                <h3 class="superdesign-sidebar-title">
                    <i class="fa fa-eye"></i>
                    {{ __('messages.preview') }}
                </h3>
                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                    {{ old('title', $order->title ?: __('messages.order_preview_title')) }}
                </h4>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
                    {{ __('messages.order_preview_copy') }}
                </p>
                <div style="height: 1px; background: #f1f5f9; margin-bottom: 16px;"></div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b;">{{ __('messages.category') }}:</span>
                        <span style="font-weight: 600; color: #1e293b;">
                            {{ \App\Support\OrderCategoryOptions::label(old('category', $order->category ?: 'uncategorized')) }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b;">{{ __('messages.pricing') }}:</span>
                        <span style="font-weight: 600; color: #1e293b;">
                            {{ __('messages.order_pricing_model_' . old('pricing_model', $order->pricing_model ?: 'fixed')) }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b;">{{ __('messages.budget') }}:</span>
                        <span style="font-weight: 600; color: #615dfa;">
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
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b;">{{ __('messages.delivery') }}:</span>
                        <span style="font-weight: 600; color: #10b981;">
                            @if(old('delivery_window_days', $order->delivery_window_days))
                                {{ __('messages.order_delivery_days_value', ['days' => old('delivery_window_days', $order->delivery_window_days)]) }}
                            @else
                                {{ __('messages.order_delivery_flexible') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Widget Column -->
            <x-widget-column side="portal_left" />
        </div>
    </div>
</div>

<style>
/* --- @.superdesign Core Tokens for /orders/create --- */
.superdesign-post-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 15px 60px;
}

/* Glassmorphic Hero Banner */
.superdesign-post-hero {
    position: relative;
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.14) 0%, rgba(35, 210, 226, 0.09) 50%, rgba(27, 200, 219, 0.03) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 28px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.superdesign-post-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(97, 93, 250, 0.25) 0%, rgba(97, 93, 250, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.superdesign-hero-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.superdesign-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.superdesign-hero-icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.35);
    flex-shrink: 0;
}
.superdesign-hero-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px 0;
    color: #1e293b;
    letter-spacing: -0.02em;
}
.superdesign-hero-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}
.superdesign-hero-badges {
    display: flex;
    align-items: center;
    gap: 10px;
}
.superdesign-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(97, 93, 250, 0.25);
    color: #615dfa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

/* Grid Architecture */
.superdesign-post-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 28px;
}
@media (max-width: 991px) {
    .superdesign-post-grid {
        grid-template-columns: 1fr;
    }
}

/* Main Composer Card */
.superdesign-composer-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 28px;
}

.superdesign-fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .superdesign-fields-row {
        grid-template-columns: 1fr;
    }
}

.superdesign-field-group {
    margin-bottom: 22px;
}
.superdesign-field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}
.superdesign-field-label i {
    color: #615dfa;
}
.superdesign-input-wrapper {
    position: relative;
}
.superdesign-input {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
}
.superdesign-input:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-textarea {
    width: 100%;
    min-height: 140px;
    padding: 12px 16px;
    font-size: 14.5px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
    resize: vertical;
}
.superdesign-textarea:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-select {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 14.5px;
    font-weight: 600;
    color: #334155;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%20615dfa' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat calc(100% - 16px) center;
    background-size: 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    cursor: pointer;
    transition: all 0.25s ease;
}
html[dir="rtl"] .superdesign-select {
    background-position: 16px center;
}
.superdesign-select:focus {
    background-color: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}

/* Form Actions */
.superdesign-actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}
.superdesign-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    border: none;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.3);
    cursor: pointer;
    transition: all 0.25s ease;
}
.superdesign-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(97, 93, 250, 0.4);
    color: #ffffff;
}
.superdesign-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    text-decoration: none;
    transition: all 0.2s ease;
}
.superdesign-btn-secondary:hover {
    background: #e2e8f0;
    color: #334155;
}

/* Sidebar Widgets */
.superdesign-sidebar-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 22px;
    margin-bottom: 20px;
}
.superdesign-sidebar-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.superdesign-sidebar-title i {
    color: #615dfa;
}

/* --- Dark Mode Parity --- */
body[data-theme="css_d"] .superdesign-post-hero,
html.app-skin-dark .superdesign-post-hero,
.dark-mode .superdesign-post-hero {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.1) 100%), #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
}
body[data-theme="css_d"] .superdesign-hero-title,
html.app-skin-dark .superdesign-hero-title,
.dark-mode .superdesign-hero-title {
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-hero-subtitle,
html.app-skin-dark .superdesign-hero-subtitle,
.dark-mode .superdesign-hero-subtitle {
    color: #94a3b8;
}
body[data-theme="css_d"] .superdesign-pill-badge,
html.app-skin-dark .superdesign-pill-badge,
.dark-mode .superdesign-pill-badge {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(97, 93, 250, 0.35);
    color: #818cf8;
}

body[data-theme="css_d"] .superdesign-composer-card,
body[data-theme="css_d"] .superdesign-sidebar-card,
html.app-skin-dark .superdesign-composer-card,
html.app-skin-dark .superdesign-sidebar-card,
.dark-mode .superdesign-composer-card,
.dark-mode .superdesign-sidebar-card {
    background: #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
body[data-theme="css_d"] .superdesign-field-label,
body[data-theme="css_d"] .superdesign-sidebar-title,
html.app-skin-dark .superdesign-field-label,
html.app-skin-dark .superdesign-sidebar-title,
.dark-mode .superdesign-field-label,
.dark-mode .superdesign-sidebar-title {
    color: #f1f5f9;
}
body[data-theme="css_d"] .superdesign-input,
body[data-theme="css_d"] .superdesign-textarea,
body[data-theme="css_d"] .superdesign-select,
html.app-skin-dark .superdesign-input,
html.app-skin-dark .superdesign-textarea,
html.app-skin-dark .superdesign-select,
.dark-mode .superdesign-input,
.dark-mode .superdesign-textarea,
.dark-mode .superdesign-select {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-input:focus,
body[data-theme="css_d"] .superdesign-textarea:focus,
body[data-theme="css_d"] .superdesign-select:focus,
html.app-skin-dark .superdesign-input:focus,
html.app-skin-dark .superdesign-textarea:focus,
html.app-skin-dark .superdesign-select:focus,
.dark-mode .superdesign-input:focus,
.dark-mode .superdesign-textarea:focus,
.dark-mode .superdesign-select:focus {
    background-color: #1e293b;
    border-color: #615dfa;
}
body[data-theme="css_d"] .superdesign-actions-bar,
html.app-skin-dark .superdesign-actions-bar,
.dark-mode .superdesign-actions-bar {
    border-top-color: #334155;
}
body[data-theme="css_d"] .superdesign-btn-secondary,
html.app-skin-dark .superdesign-btn-secondary,
.dark-mode .superdesign-btn-secondary {
    background: #334155;
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-btn-secondary:hover,
html.app-skin-dark .superdesign-btn-secondary:hover,
.dark-mode .superdesign-btn-secondary:hover {
    background: #475569;
    color: #ffffff;
}
</style>
@endsection
