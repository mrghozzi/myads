@extends('admin::layouts.admin')

@section('title', __('messages.maintenance'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $isEnabled = !empty($maintenanceSettings['enabled']);
    $currentMessage = $maintenanceSettings['message'] ?: __('messages.maintenance_default_message');
    $logoPath = $maintenanceSettings['logo_path'] ?? '';
    $enabledBy = $maintenanceUsers->get((int) ($maintenanceSettings['enabled_by'] ?? 0));
    $lastChangedBy = $maintenanceUsers->get((int) ($maintenanceSettings['last_changed_by'] ?? 0));

    $recordsTotal = $orphanDiagnostics['records']['total'] ?? 0;
    $contentTotal = $orphanDiagnostics['content']['total'] ?? 0;
    $statsTotal = $orphanDiagnostics['stats']['total'] ?? 0;
    $totalOrphans = $recordsTotal + $contentTotal + $statsTotal;
@endphp

<div class="admin-page">
    {{-- Hero Section --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.maintenance') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.system_integrity_tools') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.maintenance') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.maintenance_settings_description') }}</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.maintenance') }}" class="btn btn-light border me-2" title="{{ __('messages.refresh_diagnostics') }}">
                <i class="feather-refresh-cw me-1"></i> {{ __('messages.refresh_diagnostics') }}
            </a>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 {{ $isEnabled ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} border border-{{ $isEnabled ? 'danger' : 'success' }}-subtle">
                <span class="status-indicator-dot {{ $isEnabled ? 'bg-danger' : 'bg-success' }}"></span>
                <span class="fw-bold small">{{ $isEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}</span>
            </div>
        </div>
    </section>

    {{-- Alerts & Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="feather-check-circle fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="feather-alert-octagon fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm mb-4">
            <div class="fw-semibold mb-2">{{ __('messages.warning') ?? 'Warning' }}</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Section 1: Maintenance Mode Configuration & Public Preview --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm maintenance-main-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <span class="badge {{ $isEnabled ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} px-3 py-2 rounded-pill">
                                    <i class="feather-{{ $isEnabled ? 'shield-off' : 'shield' }} me-1"></i>
                                    {{ $isEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}
                                </span>
                                <span class="text-muted small">{{ __('messages.maintenance_status_indicator') }}</span>
                            </div>
                            <h4 class="fw-bold mb-2">{{ __('messages.maintenance_settings_title') }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.maintenance_settings_description') }}</p>
                        </div>
                        <div class="maintenance-status-panel">
                            <div class="maintenance-status-icon {{ $isEnabled ? 'is-enabled' : 'is-disabled' }}">
                                <i class="feather-{{ $isEnabled ? 'tool' : 'check-circle' }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ __('messages.maintenance_current_state') }}</div>
                                <div class="text-muted small">{{ $isEnabled ? __('messages.maintenance_state_live') : __('messages.maintenance_state_normal') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <i class="feather-alert-triangle fs-4 me-3 text-warning flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>{{ __('messages.maintenance_warning') }}</strong>
                                <div class="small text-muted mt-1">{{ __('messages.maintenance_access_note') }}</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.maintenance.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4 align-items-stretch">
                            {{-- Form Controls Column --}}
                            <div class="col-xl-7">
                                <div class="card border maintenance-form-box h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 border-bottom">
                                            <div>
                                                <h5 class="fw-bold mb-1">{{ __('messages.maintenance_preview_title') }}</h5>
                                                <p class="text-muted small mb-0">{{ __('messages.maintenance_preview_description') }}</p>
                                            </div>
                                            <div class="form-check form-switch maintenance-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch" id="maintenance_enabled" name="maintenance_enabled" value="1" {{ old('maintenance_enabled', $isEnabled) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="maintenance_enabled">{{ __('messages.maintenance_toggle_label') }}</label>
                                            </div>
                                        </div>

                                        {{-- Message --}}
                                        <div class="mb-3">
                                            <label for="maintenance_message" class="form-label fw-semibold">{{ __('messages.maintenance_message_label') }}</label>
                                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="4" placeholder="{{ __('messages.maintenance_message_placeholder') }}">{{ old('maintenance_message', $maintenanceSettings['message'] ?? '') }}</textarea>
                                            <div class="form-text text-muted">{{ __('messages.maintenance_message_help') }}</div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            {{-- Estimated Duration --}}
                                            <div class="col-md-6">
                                                <label for="estimated_duration" class="form-label fw-semibold">{{ __('messages.estimated_duration_label') }}</label>
                                                <input type="text" class="form-control" id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $maintenanceSettings['estimated_duration'] ?? '') }}" placeholder="{{ __('messages.estimated_duration_placeholder') }}">
                                                <div class="form-text text-muted">{{ __('messages.estimated_duration_help') }}</div>
                                            </div>

                                            {{-- Emergency Bypass Token --}}
                                            <div class="col-md-6">
                                                <label for="emergency_token" class="form-label fw-semibold">{{ __('messages.emergency_token_label') }}</label>
                                                <input type="text" class="form-control" id="emergency_token" name="emergency_token" value="{{ old('emergency_token', $maintenanceSettings['emergency_token'] ?? '') }}" placeholder="secret-bypass-token-123">
                                                <div class="form-text text-muted">{{ __('messages.emergency_token_help') }}</div>
                                            </div>
                                        </div>

                                        {{-- Allowed IPs Whitelist --}}
                                        <div class="mb-3">
                                            <label for="allowed_ips" class="form-label fw-semibold">{{ __('messages.allowed_ips_label') }}</label>
                                            <textarea class="form-control font-monospace" id="allowed_ips" name="allowed_ips" rows="2" placeholder="{{ __('messages.allowed_ips_placeholder') }}">{{ old('allowed_ips', $maintenanceSettings['allowed_ips'] ?? '') }}</textarea>
                                            <div class="form-text text-muted">{{ __('messages.allowed_ips_help') }}</div>
                                        </div>

                                        {{-- Logo Upload --}}
                                        <div class="mb-3">
                                            <label for="maintenance_logo" class="form-label fw-semibold">{{ __('messages.maintenance_logo_label') }}</label>
                                            <input class="form-control" type="file" id="maintenance_logo" name="maintenance_logo" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                                            <div class="form-text text-muted">{{ __('messages.maintenance_logo_help') }}</div>
                                        </div>

                                        @if($logoPath)
                                            <div class="d-flex align-items-center gap-3 flex-wrap p-2 border rounded-3 bg-body-tertiary">
                                                <img src="{{ asset($logoPath) }}" alt="{{ __('messages.maintenance_logo_label') }}" class="maintenance-logo-preview">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="remove_maintenance_logo" name="remove_maintenance_logo">
                                                    <label class="form-check-label text-danger fw-semibold" for="remove_maintenance_logo">{{ __('messages.maintenance_logo_remove') }}</label>
                                                </div>
                                            </div>
                                        @endif

                                        @if($bypassUrl)
                                            <div class="mt-3 p-3 rounded-3 bg-soft-info border border-info-subtle">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <div class="fw-semibold text-info small mb-1">{{ __('messages.emergency_bypass_url') }}</div>
                                                        <code class="user-select-all small text-break">{{ $bypassUrl }}</code>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-info text-white" onclick="copyBypassLink('{{ $bypassUrl }}')">
                                                        <i class="feather-copy me-1"></i> {{ __('messages.emergency_bypass_copy') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Live 503 Preview Column --}}
                            <div class="col-xl-5">
                                <div class="maintenance-live-preview h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="maintenance-live-badge">
                                                <i class="feather-eye me-1"></i> {{ __('messages.error_503_title') }}
                                            </span>
                                            @if(!empty($maintenanceSettings['estimated_duration']))
                                                <span class="badge bg-warning text-dark px-2 py-1 small">
                                                    <i class="feather-clock me-1"></i> {{ $maintenanceSettings['estimated_duration'] }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($logoPath)
                                            <div class="maintenance-live-logo-wrap">
                                                <img src="{{ asset($logoPath) }}" alt="{{ __('messages.maintenance_logo_label') }}" class="maintenance-live-logo">
                                            </div>
                                        @else
                                            <div class="maintenance-live-icon">
                                                <i class="feather-tool"></i>
                                            </div>
                                        @endif

                                        <h5 class="fw-bold mb-2 text-white">{{ __('messages.maintenance_page_title') }}</h5>
                                        <p class="text-white-50 small mb-4 maintenance-preview-text">{{ $currentMessage }}</p>
                                    </div>

                                    <div>
                                        <div class="maintenance-meta pt-3 border-top border-secondary-subtle">
                                            <div>
                                                <div class="text-white-50 small">{{ __('messages.maintenance_enabled_at_label') }}</div>
                                                <div class="fw-semibold text-white small">
                                                    @if(!empty($maintenanceSettings['enabled_at']))
                                                        {{ \Carbon\Carbon::createFromTimestamp((int) $maintenanceSettings['enabled_at'])->format('Y-m-d H:i') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-white-50 small">{{ __('messages.maintenance_enabled_by_label') }}</div>
                                                <div class="fw-semibold text-white small">{{ $enabledBy?->username ?? __('messages.maintenance_system_actor') }}</div>
                                            </div>
                                            <div>
                                                <div class="text-white-50 small">{{ __('messages.maintenance_last_change_label') }}</div>
                                                <div class="fw-semibold text-white small">
                                                    @if(!empty($maintenanceSettings['last_changed_at']))
                                                        {{ \Carbon\Carbon::createFromTimestamp((int) $maintenanceSettings['last_changed_at'])->diffForHumans() }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-white-50 small">{{ __('messages.maintenance_last_actor_label') }}</div>
                                                <div class="fw-semibold text-white small">{{ $lastChangedBy?->username ?? __('messages.maintenance_system_actor') }}</div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-semibold">
                                            <i class="feather-save me-2"></i>{{ __('messages.maintenance_save_button') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Smart Orphaned Data Repair Tools --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="feather-shield text-primary me-2"></i>{{ __('messages.diagnostic_overview') }}</h5>
                <p class="text-muted small mb-0">{{ __('messages.repair_orphaned_content_desc') }}</p>
            </div>
            <div>
                @if($totalOrphans > 0)
                    <span class="badge bg-soft-warning text-warning px-3 py-2">
                        <i class="feather-alert-triangle me-1"></i> {{ __('messages.orphan_detected_count', ['count' => $totalOrphans]) }}
                    </span>
                @else
                    <span class="badge bg-soft-success text-success px-3 py-2">
                        <i class="feather-check-circle me-1"></i> {{ __('messages.no_orphans_detected') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="row g-4">
            {{-- Orphaned Records --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="avatar-chip bg-soft-warning text-warning">
                                    <i class="feather-user-x fs-3"></i>
                                </div>
                                <span class="badge {{ $recordsTotal > 0 ? 'bg-danger' : 'bg-soft-success text-success' }} px-2 py-1">
                                    {{ $recordsTotal > 0 ? __('messages.orphan_detected_count', ['count' => $recordsTotal]) : __('messages.no_orphans_detected') }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.repair_orphaned_records') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.repair_orphaned_records_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.repair_orphaned') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_destructive_orphan') }}')">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="feather-scissors me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Orphaned Content --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="avatar-chip bg-soft-danger text-danger">
                                    <i class="feather-message-square fs-3"></i>
                                </div>
                                <span class="badge {{ $contentTotal > 0 ? 'bg-danger' : 'bg-soft-success text-success' }} px-2 py-1">
                                    {{ $contentTotal > 0 ? __('messages.orphan_detected_count', ['count' => $contentTotal]) : __('messages.no_orphans_detected') }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.repair_orphaned_content') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.repair_orphaned_content_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.repair_orphaned_content') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_destructive_orphan') }}')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="feather-trash-2 me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Orphaned Stats --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="avatar-chip bg-soft-info text-info">
                                    <i class="feather-pie-chart fs-3"></i>
                                </div>
                                <span class="badge {{ $statsTotal > 0 ? 'bg-danger' : 'bg-soft-success text-success' }} px-2 py-1">
                                    {{ $statsTotal > 0 ? __('messages.orphan_detected_count', ['count' => $statsTotal]) : __('messages.no_orphans_detected') }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.repair_orphaned_stats') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.repair_orphaned_stats_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.repair_orphaned_stats') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_destructive_orphan') }}')">
                            @csrf
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="feather-activity me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: System Optimization & Operations --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="feather-cpu text-primary me-2"></i>{{ __('messages.quick_actions') }}</h5>
                <p class="text-muted small mb-0">{{ __('messages.maintenance_warning') }}</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Clear Cache --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar-chip bg-soft-primary text-primary mb-3">
                                <i class="feather-zap fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.clear_cache') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.clear_cache_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.clear_cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="feather-play me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- DB Table Repair & Optimize --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar-chip bg-soft-success text-success mb-3">
                                <i class="feather-database fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.db_repair') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.db_repair_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.db_repair') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="feather-check-square me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Prune Sessions & Temp --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="avatar-chip bg-soft-warning text-warning">
                                    <i class="feather-clock fs-3"></i>
                                </div>
                                <span class="badge bg-soft-secondary text-secondary px-2 py-1">
                                    {{ __('messages.session_files_count', ['count' => $sessionFilesCount]) }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.prune_sessions_temp') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.prune_sessions_temp_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.prune_sessions') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="feather-trash me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Clean Logs --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="avatar-chip bg-soft-danger text-danger">
                                    <i class="feather-file-text fs-3"></i>
                                </div>
                                <span class="badge bg-soft-danger text-danger px-2 py-1">
                                    {{ __('messages.log_files_count', ['count' => $logFilesCount, 'size' => $logSizeMb]) }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.prune_logs') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.prune_logs_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.prune_logs') }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_action_prompt') }}')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="feather-trash-2 me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Run Migrations --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar-chip bg-soft-primary text-primary mb-3">
                                <i class="feather-upload-cloud fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">{{ __('messages.run_migrations') }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.run_migrations_desc') }}</p>
                        </div>
                        <form action="{{ route('admin.maintenance.migrate') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="feather-arrow-up-circle me-2"></i> {{ __('messages.execute_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-indicator-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .avatar-chip { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; }
    .bg-soft-primary { background-color: var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.12)); color: var(--admin-premium-accent, #615dfa); }
    .bg-soft-success { background-color: var(--admin-premium-success-soft, rgba(34, 197, 94, 0.12)); color: #17c666; }
    .bg-soft-info { background-color: rgba(61, 199, 190, 0.14); color: #3dc7be; }
    .bg-soft-warning { background-color: var(--admin-premium-warning-soft, rgba(245, 158, 11, 0.12)); color: #ffa21d; }
    .bg-soft-danger { background-color: var(--admin-premium-danger-soft, rgba(239, 68, 68, 0.12)); color: #ea4d4d; }
    .bg-soft-secondary { background-color: rgba(107, 114, 128, 0.12); color: #6b7280; }

    .transition-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .transition-hover:hover { transform: translateY(-4px); box-shadow: var(--admin-premium-shadow, 0 20px 40px rgba(15, 23, 42, 0.08)) !important; }

    .maintenance-main-card { background: var(--admin-premium-surface, #ffffff); border-radius: var(--admin-premium-radius, 24px); }
    .maintenance-status-panel { display: flex; align-items: center; gap: 14px; padding: 14px 20px; background: var(--admin-premium-surface-alt, #f6f7fb); border-radius: 18px; border: 1px solid var(--admin-premium-border, rgba(15,23,42,.08)); }
    .maintenance-status-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .maintenance-status-icon.is-enabled { background: var(--admin-premium-danger-soft, rgba(239, 68, 68, 0.12)); color: #ea4d4d; }
    .maintenance-status-icon.is-disabled { background: var(--admin-premium-success-soft, rgba(34, 197, 94, 0.12)); color: #17c666; }

    .maintenance-form-box { background: var(--admin-premium-surface-alt, #f6f7fb); border-radius: 18px; border-color: var(--admin-premium-border, rgba(15,23,42,.08)) !important; }
    .maintenance-switch .form-check-input { width: 3.2rem; height: 1.7rem; cursor: pointer; }

    .maintenance-live-preview { padding: 32px; border-radius: 22px; background: radial-gradient(circle at top, rgba(97,93,250,0.22), transparent 50%), #0f172a; color: #fff; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25); min-height: 480px; }
    .maintenance-live-icon, .maintenance-live-logo-wrap { width: 80px; height: 80px; border-radius: 20px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 32px; color: #fff; }
    .maintenance-live-logo { max-width: 64px; max-height: 64px; object-fit: contain; }
    .maintenance-logo-preview { width: 72px; height: 72px; object-fit: contain; border-radius: 14px; border: 1px solid var(--admin-premium-border); padding: 8px; background: #fff; }
    .maintenance-live-badge { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 999px; background: rgba(255,255,255,0.14); color: #fff; font-size: 12px; font-weight: 600; }
    .maintenance-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .maintenance-preview-text { line-height: 1.6; word-break: break-word; }

    html.app-skin-dark .maintenance-status-panel { background: rgba(255, 255, 255, 0.04); }
    html.app-skin-dark .maintenance-form-box { background: rgba(255, 255, 255, 0.02); }
    html.app-skin-dark .maintenance-logo-preview { background: var(--admin-premium-surface); }

    @media (max-width: 767.98px) {
        .maintenance-meta { grid-template-columns: 1fr; }
        .maintenance-live-preview { padding: 22px; }
    }
</style>

<script>
    function copyBypassLink(url) {
        if (!navigator.clipboard) {
            const input = document.createElement('input');
            input.value = url;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('{{ __('messages.emergency_bypass_copied') }}');
            return;
        }
        navigator.clipboard.writeText(url).then(function() {
            alert('{{ __('messages.emergency_bypass_copied') }}');
        });
    }
</script>
@endsection
