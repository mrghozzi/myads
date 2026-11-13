@extends('theme::layouts.master')

@section('content')
@php
    $enabledGateways = collect($gatewayDefinitions)->filter(fn ($gateway) => !empty($gateway['config']['enabled']))->values();
    $baseCurrencyCode = \App\Support\SubscriptionSettings::get('base_currency_code', 'USD');
    $entitlementService = app(\App\Services\Billing\SubscriptionEntitlementService::class);
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-crown" style="font-size: 28px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.billing_plans_title') }}</p>
    <p class="section-banner-text">{{ __('messages.billing_plans_description', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
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
                $benefits = $entitlementService->memberBenefitLines((array) ($plan->entitlements ?? []));
                $layoutStyle = $plan->layout_style ?? 'default';
                $customCardStyle = '';
                if ($layoutStyle === 'glassmorphism') {
                    $customCardStyle = 'background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);';
                } elseif ($layoutStyle === 'neon_glow') {
                    $accent = $plan->accent_color ?: '#615dfa';
                    $customCardStyle = "box-shadow: 0 0 25px {$accent}40; border: 2px solid {$accent};";
                } elseif ($layoutStyle === 'minimal') {
                    $customCardStyle = 'box-shadow: none; border: 1px solid var(--border-color, #e2e8f0);';
                }
                $cardClass = function_exists('apply_filters') ? apply_filters('billing_plans_card_class', 'widget-box', $plan) : 'widget-box';
            @endphp
            <div class="{{ $cardClass }}" style="overflow: hidden; border-top: 4px solid {{ $plan->accent_color ?: '#615dfa' }}; {{ $customCardStyle }}">
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
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                            @if($plan->is_featured)
                                <span style="display: inline-flex; padding: 6px 12px; border-radius: 999px; background: #eef6ff; color: #2563eb; font-size: 12px; font-weight: 700;">
                                    {{ __('messages.billing_featured_plan') }}
                                </span>
                            @endif
                            @if(function_exists('do_action'))
                                @php(do_action('billing_plans_card_badge', $plan))
                            @endif
                        </div>
                    </div>

                    <div style="margin: 22px 0;">
                        @if($plan->is_on_sale)
                            <div style="display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap;">
                                <span style="font-size: 20px; text-decoration: line-through; color: #94a3b8;">{{ number_format((float) $plan->base_price, 2) }}</span>
                                <span style="font-size: 34px; font-weight: 800; color: #ef4444;">{{ number_format((float) $plan->sale_price, 2) }}</span>
                                <span style="padding: 3px 8px; border-radius: 999px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 11px; font-weight: 800;">
                                    -{{ $plan->discount_percentage }}% {{ __('messages.billing_sale_tag') }}
                                </span>
                            </div>
                            @if($plan->sale_ends_at)
                                <small style="color: #ef4444; font-size: 11px; display: block; margin-top: 4px;">
                                    <i class="fa fa-clock me-1"></i>{{ __('messages.billing_sale_ends_in') }}: {{ $plan->sale_ends_at->diffForHumans() }}
                                </small>
                            @endif
                        @else
                            <div style="font-size: 34px; font-weight: 800; color: var(--heading-color);">{{ number_format((float) $plan->base_price, 2) }}</div>
                        @endif
                        <div class="user-status-text">{{ $baseCurrencyCode }} · {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}</div>
                    </div>

                    {{-- Free Trial Banner / CTA --}}
                    @if($plan->has_trial)
                        <div style="margin-bottom: 18px; padding: 12px 14px; background: rgba(16, 185, 129, 0.08); border: 1px dashed #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div>
                                <span style="color: #10b981; font-weight: 700; font-size: 13px;">
                                    <i class="fa fa-gift me-1"></i> {{ $plan->trial_days }} {{ __('messages.days') }} {{ __('messages.billing_trial_free_tag') }}
                                </span>
                                <p style="font-size: 11px; margin: 2px 0 0 0; color: var(--text-color-light);">{{ __('messages.billing_trial_free_help') }}</p>
                            </div>
                            @auth
                                @if(app(\App\Services\Billing\SubscriptionLifecycleService::class)->userCanClaimTrial(auth()->id(), $plan))
                                    <form action="{{ route('billing.trial.claim', $plan->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="button primary" style="padding: 6px 14px; font-size: 12px; white-space: nowrap; background: #10b981; border-color: #10b981;">
                                            {{ __('messages.billing_start_free_trial') }}
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    @endif

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

                                {{-- Promo / Coupon Code Input --}}
                                <div style="margin-top: 4px;">
                                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">{{ __('messages.billing_coupon_code') }}</label>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="text" name="coupon_code" class="form-input billing-coupon-input" placeholder="{{ __('messages.billing_coupon_placeholder') }}" style="text-transform: uppercase; font-family: monospace;">
                                        <button type="button" class="button secondary btn-apply-coupon" data-plan-id="{{ $plan->id }}" style="padding: 0 14px; white-space: nowrap; font-size: 12px;">
                                            {{ __('messages.apply') }}
                                        </button>
                                    </div>
                                    <div class="coupon-feedback-message" style="font-size: 11px; margin-top: 4px; display: none; font-weight: 600;"></div>
                                </div>

                                <div class="tabby-phone-container" style="display: none;">
                                    <label class="form-label">{{ __('messages.billing_phone_number') }} <span style="color: red;">*</span></label>
                                    <input type="text" name="phone_number" class="form-input billing-phone-input" placeholder="+966500000000" value="{{ old('phone_number') }}">
                                    <p class="user-status-text" style="font-size: 12px; margin-top: 4px; color: var(--text-color-light);">{{ __('messages.billing_tabby_phone_help') }}</p>
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

                    @if(function_exists('do_action'))
                        @php(do_action('billing_plans_card_footer', $plan))
                    @endif
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

    {{-- Comparison Matrix --}}
    @if($plans->isNotEmpty())
        <div class="widget-box" style="margin-top: 36px; overflow: hidden;">
            <div class="widget-box-content" style="padding: 28px;">
                <div style="margin-bottom: 24px; text-align: center;">
                    <p class="widget-box-title" style="font-size: 22px; margin-bottom: 8px;">{{ __('messages.billing_comparison_title') }}</p>
                    <p class="user-status-text" style="max-width: 600px; margin: 0 auto;">{{ __('messages.billing_comparison_subtitle') }}</p>
                </div>

                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-hover align-middle mb-0" style="width: 100%; border-collapse: separate; border-spacing: 0; text-align: center;">
                        <thead>
                            <tr style="background: rgba(97, 93, 250, 0.05);">
                                <th style="text-align: start; padding: 16px; border-bottom: 2px solid rgba(97, 93, 250, 0.15); font-size: 14px;">{{ __('messages.billing_feature_name') }}</th>
                                <th style="padding: 16px; border-bottom: 2px solid rgba(97, 93, 250, 0.15); font-size: 14px;">{{ __('messages.billing_free_member') }}</th>
                                @foreach($plans as $plan)
                                    <th style="padding: 16px; border-bottom: 2px solid {{ $plan->accent_color ?: '#615dfa' }}; font-size: 15px; color: {{ $plan->accent_color ?: '#615dfa' }};">
                                        {{ $plan->name }}
                                        <div style="font-size: 13px; font-weight: normal; color: var(--text-color-light); margin-top: 4px;">
                                            {{ number_format((float) $plan->base_price, 2) }} {{ $baseCurrencyCode }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Row: Ad-Free --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-shield-alt" style="color: #615dfa; margin-right: 6px;"></i> {{ __('messages.billing_comparison_ad_free') }}
                                </td>
                                <td style="padding: 14px 16px; color: #ef4444; border-bottom: 1px solid rgba(0,0,0,0.06);"><i class="fa fa-times-circle"></i></td>
                                @foreach($plans as $plan)
                                    @php($adFree = !empty(data_get($plan->entitlements, 'ad_free')))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                        @if($adFree)
                                            <span style="color: #10b981; font-weight: bold;"><i class="fa fa-check-circle"></i> 100% Ad-Free</span>
                                        @else
                                            <span style="color: #94a3b8;"><i class="fa fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: PTS Multiplier --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-bolt" style="color: #f59e0b; margin-right: 6px;"></i> {{ __('messages.billing_comparison_pts_multiplier') }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; border-bottom: 1px solid rgba(0,0,0,0.06);">1.0x (Standard)</td>
                                @foreach($plans as $plan)
                                    @php($multiplier = (float) data_get($plan->entitlements, 'pts_multiplier', 1.0))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06); font-weight: 700; color: {{ $multiplier > 1.0 ? '#2563eb' : '#64748b' }};">
                                        {{ $multiplier > 1.0 ? $multiplier . 'x Boost' : '1.0x' }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Exchange Priority --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-rocket" style="color: #ec4899; margin-right: 6px;"></i> {{ __('messages.billing_comparison_exchange_priority') }}
                                </td>
                                <td style="padding: 14px 16px; color: #ef4444; border-bottom: 1px solid rgba(0,0,0,0.06);"><i class="fa fa-times-circle"></i></td>
                                @foreach($plans as $plan)
                                    @php($priority = !empty(data_get($plan->entitlements, 'exchange_priority')))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                        @if($priority)
                                            <span style="color: #10b981; font-weight: bold;"><i class="fa fa-check-circle"></i> Priority Surge</span>
                                        @else
                                            <span style="color: #94a3b8;"><i class="fa fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Store Commission --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-store" style="color: #06b6d4; margin-right: 6px;"></i> {{ __('messages.billing_comparison_store_commission') }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; border-bottom: 1px solid rgba(0,0,0,0.06);">Standard (20%)</td>
                                @foreach($plans as $plan)
                                    @php($comm = data_get($plan->entitlements, 'store_commission_pct', 0))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06); font-weight: 700; color: #10b981;">
                                        {{ $comm }}% Commission
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Verified Checkmark --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-check-circle" style="color: #3b82f6; margin-right: 6px;"></i> {{ __('messages.billing_comparison_verified_badge') }}
                                </td>
                                <td style="padding: 14px 16px; color: #ef4444; border-bottom: 1px solid rgba(0,0,0,0.06);"><i class="fa fa-times-circle"></i></td>
                                @foreach($plans as $plan)
                                    @php($verified = !empty(data_get($plan->entitlements, 'subscription_verified_badge')))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                        @if($verified)
                                            <span style="color: #2563eb; font-weight: bold;"><i class="fa fa-check-circle"></i> Included</span>
                                        @else
                                            <span style="color: #94a3b8;"><i class="fa fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Custom Profile Badge --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-tag" style="color: #8b5cf6; margin-right: 6px;"></i> {{ __('messages.billing_comparison_profile_badge') }}
                                </td>
                                <td style="padding: 14px 16px; color: #ef4444; border-bottom: 1px solid rgba(0,0,0,0.06);"><i class="fa fa-times-circle"></i></td>
                                @foreach($plans as $plan)
                                    @php($badgeLabel = trim((string) data_get($plan->entitlements, 'profile_badge_label', '')))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                        @if($badgeLabel !== '')
                                            <span style="display: inline-block; padding: 3px 10px; border-radius: 999px; background: {{ data_get($plan->entitlements, 'profile_badge_color', '#615dfa') }}18; color: {{ data_get($plan->entitlements, 'profile_badge_color', '#615dfa') }}; font-weight: 700; font-size: 12px;">
                                                {{ $badgeLabel }}
                                            </span>
                                        @else
                                            <span style="color: #94a3b8;"><i class="fa fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Max Upload Size --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-cloud-upload-alt" style="color: #64748b; margin-right: 6px;"></i> {{ __('messages.billing_comparison_max_upload') }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; border-bottom: 1px solid rgba(0,0,0,0.06);">25 MB</td>
                                @foreach($plans as $plan)
                                    @php($uploadSize = data_get($plan->entitlements, 'max_upload_size_mb', 25))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06); font-weight: 700;">
                                        {{ $uploadSize }} MB
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Promotion Discount --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                    <i class="fa fa-percent" style="color: #10b981; margin-right: 6px;"></i> {{ __('messages.billing_comparison_promotion_discount') }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; border-bottom: 1px solid rgba(0,0,0,0.06);">0%</td>
                                @foreach($plans as $plan)
                                    @php($discount = data_get($plan->entitlements, 'status_promotion_discount_pct', 0))
                                    <td style="padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.06); font-weight: 700; color: {{ $discount > 0 ? '#10b981' : '#64748b' }};">
                                        {{ $discount > 0 ? $discount . '% OFF' : '-' }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Row: Bonus Points --}}
                            <tr>
                                <td style="text-align: start; padding: 14px 16px; font-weight: 600;">
                                    <i class="fa fa-coins" style="color: #f59e0b; margin-right: 6px;"></i> {{ __('messages.billing_comparison_bonus_pts') }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b;">0 PTS</td>
                                @foreach($plans as $plan)
                                    @php($bonusPts = data_get($plan->entitlements, 'bonus_pts', 0))
                                    <td style="padding: 14px 16px; font-weight: 800; color: #f59e0b;">
                                        {{ $bonusPts > 0 ? '+' . number_format($bonusPts) . ' PTS' : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.billing-purchase-form');

        forms.forEach(function (form) {
            const gatewaySelect = form.querySelector('.billing-gateway-select');
            const currencySelect = form.querySelector('.billing-currency-select');
            const tabbyPhoneContainer = form.querySelector('.tabby-phone-container');
            const phoneInput = form.querySelector('.billing-phone-input');
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

            const toggleTabbyPhone = function () {
                if (!tabbyPhoneContainer || !phoneInput) return;
                if (gatewaySelect.value === 'tabby') {
                    tabbyPhoneContainer.style.display = 'block';
                    phoneInput.setAttribute('required', 'required');
                } else {
                    tabbyPhoneContainer.style.display = 'none';
                    phoneInput.removeAttribute('required');
                }
            };

            gatewaySelect.addEventListener('change', function () {
                syncCurrencies();
                toggleTabbyPhone();
            });
            syncCurrencies();
            toggleTabbyPhone();
        });

        document.querySelectorAll('.btn-apply-coupon').forEach(function (button) {
            button.addEventListener('click', function () {
                const planId = this.dataset.planId;
                const form = this.closest('form');
                const input = form.querySelector('.billing-coupon-input');
                const feedback = form.querySelector('.coupon-feedback-message');
                const code = input ? input.value.trim() : '';

                if (!code) {
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.color = '#ef4444';
                        feedback.textContent = '{{ __("messages.billing_coupon_empty") }}';
                    }
                    return;
                }

                button.disabled = true;
                const originalText = button.textContent;
                button.textContent = '...';

                fetch('{{ route("billing.coupon.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        coupon_code: code
                    })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    button.disabled = false;
                    button.textContent = originalText;
                    if (feedback) {
                        feedback.style.display = 'block';
                        if (data.valid) {
                            feedback.style.color = '#10b981';
                            feedback.textContent = data.message + ' ({{ __("messages.billing_final_amount") }}: ' + data.final_amount + ')';
                        } else {
                            feedback.style.color = '#ef4444';
                            feedback.textContent = data.message;
                        }
                    }
                })
                .catch(function () {
                    button.disabled = false;
                    button.textContent = originalText;
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.color = '#ef4444';
                        feedback.textContent = '{{ __("messages.billing_coupon_error") }}';
                    }
                });
            });
        });
    });
</script>
@endpush
