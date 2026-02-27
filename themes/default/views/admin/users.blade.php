@extends('theme::layouts.admin')

@section('title', __('messages.users'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.users') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.users') }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex d-md-none">
                <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>{{ __('messages.back') ?? 'Back' }}</span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <form action="{{ route('admin.users') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('messages.search_users') ?? 'Search users...' }}" value="{{ request('search') }}">
                    </div>
                </form>

                <div class="dropdown">
                    <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 12" data-bs-auto-close="outside">
                        <i class="feather-filter"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                        <!-- Role Filter Section -->
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Role') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="dropdown-item {{ !request('role') ? 'active' : '' }}" {{ !request('role') ? 'aria-current="true"' : '' }}>
                            <i class="feather-users me-3" aria-hidden="true"></i>
                            <span>{{ __('messages.all_roles') ?? 'All Roles' }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => 'admin']) }}" class="dropdown-item {{ request('role') == 'admin' ? 'active' : '' }}" {{ request('role') == 'admin' ? 'aria-current="true"' : '' }}>
                            <i class="feather-shield me-3" aria-hidden="true"></i>
                            <span>{{ __('messages.Admins') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => 'member']) }}" class="dropdown-item {{ request('role') == 'member' ? 'active' : '' }}" {{ request('role') == 'member' ? 'aria-current="true"' : '' }}>
                            <i class="feather-user me-3" aria-hidden="true"></i>
                            <span>{{ __('messages.Members') }}</span>
                        </a>

                        <div class="dropdown-divider"></div>
                        
                        <!-- Online Status Filter Section -->
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.connection_status') ?? 'Connection Status' }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['online' => null]) }}" class="dropdown-item {{ !request('online') ? 'active' : '' }}" {{ !request('online') ? 'aria-current="true"' : '' }}>
                            <i class="feather-globe me-3" aria-hidden="true"></i>
                            <span>{{ __('messages.all_status') ?? 'All Status' }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['online' => '1']) }}" class="dropdown-item {{ request('online') == '1' ? 'active' : '' }}" {{ request('online') == '1' ? 'aria-current="true"' : '' }}>
                            <i class="feather-check-circle me-3 text-success" aria-hidden="true"></i>
                            <span>{{ __('messages.online') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['online' => '0']) }}" class="dropdown-item {{ request('online') == '0' ? 'active' : '' }}" {{ request('online') == '0' ? 'aria-current="true"' : '' }}>
                            <i class="feather-x-circle me-3 text-muted" aria-hidden="true"></i>
                            <span>{{ __('messages.Offline') }}</span>
                        </a>

                        <div class="dropdown-divider"></div>
                        
                        <!-- Verification Status Filter Section -->
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Verification') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => null]) }}" class="dropdown-item {{ !request('verified') ? 'active' : '' }}" {{ !request('verified') ? 'aria-current="true"' : '' }}>
                            <i class="feather-award me-3" aria-hidden="true"></i>
                            <span>{{ __('messages.All') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => '1']) }}" class="dropdown-item {{ request('verified') == '1' ? 'active' : '' }}" {{ request('verified') == '1' ? 'aria-current="true"' : '' }}>
                            <i class="feather-check-square me-3 text-primary" aria-hidden="true"></i>
                            <span>{{ __('messages.Verified') }}</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['verified' => '0']) }}" class="dropdown-item {{ request('verified') == '0' ? 'active' : '' }}" {{ request('verified') == '0' ? 'aria-current="true"' : '' }}>
                            <i class="feather-square me-3 text-muted" aria-hidden="true"></i>
                            <span>{{ __('messages.Unverified') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-md-none d-flex align-items-center">
            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersList">
                            <thead>
                                <tr>
                                    <th class="wd-30">
                                        <div class="custom-control custom-checkbox ms-1">
                                            <input type="checkbox" class="custom-control-input" id="checkAllUsers">
                                            <label class="custom-control-label" for="checkAllUsers"></label>
                                        </div>
                                    </th>
                                    <th>{{ __('messages.User') }}</th>
                                    <th>{{ __('messages.Role') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.Points') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox ms-1">
                                            <input type="checkbox" class="custom-control-input checkbox" id="checkBox_{{ $user->id }}">
                                            <label class="custom-control-label" for="checkBox_{{ $user->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="hstack gap-3">
                                            <div class="avatar-image avatar-md">
                                                <img src="{{ $user->img ? asset($user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                            </div>
                                            <div>
                                                <span class="text-truncate-1-line fw-bold text-dark">{{ $user->username }}</span>
                                                <small class="fs-12 fw-normal text-muted">{{ $user->email }}</small>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        @if($user->ucheck == 1)
                                            <span class="badge bg-soft-primary text-primary">{{ __('messages.Admin') }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">{{ __('messages.Member') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $user->isOnline() ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                                {{ $user->isOnline() ? __('messages.online') : __('messages.Offline') }}
                                            </span>
                                            <small class="text-muted">{{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold text-dark">{{ $user->pts }}</span></td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="avatar-text avatar-md bg-soft-warning text-warning" title="{{ __('messages.Banners') }}">
                                                <i class="feather-link"></i>
                                            </a>
                                            <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="avatar-text avatar-md bg-soft-success text-success" title="{{ __('messages.Links') }}">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,21">
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
                                                            <span>{{ __('messages.edit_user') ?? 'Edit User' }}</span>
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                                            <i class="feather-trash-2 me-3"></i>
                                                            <span>{{ __('messages.delete_user') ?? 'Delete User' }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAllUsers');
        const checkboxes = document.querySelectorAll('.checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
            });
        }
    });
</script>
@endsection

@section('modals')
<!-- Delete User Modals -->
@foreach($users as $user)
<div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_user') ?? 'Delete User' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.are_you_sure') }}</h4>
                <p class="text-muted">{{ __('messages.confirm_delete_user', ['user' => $user->username]) ?? "Do you really want to delete user '$user->username'?" }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
