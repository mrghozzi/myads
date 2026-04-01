@extends('theme::layouts.admin')

@section('title', __('messages.forum_moderators'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.forum_moderators') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.forum_moderators') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.forum_moderators') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.forum_moderators_desc') }}</p>
        </div>
    </section>
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-1">{{ __('messages.forum_moderators') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.forum_moderators_desc') }}</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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

                <form action="{{ route('admin.forum.moderators.store') }}" method="POST" class="border rounded-3 p-3 mb-4">
                    @csrf
                    <h6 class="mb-3">{{ __('messages.add_forum_moderator') }}</h6>

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
                            <label class="form-label">{{ __('messages.scope') }}</label>
                            <div class="form-check">
                                <input class="form-check-input scope-global-toggle" type="checkbox" name="is_global" id="create_is_global" value="1">
                                <label class="form-check-label" for="create_is_global">{{ __('messages.global_moderator') }}</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                                <label class="form-check-label" for="create_is_active">{{ __('messages.active') }}</label>
                            </div>
                        </div>

                        <div class="col-12 category-scope-wrap">
                            <label class="form-label">{{ __('messages.moderated_categories') }}</label>
                            <select name="category_ids[]" class="form-select" multiple size="6">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('messages.moderated_categories_hint') }}</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('messages.permissions') }}</label>
                            <div class="row g-2">
                                @foreach($permissionKeys as $permissionKey)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" id="create_permission_{{ $permissionKey }}" value="{{ $permissionKey }}">
                                            <label class="form-check-label" for="create_permission_{{ $permissionKey }}">
                                                {{ __('messages.permission_' . $permissionKey) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-user-plus me-1"></i>{{ __('messages.add') }}
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.scope') }}</th>
                                <th>{{ __('messages.permissions') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($moderators as $moderator)
                                @php
                                    $moderatorPermissions = is_array($moderator->permissions) ? $moderator->permissions : [];
                                @endphp
                                <tr>
                                    <td>{{ $moderator->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $moderator->user->username ?? ('#' . $moderator->user_id) }}</div>
                                        <small class="text-muted">{{ $moderator->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($moderator->is_global)
                                            <span class="badge bg-primary">{{ __('messages.global_moderator') }}</span>
                                        @else
                                            <span class="badge bg-info text-dark">{{ __('messages.section_moderator') }}</span>
                                            <div class="small text-muted mt-1">
                                                @if($moderator->categories->isEmpty())
                                                    -
                                                @else
                                                    {{ $moderator->categories->pluck('name')->implode(', ') }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(empty($moderatorPermissions))
                                            <span class="text-muted">-</span>
                                        @else
                                            @foreach($moderatorPermissions as $permission)
                                                <span class="badge bg-light text-dark border">{{ __('messages.permission_' . $permission) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($moderator->is_active)
                                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#editModeratorModal{{ $moderator->id }}">
                                            <i class="feather-edit-3"></i>
                                        </button>
                                        <form action="{{ route('admin.forum.moderators.delete', $moderator->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $moderators->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('modals')
@foreach($moderators as $moderator)
    @php
        $moderatorPermissions = is_array($moderator->permissions) ? $moderator->permissions : [];
    @endphp
    <div class="modal fade" id="editModeratorModal{{ $moderator->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.edit_forum_moderator') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.forum.moderators.update', $moderator->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.user') }}</label>
                                <select name="user_id" class="form-select" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (int) $moderator->user_id === (int) $user->id ? 'selected' : '' }}>
                                            {{ $user->username }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.scope') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input scope-global-toggle" type="checkbox" name="is_global" id="edit_is_global_{{ $moderator->id }}" value="1" {{ $moderator->is_global ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_is_global_{{ $moderator->id }}">{{ __('messages.global_moderator') }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active_{{ $moderator->id }}" value="1" {{ $moderator->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_is_active_{{ $moderator->id }}">{{ __('messages.active') }}</label>
                                </div>
                            </div>

                            <div class="col-12 category-scope-wrap">
                                <label class="form-label">{{ __('messages.moderated_categories') }}</label>
                                <select name="category_ids[]" class="form-select" multiple size="6">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $moderator->categories->contains('id', $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('messages.permissions') }}</label>
                                <div class="row g-2">
                                    @foreach($permissionKeys as $permissionKey)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" id="edit_permission_{{ $permissionKey }}_{{ $moderator->id }}" value="{{ $permissionKey }}" {{ in_array($permissionKey, $moderatorPermissions, true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="edit_permission_{{ $permissionKey }}_{{ $moderator->id }}">
                                                    {{ __('messages.permission_' . $permissionKey) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('form');
        cards.forEach(function (form) {
            const globalToggle = form.querySelector('.scope-global-toggle');
            const categoryWrap = form.querySelector('.category-scope-wrap');
            if (!globalToggle || !categoryWrap) {
                return;
            }

            const select = categoryWrap.querySelector('select');
            const syncScope = function () {
                const isGlobal = globalToggle.checked;
                categoryWrap.style.opacity = isGlobal ? '0.6' : '1';
                if (select) {
                    select.disabled = isGlobal;
                }
            };

            globalToggle.addEventListener('change', syncScope);
            syncScope();
        });
    });
</script>
@endpush
