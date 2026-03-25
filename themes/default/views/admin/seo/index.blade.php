@extends('theme::layouts.admin')

@section('title', __('messages.seo_dashboard'))

@section('content')
<div class="seo-shell">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="mb-1">{{ __('messages.seo_dashboard') }}</h3>
            <p class="text-muted mb-0">{{ __('messages.seo_dashboard_intro') }}</p>
        </div>
        <a href="{{ route('sitemap.xml') }}" target="_blank" class="btn btn-outline-primary">
            <i class="feather-external-link me-2"></i>{{ __('messages.seo_open_sitemap') }}
        </a>
    </div>

    @include('theme::admin.seo.partials.nav')
    @include('theme::admin.seo.partials.alerts')

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_score') }}</div>
                <div class="value">{{ $dashboard['score'] }}/100</div>
                <div class="mt-2">
                    <span class="seo-pill {{ $dashboard['score'] >= 85 ? 'ok' : ($dashboard['score'] >= 65 ? 'warn' : 'bad') }}">
                        <i class="feather-bar-chart-2"></i>
                        {{ $dashboard['score'] >= 85 ? __('messages.seo_health_healthy') : ($dashboard['score'] >= 65 ? __('messages.seo_health_attention') : __('messages.seo_health_critical')) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_indexable_urls') }}</div>
                <div class="value">{{ number_format($dashboard['summary_cards']['indexable_urls']) }}</div>
                <div class="seo-form-note mt-2">{{ __('messages.seo_indexable_urls_dashboard_note') }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_users_posts') }}</div>
                <div class="value">{{ number_format($dashboard['summary_cards']['users']) }} / {{ number_format($dashboard['summary_cards']['posts']) }}</div>
                <div class="seo-form-note mt-2">{{ __('messages.seo_users_posts_note') }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_content_inventory') }}</div>
                <div class="value">{{ number_format($dashboard['summary_cards']['news'] + $dashboard['summary_cards']['pages'] + $dashboard['summary_cards']['topics'] + $dashboard['summary_cards']['listings'] + $dashboard['summary_cards']['products']) }}</div>
                <div class="seo-form-note mt-2">{{ __('messages.seo_content_inventory_note') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($dashboard['checks'] as $check)
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="seo-stat">
                    <div class="label">{{ $check['label'] }}</div>
                    <div class="mt-2">
                        <span class="seo-pill {{ $check['healthy'] ? 'ok' : 'warn' }}">
                            <i class="feather-{{ $check['healthy'] ? 'check-circle' : 'alert-circle' }}"></i>
                            {{ $check['value'] }}
                        </span>
                    </div>
                    <div class="seo-form-note mt-3">{{ $check['hint'] }}</div>
                </div>
            </div>
        @endforeach
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="seo-stat">
                <div class="label">{{ __('messages.seo_sitemap_status') }}</div>
                <div class="mt-2">
                    <span class="seo-pill ok">
                        <i class="feather-map"></i>
                        {{ __('messages.seo_health_healthy') }}
                    </span>
                </div>
                <div class="seo-form-note mt-3">
                    <a href="{{ route('sitemap.xml') }}" target="_blank" class="text-decoration-none">
                        {{ __('messages.seo_preview_sitemap') }} <i class="feather-external-link ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card seo-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">{{ __('messages.seo_traffic_trends') }}</h5>
                            <p class="text-muted mb-0">{{ __('messages.seo_traffic_trends_note') }}</p>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary seo-window-btn {{ $chartWindow === '7' ? 'active' : '' }}" data-window="7">{{ __('messages.seo_window_days', ['days' => 7]) }}</button>
                            <button type="button" class="btn btn-outline-primary seo-window-btn {{ $chartWindow === '30' ? 'active' : '' }}" data-window="30">{{ __('messages.seo_window_days', ['days' => 30]) }}</button>
                            <button type="button" class="btn btn-outline-primary seo-window-btn {{ $chartWindow === '90' ? 'active' : '' }}" data-window="90">{{ __('messages.seo_window_days', ['days' => 90]) }}</button>
                        </div>
                    </div>
                    <div class="seo-chart-wrap seo-chart-wrap--tall">
                        <canvas id="seoTrafficChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <h5 class="mb-1">{{ __('messages.seo_top_scopes') }}</h5>
                    <p class="text-muted">{{ __('messages.seo_top_scopes_note') }}</p>
                    <div class="seo-chart-wrap">
                        <canvas id="seoTopScopesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.seo_priority_issues') }}</h5>
                    @forelse($dashboard['issues'] as $issue)
                        <div class="d-flex gap-3 align-items-start rounded-4 p-3 mb-3" style="background: rgba(248, 250, 252, 0.9); border: 1px solid rgba(148, 163, 184, 0.15);">
                            <span class="seo-pill {{ $issue['severity'] === 'critical' ? 'bad' : 'warn' }}">{{ __('messages.seo_severity_' . $issue['severity']) }}</span>
                            <div>
                                <div class="fw-semibold text-dark">{{ $issue['title'] }}</div>
                                <div class="text-muted">{{ $issue['action'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-4 p-3" style="background: rgba(16, 185, 129, 0.08); color: #047857;">
                            {{ __('messages.seo_no_priority_issues') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card seo-card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.seo_top_content_pages') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.seo_page') }}</th>
                                    <th>{{ __('messages.seo_scope') }}</th>
                                    <th>{{ __('messages.seo_views') }}</th>
                                    <th>{{ __('messages.seo_visitors') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dashboard['top_pages'] as $page)
                                    <tr>
                                        <td class="fw-semibold">{{ $page['label'] }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $page['scope_label'] ?? $page['scope_key'] }}</span></td>
                                        <td>{{ number_format($page['page_views']) }}</td>
                                        <td>{{ number_format($page['unique_visitors']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted text-center py-4">{{ __('messages.seo_traffic_empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartSets = @json($dashboard['chart_sets']);
    const topScopes = @json($dashboard['top_scopes']);
    const trafficCanvas = document.getElementById('seoTrafficChart');
    const topScopesCanvas = document.getElementById('seoTopScopesChart');
    const buttons = document.querySelectorAll('.seo-window-btn');
    let trafficChart = null;

    function renderTraffic(windowKey) {
        if (!trafficCanvas || !chartSets[windowKey]) {
            return;
        }

        const set = chartSets[windowKey];
        if (trafficChart) {
            trafficChart.destroy();
        }

        trafficChart = new Chart(trafficCanvas, {
            type: 'line',
            data: {
                labels: set.labels,
                datasets: [
                    {
                        label: @json(__('messages.seo_chart_page_views')),
                        data: set.page_views,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    },
                    {
                        label: @json(__('messages.seo_chart_unique_visitors')),
                        data: set.unique_visitors,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    },
                    {
                        label: @json(__('messages.seo_chart_bot_hits')),
                        data: set.bot_hits,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            buttons.forEach(function (item) { item.classList.remove('active'); });
            button.classList.add('active');
            renderTraffic(button.dataset.window);
        });
    });

    renderTraffic(@json($chartWindow));

    if (topScopesCanvas) {
        new Chart(topScopesCanvas, {
            type: 'bar',
            data: {
                labels: topScopes.map(function (item) { return item.label || item.scope_key; }),
                datasets: [{
                    label: @json(__('messages.seo_chart_page_views')),
                    data: topScopes.map(function (item) { return item.page_views; }),
                    backgroundColor: 'rgba(79, 70, 229, 0.82)',
                    borderRadius: 10
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
