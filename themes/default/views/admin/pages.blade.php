@extends('theme::layouts.admin')

@section('title', __('messages.pages'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.pages') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.pages') }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i>{{ __('messages.add_page') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.t_pages') }}</h5>
    </div>
    <div class="card-body">
        @if($pages->isEmpty())
            <div class="text-center py-5">
                <i class="feather-file-text" style="font-size: 48px; color: #ccc;"></i>
                <p class="mt-3 text-muted">{{ __('messages.no_page') }}</p>
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary mt-2">
                    <i class="feather-plus me-1"></i>{{ __('messages.add_page') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>{{ __('messages.title') }}</th>
                            <th>{{ __('messages.page_slug') ?? 'Slug' }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.order') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                            <tr>
                                <td><span class="fw-semibold">#{{ $page->id }}</span></td>
                                <td>
                                    <a href="{{ $page->getUrl() }}" target="_blank" class="text-decoration-none">
                                        {{ $page->title }}
                                        <i class="feather-external-link ms-1" style="font-size: 12px;"></i>
                                    </a>
                                </td>
                                <td><code>/page/{{ $page->slug }}</code></td>
                                <td>
                                    @if($page->status === 'published')
                                        <span class="badge bg-soft-success text-success">{{ __('messages.published') ?? 'Published' }}</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">{{ __('messages.draft') ?? 'Draft' }}</span>
                                    @endif
                                </td>
                                <td>{{ $page->order }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                        <i class="feather-edit-2 me-1"></i>{{ __('messages.edit') }}
                                    </a>
                                    <form action="{{ route('admin.pages.delete', $page->id) }}" method="POST" class="d-inline-block ms-2" onsubmit="return confirm('{{ __('messages.confirm_delete_page') ?? 'Are you sure?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="feather-trash-2 me-1"></i>{{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
