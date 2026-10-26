@extends('admin::layouts.admin')

@section('title', __('support.admin_manage_categories'))

@section('content')
<div class="main-content container-lg px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light border text-muted">
                    <i class="feather-arrow-right"></i> {{ __('support.admin_support_management') }}
                </a>
            </div>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="feather-folder text-primary me-2"></i> {{ __('support.admin_manage_categories') }}
            </h3>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="feather-plus-circle me-1"></i> {{ __('support.add_category') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">القسم</th>
                        <th>الرابط (Slug)</th>
                        <th>عدد المواضيع</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end pe-4">{{ __('support.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 p-2 text-white" style="background: {{ $cat->color ?: '#615dfa' }}; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $cat->icon }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $cat->name }}</div>
                                        <div class="text-muted small">{{ Str::limit($cat->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code>{{ $cat->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $cat->topics_count }} سؤال
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-muted">{{ $cat->sort_order }}</span>
                            </td>
                            <td>
                                @if($cat->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">معطل</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $cat->id }}" title="تعديل">
                                        <i class="feather-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.support.forum.categories.delete', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade text-start" id="editModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.support.forum.categories.update', $cat->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold">{{ __('support.edit_category') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('support.category_name') }}</label>
                                                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('support.category_slug') }}</label>
                                                        <input type="text" name="slug" value="{{ $cat->slug }}" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('support.category_desc') }}</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $cat->description }}</textarea>
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label fw-bold">{{ __('support.category_icon') }}</label>
                                                            <input type="text" name="icon" value="{{ $cat->icon }}" class="form-control">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-bold">{{ __('support.category_color') }}</label>
                                                            <input type="color" name="color" value="{{ $cat->color ?: '#615dfa' }}" class="form-control form-control-color w-100">
                                                        </div>
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label fw-bold">{{ __('support.category_order') }}</label>
                                                            <input type="number" name="sort_order" value="{{ $cat->sort_order }}" class="form-control">
                                                        </div>
                                                        <div class="col-6 d-flex align-items-end">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $cat->id }}" {{ $cat->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold" for="active{{ $cat->id }}">{{ __('support.category_active') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary fw-bold">حفظ التعديلات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                لا توجد أقسام منتدى دعم مضافة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('admin.support.forum.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ __('support.add_category') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('support.category_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="مثال: مشاكل الدفع والسحب" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('support.category_slug') }}</label>
                            <input type="text" name="slug" class="form-control" placeholder="billing-issues (اختياري)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('support.category_desc') }}</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="وصف موجز لنوع الاستفسارات في هذا القسم"></textarea>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">{{ __('support.category_icon') }}</label>
                                <input type="text" name="icon" class="form-control" value="fa-solid fa-circle-question">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">{{ __('support.category_color') }}</label>
                                <input type="color" name="color" class="form-control form-control-color w-100" value="#615dfa">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">{{ __('support.category_order') }}</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="activeNew" checked>
                                    <label class="form-check-label fw-bold" for="activeNew">{{ __('support.category_active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold">إضافة القسم</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
