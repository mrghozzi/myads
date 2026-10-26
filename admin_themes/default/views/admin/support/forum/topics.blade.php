@extends('admin::layouts.admin')

@section('title', __('support.admin_forum_management'))

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
                <i class="feather-message-circle text-primary me-2"></i> {{ __('support.admin_forum_management') }}
            </h3>
        </div>
        <a href="{{ route('admin.support.forum.categories') }}" class="btn btn-outline-primary">
            <i class="feather-folder me-1"></i> {{ __('support.admin_manage_categories') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.support.forum.topics') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="بحث بعنوان السؤال أو الاستفسار...">
                </div>

                <div class="col-md-4">
                    <select name="category_id" class="form-select">
                        <option value="">-- كافة الأقسام --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-filter me-1"></i> تصفية
                    </button>
                    @if(request()->hasAny(['search', 'category_id']))
                        <a href="{{ route('admin.support.forum.topics') }}" class="btn btn-outline-secondary" title="إلغاء التصفية">
                            <i class="feather-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Topics Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">عنوان الموضوع</th>
                        <th>القسم</th>
                        <th>الكاتب</th>
                        <th>الحالة</th>
                        <th>الردود / المشاهدات</th>
                        <th>التاريخ</th>
                        <th class="text-end pe-4">{{ __('support.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topics as $t)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('support.topic', ['id' => $t->id, 'slug' => $t->slug]) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                    {{ $t->title }}
                                </a>
                                @if($t->is_pinned)
                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1"><i class="feather-anchor"></i> مثبت</span>
                                @endif
                                @if($t->is_locked)
                                    <span class="badge bg-warning bg-opacity-10 text-warning ms-1"><i class="feather-lock"></i> مغلق</span>
                                @endif
                            </td>
                            <td>
                                @if($t->category)
                                    <span class="badge bg-light text-dark border">
                                        {{ $t->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ !empty($t->user->img) ? theme_asset('img/' . $t->user->img) : theme_asset('img/avatar.png') }}"
                                         alt="{{ $t->user->username ?? 'User' }}"
                                         class="rounded-circle" width="28" height="28" style="object-fit:cover;">
                                    <span class="small fw-semibold">{{ $t->user->username ?? 'User' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($t->isResolved())
                                    <span class="badge bg-success"><i class="feather-check-circle me-1"></i> {{ __('support.solved') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="feather-help-circle me-1"></i> {{ __('support.unsolved') }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                <i class="feather-message-square me-1"></i> {{ $t->replies_count }}
                                <span class="mx-1">•</span>
                                <i class="feather-eye me-1"></i> {{ $t->views_count }}
                            </td>
                            <td class="text-muted small">
                                {{ $t->created_at->diffForHumans() }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('support.topic', ['id' => $t->id, 'slug' => $t->slug]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="معاينة في الموقع">
                                        <i class="feather-external-link"></i>
                                    </a>
                                    <form action="{{ route('admin.support.forum.topics.pin', $t->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $t->is_pinned ? 'btn-primary' : 'btn-outline-primary' }}" title="{{ $t->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}">
                                            <i class="feather-anchor"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.support.forum.topics.lock', $t->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $t->is_locked ? 'btn-warning' : 'btn-outline-warning' }}" title="{{ $t->is_locked ? 'فتح الموضوع' : 'إغلاق الموضوع' }}">
                                            <i class="feather-lock"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.support.forum.topics.delete', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموضوع وجميع الردود التابعة له؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                لا توجد مواضيع دعم تطابق معايير البحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($topics->hasPages())
            <div class="card-footer bg-white p-3 border-0">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
