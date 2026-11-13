@php
    $landingPlans = \App\Models\SubscriptionPlan::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->take(4)
        ->get();
    $baseCurrency = \App\Support\SubscriptionSettings::get('base_currency_code', 'USD');
@endphp

@if($landingPlans->isNotEmpty())
    <section class="pricing-section py-5 position-relative" id="plans" style="z-index: 10;">
        <div class="container" style="max-width: 1200px;">
            <div class="section-header text-center mb-5 scroll-reveal">
                <div class="badge-premium d-inline-flex align-items-center gap-2 mb-3" style="background: rgba(97, 93, 250, 0.1); border: 1px solid rgba(97, 93, 250, 0.25); color: var(--primary); padding: 6px 16px; border-radius: 999px; font-weight: 700; font-size: 13px;">
                    <i class="fa-solid fa-crown"></i>
                    <span>{{ __('messages.billing_landing_plans_eyebrow') }}</span>
                </div>
                <h2 class="display-6 fw-bold mb-3" style="color: var(--text-main);">
                    {{ __('messages.billing_landing_plans_title') }}
                </h2>
                <p class="lead text-muted mx-auto" style="max-width: 640px; font-size: 1.05rem;">
                    {{ __('messages.billing_landing_plans_subtitle') }}
                </p>
            </div>

            <div class="row g-4 justify-content-center align-items-stretch">
                @foreach($landingPlans as $plan)
                    @php
                        $accent = $plan->accent_color ?: '#615dfa';
                        $layoutStyle = $plan->layout_style ?? 'default';
                        $cardStyle = '';
                        if ($layoutStyle === 'glassmorphism') {
                            $cardStyle = 'background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.25);';
                        } elseif ($layoutStyle === 'neon_glow') {
                            $cardStyle = "box-shadow: 0 0 30px {$accent}35; border: 2px solid {$accent}; background: var(--bg-surface);";
                        } elseif ($layoutStyle === 'minimal') {
                            $cardStyle = 'box-shadow: none; border: 1px solid rgba(148, 163, 184, 0.2); background: var(--bg-surface);';
                        } else {
                            $cardStyle = 'background: var(--bg-surface); border: 1px solid rgba(148, 163, 184, 0.15); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);';
                        }
                        $cardClass = function_exists('apply_filters') ? apply_filters('billing_landing_plans_card_class', 'h-100 p-4 rounded-4 position-relative d-flex flex-column justify-content-between transition-all', $plan) : 'h-100 p-4 rounded-4 position-relative d-flex flex-column justify-content-between transition-all';
                    @endphp
                    <div class="col-lg-3 col-md-6 col-12 scroll-reveal">
                        <div class="{{ $cardClass }}" style="{{ $cardStyle }} border-top: 5px solid {{ $accent }} !important;">
                            <div>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <div>
                                        <h3 class="h5 fw-bold mb-1" style="color: var(--text-main);">{{ $plan->name }}</h3>
                                        <p class="small text-muted mb-0" style="min-height: 38px;">{{ $plan->description }}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        @if($plan->is_featured)
                                            <span class="badge rounded-pill" style="background: rgba(37, 99, 235, 0.15); color: #3b82f6; border: 1px solid rgba(37, 99, 235, 0.3); font-size: 11px;">
                                                <i class="fa-solid fa-star me-1"></i>{{ __('messages.billing_featured_plan') }}
                                            </span>
                                        @endif
                                        @if(function_exists('do_action'))
                                            @php(do_action('billing_landing_plans_card_badge', $plan))
                                        @endif
                                    </div>
                                </div>

                                {{-- Price block --}}
                                <div class="my-4 py-2 border-top border-bottom" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                                    @if($plan->is_on_sale)
                                        <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                            <span class="text-decoration-line-through text-muted fs-6">{{ number_format((float) $plan->base_price, 2) }}</span>
                                            <span class="h2 fw-extrabold mb-0 text-danger">{{ number_format((float) $plan->sale_price, 2) }}</span>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 fs-11">
                                                -{{ $plan->discount_percentage }}%
                                            </span>
                                        </div>
                                        @if($plan->sale_ends_at)
                                            <div class="small text-danger mt-1 fs-11">
                                                <i class="fa-solid fa-clock me-1"></i>{{ __('messages.billing_sale_ends_in') }}: {{ $plan->sale_ends_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="h2 fw-extrabold mb-0" style="color: var(--text-main);">
                                            {{ number_format((float) $plan->base_price, 2) }}
                                        </div>
                                    @endif
                                    <div class="small text-muted mt-1">
                                        {{ $baseCurrency }} · {{ $plan->is_lifetime ? __('messages.billing_lifetime') : __('messages.billing_duration_days_value', ['days' => $plan->duration_days]) }}
                                    </div>
                                </div>

                                {{-- Free Trial Badge if available --}}
                                @if($plan->has_trial)
                                    <div class="p-2 mb-3 rounded-3 text-center" style="background: rgba(16, 185, 129, 0.1); border: 1px dashed #10b981; color: #10b981; font-weight: 700; font-size: 12px;">
                                        <i class="fa-solid fa-gift me-1"></i> {{ $plan->trial_days }} {{ __('messages.days') }} {{ __('messages.billing_trial_free_tag') }}
                                    </div>
                                @endif

                                {{-- Highlights / Bullets --}}
                                @if(!empty($plan->marketing_bullets))
                                    <ul class="list-unstyled mb-4 small" style="display: grid; gap: 8px;">
                                        @foreach(array_slice((array) $plan->marketing_bullets, 0, 5) as $bullet)
                                            <li class="d-flex align-items-center gap-2" style="color: var(--text-main);">
                                                <i class="fa-solid fa-circle-check" style="color: {{ $accent }}; font-size: 13px;"></i>
                                                <span>{{ $bullet }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('billing.plans') }}" class="btn w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: {{ $accent }}; color: #ffffff; border-radius: 12px; transition: transform 0.2s ease;">
                                    <span>{{ $plan->has_trial ? __('messages.billing_start_free_trial') : __('messages.billing_choose_plan') }}</span>
                                    <i class="fa-solid fa-arrow-right fs-12"></i>
                                </a>
                                @if(function_exists('do_action'))
                                    @php(do_action('billing_landing_plans_card_footer', $plan))
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5 scroll-reveal">
                <a href="{{ route('billing.plans') }}" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-semibold" style="border-color: var(--primary); color: var(--primary);">
                    <i class="fa-solid fa-table-list me-2"></i>{{ __('messages.billing_view_all_plans_matrix') }}
                </a>
            </div>
        </div>
    </section>
@endif
