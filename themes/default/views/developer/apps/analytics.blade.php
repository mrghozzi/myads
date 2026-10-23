@extends('theme::layouts.master')

@section('title', 'API Analytics - ' . $app->name)

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-chart-line" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">API Analytics & Telemetry - {{ $app->name }}</p>
    <p class="section-banner-text">Real-time performance metrics, error rates, and endpoint consumption analysis.</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'apps'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @include('theme::developer.partials.app_tabs', ['app' => $app, 'activeTab' => 'analytics'])

        <!-- Metrics 4-Grid -->
        <div class="dev-stat-grid" style="margin-bottom: 24px;">
            <div class="dev-stat-card">
                <span>Total Calls</span>
                <strong style="color: var(--dev-accent);">{{ number_format($totalRequests) }}</strong>
                <p style="font-size: 11px; color: var(--dev-muted); margin: 4px 0 0;">All-time requests</p>
            </div>
            <div class="dev-stat-card">
                <span>Today</span>
                <strong style="color: #23d2e2;">{{ number_format($todayRequests) }}</strong>
                <p style="font-size: 11px; color: var(--dev-muted); margin: 4px 0 0;">Last 24 hours</p>
            </div>
            <div class="dev-stat-card">
                <span>Error Rate</span>
                <strong style="color: {{ $errorRate > 5 ? '#d94b63' : '#1f9d62' }};">{{ $errorRate }}%</strong>
                <p style="font-size: 11px; color: var(--dev-muted); margin: 4px 0 0;">4xx & 5xx responses</p>
            </div>
            <div class="dev-stat-card">
                <span>Avg Latency</span>
                <strong style="color: var(--dev-title);">{{ $avgDuration }} ms</strong>
                <p style="font-size: 11px; color: var(--dev-muted); margin: 4px 0 0;">Response time</p>
            </div>
        </div>

        <!-- 7-Day Chart Box -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <p class="widget-box-title">Requests (Past 7 Days)</p>
            <div class="widget-box-content" style="padding: 24px;">
                <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 160px; gap: 12px; padding-top: 20px;">
                    @php
                        $maxCount = max(array_values($daysData)) ?: 1;
                    @endphp
                    @foreach($daysData as $day => $c)
                        @php
                            $heightPercent = max(8, round(($c / $maxCount) * 100));
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                            <span style="font-size: 11px; font-weight: 700; color: var(--dev-muted); margin-bottom: 6px;">{{ $c }}</span>
                            <div style="width: 100%; max-width: 38px; height: {{ $heightPercent }}%; background: linear-gradient(180deg, var(--dev-accent), rgba(97, 93, 250, 0.4)); border-radius: 6px 6px 0 0; transition: height 0.3s ease;"></div>
                            <span style="font-size: 10px; color: var(--dev-muted); margin-top: 8px; font-family: monospace;">{{ \Carbon\Carbon::parse($day)->format('M d') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Endpoints Table -->
        <div class="widget-box dev-panel">
            <p class="widget-box-title">Top Requested Endpoints</p>
            <div class="widget-box-content padding-none">
                @if($topEndpoints->isEmpty())
                    <div style="text-align: center; padding: 28px; color: var(--dev-muted);">
                        No endpoint telemetry captured yet.
                    </div>
                @else
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dev-border); text-align: left; background: var(--dev-surface-soft);">
                                <th style="padding: 12px 16px;">Endpoint</th>
                                <th style="padding: 12px 16px; text-align: right;">Requests</th>
                                <th style="padding: 12px 16px; text-align: right;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topEndpoints as $ep)
                                @php
                                    $pct = $totalRequests > 0 ? round(($ep->count / $totalRequests) * 100, 1) : 0;
                                @endphp
                                <tr style="border-bottom: 1px solid var(--dev-border);">
                                    <td style="padding: 12px 16px; font-family: monospace; font-weight: 600; color: var(--dev-title);">
                                        {{ $ep->endpoint }}
                                    </td>
                                    <td style="padding: 12px 16px; text-align: right; font-weight: 700;">
                                        {{ number_format($ep->count) }}
                                    </td>
                                    <td style="padding: 12px 16px; text-align: right; color: var(--dev-muted);">
                                        {{ $pct }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>
@endsection
