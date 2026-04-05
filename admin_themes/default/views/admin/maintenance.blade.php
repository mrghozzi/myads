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
@endphp
<div class="admin-page">
<section class="admin-hero">
    <div class="admin-hero__content">
        <ul class="admin-breadcrumb">
            <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li>{{ __('messages.maintenance') }}</li>
        </ul>
        <div class="admin-hero__eyebrow">{{ __('messages.maintenance') }}</div>
        <h1 class="admin-hero__title">{{ __('messages.maintenance') }}</h1>
        <p class="admin-hero__copy">{{ __('messages.maintenance_settings_description') }}</p>
    </div>
    <div class="admin-hero__actions">
        <div class="admin-summary-grid w-100">
            <div class="admin-summary-card">
                <span class="admin-summary-label">{{ __('messages.status') }}</span>
                <span class="admin-summary-value">{{ $isEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}</span>
            </div>
        </div>
    </div>
</section>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-circle-xmark me-2"></i>
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

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm maintenance-settings-card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge {{ $isEnabled ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} px-3 py-2">
                                {{ $isEnabled ? __('messages.maintenance_status_enabled') : __('messages.maintenance_status_disabled') }}
                            </span>
                            <span class="text-muted small">{{ __('messages.maintenance_status_indicator') }}</span>
                        </div>
                        <h4 class="fw-bold mb-2">{{ __('messages.maintenance_settings_title') }}</h4>
                        <p class="text-muted mb-0">{{ __('messages.maintenance_settings_description') }}</p>
                    </div>
                    <div class="maintenance-status-panel">
                        <div class="maintenance-status-icon {{ $isEnabled ? 'is-enabled' : 'is-disabled' }}">
                            <i class="fa-solid {{ $isEnabled ? 'fa-screwdriver-wrench' : 'fa-circle-check' }}"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ __('messages.maintenance_current_state') }}</div>
                            <div class="text-muted small">{{ $isEnabled ? __('messages.maintenance_state_live') : __('messages.maintenance_state_normal') }}</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4">
                    <div class="d-flex align-items-start">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
                        <div>
                            <strong>{{ __('messages.maintenance_warning') }}</strong>
                            <div class="small text-muted mt-1">{{ __('messages.maintenance_access_note') }}</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.maintenance.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4 align-items-start">
                        <div class="col-xl-7">
                            <div class="card border maintenance-preview-card h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ __('messages.maintenance_preview_title') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.maintenance_preview_description') }}</p>
                                        </div>
                                        <div class="form-check form-switch maintenance-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="maintenance_enabled" name="maintenance_enabled" value="1" {{ old('maintenance_enabled', $isEnabled) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="maintenance_enabled">{{ __('messages.maintenance_toggle_label') }}</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="maintenance_message" class="form-label fw-semibold">{{ __('messages.maintenance_message_label') }}</label>
                                        <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="5" placeholder="{{ __('messages.maintenance_message_placeholder') }}">{{ old('maintenance_message', $maintenanceSettings['message'] ?? '') }}</textarea>
                                        <div class="form-text">{{ __('messages.maintenance_message_help') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="maintenance_logo" class="form-label fw-semibold">{{ __('messages.maintenance_logo_label') }}</label>
                                        <input class="form-control" type="file" id="maintenance_logo" name="maintenance_logo" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                                        <div class="form-text">{{ __('messages.maintenance_logo_help') }}</div>
                                    </div>

                                    @if($logoPath)
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <img src="{{ asset($logoPath) }}" alt="{{ __('messages.maintenance_logo_label') }}" class="maintenance-logo-preview">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="remove_maintenance_logo" name="remove_maintenance_logo">
                                                <label class="form-check-label" for="remove_maintenance_logo">{{ __('messages.maintenance_logo_remove') }}</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5">
                            <div class="maintenance-live-preview h-100">
                                @if($logoPath)
                                    <div class="maintenance-live-logo-wrap">
                                        <img src="{{ asset($logoPath) }}" alt="{{ __('messages.maintenance_logo_label') }}" class="maintenance-live-logo">
                                    </div>
                                @else
                                    <div class="maintenance-live-icon">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                @endif
                                <span class="maintenance-live-badge">{{ __('messages.error_503_title') }}</span>
                                <h5 class="fw-bold mb-3">{{ __('messages.maintenance_page_title') }}</h5>
                                <p class="text-muted mb-4">{{ $currentMessage }}</p>
                                <div class="maintenance-meta">
                                    <div>
                                        <div class="text-muted small">{{ __('messages.maintenance_enabled_at_label') }}</div>
                                        <div class="fw-semibold">
                                            @if(!empty($maintenanceSettings['enabled_at']))
                                                {{ \Carbon\Carbon::createFromTimestamp((int) $maintenanceSettings['enabled_at'])->format('Y-m-d H:i') }}
                                            @else
                                                {{ __('messages.not_available') ?? '—' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">{{ __('messages.maintenance_enabled_by_label') }}</div>
                                        <div class="fw-semibold">{{ $enabledBy?->username ?? __('messages.maintenance_system_actor') }}</div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">{{ __('messages.maintenance_last_change_label') }}</div>
                                        <div class="fw-semibold">
                                            @if(!empty($maintenanceSettings['last_changed_at']))
                                                {{ \Carbon\Carbon::createFromTimestamp((int) $maintenanceSettings['last_changed_at'])->diffForHumans() }}
                                            @else
                                                {{ __('messages.not_available') ?? '—' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">{{ __('messages.maintenance_last_actor_label') }}</div>
                                        <div class="fw-semibold">{{ $lastChangedBy?->username ?? __('messages.maintenance_system_actor') }}</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-4">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>{{ __('messages.maintenance_save_button') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-primary text-primary mb-3 mx-auto">
                    <i class="fa-solid fa-broom fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.clear_cache') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.clear_cache_desc') }}</p>
                <form action="{{ route('admin.maintenance.clear_cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary w-100 mt-auto">
                        <i class="fa-solid fa-play me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-success text-success mb-3 mx-auto">
                    <i class="fa-solid fa-database fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.run_migrations') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.run_migrations_desc') }}</p>
                <form action="{{ route('admin.maintenance.migrate') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-success w-100 mt-auto">
                        <i class="fa-solid fa-upload me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-info text-info mb-3 mx-auto">
                    <i class="fa-solid fa-wrench fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.db_repair') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.db_repair_desc') }}</p>
                <form action="{{ route('admin.maintenance.db_repair') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-info w-100 mt-auto">
                        <i class="fa-solid fa-gears me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-0">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-warning text-warning mb-3 mx-auto">
                    <i class="fa-solid fa-link-slash fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.repair_orphaned_records') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.repair_orphaned_records_desc') }}</p>
                <form action="{{ route('admin.maintenance.repair_orphaned') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning w-100 mt-auto">
                        <i class="fa-solid fa-scissors me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-danger text-danger mb-3 mx-auto">
                    <i class="fa-solid fa-comment-slash fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.repair_orphaned_content') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.repair_orphaned_content_desc') }}</p>
                <form action="{{ route('admin.maintenance.repair_orphaned_content') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 mt-auto">
                        <i class="fa-solid fa-trash-can me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 transition-hover">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-lg bg-soft-info text-info mb-3 mx-auto">
                    <i class="fa-solid fa-chart-pie fs-2"></i>
                </div>
                <h5 class="card-title fw-bold">{{ __('messages.repair_orphaned_stats') }}</h5>
                <p class="text-muted small mb-4">{{ __('messages.repair_orphaned_stats_desc') }}</p>
                <form action="{{ route('admin.maintenance.repair_orphaned_stats') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-info w-100 mt-auto">
                        <i class="fa-solid fa-eraser me-2"></i> {{ __('messages.execute') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(97, 93, 250, 0.1); }
    .bg-soft-success { background-color: rgba(30, 197, 137, 0.1); }
    .bg-soft-info { background-color: rgba(0, 204, 255, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-soft-danger { background-color: rgba(255, 92, 120, 0.1); }
    .transition-hover:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    .avatar-lg { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    .maintenance-settings-card { background: linear-gradient(135deg, rgba(97, 93, 250, 0.05), rgba(35, 210, 226, 0.05)); }
    .maintenance-status-panel { display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: rgba(255,255,255,0.65); border-radius: 18px; border: 1px solid rgba(97, 93, 250, 0.12); }
    .maintenance-status-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .maintenance-status-icon.is-enabled { background: rgba(255, 92, 120, 0.12); color: #ff5c78; }
    .maintenance-status-icon.is-disabled { background: rgba(30, 197, 137, 0.12); color: #1ec589; }
    .maintenance-preview-card { background: rgba(255,255,255,0.55); }
    .maintenance-switch .form-check-input { width: 3rem; height: 1.6rem; }
    .maintenance-live-preview { padding: 28px; border-radius: 24px; background: radial-gradient(circle at top, rgba(97,93,250,0.18), transparent 45%), #0f172a; color: #fff; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2); }
    .maintenance-live-icon, .maintenance-live-logo-wrap { width: 88px; height: 88px; border-radius: 22px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; }
    .maintenance-live-icon { font-size: 34px; }
    .maintenance-live-logo { max-width: 72px; max-height: 72px; object-fit: contain; }
    .maintenance-logo-preview { width: 88px; height: 88px; object-fit: contain; border-radius: 18px; border: 1px solid var(--bs-border-color); padding: 10px; background: var(--bs-body-bg); }
    .maintenance-live-badge { display: inline-flex; align-items: center; padding: 8px 14px; border-radius: 999px; background: rgba(255,255,255,0.12); color: #fff; font-size: 12px; margin-bottom: 18px; }
    .maintenance-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .maintenance-live-preview .text-muted { color: rgba(255,255,255,0.72) !important; }
    body[data-theme="css_d"] .maintenance-status-panel,
    body[data-theme="dark"] .maintenance-status-panel { background: rgba(15, 23, 42, 0.5); }
    @media (max-width: 767.98px) {
        .maintenance-meta { grid-template-columns: 1fr; }
        .maintenance-live-preview { padding: 22px; }
    }
</style>
</div>
@endsection
