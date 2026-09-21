@extends('admin::layouts.admin')

@section('title', __('messages.site_health') ?? 'Site Health')

@section('content')
<style>
    .health-score-ring {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        position: relative;
        font-weight: 800;
        font-size: 2rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    .health-score-inner {
        width: 106px;
        height: 106px;
        border-radius: 50%;
        background: var(--bs-card-bg, #ffffff);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .health-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .health-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
</style>

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.site_health') ?? 'Site Health' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.health') ?? 'Health' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.site_health') ?? 'Site Health & Readiness' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.site_health_desc') ?? 'Comprehensive structural audit of server health, database, queues, security, and scheduled jobs.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.site_health', ['fresh' => 1]) }}" class="btn btn-primary">
                <i class="feather-refresh-cw me-2"></i>{{ __('messages.health_refresh') ?? 'Re-run Diagnostic Audit' }}
            </a>
            <a href="{{ route('admin.system_monitor') }}" class="btn btn-light border">
                <i class="feather-activity me-2 text-primary"></i>{{ __('messages.system_monitor') ?? 'System Monitor' }}
            </a>
        </div>
    </section>

    @php
        $score = $audit['score'] ?? 100;
        $grade = $audit['grade'] ?? 'excellent';
        $ringColor = match($grade) {
            'excellent' => '#10b981',
            'good' => '#f59e0b',
            default => '#ef4444',
        };
    @endphp

    <section class="admin-panel mt-4">
        <div class="row g-4">
            <!-- Left Summary Rail -->
            <div class="col-lg-4">
                <div class="sticky-lg-top d-flex flex-column gap-4" style="top: 1.5rem;">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <span class="admin-panel__eyebrow mb-3">{{ __('messages.site_health_score') }}</span>
                        
                        <div class="health-score-ring" style="background: conic-gradient({{ $ringColor }} {{ $score }}%, #e9ecef 0);">
                            <div class="health-score-inner">
                                <span style="color: {{ $ringColor }};">{{ $score }}%</span>
                                <span style="font-size: 0.7rem; text-transform: uppercase; color: #8f91ac;">Score</span>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-1">{{ $audit['grade_label'] }}</h4>
                        <p class="text-muted small mb-3">{{ __('messages.health_all_good') }}</p>

                        <div class="d-flex flex-column gap-2 border-top pt-3 text-start">
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">Last Audit:</span>
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($audit['audited_at'])->diffForHumans() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">Status:</span>
                                <span class="badge" style="background: {{ $ringColor }}; color: #fff;">{{ strtoupper($grade) }}</span>
                            </div>
                        </div>
                    </div>

                    @if(!empty($audit['recommendations']))
                    <div class="card border-0 shadow-sm p-4 bg-soft-warning border-start border-4 border-warning">
                        <h6 class="fw-bold text-dark mb-2"><i class="feather-alert-triangle me-2 text-warning"></i>{{ __('messages.health_recommendation') }}</h6>
                        <ul class="mb-0 ps-3 small text-dark">
                            @foreach($audit['recommendations'] as $rec)
                                <li class="mb-1">{{ $rec }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Diagnostic Checks -->
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-3">
                    @foreach($audit['checks'] as $key => $check)
                        @php
                            $status = $check['status'] ?? 'passed';
                            $badgeClass = match($status) {
                                'passed' => 'bg-success text-white',
                                'warning' => 'bg-warning text-dark',
                                'failed' => 'bg-danger text-white',
                                default => 'bg-secondary text-white',
                            };
                            $iconClass = match($status) {
                                'passed' => 'feather-check-circle text-success',
                                'warning' => 'feather-alert-circle text-warning',
                                'failed' => 'feather-x-circle text-danger',
                                default => 'feather-info text-secondary',
                            };
                        @endphp
                        <div class="card health-card shadow-sm p-3">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="mt-1" style="font-size: 1.4rem;">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1 fs-6">{{ $check['name'] }}</h5>
                                        <p class="text-muted small mb-2">{{ $check['details'] }}</p>
                                        @if(!empty($check['warning']))
                                            <div class="alert alert-warning py-1 px-2 small mb-2 d-inline-block">
                                                <i class="feather-alert-triangle me-1"></i> {{ $check['warning'] }}
                                            </div>
                                        @endif
                                        @if(!empty($check['recommendation']))
                                            <div class="alert alert-danger py-1 px-2 small mb-2 d-inline-block">
                                                <i class="feather-tool me-1"></i> {{ $check['recommendation'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge {{ $badgeClass }} mb-2 px-3 py-1">{{ strtoupper($status) }}</span>
                                    @if(!empty($check['action_url']))
                                        <div>
                                            <a href="{{ $check['action_url'] }}" class="btn btn-xs btn-outline-primary small" style="font-size: 0.75rem; border-radius: 20px;">
                                                {{ $check['action_text'] ?? 'Open' }} <i class="feather-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
