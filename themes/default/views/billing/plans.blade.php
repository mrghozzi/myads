@extends('theme::layouts.master')

@section('content')
@php
    $enabledGateways = collect($gatewayDefinitions)->filter(fn ($gateway) => !empty($gateway['config']['enabled']))->values();
    $baseCurrencyCode = \App\Support\SubscriptionSettings::get('base_currency_code', 'USD');
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-crown" style="font-size: 28px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.billing_plans_title') }}</p>
    <p class="section-banner-text">{{ __('messages.billing_plans_description') }}</p>
</div>

<div class="content-grid">
    @include('theme::billing.partials.alerts')

    @if(auth()->check() && $currentSubscription)
        <div class="widget-box" style="margin-bottom: 20px;">
            <div class="widget-box-content" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div>
                        <p class="widget-box-title" style="margin-bottom: 6px;">{{ __('messages.billing_current_subscription_title') }}</p>
                        <p class="user-status-title" style="font-size: 22px;">{{ $currentSubscription->plan_name }}</p>
                        <p class="user-status-text">{{ __('messages.billing_ends_at_label') }}: {{ optional($currentSubscription->ends_at)->format('Y-m-d H:i') ?: __('messages.billing_lifetime') }}</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        @include('theme::billing.partials.status_badge', ['status' => $currentSubscription->status])
                        <a href="{{ route('billing.dashboard') }}" class="button secondary">{{ __('messages.billing_dashboard_title') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="widget-box" style="margin-bottom: 20px;">
        <div class="widget-box-content" style="padding: 28px;">
            <form action="{{ route('billing.plans') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div>
                    <p class="widget-box-title" style="margin-bottom: 4px;">{{ __('messages.billing_compare_plans') }}</p>
                    <p class="user-status-text">{{ __('messages.billing_catalog_help') }}</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" style="min-width: 240px;">
                    <button type="submit" class="button primary">{{ __('messages.search') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($enabledGateways->isEmpty())
        <div class="alert alert-warning" role="alert">{{ __('messages.billing_no_gateways_available') }}</div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 20px;">
        @forelse($plans as $plan)
            @php
                $entitlements = array_merge([
                    'profile_badge_label' => '',
                    'bonus_pts' => 0,
                    'bonus_nvu' => 0,
                    'bonus_nlink' => 0,
                    'bonus_nsmart' => 0,
                    'status_promotion_discount_pct' => 0,
                ], (array) ($plan->entitlements ?? []));
                $benefits = [];
                if(trim((string) ($entitlements['profile_badge_label'] ?? '')) !== '') $benefits[] = __('messages.billing_profile_badge_benefit', ['label' => $entitlements['profile_badge_label']]);
                if((float) ($entitlements['bonus_pts'] ?? 0) > 0) $benefits[] = __('messages.billing_bonus_pts_benefit', ['amount' => $entitlements['bonus_pts']]);
                if((float) ($entitlements['bonus_nvu'] ?? 0) > 0) $benefits[] = __('messages.billing_bonus_nvu_benefit', ['amount' => $entitlements['bonus_nvu']]);
                if((float) ($entitlements['bonus_nlink'] ?? 0) > 0) $benefits[] = __('messages.billing_bonus_nlink_benefit', ['amount' => $entitlements['bonus_nlink']]);
                if((float) ($entitlements['bonus_nsmart'] ?? 0) > 0) $benefits[] = __('messages.billing_bonus_nsmart_benefit', ['amount' => $entitlements['bonus_nsmart']]);
                if((float) ($entitlements['status_promotion_discount_pct'] ?? 0) > 0) $benefits[] = __('messages.billing_discount_benefit', ['amount' => $entitlements['status_promotion_discount_pct']]);
            @endphp
            <div class="widget-box" style="overflow: hidden; border-top: 4px solid {{ $plan->accent_color ?: '#615dfa' }};">
                <div class="widget-box-content" style="padding: 28px;">
                    <div style="display: flex; justify-content: space-between; gap: 10px; align-items: flex-start;">
                        <div>
                            @if($plan->recommended_text)
                                <p style="display: inline-flex; margin-bottom: 12px; background: rgba(97, 93, 250, 0.12); color: {{ $plan->accent_color ?: '#615dfa' }}; border-radius: 999px; padding: 6px 12px; font-size: 12px; font-weight: 700;">
                                    {{ $plan->recommended_text }}
                                </p>
                            @endif
                            <p class="widget-box-title" style="margin-bottom: 6px;">{{ $plan->name }}</p>
                            <p class="user-status-text">{{ $plan->description }}</p>
                        </div>
                        @if($plan->is_featured)
                            <span style="display: inline-flex; padding: 6px 12px; border-radius: 999px; background: #eef6ff; color: #2563eb; font-size: 12px; font-weight: 700;">
                                {{ __('messages.billing_featured_plan') }}
                            </span>
                        @endif
                    </div>

                    <div style="margin: 22px 0;">
                        <div style="font-size: 34px; font-weight: 800; color: var(--heading-color);">{{ number_format((float) $plan->base_price, 2) }}</div>
                        <div class="user-status-text">{{ $baseCurrencyCode }} · {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}</div>
                    </div>

                    @if(!empty($plan->marketing_bullets))
                        <div style="margin-bottom: 20px;">
                            <p class="widget-box-title" style="font-size: 16px; margin-bottom: 12px;">{{ __('messages.billing_plan_highlights_title') }}</p>
                            <ul style="padding-left: 18px; margin: 0; display: grid; gap: 8px;">
                                @foreach((array) $plan->marketing_bullets as $bullet)
                                    <li>{{ $bullet }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($benefits))
                        <div style="margin-bottom: 20px;">
                            <p class="widget-box-title" style="font-size: 16px; margin-bottom: 12px;">{{ __('messages.billing_plan_benefits_title') }}</p>
                            <ul style="padding-left: 18px; margin: 0; display: grid; gap: 8px;">
                                @foreach($benefits as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @auth
                        @if($systemEnabled && $enabledGateways->isNotEmpty())
                            <form action="{{ route('billing.purchase', $plan->id) }}" method="POST" class="billing-purchase-form" style="display: grid; gap: 12px;">
                                @csrf
                                <div>
                                    <label class="form-label">{{ __('messages.billing_choose_gateway') }}</label>
                                    <select name="gateway" class="billing-gateway-select" required>
                                        @foreach($enabledGateways as $index => $gateway)
                                            <option value="{{ $gateway['key'] }}" data-supported-currencies="{{ implode(',', (array) ($gateway['supported_currencies'] ?? [])) }}" @selected(old('gateway') === $gateway['key'] || ($index === 0 && old('gateway') === null))>
                                                {{ $gateway['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('messages.billing_choose_currency') }}</label>
                                    <select name="currency_code" class="billing-currency-select" required>
                                        @foreach($activeCurrencies as $currency)
                                            <option value="{{ $currency->code }}" @selected(old('currency_code') === $currency->code)>{{ $currency->code }}{{ $currency->symbol ? ' · ' . $currency->symbol : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="button primary">{{ __('messages.billing_purchase_now') }}</button>
                            </form>
                        @else
                            <div class="alert alert-info" role="alert">{{ __('messages.billing_system_disabled_member_notice') }}</div>
                        @endif
                    @else
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="{{ route('login') }}" class="button primary">{{ __('messages.billing_sign_in_to_purchase') }}</a>
                            <a href="{{ route('register') }}" class="button secondary">{{ __('messages.register') }}</a>
                        </div>
                    @endauth
                </div>
            </div>
        @empty
            <div class="widget-box">
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="user-status-text">{{ __('messages.no_data') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.billing-purchase-form');

        forms.forEach(function (form) {
            const gatewaySelect = form.querySelector('.billing-gateway-select');
            const currencySelect = form.querySelector('.billing-currency-select');
            if (!gatewaySelect || !currencySelect) {
                return;
            }

            const originalOptions = Array.from(currencySelect.options).map(function (option) {
                return { value: option.value, text: option.text, selected: option.selected };
            });

            const syncCurrencies = function () {
                const supported = (gatewaySelect.options[gatewaySelect.selectedIndex]?.dataset.supportedCurrencies || '')
                    .split(',')
                    .map(function (item) { return item.trim(); })
                    .filter(Boolean);
                const previousValue = currencySelect.value;

                currencySelect.innerHTML = '';

                originalOptions.forEach(function (optionData) {
                    if (supported.length && !supported.includes(optionData.value)) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    option.selected = optionData.value === previousValue || (!previousValue && optionData.selected);
                    currencySelect.appendChild(option);
                });

                if (!currencySelect.value && currencySelect.options.length) {
                    currencySelect.selectedIndex = 0;
                }
            };

            gatewaySelect.addEventListener('change', syncCurrencies);
            syncCurrencies();
        });
    });
</script>
@endpush
