@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: linear-gradient(135deg, rgba(30,41,59,.96) 0%, rgba(234,88,12,.88) 55%, rgba(249,115,22,.82) 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.status_promotion_setup_title') }}</p>
    <p class="section-banner-text">{{ __('messages.status_promotion_setup_help') }}</p>
</div>

<div class="ads-nav-bar" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 20px;">
    <a href="{{ route('ads.posts.index') }}" class="ads-nav-item" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 18px; background: #eef2ff; color: #1d4ed8; border-radius: 12px; font-weight: 700; text-decoration: none;">
        <i class="fa fa-arrow-left"></i> {{ __('messages.status_promotions_title') }}
    </a>
</div>

@if(!empty($upgradeNotice))
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endif

@if($featureAvailable)
    <div style="display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(320px, .7fr); gap: 20px;">
        <div style="display: grid; gap: 18px;">
            @include('theme::partials.activity.render', ['activity' => $status, 'detailView' => false])
        </div>

        <div class="widget-box" style="padding: 0; overflow: hidden;">
            <div style="padding: 22px 24px; border-bottom: 1px solid #f1f5f9; background: linear-gradient(135deg, rgba(249,115,22,.08) 0%, rgba(245,158,11,.12) 100%);">
                <h3 style="margin: 0; color: #1f2937;">{{ __('messages.status_promotion_setup_title') }}</h3>
                <p style="margin: 8px 0 0; color: #64748b;">{{ __('messages.status_promotion_no_refund_notice') }}</p>
            </div>

            <div style="padding: 22px 24px;">
                <form
                    method="POST"
                    action="{{ url()->current() }}"
                    id="status-promotion-form"
                    data-quote-url="{{ route('ads.posts.quote', $status->id) }}"
                >
                    @csrf

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #334155;">{{ __('messages.status_promotion_objective_label') }}</label>
                        <select name="objective" id="promotion-objective" class="form-control">
                            <option value="views">{{ __('messages.status_promotion_objective_views') }}</option>
                            <option value="comments">{{ __('messages.status_promotion_objective_comments') }}</option>
                            <option value="reactions">{{ __('messages.status_promotion_objective_reactions') }}</option>
                            <option value="days">{{ __('messages.status_promotion_objective_days') }}</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #334155;">{{ __('messages.status_promotion_target_quantity') }}</label>
                        <input
                            type="number"
                            name="target_quantity"
                            id="promotion-target"
                            class="form-control"
                            min="{{ $settings['min_views_target'] }}"
                            max="{{ $settings['max_views_target'] }}"
                            value="{{ old('target_quantity', $settings['min_views_target']) }}"
                        >
                        @error('target_quantity')
                            <div style="margin-top: 8px; color: #dc2626; font-weight: 600;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="promotion-quote-card" style="padding: 18px; border-radius: 18px; background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%); border: 1px solid #dbeafe; margin-bottom: 18px;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <div style="font-size: .78rem; color: #1d4ed8; font-weight: 800; text-transform: uppercase;">{{ __('messages.status_promotion_quote_title') }}</div>
                                <div id="promotion-quote-goal" style="margin-top: 6px; color: #334155; font-weight: 700;">
                                    {{ __('messages.status_promotion_goal_summary', ['objective' => __('messages.status_promotion_objective_views'), 'target' => $quote['target_quantity'] ?? $settings['min_views_target']]) }}
                                </div>
                            </div>
                            <div style="text-align: end;">
                                <div id="promotion-quote-price" style="font-size: 1.7rem; font-weight: 800; color: #1d4ed8;">{{ $quote['charged_pts'] ?? 0 }}</div>
                                <div style="font-size: .8rem; color: #64748b;">{{ __('messages.status_promotion_pts_label') }}</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                            <div style="padding: 14px; border-radius: 14px; background: rgba(255,255,255,.8);">
                                <div style="font-size: .75rem; color: #64748b;">{{ __('messages.status_promotion_smart_factor') }}</div>
                                <div id="promotion-quote-factor" style="margin-top: 6px; font-weight: 800; color: #0f172a;">x{{ number_format((float) ($quote['smart_factor'] ?? 1), 2) }}</div>
                            </div>
                            <div style="padding: 14px; border-radius: 14px; background: rgba(255,255,255,.8);">
                                <div style="font-size: .75rem; color: #64748b;">{{ __('messages.status_promotion_delivery_cap') }}</div>
                                <div id="promotion-quote-cap" style="margin-top: 6px; font-weight: 800; color: #0f172a;">{{ $quote['delivery_cap_impressions'] ?? 0 }}</div>
                            </div>
                            <div style="padding: 14px; border-radius: 14px; background: rgba(255,255,255,.8);">
                                <div style="font-size: .75rem; color: #64748b;">{{ __('messages.status_promotion_estimated_days') }}</div>
                                <div id="promotion-quote-days" style="margin-top: 6px; font-weight: 800; color: #0f172a;">{{ $quote['estimated_duration_days'] ?? 1 }}</div>
                            </div>
                            <div style="padding: 14px; border-radius: 14px; background: rgba(255,255,255,.8);">
                                <div style="font-size: .75rem; color: #64748b;">{{ __('messages.status_promotion_current_balance') }}</div>
                                <div id="promotion-balance" style="margin-top: 6px; font-weight: 800; color: #0f172a;">{{ (int) auth()->user()->pts }}</div>
                            </div>
                        </div>

                        <div id="promotion-quote-message" style="margin-top: 14px; font-weight: 700; color: #0f766e;">
                            {{ __('messages.status_promotion_ready_to_launch') }}
                        </div>
                    </div>

                    <button type="submit" class="button primary" id="promotion-submit">
                        {{ __('messages.status_promotion_launch') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

@once
    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('status-promotion-form');
                if (!form) {
                    return;
                }

                const settings = @json($settings);
                const objectiveInput = document.getElementById('promotion-objective');
                const targetInput = document.getElementById('promotion-target');
                const submitButton = document.getElementById('promotion-submit');
                const messageBox = document.getElementById('promotion-quote-message');
                const quoteUrl = resolveQuoteUrl();

                const labels = {
                    views: @json(__('messages.status_promotion_objective_views')),
                    comments: @json(__('messages.status_promotion_objective_comments')),
                    reactions: @json(__('messages.status_promotion_objective_reactions')),
                    days: @json(__('messages.status_promotion_objective_days'))
                };

                function getCsrfToken() {
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');

                    return tokenMeta ? tokenMeta.getAttribute('content') : '';
                }

                function resolveQuoteUrl() {
                    const configuredUrl = form.getAttribute('data-quote-url') || '';
                    const fallbackPath = window.location.pathname.replace(/\/promote\/?$/, '/quote');

                    try {
                        const resolvedUrl = new URL(configuredUrl || fallbackPath, window.location.href);

                        if (resolvedUrl.origin !== window.location.origin) {
                            return resolvedUrl.pathname + resolvedUrl.search;
                        }

                        return resolvedUrl.pathname + resolvedUrl.search;
                    } catch (error) {
                        return fallbackPath;
                    }
                }

                async function readPayload(response) {
                    const responseText = await response.text();
                    const normalizedText = (responseText || '').replace(/^\uFEFF/, '').trim();

                    if (!normalizedText) {
                        return {};
                    }

                    try {
                        return JSON.parse(normalizedText);
                    } catch (error) {
                        const redirectedToLogin = response.redirected && /\/login(?:[/?#]|$)/i.test(response.url || '');

                        return {
                            message: (response.status === 419 || redirectedToLogin)
                                ? @json(__('messages.error_419_text'))
                                : @json(__('messages.status_promotion_quote_failed'))
                        };
                    }
                }

                function syncBounds() {
                    const objective = objectiveInput.value;
                    targetInput.min = settings['min_' + objective + '_target'];
                    targetInput.max = settings['max_' + objective + '_target'];

                    if (!targetInput.value || parseInt(targetInput.value, 10) < parseInt(targetInput.min, 10)) {
                        targetInput.value = targetInput.min;
                    }
                }

                async function refreshQuote() {
                    syncBounds();

                    const payload = new URLSearchParams();
                    payload.append('objective', objectiveInput.value);
                    payload.append('target_quantity', targetInput.value);
                    payload.append('_token', getCsrfToken());

                    try {
                        const response = await fetch(quoteUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: payload.toString()
                        });

                        const data = await readPayload(response);
                        if (!response.ok || !data || !data.quote) {
                            throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || @json(__('messages.status_promotion_quote_failed')));
                        }

                        document.getElementById('promotion-quote-goal').textContent = @json(__('messages.status_promotion_goal_summary', ['objective' => ':objective', 'target' => ':target']))
                            .replace(':objective', labels[data.quote.objective])
                            .replace(':target', data.quote.target_quantity);
                        document.getElementById('promotion-quote-price').textContent = data.quote.charged_pts;
                        document.getElementById('promotion-quote-factor').textContent = 'x' + Number(data.quote.smart_factor).toFixed(2);
                        document.getElementById('promotion-quote-cap').textContent = data.quote.delivery_cap_impressions;
                        document.getElementById('promotion-quote-days').textContent = data.quote.estimated_duration_days;
                        document.getElementById('promotion-balance').textContent = data.balance_pts;

                        if (data.can_afford) {
                            messageBox.textContent = @json(__('messages.status_promotion_ready_to_launch'));
                            messageBox.style.color = '#0f766e';
                            submitButton.disabled = false;
                            submitButton.style.opacity = '1';
                        } else {
                            messageBox.textContent = @json(__('messages.status_promotion_insufficient_pts'));
                            messageBox.style.color = '#dc2626';
                            submitButton.disabled = true;
                            submitButton.style.opacity = '.6';
                        }
                    } catch (error) {
                        messageBox.textContent = error.message;
                        messageBox.style.color = '#dc2626';
                        submitButton.disabled = true;
                        submitButton.style.opacity = '.6';
                    }
                }

                objectiveInput.addEventListener('change', refreshQuote);
                targetInput.addEventListener('input', refreshQuote);
                refreshQuote();
            })();
        </script>
    @endpush
@endonce
@endsection
