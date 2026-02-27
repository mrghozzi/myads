@extends('theme::layouts.admin')

@section('title', __('messages.directory_categories'))

@section('content')
<!-- Header -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.directory_categories') }}</h2>
        <p class="text-muted">{{ __('messages.directory_categories_desc') ?? 'Manage your directory categories here.' }}</p>
        <div class="mt-4">
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="feather-plus me-2"></i> {{ __('messages.add_category') }}
            </button>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.parent_category') }}</th>
                            <th>{{ __('messages.order') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $category->name }}</div>
                                @if($category->txt)
                                <small class="text-muted">{{ Str::limit($category->txt, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($category->sub == 0)
                                    <span class="badge bg-light-primary text-primary">{{ __('messages.main_category') }}</span>
                                @else
                                    {{ $category->parent->name ?? __('messages.unknown') }}
                                @endif
                            </td>
                            <td>{{ $category->ordercat }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                    <i class="feather-edit-3"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.new_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.directory_categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="statu" value="1">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.parent_category') }}</label>
                        <select name="sub" class="form-select">
                            <option value="0">{{ __('messages.main_category') }}</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.meta_keywords') }}</label>
                        <textarea name="metakeywords" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.order') }}</label>
                        <input type="number" name="ordercat" class="form-control" value="0" required>
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

@foreach($categories as $category)
<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.directory_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.parent_category') }}</label>
                        <select name="sub" class="form-select">
                            <option value="0" {{ $category->sub == 0 ? 'selected' : '' }}>{{ __('messages.main_category') }}</option>
                            @foreach($parents as $parent)
                                @if($parent->id != $category->id) {{-- Prevent selecting itself as parent --}}
                                <option value="{{ $parent->id }}" {{ $category->sub == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control" rows="2">{{ $category->txt }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.meta_keywords') }}</label>
                        <textarea name="metakeywords" class="form-control" rows="2">{{ $category->metakeywords }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.order') }}</label>
                        <input type="number" name="ordercat" class="form-control" value="{{ $category->ordercat }}" required>
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

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_category') }}</h4>
                <p class="text-muted">{{ $category->name }}</p>
                @if($category->children()->count() > 0)
                <div class="alert alert-warning mt-3">
                    <i class="feather-alert-triangle me-2"></i>
                    {{ __('messages.warning_has_subcategories') }}
                </div>
                @endif
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.directory_categories.delete', $category->id) }}" method="POST" class="d-inline">
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
