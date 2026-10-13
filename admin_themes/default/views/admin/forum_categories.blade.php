@extends('admin::layouts.admin')

@section('title', __('messages.forum_categories'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <!-- Hero Section -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('forum.index') }}"><i class="feather-message-square me-1"></i>{{ __('messages.forum') ?? 'Forum' }}</a></li>
                <li>{{ __('messages.forum_categories') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2.5 py-1 rounded-pill">
                    <i class="feather-folder-plus me-1"></i>{{ __('messages.sub_forums') }}
                </span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-folder text-primary"></i>
                <span>{{ __('messages.forum_categories') }}</span>
            </h1>
            <p class="admin-hero__copy">
                {{ __('messages.forum_categories_desc') }} &bull; {{ __('messages.drag_to_reorder_categories') }}
            </p>

            <!-- 4 KPI Global Stat Strip -->
            <div class="admin-stat-strip mt-3" id="heroStatsContainer">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-folder me-1 text-primary"></i>{{ __('messages.forum_categories') }}</span>
                    <span class="admin-stat-value text-primary" id="kpiTotalCategories">{{ $allCategories->count() }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-folder-minus me-1 text-success"></i>{{ __('messages.root_categories') }}</span>
                    <span class="admin-stat-value text-success" id="kpiRootCategories">{{ $rootCategories->count() }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-git-merge me-1 text-info"></i>{{ __('messages.sub_forums') }}</span>
                    <span class="admin-stat-value text-info" id="kpiSubForums">{{ $allCategories->whereNotNull('parent_id')->count() }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-message-square me-1 text-warning"></i>{{ __('messages.topics') }}</span>
                    <span class="admin-stat-value text-warning" id="kpiTotalTopics">{{ \App\Models\ForumTopic::forumOnly()->where('statu', 1)->whereNull('group_id')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Hero Actions -->
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 w-100 flex-wrap">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 flex-grow-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal" data-parent-id="">
                    <i class="feather-plus-circle"></i>
                    <span>{{ __('messages.add_category') }}</span>
                </button>
                <a href="{{ route('forum.index') }}" target="_blank" class="btn btn-light border d-inline-flex align-items-center gap-2" title="{{ __('messages.forum') }}">
                    <i class="feather-external-link"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.forum') }}</span>
                </a>
                <a href="{{ route('admin.forum_categories') }}" class="btn btn-light border admin-icon-btn" title="{{ __('messages.refresh') ?? 'Refresh' }}">
                    <i class="feather-refresh-cw"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Controls Toolbar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--admin-premium-radius); background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3" style="border-radius: 12px 0 0 12px;">
                            <i class="feather-search"></i>
                        </span>
                        <input type="text" id="forumCategorySearch" class="form-control border-0 bg-light py-2" placeholder="{{ __('messages.search') }}..." style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                <div class="col-md-7 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-bold">
                            {{ $allCategories->count() }} {{ __('messages.total') }}
                        </span>
                        <button type="button" id="btnExpandAll" class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-semibold">
                            <i class="feather-maximize-2 me-1"></i> {{ __('messages.expand_all') ?? 'Expand All' }}
                        </button>
                        <button type="button" id="btnCollapseAll" class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-semibold">
                            <i class="feather-minimize-2 me-1"></i> {{ __('messages.collapse_all') ?? 'Collapse All' }}
                        </button>
                        <!-- View Switcher -->
                        <div class="btn-group p-1 bg-light rounded-pill ms-2" role="group">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 active" id="btnShowTree">
                                <i class="feather-git-branch me-1"></i> {{ __('messages.tree_view') ?? 'Tree' }}
                            </button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" id="btnShowTable">
                                <i class="feather-list me-1"></i> {{ __('messages.table_view') ?? 'Table' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Helpful Drag Instructions Note -->
            <div class="alert alert-soft-primary border-0 rounded-4 mt-3 mb-0 p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 fs-13">
                    <i class="feather-info text-primary fs-18"></i>
                    <span>
                        <strong>{{ __('messages.tip') ?? 'Tip' }}:</strong>
                        {{ __('messages.drag_drop_hierarchy_tip') ?? 'Drag items up/down to reorder, or drop directly into another category to make it a sub-forum. Changes save automatically via AJAX.' }}
                    </span>
                </div>
                <span class="badge bg-white text-primary shadow-sm rounded-pill px-3 py-1 fs-12 fw-bold d-none d-md-inline-block">
                    <i class="feather-zap me-1 text-warning"></i> AJAX Auto-save
                </span>
            </div>
        </div>
    </div>

    <!-- Tree View Container -->
    <div id="treeViewWrapper" class="mb-5">
        <div id="forumTreeRoot" class="forum-tree-root">
            @forelse($rootCategories as $category)
                @include('admin::admin.partials.forum_category_tree_node', ['category' => $category, 'level' => 0])
            @empty
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 20px;">
                    <div class="card-body">
                        <i class="feather-folder text-muted" style="font-size: 48px;"></i>
                        <h5 class="fw-bold mt-3 text-dark">{{ __('messages.no_categories_found') ?? 'No categories found' }}</h5>
                        <p class="text-muted">{{ __('messages.create_first_category') ?? 'Start by creating your first category.' }}</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="feather-plus-circle me-1"></i> {{ __('messages.add_category') }}
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Table View Container (Secondary toggle) -->
    <div id="tableViewWrapper" class="card border-0 shadow-sm mb-5 d-none" style="border-radius: 20px;">
        <div class="card-header border-0 bg-transparent py-4 ps-4 pe-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">{{ __('messages.category_list') }}</h5>
            <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-bold">
                {{ $categories->total() }} {{ __('messages.total') }}
            </span>
        </div>
        <div class="card-body px-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                        <tr>
                            <th class="ps-4 py-3">#ID</th>
                            <th class="py-3">{{ __('messages.category') }}</th>
                            <th class="py-3">{{ __('messages.parent_category') ?? 'Parent' }}</th>
                            <th class="py-3">{{ __('messages.visibility') }}</th>
                            <th class="py-3">{{ __('messages.order') }}</th>
                            <th class="text-end pe-4 py-3">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @foreach($categories as $category)
                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                            <td class="ps-4">
                                <span class="fw-bold text-muted">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon-box me-3 shadow-sm" style="background: {{ $category->color ?: '#615dfa' }}; color: #fff; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa {{ $category->icons }}"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark fs-14">{{ $category->name }}</span>
                                            @if($category->isSubCategory())
                                                <span class="badge bg-soft-info text-info rounded-pill fs-11">{{ __('messages.sub_forum') ?? 'Sub-forum' }}</span>
                                            @endif
                                        </div>
                                        @if($category->txt)
                                        <div class="text-muted small opacity-80" title="{{ $category->txt }}">
                                            {{ Str::limit($category->txt, 40) }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-soft-secondary text-dark rounded-pill px-3">
                                        <i class="feather-corner-down-right me-1 text-primary"></i> {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3">{{ __('messages.root_category') ?? 'Root' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($category->visibility == 0)
                                    <span class="badge bg-soft-success text-success rounded-pill px-3"><i class="feather-eye me-1"></i> {{ __('messages.everyone') }}</span>
                                @elseif($category->visibility == 1)
                                    <span class="badge bg-soft-warning text-warning rounded-pill px-3"><i class="feather-users me-1"></i> {{ __('messages.members_only') }}</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-3"><i class="feather-shield me-1"></i> {{ __('messages.mods_only') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark fw-bold rounded-pill px-3">{{ $category->ordercat }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-icon btn-light-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="{{ __('messages.edit') }}">
                                        <i class="feather-edit-2"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-light-danger ms-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}" title="{{ __('messages.delete') }}">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
        <div class="card-footer bg-transparent border-0 pb-4">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Floating Live Status Toast -->
<div id="treeToastPill" class="tree-toast-pill" role="status">
    <div id="treeToastContent" class="d-flex align-items-center gap-2">
        <span class="spinner-border spinner-border-sm text-light"></span>
        <span>{{ __('messages.saving') ?? 'Saving changes...' }}</span>
    </div>
</div>
@endsection

@section('modals')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 id="addCategoryModalTitle" class="modal-title fw-bold fs-18 text-dark">{{ __('messages.new_category') }}</h5>
                    <p id="addCategoryModalSubtitle" class="text-muted small mb-0">{{ __('messages.add_category_desc') ?? 'Create a root category or a nested sub-forum.' }}</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.forum_categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.parent_category') ?? 'Parent Category / Forum' }}</label>
                        <select name="parent_id" id="addCategoryParentId" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                            <option value="">-- {{ __('messages.root_category') ?? 'Root Category (قسم رئيسي)' }} --</option>
                            @foreach($allCategories as $catOption)
                                <option value="{{ $catOption->id }}">
                                    {{ str_repeat('— ', $catOption->depth()) }}{{ $catOption->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text fs-12 text-muted">{{ __('messages.parent_category_hint') ?? 'Leave empty to create a top-level category, or select a parent to create a sub-forum.' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" placeholder="{{ __('messages.enter_category_name') }}" style="border-radius: 12px;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control border-soft-light bg-light" rows="3" placeholder="{{ __('messages.brief_description') }}" style="border-radius: 12px;"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 icon-picker-field">
                            @include('admin::admin.partials.icon_picker')
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.order') }}</label>
                            <input type="number" name="ordercat" class="form-control form-control-lg border-soft-light bg-light" value="0" style="border-radius: 12px;" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.visibility') }}</label>
                        <select name="visibility" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;" required>
                            <option value="0">{{ __('messages.everyone') }}</option>
                            <option value="1">{{ __('messages.members_only') }}</option>
                            <option value="2">{{ __('messages.mods_only') }}</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.category_color') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" id="addCategoryColor" class="form-control form-control-color border-soft-light bg-light p-1" value="#615dfa" style="border-radius: 12px; width: 48px; height: 42px;" onchange="document.getElementById('addCategoryColorHex').value = this.value">
                            <input type="text" id="addCategoryColorHex" class="form-control border-soft-light bg-light" value="#615dfa" style="border-radius: 12px; max-width: 140px;" oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)) document.getElementById('addCategoryColor').value = this.value">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($allCategories as $category)
<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.edit_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.forum_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.parent_category') ?? 'Parent Category / Forum' }}</label>
                        <select name="parent_id" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                            <option value="" {{ is_null($category->parent_id) ? 'selected' : '' }}>-- {{ __('messages.root_category') ?? 'Root Category (قسم رئيسي)' }} --</option>
                            @php
                                $descendantIds = $category->allChildIds();
                            @endphp
                            @foreach($allCategories as $target)
                                @if($target->id != $category->id && !in_array($target->id, $descendantIds, true))
                                    <option value="{{ $target->id }}" {{ $category->parent_id == $target->id ? 'selected' : '' }}>
                                        {{ str_repeat('— ', $target->depth()) }}{{ $target->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->name }}" style="border-radius: 12px;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control border-soft-light bg-light" rows="3" style="border-radius: 12px;">{{ $category->txt }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 icon-picker-field">
                            @include('admin::admin.partials.icon_picker', ['selectedIcon' => $category->icons])
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.order') }}</label>
                            <input type="number" name="ordercat" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->ordercat }}" style="border-radius: 12px;" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.visibility') }}</label>
                        <select name="visibility" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;" required>
                            <option value="0" {{ $category->visibility == 0 ? 'selected' : '' }}>{{ __('messages.everyone') }}</option>
                            <option value="1" {{ $category->visibility == 1 ? 'selected' : '' }}>{{ __('messages.members_only') }}</option>
                            <option value="2" {{ $category->visibility == 2 ? 'selected' : '' }}>{{ __('messages.mods_only') }}</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.category_color') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" id="editCategoryColor{{ $category->id }}" class="form-control form-control-color border-soft-light bg-light p-1" value="{{ $category->color ?: '#615dfa' }}" style="border-radius: 12px; width: 48px; height: 42px;" onchange="document.getElementById('editCategoryColorHex{{ $category->id }}').value = this.value">
                            <input type="text" id="editCategoryColorHex{{ $category->id }}" class="form-control border-soft-light bg-light" value="{{ $category->color ?: '#615dfa' }}" style="border-radius: 12px; max-width: 140px;" oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)) document.getElementById('editCategoryColor{{ $category->id }}').value = this.value">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.confirm_delete_category') }}</h4>
                <p class="text-muted mb-4">{{ $category->name }}</p>

                @php
                    $topicCount = \App\Models\ForumTopic::where('cat', $category->id)->count();
                    $childCount = $category->children->count();
                @endphp

                <form action="{{ route('admin.forum_categories.delete', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    @if($childCount > 0)
                        <div class="alert alert-soft-info border-0 rounded-4 text-start mb-3 p-3">
                            <i class="feather-info text-info me-2"></i>
                            <span class="fs-13">{{ __('messages.child_categories_reassigned_note') ?? 'Note: Sub-forums under this category will automatically be moved to the parent level.' }}</span>
                        </div>
                    @endif

                    @if($topicCount > 0)
                        <div class="alert alert-soft-warning border-0 rounded-4 text-start mb-4 p-3">
                            <div class="d-flex border-start border-warning border-4 ps-3">
                                <i class="feather-alert-triangle text-warning me-3 fs-20 mt-1"></i>
                                <div>
                                    <div class="fw-bold text-warning">{{ __('messages.category_not_empty') }}</div>
                                    <div class="small opacity-80">({{ $topicCount }} {{ __('messages.topics') }})</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 text-start">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.move_topics_to') }}</label>
                            <select name="move_to_id" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;" required>
                                <option value="">{{ __('messages.select_category') }}</option>
                                @foreach($allCategories as $target)
                                    @if($target->id != $category->id)
                                        <option value="{{ $target->id }}">{{ $target->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="modal-footer border-0 justify-content-center p-0">
                        <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm ms-2" style="border-radius: 10px;">{{ __('messages.delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // View Switcher (Tree vs Table)
    // -------------------------------------------------------------
    const btnShowTree = document.getElementById('btnShowTree');
    const btnShowTable = document.getElementById('btnShowTable');
    const treeViewWrapper = document.getElementById('treeViewWrapper');
    const tableViewWrapper = document.getElementById('tableViewWrapper');

    if (btnShowTree && btnShowTable) {
        btnShowTree.addEventListener('click', function () {
            btnShowTree.classList.add('btn-primary', 'active');
            btnShowTree.classList.remove('btn-light');
            btnShowTable.classList.add('btn-light');
            btnShowTable.classList.remove('btn-primary', 'active');
            treeViewWrapper.classList.remove('d-none');
            tableViewWrapper.classList.add('d-none');
        });

        btnShowTable.addEventListener('click', function () {
            btnShowTable.classList.add('btn-primary', 'active');
            btnShowTable.classList.remove('btn-light');
            btnShowTree.classList.add('btn-light');
            btnShowTree.classList.remove('btn-primary', 'active');
            tableViewWrapper.classList.remove('d-none');
            treeViewWrapper.classList.add('d-none');
        });
    }

    // -------------------------------------------------------------
    // Live Search Filter
    // -------------------------------------------------------------
    const searchInput = document.getElementById('forumCategorySearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = (this.value || '').trim().toLowerCase();
            const nodes = document.querySelectorAll('.forum-tree-node');
            nodes.forEach(function (node) {
                const name = (node.dataset.name || '').toLowerCase();
                if (!query || name.indexOf(query) !== -1) {
                    node.style.display = '';
                } else {
                    node.style.display = 'none';
                }
            });
        });
    }

    // -------------------------------------------------------------
    // Expand / Collapse All Sublists
    // -------------------------------------------------------------
    const btnExpandAll = document.getElementById('btnExpandAll');
    const btnCollapseAll = document.getElementById('btnCollapseAll');

    if (btnExpandAll) {
        btnExpandAll.addEventListener('click', function () {
            document.querySelectorAll('.forum-tree-sublist').forEach(function (el) {
                el.style.display = 'flex';
            });
        });
    }
    if (btnCollapseAll) {
        btnCollapseAll.addEventListener('click', function () {
            document.querySelectorAll('.forum-tree-sublist').forEach(function (el) {
                el.style.display = 'none';
            });
        });
    }

    // -------------------------------------------------------------
    // Quick "+ Sub Forum" Trigger
    // -------------------------------------------------------------
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-add-subforum');
        if (btn) {
            e.preventDefault();
            const parentId = btn.getAttribute('data-id');
            const parentName = btn.getAttribute('data-name');
            const parentSelect = document.getElementById('addCategoryParentId');
            const modalTitle = document.getElementById('addCategoryModalTitle');
            const modalSub = document.getElementById('addCategoryModalSubtitle');

            if (parentSelect) {
                parentSelect.value = parentId;
            }
            if (modalTitle) {
                modalTitle.textContent = '{{ __("messages.add_sub_forum") ?? "New Sub Forum" }}: ' + parentName;
            }
            if (modalSub) {
                modalSub.textContent = '{{ __("messages.sub_forum_under") ?? "Sub-forum will be nested under" }}: ' + parentName;
            }

            const modalEl = document.getElementById('addCategoryModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }

        // Quick "Make Root" button
        const rootBtn = e.target.closest('.btn-make-root');
        if (rootBtn) {
            e.preventDefault();
            const nodeId = rootBtn.getAttribute('data-id');
            const nodeEl = document.querySelector('.forum-tree-node[data-id="' + nodeId + '"]');
            const rootContainer = document.getElementById('forumTreeRoot');

            if (nodeEl && rootContainer) {
                rootContainer.appendChild(nodeEl);
                nodeEl.setAttribute('data-parent-id', '');
                saveTreeHierarchy();
            }
        }
    });

    // Reset Add Modal when opened normally
    const addModal = document.getElementById('addCategoryModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function () {
            const parentSelect = document.getElementById('addCategoryParentId');
            const modalTitle = document.getElementById('addCategoryModalTitle');
            const modalSub = document.getElementById('addCategoryModalSubtitle');
            if (parentSelect) parentSelect.value = '';
            if (modalTitle) modalTitle.textContent = '{{ __("messages.new_category") }}';
            if (modalSub) modalSub.textContent = '{{ __("messages.add_category_desc") ?? "Create a root category or a nested sub-forum." }}';
        });
    }

    // -------------------------------------------------------------
    // Drag & Drop Tree Reordering via AJAX
    // -------------------------------------------------------------
    let draggedNode = null;
    let dragIndicator = null;
    const reorderUrl = '{{ route("admin.forum_categories.reorder") }}';
    const csrfToken = '{{ csrf_token() }}';

    function initDraggables() {
        const nodes = document.querySelectorAll('.forum-tree-node');
        nodes.forEach(function (node) {
            node.setAttribute('draggable', 'true');

            node.addEventListener('dragstart', function (e) {
                e.stopPropagation();
                draggedNode = node;
                node.classList.add('is-dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', node.dataset.id);
            });

            node.addEventListener('dragend', function (e) {
                e.stopPropagation();
                if (draggedNode) {
                    draggedNode.classList.remove('is-dragging');
                    draggedNode = null;
                }
                clearDropIndicators();
            });

            node.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!draggedNode || draggedNode === node || draggedNode.contains(node)) {
                    return;
                }

                const item = node.querySelector('.forum-tree-item');
                if (!item) return;

                const rect = item.getBoundingClientRect();
                const relY = e.clientY - rect.top;
                const ratio = relY / rect.height;

                clearDropIndicators();

                if (ratio < 0.25) {
                    item.classList.add('drop-before');
                } else if (ratio > 0.75) {
                    item.classList.add('drop-after');
                } else {
                    item.classList.add('drop-inside');
                }
            });

            node.addEventListener('dragleave', function (e) {
                e.stopPropagation();
                const item = node.querySelector('.forum-tree-item');
                if (item && !item.contains(e.relatedTarget)) {
                    item.classList.remove('drop-before', 'drop-after', 'drop-inside');
                }
            });

            node.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (!draggedNode || draggedNode === node || draggedNode.contains(node)) {
                    clearDropIndicators();
                    return;
                }

                const item = node.querySelector('.forum-tree-item');
                if (!item) {
                    clearDropIndicators();
                    return;
                }

                const rect = item.getBoundingClientRect();
                const relY = e.clientY - rect.top;
                const ratio = relY / rect.height;

                clearDropIndicators();

                if (ratio < 0.25) {
                    // Insert before node
                    node.parentNode.insertBefore(draggedNode, node);
                    const parentNode = node.parentNode.closest('.forum-tree-node');
                    draggedNode.setAttribute('data-parent-id', parentNode ? parentNode.dataset.id : '');
                } else if (ratio > 0.75) {
                    // Insert after node
                    node.parentNode.insertBefore(draggedNode, node.nextSibling);
                    const parentNode = node.parentNode.closest('.forum-tree-node');
                    draggedNode.setAttribute('data-parent-id', parentNode ? parentNode.dataset.id : '');
                } else {
                    // Nest inside node as sub-forum
                    let sublist = node.querySelector('.forum-tree-sublist');
                    if (!sublist) {
                        sublist = document.createElement('div');
                        sublist.className = 'forum-tree-sublist';
                        sublist.setAttribute('data-parent-id', node.dataset.id);
                        node.appendChild(sublist);
                    }
                    sublist.appendChild(draggedNode);
                    draggedNode.setAttribute('data-parent-id', node.dataset.id);
                }

                updateEmptyStates();
                saveTreeHierarchy();
            });
        });
    }

    function clearDropIndicators() {
        document.querySelectorAll('.forum-tree-item').forEach(function (el) {
            el.classList.remove('drop-before', 'drop-after', 'drop-inside');
        });
    }

    function updateEmptyStates() {
        document.querySelectorAll('.forum-tree-sublist').forEach(function (sublist) {
            const hasItems = sublist.querySelectorAll('.forum-tree-node').length > 0;
            const emptyZone = sublist.querySelector('.forum-tree-empty-dropzone');
            if (emptyZone) {
                emptyZone.classList.toggle('d-none', hasItems);
            }
        });
    }

    function showToast(status, message) {
        const toast = document.getElementById('treeToastPill');
        const content = document.getElementById('treeToastContent');
        if (!toast || !content) return;

        let icon = '<span class="spinner-border spinner-border-sm text-light"></span>';
        if (status === 'success') {
            icon = '<i class="feather-check-circle text-success fs-18"></i>';
        } else if (status === 'error') {
            icon = '<i class="feather-alert-triangle text-danger fs-18"></i>';
        }

        content.innerHTML = icon + '<span class="ms-2">' + message + '</span>';
        toast.classList.add('is-visible');

        if (status !== 'saving') {
            setTimeout(function () {
                toast.classList.remove('is-visible');
            }, 3000);
        }
    }

    function saveTreeHierarchy() {
        showToast('saving', '{{ __("messages.saving") ?? "Saving categories tree..." }}');

        const items = [];
        const root = document.getElementById('forumTreeRoot');

        function traverse(container, parentId) {
            const childNodes = Array.prototype.slice.call(container.children).filter(function (el) {
                return el.classList && el.classList.contains('forum-tree-node');
            });

            childNodes.forEach(function (node, index) {
                const id = parseInt(node.getAttribute('data-id'), 10);
                items.push({
                    id: id,
                    parent_id: parentId ? parseInt(parentId, 10) : null,
                    order: index
                });

                const sublist = node.querySelector('.forum-tree-sublist');
                if (sublist) {
                    traverse(sublist, id);
                }
            });
        }

        traverse(root, null);

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(function (res) {
            if (!res.ok) {
                throw new Error('Server error');
            }
            return res.json();
        })
        .then(function (data) {
            showToast('success', data.message || '{{ __("messages.categories_reordered") ?? "Categories hierarchy updated successfully!" }}');
        })
        .catch(function (err) {
            console.error('Reorder error:', err);
            showToast('error', '{{ __("messages.error_occurred") ?? "Failed to save category order." }}');
        });
    }

    initDraggables();
});

// -------------------------------------------------------------
// Icon Picker Logic
// -------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    var pickers = Array.prototype.slice.call(document.querySelectorAll('[data-icon-picker]'));
    if (!pickers.length) return;

    function slugFromValue(value) {
        if (!value) return '';
        var parts = value.trim().split(/\s+/);
        var lastPart = parts[parts.length - 1] || '';
        return lastPart.replace(/^fa-/, '');
    }

    function optionPayload(option) {
        return {
            value: option.value,
            label: (option.textContent || '').trim(),
            iconClass: option.dataset.icon || option.value,
        };
    }

    function collectGroups(select, fallbackLabel) {
        var groups = [];
        var groupedValues = new Set();
        var fallbackOptions = [];

        Array.prototype.forEach.call(select.children, function (child) {
            if (child.tagName === 'OPTGROUP') {
                var options = Array.prototype.slice.call(child.querySelectorAll('option')).filter(function (option) {
                    return option.value;
                }).map(function (option) {
                    groupedValues.add(option.value);
                    return optionPayload(option);
                });
                if (options.length) {
                    groups.push({
                        label: child.label,
                        options: options,
                    });
                }
            } else if (child.tagName === 'OPTION' && child.value) {
                fallbackOptions.push(optionPayload(child));
            }
        });

        fallbackOptions.forEach(function (option) {
            if (!groupedValues.has(option.value)) {
                groups.unshift({
                    label: fallbackLabel,
                    options: [option],
                });
            }
        });

        return groups;
    }

    function syncPreview(picker) {
        var select = picker.querySelector('.icon-picker__native');
        var trigger = picker.querySelector('.icon-picker__trigger');
        var badgeIcon = picker.querySelector('.icon-picker__trigger-badge i');
        var valueNode = picker.querySelector('.icon-picker__trigger-value');
        var placeholder = picker.dataset.placeholder || 'Select Icon';
        var selectedOption = select.options[select.selectedIndex];
        var value = selectedOption && selectedOption.value ? selectedOption.value : '';
        var label = value ? ((selectedOption.textContent || '').trim() || slugFromValue(value)) : placeholder;
        var iconClass = value ? (selectedOption.dataset.icon || value) : 'fa-solid fa-icons';

        badgeIcon.className = iconClass;
        valueNode.textContent = label;
        trigger.classList.toggle('is-empty', !value);
    }

    function closePicker(picker) {
        var trigger = picker.querySelector('.icon-picker__trigger');
        var panel = picker.querySelector('.icon-picker__panel');
        var search = picker.querySelector('.icon-picker__search');

        picker.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.hidden = true;
        search.value = '';
        renderOptions(picker, '');
    }

    function closeOtherPickers(currentPicker) {
        pickers.forEach(function (picker) {
            if (picker !== currentPicker) {
                closePicker(picker);
            }
        });
    }

    function openPicker(picker) {
        closeOtherPickers(picker);
        var trigger = picker.querySelector('.icon-picker__trigger');
        var panel = picker.querySelector('.icon-picker__panel');
        var search = picker.querySelector('.icon-picker__search');

        picker.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.hidden = false;
        renderOptions(picker, search.value);

        window.requestAnimationFrame(function () {
            search.focus();
        });
    }

    function renderOptions(picker, filterText) {
        var results = picker.querySelector('.icon-picker__results');
        var groups = picker._groups || [];
        var query = (filterText || '').trim().toLowerCase();
        var selectedValue = picker.querySelector('.icon-picker__native').value;

        results.innerHTML = '';
        var matchCount = 0;

        groups.forEach(function (group) {
            var matchingOptions = group.options.filter(function (option) {
                if (!query) return true;
                return option.label.toLowerCase().indexOf(query) !== -1 ||
                    option.value.toLowerCase().indexOf(query) !== -1;
            });

            if (!matchingOptions.length) return;
            matchCount += matchingOptions.length;

            var groupNode = document.createElement('div');
            groupNode.className = 'icon-picker__group';

            var titleNode = document.createElement('p');
            titleNode.className = 'icon-picker__group-title';
            titleNode.textContent = group.label;
            groupNode.appendChild(titleNode);

            var listNode = document.createElement('div');
            listNode.className = 'icon-picker__group-options';

            matchingOptions.forEach(function (option) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'icon-picker__option' + (option.value === selectedValue ? ' is-selected' : '');
                btn.setAttribute('data-value', option.value);

                btn.innerHTML =
                    '<span class="icon-picker__option-badge"><i class="' + option.iconClass + '"></i></span>' +
                    '<span class="icon-picker__option-copy">' +
                        '<span class="icon-picker__option-label">' + option.label + '</span>' +
                        '<span class="icon-picker__option-meta">' + option.value + '</span>' +
                    '</span>' +
                    '<i class="feather-check icon-picker__option-check"></i>';

                btn.addEventListener('click', function () {
                    var nativeSelect = picker.querySelector('.icon-picker__native');
                    nativeSelect.value = option.value;
                    nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    syncPreview(picker);
                    closePicker(picker);
                });

                listNode.appendChild(btn);
            });

            groupNode.appendChild(listNode);
            results.appendChild(groupNode);
        });

        if (!matchCount) {
            var emptyNode = document.createElement('div');
            emptyNode.className = 'icon-picker__empty';
            emptyNode.textContent = 'No icons found';
            results.appendChild(emptyNode);
        }
    }

    pickers.forEach(function (picker) {
        var select = picker.querySelector('.icon-picker__native');
        var trigger = picker.querySelector('.icon-picker__trigger');
        var search = picker.querySelector('.icon-picker__search');
        var fallbackLabel = picker.dataset.allLabel || 'All Icons';

        picker._groups = collectGroups(select, fallbackLabel);
        syncPreview(picker);
        picker.classList.add('is-ready');

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (picker.classList.contains('is-open')) {
                closePicker(picker);
            } else {
                openPicker(picker);
            }
        });

        search.addEventListener('input', function () {
            renderOptions(picker, search.value);
        });

        select.addEventListener('change', function () {
            syncPreview(picker);
        });
    });

    document.addEventListener('click', function (e) {
        pickers.forEach(function (picker) {
            if (!picker.contains(e.target)) {
                closePicker(picker);
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
/* --- Forum Category Tree Modern Styling --- */
.forum-tree-root {
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 120px;
}
.forum-tree-node {
    position: relative;
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.forum-tree-node.is-dragging {
    opacity: 0.45;
    transform: scale(0.98);
}
.forum-tree-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 18px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    transition: all 0.2s ease;
    user-select: none;
}
.app-skin-dark .forum-tree-item {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
.forum-tree-item:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.08);
}
.forum-drag-handle {
    cursor: grab;
    color: #94a3b8;
    padding: 8px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 16px;
}
.forum-drag-handle:hover,
.forum-drag-handle:active {
    color: #6366f1;
    background: #eef2ff;
    cursor: grabbing;
}
.app-skin-dark .forum-drag-handle:hover {
    background: #312e81;
    color: #a5b4fc;
}
.forum-tree-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.forum-tree-content {
    min-width: 0;
    flex: 1;
}
.forum-tree-title {
    font-size: 15px;
}
.forum-tree-sublist {
    margin-inline-start: 36px;
    padding-inline-start: 18px;
    border-inline-start: 2px dashed #cbd5e1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
    min-height: 10px;
    position: relative;
}
.app-skin-dark .forum-tree-sublist {
    border-inline-start-color: #475569;
}
.forum-tree-empty-dropzone {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
    font-size: 12px;
    color: #64748b;
    background: rgba(248, 250, 252, 0.6);
    display: flex;
    align-items: center;
}
.app-skin-dark .forum-tree-empty-dropzone {
    background: rgba(30, 41, 59, 0.5);
    border-color: #334155;
    color: #94a3b8;
}

/* Drop Indicators */
.forum-tree-item.drop-before {
    border-top: 3px solid #6366f1 !important;
}
.forum-tree-item.drop-after {
    border-bottom: 3px solid #6366f1 !important;
}
.forum-tree-item.drop-inside {
    background: #eff6ff !important;
    border: 2px dashed #3b82f6 !important;
}
.app-skin-dark .forum-tree-item.drop-inside {
    background: rgba(59, 130, 246, 0.15) !important;
    border-color: #60a5fa !important;
}

/* Floating Save Toast */
.tree-toast-pill {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 1080;
    padding: 12px 22px;
    background: #1e293b;
    color: #fff;
    border-radius: 30px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
    transform: translateY(120px);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: none;
}
html[dir="rtl"] .tree-toast-pill {
    right: auto;
    left: 28px;
}
.tree-toast-pill.is-visible {
    transform: translateY(0);
    opacity: 1;
}

/* Icon Picker Styles */
.icon-picker {
    position: relative;
    width: 100%;
}
.icon-picker__native {
    display: none !important;
}
.icon-picker__trigger {
    width: 100%;
    min-height: 48px;
    border-radius: 14px;
    border: 1px solid #d9e2ef;
    background: #f8fafc;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    cursor: pointer;
    text-align: start;
    font: inherit;
    transition: all .2s ease;
}
.icon-picker__trigger:hover,
.icon-picker.is-open .icon-picker__trigger {
    background: #fff;
    border-color: #3454d1;
    box-shadow: 0 0 0 4px rgba(52, 84, 209, 0.1);
}
.icon-picker__trigger-content {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.icon-picker__trigger-badge {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #eef3ff;
    color: #3454d1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.icon-picker__trigger-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.icon-picker__panel {
    border-radius: 18px;
    background: #fff;
    border: 1px solid #d9e2ef;
    box-shadow: 0 18px 40px -10px rgba(15, 23, 42, 0.18);
    overflow: hidden;
}
.icon-picker__header {
    padding: 12px 14px;
    background: #f8fafc;
    border-bottom: 1px solid #e7edf5;
    position: relative;
}
.icon-picker__search {
    width: 100%;
    border-radius: 10px;
    border: 1px solid #d9e2ef;
    padding: 8px 14px;
    font-size: 13px;
}
.icon-picker__results {
    max-height: 280px;
    overflow-y: auto;
    padding: 8px;
}
.icon-picker__group-title {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    padding: 6px 8px 4px;
    margin: 0;
}
.icon-picker__group-options {
    display: grid;
    gap: 4px;
}
.icon-picker__option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border: 1px solid transparent;
    border-radius: 10px;
    background: transparent;
    cursor: pointer;
    width: 100%;
    text-align: start;
    font: inherit;
    transition: all .15s ease;
}
.icon-picker__option:hover {
    background: #f1f5f9;
}
.icon-picker__option.is-selected {
    background: #eef2ff;
    border-color: #c7d2fe;
}
.icon-picker__option-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #1e293b;
    flex-shrink: 0;
}
.icon-picker__option-copy {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}
.icon-picker__option-label {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.icon-picker__option-meta {
    font-size: 11px;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.icon-picker__option-check {
    font-size: 14px;
    color: #4f46e5;
    opacity: 0;
}
.icon-picker__option.is-selected .icon-picker__option-check {
    opacity: 1;
}
@media (min-width: 768px) {
    .icon-picker.is-ready .icon-picker__panel {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 8px);
        z-index: 1055;
    }
}
</style>
@endpush
