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
            <!-- Server Load -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-soft-primary text-primary rounded p-2 me-3">
                                <i class="feather-cpu fs-4"></i>
                            </div>
                            <h5 class="card-title mb-0">{{ __('messages.server_load_cpu') ?? 'Server Load (CPU)' }}</h5>
                        </div>
                        <div class="row text-center mt-4">
                            <div class="col-4">
                                <h3 class="mb-1 fw-bold {{ $load[0] > 2 ? 'text-danger' : 'text-success' }}">{{ number_format($load[0], 2) }}</h3>
                                <span class="text-muted small">{{ __('messages.one_min') ?? '1 Min' }}</span>
                            </div>
                            <div class="col-4 border-start border-end">
                                <h3 class="mb-1 fw-bold {{ $load[1] > 2 ? 'text-danger' : 'text-success' }}">{{ number_format($load[1], 2) }}</h3>
                                <span class="text-muted small">{{ __('messages.five_min') ?? '5 Min' }}</span>
                            </div>
                            <div class="col-4">
                                <h3 class="mb-1 fw-bold {{ $load[2] > 2 ? 'text-danger' : 'text-success' }}">{{ number_format($load[2], 2) }}</h3>
                                <span class="text-muted small">{{ __('messages.fifteen_min') ?? '15 Min' }}</span>
                            </div>
                        </div>
                        <div class="mt-4 small text-muted text-center">
                            <i class="feather-info me-1"></i> {{ __('messages.cpu_load_info') ?? 'Values above the number of CPU cores indicate the server is overloaded.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="col-lg-4 col-md-6">
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

            <!-- Storage & Cache -->
            <div class="col-lg-4 col-md-6">
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

            <!-- Pressure Sources -->
            <div class="col-12">
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
            </div>

            <!-- App Environment -->
            <div class="col-12">
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
</style>
@endsection
