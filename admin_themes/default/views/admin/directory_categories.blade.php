@extends('admin::layouts.admin')

@section('title', __('messages.directory_categories'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <!-- Floating Toast Notification -->
    <div id="dirToastPill" class="dir-toast-pill" role="status" aria-live="polite">
        <div id="dirToastContent" class="d-flex align-items-center">
            <span class="spinner-border spinner-border-sm text-light me-2"></span>
            <span>{{ __('messages.saving') ?? 'Processing...' }}</span>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('directory.index') }}"><i class="feather-compass me-1"></i>{{ __('messages.web_directory') ?? 'Directory' }}</a></li>
                <li>{{ __('messages.directory_categories') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-warning text-warning px-2.5 py-1 rounded-pill">
                    <i class="feather-folder-plus me-1"></i>{{ __('messages.directory_taxonomy') ?? 'Directory Taxonomy & Hierarchy' }}
                </span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-folder text-warning"></i>
                <span>{{ __('messages.directory_categories') }}</span>
            </h1>
            <p class="admin-hero__copy">
                {{ __('messages.directory_categories_desc') }} &bull; {{ __('messages.drag_drop_hierarchy_tip') ?? 'Drag categories to reorder or nest, toggle status, and inspect listings.' }}
            </p>

            <!-- 4 KPI Global Stat Strip -->
            <div class="admin-stat-strip mt-3" id="heroStatsContainer">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-folder me-1 text-warning"></i>{{ __('messages.cat_s') ?? 'Total Categories' }}</span>
                    <span class="admin-stat-value text-warning" id="kpiTotalCategories">{{ $stats['total_categories'] }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-layers me-1 text-primary"></i>{{ __('messages.main_category') ?? 'Root Categories' }}</span>
                    <span class="admin-stat-value text-primary" id="kpiRootCategories">{{ $stats['root_categories'] }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-git-merge me-1 text-info"></i>{{ __('messages.subcategories_count') ?? 'Subcategories' }}</span>
                    <span class="admin-stat-value text-info" id="kpiSubCategories">{{ $stats['sub_categories'] }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-globe me-1 text-success"></i>{{ __('messages.listings_count') ?? 'Directory Listings' }}</span>
                    <span class="admin-stat-value text-success" id="kpiTotalListings">{{ $stats['total_listings'] }}</span>
                </div>
            </div>
        </div>

        <!-- Hero Actions -->
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 w-100 flex-wrap">
                <button type="button" class="btn btn-warning d-inline-flex align-items-center gap-2 flex-grow-1 shadow-sm text-dark fw-bold" id="btnOpenAddCategoryModal" data-bs-toggle="modal" data-bs-target="#categoryModal" data-parent-id="0">
                    <i class="feather-plus-circle"></i>
                    <span>{{ __('messages.add_category') }}</span>
                </button>
                <a href="{{ route('directory.index') }}" target="_blank" class="btn btn-light border d-inline-flex align-items-center gap-2" title="{{ __('messages.web_directory') ?? 'Web Directory' }}">
                    <i class="feather-external-link"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.web_directory') ?? 'Directory' }}</span>
                </a>
                <a href="{{ route('admin.directory_categories') }}" class="btn btn-light border admin-icon-btn" title="{{ __('messages.refresh') ?? 'Refresh' }}">
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
                        <input type="text" id="dirCategorySearch" class="form-control border-0 bg-light py-2" placeholder="{{ __('messages.search') }}..." style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                <div class="col-md-7 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                        <select id="dirFilterLevel" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 py-2 fw-semibold" style="width: auto;">
                            <option value="all">{{ __('messages.all') ?? 'All Categories' }}</option>
                            <option value="root">{{ __('messages.main_category') ?? 'Root Categories' }}</option>
                            <option value="sub">{{ __('messages.subcategories_count') ?? 'Subcategories' }}</option>
                            <option value="active">{{ __('messages.active') ?? 'Active Only' }}</option>
                            <option value="inactive">{{ __('messages.inactive') ?? 'Inactive Only' }}</option>
                        </select>
                        <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-2 fw-bold" id="badgeTotalFilterCount">
                            {{ $stats['total_categories'] }} {{ __('messages.total') }}
                        </span>
                        <button type="button" id="btnExpandAll" class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-semibold">
                            <i class="feather-maximize-2 me-1"></i> {{ __('messages.expand_all') ?? 'Expand All' }}
                        </button>
                        <button type="button" id="btnCollapseAll" class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-semibold">
                            <i class="feather-minimize-2 me-1"></i> {{ __('messages.collapse_all') ?? 'Collapse All' }}
                        </button>
                        <!-- View Switcher -->
                        <div class="btn-group p-1 bg-light rounded-pill ms-2" role="group">
                            <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 active fw-bold" id="btnShowTree">
                                <i class="feather-git-branch me-1"></i> {{ __('messages.tree_view') ?? 'Tree' }}
                            </button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3 fw-bold" id="btnShowTable">
                                <i class="feather-list me-1"></i> {{ __('messages.table_view') ?? 'Table' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree View -->
    <div id="dirTreeViewContainer" class="mb-5">
        <div class="dir-tree-root" id="dirTreeRoot">
            @forelse($rootCategories as $root)
            <div class="dir-tree-node" data-id="{{ $root->id }}" data-level="root" data-status="{{ $root->statu }}" data-name="{{ strtolower($root->name) }}" data-slug="{{ strtolower($root->slug) }}">
                <div class="dir-tree-item {{ $root->statu == 0 ? 'is-disabled' : '' }}" draggable="true">
                    <div class="dir-drag-handle" title="{{ __('messages.drag_to_reorder_categories') ?? 'Drag to reorder' }}">
                        <i class="feather-move"></i>
                    </div>

                    <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle dir-toggle-collapse {{ $root->children->isEmpty() ? 'invisible' : '' }}" data-target="#sublist-{{ $root->id }}" aria-expanded="true" title="{{ __('messages.expand_collapse') ?? 'Expand / Collapse' }}">
                        <i class="feather-chevron-down"></i>
                    </button>

                    <div class="dir-tree-icon bg-soft-warning text-warning">
                        <i class="{{ $root->display_icon }}"></i>
                    </div>

                    <div class="dir-tree-content">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="dir-tree-title fw-bold text-dark fs-15">{{ $root->name }}</span>
                            @if($root->slug)
                                <code class="badge bg-soft-light text-muted fw-normal px-2 py-1 rounded">{{ $root->slug }}</code>
                            @endif
                            <span class="badge bg-soft-primary text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                <i class="feather-layers me-1"></i> {{ __('messages.main_category') }}
                            </span>
                        </div>
                        @if($root->txt)
                            <div class="text-muted small text-truncate opacity-75" style="max-width: 450px;" title="{{ $root->txt }}">
                                {{ $root->txt }}
                            </div>
                        @endif
                    </div>

                    <!-- Counters & Status -->
                    <div class="dir-tree-meta d-flex align-items-center gap-2 ms-auto">
                        <span class="badge bg-soft-info text-info rounded-pill px-3 py-1.5 fw-semibold" title="{{ __('messages.subcategories_count') ?? 'Subcategories' }}">
                            <i class="feather-git-merge me-1"></i>{{ $root->children->count() }}
                        </span>
                        <a href="{{ route('directory.index') }}?cat={{ $root->id }}" target="_blank" class="badge bg-soft-success text-success text-decoration-none rounded-pill px-3 py-1.5 fw-semibold" title="{{ __('messages.listings_count') ?? 'Directory Listings' }}">
                            <i class="feather-globe me-1"></i>{{ $root->listings_count }} {{ __('messages.web_directory') ?? 'sites' }}
                        </a>
                        <div class="form-check form-switch m-0 ms-2" title="{{ __('messages.status') ?? 'Status' }}">
                            <input class="form-check-input dir-status-toggle" type="checkbox" role="switch" data-id="{{ $root->id }}" {{ $root->statu == 1 ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="dir-tree-actions d-flex align-items-center gap-1 ms-2">
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-pill btn-quick-add-sub" data-parent-id="{{ $root->id }}" data-parent-name="{{ $root->name }}" title="{{ __('messages.add_subcategory') ?? 'Add Subcategory' }}">
                            <i class="feather-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-pill btn-edit-category" 
                            data-id="{{ $root->id }}"
                            data-name="{{ $root->name }}"
                            data-slug="{{ $root->slug }}"
                            data-icon="{{ $root->icon }}"
                            data-sub="0"
                            data-ordercat="{{ $root->ordercat }}"
                            data-statu="{{ $root->statu }}"
                            data-txt="{{ $root->txt }}"
                            data-metakeywords="{{ $root->metakeywords }}"
                            title="{{ __('messages.edit') }}">
                            <i class="feather-edit-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-light text-danger rounded-pill btn-delete-category" 
                            data-id="{{ $root->id }}"
                            data-name="{{ $root->name }}"
                            data-children-count="{{ $root->children->count() }}"
                            data-listings-count="{{ $root->listings_count }}"
                            title="{{ __('messages.delete') }}">
                            <i class="feather-trash-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Subcategories Container -->
                <div class="dir-tree-sublist collapse show" id="sublist-{{ $root->id }}">
                    <div class="dir-tree-empty-dropzone {{ $root->children->isNotEmpty() ? 'd-none' : '' }}">
                        <i class="feather-corner-down-right me-2 opacity-50"></i>
                        <span>{{ __('messages.drag_drop_hierarchy_tip') ?? 'Drop subcategories here or click + to add.' }}</span>
                    </div>

                    @foreach($root->children as $child)
                    <div class="dir-tree-node" data-id="{{ $child->id }}" data-level="sub" data-status="{{ $child->statu }}" data-name="{{ strtolower($child->name) }}" data-slug="{{ strtolower($child->slug) }}">
                        <div class="dir-tree-item dir-tree-item--sub {{ $child->statu == 0 ? 'is-disabled' : '' }}" draggable="true">
                            <div class="dir-drag-handle" title="{{ __('messages.drag_to_reorder_categories') ?? 'Drag to reorder' }}">
                                <i class="feather-move"></i>
                            </div>

                            <span class="dir-sub-connector text-muted opacity-50">
                                <i class="feather-corner-down-right"></i>
                            </span>

                            <div class="dir-tree-icon dir-tree-icon--sub bg-soft-light text-muted">
                                <i class="{{ $child->display_icon }}"></i>
                            </div>

                            <div class="dir-tree-content">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="dir-tree-title fw-bold text-dark fs-14">{{ $child->name }}</span>
                                    @if($child->slug)
                                        <code class="badge bg-soft-light text-muted fw-normal px-2 py-0.5 rounded fs-11">{{ $child->slug }}</code>
                                    @endif
                                </div>
                                @if($child->txt)
                                    <div class="text-muted small text-truncate opacity-75" style="max-width: 380px;" title="{{ $child->txt }}">
                                        {{ $child->txt }}
                                    </div>
                                @endif
                            </div>

                            <!-- Counters & Status -->
                            <div class="dir-tree-meta d-flex align-items-center gap-2 ms-auto">
                                <a href="{{ route('directory.index') }}?cat={{ $child->id }}" target="_blank" class="badge bg-soft-success text-success text-decoration-none rounded-pill px-2.5 py-1 fw-semibold fs-11" title="{{ __('messages.listings_count') ?? 'Directory Listings' }}">
                                    <i class="feather-globe me-1"></i>{{ $child->listings_count }}
                                </a>
                                <div class="form-check form-switch m-0 ms-2" title="{{ __('messages.status') ?? 'Status' }}">
                                    <input class="form-check-input dir-status-toggle" type="checkbox" role="switch" data-id="{{ $child->id }}" {{ $child->statu == 1 ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="dir-tree-actions d-flex align-items-center gap-1 ms-2">
                                <button type="button" class="btn btn-sm btn-icon btn-light rounded-pill btn-edit-category" 
                                    data-id="{{ $child->id }}"
                                    data-name="{{ $child->name }}"
                                    data-slug="{{ $child->slug }}"
                                    data-icon="{{ $child->icon }}"
                                    data-sub="{{ $root->id }}"
                                    data-ordercat="{{ $child->ordercat }}"
                                    data-statu="{{ $child->statu }}"
                                    data-txt="{{ $child->txt }}"
                                    data-metakeywords="{{ $child->metakeywords }}"
                                    title="{{ __('messages.edit') }}">
                                    <i class="feather-edit-2"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-light text-danger rounded-pill btn-delete-category" 
                                    data-id="{{ $child->id }}"
                                    data-name="{{ $child->name }}"
                                    data-children-count="0"
                                    data-listings-count="{{ $child->listings_count }}"
                                    title="{{ __('messages.delete') }}">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="card border-0 shadow-sm text-center p-5" style="border-radius: var(--admin-premium-radius); background: var(--admin-premium-surface);">
                <div class="avatar-text avatar-xl bg-soft-warning text-warning rounded-circle mx-auto mb-3" style="width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    <i class="feather-folder-plus"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ __('messages.create_first_category') ?? 'No directory categories found' }}</h5>
                <p class="text-muted mb-4">{{ __('messages.add_category_desc') ?? 'Get started by creating your first directory category.' }}</p>
                <div>
                    <button type="button" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#categoryModal" data-parent-id="0">
                        <i class="feather-plus-circle me-1"></i> {{ __('messages.add_category') }}
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Table View -->
    <div id="dirTableViewContainer" class="card border-0 shadow-sm mb-5 d-none" style="border-radius: var(--admin-premium-radius); background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
        <div class="card-header border-0 bg-transparent py-3 px-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">{{ __('messages.category_list') }}</h5>
            <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-1.5 fw-bold">
                {{ $allCategories->count() }} {{ __('messages.total') }}
            </span>
        </div>
        <div class="card-body px-0 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dirCategoriesTable">
                    <thead class="text-uppercase fs-11 fw-bold text-muted bg-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 70px;">#ID</th>
                            <th class="py-3">{{ __('messages.category') }}</th>
                            <th class="py-3">{{ __('messages.parent_category') }}</th>
                            <th class="text-center py-3">{{ __('messages.listings_count') ?? 'Listings' }}</th>
                            <th class="text-center py-3" style="width: 90px;">{{ __('messages.order') }}</th>
                            <th class="text-center py-3" style="width: 100px;">{{ __('messages.status') ?? 'Status' }}</th>
                            <th class="text-end pe-4 py-3" style="width: 140px;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @forelse($allCategories as $category)
                        <tr class="dir-table-row {{ $category->statu == 0 ? 'opacity-75' : '' }}" data-id="{{ $category->id }}" data-level="{{ $category->sub == 0 ? 'root' : 'sub' }}" data-status="{{ $category->statu }}" data-name="{{ strtolower($category->name) }}" data-slug="{{ strtolower($category->slug) }}">
                            <td class="ps-4">
                                <span class="fw-bold text-muted">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dir-tree-icon dir-tree-icon--sm {{ $category->sub == 0 ? 'bg-soft-warning text-warning' : 'bg-soft-light text-muted' }}">
                                        <i class="{{ $category->display_icon }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-0.5">{{ $category->name }}</div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($category->slug)
                                                <code class="text-muted fs-11">{{ $category->slug }}</code>
                                            @endif
                                            @if($category->txt)
                                                <span class="text-muted small opacity-75" title="{{ $category->txt }}">&bull; {{ Str::limit($category->txt, 35) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->sub == 0)
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-bold">
                                        <i class="feather-layers me-1"></i> {{ __('messages.main_category') }}
                                    </span>
                                @else
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="feather-corner-down-right me-1.5 opacity-50"></i>
                                        <span class="fw-medium text-dark">{{ $category->parent->name ?? __('messages.unknown') }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('directory.index') }}?cat={{ $category->id }}" target="_blank" class="badge bg-soft-success text-success text-decoration-none rounded-pill px-3 py-1.5 fw-bold">
                                    <i class="feather-globe me-1"></i>{{ $category->listings_count }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold rounded-pill px-2.5 py-1">{{ $category->ordercat }}</span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block m-0">
                                    <input class="form-check-input dir-status-toggle" type="checkbox" role="switch" data-id="{{ $category->id }}" {{ $category->statu == 1 ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('directory.category', $category->slug ?: $category->id) }}" target="_blank" class="btn btn-sm btn-icon btn-light rounded-pill" title="{{ __('messages.view') ?? 'View in directory' }}">
                                        <i class="feather-external-link"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-light rounded-pill ms-1 btn-edit-category"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-slug="{{ $category->slug }}"
                                        data-icon="{{ $category->icon }}"
                                        data-sub="{{ $category->sub }}"
                                        data-ordercat="{{ $category->ordercat }}"
                                        data-statu="{{ $category->statu }}"
                                        data-txt="{{ $category->txt }}"
                                        data-metakeywords="{{ $category->metakeywords }}"
                                        title="{{ __('messages.edit') }}">
                                        <i class="feather-edit-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger rounded-pill ms-1 btn-delete-category"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-children-count="{{ $category->children->count() }}"
                                        data-listings-count="{{ $category->listings_count }}"
                                        title="{{ __('messages.delete') }}">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="feather-folder-minus fs-3 d-block mb-2 opacity-50"></i>
                                {{ __('messages.no_results') ?? 'No categories found' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- ========================================== -->
<!-- Reusable Dynamic Category Modal (Add / Edit) -->
<!-- ========================================== -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius); background: var(--admin-premium-surface);">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold fs-18 text-dark" id="categoryModalTitle">{{ __('messages.new_category') }}</h5>
                    <p class="text-muted small mb-0">{{ __('messages.add_category_desc') ?? 'Configure category details, hierarchy and SEO metadata.' }}</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" action="{{ route('admin.directory_categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Category Name -->
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted small text-uppercase">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="fieldCatName" class="form-control form-control-lg bg-light border-0" placeholder="{{ __('messages.enter_category_name') }}" style="border-radius: 12px;" required>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted small text-uppercase">{{ __('messages.order') }}</label>
                            <input type="number" name="ordercat" id="fieldCatOrder" class="form-control form-control-lg bg-light border-0" value="0" style="border-radius: 12px;">
                        </div>

                        <!-- Slug -->
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted small text-uppercase d-flex align-items-center justify-content-between">
                                <span>{{ __('messages.slug') ?? 'URL Slug' }}</span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="btnAutoSlug">
                                    <i class="feather-refresh-cw me-1"></i>{{ __('messages.auto_generate') ?? 'Generate' }}
                                </button>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted ps-3" style="border-radius: 12px 0 0 12px;">
                                    /directory/
                                </span>
                                <input type="text" name="slug" id="fieldCatSlug" class="form-control form-control-lg bg-light border-0" placeholder="web-development" style="border-radius: 0 12px 12px 0;">
                            </div>
                            <div class="form-text small opacity-75">{{ __('messages.slug_hint') ?? 'Unique identifier used for SEO-friendly URLs. Leave blank to auto-generate.' }}</div>
                        </div>

                        <!-- Parent Category -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted small text-uppercase">{{ __('messages.parent_category') }}</label>
                            <select name="sub" id="fieldCatSub" class="form-select form-select-lg bg-light border-0" style="border-radius: 12px;">
                                <option value="0">{{ __('messages.main_category') }} (Root)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Icon Picker Field -->
                        <div class="col-12">
                            <div class="icon-picker-field">
                                @include('admin::admin.partials.icon_picker', ['name' => 'icon', 'selectedIcon' => ''])
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small text-uppercase">{{ __('messages.desc') }}</label>
                            <textarea name="txt" id="fieldCatTxt" class="form-control bg-light border-0" rows="2" placeholder="{{ __('messages.brief_description') }}" style="border-radius: 12px;"></textarea>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small text-uppercase">{{ __('messages.meta_keywords') }}</label>
                            <textarea name="metakeywords" id="fieldCatKeywords" class="form-control bg-light border-0" rows="2" placeholder="{{ __('messages.meta_keywords_placeholder') ?? 'web design, html, tools...' }}" style="border-radius: 12px;"></textarea>
                        </div>

                        <!-- Active Switch -->
                        <div class="col-12 pt-2">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                                <div>
                                    <div class="fw-bold text-dark fs-14">{{ __('messages.status') ?? 'Category Status' }}</div>
                                    <div class="text-muted small">{{ __('messages.category_status_hint') ?? 'Enable or disable this category from the public directory.' }}</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="statu" value="1" id="fieldCatStatus" checked role="switch" style="width: 2.5em; height: 1.25em;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-pill" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" id="btnSaveCategory">
                        <i class="feather-check me-1"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- Smart Delete Confirmation Modal -->
<!-- ========================================== -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius); background: var(--admin-premium-surface);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteCategoryForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                        <i class="feather-trash-2"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ __('messages.confirm_delete_category') }}</h4>
                    <p class="text-muted mb-3 fs-15 fw-semibold" id="deleteCatName">Category Name</p>

                    <!-- Subcategories Warning -->
                    <div id="deleteSubWarning" class="alert alert-soft-warning border-0 rounded-4 text-start mb-3 d-none">
                        <div class="d-flex align-items-center">
                            <i class="feather-alert-triangle text-warning me-2 fs-18"></i>
                            <div class="small">
                                <span class="fw-bold">{{ __('messages.warning') ?? 'Warning' }}:</span>
                                <span id="deleteSubWarningText">{{ __('messages.child_categories_reassigned_note') ?? 'Child categories under this category will automatically be moved to the root level.' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Listings Reassign Box -->
                    <div id="deleteListingsBox" class="text-start p-3 bg-light rounded-4 mb-3 d-none">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="feather-globe text-primary me-1"></i>
                            {{ __('messages.reassign_listings_to') ?? 'Reassign directory listings to' }}:
                        </label>
                        <p class="text-muted small mb-2">{{ __('messages.move_listings_note') ?? 'Choose a category to move the existing listings to before deleting.' }}</p>
                        <select name="move_to_id" id="deleteMoveToSelect" class="form-select border-0 bg-white" style="border-radius: 10px;">
                            <option value="">{{ __('messages.unlink_listings') ?? 'Unlink listings (Category ID: 0)' }}</option>
                            @foreach($allCategories as $catOption)
                                <option value="{{ $catOption->id }}">{{ $catOption->sub == 0 ? '[Root] ' : '— ' }}{{ $catOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-pill" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm" id="btnConfirmDelete">
                        <i class="feather-trash-2 me-1"></i> {{ __('messages.delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const reorderUrl = '{{ route("admin.directory_categories.reorder") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

    // -------------------------------------------------------------
    // Toast Notification Helper
    // -------------------------------------------------------------
    function showToast(status, message) {
        const toast = document.getElementById('dirToastPill');
        const content = document.getElementById('dirToastContent');
        if (!toast || !content) return;

        let icon = '<span class="spinner-border spinner-border-sm text-light me-2"></span>';
        if (status === 'success') {
            icon = '<i class="feather-check-circle text-success fs-18 me-2"></i>';
        } else if (status === 'error') {
            icon = '<i class="feather-alert-triangle text-danger fs-18 me-2"></i>';
        }

        content.innerHTML = icon + '<span class="fw-semibold">' + message + '</span>';
        toast.classList.add('is-visible');

        if (status !== 'saving') {
            setTimeout(function () {
                toast.classList.remove('is-visible');
            }, 3000);
        }
    }

    // -------------------------------------------------------------
    // View Switcher (Tree View vs Table View)
    // -------------------------------------------------------------
    const btnShowTree = document.getElementById('btnShowTree');
    const btnShowTable = document.getElementById('btnShowTable');
    const treeView = document.getElementById('dirTreeViewContainer');
    const tableView = document.getElementById('dirTableViewContainer');

    function setView(view) {
        if (view === 'table') {
            treeView.classList.add('d-none');
            tableView.classList.remove('d-none');
            btnShowTable.classList.add('active', 'btn-warning', 'text-dark');
            btnShowTable.classList.remove('btn-light');
            btnShowTree.classList.remove('active', 'btn-warning', 'text-dark');
            btnShowTree.classList.add('btn-light');
            localStorage.setItem('myads_dir_cat_view', 'table');
        } else {
            tableView.classList.add('d-none');
            treeView.classList.remove('d-none');
            btnShowTree.classList.add('active', 'btn-warning', 'text-dark');
            btnShowTree.classList.remove('btn-light');
            btnShowTable.classList.remove('active', 'btn-warning', 'text-dark');
            btnShowTable.classList.add('btn-light');
            localStorage.setItem('myads_dir_cat_view', 'tree');
        }
    }

    btnShowTree.addEventListener('click', function () { setView('tree'); });
    btnShowTable.addEventListener('click', function () { setView('table'); });

    // Restore saved view preference
    const savedView = localStorage.getItem('myads_dir_cat_view');
    if (savedView === 'table') {
        setView('table');
    }

    // Expand / Collapse All
    const btnExpandAll = document.getElementById('btnExpandAll');
    const btnCollapseAll = document.getElementById('btnCollapseAll');

    if (btnExpandAll && btnCollapseAll) {
        btnExpandAll.addEventListener('click', function () {
            document.querySelectorAll('.dir-tree-sublist').forEach(function (sub) {
                sub.classList.add('show');
            });
            document.querySelectorAll('.dir-toggle-collapse').forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'true');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'feather-chevron-down';
            });
        });

        btnCollapseAll.addEventListener('click', function () {
            document.querySelectorAll('.dir-tree-sublist').forEach(function (sub) {
                sub.classList.remove('show');
            });
            document.querySelectorAll('.dir-toggle-collapse').forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'false');
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'feather-chevron-right';
            });
        });
    }

    // Toggle collapse button per node
    document.querySelectorAll('.dir-toggle-collapse').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetSelector = this.getAttribute('data-target');
            const target = document.querySelector(targetSelector);
            if (!target) return;

            const isExpanded = target.classList.contains('show');
            const icon = this.querySelector('i');

            if (isExpanded) {
                target.classList.remove('show');
                this.setAttribute('aria-expanded', 'false');
                if (icon) icon.className = 'feather-chevron-right';
            } else {
                target.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
                if (icon) icon.className = 'feather-chevron-down';
            }
        });
    });

    // -------------------------------------------------------------
    // Live Search & Filter
    // -------------------------------------------------------------
    const searchInput = document.getElementById('dirCategorySearch');
    const filterLevel = document.getElementById('dirFilterLevel');
    const badgeCount = document.getElementById('badgeTotalFilterCount');

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const level = filterLevel.value;
        let visibleCount = 0;

        // Filter Tree Nodes
        document.querySelectorAll('#dirTreeRoot > .dir-tree-node').forEach(function (rootNode) {
            const rootName = rootNode.getAttribute('data-name') || '';
            const rootSlug = rootNode.getAttribute('data-slug') || '';
            const rootStatus = rootNode.getAttribute('data-status') || '';
            let rootMatches = query === '' || rootName.includes(query) || rootSlug.includes(query);

            let subMatchesTotal = 0;
            rootNode.querySelectorAll('.dir-tree-sublist .dir-tree-node').forEach(function (subNode) {
                const subName = subNode.getAttribute('data-name') || '';
                const subSlug = subNode.getAttribute('data-slug') || '';
                const subStatus = subNode.getAttribute('data-status') || '';
                let subMatches = query === '' || subName.includes(query) || subSlug.includes(query);

                // Level filter on sub
                if (level === 'root') subMatches = false;
                if (level === 'active' && subStatus !== '1') subMatches = false;
                if (level === 'inactive' && subStatus !== '0') subMatches = false;

                if (subMatches) {
                    subNode.classList.remove('d-none');
                    subMatchesTotal++;
                    visibleCount++;
                } else {
                    subNode.classList.add('d-none');
                }
            });

            // Level filter on root
            if (level === 'sub') rootMatches = false;
            if (level === 'active' && rootStatus !== '1') rootMatches = false;
            if (level === 'inactive' && rootStatus !== '0') rootMatches = false;

            if (rootMatches) {
                visibleCount++;
            }

            if (rootMatches || subMatchesTotal > 0) {
                rootNode.classList.remove('d-none');
                // Auto expand if search active
                if (query !== '' && subMatchesTotal > 0) {
                    const sublist = rootNode.querySelector('.dir-tree-sublist');
                    if (sublist) sublist.classList.add('show');
                }
            } else {
                rootNode.classList.add('d-none');
            }
        });

        // Filter Table Rows
        document.querySelectorAll('#dirCategoriesTable tbody tr.dir-table-row').forEach(function (row) {
            const rowName = row.getAttribute('data-name') || '';
            const rowSlug = row.getAttribute('data-slug') || '';
            const rowLevel = row.getAttribute('data-level') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            let rowMatches = query === '' || rowName.includes(query) || rowSlug.includes(query);
            if (level === 'root' && rowLevel !== 'root') rowMatches = false;
            if (level === 'sub' && rowLevel !== 'sub') rowMatches = false;
            if (level === 'active' && rowStatus !== '1') rowMatches = false;
            if (level === 'inactive' && rowStatus !== '0') rowMatches = false;

            if (rowMatches) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });

        if (badgeCount) {
            badgeCount.innerText = visibleCount + ' {{ __("messages.total") }}';
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterLevel) filterLevel.addEventListener('change', applyFilters);

    // -------------------------------------------------------------
    // Status Toggle Switch (AJAX)
    // -------------------------------------------------------------
    document.querySelectorAll('.dir-status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const catId = this.getAttribute('data-id');
            const isChecked = this.checked;
            const toggleEl = this;

            showToast('saving', '{{ __("messages.saving") ?? "Updating status..." }}');

            fetch('{{ url("/directory/categories") }}/' + catId + '/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showToast('success', data.message || '{{ __("messages.category_updated") }}');
                    // Sync other toggles with same id (between tree & table)
                    document.querySelectorAll('.dir-status-toggle[data-id="' + catId + '"]').forEach(function (t) {
                        t.checked = (data.statu == 1);
                    });
                    // Update dataset
                    document.querySelectorAll('[data-id="' + catId + '"]').forEach(function (el) {
                        el.setAttribute('data-status', data.statu);
                    });
                } else {
                    toggleEl.checked = !isChecked;
                    showToast('error', data.error || 'Failed to update status');
                }
            })
            .catch(function (err) {
                console.error(err);
                toggleEl.checked = !isChecked;
                showToast('error', 'Network error occurred');
            });
        });
    });

    // -------------------------------------------------------------
    // Dynamic Add / Edit Modal
    // -------------------------------------------------------------
    const categoryModal = document.getElementById('categoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('categoryModalTitle');
    const fieldName = document.getElementById('fieldCatName');
    const fieldSlug = document.getElementById('fieldCatSlug');
    const fieldSub = document.getElementById('fieldCatSub');
    const fieldOrder = document.getElementById('fieldCatOrder');
    const fieldTxt = document.getElementById('fieldCatTxt');
    const fieldKeywords = document.getElementById('fieldCatKeywords');
    const fieldStatus = document.getElementById('fieldCatStatus');
    const btnAutoSlug = document.getElementById('btnAutoSlug');

    // Auto Slug Generator helper
    function generateSlug(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\u0621-\u064A\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    if (btnAutoSlug) {
        btnAutoSlug.addEventListener('click', function () {
            if (fieldName.value.trim() !== '') {
                fieldSlug.value = generateSlug(fieldName.value);
            }
        });
    }

    if (fieldName) {
        fieldName.addEventListener('input', function () {
            if (categoryForm.getAttribute('data-mode') === 'create' && fieldSlug.value === '') {
                fieldSlug.value = generateSlug(fieldName.value);
            }
        });
    }

    // Reset modal on open add
    const btnOpenAdd = document.getElementById('btnOpenAddCategoryModal');
    if (btnOpenAdd) {
        btnOpenAdd.addEventListener('click', function () {
            setupModalForAdd(0);
        });
    }

    // Quick add subcategory buttons
    document.querySelectorAll('.btn-quick-add-sub').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const parentId = this.getAttribute('data-parent-id');
            setupModalForAdd(parentId);
            const modal = bootstrap.Modal.getOrCreateInstance(categoryModal);
            modal.show();
        });
    });

    function setupModalForAdd(parentId) {
        categoryForm.reset();
        categoryForm.setAttribute('data-mode', 'create');
        categoryForm.action = '{{ route("admin.directory_categories.store") }}';
        modalTitle.innerText = '{{ __("messages.new_category") }}';
        fieldSub.value = parentId || 0;
        fieldStatus.checked = true;
        fieldOrder.value = 0;

        // Reset icon picker
        resetIconPicker('');
    }

    // Edit Category buttons
    document.querySelectorAll('.btn-edit-category').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name') || '';
            const slug = this.getAttribute('data-slug') || '';
            const icon = this.getAttribute('data-icon') || '';
            const sub = this.getAttribute('data-sub') || '0';
            const ordercat = this.getAttribute('data-ordercat') || '0';
            const statu = this.getAttribute('data-statu') || '1';
            const txt = this.getAttribute('data-txt') || '';
            const metakeywords = this.getAttribute('data-metakeywords') || '';

            categoryForm.setAttribute('data-mode', 'edit');
            categoryForm.action = '{{ url("/directory/categories") }}/' + id;
            modalTitle.innerText = '{{ __("messages.edit_category") }}';

            fieldName.value = name;
            fieldSlug.value = slug;
            fieldSub.value = sub;
            fieldOrder.value = ordercat;
            fieldTxt.value = txt;
            fieldKeywords.value = metakeywords;
            fieldStatus.checked = (statu == '1');

            resetIconPicker(icon);

            const modal = bootstrap.Modal.getOrCreateInstance(categoryModal);
            modal.show();
        });
    });

    function resetIconPicker(iconValue) {
        const picker = categoryModal.querySelector('[data-icon-picker]');
        if (!picker) return;

        const nativeSelect = picker.querySelector('.icon-picker__native');
        const trigger = picker.querySelector('.icon-picker__trigger');
        const badgeIcon = picker.querySelector('.icon-picker__trigger-badge i');
        const valueNode = picker.querySelector('.icon-picker__trigger-value');

        if (nativeSelect) nativeSelect.value = iconValue;
        if (badgeIcon) badgeIcon.className = iconValue || 'fa-solid fa-icons';
        if (valueNode) valueNode.textContent = iconValue ? iconValue.replace('fa-solid fa-', '').replace('feather-', '') : (picker.getAttribute('data-placeholder') || 'Select Icon');

        picker.querySelectorAll('.icon-picker__option').forEach(function (opt) {
            opt.classList.toggle('is-selected', opt.getAttribute('data-value') === iconValue);
        });
    }

    // -------------------------------------------------------------
    // Delete Confirmation Modal
    // -------------------------------------------------------------
    const deleteModal = document.getElementById('deleteCategoryModal');
    const deleteForm = document.getElementById('deleteCategoryForm');
    const deleteName = document.getElementById('deleteCatName');
    const deleteSubWarning = document.getElementById('deleteSubWarning');
    const deleteListingsBox = document.getElementById('deleteListingsBox');
    const deleteMoveSelect = document.getElementById('deleteMoveToSelect');

    document.querySelectorAll('.btn-delete-category').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const childrenCount = parseInt(this.getAttribute('data-children-count') || 0, 10);
            const listingsCount = parseInt(this.getAttribute('data-listings-count') || 0, 10);

            deleteForm.action = '{{ url("/directory/categories") }}/' + id;
            deleteName.innerText = name;

            // Show/hide subcategories warning
            if (childrenCount > 0) {
                deleteSubWarning.classList.remove('d-none');
            } else {
                deleteSubWarning.classList.add('d-none');
            }

            // Show/hide listings reassign dropdown
            if (listingsCount > 0) {
                deleteListingsBox.classList.remove('d-none');
                // Hide current category from the target select
                if (deleteMoveSelect) {
                    Array.from(deleteMoveSelect.options).forEach(function (opt) {
                        opt.disabled = (opt.value === id);
                    });
                }
            } else {
                deleteListingsBox.classList.add('d-none');
            }

            const modal = bootstrap.Modal.getOrCreateInstance(deleteModal);
            modal.show();
        });
    });

    // -------------------------------------------------------------
    // Drag & Drop Tree Reordering Engine
    // -------------------------------------------------------------
    let draggedNode = null;
    let dropTargetItem = null;

    function initDraggables() {
        const root = document.getElementById('dirTreeRoot');
        if (!root) return;

        root.addEventListener('dragstart', function (e) {
            const item = e.target.closest('.dir-tree-item');
            if (!item) return;

            draggedNode = item.closest('.dir-tree-node');
            if (!draggedNode) return;

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', draggedNode.getAttribute('data-id'));

            setTimeout(function () {
                draggedNode.classList.add('is-dragging');
            }, 0);
        });

        root.addEventListener('dragend', function (e) {
            if (draggedNode) {
                draggedNode.classList.remove('is-dragging');
            }
            clearDropIndicators();
            draggedNode = null;
            dropTargetItem = null;
        });

        root.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            const item = e.target.closest('.dir-tree-item');
            if (!item || !draggedNode || item === draggedNode.querySelector('.dir-tree-item')) {
                return;
            }

            const targetNode = item.closest('.dir-tree-node');
            if (draggedNode.contains(targetNode)) {
                return; // Cannot drop inside its own descendant
            }

            clearDropIndicators();
            dropTargetItem = item;

            const rect = item.getBoundingClientRect();
            const relY = e.clientY - rect.top;
            const height = rect.height;

            if (relY < height * 0.3) {
                item.classList.add('drop-before');
            } else if (relY > height * 0.7) {
                item.classList.add('drop-after');
            } else {
                // If dropping into middle of root node, allow nesting
                if (targetNode.getAttribute('data-level') === 'root') {
                    item.classList.add('drop-inside');
                } else {
                    item.classList.add('drop-after');
                }
            }
        });

        root.addEventListener('dragleave', function (e) {
            const item = e.target.closest('.dir-tree-item');
            if (item && item === dropTargetItem) {
                item.classList.remove('drop-before', 'drop-after', 'drop-inside');
            }
        });

        root.addEventListener('drop', function (e) {
            e.preventDefault();
            if (!draggedNode || !dropTargetItem) return;

            const targetNode = dropTargetItem.closest('.dir-tree-node');
            if (!targetNode || draggedNode.contains(targetNode)) return;

            const isBefore = dropTargetItem.classList.contains('drop-before');
            const isInside = dropTargetItem.classList.contains('drop-inside');
            const isAfter = dropTargetItem.classList.contains('drop-after');

            clearDropIndicators();

            if (isInside && targetNode.getAttribute('data-level') === 'root') {
                // Nest inside target root node
                let sublist = targetNode.querySelector('.dir-tree-sublist');
                if (sublist) {
                    sublist.appendChild(draggedNode);
                    draggedNode.setAttribute('data-level', 'sub');
                    const treeItem = draggedNode.querySelector('.dir-tree-item');
                    if (treeItem) treeItem.classList.add('dir-tree-item--sub');
                }
            } else if (isBefore) {
                targetNode.parentNode.insertBefore(draggedNode, targetNode);
            } else if (isAfter) {
                targetNode.parentNode.insertBefore(draggedNode, targetNode.nextSibling);
            }

            updateEmptyDropzones();
            saveTreeHierarchy();
        });
    }

    function clearDropIndicators() {
        document.querySelectorAll('.dir-tree-item').forEach(function (el) {
            el.classList.remove('drop-before', 'drop-after', 'drop-inside');
        });
    }

    function updateEmptyDropzones() {
        document.querySelectorAll('.dir-tree-sublist').forEach(function (sublist) {
            const hasItems = sublist.querySelectorAll('.dir-tree-node').length > 0;
            const dropzone = sublist.querySelector('.dir-tree-empty-dropzone');
            if (dropzone) {
                dropzone.classList.toggle('d-none', hasItems);
            }
        });
    }

    function saveTreeHierarchy() {
        showToast('saving', '{{ __("messages.saving") ?? "Saving categories order..." }}');

        const items = [];
        const root = document.getElementById('dirTreeRoot');
        if (!root) return;

        // Traverse root nodes
        const rootNodes = Array.prototype.slice.call(root.children).filter(function (el) {
            return el.classList && el.classList.contains('dir-tree-node');
        });

        rootNodes.forEach(function (rootEl, rootIdx) {
            const rootId = parseInt(rootEl.getAttribute('data-id'), 10);
            items.push({
                id: rootId,
                parent_id: 0,
                order: rootIdx
            });

            // Traverse children
            const sublist = rootEl.querySelector('.dir-tree-sublist');
            if (sublist) {
                const childNodes = Array.prototype.slice.call(sublist.children).filter(function (el) {
                    return el.classList && el.classList.contains('dir-tree-node');
                });

                childNodes.forEach(function (childEl, childIdx) {
                    const childId = parseInt(childEl.getAttribute('data-id'), 10);
                    items.push({
                        id: childId,
                        parent_id: rootId,
                        order: childIdx
                    });
                });
            }
        });

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                showToast('success', data.message || '{{ __("messages.categories_reordered") }}');
            } else {
                showToast('error', data.error || 'Failed to update order');
            }
        })
        .catch(function (err) {
            console.error('Reorder error:', err);
            showToast('error', 'Error occurred while saving order');
        });
    }

    initDraggables();

    // -------------------------------------------------------------
    // Integrated Icon Picker Engine
    // -------------------------------------------------------------
    var pickers = Array.prototype.slice.call(document.querySelectorAll('[data-icon-picker]'));
    if (pickers.length) {
        pickers.forEach(function (picker) {
            var nativeSelect = picker.querySelector('.icon-picker__native');
            var trigger = picker.querySelector('.icon-picker__trigger');
            var panel = picker.querySelector('.icon-picker__panel');
            var search = picker.querySelector('.icon-picker__search');
            var results = picker.querySelector('.icon-picker__results');
            var empty = picker.querySelector('.icon-picker__empty');

            if (!trigger || !panel) return;

            // Build options cache from native select optgroups
            var groups = [];
            Array.prototype.slice.call(nativeSelect.children).forEach(function (node) {
                if (node.tagName === 'OPTGROUP') {
                    var options = [];
                    Array.prototype.slice.call(node.children).forEach(function (opt) {
                        if (opt.value) {
                            options.push({
                                value: opt.value,
                                label: opt.textContent.trim(),
                                iconClass: opt.getAttribute('data-icon') || opt.value
                            });
                        }
                    });
                    if (options.length) {
                        groups.push({ title: node.label, options: options });
                    }
                }
            });

            function renderOptions(query) {
                results.innerHTML = '';
                var matchCount = 0;
                var selectedVal = nativeSelect.value;
                var q = (query || '').toLowerCase().trim();

                groups.forEach(function (group) {
                    var matchedOptions = group.options.filter(function (opt) {
                        return !q || opt.label.toLowerCase().includes(q) || opt.value.toLowerCase().includes(q);
                    });

                    if (matchedOptions.length) {
                        matchCount += matchedOptions.length;
                        var groupEl = document.createElement('div');
                        groupEl.className = 'icon-picker__group';
                        var titleEl = document.createElement('div');
                        titleEl.className = 'icon-picker__group-title';
                        titleEl.textContent = group.title;
                        groupEl.appendChild(titleEl);

                        var optionsContainer = document.createElement('div');
                        optionsContainer.className = 'icon-picker__group-options';

                        matchedOptions.forEach(function (opt) {
                            var btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'icon-picker__option' + (opt.value === selectedVal ? ' is-selected' : '');
                            btn.setAttribute('data-value', opt.value);
                            btn.innerHTML = '<span class="icon-picker__option-badge"><i class="' + opt.iconClass + '"></i></span>' +
                                            '<span class="icon-picker__option-copy">' +
                                                '<span class="icon-picker__option-label">' + opt.label + '</span>' +
                                                '<span class="icon-picker__option-meta">' + opt.value + '</span>' +
                                            '</span>' +
                                            '<i class="feather-check icon-picker__option-check"></i>';

                            btn.addEventListener('click', function () {
                                nativeSelect.value = opt.value;
                                picker.querySelector('.icon-picker__trigger-badge i').className = opt.iconClass;
                                picker.querySelector('.icon-picker__trigger-value').textContent = opt.label;
                                closePanel();
                            });
                            optionsContainer.appendChild(btn);
                        });

                        groupEl.appendChild(optionsContainer);
                        results.appendChild(groupEl);
                    }
                });

                if (empty) {
                    empty.hidden = (matchCount > 0);
                }
            }

            function openPanel() {
                panel.hidden = false;
                picker.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                renderOptions(search ? search.value : '');
                if (search) search.focus();
            }

            function closePanel() {
                panel.hidden = true;
                picker.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }

            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                if (panel.hidden) {
                    openPanel();
                } else {
                    closePanel();
                }
            });

            if (search) {
                search.addEventListener('input', function () {
                    renderOptions(this.value);
                });
            }

            document.addEventListener('click', function (e) {
                if (!picker.contains(e.target)) {
                    closePanel();
                }
            });
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Ensure Bootstrap Modals sit above layout backdrop without interference */
.modal {
    z-index: 1060 !important;
}
.modal-backdrop {
    z-index: 1055 !important;
}

/* --- Directory Category Tree Modern Styling --- */
.dir-tree-root {
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 120px;
}
.dir-tree-node {
    position: relative;
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.dir-tree-node.is-dragging {
    opacity: 0.45;
    transform: scale(0.98);
}
.dir-tree-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: var(--admin-premium-surface, #ffffff);
    border: 1px solid var(--admin-premium-border, #e2e8f0);
    border-radius: 16px;
    transition: all 0.2s ease;
    user-select: none;
}
.dir-tree-item:hover {
    border-color: #f59e0b;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.1);
}
.dir-tree-item.is-disabled {
    opacity: 0.65;
    background: rgba(var(--nxl-primary-rgb), 0.02);
}
.dir-drag-handle {
    cursor: grab;
    color: #94a3b8;
    padding: 6px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    transition: all 0.2s ease;
}
.dir-drag-handle:hover,
.dir-drag-handle:active {
    color: #f59e0b;
    background: #fef3c7;
    cursor: grabbing;
}
.dir-tree-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.dir-tree-icon--sub {
    width: 34px;
    height: 34px;
    font-size: 15px;
    border-radius: 10px;
}
.dir-tree-icon--sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    border-radius: 8px;
}
.dir-tree-content {
    min-width: 0;
    flex: 1;
}
.dir-tree-title {
    color: var(--admin-heading-color, #1e293b);
}
.dir-tree-sublist {
    margin-inline-start: 36px;
    padding-inline-start: 18px;
    border-inline-start: 2px dashed rgba(203, 213, 225, 0.7);
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
    min-height: 10px;
    position: relative;
}
.dir-tree-item--sub {
    padding: 10px 14px;
    border-radius: 12px;
    background: rgba(var(--nxl-white-rgb), 0.6);
}
.dir-tree-empty-dropzone {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px dashed rgba(203, 213, 225, 0.6);
    font-size: 12px;
    color: #64748b;
    background: rgba(248, 250, 252, 0.5);
    display: flex;
    align-items: center;
}

/* Drop Indicators */
.dir-tree-item.drop-before {
    border-top: 3px solid #f59e0b !important;
}
.dir-tree-item.drop-after {
    border-bottom: 3px solid #f59e0b !important;
}
.dir-tree-item.drop-inside {
    background: #fffbeb !important;
    border: 2px dashed #f59e0b !important;
}

/* Floating Save Toast */
.dir-toast-pill {
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
    font-size: 14px;
    transform: translateY(120px);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: none;
}
html[dir="rtl"] .dir-toast-pill {
    right: auto;
    left: 28px;
}
.dir-toast-pill.is-visible {
    transform: translateY(0);
    opacity: 1;
}

/* --- Dark Mode Adaptation --- */
.app-skin-dark .dir-tree-item {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
.app-skin-dark .dir-tree-title {
    color: #f8fafc !important;
}
.app-skin-dark .dir-tree-item--sub {
    background: rgba(30, 41, 59, 0.7);
}
.app-skin-dark .dir-tree-sublist {
    border-inline-start-color: #475569;
}
.app-skin-dark .dir-tree-empty-dropzone {
    background: rgba(15, 23, 42, 0.4);
    border-color: #334155;
    color: #94a3b8;
}
.app-skin-dark .dir-tree-item.drop-inside {
    background: rgba(245, 158, 11, 0.15) !important;
    border-color: #f59e0b !important;
}

/* --- Integrated Icon Picker Styling --- */
.icon-picker {
    position: relative;
    width: 100%;
}
.icon-picker__native {
    display: none !important;
}
.icon-picker__trigger {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 0;
    background: #f8fafc;
    color: inherit;
    font: inherit;
    text-align: start;
    cursor: pointer;
    transition: all 0.2s ease;
}
.app-skin-dark .icon-picker__trigger {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
.icon-picker__trigger-badge {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #f59e0b;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.app-skin-dark .icon-picker__trigger-badge {
    background: #0f172a;
    color: #fbbf24;
}
.icon-picker__trigger-content {
    flex: 1;
    min-width: 0;
}
.icon-picker__trigger-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 700;
}
.icon-picker__trigger-value {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}
.app-skin-dark .icon-picker__trigger-value {
    color: #f8fafc;
}
.icon-picker__panel {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    z-index: 1060;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    padding: 12px;
    max-height: 280px;
    overflow-y: auto;
}
.app-skin-dark .icon-picker__panel {
    background: #1e293b;
    border-color: #334155;
}
.icon-picker__search {
    width: 100%;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 13px;
    margin-bottom: 10px;
}
.app-skin-dark .icon-picker__search {
    background: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
.icon-picker__group-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 6px 4px;
}
.icon-picker__group-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 6px;
    margin-bottom: 8px;
}
.icon-picker__option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: #f8fafc;
    color: inherit;
    font-size: 12px;
    cursor: pointer;
    text-align: start;
    transition: all 0.15s ease;
}
.app-skin-dark .icon-picker__option {
    background: #0f172a;
    color: #f8fafc;
}
.icon-picker__option:hover {
    background: #fef3c7;
    border-color: #fde68a;
    color: #92400e;
}
.app-skin-dark .icon-picker__option:hover {
    background: rgba(245, 158, 11, 0.2);
    border-color: #f59e0b;
    color: #fbbf24;
}
.icon-picker__option.is-selected {
    background: #f59e0b;
    color: #fff !important;
}
.icon-picker__option-badge i {
    font-size: 14px;
}
.icon-picker__option-check {
    display: none;
    margin-left: auto;
}
.icon-picker__option.is-selected .icon-picker__option-check {
    display: inline-block;
}
</style>
@endpush
