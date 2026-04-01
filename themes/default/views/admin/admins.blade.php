@extends('theme::layouts.admin')

@section('title', __('messages.site_admins'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.site_admins') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.site_admins') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.site_admins_desc') }}</p>
        </div>
    </section>
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-1">{{ __('messages.site_admins') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.site_admins_desc') }}</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($upgradeNotice))
                    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
                @endif

                @if($featureAvailable ?? true)
                    <form action="{{ route('admin.admins.store') }}" method="POST" class="border rounded-3 p-3 mb-4 site-admin-form">
                        @csrf
                        <h6 class="mb-3">{{ __('messages.add_site_admin') }}</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.user') }}</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">{{ __('messages.select') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.access_level') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input full-access-toggle" type="checkbox" name="has_full_access" id="create_has_full_access" value="1">
                                    <label class="form-check-label" for="create_has_full_access">{{ __('messages.full_admin_access') }}</label>
                                </div>
                                <small class="text-muted">{{ __('messages.full_admin_access_hint') }}</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                                    <label class="form-check-label" for="create_is_active">{{ __('messages.active') }}</label>
                                </div>
                            </div>

                            <div class="col-12 module-permissions-wrap">
                                <label class="form-label">{{ __('messages.module_permissions') }}</label>
                                <div class="row g-2">
                                    @foreach($permissionModules as $permissionModule)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input module-permission-checkbox" type="checkbox" name="permissions[]" id="create_permission_{{ $permissionModule }}" value="{{ $permissionModule }}">
                                                <label class="form-check-label" for="create_permission_{{ $permissionModule }}">
                                                    {{ __('messages.admin_module_' . $permissionModule) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">{{ __('messages.module_permissions_hint') }}</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-user-plus me-1"></i>{{ __('messages.add') }}
                            </button>
                        </div>
                    </form>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.access_level') }}</th>
                                <th>{{ __('messages.module_permissions') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.created_by') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                @php
                                    $permissions = is_array($admin->permissions) ? $admin->permissions : [];
                                @endphp
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $admin->user->username ?? ('#' . $admin->user_id) }}</div>
                                        <small class="text-muted">{{ $admin->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($admin->is_super)
                                            <span class="badge bg-danger">{{ __('messages.super_admin') }}</span>
                                        @elseif($admin->has_full_access)
                                            <span class="badge bg-primary">{{ __('messages.full_admin_access') }}</span>
                                        @else
                                            <span class="badge bg-info text-dark">{{ __('messages.limited_admin_access') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->is_super || $admin->has_full_access)
                                            <span class="badge bg-light text-dark border">{{ __('messages.all_modules') }}</span>
                                        @elseif(empty($permissions))
                                            <span class="text-muted">-</span>
                                        @else
                                            @foreach($permissions as $permission)
                                                <span class="badge bg-light text-dark border">{{ __('messages.admin_module_' . $permission) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->is_active)
                                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->creator)
                                            <div class="fw-semibold">{{ $admin->creator->username }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                            <i class="feather-edit-3"></i>
                                        </button>
                                        @if(!$admin->is_super && (int) $admin->user_id !== 1)
                                            <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ ($featureAvailable ?? true) ? __('messages.no_data') : __('messages.upgrade_legacy_mode_notice') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(($featureAvailable ?? true) && $admins->hasPages())
                    {{ $admins->links('pagination::bootstrap-5') }}
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('modals')
@if($featureAvailable ?? true)
@foreach($admins as $admin)
    @php
        $permissions = is_array($admin->permissions) ? $admin->permissions : [];
        $isLockedSuperAdmin = $admin->is_super || (int) $admin->user_id === 1;
    @endphp
    <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.edit_site_admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST" class="site-admin-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.user') }}</label>
                                <select name="user_id" class="form-select" {{ $isLockedSuperAdmin ? 'disabled' : '' }} required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (int) $admin->user_id === (int) $user->id ? 'selected' : '' }}>
                                            {{ $user->username }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($isLockedSuperAdmin)
                                    <input type="hidden" name="user_id" value="{{ $admin->user_id }}">
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.access_level') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input full-access-toggle" type="checkbox" name="has_full_access" id="edit_has_full_access_{{ $admin->id }}" value="1" {{ $admin->has_full_access ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="edit_has_full_access_{{ $admin->id }}">{{ __('messages.full_admin_access') }}</label>
                                </div>
                                @if($isLockedSuperAdmin)
                                    <input type="hidden" name="has_full_access" value="{{ $admin->has_full_access ? 1 : 0 }}">
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active_{{ $admin->id }}" value="1" {{ $admin->is_active ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="edit_is_active_{{ $admin->id }}">{{ __('messages.active') }}</label>
                                </div>
                                @if($isLockedSuperAdmin)
                                    <input type="hidden" name="is_active" value="1">
                                @endif
                            </div>

                            <div class="col-12 module-permissions-wrap">
                                <label class="form-label">{{ __('messages.module_permissions') }}</label>
                                <div class="row g-2">
                                    @foreach($permissionModules as $permissionModule)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input module-permission-checkbox" type="checkbox" name="permissions[]" id="edit_permission_{{ $permissionModule }}_{{ $admin->id }}" value="{{ $permissionModule }}" {{ in_array($permissionModule, $permissions, true) ? 'checked' : '' }} {{ $isLockedSuperAdmin ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="edit_permission_{{ $permissionModule }}_{{ $admin->id }}">
                                                    {{ __('messages.admin_module_' . $permissionModule) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($isLockedSuperAdmin)
                                    @foreach($permissions as $permission)
                                        <input type="hidden" name="permissions[]" value="{{ $permission }}">
                                    @endforeach
                                @endif
                            </div>

                            @if($isLockedSuperAdmin)
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">{{ __('messages.super_admin_cannot_be_removed') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
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
                permissionsWrap.style.opacity = disablePermissions ? '0.6' : '1';

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
    });
</script>
@endpush
