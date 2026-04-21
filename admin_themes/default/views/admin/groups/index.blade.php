@extends('admin::layouts.admin')

@section('title', __('messages.admin_groups_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.admin_groups_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.admin_groups_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.admin_groups_description') }}</p>

            @if($schemaReady && $groups instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="admin-stat-strip">
                    <div class="admin-stat-card">
                        <span class="admin-stat-label">{{ __('messages.groups') ?? 'Groups' }}</span>
                        <span class="admin-stat-value">{{ number_format($groups->total()) }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="admin-hero__actions">
            <a class="btn btn-primary" href="{{ route('admin.groups.settings') }}">
                <i class="feather-settings me-2"></i>
                {{ __('messages.admin_groups_settings_title') }}
            </a>
        </div>
    </section>

    @if(!$schemaReady)
        <div class="container-fluid mt-4">
            <div class="alert alert-warning">
                <i class="feather-alert-triangle me-2"></i>
                {{ __('messages.groups_feature_disabled') }}
            </div>
        </div>
    @else
        <section class="admin-panel mt-4">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.admin_groups_title') }}</span>
                    <h2 class="admin-panel__title">
                        @if($groups instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $groups->total() > 0)
                            {{ $groups->firstItem() }}-{{ $groups->lastItem() }} / {{ $groups->total() }}
                        @else
                            {{ $groups->count() }}
                        @endif
                    </h2>
                </div>
                <div class="admin-hero__actions p-0 shadow-none bg-transparent">
                    <form method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>{{ __('messages.all') }}</option>
                            <option value="pending_review" {{ $filter === 'pending_review' ? 'selected' : '' }}>{{ __('messages.groups_status_pending_review') }}</option>
                            <option value="active" {{ $filter === 'active' ? 'selected' : '' }}>{{ __('messages.groups_status_active') }}</option>
                            <option value="suspended" {{ $filter === 'suspended' ? 'selected' : '' }}>{{ __('messages.groups_status_suspended') }}</option>
                            <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>{{ __('messages.groups_status_rejected') }}</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="admin-panel__body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.author') }}</th>
                                <th>{{ __('messages.groups_privacy') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.members') }}</th>
                                <th>{{ __('messages.posts') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        <div class="fw-bold text-dark">{{ $group->name }}</div>
                                        <div class="text-muted small">@ {{ $group->slug }}</div>
                                    </td>
                                    <td data-label="{{ __('messages.author') }}">
                                        @if($group->owner)
                                            <a href="{{ route('profile.show', $group->owner->username) }}" class="admin-person">
                                                <span class="admin-person__avatar" style="width: 24px; height: 24px;">
                                                    <img src="{{ $group->owner->img ? asset($group->owner->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $group->owner->username }}">
                                                </span>
                                                <span class="admin-person__name fs-13">{{ $group->owner->username }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted small">{{ __('messages.unknown_user') }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.groups_privacy') }}">
                                        @if($group->privacy === \App\Models\Group::PRIVACY_PUBLIC)
                                            <span class="badge bg-soft-success text-success">
                                                <i class="feather-globe me-1"></i> {{ __('messages.groups_public') }}
                                            </span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">
                                                <i class="feather-lock me-1"></i> {{ __('messages.groups_private') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.status') }}">
                                        @php
                                            $statusClass = match($group->status) {
                                                'active' => 'bg-soft-success text-success',
                                                'pending_review' => 'bg-soft-warning text-warning',
                                                'suspended' => 'bg-soft-danger text-danger',
                                                'rejected' => 'bg-soft-secondary text-secondary',
                                                default => 'bg-soft-primary text-primary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ __('messages.groups_status_' . $group->status) }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('messages.members') }}">
                                        <span class="fw-bold">{{ number_format($group->members_count) }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.posts') }}">
                                        <span class="fw-bold">{{ number_format($group->posts_count) }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                        <div class="admin-action-cluster">
                                            <a class="btn btn-sm btn-outline-secondary admin-icon-btn" href="{{ route('groups.show', $group) }}" target="_blank" title="{{ __('messages.view') }}">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary admin-icon-btn" href="{{ route('admin.groups.edit', $group) }}" title="{{ __('messages.Settings') }}">
                                                <i class="feather-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-info admin-icon-btn" href="{{ route('admin.groups.members', $group) }}" title="{{ __('messages.members') }}">
                                                <i class="feather-users"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.groups.status', $group) }}" class="d-inline-block">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                    @foreach(['pending_review', 'active', 'suspended', 'rejected'] as $status)
                                                        <option value="{{ $status }}" {{ $group->status === $status ? 'selected' : '' }}>
                                                            {{ __('messages.groups_status_' . $status) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                            <form method="POST" action="{{ route('admin.groups.feature', $group) }}" class="d-inline-block">
                                                @csrf
                                                <button class="btn btn-sm {{ $group->is_featured ? 'btn-primary' : 'btn-outline-primary' }} admin-icon-btn" type="submit" title="{{ $group->is_featured ? __('messages.groups_unfeature') : __('messages.groups_feature') }}">
                                                    <i class="feather-star"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="admin-empty-state py-5">
                                            <div class="admin-avatar-circle mb-3">
                                                <i class="feather-users"></i>
                                            </div>
                                            <h4 class="mb-1">{{ __('messages.groups_empty_state') }}</h4>
                                            <p class="text-muted">{{ __('messages.admin_groups_description') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($groups instanceof \Illuminate\Contracts\Pagination\Paginator && $groups->hasPages())
                <div class="admin-panel__footer">
                    {{ $groups->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
