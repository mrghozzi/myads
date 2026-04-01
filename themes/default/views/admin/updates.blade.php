@extends('theme::layouts.admin')

@section('title', __('messages.updates_myads'))

@section('content')
@php
    $maintenanceEnabled = !empty($maintenanceSettings['enabled']);
    $preflightChecks = collect($preflightReport->checks ?? []);
    $failedChecks = $preflightChecks->where('status', '!=', 'passed')->count();
    $passedChecks = $preflightChecks->where('status', 'passed')->count();
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
                                <div class="admin-release-notes">{!! nl2br(e($latestRelease['body'])) !!}</div>
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
                            <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal" @disabled(!$preflightReport->isSafe())>
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
                        <button type="submit" class="btn btn-primary fw-bold" id="btn-update" onclick="startUpdate(this)" @disabled(!$preflightReport->isSafe())>
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
<script>
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

    function startUpdate(btn) {
        const form = btn.closest('form');
        if (form) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("messages.updating") ?? "Updating..." }}';
            setTimeout(() => {
                btn.disabled = true;
            }, 10);
        }
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
@endpush
