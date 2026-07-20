@extends('admin::layouts.admin')

@section('title', __('messages.shg_title'))

@section('content')
@php
    $isFileCache = ($environment['cache_store'] ?? 'file') === 'file';
    $isFileSession = ($environment['session_driver'] ?? 'file') === 'file';
    $isDebug = !empty($environment['debug']);
@endphp

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.shg_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.shg_performance') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.shg_hero_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.shg_hero_copy') }}</p>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('messages.shg_cache_system') }}</div>
                        <div class="fw-bold fs-5">{{ $environment['cache_store'] }}</div>
                        <span class="badge {{ $isFileCache ? 'bg-warning text-dark' : 'bg-success' }} mt-3">{{ $isFileCache ? __('messages.shg_needs_attention') : __('messages.shg_good') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('messages.shg_session_system') }}</div>
                        <div class="fw-bold fs-5">{{ $environment['session_driver'] }}</div>
                        <span class="badge {{ $isFileSession ? 'bg-warning text-dark' : 'bg-success' }} mt-3">{{ $isFileSession ? __('messages.shg_many_files') : __('messages.shg_good') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('messages.shg_debug_mode') }}</div>
                        <div class="fw-bold fs-5">{{ $isDebug ? __('messages.shg_enabled') : __('messages.shg_disabled') }}</div>
                        <span class="badge {{ $isDebug ? 'bg-danger' : 'bg-success' }} mt-3">{{ $isDebug ? __('messages.shg_disable_in_prod') : __('messages.shg_good') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ __('messages.shg_php_memory') }}</div>
                        <div class="fw-bold fs-5">{{ $environment['php_memory_limit'] }}</div>
                        <span class="badge bg-primary mt-3">{{ __('messages.shg_current_val') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="feather-sliders me-2 text-primary"></i>{{ __('messages.shg_env_suggested') }}</h5>
                        <p class="text-muted">{{ __('messages.shg_env_suggested_desc') }}</p>
                        <pre class="bg-light border rounded p-3 mb-0"><code>APP_DEBUG=false
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
LOG_LEVEL=error</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="feather-alert-triangle me-2 text-warning"></i>{{ __('messages.shg_cpu_file_usage') }}</h5>
                        <ul class="list-unstyled mb-0 shared-hosting-list">
                            <li>{{ __('messages.shg_tip_1') }}</li>
                            <li>{{ __('messages.shg_tip_2') }}</li>
                            <li>{{ __('messages.shg_tip_3') }}</li>
                            <li>{{ __('messages.shg_tip_4') }}</li>
                            <li>{{ __('messages.shg_tip_5') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="feather-database me-2 text-success"></i>{{ __('messages.shg_db_cleanup') }}</h5>
                        <p class="text-muted">{{ __('messages.shg_db_cleanup_desc') }}</p>
                        <a class="btn btn-outline-primary" href="{{ route('admin.database_cleanup') }}">{{ __('messages.shg_open_db_cleanup') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="feather-activity me-2 text-danger"></i>{{ __('messages.shg_perf_settings') }}</h5>
                        <p class="text-muted">{{ __('messages.shg_perf_settings_desc') }}</p>
                        <a class="btn btn-outline-primary" href="{{ route('admin.settings.performance') }}">{{ __('messages.shg_open_perf_settings') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="feather-clock me-2 text-info"></i>{{ __('messages.shg_cron_jobs') }}</h5>
                        <p class="text-muted">{{ __('messages.shg_cron_jobs_desc') }}</p>
                        <pre class="bg-light border rounded p-3 mb-0"><code>* * * * * php /path/to/artisan schedule:run</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="feather-check-circle me-2 text-success"></i>{{ __('messages.shg_safe_list') }}</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <ul class="mb-0 shared-hosting-list">
                            <li>{!! __('messages.shg_safe_1') !!}</li>
                            <li>{{ __('messages.shg_safe_2') }}</li>
                            <li>{{ __('messages.shg_safe_3') }}</li>
                            <li>{{ __('messages.shg_safe_4') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0 shared-hosting-list">
                            <li>{{ __('messages.shg_safe_5') }}</li>
                            <li>{{ __('messages.shg_safe_6') }}</li>
                            <li>{{ __('messages.shg_safe_7') }}</li>
                            <li>{{ __('messages.shg_safe_8') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .shared-hosting-list li {
        position: relative;
        padding-left: 1.4rem;
        margin-bottom: .7rem;
        color: var(--bs-body-color);
    }

    .shared-hosting-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: .55rem;
        width: .45rem;
        height: .45rem;
        border-radius: 50%;
        background: var(--bs-primary);
    }

    body[dir="rtl"] .shared-hosting-list li {
        padding-left: 0;
        padding-right: 1.4rem;
    }

    body[dir="rtl"] .shared-hosting-list li::before {
        left: auto;
        right: 0;
    }
</style>
@endsection
