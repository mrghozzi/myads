@extends('admin::layouts.admin')

@section('title', __('messages.updates_myads') ?? 'System Updates')

@section('content')
@php
    $maintenanceEnabled = !empty($maintenanceSettings['enabled']);
    $preflightChecks = collect($preflightReport->checks ?? []);
    $failedChecks = $preflightChecks->where('status', '!=', 'passed')->count();
    $passedChecks = $preflightChecks->where('status', 'passed')->count();
    $updateProgressStages = $activeUpdateSession['stages'] ?? [
        ['key' => 'initialize', 'label' => __('messages.update_stage_initialize'), 'icon' => 'shield', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'download', 'label' => __('messages.update_stage_download'), 'icon' => 'download-cloud', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'extract', 'label' => __('messages.update_stage_extract'), 'icon' => 'archive', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'package_preflight', 'label' => __('messages.update_stage_package_preflight'), 'icon' => 'search', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'enable_maintenance', 'label' => __('messages.update_stage_enable_maintenance'), 'icon' => 'tool', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'finalize', 'label' => __('messages.update_stage_finalize'), 'icon' => 'upload-cloud', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
        ['key' => 'cleanup', 'label' => __('messages.update_stage_cleanup'), 'icon' => 'check-circle', 'status' => 'pending', 'percent' => 0, 'detail' => ''],
    ];

    $mysqlVersion = 'MySQL';
    try {
        $mysqlVersion = \Illuminate\Support\Facades\DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
    } catch (\Throwable) {}
@endphp

<div class="admin-page">
    <!-- Admin Hero Header (Superdesign Style) -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.admin_panel') ?? 'Admin' }}</a></li>
                <li>{{ __('messages.updates') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <i class="feather-refresh-cw me-1"></i>{{ __('messages.updates_myads') ?? 'System Updates' }}
            </div>
            <h1 class="admin-hero__title" id="status-title">
                @if($updateAvailable)
                    {{ __('messages.new_version_available') ?? 'New Update Available!' }}
                    <span class="badge bg-primary fs-13 align-middle ms-2">v{{ $latestVersion }}</span>
                @else
                    {{ __('messages.system_up_to_date') ?? 'System is Up to Date' }}
                    <span class="badge bg-soft-success text-success fs-13 align-middle ms-2"><i class="feather-check me-1"></i>v{{ $currentVersion }}</span>
                @endif
            </h1>
            <p class="admin-hero__copy" id="status-subtitle">
                @if($updateAvailable)
                    {{ __('messages.update_available_desc') ?? 'A newer release is available on GitHub with performance upgrades, security patches, and fixes.' }}
                @else
                    {{ __('messages.up_to_date_desc') ?? 'Your platform is operating on the official latest version. No pending updates.' }}
                @endif
            </p>

            <!-- Quick Metrics Strip -->
            <div class="admin-stat-strip" id="version-display">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.installed') ?? 'Installed Version' }}</span>
                    <span class="admin-stat-value text-primary">v{{ $currentVersion }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.latest') ?? 'Latest Release' }}</span>
                    <span class="admin-stat-value {{ $updateAvailable ? 'text-warning' : 'text-success' }}">
                        {{ $latestVersion ? 'v' . $latestVersion : '--' }}
                    </span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.update_preflight_title') }}</span>
                    <span class="admin-stat-value {{ $preflightReport->isSafe() ? 'text-success' : 'text-danger' }}">
                        {{ $passedChecks }}/{{ $preflightChecks->count() }}
                    </span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.maintenance_mode') ?? 'Maintenance' }}</span>
                    <span class="admin-stat-value fs-15 {{ $maintenanceEnabled ? 'text-warning' : 'text-muted' }}">
                        {{ $maintenanceEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div class="admin-modal-icon {{ $updateAvailable ? 'is-warning' : 'is-primary' }} mb-0" id="status-icon">
                        <i class="feather-{{ $updateAvailable ? 'arrow-up-circle' : 'shield' }}"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.current_version') ?? 'Version' }}</span>
                        <div class="admin-panel__title mb-0">v{{ $currentVersion }}</div>
                        <div class="admin-muted fs-12">
                            @if($latestVersion)
                                {{ __('messages.target_version') ?? 'Target' }}: v{{ $latestVersion }}
                            @else
                                {{ __('messages.check_for_updates') }}
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary w-100 mt-3 fw-bold" id="btn-check-update" onclick="checkForUpdates()">
                    <i class="feather-refresh-cw me-2"></i>{{ __('messages.check_for_updates') }}
                </button>
            </div>

            <div class="admin-chip-list">
                <span class="admin-chip {{ $preflightReport->isSafe() ? 'text-success' : 'text-danger' }}">
                    <i class="feather-shield"></i>
                    {{ $preflightReport->isSafe() ? __('messages.update_preflight_passed') : __('messages.update_preflight_failed') }}
                </span>
                @if($latestRelease && !empty($latestRelease['published_at']))
                    <span class="admin-chip">
                        <i class="feather-calendar"></i>
                        {{ \Carbon\Carbon::parse($latestRelease['published_at'])->format('Y-m-d') }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    <!-- Session Notifications -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-0" role="alert">
            <i class="feather-check-circle fs-4 me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-0" role="alert">
            <i class="feather-alert-octagon fs-4 me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info d-flex align-items-center shadow-sm border-0 mb-0" role="alert">
            <i class="feather-info fs-4 me-3"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-0" role="alert">
            <div class="fw-bold mb-2 d-flex align-items-center">
                <i class="feather-alert-triangle me-2"></i>{{ __('messages.warning') ?? 'Warning' }}
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Workspace Grid (8 cols Main / 4 cols Sidebar) -->
    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-xl-8 col-lg-7">
            <div class="d-flex flex-column gap-4">

                <!-- 1. Active Staged Progress Card (Live Stepper) -->
                <section class="admin-panel admin-update-progress-panel" id="update-progress-panel" @if(!$activeUpdateSession) style="display: none;" @endif>
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.update_progress_title') }}</span>
                            <h2 class="admin-panel__title" id="update-progress-stage">
                                {{ $activeUpdateSession['stage_label'] ?? __('messages.update_progress_idle') }}
                            </h2>
                        </div>
                        <span class="badge bg-soft-primary text-primary fs-12 px-3 py-2" id="update-progress-status">
                            {{ $activeUpdateSession['status'] ?? __('messages.pending') }}
                        </span>
                    </div>

                    <div class="admin-panel__body">
                        <!-- Progress Metric & Bar -->
                        <div class="admin-update-progress">
                            <div class="admin-update-progress__meta">
                                <span id="update-progress-detail" class="fw-semibold">{{ $activeUpdateSession['detail'] ?? __('messages.update_progress_description') }}</span>
                                <strong id="update-progress-percent" class="fs-5 text-primary">{{ (int) ($activeUpdateSession['percent'] ?? 0) }}%</strong>
                            </div>
                            <div class="progress admin-update-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) ($activeUpdateSession['percent'] ?? 0) }}">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="update-progress-bar" style="width: {{ (int) ($activeUpdateSession['percent'] ?? 0) }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                                <span id="update-progress-bytes">
                                    @if(!empty($activeUpdateSession['bytes_total']))
                                        {{ __('messages.update_stage_download_detail', [
                                            'downloaded' => $activeUpdateSession['bytes_done'] ?? 0,
                                            'total' => $activeUpdateSession['bytes_total'],
                                        ]) }}
                                    @endif
                                </span>
                                <span><i class="feather-activity me-1"></i>{{ __('messages.live_execution') ?? 'Live Stage Pipeline' }}</span>
                            </div>
                        </div>

                        <!-- 7 Execution Stage Steps -->
                        <div class="admin-update-steps mt-4" id="update-progress-steps">
                            @foreach($updateProgressStages as $stage)
                                <div class="admin-update-step is-{{ $stage['status'] ?? 'pending' }}" data-update-stage="{{ $stage['key'] }}">
                                    <span class="admin-update-step__icon">
                                        <i class="feather-{{ $stage['icon'] ?? 'circle' }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="admin-update-step__title">{{ $stage['label'] }}</span>
                                        <span class="admin-update-step__detail">{{ $stage['detail'] ?? '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Error Banner (Shown on Failure) -->
                        <div class="admin-status-banner is-danger mt-4" id="update-progress-error" style="display: none;">
                            <i class="feather-alert-octagon fs-4"></i>
                            <div>
                                <strong>{{ __('messages.update_session_failed') }}</strong>
                                <div class="mt-1" id="update-progress-error-text"></div>
                            </div>
                        </div>

                        <!-- Recovery Controls -->
                        <div class="d-flex flex-wrap gap-2 mt-4 pt-2 border-top">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" id="update-retry-btn" style="display: none;">
                                <i class="feather-rotate-cw me-1"></i>{{ __('messages.update_retry_stage') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm fw-bold" id="update-cancel-btn" style="display: none;">
                                <i class="feather-x-circle me-1"></i>{{ __('messages.update_cancel') }}
                            </button>
                        </div>
                    </div>
                </section>

                <!-- 2. Available Release Card -->
                @if($updateAvailable && $latestRelease)
                    <section class="admin-panel" id="update-card">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.available_updates') ?? 'Available Release' }}</span>
                                <h2 class="admin-panel__title d-flex align-items-center gap-2">
                                    {{ $latestRelease['name'] ?: $latestRelease['tag'] }}
                                    <span class="badge bg-primary fs-12">v{{ $latestVersion }}</span>
                                </h2>
                            </div>
                            @if($latestRelease['published_at'])
                                <span class="admin-chip">
                                    <i class="feather-calendar"></i>{{ \Carbon\Carbon::parse($latestRelease['published_at'])->format('M d, Y') }}
                                </span>
                            @endif
                        </div>

                        <div class="admin-panel__body">
                            <!-- Release Meta Pills -->
                            <div class="admin-metric-inline mb-4">
                                <span class="admin-metric-pill"><i class="feather-tag"></i>{{ $latestRelease['tag'] }}</span>
                                @if($latestRelease['download_size'])
                                    <span class="admin-metric-pill"><i class="feather-hard-drive"></i>{{ number_format($latestRelease['download_size'] / 1024 / 1024, 2) }} MB</span>
                                @endif
                                @if($latestRelease['html_url'])
                                    <a href="{{ $latestRelease['html_url'] }}" target="_blank" class="admin-metric-pill text-decoration-none">
                                        <i class="feather-external-link"></i>{{ __('messages.view_on_github') ?? 'View on GitHub' }}
                                    </a>
                                @endif
                            </div>

                            <!-- Release Notes (Markdown rendered) -->
                            @if($latestRelease['body'])
                                <div class="mb-4">
                                    <div class="admin-panel__eyebrow mb-2">
                                        <i class="feather-file-text me-1"></i>{{ __('messages.release_notes') }}
                                    </div>
                                    <div class="admin-release-notes-wrapper p-3 rounded border">
                                        <div class="admin-release-notes markdown-content" style="display: none;">{{ $latestRelease['body'] }}</div>
                                    </div>
                                </div>
                            @endif

                            <!-- Safety & Backup Note -->
                            <div class="admin-status-banner is-warning mb-3">
                                <i class="feather-alert-triangle fs-4"></i>
                                <div>
                                    <strong>{{ __('messages.important') ?? 'Important' }}</strong>
                                    <div>{{ __('messages.backup_warning') ?? 'Please ensure you have verified your database and files backup before running this release upgrade.' }}</div>
                                </div>
                            </div>

                            <div class="admin-surface-soft mb-4 p-3 rounded">
                                <div class="fw-bold text-dark d-flex align-items-center">
                                    <i class="feather-tool text-primary me-2"></i>{{ __('messages.maintenance_update_title') }}
                                </div>
                                <div class="admin-muted small mt-1">{{ __('messages.maintenance_update_auto_activate') }}</div>
                            </div>

                            <!-- Action Trigger -->
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <button type="button" class="btn btn-primary px-4 py-2 fw-bold js-update-trigger shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal" @disabled(!$preflightReport->isSafe())>
                                    <i class="feather-download me-2"></i>{{ __('messages.update_now') }}
                                </button>
                                @if($latestRelease['html_url'])
                                    <a href="{{ $latestRelease['html_url'] }}" target="_blank" class="btn btn-outline-secondary px-4 py-2">
                                        <i class="feather-github me-2"></i>{{ __('messages.release_page') ?? 'Release Page' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </section>
                @else
                    <!-- 3. System Up To Date Card -->
                    <section class="admin-panel" id="no-update-card">
                        <div class="admin-panel__body text-center py-5">
                            <div class="admin-modal-icon is-primary mx-auto mb-3" style="width: 64px; height: 64px; font-size: 28px;">
                                <i class="feather-check-circle text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-2">{{ __('messages.all_good') ?? 'Everything looks good!' }}</h3>
                            <p class="admin-muted mx-auto mb-4" style="max-width: 520px;">
                                {{ __('messages.no_updates_desc') ?? 'Your MyAds platform is running on the latest official build. Security checks and database integrity are verified.' }}
                            </p>
                            <div class="d-inline-flex gap-2">
                                <a href="https://github.com/mrghozzi/myads/releases" target="_blank" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="feather-github me-1"></i>{{ __('messages.view_all_releases') ?? 'View All Releases' }}
                                </a>
                                <a href="{{ route('admin.about') }}" class="btn btn-light btn-sm px-3 border">
                                    <i class="feather-info me-1"></i>{{ __('messages.system_info') }}
                                </a>
                            </div>
                        </div>
                    </section>
                @endif

                <!-- 4. Quick Link: What's New & System Stats -->
                <section class="admin-panel">
                    <div class="admin-panel__body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-soft-primary text-primary rounded p-3">
                                    <i class="feather-award fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ __('messages.about_myads') ?? 'About MYADS & Platform Statistics' }}</h6>
                                    <p class="admin-muted small mb-0">{{ __('messages.view_system_overview_stats') ?? 'Explore server metrics, platform volume, and system architecture.' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.about') }}" class="btn btn-outline-primary btn-sm">
                                <i class="feather-external-link me-1"></i>{{ __('messages.view_details') ?? 'View Details' }}
                            </a>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        <!-- Sidebar Column (Preflight & Environment) -->
        <div class="col-xl-4 col-lg-5">
            <div class="d-flex flex-column gap-4">

                <!-- Preflight Safety Checklist Card -->
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.update_preflight_title') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.update_preflight_title') }}</h2>
                        </div>
                        <span class="badge {{ $preflightReport->isSafe() ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-2 py-1">
                            {{ $preflightReport->isSafe() ? __('messages.update_preflight_passed') : __('messages.update_preflight_failed') }}
                        </span>
                    </div>

                    <div class="admin-panel__body">
                        <p class="admin-panel__copy small mb-3">{{ __('messages.update_preflight_description') }}</p>

                        <div class="admin-check-list">
                            @foreach($preflightReport->checks as $check)
                                <div class="admin-check-item">
                                    <span class="admin-check-item__icon {{ $check['status'] === 'passed' ? 'is-passed' : 'is-failed' }}">
                                        <i class="feather-{{ $check['status'] === 'passed' ? 'check' : 'alert-circle' }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="admin-check-item__title">{{ $check['title'] }}</span>
                                        <span class="admin-check-item__detail">{{ $check['detail'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!$preflightReport->isSafe())
                            <div class="admin-status-banner is-danger mt-3">
                                <i class="feather-alert-octagon"></i>
                                <div>
                                    <strong>{{ __('messages.update_preflight_failed') }}</strong>
                                    <div class="mt-1 small">{{ __('messages.update_blocked_preflight', ['details' => implode(' ', $preflightReport->failureMessages())]) }}</div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 p-2 rounded bg-soft-success text-success small d-flex align-items-center">
                                <i class="feather-shield me-2 fs-5"></i>
                                <span>{{ __('messages.update_preflight_passed') }} — {{ __('messages.safe_to_update') ?? 'Ready for safe upgrade' }}</span>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Maintenance Policy Card -->
                <section class="admin-note-card">
                    <span class="admin-note-label">{{ __('messages.maintenance_update_title') }}</span>
                    <span class="admin-note-copy">{{ $maintenanceEnabled ? __('messages.maintenance_update_active_notice') : __('messages.maintenance_update_inactive_notice') }}</span>
                    <div class="admin-chip-list mt-3">
                        <span class="admin-chip">
                            <i class="feather-tool"></i>
                            {{ $maintenanceEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}
                        </span>
                    </div>
                </section>

                <!-- Environment & System Specs Card -->
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.system_info') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.system_info') }}</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body p-0">
                        <table class="table admin-kv-table mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">{{ __('messages.script_name') ?? 'Script' }}</td>
                                    <td class="text-end fw-bold pe-3">MyAds Core</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">{{ __('messages.current_version') ?? 'Version' }}</td>
                                    <td class="text-end fw-bold pe-3">v{{ $currentVersion }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">PHP</td>
                                    <td class="text-end fw-bold pe-3">{{ phpversion() }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">Laravel</td>
                                    <td class="text-end fw-bold pe-3">{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">Database</td>
                                    <td class="text-end fw-bold pe-3">{{ $mysqlVersion }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted ps-3">{{ __('messages.github_repo') ?? 'Repository' }}</td>
                                    <td class="text-end pe-3">
                                        <a href="https://github.com/mrghozzi/myads" target="_blank" class="text-primary fw-semibold fs-12">
                                            <i class="feather-github me-1"></i>mrghozzi/myads
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
@if($updateAvailable && $latestRelease)
    <!-- Upgrade Confirmation Modal -->
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius);">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="confirmUpdateModalLabel">
                        <i class="feather-alert-triangle text-warning me-2 fs-4"></i>{{ __('messages.confirm_update') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <!-- Version Transition Badge -->
                    <div class="p-3 mb-3 rounded d-flex align-items-center justify-content-center gap-3 bg-light">
                        <span class="badge bg-secondary fs-13 py-2 px-3">v{{ $currentVersion }}</span>
                        <i class="feather-arrow-right fs-4 text-primary"></i>
                        <span class="badge bg-primary fs-13 py-2 px-3">v{{ $latestVersion }}</span>
                    </div>

                    <p class="text-muted small mb-3">
                        {{ __('messages.confirm_update_desc') }}
                        <strong>v{{ $currentVersion }}</strong>
                        {{ __('messages.to') ?? 'to' }}
                        <strong>v{{ $latestVersion }}</strong>.
                    </p>

                    <div class="admin-status-banner is-danger mb-3 p-3 rounded">
                        <i class="feather-alert-circle fs-4"></i>
                        <div>
                            <strong>{{ __('messages.before_updating') ?? 'Before updating' }}</strong>
                            <ul class="mb-0 mt-2 ps-3 small">
                                <li>{{ __('messages.backup_database') ?? 'Backup your database' }}</li>
                                <li>{{ __('messages.backup_files') ?? 'Backup your files (especially modified theme files)' }}</li>
                                <li>{{ __('messages.ensure_no_users') ?? 'Maintenance mode will automatically engage during update.' }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Explicit Checkboxes -->
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="backup_ack_database" name="backup_ack_database" form="update-form" {{ old('backup_ack_database') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="backup_ack_database">
                            {{ __('messages.backup_ack_database') ?? 'I have created a backup of the database.' }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="backup_ack_files" name="backup_ack_files" form="update-form" {{ old('backup_ack_files') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="backup_ack_files">
                            {{ __('messages.backup_ack_files') ?? 'I have created a backup of the files.' }}
                        </label>
                    </div>
                </div>

                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <form action="{{ route('admin.updates.process') }}" method="POST" id="update-form">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-bold px-4" id="btn-update" @disabled(!$preflightReport->isSafe())>
                            <i class="feather-download me-1"></i>{{ __('messages.yes_update') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script>
    const updateConfig = {
        initialSession: @json($activeUpdateSession),
        startUrl: @json(route('admin.updates.process')),
        routes: {
            step: @json(route('admin.updates.step', '__TOKEN__')),
            status: @json(route('admin.updates.status', '__TOKEN__')),
            cancel: @json(route('admin.updates.cancel', '__TOKEN__'))
        },
        labels: {
            checking: @json(__('messages.checking') ?? 'Checking...'),
            updating: @json(__('messages.updating') ?? 'Updating...'),
            completed: @json(__('messages.update_session_completed')),
            failed: @json(__('messages.update_session_failed')),
            cancelled: @json(__('messages.update_session_cancelled')),
            connectionError: @json(__('messages.connection_error') ?? 'Connection error. Please try again.'),
            downloadTemplate: @json(__('messages.update_stage_download_detail', ['downloaded' => ':downloaded', 'total' => ':total']))
        }
    };

    let currentUpdateSession = updateConfig.initialSession || null;
    let currentUpdateRoutes = currentUpdateSession ? routesForToken(currentUpdateSession.token) : null;
    let updateLoopRunning = false;
    let updateRecoveryTimer = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Markdown Rendering with DOMPurify sanitization
        function renderMarkdown() {
            document.querySelectorAll('.markdown-content').forEach(el => {
                if (!el.getAttribute('data-rendered')) {
                    const rawContent = el.innerText || el.innerHTML;
                    el.innerHTML = DOMPurify.sanitize(marked.parse(rawContent));
                    el.setAttribute('data-rendered', 'true');
                    el.style.display = 'block';
                }
            });
        }
        renderMarkdown();

        const updateForm = document.getElementById('update-form');
        if (updateForm) {
            updateForm.addEventListener('submit', startUpdateSession);
        }

        const retryBtn = document.getElementById('update-retry-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', function () {
                runUpdateLoop();
            });
        }

        const cancelBtn = document.getElementById('update-cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', cancelUpdateSession);
        }

        if (currentUpdateSession) {
            renderUpdateSession(currentUpdateSession);
            scheduleUpdateRecovery(700);
        }

        window.addEventListener('online', function () {
            scheduleUpdateRecovery(500);
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                scheduleUpdateRecovery(500);
            }
        });
    });

    function checkForUpdates() {
        const btn = document.getElementById('btn-check-update');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> ' + updateConfig.labels.checking;

        fetch('{{ route("admin.updates.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;

            if (!data.success) {
                showFlash('danger', '<i class="feather-wifi-off me-2"></i>' + (data.message || '{{ __("messages.update_check_failed") ?? "Could not connect to GitHub." }}'));
                return;
            }

            if (data.updateAvailable) {
                showFlash('warning', '<i class="feather-arrow-up-circle me-2"></i>{{ __("messages.new_update_found") ?? "A new update" }} <strong>v' + data.latestVersion + '</strong> {{ __("messages.is_available") ?? "is available!" }}');
                setTimeout(() => location.reload(), 1500);
            } else {
                showFlash('success', '<i class="feather-check-circle me-2"></i>{{ __("messages.system_up_to_date") ?? "Your system is up to date!" }} (v' + data.currentVersion + ')');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + updateConfig.labels.connectionError);
        });
    }

    function routesForToken(token) {
        const encoded = encodeURIComponent(token || '');

        return {
            step: updateConfig.routes.step.replace('__TOKEN__', encoded),
            status: updateConfig.routes.status.replace('__TOKEN__', encoded),
            cancel: updateConfig.routes.cancel.replace('__TOKEN__', encoded)
        };
    }

    function csrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    async function startUpdateSession(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const btn = document.getElementById('btn-update');
        const originalHtml = btn ? btn.innerHTML : '';

        setUpdateControlsLocked(true);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> ' + updateConfig.labels.updating;
        }

        try {
            const response = await fetch(form.action || updateConfig.startUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: new FormData(form)
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || validationMessage(data) || updateConfig.labels.connectionError);
            }

            currentUpdateSession = data.session;
            currentUpdateRoutes = data.routes || routesForToken(currentUpdateSession.token);
            renderUpdateSession(currentUpdateSession);

            const modalElement = document.getElementById('confirmUpdateModal');
            if (modalElement && window.bootstrap) {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();
            }

            setTimeout(runUpdateLoop, 600);
        } catch (error) {
            setUpdateControlsLocked(false);
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(error.message || updateConfig.labels.connectionError));
        } finally {
            if (btn) {
                btn.innerHTML = originalHtml || '<i class="feather-download me-1"></i>{{ __("messages.yes_update") }}';
            }
        }
    }

    async function runUpdateLoop() {
        if (!currentUpdateSession || !currentUpdateRoutes || updateLoopRunning) {
            return;
        }

        if (['completed', 'cancelled'].includes(currentUpdateSession.status)) {
            return;
        }

        updateLoopRunning = true;
        setUpdateControlsLocked(true);

        try {
            const response = await fetch(currentUpdateRoutes.step, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || updateConfig.labels.connectionError);
            }

            currentUpdateSession = data.session;
            currentUpdateRoutes = data.routes || currentUpdateRoutes;
            renderUpdateSession(currentUpdateSession);

            if (currentUpdateSession.status === 'completed') {
                showFlash('success', '<i class="feather-check-circle me-2"></i>' + escapeHtml(data.message || updateConfig.labels.completed));
                setTimeout(() => location.reload(), 1600);
                return;
            }

            if (currentUpdateSession.status === 'failed') {
                setUpdateControlsLocked(false);
                showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(currentUpdateSession.error || updateConfig.labels.failed));
                return;
            }

            if (currentUpdateSession.status === 'cancelled') {
                setUpdateControlsLocked(false);
                showFlash('info', '<i class="feather-x-circle me-2"></i>' + escapeHtml(updateConfig.labels.cancelled));
                return;
            }

            setTimeout(runUpdateLoop, 700);
        } catch (error) {
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(error.message || updateConfig.labels.connectionError));
            scheduleUpdateRecovery(3000);
        } finally {
            updateLoopRunning = false;
        }
    }

    async function pollUpdateStatus() {
        if (!currentUpdateSession || !currentUpdateRoutes) {
            return;
        }

        try {
            const response = await fetch(currentUpdateRoutes.status, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || updateConfig.labels.connectionError);
            }

            currentUpdateSession = data.session;
            currentUpdateRoutes = data.routes || currentUpdateRoutes;
            renderUpdateSession(currentUpdateSession);

            if (currentUpdateSession.status === 'running' && !currentUpdateSession.is_stale && !currentUpdateSession.can_auto_resume) {
                setTimeout(pollUpdateStatus, 1500);
            } else if (currentUpdateSession.status === 'pending' || currentUpdateSession.can_auto_resume) {
                scheduleUpdateRecovery(700);
            }
        } catch (error) {
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(error.message || updateConfig.labels.connectionError));
            scheduleUpdateRecovery(5000);
        }
    }

    function scheduleUpdateRecovery(delay) {
        if (!currentUpdateSession || !currentUpdateRoutes) {
            return;
        }

        if (['completed', 'cancelled'].includes(currentUpdateSession.status)) {
            return;
        }

        if (updateRecoveryTimer) {
            clearTimeout(updateRecoveryTimer);
        }

        updateRecoveryTimer = setTimeout(function () {
            updateRecoveryTimer = null;
            resumeUpdateSession();
        }, delay);
    }

    function resumeUpdateSession() {
        if (!currentUpdateSession || !currentUpdateRoutes || updateLoopRunning) {
            return;
        }

        if (['completed', 'cancelled'].includes(currentUpdateSession.status)) {
            return;
        }

        if (currentUpdateSession.status === 'running' && !currentUpdateSession.is_stale && !currentUpdateSession.can_auto_resume) {
            pollUpdateStatus();
            return;
        }

        if (currentUpdateSession.status === 'failed' && !currentUpdateSession.can_auto_resume) {
            return;
        }

        if (currentUpdateSession.status === 'pending' || currentUpdateSession.can_auto_resume || currentUpdateSession.is_stale) {
            runUpdateLoop();
        }
    }

    async function cancelUpdateSession() {
        if (!currentUpdateSession || !currentUpdateRoutes) {
            return;
        }

        if (!currentUpdateSession.can_cancel) {
            showFlash('warning', '<i class="feather-alert-triangle me-2"></i>' + escapeHtml('{{ __("messages.update_session_cancel_forbidden") }}'));
            return;
        }

        const cancelBtn = document.getElementById('update-cancel-btn');
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>' + escapeHtml('{{ __("messages.update_cancel") }}');
        }

        try {
            const response = await fetch(currentUpdateRoutes.cancel, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || updateConfig.labels.connectionError);
            }

            currentUpdateSession = data.session;
            renderUpdateSession(currentUpdateSession);
            setUpdateControlsLocked(false);
            showFlash('info', '<i class="feather-x-circle me-2"></i>' + escapeHtml(data.message || updateConfig.labels.cancelled));
        } catch (error) {
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(error.message || updateConfig.labels.connectionError));
        } finally {
            if (cancelBtn) {
                cancelBtn.disabled = false;
                cancelBtn.innerHTML = '<i class="feather-x-circle me-1"></i>' + escapeHtml('{{ __("messages.update_cancel") }}');
            }
        }
    }

    function renderUpdateSession(session) {
        const panel = document.getElementById('update-progress-panel');
        if (!panel || !session) {
            return;
        }

        panel.style.display = '';

        const percent = Math.max(0, Math.min(100, parseInt(session.percent || 0, 10)));
        const stage = document.getElementById('update-progress-stage');
        const status = document.getElementById('update-progress-status');
        const detail = document.getElementById('update-progress-detail');
        const percentLabel = document.getElementById('update-progress-percent');
        const bar = document.getElementById('update-progress-bar');
        const progress = panel.querySelector('.admin-update-progress__bar');
        const bytes = document.getElementById('update-progress-bytes');
        const errorBox = document.getElementById('update-progress-error');
        const errorText = document.getElementById('update-progress-error-text');
        const retryBtn = document.getElementById('update-retry-btn');
        const cancelBtn = document.getElementById('update-cancel-btn');

        if (stage) stage.textContent = session.stage_label || updateConfig.labels.updating;
        if (status) {
            status.textContent = session.status || '';
            status.className = 'badge ' + statusClass(session.status);
        }
        if (detail) detail.textContent = session.detail || '';
        if (percentLabel) percentLabel.textContent = percent + '%';
        if (bar) bar.style.width = percent + '%';
        if (progress) progress.setAttribute('aria-valuenow', String(percent));

        if (bytes) {
            if (session.current_stage === 'download' && session.bytes_total) {
                bytes.textContent = updateConfig.labels.downloadTemplate
                    .replace(':downloaded', formatBytes(parseInt(session.bytes_done || 0, 10)))
                    .replace(':total', formatBytes(parseInt(session.bytes_total || 0, 10)));
            } else {
                bytes.textContent = '';
            }
        }

        if (Array.isArray(session.stages)) {
            session.stages.forEach(function (item) {
                const node = document.querySelector('[data-update-stage="' + item.key + '"]');
                if (!node) return;
                node.className = 'admin-update-step is-' + (item.status || 'pending') + (item.key === session.current_stage ? ' is-current' : '');
                const title = node.querySelector('.admin-update-step__title');
                const itemDetail = node.querySelector('.admin-update-step__detail');
                if (title) title.textContent = item.label || item.key;
                if (itemDetail) itemDetail.textContent = item.detail || '';
            });
        }

        if (errorBox && errorText) {
            if (session.status === 'failed') {
                errorBox.style.display = '';
                errorText.textContent = session.error || updateConfig.labels.failed;
            } else {
                errorBox.style.display = 'none';
                errorText.textContent = '';
            }
        }

        if (retryBtn) retryBtn.style.display = session.can_retry ? 'inline-flex' : 'none';
        if (cancelBtn) {
            const isTerminal = ['completed', 'cancelled'].includes(session.status);
            cancelBtn.style.display = isTerminal ? 'none' : 'inline-flex';
            cancelBtn.disabled = !session.can_cancel;
            cancelBtn.title = session.can_cancel ? '' : '{{ __("messages.update_session_cancel_forbidden") }}';
        }
    }

    function setUpdateControlsLocked(locked) {
        document.querySelectorAll('.js-update-trigger, #btn-check-update').forEach(function (button) {
            button.disabled = locked;
        });
    }

    function statusClass(status) {
        if (status === 'completed') return 'bg-soft-success text-success';
        if (status === 'failed') return 'bg-soft-danger text-danger';
        if (status === 'cancelled') return 'bg-soft-secondary text-secondary';
        if (status === 'running') return 'bg-soft-warning text-warning';

        return 'bg-soft-primary text-primary';
    }

    function formatBytes(bytes) {
        if (!bytes || bytes <= 0) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB'];
        let value = bytes;
        let index = 0;
        while (value >= 1024 && index < units.length - 1) {
            value = value / 1024;
            index++;
        }
        return value.toFixed(index === 0 ? 0 : 2) + ' ' + units[index];
    }

    function validationMessage(data) {
        if (!data || !data.errors) return null;
        const firstKey = Object.keys(data.errors)[0];
        return firstKey && data.errors[firstKey] ? data.errors[firstKey][0] : null;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function showFlash(type, message) {
        const container = document.querySelector('.admin-page');
        const existing = container.querySelector('.dynamic-alert');
        if (existing) {
            existing.remove();
        }

        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' d-flex align-items-center shadow-sm border-0 dynamic-alert';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = '<div>' + message + '</div>';

        const hero = container.querySelector('.admin-hero');
        if (hero) {
            hero.insertAdjacentElement('afterend', alertDiv);
        } else {
            container.prepend(alertDiv);
        }

        setTimeout(() => {
            alertDiv.style.transition = 'opacity 0.5s';
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 500);
        }, 5000);
    }
</script>
<style>
    /* Superdesign Refined Styles for /admin/updates */
    .admin-update-progress {
        background: var(--admin-premium-surface-alt, #f6f7fb);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 16px;
        padding: 1.25rem;
    }
    .admin-update-progress__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.95rem;
        color: var(--admin-premium-text, #1f2937);
    }
    .admin-update-progress__bar {
        height: 12px;
        margin-top: 0.75rem;
        background: rgba(120, 130, 160, 0.14);
        border-radius: 999px;
        overflow: hidden;
    }
    .admin-update-progress__bar .progress-bar {
        background: linear-gradient(90deg, #3454d1, #615dfa);
        transition: width 0.4s ease;
    }
    .admin-update-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.85rem;
    }
    .admin-update-step {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        min-height: 76px;
        padding: 0.95rem;
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 12px;
        background: var(--admin-premium-surface, #ffffff);
        transition: border-color 0.25s ease, background 0.25s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    .admin-update-step:hover {
        transform: translateY(-2px);
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15,23,42,.05));
    }
    .admin-update-step__icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 38px;
        background: var(--admin-premium-surface-alt, #f6f7fb);
        color: var(--admin-premium-muted, #6b7280);
        font-size: 1.15rem;
        transition: all 0.25s ease;
    }
    .admin-update-step__title {
        display: block;
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--admin-premium-text, #1f2937);
        line-height: 1.3;
    }
    .admin-update-step__detail {
        display: block;
        margin-top: 0.25rem;
        color: var(--admin-premium-muted, #6b7280);
        font-size: 0.76rem;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }
    .admin-update-step.is-current {
        border-color: rgba(97, 93, 250, 0.45);
        background: rgba(97, 93, 250, 0.04);
        box-shadow: 0 0 0 1px rgba(97, 93, 250, 0.2);
    }
    .admin-update-step.is-running .admin-update-step__icon,
    .admin-update-step.is-current .admin-update-step__icon {
        background: rgba(97, 93, 250, 0.15);
        color: var(--admin-premium-accent, #615dfa);
        animation: pulseIcon 1.8s infinite;
    }
    .admin-update-step.is-completed .admin-update-step__icon {
        background: rgba(23, 198, 102, 0.15);
        color: #17c666;
    }
    .admin-update-step.is-completed {
        border-color: rgba(23, 198, 102, 0.25);
    }
    .admin-update-step.is-failed {
        border-color: rgba(234, 77, 77, 0.35);
        background: rgba(234, 77, 77, 0.04);
    }
    .admin-update-step.is-failed .admin-update-step__icon {
        background: rgba(234, 77, 77, 0.15);
        color: #ea4d4d;
    }

    @keyframes pulseIcon {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    .admin-release-notes-wrapper {
        background: var(--admin-premium-surface-alt, #f6f7fb);
        max-height: 380px;
        overflow-y: auto;
    }
    .markdown-content h1, .markdown-content h2, .markdown-content h3 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
        color: var(--admin-premium-text, #1f2937);
    }
    .markdown-content h1 { font-size: 1.35rem; }
    .markdown-content h2 { font-size: 1.15rem; }
    .markdown-content h3 { font-size: 1.05rem; }
    .markdown-content p { margin-bottom: 0.85rem; line-height: 1.6; font-size: 0.9rem; }
    .markdown-content ul, .markdown-content ol { margin-bottom: 0.85rem; padding-inline-start: 1.5rem; }
    .markdown-content li { margin-bottom: 0.4rem; font-size: 0.88rem; }
    .markdown-content code {
        background: rgba(97, 93, 250, 0.1);
        color: var(--admin-premium-accent, #615dfa);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        font-size: 85%;
    }
    .markdown-content pre {
        background: var(--admin-premium-surface, #ffffff);
        padding: 0.85rem;
        border-radius: 8px;
        overflow-x: auto;
        margin-bottom: 1rem;
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
    }
    .markdown-content pre code { background: transparent; color: inherit; padding: 0; }
    .markdown-content blockquote {
        border-inline-start: 4px solid var(--admin-premium-accent, #615dfa);
        padding-inline-start: 1rem;
        font-style: italic;
        color: var(--admin-premium-muted, #6b7280);
    }
    .markdown-content hr {
        margin: 1.25rem 0;
        border-top: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        opacity: 1;
    }

    @media (max-width: 575.98px) {
        .admin-update-progress__meta { align-items: flex-start; flex-direction: column; gap: 0.35rem; }
        .admin-update-steps { grid-template-columns: 1fr; }
    }
</style>
@endpush
