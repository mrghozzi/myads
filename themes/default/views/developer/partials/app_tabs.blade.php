<div class="dev-tabs-bar" style="display: flex; gap: 10px; margin-bottom: 22px; flex-wrap: wrap;">
    <a href="{{ route('developer.apps.show', $app->id) }}" class="button {{ ($activeTab ?? 'general') === 'general' ? 'primary' : 'white' }}" style="padding: 0 16px; height: 38px; line-height: 38px; border-radius: 8px; font-size: 13px; font-weight: 600;">
        <i class="fa fa-sliders me-2"></i>{{ __('messages.app_settings') }}
    </a>
    <a href="{{ route('developer.apps.webhooks.index', $app->id) }}" class="button {{ ($activeTab ?? '') === 'webhooks' ? 'primary' : 'white' }}" style="padding: 0 16px; height: 38px; line-height: 38px; border-radius: 8px; font-size: 13px; font-weight: 600;">
        <i class="fa fa-bolt me-2"></i>{{ __('messages.webhooks') ?? 'Webhooks' }}
    </a>
    <a href="{{ route('developer.apps.analytics', $app->id) }}" class="button {{ ($activeTab ?? '') === 'analytics' ? 'primary' : 'white' }}" style="padding: 0 16px; height: 38px; line-height: 38px; border-radius: 8px; font-size: 13px; font-weight: 600;">
        <i class="fa fa-chart-line me-2"></i>{{ __('messages.analytics') ?? 'Analytics' }}
    </a>
    <a href="{{ route('developer.apps.logs', $app->id) }}" class="button {{ ($activeTab ?? '') === 'logs' ? 'primary' : 'white' }}" style="padding: 0 16px; height: 38px; line-height: 38px; border-radius: 8px; font-size: 13px; font-weight: 600;">
        <i class="fa fa-stream me-2"></i>{{ __('messages.request_logs') ?? 'Live Logs' }}
    </a>
</div>
