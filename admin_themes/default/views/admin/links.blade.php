@extends('admin::layouts.admin')

@section('title', __('messages.textads'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.textads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.textads') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.codes') }} / {{ __('messages.Stats') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('ads.links.code') }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                        <i class="feather-code"></i>
                    </a>
                    <a href="{{ route('admin.stats', ['ty' => 'clik']) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                        <i class="feather-bar-chart-2"></i>
                    </a>
                    @include('admin::admin.partials.inventory_filter_dropdown', [
                        'action' => route('admin.links'),
                        'resetUrl' => route('admin.links', ['reset_filters' => 1]),
                        'preferenceKey' => 'links',
                        'filterState' => $filterState,
                        'filterFields' => $filterFields,
                        'resultsCount' => $resultsCount,
                    ])
                    <a href="{{ route('ads.links.create') }}" class="btn btn-primary ms-auto">
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
                    <h5 class="card-title mb-0">{{ __('messages.textads') }}</h5>
                    <span class="badge bg-soft-primary text-primary">{{ __('messages.results_count', ['count' => $resultsCount]) }}</span>
                </div>
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
