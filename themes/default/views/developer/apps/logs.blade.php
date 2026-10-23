@extends('theme::layouts.master')

@section('title', 'API Request Logs - ' . $app->name)

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-stream" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">Live Request Logs - {{ $app->name }}</p>
    <p class="section-banner-text">Real-time inspection of API calls, response codes, and latency for debugging.</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'apps'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @include('theme::developer.partials.app_tabs', ['app' => $app, 'activeTab' => 'logs'])

        <div class="widget-box dev-panel">
            <div class="widget-box-content padding-none">
                @if($logs->isEmpty())
                    <div style="text-align: center; padding: 40px; color: var(--dev-muted);">
                        <i class="fa fa-stream" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p>No requests recorded yet for this app. Calls made via API will stream here automatically.</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dev-border); text-align: left; background: var(--dev-surface-soft);">
                                    <th style="padding: 12px 16px;">Method</th>
                                    <th style="padding: 12px 16px;">Status</th>
                                    <th style="padding: 12px 16px;">Endpoint</th>
                                    <th style="padding: 12px 16px;">Latency</th>
                                    <th style="padding: 12px 16px;">IP Address</th>
                                    <th style="padding: 12px 16px; text-align: right;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    @php
                                        $methodColor = match($log->method) {
                                            'POST' => '#23d2e2',
                                            'PUT', 'PATCH' => '#ffbe5c',
                                            'DELETE' => '#ff7d93',
                                            default => '#615dfa',
                                        };
                                        $statusBg = $log->status_code >= 400 ? 'rgba(217, 75, 99, 0.15)' : 'rgba(31, 157, 98, 0.15)';
                                        $statusColor = $log->status_code >= 400 ? '#d94b63' : '#1f9d62';
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--dev-border);">
                                        <td style="padding: 12px 16px;">
                                            <span style="font-weight: 800; font-family: monospace; font-size: 11px; color: {{ $methodColor }}; background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px;">
                                                {{ $log->method }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; background: {{ $statusBg }}; color: {{ $statusColor }};">
                                                {{ $log->status_code }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; font-family: monospace; font-weight: 600; color: var(--dev-title);">
                                            {{ $log->endpoint }}
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--dev-muted);">
                                            {{ $log->duration_ms }} ms
                                        </td>
                                        <td style="padding: 12px 16px; font-family: monospace; color: var(--dev-muted); font-size: 12px;">
                                            {{ $log->ip_address ?: '-' }}
                                        </td>
                                        <td style="padding: 12px 16px; text-align: right; color: var(--dev-muted); font-size: 12px;">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="padding: 16px 20px;">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>
@endsection
