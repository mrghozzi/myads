@extends('theme::layouts.admin')

@section('title', __('messages.bannads'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.bannads') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.bannads') }}</li>
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
                <a href="{{ route('ads.banners.code') }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                    <i class="feather-code"></i>
                </a>
                <a href="{{ route('admin.stats', ['ty' => 'banner', 'st' => 'vu']) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                    <i class="feather-bar-chart-2"></i>
                </a>
                
                <div class="dropdown">
                    <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 12" data-bs-auto-close="outside">
                        <i class="feather-filter"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                        <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.Filter') }}</div>
                        <a href="{{ route('admin.banners') }}" class="dropdown-item {{ !request('user_id') ? 'active' : '' }}" {{ !request('user_id') ? 'aria-current="true"' : '' }}>
                            <i class="feather-list me-3"></i>
                            <span>{{ __('messages.all_banners') ?? 'All Banners' }}</span>
                        </a>
                        @if(request('user_id'))
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header fw-bold text-uppercase fs-11 text-muted">{{ __('messages.current_user') ?? 'Current User' }}</div>
                            <div class="dropdown-item active">
                                <i class="feather-user me-3"></i>
                                <span>{{ __('messages.user_id_label') ?? 'User ID:' }} {{ request('user_id') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>{{ __('messages.add') }}</span>
                </a>
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
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.Vu') }}</th>
                                    <th>{{ __('messages.Clik') }}</th>
                                    <th>{{ __('messages.size') }}</th>
                                    <th>{{ __('messages.Statu') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($banners as $banner)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ $banner->id }}</span>
                                    </td>
                                    <td>
                                        <div class="hstack gap-3">
                                            @if($banner->img)
                                                <div class="avatar-image avatar-md">
                                                    <img src="{{ $banner->img }}" alt="" class="img-fluid">
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-truncate-1-line fw-bold text-dark">{{ Str::limit($banner->name, 25) }}</span>
                                                <div class="small text-muted">
                                                    @if($banner->user)
                                                        <a href="{{ route('profile.show', $banner->user->username) }}" target="_blank" class="text-muted">{{ $banner->user->username }}</a>
                                                    @else
                                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown User' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.stats', ['ty' => 'banner', 'id' => $banner->id]) }}" class="badge bg-soft-warning text-warning">{{ $banner->vu }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.stats', ['ty' => 'vu', 'id' => $banner->id]) }}" class="badge bg-soft-primary text-primary">{{ $banner->clik }}</a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $banner->px }}</span></td>
                                    <td>
                                        @if($banner->statu == 1)
                                            <span class="badge bg-soft-success text-success">ON</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">OFF</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="avatar-text avatar-md bg-soft-success text-success">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $banner->id }}">
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
                    {{ $banners->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
@foreach($banners as $banner)
<div class="modal fade" id="deleteModal{{ $banner->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.sure_to_delete') }} #{{ $banner->id }}?</h4>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="{{ route('admin.banners.delete', $banner->id) }}" method="POST" class="d-inline">
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
