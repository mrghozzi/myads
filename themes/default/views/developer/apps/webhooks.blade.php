@extends('theme::layouts.master')

@section('title', __('messages.webhooks') . ' - ' . $app->name)

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-bolt" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.webhooks') ?? 'Webhooks' }} - {{ $app->name }}</p>
    <p class="section-banner-text">{{ __('messages.webhooks_subtitle') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'apps'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        @include('theme::developer.partials.app_tabs', ['app' => $app, 'activeTab' => 'webhooks'])

        <!-- Add Webhook Card -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <p class="widget-box-title">{{ __('messages.create_webhook') ?? 'Register New Webhook Endpoint' }}</p>
            <div class="widget-box-content" style="padding: 26px;">
                <form action="{{ route('developer.apps.webhooks.store', $app->id) }}" method="POST">
                    @csrf
                    <div class="dev-field" style="margin-bottom: 16px;">
                        <label for="webhook_url"><strong>{{ __('messages.webhook_endpoint_url') }}</strong> <span class="text-danger">*</span></label>
                        <input id="webhook_url" type="url" name="url" class="form-control dev-control" placeholder="https://example.com/api/myads-webhook" required>
                        <span class="dev-help-text">{{ __('messages.webhook_url_help') }}</span>
                    </div>

                    <div class="dev-field" style="margin-bottom: 20px;">
                        <label><strong>{{ __('messages.subscribe_to_events') }}</strong> <span class="text-danger">*</span></label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; margin-top: 8px;">
                            @foreach($supportedEvents as $eventKey => $eventMeta)
                                <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px; border: 1px solid var(--dev-border); border-radius: 8px; cursor: pointer; background: var(--dev-surface-soft);">
                                    <input type="checkbox" name="events[]" value="{{ $eventKey }}" checked style="margin-top: 3px;">
                                    <div>
                                        <strong style="font-size: 13px; color: var(--dev-title); font-family: monospace;">{{ $eventKey }}</strong>
                                        <p style="font-size: 11px; color: var(--dev-muted); margin: 2px 0 0;">{{ $eventMeta['description'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="button primary">
                        <i class="fa fa-plus me-1"></i> {{ __('messages.register_webhook') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Active Webhooks List -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <p class="widget-box-title">{{ __('messages.configured_webhooks') }} ({{ $webhooks->count() }})</p>
            <div class="widget-box-content" style="padding: 24px;">
                @if($webhooks->isEmpty())
                    <div style="text-align: center; padding: 30px; color: var(--dev-muted);">
                        <i class="fa fa-bolt" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p>{{ __('messages.no_webhooks_yet') }}</p>
                    </div>
                @else
                    <div style="display: grid; gap: 16px;">
                        @foreach($webhooks as $wh)
                            <div style="border: 1px solid var(--dev-border); border-radius: 10px; padding: 18px; background: var(--dev-surface-soft);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
                                    <div style="flex: 1; min-width: 250px;">
                                        <span class="dev-chip" style="font-size: 11px; margin-bottom: 6px; display: inline-block;">
                                            <i class="fa {{ $wh->is_active ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-1"></i>
                                            {{ $wh->is_active ? __('messages.active') : __('messages.disabled') }}
                                        </span>
                                        <div style="font-family: monospace; font-size: 14px; font-weight: 700; color: var(--dev-title); word-break: break-all;">
                                            {{ $wh->url }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--dev-muted); margin-top: 6px;">
                                            {{ __('messages.signing_secret') }}: <code style="background: var(--dev-code-bg); color: var(--dev-code-text); padding: 2px 6px; border-radius: 4px;">{{ $wh->secret }}</code>
                                        </div>
                                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px;">
                                            @foreach((array) $wh->events as $ev)
                                                <span style="font-size: 11px; padding: 2px 8px; border-radius: 4px; background: rgba(97, 93, 250, 0.1); color: var(--dev-accent); font-family: monospace;">{{ $ev }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" class="button white btn-test-webhook" data-test-url="{{ route('developer.apps.webhooks.test', ['app' => $app->id, 'webhook' => $wh->id]) }}" style="font-size: 12px; height: 32px; line-height: 32px; padding: 0 12px;">
                                            <i class="fa fa-paper-plane me-1"></i> {{ __('messages.test_ping') }}
                                        </button>
                                        <form action="{{ route('developer.apps.webhooks.destroy', ['app' => $app->id, 'webhook' => $wh->id]) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_webhook_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button white text-danger" style="font-size: 12px; height: 32px; line-height: 32px; padding: 0 12px;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Deliveries Logs -->
        <div class="widget-box dev-panel">
            <p class="widget-box-title">{{ __('messages.recent_delivery_history') }}</p>
            <div class="widget-box-content padding-none">
                @if($recentDeliveries->isEmpty())
                    <div style="text-align: center; padding: 28px; color: var(--dev-muted);">
                        {{ __('messages.no_deliveries_yet') }}
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dev-border); text-align: left; background: var(--dev-surface-soft);">
                                    <th style="padding: 12px 16px;">{{ __('messages.status') }}</th>
                                    <th style="padding: 12px 16px;">{{ __('messages.event') }}</th>
                                    <th style="padding: 12px 16px;">{{ __('messages.latency') }}</th>
                                    <th style="padding: 12px 16px;">{{ __('messages.time') }}</th>
                                    <th style="padding: 12px 16px; text-align: right;">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDeliveries as $deliv)
                                    <tr style="border-bottom: 1px solid var(--dev-border);">
                                        <td style="padding: 12px 16px;">
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; {{ $deliv->isSuccess() ? 'background: rgba(31, 157, 98, 0.15); color: #1f9d62;' : 'background: rgba(217, 75, 99, 0.15); color: #d94b63;' }}">
                                                {{ $deliv->status_code ?: 'FAILED' }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; font-family: monospace; font-weight: 600;">
                                            {{ $deliv->event }}
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--dev-muted);">
                                            {{ $deliv->duration_ms }} ms
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--dev-muted); font-size: 12px;">
                                            {{ $deliv->created_at ? $deliv->created_at->diffForHumans() : '-' }}
                                        </td>
                                        <td style="padding: 12px 16px; text-align: right;">
                                            <button type="button" class="button white btn-resend-webhook" data-resend-url="{{ route('developer.apps.webhooks.resend', ['app' => $app->id, 'delivery' => $deliv->id]) }}" style="font-size: 11px; height: 26px; line-height: 26px; padding: 0 8px;">
                                                <i class="fa fa-rotate-right me-1"></i> {{ __('messages.resend') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-test-webhook').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-test-url');
            var originalText = this.innerHTML;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
            this.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    alert('Ping success! Response: HTTP ' + data.status_code + ' (' + data.duration_ms + 'ms)');
                } else {
                    alert('Ping response: HTTP ' + data.status_code + (data.error ? '\nError: ' + data.error : ''));
                }
                location.reload();
            })
            .catch(function(err) {
                alert('Test request error: ' + err.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });

    document.querySelectorAll('.btn-resend-webhook').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-resend-url');
            var originalText = this.innerHTML;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                alert('Resend completed! Result: HTTP ' + data.status_code + ' (' + data.duration_ms + 'ms)');
                location.reload();
            })
            .catch(function(err) {
                alert('Resend error: ' + err.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });
});
</script>
@endpush
@endsection
