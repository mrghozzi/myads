@extends('admin::layouts.admin')

@section('title', __('messages.site_admins') ?? 'Site Administrators')

@section('content')
<div class="admin-page">
    <!-- Hero Section -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.site_admins') ?? 'Site Administrators' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1"><i class="feather-shield me-1"></i>{{ __('messages.options') ?? 'Roles & Permissions' }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-user-check text-primary"></i>
                {{ __('messages.site_admins') ?? 'Site Administrators' }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.site_admins_desc') ?? 'Assign full admin access or grant specific admin modules to selected members.' }}</p>

            <!-- Summary Stat Strip -->
            <div class="row g-3 mt-2">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-body-tertiary border shadow-sm">
                        <span class="text-muted small d-block mb-1">{{ __('messages.site_admins') ?? 'Total Admins' }}</span>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-body-tertiary border shadow-sm">
                        <span class="text-muted small d-block mb-1">{{ __('messages.super_admins') ?? 'Super Admins' }}</span>
                        <div class="fs-4 fw-bold text-danger">{{ number_format($stats['super'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-body-tertiary border shadow-sm">
                        <span class="text-muted small d-block mb-1">{{ __('messages.full_access_admins') ?? 'Full Access' }}</span>
                        <div class="fs-4 fw-bold text-info">{{ number_format($stats['full'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-body-tertiary border shadow-sm">
                        <span class="text-muted small d-block mb-1">{{ __('messages.active_admins') ?? 'Active Admins' }}</span>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['active'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success & Error Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-alert-octagon fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-alert-triangle fs-5"></i>
                <div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!empty($upgradeNotice))
        @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
    @endif

    @if($featureAvailable ?? true)
        <!-- Add New Administrator Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2 text-primary">
                    <i class="feather-user-plus"></i>
                    {{ __('messages.add_site_admin') ?? 'Assign New Administrator' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.admins.store') }}" method="POST" class="site-admin-form">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Select Member via AJAX Search -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                            
                            <!-- Selected User Preview Card -->
                            <div id="selected-user-card" class="p-2 rounded border bg-success-subtle border-success-subtle align-items-center justify-content-between mb-2" style="display: none;">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img id="selected-user-avatar" src="" class="rounded-circle border flex-shrink-0" width="36" height="36" alt="">
                                    <div class="overflow-hidden">
                                        <div id="selected-user-name" class="fw-bold text-success-emphasis small text-truncate"></div>
                                        <div id="selected-user-email" class="text-muted text-truncate" style="font-size: 11px;"></div>
                                    </div>
                                </div>
                                <button type="button" id="clear-selected-user" class="btn btn-sm btn-light-danger border-0 p-1 rounded-circle" title="Clear selection">
                                    <i class="feather-x fs-6"></i>
                                </button>
                            </div>

                            <!-- Hidden Input for Form Submission -->
                            <input type="hidden" name="user_id" id="admin-selected-user-id" required>

                            <!-- Interactive Search Container -->
                            <div class="position-relative" id="user-search-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-primary-subtle"><i class="feather-search text-muted"></i></span>
                                    <input type="text" id="admin-user-search-input" class="form-control border-primary-subtle" placeholder="{{ __('messages.search_user_ajax_placeholder') ?? 'Type username or email address...' }}" autocomplete="off">
                                    <span class="input-group-text bg-body-tertiary border-primary-subtle d-none" id="user-search-spinner">
                                        <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                    </span>
                                </div>
                                <!-- AJAX Results Dropdown Menu -->
                                <div id="admin-user-search-results" class="dropdown-menu w-100 shadow-lg p-0 mt-1 border" style="max-height: 280px; overflow-y: auto; z-index: 1060;">
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">Search members by typing username or email address.</small>
                        </div>

                        <!-- Access Level Switch -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('messages.access_level') }}</label>
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label fw-bold mb-0 cursor-pointer" for="create_has_full_access">
                                        {{ __('messages.full_admin_access') }}
                                    </label>
                                    <div class="small text-muted">{{ __('messages.full_admin_access_hint') }}</div>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0 ms-2">
                                    <input class="form-check-input full-access-toggle" type="checkbox" name="has_full_access" id="create_has_full_access" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Active Status Switch -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('messages.status') }}</label>
                            <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between">
                                <label class="form-check-label fw-bold mb-0 cursor-pointer" for="create_is_active">
                                    {{ __('messages.active') }}
                                </label>
                                <div class="form-check form-switch fs-4 mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                                </div>
                            </div>
                        </div>

                        <!-- Module Permissions Grid -->
                        <div class="col-12 module-permissions-wrap">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold mb-0">{{ __('messages.module_permissions') }}</label>
                                <small class="text-muted">{{ __('messages.module_permissions_hint') }}</small>
                            </div>
                            
                            <div class="p-3 rounded border bg-body-tertiary">
                                <div class="row g-3">
                                    @foreach($permissionModules as $permissionModule)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-card p-2 rounded border bg-body">
                                                <input class="form-check-input module-permission-checkbox ms-1" type="checkbox" name="permissions[]" id="create_permission_{{ $permissionModule }}" value="{{ $permissionModule }}">
                                                <label class="form-check-label fw-semibold ms-2" for="create_permission_{{ $permissionModule }}">
                                                    {{ __('messages.admin_module_' . $permissionModule) ?? ucfirst($permissionModule) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="feather-user-plus"></i>
                            <span>{{ __('messages.add') ?? 'Assign Administrator' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.admins') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('messages.search_admins_placeholder') ?? 'Search by username or email...' }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="level" class="form-select">
                        <option value="">-- {{ __('messages.all_access_levels') ?? 'All Access Levels' }} --</option>
                        <option value="super" {{ request('level') === 'super' ? 'selected' : '' }}>{{ __('messages.super_admins') ?? 'Super Administrators' }}</option>
                        <option value="full" {{ request('level') === 'full' ? 'selected' : '' }}>{{ __('messages.full_access_admins') ?? 'Full Access Admins' }}</option>
                        <option value="limited" {{ request('level') === 'limited' ? 'selected' : '' }}>{{ __('messages.limited_access_admins') ?? 'Limited Access Admins' }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- {{ __('messages.all_statuses') ?? 'All Statuses' }} --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="feather-filter me-1"></i> {{ __('messages.filter') ?? 'Filter' }}
                    </button>
                    @if(request()->anyFilled(['search', 'level', 'status']))
                        <a href="{{ route('admin.admins') }}" class="btn btn-outline-secondary" title="{{ __('messages.reset_filters') ?? 'Reset' }}">
                            <i class="feather-refresh-cw"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Administrators Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                <i class="feather-users"></i>
                {{ __('messages.site_admins') }} ({{ number_format($admins->total()) }})
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>{{ __('messages.user') }}</th>
                            <th>{{ __('messages.access_level') }}</th>
                            <th>{{ __('messages.module_permissions') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.created_by') }}</th>
                            <th class="text-end pe-4">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            @php
                                $permissions = is_array($admin->permissions) ? $admin->permissions : [];
                                $isSuper = $admin->is_super || (int) $admin->user_id === 1;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted fw-bold">{{ $admin->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="{{ $admin->user?->img ? asset($admin->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" class="rounded-circle border" width="40" height="40" alt="{{ $admin->user->username ?? 'User' }}">
                                            @if($isSuper)
                                                <span class="position-absolute bottom-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:16px; height:16px; font-size:10px;" title="Super Admin">★</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-body">
                                                {{ $admin->user->username ?? ('#' . $admin->user_id) }}
                                            </div>
                                            <div class="small text-muted">{{ $admin->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($isSuper)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                            <i class="feather-shield me-1"></i> {{ __('messages.super_admin') }}
                                        </span>
                                    @elseif($admin->has_full_access)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                            <i class="feather-check-circle me-1"></i> {{ __('messages.full_admin_access') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-bold">
                                            <i class="feather-lock me-1"></i> {{ __('messages.limited_admin_access') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($isSuper || $admin->has_full_access)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="feather-layers me-1"></i> {{ __('messages.all_modules') }} ({{ count($permissionModules) }})
                                        </span>
                                    @elseif(empty($permissions))
                                        <span class="text-muted small">No specific modules</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 320px;">
                                            @foreach(array_slice($permissions, 0, 4) as $permission)
                                                <span class="badge bg-body-tertiary text-body border px-2 py-1 small">
                                                    {{ __('messages.admin_module_' . $permission) ?? ucfirst($permission) }}
                                                </span>
                                            @endforeach
                                            @if(count($permissions) > 4)
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 small" title="{{ implode(', ', $permissions) }}">
                                                    +{{ count($permissions) - 4 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($isSuper)
                                        <span class="badge bg-success px-2 py-1">{{ __('messages.active') }}</span>
                                    @else
                                        <form action="{{ route('admin.admins.status', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-link p-0 border-0 text-decoration-none">
                                                @if($admin->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 cursor-pointer">
                                                        <i class="feather-check me-1"></i> {{ __('messages.active') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 cursor-pointer">
                                                        <i class="feather-x me-1"></i> {{ __('messages.inactive') }}
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if($admin->creator)
                                        <span class="small fw-semibold text-body"><i class="feather-user me-1 text-muted"></i> {{ $admin->creator->username }}</span>
                                    @else
                                        <span class="text-muted small">System</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}" title="{{ __('messages.edit') }}">
                                            <i class="feather-edit-3"></i>
                                        </button>
                                        @if(!$isSuper)
                                            <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="feather-users fs-1 d-block mb-2 text-secondary"></i>
                                    <div>{{ ($featureAvailable ?? true) ? __('messages.no_data') : __('messages.upgrade_legacy_mode_notice') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(($featureAvailable ?? true) && $admins->hasPages())
            <div class="card-footer bg-transparent border-top py-3 d-flex align-items-center justify-content-between">
                <div class="text-muted small">
                    Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} administrators
                </div>
                <div>
                    {{ $admins->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('modals')
<!-- Edit Admin Modals -->
@if($featureAvailable ?? true)
    @foreach($admins as $admin)
        @php
            $permissions = is_array($admin->permissions) ? $admin->permissions : [];
            $isLockedSuperAdmin = $admin->is_super || (int) $admin->user_id === 1;
        @endphp
        <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-body-tertiary border-bottom py-3">
                        <h5 class="modal-title fw-bold text-primary d-flex align-items-center gap-2">
                            <i class="feather-edit-3"></i>
                            {{ __('messages.edit_site_admin') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST" class="site-admin-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <!-- User Display Banner -->
                                <div class="col-12">
                                    <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center gap-3">
                                        <img src="{{ $admin->user?->img ? asset($admin->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" class="rounded-circle border" width="48" height="48" alt="{{ $admin->user->username ?? '' }}">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-body">{{ $admin->user->username ?? ('#' . $admin->user_id) }}</h6>
                                            <small class="text-muted">{{ $admin->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="user_id" value="{{ $admin->user_id }}">
                                </div>

                                <!-- Access Level -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.access_level') }}</label>
                                    <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between">
                                        <div>
                                            <label class="form-check-label fw-bold mb-0 cursor-pointer" for="edit_has_full_access_{{ $admin->id }}">
                                                {{ __('messages.full_admin_access') }}
                                            </label>
                                            <div class="small text-muted">{{ __('messages.full_admin_access_hint') }}</div>
                                        </div>
                                        <div class="form-check form-switch fs-4 mb-0 ms-2">
                                            <input class="form-check-input full-access-toggle" type="checkbox" name="has_full_access" id="edit_has_full_access_{{ $admin->id }}" value="1" {{ $admin->has_full_access ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    @if($isLockedSuperAdmin)
                                        <input type="hidden" name="has_full_access" value="{{ $admin->has_full_access ? 1 : 0 }}">
                                    @endif
                                </div>

                                <!-- Status Switch -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.status') }}</label>
                                    <div class="p-3 rounded border bg-body-tertiary d-flex align-items-center justify-content-between">
                                        <label class="form-check-label fw-bold mb-0 cursor-pointer" for="edit_is_active_{{ $admin->id }}">
                                            {{ __('messages.active') }}
                                        </label>
                                        <div class="form-check form-switch fs-4 mb-0 ms-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active_{{ $admin->id }}" value="1" {{ $admin->is_active ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    @if($isLockedSuperAdmin)
                                        <input type="hidden" name="is_active" value="1">
                                    @endif
                                </div>

                                <!-- Module Permissions Grid -->
                                <div class="col-12 module-permissions-wrap">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold mb-0">{{ __('messages.module_permissions') }}</label>
                                        <small class="text-muted">Specific modules granted to this admin</small>
                                    </div>
                                    <div class="p-3 rounded border bg-body-tertiary">
                                        <div class="row g-3">
                                            @foreach($permissionModules as $permissionModule)
                                                <div class="col-md-4 col-lg-3">
                                                    <div class="form-check form-card p-2 rounded border bg-body">
                                                        <input class="form-check-input module-permission-checkbox ms-1" type="checkbox" name="permissions[]" id="edit_permission_{{ $permissionModule }}_{{ $admin->id }}" value="{{ $permissionModule }}" {{ in_array($permissionModule, $permissions, true) ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                                        <label class="form-check-label fw-semibold ms-2" for="edit_permission_{{ $permissionModule }}_{{ $admin->id }}">
                                                            {{ __('messages.admin_module_' . $permissionModule) ?? ucfirst($permissionModule) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if($isLockedSuperAdmin)
                                        @foreach($permissions as $permission)
                                            <input type="hidden" name="permissions[]" value="{{ $permission }}">
                                        @endforeach
                                    @endif
                                </div>

                                @if($isLockedSuperAdmin)
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0 d-flex align-items-center gap-2">
                                            <i class="feather-lock fs-5"></i>
                                            <div>{{ __('messages.super_admin_status_locked') ?? 'Super Administrator credentials & permissions are locked for system protection.' }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer bg-transparent border-top py-3">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" {{ $isLockedSuperAdmin ? 'disabled' : '' }}>{{ __('messages.update') ?? 'Update Administrator' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.site-admin-form');

        forms.forEach(function (form) {
            const fullAccessToggle = form.querySelector('.full-access-toggle');
            const permissionsWrap = form.querySelector('.module-permissions-wrap');

            if (!fullAccessToggle || !permissionsWrap) {
                return;
            }

            const syncPermissions = function () {
                const disablePermissions = fullAccessToggle.checked || fullAccessToggle.disabled;
                permissionsWrap.style.opacity = disablePermissions ? '0.5' : '1';

                permissionsWrap.querySelectorAll('.module-permission-checkbox').forEach(function (checkbox) {
                    checkbox.disabled = disablePermissions;
                    if (disablePermissions) {
                        checkbox.checked = false;
                    }
                });
            };

            fullAccessToggle.addEventListener('change', syncPermissions);
            syncPermissions();
        });

        // Smart AJAX User Search Picker
        const userSearchInput = document.getElementById('admin-user-search-input');
        const userSearchResults = document.getElementById('admin-user-search-results');
        const userSearchSpinner = document.getElementById('user-search-spinner');
        const selectedUserIdInput = document.getElementById('admin-selected-user-id');
        const selectedUserCard = document.getElementById('selected-user-card');
        const selectedUserAvatar = document.getElementById('selected-user-avatar');
        const selectedUserName = document.getElementById('selected-user-name');
        const selectedUserEmail = document.getElementById('selected-user-email');
        const clearSelectedUserBtn = document.getElementById('clear-selected-user');
        const searchWrapper = document.getElementById('user-search-wrapper');

        let debounceTimer = null;

        function performUserSearch(query) {
            if (!userSearchSpinner || !userSearchResults) return;
            userSearchSpinner.classList.remove('d-none');

            fetch('{{ route('admin.admins.search_users') }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    userSearchSpinner.classList.add('d-none');
                    userSearchResults.innerHTML = '';

                    if (!data || data.length === 0) {
                        userSearchResults.innerHTML = '<div class="p-3 text-center text-muted small"><i class="feather-user-x me-1"></i> {{ __('messages.no_data') }}</div>';
                        userSearchResults.classList.add('show');
                        return;
                    }

                    data.forEach(user => {
                        const item = document.createElement('a');
                        item.href = 'javascript:void(0);';
                        item.className = 'dropdown-item p-2 border-bottom d-flex align-items-center gap-2 cursor-pointer';
                        item.innerHTML = `
                            <img src="${user.avatar}" class="rounded-circle border flex-shrink-0" width="32" height="32">
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold text-truncate small">${user.username}</div>
                                <div class="text-muted text-truncate" style="font-size: 11px;">${user.email}</div>
                            </div>
                        `;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectUser(user);
                        });
                        userSearchResults.appendChild(item);
                    });
                    userSearchResults.classList.add('show');
                })
                .catch(err => {
                    userSearchSpinner.classList.add('d-none');
                    console.error('AJAX search error:', err);
                });
        }

        function selectUser(user) {
            if (!selectedUserIdInput || !selectedUserCard) return;
            selectedUserIdInput.value = user.id;
            selectedUserAvatar.src = user.avatar;
            selectedUserName.textContent = user.username;
            selectedUserEmail.textContent = user.email;
            selectedUserCard.style.display = 'flex';
            searchWrapper.style.display = 'none';
            userSearchResults.classList.remove('show');
        }

        if (clearSelectedUserBtn) {
            clearSelectedUserBtn.addEventListener('click', function() {
                selectedUserIdInput.value = '';
                if (userSearchInput) userSearchInput.value = '';
                selectedUserCard.style.display = 'none';
                searchWrapper.style.display = 'block';
            });
        }

        if (userSearchInput) {
            userSearchInput.addEventListener('focus', function() {
                performUserSearch(this.value.trim());
            });

            userSearchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                debounceTimer = setTimeout(() => {
                    performUserSearch(query);
                }, 250);
            });
        }

        document.addEventListener('click', function(e) {
            if (searchWrapper && !searchWrapper.contains(e.target)) {
                if (userSearchResults) userSearchResults.classList.remove('show');
            }
        });
    });
</script>
@endpush
