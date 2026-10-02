@extends('admin::layouts.admin')

@section('title', __('messages.knowledgebase'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $updatedThisWeek = \App\Models\Knowledgebase::where('updated_at', '>=', now()->subDays(7))->count();
    $totalArticlesForSpark = max(1, $totalArticles ?? 0);
    $totalPending = $totalPending ?? 0;
    $pendingRevisions = $pendingRevisions ?? collect();
    $allArticles = $allArticles ?? collect();
    $kbCategories = $kbCategories ?? collect();
    $categories = $categories ?? collect();
@endphp

<div class="kb-power-dashboard" id="kbAdminApp">
    <!-- Toast Notifications Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="kbToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 14px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="feather-check-circle fs-5" id="kbToastIcon"></i>
                    <span id="kbToastMessage">Success</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Hero / Filter Toolbar -->
    <div class="kb-hero">
        <div class="kb-hero-left">
            <div class="kb-hero-icon">
                <i class="feather-book-open"></i>
            </div>
            <div>
                <h1 class="kb-hero-title">{{ __('messages.knowledgebase') }}</h1>
                <p class="kb-hero-sub">{{ __('messages.kb_description') ?? 'Manage, moderate and organize platform documentation and community revisions.' }}</p>
            </div>
        </div>
        <div class="kb-hero-right">
            <a href="{{ route('admin.kb_categories') }}" class="kb-btn kb-btn-ghost">
                <i class="feather-folder me-1"></i> {{ __('messages.kb_categories') }}
            </a>
            <button type="button" class="kb-btn kb-btn-primary" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                <i class="feather-plus me-1"></i> {{ __('messages.add_article') }}
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="kb-stats">
        <div class="kb-stat">
            <div class="kb-stat-icon kb-stat-icon-indigo"><i class="feather-book-open"></i></div>
            <div class="kb-stat-body">
                <div class="kb-stat-label">{{ __('messages.total_articles') }}</div>
                <div class="kb-stat-value" id="statTotalArticles">{{ $totalArticles }}</div>
            </div>
            <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,22 15,18 30,20 45,12 60,14 75,8 90,10 100,4"/>
            </svg>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-icon kb-stat-icon-amber"><i class="feather-clock"></i></div>
            <div class="kb-stat-body">
                <div class="kb-stat-label">{{ __('messages.pending_revisions') ?? 'Pending Revisions' }}</div>
                <div class="kb-stat-value" id="statPendingRevisions">{{ $totalPending }}</div>
            </div>
            <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,24 15,16 30,18 45,10 60,12 75,6 90,8 100,2"/>
            </svg>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-icon kb-stat-icon-emerald"><i class="feather-folder"></i></div>
            <div class="kb-stat-body">
                <div class="kb-stat-label">{{ __('messages.cat_s') }}</div>
                <div class="kb-stat-value">{{ $categories->count() }}</div>
            </div>
            <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,18 15,20 30,12 45,16 60,10 75,12 90,6 100,8"/>
            </svg>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-icon kb-stat-icon-rose"><i class="feather-tag"></i></div>
            <div class="kb-stat-body">
                <div class="kb-stat-label">{{ __('messages.kb_categories_label') ?? 'KB Categories' }}</div>
                <div class="kb-stat-value">{{ $kbCategories->count() }}</div>
            </div>
            <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,20 15,14 30,16 45,8 60,12 75,4 90,6 100,2"/>
            </svg>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="kb-tabs-bar">
        <ul class="nav nav-pills kb-nav-pills" id="kbAdminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-articles-btn" data-bs-toggle="pill" data-bs-target="#tab-articles" type="button" role="tab">
                    <i class="feather-file-text me-1"></i> {{ __('messages.all_articles') ?? 'All Articles' }}
                    <span class="kb-pill-badge ms-1">{{ $totalArticles }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link position-relative" id="tab-pending-btn" data-bs-toggle="pill" data-bs-target="#tab-pending" type="button" role="tab">
                    <i class="feather-git-pull-request me-1"></i> {{ __('messages.pending_revisions') ?? 'Pending Revisions' }}
                    <span class="kb-pill-badge kb-pill-badge-warning ms-1" id="pendingBadgeCount">{{ $totalPending }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-categories-btn" data-bs-toggle="pill" data-bs-target="#tab-categories" type="button" role="tab">
                    <i class="feather-grid me-1"></i> {{ __('messages.documentation_category') }}
                    <span class="kb-pill-badge ms-1">{{ $categories->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="kbAdminTabContent">
        <!-- TAB 1: ALL ARTICLES -->
        <div class="tab-pane fade show active" id="tab-articles" role="tabpanel">
            <!-- Filter Toolbar -->
            <form action="{{ route('admin.knowledgebase') }}" method="GET" class="kb-toolbar" id="kbFilterForm">
                <div class="kb-toolbar-left">
                    <div class="kb-search">
                        <i class="feather-search"></i>
                        <input type="text" name="search" id="kbSearchInput" class="form-control" placeholder="{{ __('messages.search_placeholder') ?? 'Search articles…' }}" value="{{ request('search') }}" autocomplete="off">
                        <div class="kb-search-spinner" id="kbSearchSpinner"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>
                    </div>
                    <select name="category" id="kbFilterStoreCat" class="form-select kb-select">
                        <option value="">{{ __('messages.all_categories') ?? 'All Stores' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @if(isset($kbCategories) && $kbCategories->isNotEmpty())
                        <select name="kb_category_id" id="kbFilterTaxonomyCat" class="form-select kb-select">
                            <option value="">{{ __('messages.kb_all_categories') ?? 'All KB Categories' }}</option>
                            @foreach($kbCategories as $kbCat)
                                <option value="{{ $kbCat->id }}" {{ request('kb_category_id') == $kbCat->id ? 'selected' : '' }}>{{ $kbCat->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <select name="sort" id="kbFilterSort" class="form-select kb-select">
                        <option value="recent" {{ request('sort', 'recent') == 'recent' ? 'selected' : '' }}>{{ __('messages.sort_recent') ?? 'Recently updated' }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('messages.sort_oldest') ?? 'Oldest first' }}</option>
                        <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>{{ __('messages.sort_az') ?? 'A → Z' }}</option>
                    </select>
                    <a href="{{ route('admin.knowledgebase') }}" class="kb-btn kb-btn-ghost kb-btn-sm" id="kbResetFilters" style="{{ (request('search') || request('category') || request('kb_category_id') || request('sort')) ? '' : 'display:none;' }}">
                        <i class="feather-x me-1"></i> {{ __('messages.clear') ?? 'Clear' }}
                    </a>
                </div>
            </form>

            <div class="kb-card">
                <div class="kb-card-header">
                    <h5 class="kb-card-title">
                        {{ __('messages.articles') }}
                        <span class="kb-count-chip" id="kbTableCount">{{ $allArticles->total() }}</span>
                    </h5>
                    <div class="text-muted small">
                        {{ __('messages.showing') ?? 'Showing' }} <strong id="kbShowingCount">{{ $allArticles->count() }}</strong> {{ __('messages.of') ?? 'of' }} <strong id="kbTotalCount">{{ $allArticles->total() }}</strong>
                    </div>
                </div>
                <div class="kb-table-wrap">
                    <table class="kb-table">
                        <thead>
                            <tr>
                                <th style="width:40%">{{ __('messages.article') ?? 'Article' }}</th>
                                <th>{{ __('messages.category_fallback') }}</th>
                                <th>{{ __('messages.kb_category') }}</th>
                                <th>{{ __('messages.last_updated') ?? 'Last updated' }}</th>
                                <th class="text-end">{{ __('messages.actions') ?? 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody id="kbArticlesTableBody">
                            @forelse($allArticles as $art)
                                <tr id="kb-row-{{ $art->id }}">
                                    <td>
                                        <div class="kb-article-cell">
                                            <span class="kb-article-icon"><i class="feather-file-text"></i></span>
                                            <div>
                                                <a href="javascript:void(0);" class="kb-article-title" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $art->id }}">{{ $art->name }}</a>
                                                <div class="kb-article-snippet">{{ Str::limit(strip_tags((string)$art->o_valuer), 90) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="kb-badge kb-badge-soft-primary">{{ $art->o_mode ?? '—' }}</span></td>
                                    <td>
                                        @if($art->kbCategory)
                                            <span class="kb-badge kb-badge-soft-success"><i class="feather-tag me-1"></i>{{ $art->kbCategory->name }}</span>
                                        @else
                                            <span class="kb-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="kb-muted">{{ $art->updated_at ? \Carbon\Carbon::parse($art->updated_at)->diffForHumans() : '—' }}</td>
                                    <td class="text-end">
                                        <div class="kb-actions">
                                            <button type="button" class="kb-icon-btn kb-icon-btn-view" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $art->id }}" title="{{ __('messages.preview') }}"><i class="feather-eye"></i></button>
                                            <button type="button" class="kb-icon-btn kb-icon-btn-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $art->id }}" title="{{ __('messages.edit') }}"><i class="feather-edit-3"></i></button>
                                            <button type="button" class="kb-icon-btn kb-icon-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $art->id }}" title="{{ __('messages.delete') }}"><i class="feather-trash-2"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="kb-empty">{{ __('messages.no_results_found') ?? 'No articles found.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="kb-card-footer" id="kbPaginationWrap">
                    {{ $allArticles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- TAB 2: PENDING COMMUNITY REVISIONS -->
        <div class="tab-pane fade" id="tab-pending" role="tabpanel">
            <div class="kb-card">
                <div class="kb-card-header">
                    <div>
                        <h5 class="kb-card-title">
                            <i class="feather-git-pull-request text-warning"></i>
                            {{ __('messages.pending_revisions') ?? 'Community Suggested Edits' }}
                            <span class="kb-count-chip kb-count-chip-warning" id="kbPendingTableCount">{{ $totalPending }}</span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1">{{ __('messages.kb_pending_desc') ?? 'Review submissions proposed by contributors. Compare side-by-side diffs and safely approve or decline them.' }}</p>
                    </div>
                </div>
                <div class="kb-table-wrap">
                    <table class="kb-table">
                        <thead>
                            <tr>
                                <th style="width:35%">{{ __('messages.article') }}</th>
                                <th>{{ __('messages.category_fallback') }}</th>
                                <th>{{ __('messages.author') }}</th>
                                <th>{{ __('messages.submitted') ?? 'Submitted' }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="kbPendingTableBody">
                            @forelse($pendingRevisions as $pending)
                                @php
                                    $pAuthor = $pending->o_parent > 0 ? \App\Models\User::find($pending->o_parent) : null;
                                @endphp
                                <tr id="kb-pending-row-{{ $pending->id }}">
                                    <td>
                                        <div class="kb-article-cell">
                                            <span class="kb-article-icon kb-article-icon-pending"><i class="feather-edit"></i></span>
                                            <div>
                                                <div class="kb-article-title">{{ $pending->name }}</div>
                                                <div class="kb-article-snippet">{{ Str::limit(strip_tags((string)$pending->o_valuer), 80) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="kb-badge kb-badge-soft-primary">{{ $pending->o_mode }}</span></td>
                                    <td>
                                        <span class="kb-badge kb-badge-soft-indigo">
                                            <i class="feather-user me-1"></i>{{ $pAuthor ? $pAuthor->username : __('messages.guest') }}
                                        </span>
                                    </td>
                                    <td class="kb-muted">{{ $pending->created_at ? \Carbon\Carbon::parse($pending->created_at)->diffForHumans() : '—' }}</td>
                                    <td class="text-end">
                                        <div class="kb-actions">
                                            <button type="button" class="kb-btn kb-btn-diff kb-btn-sm kb-diff-trigger" data-id="{{ $pending->id }}" title="{{ __('messages.kb_view_diff') ?? 'View Diff' }}">
                                                <i class="feather-git-commit me-1"></i> {{ __('messages.kb_view_diff') ?? 'Diff' }}
                                            </button>
                                            <button type="button" class="kb-btn kb-btn-ghost kb-btn-sm kb-preview-trigger" data-id="{{ $pending->id }}" title="{{ __('messages.preview') }}">
                                                <i class="feather-eye me-1"></i> {{ __('messages.preview') }}
                                            </button>
                                            <button type="button" class="kb-btn kb-btn-success kb-btn-sm kb-approve-btn" data-id="{{ $pending->id }}" title="{{ __('messages.kb_approve') ?? 'Approve' }}">
                                                <i class="feather-check me-1"></i> {{ __('messages.kb_approve') ?? 'Approve' }}
                                            </button>
                                            <button type="button" class="kb-btn kb-btn-danger kb-btn-sm kb-reject-btn" data-id="{{ $pending->id }}" title="{{ __('messages.kb_reject') ?? 'Reject' }}">
                                                <i class="feather-x me-1"></i> {{ __('messages.kb_reject') ?? 'Reject' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="kb-no-pending-row">
                                    <td colspan="5" class="kb-empty">
                                        <i class="feather-check-circle fs-3 text-success d-block mb-2"></i>
                                        {{ __('messages.kb_no_pending_revisions') ?? 'No pending community revisions awaiting review.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: CATEGORIES OVERVIEW -->
        <div class="tab-pane fade" id="tab-categories" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="kb-card h-100">
                        <div class="kb-card-header">
                            <h5 class="kb-card-title">
                                <i class="feather-package text-primary"></i>
                                {{ __('messages.documentation_category') }}
                                <span class="kb-count-chip">{{ $categories->count() }}</span>
                            </h5>
                        </div>
                        <div class="kb-table-wrap">
                            <table class="kb-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.store') }}</th>
                                        <th class="text-center">{{ __('messages.articles') }}</th>
                                        <th class="text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="kb-article-icon kb-article-icon-cat"><i class="feather-folder"></i></span>
                                                    <strong>{{ $category->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-center"><span class="kb-badge kb-badge-soft-indigo">{{ $category->count }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.knowledgebase', ['category' => $category->name]) }}" class="kb-btn kb-btn-ghost kb-btn-sm">
                                                    <i class="feather-filter me-1"></i> {{ __('messages.filter') ?? 'Filter' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="kb-empty">{{ __('messages.no_categories_yet') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="kb-card h-100">
                        <div class="kb-card-header">
                            <h5 class="kb-card-title">
                                <i class="feather-tag text-success"></i>
                                {{ __('messages.kb_categories') }}
                                <span class="kb-count-chip">{{ $kbCategories->count() }}</span>
                            </h5>
                            <a href="{{ route('admin.kb_categories') }}" class="kb-btn kb-btn-ghost kb-btn-sm">
                                <i class="feather-settings me-1"></i> {{ __('messages.manage') ?? 'Manage' }}
                            </a>
                        </div>
                        <div class="kb-table-wrap">
                            <table class="kb-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.name') }}</th>
                                        <th class="text-center">{{ __('messages.sort') }}</th>
                                        <th class="text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kbCategories as $kbCat)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="kb-article-icon" style="background: rgba(16,185,129,0.12); color: #10b981;"><i class="feather-tag"></i></span>
                                                    <div>
                                                        <strong>{{ $kbCat->name }}</strong>
                                                        @if($kbCat->description)
                                                            <div class="text-muted small">{{ Str::limit($kbCat->description, 50) }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><span class="badge bg-light text-dark">{{ $kbCat->sort_order }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.knowledgebase', ['kb_category_id' => $kbCat->id]) }}" class="kb-btn kb-btn-ghost kb-btn-sm">
                                                    <i class="feather-filter me-1"></i> {{ __('messages.filter') ?? 'Filter' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="kb-empty">{{ __('messages.kb_no_categories') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    @include('theme::store.partials.kb-superdesign-formatter')
</div>
@endsection

@section('modals')
<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.new_article') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.knowledgebase.store') }}" method="POST" id="addArticleForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" required maxlength="150" style="border-radius: 12px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.category_fallback') }} (Store Product) <span class="text-danger">*</span></label>
                            <input type="text" name="o_mode" class="form-control border-soft-light bg-light" placeholder="e.g. MyProduct" required style="border-radius: 12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.kb_category') }} <small class="text-muted">({{ __('messages.optional') }})</small></label>
                            <select name="kb_category_id" class="form-select border-soft-light bg-light" style="border-radius: 12px;">
                                <option value="">{{ __('messages.kb_no_category') }}</option>
                                @foreach($kbCategories as $kbCat)
                                    <option value="{{ $kbCat->id }}">{{ $kbCat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-0">{{ __('messages.content') }} (Markdown) <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#admin-kb-add-content" style="border-radius: 8px;">
                                <i class="feather-edit me-1"></i> StackEdit
                            </button>
                        </div>
                        <textarea name="o_valuer" id="admin-kb-add-content" rows="10" class="form-control border-soft-light bg-light font-monospace" required style="border-radius: 12px; font-size: 0.9rem;"></textarea>
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

<!-- Dynamic Preview Modal -->
<div class="modal fade" id="adminPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold fs-18 text-dark" id="adminPreviewTitle">Preview Article</h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-soft-primary text-primary" id="adminPreviewProduct"></span>
                        <span class="badge bg-soft-success text-success" id="adminPreviewCategory"></span>
                        <span class="text-muted small" id="adminPreviewMeta"></span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="admin-preview-box p-4 rounded-4" id="adminPreviewContent" style="background: var(--admin-premium-surface-alt); min-height: 200px;">
                    <div class="text-center py-5 text-muted"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Visual Diff Modal -->
<div class="modal fade" id="adminDiffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold fs-18 text-dark d-flex align-items-center gap-2">
                        <i class="feather-git-commit text-warning"></i>
                        <span id="adminDiffTitle">{{ __('messages.kb_view_diff') }}</span>
                    </h5>
                    <p class="text-muted small mb-0 mt-1" id="adminDiffSubtitle">{{ __('messages.kb_diff_guide') ?? 'Red lines (-) represent current content; Green lines (+) represent proposed contributor edits.' }}</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="admin-diff-container rounded-4 p-3" id="adminDiffBody" style="background: #111827; color: #f3f4f6; font-family: 'JetBrains Mono', Consolas, monospace; font-size: 0.85rem; max-height: 520px; overflow-y: auto; direction: ltr; text-align: left;">
                    <div class="text-center py-5 text-muted"><div class="spinner-border text-warning" role="status"></div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                <div class="small text-muted" id="adminDiffAuthor"></div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.close') }}</button>
                    <button type="button" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" id="adminDiffRejectBtn" style="border-radius: 10px;">
                        <i class="feather-x me-1"></i> {{ __('messages.kb_reject') ?? 'Reject' }}
                    </button>
                    <button type="button" class="btn btn-success fw-bold px-4 py-2 shadow-sm" id="adminDiffApproveBtn" style="border-radius: 10px;">
                        <i class="feather-check me-1"></i> {{ __('messages.kb_approve') ?? 'Approve' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $displayedArticles = collect();
    if(isset($allArticles)) {
        $displayedArticles = $displayedArticles->merge($allArticles->items());
    }
    $displayedArticles = $displayedArticles->unique('id');
@endphp

@foreach($displayedArticles as $article)
<!-- View Modal -->
<div class="modal fade" id="viewArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold fs-18 text-dark">{{ $article->name }}</h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-soft-primary text-primary">{{ $article->o_mode }}</span>
                        @if($article->kbCategory)
                            <span class="badge bg-soft-success text-success">{{ $article->kbCategory->name }}</span>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <template class="admin-kb-source-markdown" id="admin-kb-source-{{ $article->id }}">{!! htmlspecialchars($article->o_valuer ?? '', ENT_QUOTES, 'UTF-8') !!}</template>
                <div class="article-content markdown-content p-4 rounded-4" id="admin-kb-view-{{ $article->id }}" style="background: var(--admin-premium-surface-alt); line-height: 1.7;"></div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" data-bs-target="#editArticleModal{{ $article->id }}" data-bs-toggle="modal" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="feather-edit-3 me-1"></i> {{ __('messages.edit') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.edit_article') }} — {{ $article->name }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.knowledgebase.update', $article->id) }}" method="POST" class="admin-edit-article-form" data-id="{{ $article->id }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ $article->name }}" class="form-control form-control-lg border-soft-light bg-light" required maxlength="150" style="border-radius: 12px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.category_fallback') }} (Store Product) <span class="text-danger">*</span></label>
                            <input type="text" name="o_mode" value="{{ $article->o_mode }}" class="form-control border-soft-light bg-light" required style="border-radius: 12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.kb_category') }} <small class="text-muted">({{ __('messages.optional') }})</small></label>
                            <select name="kb_category_id" class="form-select border-soft-light bg-light" style="border-radius: 12px;">
                                <option value="">{{ __('messages.kb_no_category') }}</option>
                                @foreach($kbCategories as $kbCat)
                                    <option value="{{ $kbCat->id }}" {{ $article->kb_category_id == $kbCat->id ? 'selected' : '' }}>{{ $kbCat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-0">{{ __('messages.content') }} (Markdown) <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#admin-kb-edit-content-{{ $article->id }}" style="border-radius: 8px;">
                                <i class="feather-edit me-1"></i> StackEdit
                            </button>
                        </div>
                        <textarea name="o_valuer" id="admin-kb-edit-content-{{ $article->id }}" rows="10" class="form-control border-soft-light bg-light font-monospace" required style="border-radius: 12px; font-size: 0.9rem;">{{ $article->o_valuer }}</textarea>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_article') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.confirm_delete_article') }}</h4>
                <p class="text-muted mb-0">{{ $article->name }}</p>
                <div class="badge bg-soft-primary text-primary mt-2">{{ $article->o_mode }}</div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger fw-bold px-4 py-2 shadow-sm admin-delete-confirm-btn" data-id="{{ $article->id }}" data-url="{{ route('admin.knowledgebase.delete', $article->id) }}" style="border-radius: 10px;">
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
(function() {
    function showToast(msg, isSuccess = true) {
        const toastEl = document.getElementById('kbToast');
        if (!toastEl) return;
        const icon = document.getElementById('kbToastIcon');
        const text = document.getElementById('kbToastMessage');
        text.innerText = msg;
        toastEl.className = 'toast align-items-center text-white border-0 shadow-lg ' + (isSuccess ? 'bg-success' : 'bg-danger');
        if (icon) icon.className = isSuccess ? 'feather-check-circle fs-5' : 'feather-alert-triangle fs-5';
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
    }

    // Markdown render for modals
    function renderMarkdownForModal(modalEl) {
        modalEl.querySelectorAll('.markdown-content').forEach(el => {
            if (!el.getAttribute('data-rendered')) {
                const id = el.id.replace('admin-kb-view-', '');
                const tmpl = document.getElementById('admin-kb-source-' + id);
                const rawMarkdown = tmpl ? tmpl.innerHTML : el.innerText;
                el.innerHTML = DOMPurify.sanitize(marked.parse(rawMarkdown));
                el.setAttribute('data-rendered', 'true');
                if (window.enhanceSuperdesignKbContent) {
                    window.enhanceSuperdesignKbContent(el);
                }
            }
        });
    }

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            renderMarkdownForModal(this);
        });
    });

    // StackEdit Integration
    const stackedit = new Stackedit();
    document.querySelectorAll('.open-stackedit').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const textarea = document.querySelector(targetId);
            const modal = this.closest('.modal-content');
            const nameInput = modal ? modal.querySelector('input[name="name"]') : null;
            const articleName = nameInput ? nameInput.value : 'Article Content';

            stackedit.openFile({
                name: articleName,
                content: { text: textarea.value }
            });

            stackedit.off('fileChange');
            stackedit.on('fileChange', (file) => {
                textarea.value = file.content.text;
            });
        });
    });

    // Real-Time Debounced AJAX Search for All Articles
    const searchInput = document.getElementById('kbSearchInput');
    const storeCatSelect = document.getElementById('kbFilterStoreCat');
    const taxCatSelect = document.getElementById('kbFilterTaxonomyCat');
    const sortSelect = document.getElementById('kbFilterSort');
    const searchSpinner = document.getElementById('kbSearchSpinner');
    const resetBtn = document.getElementById('kbResetFilters');
    let searchTimeout = null;

    function doAjaxFilter() {
        if (searchSpinner) searchSpinner.style.display = 'block';
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) params.append('search', searchInput.value.trim());
        if (storeCatSelect && storeCatSelect.value) params.append('category', storeCatSelect.value);
        if (taxCatSelect && taxCatSelect.value) params.append('kb_category_id', taxCatSelect.value);
        if (sortSelect && sortSelect.value) params.append('sort', sortSelect.value);

        if (resetBtn) {
            resetBtn.style.display = (params.toString().length > 0) ? 'inline-flex' : 'none';
        }

        fetch('{{ route('admin.knowledgebase') }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (searchSpinner) searchSpinner.style.display = 'none';
            if (data.success) {
                const tbody = document.getElementById('kbArticlesTableBody');
                const countBadge = document.getElementById('kbTableCount');
                const showCount = document.getElementById('kbShowingCount');
                const totalCount = document.getElementById('kbTotalCount');
                const pagWrap = document.getElementById('kbPaginationWrap');

                if (countBadge) countBadge.innerText = data.total;
                if (showCount) showCount.innerText = data.articles.length;
                if (totalCount) totalCount.innerText = data.total;
                if (pagWrap) pagWrap.innerHTML = data.pagination || '';

                if (tbody) {
                    if (data.articles.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="kb-empty">{{ __('messages.no_results_found') ?? 'No articles found.' }}</td></tr>';
                    } else {
                        tbody.innerHTML = data.articles.map(art => {
                            const catBadge = art.kb_category ? `<span class="kb-badge kb-badge-soft-success"><i class="feather-tag me-1"></i>${art.kb_category.name}</span>` : '<span class="kb-muted">—</span>';
                            const preview = (art.o_valuer || '').replace(/<[^>]*>?/gm, '').substring(0, 90);
                            return `
                                <tr id="kb-row-${art.id}">
                                    <td>
                                        <div class="kb-article-cell">
                                            <span class="kb-article-icon"><i class="feather-file-text"></i></span>
                                            <div>
                                                <a href="javascript:void(0);" class="kb-article-title" data-bs-toggle="modal" data-bs-target="#viewArticleModal${art.id}">${art.name}</a>
                                                <div class="kb-article-snippet">${preview}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="kb-badge kb-badge-soft-primary">${art.o_mode || '—'}</span></td>
                                    <td>${catBadge}</td>
                                    <td class="kb-muted">${art.updated_at ? new Date(art.updated_at).toLocaleDateString() : '—'}</td>
                                    <td class="text-end">
                                        <div class="kb-actions">
                                            <button type="button" class="kb-icon-btn kb-icon-btn-view" data-bs-toggle="modal" data-bs-target="#viewArticleModal${art.id}" title="{{ __('messages.preview') }}"><i class="feather-eye"></i></button>
                                            <button type="button" class="kb-icon-btn kb-icon-btn-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal${art.id}" title="{{ __('messages.edit') }}"><i class="feather-edit-3"></i></button>
                                            <button type="button" class="kb-icon-btn kb-icon-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal${art.id}" title="{{ __('messages.delete') }}"><i class="feather-trash-2"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }
                }
            }
        })
        .catch(() => {
            if (searchSpinner) searchSpinner.style.display = 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(doAjaxFilter, 280);
        });
    }
    if (storeCatSelect) storeCatSelect.addEventListener('change', doAjaxFilter);
    if (taxCatSelect) taxCatSelect.addEventListener('change', doAjaxFilter);
    if (sortSelect) sortSelect.addEventListener('change', doAjaxFilter);

    // Moderation: Approve Pending Revision
    document.querySelectorAll('.kb-approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!confirm('{{ __('messages.kb_approve_confirm') ?? 'Are you sure you want to approve this community revision?' }}')) return;

            fetch('{{ url('/admin/knowledgebase/approve-pending') }}/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message);
                    const row = document.getElementById('kb-pending-row-' + id);
                    if (row) row.remove();
                    updatePendingCounters(-1);
                } else {
                    showToast('Failed to approve', false);
                }
            })
            .catch(() => showToast('Error processing request', false));
        });
    });

    // Moderation: Reject Pending Revision
    document.querySelectorAll('.kb-reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!confirm('{{ __('messages.kb_reject_confirm') ?? 'Are you sure you want to reject this revision?' }}')) return;

            fetch('{{ url('/admin/knowledgebase/reject-pending') }}/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message);
                    const row = document.getElementById('kb-pending-row-' + id);
                    if (row) row.remove();
                    updatePendingCounters(-1);
                } else {
                    showToast('Failed to reject', false);
                }
            })
            .catch(() => showToast('Error processing request', false));
        });
    });

    function updatePendingCounters(diff) {
        const badge = document.getElementById('pendingBadgeCount');
        const stat = document.getElementById('statPendingRevisions');
        const tableBadge = document.getElementById('kbPendingTableCount');
        let current = parseInt(badge ? badge.innerText : '0', 10);
        let newVal = Math.max(0, current + diff);
        if (badge) badge.innerText = newVal;
        if (stat) stat.innerText = newVal;
        if (tableBadge) tableBadge.innerText = newVal;
        if (newVal === 0) {
            const tbody = document.getElementById('kbPendingTableBody');
            if (tbody) {
                tbody.innerHTML = `<tr id="kb-no-pending-row"><td colspan="5" class="kb-empty"><i class="feather-check-circle fs-3 text-success d-block mb-2"></i>{{ __('messages.kb_no_pending_revisions') ?? 'No pending community revisions awaiting review.' }}</td></tr>`;
            }
        }
    }

    // Dynamic Preview Modal Handler
    document.querySelectorAll('.kb-preview-trigger').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const previewModal = new bootstrap.Modal(document.getElementById('adminPreviewModal'));
            const contentBox = document.getElementById('adminPreviewContent');
            contentBox.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary" role="status"></div></div>';
            previewModal.show();

            fetch('{{ url('/admin/knowledgebase/article-preview') }}/' + id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('adminPreviewTitle').innerText = res.name;
                    document.getElementById('adminPreviewProduct').innerText = res.product;
                    const catEl = document.getElementById('adminPreviewCategory');
                    if (res.category) {
                        catEl.innerText = res.category;
                        catEl.style.display = 'inline-block';
                    } else {
                        catEl.style.display = 'none';
                    }
                    document.getElementById('adminPreviewMeta').innerText = `By ${res.author} • ${res.updated_at || ''}`;
                    contentBox.innerHTML = DOMPurify.sanitize(marked.parse(res.content || ''));
                    if (window.enhanceSuperdesignKbContent) {
                        window.enhanceSuperdesignKbContent(contentBox);
                    }
                }
            })
            .catch(() => {
                contentBox.innerHTML = '<div class="alert alert-danger">Error loading article preview.</div>';
            });
        });
    });

    // Dynamic Diff Modal Handler
    let activeDiffEntryId = null;
    document.querySelectorAll('.kb-diff-trigger').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            activeDiffEntryId = id;
            const diffModal = new bootstrap.Modal(document.getElementById('adminDiffModal'));
            const diffBody = document.getElementById('adminDiffBody');
            diffBody.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-warning" role="status"></div></div>';
            diffModal.show();

            fetch('{{ url('/admin/knowledgebase/diff') }}/' + id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('adminDiffTitle').innerText = `Diff: ${res.articleName} (${res.product})`;
                    document.getElementById('adminDiffAuthor').innerText = `Contributor: ${res.author}`;
                    renderDiffView(res.currentText, res.newText, diffBody);
                }
            })
            .catch(() => {
                diffBody.innerHTML = '<div class="alert alert-danger">Error generating diff comparison.</div>';
            });
        });
    });

    function renderDiffView(oldStr, newStr, targetEl) {
        const oldLines = (oldStr || '').split('\n');
        const newLines = (newStr || '').split('\n');
        let html = '';
        const maxLen = Math.max(oldLines.length, newLines.length);

        for (let i = 0; i < maxLen; i++) {
            const o = oldLines[i];
            const n = newLines[i];
            if (o === undefined) {
                html += `<div style="background: rgba(16,185,129,0.18); color: #34d399; padding: 2px 10px;">+ ${escapeHtml(n)}</div>`;
            } else if (n === undefined) {
                html += `<div style="background: rgba(239,68,68,0.18); color: #f87171; padding: 2px 10px;">- ${escapeHtml(o)}</div>`;
            } else if (o !== n) {
                html += `<div style="background: rgba(239,68,68,0.18); color: #f87171; padding: 2px 10px;">- ${escapeHtml(o)}</div>`;
                html += `<div style="background: rgba(16,185,129,0.18); color: #34d399; padding: 2px 10px;">+ ${escapeHtml(n)}</div>`;
            } else {
                html += `<div style="color: #9ca3af; padding: 2px 10px;">  ${escapeHtml(o)}</div>`;
            }
        }
        targetEl.innerHTML = html || '<div class="p-3 text-muted">No line changes detected.</div>';
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }

    const diffApproveBtn = document.getElementById('adminDiffApproveBtn');
    if (diffApproveBtn) {
        diffApproveBtn.addEventListener('click', () => {
            if (!activeDiffEntryId) return;
            const diffModal = bootstrap.Modal.getInstance(document.getElementById('adminDiffModal'));
            if (diffModal) diffModal.hide();
            const btn = document.querySelector(`.kb-approve-btn[data-id="${activeDiffEntryId}"]`);
            if (btn) btn.click();
        });
    }

    const diffRejectBtn = document.getElementById('adminDiffRejectBtn');
    if (diffRejectBtn) {
        diffRejectBtn.addEventListener('click', () => {
            if (!activeDiffEntryId) return;
            const diffModal = bootstrap.Modal.getInstance(document.getElementById('adminDiffModal'));
            if (diffModal) diffModal.hide();
            const btn = document.querySelector(`.kb-reject-btn[data-id="${activeDiffEntryId}"]`);
            if (btn) btn.click();
        });
    }

    // AJAX Delete Confirmation
    document.querySelectorAll('.admin-delete-confirm-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            const modalEl = document.getElementById('deleteArticleModal' + id);
            const bsModal = bootstrap.Modal.getInstance(modalEl);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (bsModal) bsModal.hide();
                if (res.success) {
                    showToast(res.message);
                    const row = document.getElementById('kb-row-' + id);
                    if (row) row.remove();
                    const stat = document.getElementById('statTotalArticles');
                    const tblCount = document.getElementById('kbTableCount');
                    if (stat) stat.innerText = Math.max(0, parseInt(stat.innerText || '0', 10) - 1);
                    if (tblCount) tblCount.innerText = Math.max(0, parseInt(tblCount.innerText || '0', 10) - 1);
                } else {
                    showToast('Failed to delete', false);
                }
            })
            .catch(() => {
                if (bsModal) bsModal.hide();
                showToast('Error deleting article', false);
            });
        });
    });

})();
</script>
@endpush

@push('styles')
<style>
    .markdown-content h1, .markdown-content h2, .markdown-content h3 { margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 700; }
    .markdown-content p { margin-bottom: 0.75rem; }
    .markdown-content pre { background: #161b28; color: #e2e8f0; padding: 1rem; border-radius: 12px; overflow-x: auto; margin-bottom: 1rem; }
    .markdown-content code { background: rgba(97,93,250,0.1); color: #615dfa; padding: 2px 6px; border-radius: 6px; }

    /* KB Power-User Dashboard */
    .kb-power-dashboard { display: flex; flex-direction: column; gap: 1.25rem; }

    /* Hero */
    .kb-hero {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap;
        background: var(--admin-premium-surface, #fff);
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15));
        border-radius: 18px;
        padding: 1.25rem 1.75rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .kb-hero-left { display: flex; align-items: center; gap: 1.2rem; }
    .kb-hero-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #615dfa 0%, #8b7cff 100%);
        color: #fff; font-size: 24px;
        box-shadow: 0 8px 18px rgba(97,93,250,0.28);
    }
    .kb-hero-title { font-size: 1.6rem; font-weight: 800; margin: 0; color: var(--admin-premium-text, #1e293b); }
    .kb-hero-sub { margin: 0; color: var(--admin-premium-muted, #64748b); font-size: 0.9rem; }
    .kb-hero-right { display: flex; gap: 0.6rem; }

    /* Buttons */
    .kb-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.55rem 1.15rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600;
        border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all 0.15s ease;
        line-height: 1.2; white-space: nowrap;
    }
    .kb-btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8125rem; }
    .kb-btn-primary { background: #615dfa; color: #fff; border-color: #615dfa; box-shadow: 0 4px 12px rgba(97,93,250,0.25); }
    .kb-btn-primary:hover { background: #5048e8; color: #fff; transform: translateY(-1px); }
    .kb-btn-ghost { background: transparent; color: var(--admin-premium-text, #334155); border-color: var(--admin-premium-border, rgba(143,145,172,0.25)); }
    .kb-btn-ghost:hover { background: rgba(97,93,250,0.06); color: #615dfa; border-color: #615dfa; }
    .kb-btn-diff { background: rgba(245,158,11,0.12); color: #d97706; border-color: rgba(245,158,11,0.25); }
    .kb-btn-diff:hover { background: #f59e0b; color: #fff; }
    .kb-btn-success { background: #10b981; color: #fff; border-color: #10b981; }
    .kb-btn-success:hover { background: #059669; color: #fff; }
    .kb-btn-danger { background: #ef4444; color: #fff; border-color: #ef4444; }
    .kb-btn-danger:hover { background: #dc2626; color: #fff; }

    /* Tabs Bar */
    .kb-tabs-bar {
        background: var(--admin-premium-surface, #fff);
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15));
        border-radius: 14px;
        padding: 6px;
    }
    .kb-nav-pills .nav-link {
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--admin-premium-muted, #64748b);
        padding: 8px 18px;
        transition: all 0.2s ease;
    }
    .kb-nav-pills .nav-link.active {
        background: #615dfa;
        color: #fff;
        box-shadow: 0 4px 12px rgba(97,93,250,0.25);
    }
    .kb-pill-badge {
        display: inline-block;
        padding: 2px 7px;
        font-size: 0.72rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.25);
        color: inherit;
        font-weight: 800;
    }
    .kb-nav-pills .nav-link:not(.active) .kb-pill-badge {
        background: rgba(97,93,250,0.1);
        color: #615dfa;
    }
    .kb-pill-badge-warning {
        background: #f59e0b !important;
        color: #fff !important;
    }

    /* Toolbar */
    .kb-toolbar {
        background: var(--admin-premium-surface, #fff);
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15));
        border-radius: 14px; padding: 0.85rem 1.15rem;
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem;
    }
    .kb-toolbar-left { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; flex: 1; }
    .kb-search {
        position: relative; flex: 1; min-width: 250px; max-width: 380px;
    }
    .kb-search i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--admin-premium-muted, #94a3b8); font-size: 15px; pointer-events: none;
    }
    .kb-search .form-control {
        padding-left: 40px; height: 40px; border-radius: 10px;
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.2)); background: var(--admin-premium-surface-alt, #f8fafc);
        color: var(--admin-premium-text, #1e293b); font-size: 0.875rem;
    }
    .kb-search .form-control:focus { background: #fff; border-color: #615dfa; box-shadow: 0 0 0 3px rgba(97,93,250,0.15); }
    .kb-search-spinner {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        display: none;
    }
    .kb-select {
        height: 40px; border-radius: 10px; border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.2));
        background: var(--admin-premium-surface-alt, #f8fafc); color: var(--admin-premium-text, #1e293b);
        font-size: 0.875rem; padding: 0 0.85rem; min-width: 170px;
    }
    .kb-select:focus { border-color: #615dfa; box-shadow: 0 0 0 3px rgba(97,93,250,0.15); }

    /* Stats */
    .kb-stats {
        display: grid; gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    .kb-stat {
        position: relative; overflow: hidden;
        background: var(--admin-premium-surface, #fff);
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15));
        border-radius: 16px; padding: 1.15rem 1.35rem;
        display: flex; align-items: center; gap: 1rem;
        box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .kb-stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 20px; flex-shrink: 0;
    }
    .kb-stat-icon-indigo { background: linear-gradient(135deg, #615dfa, #8b7cff); }
    .kb-stat-icon-emerald { background: linear-gradient(135deg, #10b981, #34d399); }
    .kb-stat-icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .kb-stat-icon-rose { background: linear-gradient(135deg, #f43f5e, #fb7185); }
    .kb-stat-body { display: flex; flex-direction: column; }
    .kb-stat-label { font-size: 0.75rem; color: var(--admin-premium-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; }
    .kb-stat-value { font-size: 1.65rem; font-weight: 800; color: var(--admin-premium-text, #1e293b); line-height: 1.1; margin-top: 2px; }
    .kb-sparkline { position: absolute; right: 10px; bottom: 8px; width: 75px; height: 26px; opacity: 0.16; color: #615dfa; }

    /* Card */
    .kb-card {
        background: var(--admin-premium-surface, #fff);
        border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15));
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02);
    }
    .kb-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.15rem 1.5rem; border-bottom: 1px solid var(--admin-premium-border, rgba(143,145,172,0.12));
    }
    .kb-card-title { margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--admin-premium-text, #1e293b); display: inline-flex; align-items: center; gap: 0.6rem; }
    .kb-count-chip {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 24px; height: 22px; padding: 0 8px; border-radius: 999px;
        background: rgba(97,93,250,0.12); color: #615dfa; font-size: 0.72rem; font-weight: 800;
    }
    .kb-count-chip-warning {
        background: rgba(245,158,11,0.15);
        color: #d97706;
    }
    .kb-card-footer { padding: 0.85rem 1.5rem; border-top: 1px solid var(--admin-premium-border, rgba(143,145,172,0.12)); }

    /* Table */
    .kb-table-wrap { overflow-x: auto; }
    .kb-table { width: 100%; border-collapse: collapse; }
    .kb-table thead th {
        text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--admin-premium-muted, #64748b); font-weight: 800;
        padding: 0.85rem 1.5rem; background: var(--admin-premium-surface-alt, #f8fafc);
        border-bottom: 1px solid var(--admin-premium-border, rgba(143,145,172,0.12));
    }
    .kb-table tbody td {
        padding: 0.95rem 1.5rem; border-bottom: 1px solid var(--admin-premium-border, rgba(143,145,172,0.08));
        vertical-align: middle; color: var(--admin-premium-text, #1e293b); font-size: 0.875rem;
    }
    .kb-table tbody tr:last-child td { border-bottom: 0; }
    .kb-table tbody tr:hover td { background: var(--admin-premium-surface-alt, #f8fafc); }
    .kb-empty { text-align: center; color: var(--admin-premium-muted, #94a3b8); padding: 3rem !important; }
    .kb-muted { color: var(--admin-premium-muted, #64748b); }

    /* Article cell */
    .kb-article-cell { display: flex; align-items: center; gap: 0.85rem; }
    .kb-article-icon {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(97,93,250,0.1); color: #615dfa; font-size: 16px;
    }
    .kb-article-icon-pending { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .kb-article-icon-cat { background: rgba(14,165,233,0.12); color: #0ea5e9; }
    .kb-article-title {
        display: block; font-weight: 700; color: var(--admin-premium-text, #1e293b); text-decoration: none;
        line-height: 1.35;
    }
    .kb-article-title:hover { color: #615dfa; }
    .kb-article-snippet {
        font-size: 0.78rem; color: var(--admin-premium-muted, #64748b); margin-top: 2px;
        max-width: 440px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    /* Badges */
    .kb-badge {
        display: inline-block; padding: 3px 10px; border-radius: 8px; font-size: 0.72rem; font-weight: 700;
    }
    .kb-badge-soft-primary { background: rgba(97,93,250,0.1); color: #615dfa; }
    .kb-badge-soft-success { background: rgba(16,185,129,0.1); color: #10b981; }
    .kb-badge-soft-indigo { background: rgba(99,102,241,0.1); color: #6366f1; }

    /* Actions */
    .kb-actions { display: inline-flex; gap: 0.35rem; }
    .kb-icon-btn {
        width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.2));
        background: var(--admin-premium-surface-alt, #f8fafc); color: var(--admin-premium-muted, #64748b);
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px;
        transition: all 0.15s ease;
    }
    .kb-icon-btn:hover { background: #fff; transform: translateY(-1px); }
    .kb-icon-btn-view:hover { color: #0ea5e9; border-color: #0ea5e9; }
    .kb-icon-btn-primary { color: #615dfa; }
    .kb-icon-btn-primary:hover { background: rgba(97,93,250,0.1); border-color: #615dfa; }
    .kb-icon-btn-danger { color: #ef4444; }
    .kb-icon-btn-danger:hover { background: rgba(239,68,68,0.1); border-color: #ef4444; }
</style>
@endpush
