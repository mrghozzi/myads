@extends('theme::layouts.admin')

@section('title', __('messages.pages'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.pages') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.pages') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.t_pages') }} / {{ __('messages.add_page') }}</p>
            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.pages') }}</span>
                    <span class="admin-stat-value">{{ number_format($pages->count()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.published') ?? 'Published' }}</span>
                    <span class="admin-stat-value">{{ number_format($pages->where('status', 'published')->count()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.draft') ?? 'Draft' }}</span>
                    <span class="admin-stat-value">{{ number_format($pages->where('status', '!=', 'published')->count()) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary w-100">
                    <i class="feather-plus me-2"></i>{{ __('messages.add_page') }}
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.t_pages') }}</span>
                <h2 class="admin-panel__title">{{ number_format($pages->count()) }}</h2>
            </div>
        </div>

        <div class="admin-panel__body">
            @if($pages->isEmpty())
                <div class="admin-empty-state text-center">
                    <div class="admin-modal-icon mx-auto mb-3">
                        <i class="feather-file-text"></i>
                    </div>
                    <h3 class="h5 mb-2">{{ __('messages.no_page') }}</h3>
                    <p class="text-muted mb-3">{{ __('messages.add_page') }}</p>
                    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>{{ __('messages.add_page') }}
                    </a>
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify admin-density-table">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td data-label="ID"><span class="fw-semibold">#{{ $page->id }}</span></td>
                                    <td data-label="{{ __('messages.title') }}">
                                        <a href="{{ $page->getUrl() }}" target="_blank" class="text-decoration-none fw-semibold">
                                            {{ $page->title }}
                                            <i class="feather-external-link ms-1 small"></i>
                                        </a>
                                    </td>
                                    <td data-label="{{ __('messages.page_slug') ?? 'Slug' }}"><code>/page/{{ $page->slug }}</code></td>
                                    <td data-label="{{ __('messages.status') }}">
                                        @if($page->status === 'published')
                                            <span class="badge bg-soft-success text-success">{{ __('messages.published') ?? 'Published' }}</span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning">{{ __('messages.draft') ?? 'Draft' }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.order') }}">{{ $page->order }}</td>
                                    <td data-label="{{ __('messages.actions') }}">
                                        <div class="admin-action-cluster justify-content-end">
                                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-edit-2 me-1"></i>{{ __('messages.edit') }}
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger js-page-delete"
                                                data-delete-action="{{ route('admin.pages.delete', $page->id) }}"
                                                data-page-title="{{ $page->title }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#pageDeleteModal"
                                            >
                                                <i class="feather-trash-2 me-1"></i>{{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="pageDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mx-auto mb-3">
                    <i class="feather-trash-2"></i>
                </div>
                <h3 class="h5 mb-2">{{ __('messages.delete') }}</h3>
                <p class="text-muted mb-0" id="pageDeleteLabel">{{ __('messages.confirm_delete_page') ?? 'Are you sure?' }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="" method="POST" id="pageDeleteForm">
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
    const deleteForm = document.getElementById('pageDeleteForm');
    const deleteLabel = document.getElementById('pageDeleteLabel');

    document.querySelectorAll('.js-page-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            if (deleteForm) {
                deleteForm.action = this.dataset.deleteAction;
            }

            if (deleteLabel) {
                deleteLabel.textContent = "{{ __('messages.confirm_delete_page') ?? 'Are you sure?' }}" + ' ' + (this.dataset.pageTitle || '');
            }
        });
    });
});
</script>
@endpush
