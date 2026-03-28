@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: linear-gradient(135deg, rgba(15,23,42,.92) 0%, rgba(29,78,216,.82) 55%, rgba(56,189,248,.78) 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.status_promotions_title') }}</p>
    <p class="section-banner-text">{{ __('messages.status_promotions_member_help') }}</p>
</div>

<div class="ads-nav-bar" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 20px;">
    <a href="{{ route('ads.index') }}" class="ads-nav-item" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 18px; background: #eef2ff; color: #1d4ed8; border-radius: 12px; font-weight: 700; text-decoration: none;">
        <i class="fa fa-arrow-left"></i> {{ __('messages.ads') }}
    </a>
    <a href="{{ route('ads.posts.index') }}" class="ads-nav-item" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 18px; background: linear-gradient(135deg, #f97316 0%, #f59e0b 100%); color: #fff; border-radius: 12px; font-weight: 700; text-decoration: none;">
        <i class="fa fa-bullhorn"></i> {{ __('messages.status_promotions_title') }}
    </a>
</div>

@if(!empty($upgradeNotice))
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endif

@if($featureAvailable)
    <div style="display: grid; gap: 18px;">
        @forelse($promotions as $promotion)
            @php
                $status = $promotion->promotedStatus;
                $progress = $promotion->progressPercentage($status);
                $currentProgress = $promotion->currentProgressValue($status);
                $objectiveKey = 'messages.status_promotion_objective_' . $promotion->objective;
                $statusKey = 'messages.status_promotion_status_' . $promotion->status;
            @endphp
            <div class="widget-box" style="padding: 0; overflow: hidden;">
                <div style="padding: 22px 24px; border-bottom: 1px solid #eef2ff; display: flex; justify-content: space-between; gap: 16px; align-items: start; flex-wrap: wrap;">
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: rgba(249,115,22,.1); color: #ea580c; font-size: .72rem; font-weight: 800; text-transform: uppercase;">
                            <i class="fa fa-bullhorn"></i> {{ __('messages.status_promotion_ad_badge') }}
                        </div>
                        <h3 style="margin: 12px 0 8px; color: #1f2937;">{{ __('messages.status_promotion_campaign_id', ['id' => $promotion->id]) }}</h3>
                        <p style="margin: 0; color: #6b7280;">
                            {{ __('messages.status_promotion_goal_summary', [
                                'objective' => __($objectiveKey),
                                'target' => $promotion->target_quantity,
                            ]) }}
                        </p>
                    </div>
                    <div style="text-align: end;">
                        <div style="font-size: 1.7rem; font-weight: 800; color: #1d4ed8;">{{ $promotion->charged_pts }}</div>
                        <div style="font-size: .8rem; color: #6b7280;">{{ __('messages.status_promotion_pts_label') }}</div>
                        @php
                            $statusColors = [
                                \App\Models\StatusPromotion::STATUS_ACTIVE => ['bg' => 'rgba(34, 197, 94, 0.1)', 'color' => '#15803d'],
                                \App\Models\StatusPromotion::STATUS_PAUSED => ['bg' => 'rgba(249, 115, 22, 0.1)', 'color' => '#c2410c'],
                                \App\Models\StatusPromotion::STATUS_COMPLETED => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#b91c1c'],
                                \App\Models\StatusPromotion::STATUS_EXPIRED => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#b91c1c'],
                                \App\Models\StatusPromotion::STATUS_BUDGET_CAPPED => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#b91c1c'],
                            ];
                            $currentColor = $statusColors[$promotion->status] ?? ['bg' => '#f8fafc', 'color' => '#334155'];
                        @endphp
                        <div style="margin-top: 10px; display: inline-flex; padding: 6px 12px; border-radius: 999px; background: {{ $currentColor['bg'] }}; color: {{ $currentColor['color'] }}; font-size: .78rem; font-weight: 700;">
                            {{ __($statusKey) }}
                        </div>
                    </div>
                </div>

                <div style="padding: 22px 24px;">
                    @if($status)
                        <div style="margin-bottom: 18px;">
                            <a href="{{ $status->promotionDestinationUrl() }}" style="font-weight: 700; color: #1d4ed8; text-decoration: none;">
                                {{ __('messages.status_promotion_view_post') }}
                            </a>
                        </div>
                    @endif

                    <div style="margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; font-size: .85rem;">
                            <strong style="color: #334155;">{{ __('messages.status_promotion_progress') }}</strong>
                            <span style="color: #64748b;">{{ $currentProgress }} / {{ $promotion->target_quantity }}</span>
                        </div>
                        <div style="height: 12px; border-radius: 999px; background: #e2e8f0; overflow: hidden;">
                            <div style="height: 100%; width: {{ $progress }}%; background: linear-gradient(135deg, #1d4ed8 0%, #38bdf8 100%);"></div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
                        <div style="padding: 14px; border-radius: 14px; background: #f8fafc;">
                            <div style="font-size: .75rem; color: #64748b;">{{ __('messages.status_promotion_remaining_impressions') }}</div>
                            <div style="margin-top: 6px; font-size: 1.05rem; font-weight: 700; color: #0f172a;">{{ $promotion->remainingImpressions() }}</div>
                        </div>
                        <div style="padding: 14px; border-radius: 14px; background: #fff7ed;">
                            <div style="font-size: .75rem; color: #9a3412;">{{ __('messages.status_promotion_smart_factor') }}</div>
                            <div style="margin-top: 6px; font-size: 1.05rem; font-weight: 700; color: #c2410c;">x{{ number_format((float) $promotion->smart_factor, 2) }}</div>
                        </div>
                        <div style="padding: 14px; border-radius: 14px; background: #eff6ff;">
                            <div style="font-size: .75rem; color: #1d4ed8;">{{ __('messages.status_promotion_ends_at') }}</div>
                            <div style="margin-top: 6px; font-size: 1.05rem; font-weight: 700; color: #1e3a8a;">{{ optional($promotion->ends_at)->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    @if($promotion->status === \App\Models\StatusPromotion::STATUS_BUDGET_CAPPED)
                        <div style="margin-top: 16px; padding: 14px 16px; border-radius: 14px; background: #fff7ed; color: #9a3412; font-weight: 600;">
                            {{ __('messages.status_promotion_budget_capped_help') }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="widget-box">
                <div class="widget-box-content" style="padding: 36px 28px; text-align: center;">
                    <i class="fa fa-bullhorn" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                    <p style="margin: 0 0 12px; color: #475569; font-weight: 700;">{{ __('messages.status_promotion_empty_title') }}</p>
                    <p style="margin: 0; color: #64748b;">{{ __('messages.status_promotion_empty_help') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 22px;">
        {{ $promotions->links() }}
    </div>
@endif
@endsection
