@extends('theme::layouts.admin')

@section('title', __('messages.links'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.links') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.links') }}</li>
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
                
                <div class="dropdown">
                    <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 12" data-bs-auto-close="outside">
                        <i class="feather-filter"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Filter') }}</div>
                        <a href="{{ route('admin.links') }}" class="dropdown-item {{ !request('user_id') ? 'active' : '' }}" {{ !request('user_id') ? 'aria-current="true"' : '' }}>
                            <i class="feather-list me-3"></i>
                            <span>{{ __('messages.all_links') ?? 'All Links' }}</span>
                        </a>
                        @if(request('user_id'))
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.current_user') ?? 'Current User' }}</div>
                            <div class="dropdown-item active">
                                <i class="feather-user me-3"></i>
                                <span>User ID: {{ request('user_id') }}</span>
                            </div>
                        @endif
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
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.clicks') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($links as $link)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ $link->id }}</span>
                                    </td>
                                    <td>
                                        @if($link->user)
                                            <a href="{{ route('profile.show', $link->user->username) }}" target="_blank" class="hstack gap-3">
                                                <div class="avatar-image avatar-md">
                                                    <img src="{{ $link->user->img ? asset($link->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                                </div>
                                                <div>
                                                    <span class="text-truncate-1-line fw-bold text-dark">{{ $link->user->username }}</span>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $link->name }}</div>
                                        <div class="small">
                                            <a href="{{ $link->url }}" target="_blank" class="text-primary">{{ Str::limit($link->url, 30) }}</a>
                                        </div>
                                        <div class="small text-muted">{{ Str::limit($link->txt, 50) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">{{ $link->clik }}</span>
                                    </td>
                                    <td>
                                        @if($link->statu == 1)
                                            <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-success text-success" data-bs-toggle="modal" data-bs-target="#editModal{{ $link->id }}">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $link->id }}">
                                                <i class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $links->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
@foreach($links as $link)
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_link') }} #{{ $link->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.links.update', $link->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" value="{{ $link->name }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.url') }}</label>
                        <input type="text" name="url" value="{{ $link->url }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') }}</label>
                        <textarea name="txt" rows="3" class="form-control">{{ $link->txt }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="statu" class="form-select">
                            <option value="1" {{ $link->statu == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="0" {{ $link->statu == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal{{ $link->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_link') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_link') }}</h4>
                <p class="text-muted">#{{ $link->id }} - {{ $link->name }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.links.delete', $link->id) }}" method="POST" class="d-inline">
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
