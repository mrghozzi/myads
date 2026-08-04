@extends('admin::layouts.admin')

@section('title', __('messages.system_monitor') ?? 'System Monitor')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.system_monitor') ?? 'System Monitor' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.health') ?? 'Health' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.system_monitor') ?? 'System Monitor' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.system_monitor_desc') ?? 'Real-time overview of your server\'s resource consumption and application health.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.shared_hosting_guide') }}" class="btn btn-primary">
                <i class="feather-book-open me-2"></i>{{ __('messages.pressure_guide_button') }}
            </a>
            <form action="{{ route('admin.system_monitor.clear_cache') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('{{ __('messages.clear_system_cache_confirm') ?? 'Are you sure you want to clear the system cache? This may cause a temporary spike in CPU as caches are rebuilt.' }}')">
                    <i class="feather-trash-2 me-2"></i>{{ __('messages.clear_system_cache') ?? 'Clear System Cache' }}
                </button>
            </form>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel mt-4">
        <div class="row g-4">
            <!-- Vitals rail -->
            <div class="col-lg-4">
                <div class="sticky-lg-top d-flex flex-column gap-4" style="top: 1.5rem;">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="admin-panel__eyebrow mb-0">{{ __('messages.system_vitals') }}</span>
                                <a href="{{ route('admin.system_monitor') }}" class="btn btn-sm btn-light border" title="{{ __('messages.refresh') }}">
                                    <i class="feather-refresh-cw"></i>
                                </a>
                            </div>

                            @php
                                $cpuRingColor = $cpuPercent >= 100 ? 'danger' : ($cpuPercent >= 60 ? 'warning' : 'success');
                                $ramRingColor = $ramPercent === null ? 'secondary' : ($ramPercent >= 90 ? 'danger' : ($ramPercent >= 70 ? 'warning' : 'success'));
                                $diskRingColor = $diskUsedPercent === null ? 'secondary' : ($diskUsedPercent >= 90 ? 'danger' : ($diskUsedPercent >= 75 ? 'warning' : 'success'));
                            @endphp

                            <div class="vitals-row d-flex align-items-center gap-3">
                                <div class="vitals-ring" style="background: conic-gradient(var(--bs-{{ $cpuRingColor }}) {{ $cpuPercent }}%, #e9ecef 0);">
                                    <span>{{ $cpuPercent }}%</span>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">{{ __('messages.cpu_usage') }}</div>
                                    <div class="fw-bold">{{ __('messages.load_label') }}: {{ number_format($load[0], 2) }}</div>
                                </div>
                            </div>

                            <div class="vitals-row d-flex align-items-center gap-3">
                                <div class="vitals-ring" style="background: conic-gradient(var(--bs-{{ $ramRingColor }}) {{ $ramPercent ?? 0 }}%, #e9ecef 0);">
                                    <span>{{ $ramPercent !== null ? $ramPercent . '%' : '∞' }}</span>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">{{ __('messages.ram_usage') }}</div>
                                    <div class="fw-bold">{{ number_format($memoryUsage / 1048576, 2) }} MB / {{ $ramPercent !== null ? $memoryLimit : __('messages.unlimited') }}</div>
                                </div>
                            </div>

                            <div class="vitals-row d-flex align-items-center gap-3">
                                <div class="vitals-ring" style="background: conic-gradient(var(--bs-{{ $diskRingColor }}) {{ $diskUsedPercent ?? 0 }}%, #e9ecef 0);">
                                    <span>{{ $diskUsedPercent !== null ? $diskUsedPercent . '%' : '—' }}</span>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">{{ __('messages.disk_usage') }}</div>
                                    <div class="fw-bold">{{ $diskFree > 0 ? number_format($diskFree / 1073741824, 2) . ' GB' : __('messages.not_available') }}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center vitals-status-row">
                                    <span class="text-muted small text-uppercase fw-bold">{{ __('messages.opcache_status') }}</span>
                                    <span class="badge {{ $opcacheEnabled ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $opcacheEnabled ? __('messages.enabled') : __('messages.disabled') }}
                                        @if($opcacheEnabled && $opcacheHitRate !== null)
                                            &bull; {{ $opcacheHitRate }}%
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center vitals-status-row">
                                    <span class="text-muted small text-uppercase fw-bold">{{ __('messages.failed_jobs') }}</span>
                                    <span class="badge {{ $failedJobsCount === null ? 'bg-secondary' : ($failedJobsCount > 0 ? 'bg-danger' : 'bg-success') }}">
                                        @if($failedJobsCount === null)
                                            {{ __('messages.not_available') }}
                                        @elseif($failedJobsCount > 0)
                                            {{ __('messages.failed_jobs_critical', ['count' => $failedJobsCount]) }}
                                        @else
                                            {{ __('messages.failed_jobs_none') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center vitals-status-row">
                                    <span class="text-muted small text-uppercase fw-bold">{{ __('messages.scheduler_status') }}</span>
                                    <span class="badge {{ $schedulerStale ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">
                                        {{ $schedulerLastRun ? $schedulerLastRun->diffForHumans() : __('messages.scheduler_never_run') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-soft-primary">
                        <div class="card-body">
                            <p class="text-primary fw-bold small text-uppercase mb-2">{{ __('messages.operational_note_title') }}</p>
                            <p class="text-primary small mb-0">{{ __('messages.operational_note_desc') }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Main diagnostics column -->
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-4">

                    <div class="row g-4">
                        <!-- Memory Usage detail -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-soft-success text-success rounded p-2 me-3">
                                            <i class="feather-database fs-4"></i>
                                        </div>
                                        <h5 class="card-title mb-0">{{ __('messages.memory_usage_ram') ?? 'Memory Usage (RAM)' }}</h5>
                                    </div>
                                    <ul class="list-group list-group-flush mt-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.current_usage') ?? 'Current Usage' }}</span>
                                            <span class="fw-bold">{{ number_format($memoryUsage / 1048576, 2) }} MB</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.peak_usage') ?? 'Peak Usage' }}</span>
                                            <span class="fw-bold">{{ number_format($memoryPeak / 1048576, 2) }} MB</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.php_memory_limit') ?? 'PHP Memory Limit' }}</span>
                                            <span class="fw-bold">{{ $memoryLimit }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Storage & Cache detail -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-soft-warning text-warning rounded p-2 me-3">
                                            <i class="feather-hard-drive fs-4"></i>
                                        </div>
                                        <h5 class="card-title mb-0">{{ __('messages.storage_cache') ?? 'Storage & Cache' }}</h5>
                                    </div>
                                    <ul class="list-group list-group-flush mt-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.framework_cache_size') ?? 'Framework Cache Size' }}</span>
                                            <span class="fw-bold">{{ number_format($cacheSize / 1048576, 2) }} MB</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.total_disk_space') ?? 'Total Disk Space' }}</span>
                                            <span class="fw-bold">{{ $diskTotal > 0 ? number_format($diskTotal / 1073741824, 2) . ' GB' : __('messages.not_available') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('messages.free_disk_space') ?? 'Free Disk Space' }}</span>
                                            <span class="fw-bold text-success">{{ $diskFree > 0 ? number_format($diskFree / 1073741824, 2) . ' GB' : __('messages.not_available') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Critical PHP Extensions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title mb-1">{{ __('messages.critical_extensions_title') }}</h5>
                                <p class="text-muted mb-0 small">{{ __('messages.critical_extensions_desc') }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-2 row-cols-md-3 g-3">
                                @foreach($criticalExtensions as $extension)
                                    <div class="col">
                                        <div class="extension-check">
                                            <span class="small text-muted">{{ $extension['label'] }}</span>
                                            @if($extension['loaded'])
                                                <i class="feather-check-circle text-success"></i>
                                            @else
                                                <i class="feather-alert-circle text-danger"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Pressure Sources -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                                <div>
                                    <h5 class="card-title mb-1">{{ __('messages.pressure_sources_title') }}</h5>
                                    <p class="text-muted mb-0">{{ __('messages.pressure_sources_desc') }}</p>
                                </div>
                                <a href="{{ route('admin.shared_hosting_guide') }}" class="btn btn-outline-primary">
                                    <i class="feather-book-open me-2"></i>{{ __('messages.pressure_view_tips') }}
                                </a>
                            </div>

                            <div class="row g-3">
                                @foreach($pressureSources as $source)
                                    @php
                                        $severity = $source['severity'] ?? 'info';
                                        $badgeClass = match ($severity) {
                                            'danger' => 'bg-danger',
                                            'warning' => 'bg-warning text-dark',
                                            'success' => 'bg-success',
                                            default => 'bg-info text-dark',
                                        };
                                        $borderClass = match ($severity) {
                                            'danger' => 'border-danger',
                                            'warning' => 'border-warning',
                                            'success' => 'border-success',
                                            default => 'border-info',
                                        };
                                    @endphp
                                    <div class="col-lg-6">
                                        <div class="pressure-source-card border {{ $borderClass }}">
                                            <div class="d-flex align-items-start justify-content-between gap-3">
                                                <div>
                                                    <span class="badge {{ $badgeClass }} mb-2">{{ __('messages.pressure_severity_' . $severity) }}</span>
                                                    <h6 class="fw-bold mb-2">{{ $source['title'] }}</h6>
                                                    <p class="text-muted mb-2">{{ $source['description'] }}</p>
                                                    <div class="small fw-semibold">{{ $source['action'] }}</div>
                                                </div>
                                                @if(!empty($source['route']) && Route::has($source['route']))
                                                    <a href="{{ route($source['route']) }}" class="btn btn-sm btn-light border flex-shrink-0">
                                                        <i class="feather-arrow-left"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Storage Disks & Drivers -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title mb-1">{{ __('messages.storage_disks_title') }}</h5>
                                <p class="text-muted mb-0 small">{{ __('messages.storage_disks_desc') }}</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>{{ __('messages.disk_name_col') }}</th>
                                        <th>{{ __('messages.disk_driver_col') }}</th>
                                        <th>{{ __('messages.disk_usage_col') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($storageDisks as $disk)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="feather-{{ $disk['driver'] === 'local' ? 'hard-drive' : 'cloud' }} text-primary"></i>
                                                    <span class="fw-bold text-capitalize">{{ $disk['name'] }}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $disk['driver'] }}</span></td>
                                            <td>
                                                @if($disk['usedBytes'] !== null)
                                                    <span class="fw-bold">{{ number_format($disk['usedBytes'] / 1048576, 2) }} MB</span>
                                                @else
                                                    <span class="text-muted small text-uppercase">{{ __('messages.disk_remote_note') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- App Environment -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('messages.environment_overview') ?? 'Environment Overview' }}</h5>
                            <div class="row text-center">
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="text-muted small mb-1">{{ __('messages.php_version') ?? 'PHP Version' }}</div>
                                    <div class="fw-bold fs-5">{{ phpversion() }}</div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="text-muted small mb-1">{{ __('messages.laravel_version') ?? 'Laravel Version' }}</div>
                                    <div class="fw-bold fs-5">{{ app()->version() }}</div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="text-muted small mb-1">{{ __('messages.environment') ?? 'Environment' }}</div>
                                    <div class="fw-bold fs-5"><span class="badge bg-primary">{{ env('APP_ENV', 'production') }}</span></div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="text-muted small mb-1">{{ __('messages.debug_mode') ?? 'Debug Mode' }}</div>
                                    <div class="fw-bold fs-5">
                                        @if(env('APP_DEBUG'))
                                            <span class="badge bg-danger">{{ __('messages.enabled') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('messages.disabled') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .pressure-source-card {
        height: 100%;
        padding: 18px;
        border-radius: 8px;
        background: var(--bs-body-bg);
        border-inline-start-width: 4px !important;
    }

    .pressure-source-card .btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .vitals-ring {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .vitals-ring::before {
        content: "";
        position: absolute;
        inset: 6px;
        border-radius: 50%;
        background: var(--admin-premium-surface, var(--bs-body-bg));
    }

    .vitals-ring span {
        position: relative;
        z-index: 1;
    }

    .vitals-row + .vitals-row {
        margin-top: 1.25rem;
    }

    .vitals-status-row + .vitals-status-row {
        margin-top: 0.65rem;
    }

    .extension-check {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem 0;
    }
</style>
@endsection
