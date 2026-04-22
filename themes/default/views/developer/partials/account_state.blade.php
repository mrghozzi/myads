@php
    $developerApps = collect($apps ?? []);
    $developerEligibilityCheck = auth()->check()
        ? app(\App\Services\DeveloperEligibilityService::class)->checkEligibility(auth()->user())
        : null;
    $developerEligible = auth()->check() ? (bool) ($developerEligibilityCheck['eligible'] ?? false) : false;
    $developerReason = $developerEligibilityCheck['reason'] ?? null;
    $developerReasonMessage = $developerReason
        ? __('messages.dev_reason_' . $developerReason, [
            'days' => $developerEligibilityCheck['required_days'] ?? null,
            'count' => $developerEligibilityCheck['required_followers'] ?? null,
        ])
        : null;
@endphp

<div class="dev-side-stack">
    <div class="widget-box dev-panel">
        <p class="widget-box-title">{{ __('messages.my_apps') }}</p>
        <div class="widget-box-content" style="padding: 28px;">
            @guest
                <p class="dev-card-copy">{{ __('messages.dev_login_cta_desc') }}</p>
                <div class="dev-inline-actions" style="margin-top: 18px;">
                    <a href="{{ route('login') }}" class="button primary">{{ __('messages.login') }}</a>
                    <a href="{{ route('register') }}" class="button secondary">{{ __('messages.register') }}</a>
                </div>
            @else
                @if(!$developerEligible)
                    <div class="dev-note dev-note--warning" style="margin-bottom: 0;">
                        <strong>{{ __('messages.dev_not_eligible') }}</strong>
                        @if($developerReasonMessage)
                            <p>{{ $developerReasonMessage }}</p>
                        @endif
                    </div>
                @elseif($developerApps->isEmpty())
                    <div class="dev-empty">
                        <i class="fa fa-cubes"></i>
                        <p class="dev-card-copy">{{ __('messages.no_apps_yet') }}</p>
                        <div class="dev-inline-actions" style="justify-content: center; margin-top: 16px;">
                            <a href="{{ route('developer.apps.create') }}" class="button primary">{{ __('messages.create_app') }}</a>
                        </div>
                    </div>
                @else
                    <div class="dev-stat-grid dev-stat-grid--compact">
                        <div class="dev-stat-card">
                            <span>{{ __('messages.total_apps') }}</span>
                            <strong>{{ $developerApps->count() }}</strong>
                        </div>
                        <div class="dev-stat-card">
                            <span>{{ __('messages.active_apps') }}</span>
                            <strong>{{ $developerApps->where('status', 'active')->count() }}</strong>
                        </div>
                    </div>

                    <div class="dev-divider"></div>

                    <div class="dev-state-list">
                        @foreach($developerApps->take(3) as $developerApp)
                            <a href="{{ route('developer.apps.show', $developerApp->id) }}">
                                <div>
                                    <div class="dev-app-name" style="font-size: 0.92rem;">{{ $developerApp->name }}</div>
                                    <div class="dev-app-domain" style="margin-top: 6px;">
                                        <i class="fa fa-globe"></i>
                                        {{ parse_url($developerApp->domain, PHP_URL_HOST) ?: $developerApp->domain }}
                                    </div>
                                </div>
                                @include('theme::developer.partials.status_badge', ['status' => $developerApp->status])
                            </a>
                        @endforeach
                    </div>

                    <div class="dev-inline-actions" style="margin-top: 18px;">
                        <a href="{{ route('developer.apps.index') }}" class="button secondary">{{ __('messages.manage_apps') }}</a>
                        <a href="{{ route('developer.apps.create') }}" class="button primary">{{ __('messages.create_app') }}</a>
                    </div>
                @endif
            @endguest
        </div>
    </div>

    <div class="widget-box dev-panel">
        <p class="widget-box-title">{{ __('messages.platform_info') }}</p>
        <div class="widget-box-content" style="padding: 28px;">
            <ul class="dev-list-reset">
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>{{ __('messages.v1_api_desc') }}</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>{{ __('messages.oauth_secured_desc') }}</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>{{ __('messages.dev_share_desc') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
