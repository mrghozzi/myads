@extends('theme::layouts.admin')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ __('messages.updates_myads') ?? 'System Updates' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.admin_panel') ?? 'Admin' }}</a></li>
                <li class="breadcrumb-item">{{ __('messages.updates') ?? 'Updates' }}</li>
            </ul>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="feather-check-circle fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="feather-alert-circle fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="feather-info fs-4 me-2"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    <div class="row">
        {{-- Version Status Card --}}
        <div class="col-xxl-4 col-lg-5 col-12">
            <div class="card stretch stretch-full mb-4">
                <div class="card-body d-flex flex-column align-items-center text-center py-5">
                    <div class="mb-4" id="status-icon">
                        @if($updateAvailable)
                            <div class="wd-80 ht-80 rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(255, 171, 0, 0.12);">
                                <i class="feather-arrow-up-circle" style="font-size: 40px; color: #ffab00;"></i>
                            </div>
                        @else
                            <div class="wd-80 ht-80 rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(0, 200, 83, 0.12);">
                                <i class="feather-check-circle" style="font-size: 40px; color: #00c853;"></i>
                            </div>
                        @endif
                    </div>

                    <h4 class="fw-bold mb-2" id="status-title">
                        @if($updateAvailable)
                            {{ __('messages.new_version_available') ?? 'Update Available!' }}
                        @else
                            {{ __('messages.system_up_to_date') ?? 'System is Up to Date' }}
                        @endif
                    </h4>

                    <p class="text-muted fs-13 mb-4" id="status-subtitle">
                        @if($updateAvailable)
                            {{ __('messages.update_available_desc') ?? 'A new version is available for download.' }}
                        @else
                            {{ __('messages.up_to_date_desc') ?? 'You are running the latest version of MyAds.' }}
                        @endif
                    </p>

                    {{-- Version Comparison --}}
                    <div class="d-flex align-items-center gap-3 mb-4" id="version-display">
                        <div class="text-center">
                            <span class="badge bg-soft-primary text-primary px-3 py-2 fs-13 fw-bold">v{{ $currentVersion }}</span>
                            <small class="d-block text-muted mt-1 fs-11">{{ __('messages.installed') ?? 'Installed' }}</small>
                        </div>
                        @if($latestVersion)
                            <i class="feather-arrow-right text-muted"></i>
                            <div class="text-center">
                                <span class="badge {{ $updateAvailable ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }} px-3 py-2 fs-13 fw-bold">v{{ $latestVersion }}</span>
                                <small class="d-block text-muted mt-1 fs-11">{{ __('messages.latest') ?? 'Latest' }}</small>
                            </div>
                        @endif
                    </div>

                    {{-- Check for Updates Button --}}
                    <button type="button" class="btn btn-outline-primary btn-sm px-4" id="btn-check-update" onclick="checkForUpdates()">
                        <i class="feather-refresh-cw me-1"></i>
                        {{ __('messages.check_for_updates') ?? 'Check for Updates' }}
                    </button>
                </div>
            </div>

            {{-- System Info Card --}}
            <div class="card stretch stretch-full mb-4">
                <div class="card-header">
                    <h5 class="card-title fs-14">{{ __('messages.system_info') ?? 'System Information' }}</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium text-muted ps-3">{{ __('messages.script_name') ?? 'Script' }}</td>
                                <td class="text-end pe-3 fw-bold">MyAds</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted ps-3">{{ __('messages.current_version') ?? 'Version' }}</td>
                                <td class="text-end pe-3 fw-bold">v{{ $currentVersion }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted ps-3">PHP</td>
                                <td class="text-end pe-3 fw-bold">{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted ps-3">Laravel</td>
                                <td class="text-end pe-3 fw-bold">{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted ps-3">{{ __('messages.github_repo') ?? 'Repository' }}</td>
                                <td class="text-end pe-3">
                                    <a href="https://github.com/mrghozzi/myads" target="_blank" class="text-primary fs-12">
                                        <i class="feather-github me-1"></i>mrghozzi/myads
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Release Details --}}
        <div class="col-xxl-8 col-lg-7 col-12">
            @if($updateAvailable && $latestRelease)
                {{-- Update Available Card --}}
                <div class="card stretch stretch-full mb-4" id="update-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title fs-14">
                            <i class="feather-package me-2 text-warning"></i>
                            {{ $latestRelease['name'] ?: $latestRelease['tag'] }}
                        </h5>
                        @if($latestRelease['published_at'])
                            <span class="badge bg-soft-secondary text-secondary fs-11">
                                <i class="feather-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($latestRelease['published_at'])->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- Release Meta --}}
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <span class="badge bg-soft-warning text-warning px-3 py-2">
                                <i class="feather-tag me-1"></i>{{ $latestRelease['tag'] }}
                            </span>
                            @if($latestRelease['download_size'])
                                <span class="badge bg-soft-info text-info px-3 py-2">
                                    <i class="feather-hard-drive me-1"></i>{{ number_format($latestRelease['download_size'] / 1024 / 1024, 2) }} MB
                                </span>
                            @endif
                            @if($latestRelease['html_url'])
                                <a href="{{ $latestRelease['html_url'] }}" target="_blank" class="badge bg-soft-primary text-primary px-3 py-2 text-decoration-none">
                                    <i class="feather-external-link me-1"></i>{{ __('messages.view_on_github') ?? 'View on GitHub' }}
                                </a>
                            @endif
                        </div>

                        {{-- Changelog / Release Notes --}}
                        @if($latestRelease['body'])
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="feather-file-text me-1"></i>
                                    {{ __('messages.release_notes') ?? 'Release Notes' }}
                                </h6>
                                <div class="changelog-content p-3 rounded-3" style="background: var(--bs-body-bg); border: 1px solid var(--bs-border-color);">
                                    {!! nl2br(e($latestRelease['body'])) !!}
                                </div>
                            </div>
                        @endif

                        {{-- Backup Warning --}}
                        <div class="alert alert-soft-warning d-flex align-items-start gap-3 mb-4">
                            <i class="feather-alert-triangle fs-4 mt-1"></i>
                            <div>
                                <strong>{{ __('messages.important') ?? 'Important' }}:</strong>
                                {{ __('messages.backup_warning') ?? 'Please create a full backup of your database and files before proceeding with the update. This action cannot be undone.' }}
                            </div>
                        </div>

                        {{-- Update Button --}}
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal">
                                <i class="feather-download me-2"></i>
                                {{ __('messages.update_now') ?? 'Update Now' }}
                            </button>
                            @if($latestRelease['html_url'])
                                <a href="{{ $latestRelease['html_url'] }}" target="_blank" class="btn btn-outline-secondary px-4 py-2">
                                    <i class="feather-github me-2"></i>
                                    {{ __('messages.release_page') ?? 'Release Page' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- No Updates Card --}}
                <div class="card stretch stretch-full mb-4" id="no-update-card">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                        <div class="wd-100 ht-100 rounded-circle d-flex align-items-center justify-content-center mb-4" style="background: rgba(0, 200, 83, 0.08);">
                            <i class="feather-shield" style="font-size: 48px; color: #00c853;"></i>
                        </div>
                        <h4 class="fw-bold mb-2">{{ __('messages.all_good') ?? 'Everything looks good!' }}</h4>
                        <p class="text-muted fs-13 mb-3" style="max-width: 400px;">
                            {{ __('messages.no_updates_desc') ?? 'Your MyAds installation is running the latest version. We will check for updates automatically, or you can check manually anytime.' }}
                        </p>
                        <a href="https://github.com/mrghozzi/myads/releases" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="feather-github me-1"></i>
                            {{ __('messages.view_all_releases') ?? 'View All Releases' }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @section('modals')
    {{-- Confirm Update Modal --}}
    @if($updateAvailable && $latestRelease)
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="confirmUpdateModalLabel">
                        <i class="feather-alert-triangle text-warning me-2"></i>
                        {{ __('messages.confirm_update') ?? 'Confirm Update' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-3">
                        {{ __('messages.confirm_update_desc') ?? 'You are about to update your system from' }}
                        <strong>v{{ $currentVersion }}</strong>
                        {{ __('messages.to') ?? 'to' }}
                        <strong>v{{ $latestVersion }}</strong>.
                    </p>
                    <div class="alert alert-soft-danger mb-0">
                        <div class="d-flex align-items-start gap-2">
                            <i class="feather-alert-circle mt-1"></i>
                            <div class="fs-12">
                                <strong>{{ __('messages.before_updating') ?? 'Before updating' }}:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>{{ __('messages.backup_database') ?? 'Backup your database' }}</li>
                                    <li>{{ __('messages.backup_files') ?? 'Backup your files (especially modified theme files)' }}</li>
                                    <li>{{ __('messages.ensure_no_users') ?? 'Ensure no other users are actively using the system' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        {{ __('messages.cancel') ?? 'Cancel' }}
                    </button>
                    <form action="{{ route('admin.updates.process') }}" method="POST" id="update-form">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-bold" id="btn-update" onclick="startUpdate(this)">
                            <i class="feather-download me-1"></i>
                            {{ __('messages.yes_update') ?? 'Yes, Update Now' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endsection

    <style>
        .changelog-content {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 400px;
            overflow-y: auto;
        }
        .wd-80 { width: 80px; }
        .ht-80 { height: 80px; }
        .wd-100 { width: 100px; }
        .ht-100 { height: 100px; }
        .spinner-border-sm-custom {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
    </style>

    <script>
        function checkForUpdates() {
            const btn = document.getElementById('btn-check-update');
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm-custom me-1" role="status"></span> {{ __("messages.checking") ?? "Checking..." }}';

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
                    // Reload to show full release details
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showFlash('success', '<i class="feather-check-circle me-2"></i>{{ __("messages.system_up_to_date") ?? "Your system is up to date!" }} (v' + data.currentVersion + ')');
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showFlash('danger', '<i class="feather-alert-circle me-2"></i>{{ __("messages.connection_error") ?? "Connection error. Please try again." }}');
            });
        }

        function startUpdate(btn) {
            // Get the form before disabling the button
            const form = btn.closest('form');
            if (form) {
                // Change button state
                btn.innerHTML = '<span class="spinner-border spinner-border-sm-custom me-1" role="status"></span> {{ __("messages.updating") ?? "Updating..." }}';
                // Add a small delay to allow the form to submit before disabling
                setTimeout(() => {
                    btn.disabled = true;
                }, 10);
            }
        }

        function showFlash(type, message) {
            const container = document.querySelector('.main-content');
            const existing = container.querySelector('.dynamic-alert');
            if (existing) existing.remove();

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-' + type + ' d-flex align-items-center mb-4 dynamic-alert';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = '<div>' + message + '</div>';
            
            // Insert after the page-header
            const pageHeader = container.querySelector('.page-header');
            if (pageHeader) {
                pageHeader.after(alertDiv);
            } else {
                container.prepend(alertDiv);
            }

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.style.transition = 'opacity 0.5s';
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 500);
            }, 5000);
        }
    </script>
@endsection
