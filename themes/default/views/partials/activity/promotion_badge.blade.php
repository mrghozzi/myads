@if(!empty($activity->is_promoted_ad))
    <div class="activity-sponsored-chip" style="margin-top: 12px; display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: linear-gradient(135deg, #f97316 0%, #f59e0b 100%); color: #fff; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;">
        <i class="fa fa-bullhorn" aria-hidden="true"></i>
        <span>{{ __('messages.status_promotion_ad_badge') }}</span>
    </div>
@endif
