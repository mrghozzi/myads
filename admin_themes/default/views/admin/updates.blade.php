@extends('admin::layouts.admin')

@section('title', __('messages.updates_myads'))

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
@endphp

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.admin_panel') ?? 'Admin' }}</a></li>
                <li>{{ __('messages.updates') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.updates_myads') ?? 'System Updates' }}</div>
            <h1 class="admin-hero__title" id="status-title">
                @if($updateAvailable)
                    {{ __('messages.new_version_available') ?? 'Update Available!' }}
                @else
                    {{ __('messages.system_up_to_date') ?? 'System is Up to Date' }}
                @endif
            </h1>
            <p class="admin-hero__copy" id="status-subtitle">
                @if($updateAvailable)
                    {{ __('messages.update_available_desc') ?? 'A new version is available for download.' }}
                @else
                    {{ __('messages.up_to_date_desc') ?? 'You are running the latest version of MyAds.' }}
                @endif
            </p>

            <div class="admin-stat-strip" id="version-display">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.installed') ?? 'Installed' }}</span>
                    <span class="admin-stat-value">v{{ $currentVersion }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.latest') ?? 'Latest' }}</span>
                    <span class="admin-stat-value">{{ $latestVersion ? 'v' . $latestVersion : '--' }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.update_preflight_title') }}</span>
                    <span class="admin-stat-value">{{ $passedChecks }}/{{ $preflightChecks->count() }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div class="admin-modal-icon {{ $updateAvailable ? 'is-primary' : 'is-primary' }} mb-0" id="status-icon">
                        <i class="feather-{{ $updateAvailable ? 'arrow-up-circle' : 'check-circle' }}"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.current_version') ?? 'Version' }}</span>
                        <div class="admin-panel__title mb-1">v{{ $currentVersion }}</div>
                        <div class="admin-muted">
                            @if($latestVersion)
                                v{{ $latestVersion }}
                            @else
                                {{ __('messages.check_for_updates') }}
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary w-100 mt-3" id="btn-check-update" onclick="checkForUpdates()">
                    <i class="feather-refresh-cw me-1"></i>{{ __('messages.check_for_updates') }}
                </button>
            </div>

            <div class="admin-chip-list">
                <span class="admin-chip">
                    <i class="feather-shield"></i>
                    {{ $preflightReport->isSafe() ? __('messages.update_preflight_passed') : __('messages.update_preflight_failed') }}
                </span>
                <span class="admin-chip">
                    <i class="feather-tool"></i>
                    {{ $maintenanceEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}
                </span>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
            <i class="feather-check-circle fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
            <i class="feather-alert-circle fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
            <i class="feather-info fs-4 me-2"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-0" role="alert">
            <div class="fw-bold mb-2">{{ __('messages.warning') ?? 'Warning' }}</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-workspace-grid">
        <div class="admin-section-stack">
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.update_preflight_title') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.update_preflight_title') }}</h2>
                    </div>
                    <span class="badge {{ $preflightReport->isSafe() ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                        {{ $preflightReport->isSafe() ? __('messages.update_preflight_passed') : __('messages.update_preflight_failed') }}
                    </span>
                </div>
                <div class="admin-panel__body">
                    <p class="admin-panel__copy">{{ __('messages.update_preflight_description') }}</p>

                    <div class="admin-check-list">
                        @foreach($preflightReport->checks as $check)
                            <div class="admin-check-item">
                                <span class="admin-check-item__icon {{ $check['status'] === 'passed' ? 'is-passed' : 'is-failed' }}">
                                    <i class="feather-{{ $check['status'] === 'passed' ? 'check' : 'alert-circle' }}"></i>
                                </span>
                                <div>
                                    <span class="admin-check-item__title">{{ $check['title'] }}</span>
                                    <span class="admin-check-item__detail">{{ $check['detail'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!$preflightReport->isSafe())
                        <div class="admin-status-banner is-danger mt-4">
                            <i class="feather-alert-octagon"></i>
                            <div>
                                <strong>{{ __('messages.update_preflight_failed') }}</strong>
                                <div class="mt-1">{{ __('messages.update_blocked_preflight', ['details' => implode(' ', $preflightReport->failureMessages())]) }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section class="admin-panel admin-update-progress-panel" id="update-progress-panel" @if(!$activeUpdateSession) style="display: none;" @endif>
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.update_progress_title') }}</span>
                        <h2 class="admin-panel__title" id="update-progress-stage">
                            {{ $activeUpdateSession['stage_label'] ?? __('messages.update_progress_idle') }}
                        </h2>
                    </div>
                    <span class="badge bg-soft-primary text-primary" id="update-progress-status">
                        {{ $activeUpdateSession['status'] ?? __('messages.pending') }}
                    </span>
                </div>
                <div class="admin-panel__body">
                    <div class="admin-update-progress">
                        <div class="admin-update-progress__meta">
                            <span id="update-progress-detail">{{ $activeUpdateSession['detail'] ?? __('messages.update_progress_description') }}</span>
                            <strong id="update-progress-percent">{{ (int) ($activeUpdateSession['percent'] ?? 0) }}%</strong>
                        </div>
                        <div class="progress admin-update-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) ($activeUpdateSession['percent'] ?? 0) }}">
                            <div class="progress-bar" id="update-progress-bar" style="width: {{ (int) ($activeUpdateSession['percent'] ?? 0) }}%;"></div>
                        </div>
                        <div class="admin-muted mt-2" id="update-progress-bytes">
                            @if(!empty($activeUpdateSession['bytes_total']))
                                {{ __('messages.update_stage_download_detail', [
                                    'downloaded' => $activeUpdateSession['bytes_done'] ?? 0,
                                    'total' => $activeUpdateSession['bytes_total'],
                                ]) }}
                            @endif
                        </div>
                    </div>

                    <div class="admin-update-steps mt-4" id="update-progress-steps">
                        @foreach($updateProgressStages as $stage)
                            <div class="admin-update-step is-{{ $stage['status'] ?? 'pending' }}" data-update-stage="{{ $stage['key'] }}">
                                <span class="admin-update-step__icon">
                                    <i class="feather-{{ $stage['icon'] ?? 'circle' }}"></i>
                                </span>
                                <div>
                                    <span class="admin-update-step__title">{{ $stage['label'] }}</span>
                                    <span class="admin-update-step__detail">{{ $stage['detail'] ?? '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="admin-status-banner is-danger mt-4" id="update-progress-error" style="display: none;">
                        <i class="feather-alert-circle"></i>
                        <div>
                            <strong>{{ __('messages.update_session_failed') }}</strong>
                            <div class="mt-1" id="update-progress-error-text"></div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="update-retry-btn" style="display: none;">
                            <i class="feather-rotate-cw me-1"></i>{{ __('messages.update_retry_stage') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="update-cancel-btn" style="display: none;">
                            <i class="feather-x-circle me-1"></i>{{ __('messages.update_cancel') }}
                        </button>
                    </div>
                </div>
            </section>

            @if($updateAvailable && $latestRelease)
                <section class="admin-panel" id="update-card">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.available_updates') ?? __('messages.updates') }}</span>
                            <h2 class="admin-panel__title">{{ $latestRelease['name'] ?: $latestRelease['tag'] }}</h2>
                        </div>
                        @if($latestRelease['published_at'])
                            <span class="admin-chip"><i class="feather-calendar"></i>{{ \Carbon\Carbon::parse($latestRelease['published_at'])->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <div class="admin-panel__body">
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

                        @if($latestRelease['body'])
                            <div class="mb-4">
                                <div class="admin-panel__eyebrow">{{ __('messages.release_notes') }}</div>
                                <div class="admin-release-notes markdown-content" style="display: none;">{{ $latestRelease['body'] }}</div>
                            </div>
                        @endif

                        <div class="admin-status-banner is-warning mb-3">
                            <i class="feather-alert-triangle"></i>
                            <div>
                                <strong>{{ __('messages.important') ?? 'Important' }}</strong>
                                <div>{{ __('messages.backup_warning') ?? 'Please create a full backup of your database and files before proceeding with the update. This action cannot be undone.' }}</div>
                            </div>
                        </div>

                        <div class="admin-surface-soft mb-4">
                            <div class="admin-panel__eyebrow">{{ __('messages.maintenance_update_title') }}</div>
                            <div class="admin-muted">{{ __('messages.maintenance_update_auto_activate') }}</div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="button" class="btn btn-primary px-4 js-update-trigger" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal" @disabled(!$preflightReport->isSafe())>
                                <i class="feather-download me-2"></i>{{ __('messages.update_now') }}
                            </button>
                            @if($latestRelease['html_url'])
                                <a href="{{ $latestRelease['html_url'] }}" target="_blank" class="btn btn-outline-secondary px-4">
                                    <i class="feather-github me-2"></i>{{ __('messages.release_page') ?? 'Release Page' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @else
                <section class="admin-panel" id="no-update-card">
                    <div class="admin-panel__body">
                        <div class="admin-empty-state py-4">
                            <div class="admin-modal-icon is-primary">
                                <i class="feather-shield"></i>
                            </div>
                            <h4>{{ __('messages.all_good') ?? 'Everything looks good!' }}</h4>
                            <p class="admin-muted mb-0">{{ __('messages.no_updates_desc') }}</p>
                            <a href="https://github.com/mrghozzi/myads/releases" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="feather-github me-1"></i>{{ __('messages.view_all_releases') ?? 'View All Releases' }}
                            </a>
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <aside class="admin-section-stack">
            <section class="admin-note-card {{ $maintenanceEnabled ? '' : '' }}">
                <span class="admin-note-label">{{ __('messages.maintenance_update_title') }}</span>
                <span class="admin-note-copy">{{ $maintenanceEnabled ? __('messages.maintenance_update_active_notice') : __('messages.maintenance_update_inactive_notice') }}</span>
                <div class="admin-chip-list mt-3">
                    <span class="admin-chip"><i class="feather-tool"></i>{{ $maintenanceEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}</span>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.system_info') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.system_info') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <table class="table admin-kv-table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium text-muted">{{ __('messages.script_name') ?? 'Script' }}</td>
                                <td class="text-end fw-bold">MyAds</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">{{ __('messages.current_version') ?? 'Version' }}</td>
                                <td class="text-end fw-bold">v{{ $currentVersion }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">PHP</td>
                                <td class="text-end fw-bold">{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Laravel</td>
                                <td class="text-end fw-bold">{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">{{ __('messages.github_repo') ?? 'Repository' }}</td>
                                <td class="text-end">
                                    <a href="https://github.com/mrghozzi/myads" target="_blank" class="text-primary fs-12">
                                        <i class="feather-github me-1"></i>mrghozzi/myads
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@section('modals')
@if($updateAvailable && $latestRelease)
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="confirmUpdateModalLabel">
                        <i class="feather-alert-triangle text-warning me-2"></i>{{ __('messages.confirm_update') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        {{ __('messages.confirm_update_desc') }}
                        <strong>v{{ $currentVersion }}</strong>
                        {{ __('messages.to') ?? 'to' }}
                        <strong>v{{ $latestVersion }}</strong>.
                    </p>

                    <div class="admin-status-banner is-danger mb-3">
                        <i class="feather-alert-circle"></i>
                        <div>
                            <strong>{{ __('messages.before_updating') ?? 'Before updating' }}</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                <li>{{ __('messages.backup_database') ?? 'Backup your database' }}</li>
                                <li>{{ __('messages.backup_files') ?? 'Backup your files (especially modified theme files)' }}</li>
                                <li>{{ __('messages.ensure_no_users') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="backup_ack_database" name="backup_ack_database" form="update-form" {{ old('backup_ack_database') ? 'checked' : '' }}>
                        <label class="form-check-label" for="backup_ack_database">
                            {{ __('messages.backup_ack_database') ?? 'I have created a backup of the database.' }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="backup_ack_files" name="backup_ack_files" form="update-form" {{ old('backup_ack_files') ? 'checked' : '' }}>
                        <label class="form-check-label" for="backup_ack_files">
                            {{ __('messages.backup_ack_files') ?? 'I have created a backup of the files.' }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                    <form action="{{ route('admin.updates.process') }}" method="POST" id="update-form">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-bold" id="btn-update" @disabled(!$preflightReport->isSafe())>
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

    document.addEventListener('DOMContentLoaded', function () {
        // Markdown Rendering
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
            if (currentUpdateSession.status === 'pending') {
                setTimeout(runUpdateLoop, 700);
            } else if (currentUpdateSession.status === 'running') {
                setTimeout(pollUpdateStatus, 1200);
            }
        }
    });

    function checkForUpdates() {
        const btn = document.getElementById('btn-check-update');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("messages.checking") ?? "Checking..." }}';

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
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>{{ __("messages.connection_error") ?? "Connection error. Please try again." }}');
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

            if (currentUpdateSession.status === 'running') {
                setTimeout(pollUpdateStatus, 1500);
            } else if (currentUpdateSession.status === 'pending') {
                setTimeout(runUpdateLoop, 700);
            }
        } catch (error) {
            showFlash('danger', '<i class="feather-alert-circle me-2"></i>' + escapeHtml(error.message || updateConfig.labels.connectionError));
        }
    }

    async function cancelUpdateSession() {
        if (!currentUpdateSession || !currentUpdateRoutes || !currentUpdateSession.can_cancel) {
            return;
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
        if (cancelBtn) cancelBtn.style.display = session.can_cancel ? 'inline-flex' : 'none';
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
        alertDiv.className = 'alert alert-' + type + ' d-flex align-items-center dynamic-alert';
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
    .markdown-content h1, .markdown-content h2, .markdown-content h3 { margin-top: 1.25rem; margin-bottom: 0.75rem; font-weight: 700; color: var(--vz-heading-color); }
    .markdown-content h1 { font-size: 1.5rem; }
    .markdown-content h2 { font-size: 1.25rem; }
    .markdown-content h3 { font-size: 1.1rem; }
    .markdown-content p { margin-bottom: 1rem; line-height: 1.6; }
    .markdown-content ul, .markdown-content ol { margin-bottom: 1rem; padding-left: 1.5rem; }
    .markdown-content li { margin-bottom: 0.5rem; }
    .markdown-content code { background: rgba(var(--vz-primary-rgb), 0.1); color: var(--vz-primary); padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 85%; }
    .markdown-content pre { background: var(--vz-light); padding: 1rem; border-radius: 6px; overflow-x: auto; margin-bottom: 1rem; border: 1px solid var(--vz-border-color); }
    .markdown-content pre code { background: transparent; color: inherit; padding: 0; }
    .markdown-content blockquote { border-left: 4px solid var(--vz-primary); padding-left: 1rem; margin-left: 0; font-style: italic; color: var(--vz-muted); }
    .markdown-content img { max-width: 100%; height: auto; border-radius: 6px; }
    .markdown-content hr { margin: 1.5rem 0; border-top: 1px solid var(--vz-border-color); opacity: 1; }
    .admin-update-progress { background: rgba(var(--vz-primary-rgb), 0.06); border: 1px solid rgba(var(--vz-primary-rgb), 0.14); border-radius: 8px; padding: 1rem; }
    .admin-update-progress__meta { display: flex; align-items: center; justify-content: space-between; gap: 1rem; font-size: 0.95rem; color: var(--vz-heading-color); }
    .admin-update-progress__bar { height: 10px; margin-top: 0.75rem; background: rgba(120, 130, 160, 0.18); border-radius: 999px; overflow: hidden; }
    .admin-update-progress__bar .progress-bar { background: linear-gradient(90deg, #0ea5e9, #2563eb); }
    .admin-update-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.75rem; }
    .admin-update-step { display: flex; align-items: flex-start; gap: 0.75rem; min-height: 72px; padding: 0.85rem; border: 1px solid var(--vz-border-color); border-radius: 8px; background: var(--vz-card-bg); transition: border-color 0.2s ease, background 0.2s ease; }
    .admin-update-step__icon { width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 34px; background: var(--vz-light); color: var(--vz-muted); }
    .admin-update-step__title { display: block; font-weight: 700; color: var(--vz-heading-color); line-height: 1.25; }
    .admin-update-step__detail { display: block; margin-top: 0.25rem; color: var(--vz-muted); font-size: 0.78rem; line-height: 1.35; overflow-wrap: anywhere; }
    .admin-update-step.is-current { border-color: rgba(var(--vz-primary-rgb), 0.45); background: rgba(var(--vz-primary-rgb), 0.05); }
    .admin-update-step.is-running .admin-update-step__icon,
    .admin-update-step.is-current .admin-update-step__icon { background: rgba(var(--vz-primary-rgb), 0.14); color: var(--vz-primary); }
    .admin-update-step.is-completed .admin-update-step__icon { background: rgba(25, 135, 84, 0.14); color: #198754; }
    .admin-update-step.is-failed { border-color: rgba(220, 53, 69, 0.35); background: rgba(220, 53, 69, 0.05); }
    .admin-update-step.is-failed .admin-update-step__icon { background: rgba(220, 53, 69, 0.14); color: #dc3545; }
    @media (max-width: 575.98px) {
        .admin-update-progress__meta { align-items: flex-start; flex-direction: column; gap: 0.35rem; }
        .admin-update-steps { grid-template-columns: 1fr; }
    }
</style>
@endpush
