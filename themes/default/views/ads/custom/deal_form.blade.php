@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

@php
    $isInvite = $source === \App\Models\CustomAdDeal::SOURCE_INVITE;
    $action = $isInvite
        ? route('ads.custom.placements.invite.store', $placement)
        : route('ads.custom.placements.request.store', $placement);
@endphp

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #0ea5e9 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-deal">
    <p class="section-banner-title">{{ $isInvite ? __('messages.custom_ads_invite') : __('messages.custom_ads_request_deal') }}</p>
    <p class="section-banner-text">{{ $placement->name }}</p>
</div>

<div class="custom-ads-toolbar">
    <a class="custom-ads-pill" href="{{ $isInvite ? route('ads.custom.index') : route('ads.custom.marketplace') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.back') }}</a>
</div>

<div class="custom-ads-shell">
    <div class="custom-ads-card">
        <div class="custom-ads-pills" style="margin-bottom: 10px;">
            <span class="custom-ads-pill">{{ __('messages.custom_ads_format_' . $placement->format) }}</span>
            <span class="custom-ads-pill">{{ $placement->size }}</span>
        </div>
        <h4>{{ $placement->name }}</h4>
        <p class="custom-ads-muted">{{ $placement->description }}</p>
        <div class="custom-ads-muted">{{ __('messages.publisher') }}: {{ $placement->user?->username }}</div>
    </div>

    <div class="widget-box">
        <form method="POST" action="{{ $action }}">
            @csrf

            @if($isInvite)
                <div class="form-item" style="margin-bottom: 18px;">
                    <label class="rl-label">{{ __('messages.custom_ads_advertiser_lookup') }}</label>
                    <input type="text" name="advertiser" value="{{ old('advertiser') }}" required placeholder="username@example.com">
                    @error('advertiser')<p class="error">{{ $message }}</p>@enderror
                </div>
            @endif

            <div class="custom-ads-form-grid">
                <div class="form-item">
                    <label class="rl-label">{{ __('messages.custom_ads_payment_type') }}</label>
                    <select name="payment_type" id="custom-ad-payment-type" required>
                        <option value="{{ \App\Models\CustomAdDeal::PAYMENT_PTS_DAILY }}" @selected(old('payment_type', $deal->payment_type) === \App\Models\CustomAdDeal::PAYMENT_PTS_DAILY)>{{ __('messages.custom_ads_pts_daily') }}</option>
                        <option value="{{ \App\Models\CustomAdDeal::PAYMENT_EXTERNAL }}" @selected(old('payment_type', $deal->payment_type) === \App\Models\CustomAdDeal::PAYMENT_EXTERNAL)>{{ __('messages.custom_ads_external') }}</option>
                    </select>
                </div>
                <div class="form-item custom-ad-pts-field">
                    <label class="rl-label">{{ __('messages.custom_ads_daily_pts') }}</label>
                    <input type="number" name="daily_pts" min="0" step="0.01" value="{{ old('daily_pts', $deal->daily_pts) }}">
                    @error('daily_pts')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="form-item custom-ad-external-field">
                    <label class="rl-label">{{ __('messages.custom_ads_external_amount') }}</label>
                    <input type="number" name="external_amount" min="0" step="0.01" value="{{ old('external_amount', $deal->external_amount) }}">
                    @error('external_amount')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="form-item custom-ad-external-field">
                    <label class="rl-label">{{ __('messages.currency') }}</label>
                    <input type="text" name="external_currency" maxlength="8" value="{{ old('external_currency', $deal->external_currency ?: 'USD') }}">
                </div>
                <div class="form-item">
                    <label class="rl-label">{{ __('messages.start_date') }}</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', optional($deal->starts_at)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-item">
                    <label class="rl-label">{{ __('messages.end_date') }}</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', optional($deal->ends_at)->format('Y-m-d')) }}" required>
                    <p class="custom-ads-muted">{{ __('messages.custom_ads_max_duration', ['days' => $maxDurationDays]) }}</p>
                    @error('ends_at')<p class="error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-item custom-ad-external-field" style="margin-top: 18px;">
                <label class="rl-label">{{ __('messages.custom_ads_external_note') }}</label>
                <textarea name="external_note" rows="3">{{ old('external_note', $deal->external_note) }}</textarea>
            </div>

            <div class="form-item" style="margin-top: 18px;">
                <label class="rl-label">{{ __('messages.custom_ads_terms') }}</label>
                <textarea name="terms" rows="3">{{ old('terms', $deal->terms) }}</textarea>
            </div>

            <div class="custom-ads-card" style="margin-top: 22px;">
                <h4>{{ __('messages.custom_ads_creative') }}</h4>
                <div class="custom-ads-form-grid">
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.title') }}</label>
                        <input type="text" name="headline" value="{{ old('headline', $creative->headline) }}" required>
                        @error('headline')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.url') }}</label>
                        <input type="url" name="target_url" value="{{ old('target_url', $creative->target_url) }}" required placeholder="https://example.com">
                        @error('target_url')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.img') }}</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $creative->image_url) }}" placeholder="https://example.com/banner.png">
                    </div>
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.custom_ads_button_label') }}</label>
                        <input type="text" name="button_label" value="{{ old('button_label', $creative->button_label ?: __('messages.custom_ads_learn_more')) }}">
                    </div>
                </div>

                <div class="form-item" style="margin-top: 18px;">
                    <label class="rl-label">{{ __('messages.description') }}</label>
                    <textarea name="body" rows="3">{{ old('body', $creative->body) }}</textarea>
                </div>

                <div class="custom-ads-form-grid" style="margin-top: 18px;">
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.custom_ads_background_color') }}</label>
                        <input type="color" name="background_color" value="{{ old('background_color', $creative->background_color ?: '#ffffff') }}">
                    </div>
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.custom_ads_text_color') }}</label>
                        <input type="color" name="text_color" value="{{ old('text_color', $creative->text_color ?: '#1f2937') }}">
                    </div>
                    <div class="form-item">
                        <label class="rl-label">{{ __('messages.custom_ads_accent_color') }}</label>
                        <input type="color" name="accent_color" value="{{ old('accent_color', $creative->accent_color ?: '#615dfa') }}">
                    </div>
                </div>
            </div>

            <div class="custom-ads-actions" style="margin-top: 22px;">
                <button type="submit" class="button secondary">{{ $isInvite ? __('messages.custom_ads_send_invite') : __('messages.custom_ads_send_request') }}</button>
                <a href="{{ route('ads.custom.index') }}" class="button tertiary">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var payment = document.getElementById('custom-ad-payment-type');
    function syncPaymentFields() {
        var external = payment && payment.value === '{{ \App\Models\CustomAdDeal::PAYMENT_EXTERNAL }}';
        document.querySelectorAll('.custom-ad-external-field').forEach(function (node) { node.style.display = external ? '' : 'none'; });
        document.querySelectorAll('.custom-ad-pts-field').forEach(function (node) { node.style.display = external ? 'none' : ''; });
    }
    if (payment) {
        payment.addEventListener('change', syncPaymentFields);
        syncPaymentFields();
    }
});
</script>
@endsection
