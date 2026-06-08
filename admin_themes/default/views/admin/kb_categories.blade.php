@extends('admin::layouts.admin')

@section('title', __('messages.kb_manage_categories'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="main-content container-lg px-4">
    <div class="row g-0 align-items-center border-bottom mb-5 pb-4">
        <div class="col-lg-8">
            <h2 class="fw-bolder mb-1 text-dark">{{ __('messages.kb_manage_categories') }}</h2>
            <p class="text-muted mb-0">{{ __('messages.kb_manage_categories_desc') }}</p>
        </div>
        <div class="col-lg-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="feather-plus me-2"></i> {{ __('messages.add') }}
            </button>
            <a href="{{ route('admin.knowledgebase') }}" class="btn btn-outline-secondary ms-2">
                <i class="feather-book-open me-1"></i> {{ __('messages.knowledgebase') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($categories->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="wd-60 ht-60 d-flex align-items-center justify-content-center mb-3 mx-auto bg-soft-primary rounded-circle">
                    <i class="feather-folder fs-24 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('messages.kb_no_categories') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.kb_no_categories_desc') }}</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="feather-plus me-1"></i> {{ __('messages.add') }}
                </button>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th class="text-center">{{ __('messages.topics') }}</th>
                                <th class="text-center">{{ __('messages.sort') }}</th>
                                <th class="text-end pe-4">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $category->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="wd-32 ht-32 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                                <i class="feather-folder fs-14 text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $category->name }}</span>
                                                <br><small class="text-muted">{{ $category->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($category->description, 60) ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary">{{ $category->articles_count }}</span>
                                    </td>
                                    <td class="text-center text-muted">{{ $category->sort_order }}</td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                            <i class="feather-edit-3"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-light-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('modals')
{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.add') }} {{ __('messages.kb_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kb_categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
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

{{-- Edit & Delete Modals --}}
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit') }} — {{ $category->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kb_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="500">{{ $category->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $category->sort_order }}" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete') }} — {{ $category->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.kb_confirm_delete_category') }}</h4>
                <p class="text-muted">{{ $category->name }} ({{ $category->articles_count }} {{ __('messages.topics') }})</p>
                <p class="text-muted small">{{ __('messages.kb_delete_category_note') }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.kb_categories.delete', $category->id) }}" method="POST" class="d-inline">
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
