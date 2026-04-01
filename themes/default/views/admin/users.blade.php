@extends('theme::layouts.admin')

@section('title', __('messages.users'))

@section('content')
@php
    $visibleUsers = collect($users->items());
    $onlineCount = $visibleUsers->filter(fn ($listedUser) => $listedUser->isOnline())->count();
    $verifiedCount = $visibleUsers->where('ucheck', 1)->count();
    $activeFilters = [];

    if (request('search')) {
        $activeFilters[] = [
            'icon' => 'feather-search',
            'label' => __('messages.search_users'),
            'value' => request('search'),
        ];
    }

    if (request('role')) {
        $activeFilters[] = [
            'icon' => 'feather-shield',
            'label' => __('messages.Role'),
            'value' => request('role') === 'admin' ? __('messages.Admins') : __('messages.Members'),
        ];
    }

    if (request()->has('online') && request('online') !== null && request('online') !== '') {
        $activeFilters[] = [
            'icon' => 'feather-activity',
            'label' => __('messages.connection_status'),
            'value' => request('online') === '1' ? __('messages.online') : __('messages.offline'),
        ];
    }

    if (request()->has('verified') && request('verified') !== null && request('verified') !== '') {
        $activeFilters[] = [
            'icon' => 'feather-award',
            'label' => __('messages.Verification'),
            'value' => request('verified') === '1' ? __('messages.Verified') : __('messages.Unverified'),
        ];
    }

    if (request('sort') && request('sort') !== 'id') {
        $activeFilters[] = [
            'icon' => 'feather-arrow-up-right',
            'label' => __('messages.order'),
            'value' => strtoupper(request('sort')) . ' / ' . strtoupper(request('direction', 'desc')),
        ];
    }
@endphp

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.users') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.users') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.users') }} / {{ __('messages.connection_status') }} / {{ __('messages.Verification') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.users') }}</span>
                    <span class="admin-stat-value">{{ number_format($users->total()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.online') }}</span>
                    <span class="admin-stat-value">{{ number_format($onlineCount) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.Verified') }}</span>
                    <span class="admin-stat-value">{{ number_format($verifiedCount) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <form action="{{ route('admin.users') }}" method="GET" class="admin-toolbar-row flex-grow-1 w-100">
                    <div class="input-group flex-grow-1">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="{{ __('messages.search_users') }}"
                            value="{{ request('search') }}"
                        >
                    </div>

                    @foreach(['role', 'online', 'verified', 'sort', 'direction'] as $preserveKey)
                        @if(request()->has($preserveKey) && request($preserveKey) !== null && request($preserveKey) !== '')
                            <input type="hidden" name="{{ $preserveKey }}" value="{{ request($preserveKey) }}">
                        @endif
                    @endforeach

                    <button type="submit" class="btn btn-primary admin-icon-btn">
                        <i class="feather-search"></i>
                    </button>
                </form>

                <div class="dropdown ms-auto">
                    <a class="btn btn-light admin-icon-btn" data-bs-toggle="dropdown" data-bs-offset="0,12" data-bs-auto-close="outside">
                        <i class="feather-filter"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-2 admin-filter-dropdown-menu" style="min-width: 280px; max-width: min(92vw, 280px);">
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Role') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="dropdown-item {{ !request('role') ? 'active' : '' }}">
                            <i class="feather-users me-3"></i>
                            <span>{{ __('messages.All') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => 'admin']) }}" class="dropdown-item {{ request('role') == 'admin' ? 'active' : '' }}">
                            <i class="feather-shield me-3"></i>
                            <span>{{ __('messages.Admins') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => 'member']) }}" class="dropdown-item {{ request('role') == 'member' ? 'active' : '' }}">
                            <i class="feather-user me-3"></i>
                            <span>{{ __('messages.Members') }}</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.connection_status') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['online' => null]) }}" class="dropdown-item {{ !request()->has('online') || request('online') === null || request('online') === '' ? 'active' : '' }}">
                            <i class="feather-globe me-3"></i>
                            <span>{{ __('messages.all_status') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['online' => '1']) }}" class="dropdown-item {{ request('online') == '1' ? 'active' : '' }}">
                            <i class="feather-check-circle me-3 text-success"></i>
                            <span>{{ __('messages.online') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['online' => '0']) }}" class="dropdown-item {{ request('online') == '0' ? 'active' : '' }}">
                            <i class="feather-x-circle me-3 text-muted"></i>
                            <span>{{ __('messages.offline') }}</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Verification') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => null]) }}" class="dropdown-item {{ !request()->has('verified') || request('verified') === null || request('verified') === '' ? 'active' : '' }}">
                            <i class="feather-award me-3"></i>
                            <span>{{ __('messages.All') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => '1']) }}" class="dropdown-item {{ request('verified') == '1' ? 'active' : '' }}">
                            <i class="feather-check-square me-3 text-primary"></i>
                            <span>{{ __('messages.Verified') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => '0']) }}" class="dropdown-item {{ request('verified') == '0' ? 'active' : '' }}">
                            <i class="feather-square me-3 text-muted"></i>
                            <span>{{ __('messages.Unverified') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            @if(!empty($activeFilters))
                <div class="admin-filter-chip-list">
                    @foreach($activeFilters as $filter)
                        <span class="admin-filter-chip">
                            <i class="{{ $filter['icon'] }}"></i>
                            <span>{{ $filter['label'] }}: {{ $filter['value'] }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.users') }}</span>
                <h2 class="admin-panel__title">
                    @if($users->total() > 0)
                        {{ $users->firstItem() }}-{{ $users->lastItem() }} / {{ $users->total() }}
                    @else
                        0
                    @endif
                </h2>
            </div>
            <div class="admin-chip-list">
                @if(request('search'))
                    <span class="admin-chip"><i class="feather-search"></i>{{ request('search') }}</span>
                @endif
                <span class="admin-chip"><i class="feather-chevron-down"></i>{{ strtoupper(request('direction', 'desc')) }}</span>
                <span class="admin-chip"><i class="feather-sliders"></i>{{ strtoupper(request('sort', 'id')) }}</span>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify" id="usersList">
                    <thead>
                        <tr>
                            <th class="wd-30">
                                <div class="custom-control custom-checkbox ms-1">
                                    <input type="checkbox" class="custom-control-input" id="checkAllUsers">
                                    <label class="custom-control-label" for="checkAllUsers"></label>
                                </div>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => request('sort') === 'username' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-reset">
                                    {{ __('messages.User') }}
                                    @if(request('sort') === 'username')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'role', 'direction' => request('sort') === 'role' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-reset">
                                    {{ __('messages.Role') }}
                                    @if(request('sort') === 'role')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'online', 'direction' => request('sort') === 'online' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-reset">
                                    {{ __('messages.status') }}
                                    @if(request('sort') === 'online')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'pts', 'direction' => request('sort') === 'pts' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-reset">
                                    {{ __('messages.points') }}
                                    @if(request('sort') === 'pts')
                                        <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td data-label="#">
                                    <div class="custom-control custom-checkbox ms-1">
                                        <input type="checkbox" class="custom-control-input checkbox" id="checkBox_{{ $user->id }}">
                                        <label class="custom-control-label" for="checkBox_{{ $user->id }}"></label>
                                    </div>
                                </td>
                                <td data-label="{{ __('messages.User') }}">
                                    <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="admin-person">
                                        <span class="admin-person__avatar">
                                            <img src="{{ $user->img ? asset($user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $user->username }}">
                                        </span>
                                        <span class="admin-person__body">
                                            <span class="admin-person__name">
                                                {{ $user->username }}
                                                @if($user->ucheck == 1)
                                                    <i class="bi bi-patch-check-fill text-primary" title="{{ __('messages.Verified') }}"></i>
                                                @endif
                                            </span>
                                            <span class="admin-person__meta">
                                                <span>{{ $user->email }}</span>
                                                <span>#{{ $user->id }}</span>
                                            </span>
                                        </span>
                                    </a>
                                </td>
                                <td data-label="{{ __('messages.Role') }}">
                                    @if($user->isAdmin())
                                        <span class="badge bg-soft-primary text-primary">{{ __('messages.Admin') }}</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">{{ __('messages.Member') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.status') }}">
                                    <div class="admin-inline-meta">
                                        <span class="badge {{ $user->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                            {{ $user->isOnline() ? __('messages.online') : __('messages.offline') }}
                                        </span>
                                        <span>{{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td data-label="{{ __('messages.points') }}">
                                    <strong>{{ number_format((float) $user->pts, 2) }}</strong>
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <div class="admin-action-cluster">
                                        <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.Banners') }}">
                                            <i class="feather-link text-warning"></i>
                                        </a>
                                        <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.Links') }}">
                                            <i class="feather-eye text-success"></i>
                                        </a>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-light admin-icon-btn" data-bs-toggle="dropdown" data-bs-offset="0,12">
                                                <i class="feather-more-horizontal"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('profile.show', $user->username) }}" target="_blank">
                                                        <i class="feather-eye me-3"></i>
                                                        <span>{{ __('messages.view_profile') }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                        <i class="feather-edit-3 me-3"></i>
                                                        <span>{{ __('messages.edit_user') }}</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item text-danger user-delete-trigger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteUserModal"
                                                        data-action="{{ route('admin.users.delete', $user->id) }}"
                                                        data-user-name="{{ $user->username }}"
                                                    >
                                                        <i class="feather-trash-2 me-3"></i>
                                                        <span>{{ __('messages.delete_user') }}</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-users"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_user') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel__footer">
            <span class="admin-muted">{{ __('messages.users') }}: {{ $users->total() }}</span>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="admin-modal-icon is-danger">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.are_you_sure') }}</h4>
                <p class="text-muted mb-0">
                    {{ __('messages.User') }}:
                    <strong id="deleteUserModalText"></strong>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="" method="POST" id="deleteUserModalForm" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAllUsers');
        const checkboxes = document.querySelectorAll('.checkbox');
        const deleteModal = document.getElementById('deleteUserModal');
        const deleteForm = document.getElementById('deleteUserModalForm');
        const deleteText = document.getElementById('deleteUserModalText');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = checkAll.checked;
                });
            });
        }

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const username = trigger.getAttribute('data-user-name') || '';
                const action = trigger.getAttribute('data-action') || '';

                deleteForm.setAttribute('action', action);
                deleteText.textContent = username;
            });
        }
    });
</script>
@endpush
