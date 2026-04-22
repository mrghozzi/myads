@php
    $developerPlatformSettings = app(\App\Services\DeveloperPlatformSettings::class);
    $developerRules = [];

    if ($developerPlatformSettings->requireAdminApproval()) {
        $developerRules[] = [
            'title' => __('messages.require_admin_approval'),
            'copy' => __('messages.require_admin_approval_desc'),
        ];
    }

    if ($developerPlatformSettings->getMinAccountAgeDays() > 0) {
        $developerRules[] = [
            'title' => __('messages.min_account_age_days'),
            'copy' => __('messages.dev_reason_min_account_age_days', ['days' => $developerPlatformSettings->getMinAccountAgeDays()]),
        ];
    }

    if ($developerPlatformSettings->getMinFollowersCount() > 0) {
        $developerRules[] = [
            'title' => __('messages.min_followers_count'),
            'copy' => __('messages.dev_reason_min_followers_count', ['count' => $developerPlatformSettings->getMinFollowersCount()]),
        ];
    }

    if ($developerPlatformSettings->requirePaidPlan()) {
        $developerRules[] = [
            'title' => __('messages.require_paid_plan'),
            'copy' => __('messages.require_paid_plan_desc'),
        ];
    }
@endphp

<div class="widget-box dev-panel">
    <p class="widget-box-title">{{ __('messages.platform_status') }}</p>
    <div class="widget-box-content" style="padding: 28px;">
        <p class="dev-card-copy">{{ __('messages.dev_platform_settings_desc') }}</p>

        <div class="dev-chip-row" style="margin-top: 18px;">
            <span class="dev-chip">
                <i class="fa fa-plug"></i>
                {{ __('messages.v1_api') }}
            </span>
            <span class="dev-chip">
                <i class="fa fa-shield-halved"></i>
                {{ __('messages.oauth_secured') }}
            </span>
        </div>

        @if(count($developerRules) > 0)
            <div class="dev-rule-list">
                @foreach($developerRules as $rule)
                    <div class="dev-rule-item">
                        <strong>{{ $rule['title'] }}</strong>
                        <span class="dev-rule-value">{{ $rule['copy'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
