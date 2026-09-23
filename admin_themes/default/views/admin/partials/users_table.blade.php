@php
    $currentSort = request('sort', 'id');
    $currentDirection = strtolower(request('direction', 'desc'));
    $nextDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
    $authId = (int) auth()->id();
@endphp

<div class="admin-table-wrap">
    <table class="table table-hover align-middle admin-table admin-table-cardify mb-0" id="usersListTable">
        <thead>
            <tr>
                <th class="wd-35 text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkAllUsers">
                        <label class="custom-control-label" for="checkAllUsers"></label>
                    </div>
                </th>
                <th>
                    <a href="javascript:void(0)" class="text-reset table-sort-link d-inline-flex align-items-center gap-1" data-sort="username">
                        <span>{{ __('messages.User') }}</span>
                        @if($currentSort === 'username')
                            <i class="feather-arrow-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fs-11 text-primary"></i>
                        @else
                            <i class="feather-chevrons-up text-muted opacity-50 fs-11"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="javascript:void(0)" class="text-reset table-sort-link d-inline-flex align-items-center gap-1" data-sort="role">
                        <span>{{ __('messages.Role') }}</span>
                        @if($currentSort === 'role')
                            <i class="feather-arrow-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fs-11 text-primary"></i>
                        @else
                            <i class="feather-chevrons-up text-muted opacity-50 fs-11"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="javascript:void(0)" class="text-reset table-sort-link d-inline-flex align-items-center gap-1" data-sort="online">
                        <span>{{ __('messages.status') }}</span>
                        @if($currentSort === 'online')
                            <i class="feather-arrow-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fs-11 text-primary"></i>
                        @else
                            <i class="feather-chevrons-up text-muted opacity-50 fs-11"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">
                    <span>{{ __('messages.Verification') }}</span>
                </th>
                <th>
                    <a href="javascript:void(0)" class="text-reset table-sort-link d-inline-flex align-items-center gap-1" data-sort="pts">
                        <span>{{ __('messages.points') }}</span>
                        @if($currentSort === 'pts')
                            <i class="feather-arrow-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fs-11 text-primary"></i>
                        @else
                            <i class="feather-chevrons-up text-muted opacity-50 fs-11"></i>
                        @endif
                    </a>
                </th>
                <th class="text-end pe-3">{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @forelse($users as $user)
                @php
                    $isSuper = (int)$user->id === 1;
                    $isSelf = (int)$user->id === $authId;
                    $canDelete = !$isSuper && !$isSelf;
                @endphp
                <tr id="user-row-{{ $user->id }}" class="user-row align-middle" data-user-id="{{ $user->id }}">
                    <td data-label="#" class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input 
                                type="checkbox" 
                                class="custom-control-input user-item-checkbox" 
                                id="user_check_{{ $user->id }}" 
                                value="{{ $user->id }}" 
                                {{ !$canDelete ? 'disabled' : '' }}
                            >
                            <label class="custom-control-label" for="user_check_{{ $user->id }}"></label>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.User') }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative flex-shrink-0">
                                <img 
                                    src="{{ $user->avatarUrl() }}" 
                                    alt="{{ $user->username }}" 
                                    class="rounded-circle border" 
                                    width="42" 
                                    height="42"
                                    style="object-fit: cover;"
                                    loading="lazy"
                                >
                                <span class="position-absolute bottom-0 end-0 p-1 {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle" style="width: 10px; height: 10px;" title="{{ $user->isOnline() ? __('messages.online') : __('messages.offline') }}"></span>
                            </div>
                            <div class="overflow-hidden min-w-0">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="fw-bold text-reset text-truncate admin-user-link">
                                        {{ $user->username }}
                                    </a>
                                    @if($user->ucheck == 1)
                                        <i class="bi bi-patch-check-fill text-primary ms-1" title="{{ __('messages.Verified') }}"></i>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 fs-12 text-muted mt-1">
                                    <span class="text-truncate"><i class="feather-mail me-1 fs-11"></i>{{ $user->email }}</span>
                                    <span class="badge bg-light text-muted border py-0 px-1 font-monospace">#{{ $user->id }}</span>
                                    @if(!empty($user->public_uid))
                                        <span class="badge bg-soft-info text-info border-0 py-0 px-1 font-monospace">{{ $user->public_uid }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.Role') }}">
                        @if($user->isSuperAdmin())
                            <span class="badge bg-soft-danger text-danger border border-danger-subtle px-2 py-1">
                                <i class="feather-shield me-1"></i>{{ __('messages.super_admins') ?? 'Super Admin' }}
                            </span>
                        @elseif($user->isAdmin())
                            <span class="badge bg-soft-primary text-primary border border-primary-subtle px-2 py-1">
                                <i class="feather-user-check me-1"></i>{{ __('messages.Admin') }}
                            </span>
                        @else
                            <span class="badge bg-soft-secondary text-secondary px-2 py-1">
                                <i class="feather-user me-1"></i>{{ __('messages.Member') }}
                            </span>
                        @endif
                    </td>
                    <td data-label="{{ __('messages.status') }}">
                        <div class="d-inline-flex flex-column gap-1">
                            <span class="badge {{ $user->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-secondary text-muted' }} px-2 py-1">
                                <i class="feather-{{ $user->isOnline() ? 'activity' : 'moon' }} me-1"></i>
                                {{ $user->isOnline() ? __('messages.online') : __('messages.offline') }}
                            </span>
                            @if($user->online)
                                <small class="text-muted fs-11">
                                    {{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}
                                </small>
                            @endif
                        </div>
                    </td>
                    <td data-label="{{ __('messages.Verification') }}" class="text-center">
                        <button 
                            type="button" 
                            class="btn btn-sm btn-verify-toggle {{ $user->ucheck == 1 ? 'btn-light-success text-success' : 'btn-light text-muted' }} rounded-pill px-2 py-1 border-0 shadow-none transition-all"
                            data-user-id="{{ $user->id }}"
                            title="{{ $user->ucheck == 1 ? __('messages.Verified') . ' (Click to toggle)' : __('messages.Unverified') . ' (Click to toggle)' }}"
                        >
                            <i class="feather-{{ $user->ucheck == 1 ? 'check-circle' : 'circle' }} me-1"></i>
                            <span class="verify-label">{{ $user->ucheck == 1 ? __('messages.Verified') : __('messages.Unverified') }}</span>
                        </button>
                    </td>
                    <td data-label="{{ __('messages.points') }}">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <div class="fw-bold fs-6 text-primary user-pts-display">{{ number_format((float) $user->pts, 2) }} <span class="fs-11 text-muted fw-normal">PTS</span></div>
                                <div class="fs-11 text-muted">VU: {{ number_format((float)$user->vu, 0) }} &bull; NVU: {{ number_format((float)$user->nvu, 0) }}</div>
                            </div>
                            <button 
                                type="button" 
                                class="btn btn-sm btn-light-primary rounded-circle p-1 border-0 trigger-quick-balance"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->username }}"
                                data-pts="{{ (float) $user->pts }}"
                                data-vu="{{ (float) $user->vu }}"
                                data-nvu="{{ (float) $user->nvu }}"
                                data-nlink="{{ (float) $user->nlink }}"
                                data-nsmart="{{ (float) $user->nsmart }}"
                                title="{{ __('messages.quick_balance_adjust') }}"
                            >
                                <i class="feather-plus-circle fs-12"></i>
                            </button>
                        </div>
                    </td>
                    <td data-label="{{ __('messages.actions') }}" class="text-end pe-3">
                        <div class="admin-action-cluster justify-content-end">
                            <!-- Quick View Dossier Modal Trigger -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-light admin-icon-btn trigger-quick-view" 
                                data-user-id="{{ $user->id }}" 
                                title="{{ __('messages.quick_view') }}"
                            >
                                <i class="feather-eye text-primary"></i>
                            </button>

                            <!-- Direct Link to Edit User -->
                            <a 
                                href="{{ route('admin.users.edit', $user->id) }}" 
                                class="btn btn-sm btn-light admin-icon-btn" 
                                title="{{ __('messages.edit_user') }}"
                            >
                                <i class="feather-edit-3 text-info"></i>
                            </a>

                            <!-- Action Dropdown -->
                            <div class="dropdown">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-light admin-icon-btn" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-offset="0,8"
                                    aria-expanded="false"
                                >
                                    <i class="feather-more-horizontal"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border p-1" style="min-width: 200px;">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('profile.show', $user->username) }}" target="_blank">
                                            <i class="feather-external-link me-2 text-muted"></i>
                                            <span>{{ __('messages.view_profile') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item py-2 trigger-quick-password"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->username }}"
                                        >
                                            <i class="feather-key me-2 text-warning"></i>
                                            <span>{{ __('messages.change_password') }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item py-2 trigger-quick-notify"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->username }}"
                                        >
                                            <i class="feather-bell me-2 text-info"></i>
                                            <span>{{ __('messages.send_notification') }}</span>
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li class="dropdown-header fs-11 text-uppercase text-muted py-1">{{ __('messages.content') ?? 'Content' }}</li>
                                    <li>
                                        <a class="dropdown-item py-1 fs-13" href="{{ route('admin.banners', ['user_id' => $user->id]) }}">
                                            <i class="feather-image me-2 text-muted"></i>
                                            <span>{{ __('messages.Banners') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1 fs-13" href="{{ route('admin.links', ['user_id' => $user->id]) }}">
                                            <i class="feather-link me-2 text-muted"></i>
                                            <span>{{ __('messages.Links') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1 fs-13" href="{{ route('admin.smart_ads', ['user_id' => $user->id]) }}">
                                            <i class="feather-target me-2 text-muted"></i>
                                            <span>{{ __('messages.smart_ads') }}</span>
                                        </a>
                                    </li>
                                    @if($canDelete)
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button
                                                type="button"
                                                class="dropdown-item py-2 text-danger trigger-single-delete"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->username }}"
                                                data-action="{{ route('admin.users.delete', $user->id) }}"
                                            >
                                                <i class="feather-trash-2 me-2"></i>
                                                <span>{{ __('messages.delete_user') }}</span>
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-0">
                        <div class="admin-empty-state text-center py-5">
                            <div class="admin-avatar-circle mx-auto mb-3 bg-soft-primary text-primary" style="width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="feather-users fs-2"></i>
                            </div>
                            <h4 class="fw-bold mb-2">{{ __('messages.no_users_found_matching') }}</h4>
                            <p class="text-muted small mb-3">{{ __('messages.no_user') }}</p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="resetFiltersBtn">
                                <i class="feather-refresh-cw me-1"></i>{{ __('messages.reset_filters') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-panel__footer d-flex flex-wrap align-items-center justify-content-between p-3 border-top gap-3">
    <div class="text-muted small">
        @if($users->total() > 0)
            {{ __('messages.showing') ?? 'Showing' }} <strong>{{ $users->firstItem() }}</strong> - <strong>{{ $users->lastItem() }}</strong> {{ __('messages.of') ?? 'of' }} <strong>{{ number_format($users->total()) }}</strong> {{ __('messages.users') }}
        @else
            0 {{ __('messages.users') }}
        @endif
    </div>
    <div class="admin-pagination-container">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
