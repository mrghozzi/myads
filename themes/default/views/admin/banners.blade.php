@extends('theme::layouts.admin')

@section('title', __('messages.bannads'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.bannads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.bannads') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.codes') }} / {{ __('messages.Stats') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('ads.banners.code') }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                        <i class="feather-code"></i>
                    </a>
                    <a href="{{ route('admin.stats', ['ty' => 'banner', 'st' => 'vu']) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                        <i class="feather-bar-chart-2"></i>
                    </a>
                    @include('theme::admin.partials.inventory_filter_dropdown', [
                        'action' => route('admin.banners'),
                        'resetUrl' => route('admin.banners', ['reset_filters' => 1]),
                        'preferenceKey' => 'banners',
                        'filterState' => $filterState,
                        'filterFields' => $filterFields,
                        'resultsCount' => $resultsCount,
                    ])
                    <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="btn btn-primary ms-auto">
                        <i class="feather-plus me-2"></i>
                        <span>{{ __('messages.add') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('messages.bannads') }}</h5>
                    <span class="badge bg-soft-primary text-primary">{{ __('messages.results_count', ['count' => $resultsCount]) }}</span>
                </div>
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
