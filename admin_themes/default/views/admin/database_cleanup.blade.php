@extends('admin::layouts.admin')

@section('title', __('messages.database_cleanup'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.database_cleanup') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.maintenance') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.database_cleanup') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.database_cleanup_desc') }}</p>
        </div>
    </section>

    <div class="admin-content-inner">

        {{-- v4.4.4: Table Sizes Overview --}}
        <div class="row mb-4">
            @foreach($tableSizes as $table => $stats)
            <div class="col-md-4 col-lg mb-3">
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small text-uppercase mb-1">{{ str_replace('_', ' ', $table) }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['rows']) }}</h3>
                        <span class="badge bg-soft-primary text-primary">{{ $stats['size_mb'] }} MB</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- v4.4.4: Auto-Cleanup Settings --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="feather-settings me-2"></i>{{ __('messages.auto_cleanup_settings') ?? 'Auto-Cleanup Settings' }}
                        </h5>
                        @if($autoCleanupEnabled)
                            <span class="badge bg-soft-success text-success">{{ __('messages.enabled') ?? 'Enabled' }}</span>
                        @else
                            <span class="badge bg-soft-danger text-danger">{{ __('messages.disabled') ?? 'Disabled' }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.database_cleanup.action') }}" method="POST">
                            @csrf
                            <input type="hidden" name="save_settings" value="1">

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="auto_cleanup_enabled" name="auto_cleanup_enabled" value="1" {{ $autoCleanupEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_cleanup_enabled">
                                    <strong>{{ __('messages.enable_auto_cleanup') ?? 'Enable Automatic Cleanup' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ __('messages.auto_cleanup_hint') ?? 'Automatically prune old records on a probabilistic basis (1% chance per ad request). No cron setup required.' }}</small>
                                </label>
                            </div>

                            <h6 class="mb-3">{{ __('messages.retention_periods') ?? 'Data Retention Periods (Days)' }}</h6>
                            <div class="row">
                                <div class="col-md-4 col-lg mb-3">
                                    <label class="form-label small text-muted">{{ __('messages.state_table') ?? 'Ad Event Logs' }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="retention_state" class="form-control" min="1" value="{{ $retentionDays['state'] }}">
                                        <span class="input-group-text">{{ __('messages.days') ?? 'days' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg mb-3">
                                    <label class="form-label small text-muted">{{ __('messages.banner_impressions') ?? 'Banner Impressions' }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="retention_banner_impressions" class="form-control" min="1" value="{{ $retentionDays['banner_impressions'] }}">
                                        <span class="input-group-text">{{ __('messages.days') ?? 'days' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg mb-3">
                                    <label class="form-label small text-muted">{{ __('messages.smart_ad_impressions') ?? 'Smart Ad Impressions' }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="retention_smart_ad_impressions" class="form-control" min="1" value="{{ $retentionDays['smart_ad_impressions'] }}">
                                        <span class="input-group-text">{{ __('messages.days') ?? 'days' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg mb-3">
                                    <label class="form-label small text-muted">{{ __('messages.seo_daily_metrics') ?? 'SEO Metrics' }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="retention_seo_daily_metrics" class="form-control" min="1" value="{{ $retentionDays['seo_daily_metrics'] }}">
                                        <span class="input-group-text">{{ __('messages.days') ?? 'days' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg mb-3">
                                    <label class="form-label small text-muted">{{ __('messages.custom_ad_events') ?? 'Custom Ad Events' }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="retention_custom_ad_events" class="form-control" min="1" value="{{ $retentionDays['custom_ad_events'] }}">
                                        <span class="input-group-text">{{ __('messages.days') ?? 'days' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="feather-save me-1"></i> {{ __('messages.save_settings') ?? 'Save Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Manual Cleanup --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-trash-2 me-2"></i>{{ __('messages.manual_cleanup') ?? 'Manual Cleanup' }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.database_cleanup.action') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.state_table') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($stateCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="state_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.banner_impressions') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($bannerImpressionsCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="banner_impressions_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.seo_daily_metrics') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($seoMetricsCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="seo_metrics_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.confirm_cleanup') }}')">
                                    <i class="feather-trash-2 me-2"></i> {{ __('messages.execute_cleanup') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- v4.4.4: Storage Cleanup --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="mb-1"><i class="feather-hard-drive me-2"></i>{{ __('messages.cache_files') ?? 'Cache Files' }}</h6>
                                <p class="text-muted small mb-0">{{ __('messages.expired_cache_hint') ?? 'Remove expired cache files to free disk space' }}</p>
                            </div>
                            <span class="badge bg-soft-warning text-warning fs-6">{{ $cacheSize }} MB</span>
                        </div>
                        <form action="{{ route('admin.database_cleanup.action') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" name="prune_cache" value="1" class="btn btn-outline-warning btn-sm">
                                <i class="feather-trash me-1"></i> {{ __('messages.prune_expired') ?? 'Prune Expired' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="mb-1"><i class="feather-users me-2"></i>{{ __('messages.session_files') ?? 'Session Files' }}</h6>
                                <p class="text-muted small mb-0">{{ __('messages.stale_sessions_hint') ?? 'Remove stale session files that are no longer active' }}</p>
                            </div>
                            <span class="badge bg-soft-info text-info fs-6">{{ $sessionSize }} MB</span>
                        </div>
                        <form action="{{ route('admin.database_cleanup.action') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" name="prune_sessions" value="1" class="btn btn-outline-info btn-sm">
                                <i class="feather-trash me-1"></i> {{ __('messages.prune_stale') ?? 'Prune Stale' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
