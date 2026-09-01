@extends('admin::layouts.admin')

@section('title', __('messages.knowledgebase'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $updatedThisWeek = \App\Models\Knowledgebase::where('updated_at', '>=', now()->subDays(7))->count();
    $totalArticlesForSpark = max(1, $totalArticles);
@endphp
<div class="kb-power-dashboard">
    <!-- Hero / Filter Toolbar -->
    <div class="kb-hero">
        <div class="kb-hero-left">
            <div class="kb-hero-icon">
                <i class="feather-book-open"></i>
            </div>
            <div>
                <h1 class="kb-hero-title">{{ __('messages.knowledgebase') }}</h1>
                <p class="kb-hero-sub">{{ __('messages.kb_description') ?? 'A premium web applications with integrate knowledge base.' }}</p>
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

    <!-- Filter Toolbar -->
    <form action="{{ route('admin.knowledgebase') }}" method="GET" class="kb-toolbar">
        <div class="kb-toolbar-left">
            <div class="kb-search">
                <i class="feather-search"></i>
                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_placeholder') ?? 'Search articles…' }}" value="{{ request('search') }}">
            </div>
            <select name="category" class="form-select kb-select" onchange="this.form.submit()">
                <option value="">{{ __('messages.all_categories') ?? 'All categories' }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="form-select kb-select" onchange="this.form.submit()">
                <option value="recent" {{ request('sort', 'recent') == 'recent' ? 'selected' : '' }}>{{ __('messages.sort_recent') ?? 'Recently updated' }}</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('messages.sort_oldest') ?? 'Oldest first' }}</option>
                <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>{{ __('messages.sort_az') ?? 'A → Z' }}</option>
            </select>
            @if(request('search') || request('category') || request('sort'))
                <a href="{{ route('admin.knowledgebase') }}" class="kb-btn kb-btn-ghost kb-btn-sm">
                    <i class="feather-x me-1"></i> {{ __('messages.clear') ?? 'Clear' }}
                </a>
            @endif
        </div>
    </form>

    @if(isset($searchResults))
        <!-- Search Results Table -->
        <div class="kb-card">
            <div class="kb-card-header">
                <h5 class="kb-card-title">
                    {{ __('messages.search_results') }}
                    <span class="kb-count-chip">{{ $searchResults->total() }}</span>
                </h5>
            </div>
            <div class="kb-table-wrap">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th style="width:50%">{{ __('messages.article') ?? 'Article' }}</th>
                            <th>{{ __('messages.category_fallback') }}</th>
                            <th>{{ __('messages.kb_category') }}</th>
                            <th class="text-end">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($searchResults as $article)
                            <tr>
                                <td>
                                    <div class="kb-article-cell">
                                        <span class="kb-article-icon"><i class="feather-file-text"></i></span>
                                        <div>
                                            <a href="javascript:void(0);" class="kb-article-title" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">{{ $article->name }}</a>
                                            <div class="kb-article-snippet">{{ Str::limit(strip_tags($article->o_valuer), 90) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="kb-badge kb-badge-soft-primary">{{ $article->o_mode ?? '—' }}</span></td>
                                <td>
                                    @if($article->kbCategory)
                                        <span class="kb-badge kb-badge-soft-success">{{ $article->kbCategory->name }}</span>
                                    @else
                                        <span class="kb-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="kb-actions">
                                        <button class="kb-icon-btn kb-icon-btn-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}" title="{{ __('messages.edit') }}"><i class="feather-edit-3"></i></button>
                                        <button class="kb-icon-btn kb-icon-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}" title="{{ __('messages.delete') }}"><i class="feather-trash-2"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="kb-empty">{{ __('messages.no_results_found') ?? 'No articles found.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="kb-card-footer">{{ $searchResults->links('pagination::bootstrap-5') }}</div>
        </div>
    @else
        <!-- Quick Stats -->
        <div class="kb-stats">
            <div class="kb-stat">
                <div class="kb-stat-icon kb-stat-icon-indigo"><i class="feather-book-open"></i></div>
                <div class="kb-stat-body">
                    <div class="kb-stat-label">{{ __('messages.total_articles') }}</div>
                    <div class="kb-stat-value">{{ $totalArticles }}</div>
                </div>
                <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,22 15,18 30,20 45,12 60,14 75,8 90,10 100,4"/>
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
                <div class="kb-stat-icon kb-stat-icon-amber"><i class="feather-clock"></i></div>
                <div class="kb-stat-body">
                    <div class="kb-stat-label">{{ __('messages.updated_this_week') ?? 'Updated this week' }}</div>
                    <div class="kb-stat-value">{{ $updatedThisWeek }}</div>
                </div>
                <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,24 15,16 30,18 45,10 60,12 75,6 90,8 100,2"/>
                </svg>
            </div>
            <div class="kb-stat">
                <div class="kb-stat-icon kb-stat-icon-rose"><i class="feather-eye"></i></div>
                <div class="kb-stat-body">
                    <div class="kb-stat-label">{{ __('messages.kb_categories_label') ?? 'KB Categories' }}</div>
                    <div class="kb-stat-value">{{ $kbCategories->count() }}</div>
                </div>
                <svg class="kb-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,20 15,14 30,16 45,8 60,12 75,4 90,6 100,2"/>
                </svg>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="kb-card">
            <div class="kb-card-header">
                <h5 class="kb-card-title">
                    {{ __('messages.documentation_category') }}
                    <span class="kb-count-chip">{{ $categories->count() }}</span>
                </h5>
            </div>
            <div class="kb-table-wrap">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th style="width:40%">{{ __('messages.category_fallback') }}</th>
                            <th>{{ __('messages.articles') ?? 'Articles' }}</th>
                            <th>{{ __('messages.last_updated') ?? 'Last updated' }}</th>
                            <th class="text-end">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            @php
                                $lastArticle = $category->articles->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="kb-article-cell">
                                        <span class="kb-article-icon kb-article-icon-cat"><i class="feather-folder"></i></span>
                                        <div>
                                            <a href="javascript:void(0);" class="kb-article-title">{{ $category->name }}</a>
                                            <div class="kb-article-snippet">{{ $category->count }} {{ __('messages.topics') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="kb-badge kb-badge-soft-indigo">{{ $category->count }}</span></td>
                                <td class="kb-muted">{{ $lastArticle && $lastArticle->updated_at ? \Carbon\Carbon::parse($lastArticle->updated_at)->diffForHumans() : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.knowledgebase', ['category' => $category->name]) }}" class="kb-btn kb-btn-ghost kb-btn-sm">
                                        <i class="feather-eye me-1"></i> {{ __('messages.view') ?? 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="kb-empty">{{ __('messages.no_categories_yet') ?? 'No categories yet.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trending Articles Table -->
        <div class="kb-card">
            <div class="kb-card-header">
                <h5 class="kb-card-title">
                    {{ __('messages.trending_topics') }}
                    <span class="kb-count-chip">{{ $latestArticles->count() }}</span>
                </h5>
            </div>
            <div class="kb-table-wrap">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th style="width:45%">{{ __('messages.article') ?? 'Article' }}</th>
                            <th>{{ __('messages.category_fallback') }}</th>
                            <th>{{ __('messages.last_updated') ?? 'Last updated' }}</th>
                            <th class="text-end">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestArticles as $article)
                            <tr>
                                <td>
                                    <div class="kb-article-cell">
                                        <span class="kb-article-icon"><i class="feather-file-text"></i></span>
                                        <div>
                                            <a href="javascript:void(0);" class="kb-article-title" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">{{ $article->name }}</a>
                                            <div class="kb-article-snippet">{{ Str::limit(strip_tags($article->o_valuer), 80) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="kb-badge kb-badge-soft-primary">{{ $article->o_mode ?? '—' }}</span>
                                    @if($article->kbCategory)
                                        <span class="kb-badge kb-badge-soft-success ms-1">{{ $article->kbCategory->name }}</span>
                                    @endif
                                </td>
                                <td class="kb-muted">{{ $article->updated_at ? \Carbon\Carbon::parse($article->updated_at)->diffForHumans() : '—' }}</td>
                                <td class="text-end">
                                    <div class="kb-actions">
                                        <button class="kb-icon-btn kb-icon-btn-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}" title="{{ __('messages.edit') }}"><i class="feather-edit-3"></i></button>
                                        <button class="kb-icon-btn kb-icon-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}" title="{{ __('messages.delete') }}"><i class="feather-trash-2"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('modals')
<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.new_article') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.knowledgebase.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.title') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.category_fallback') }}</label>
                        <input type="text" name="o_mode" class="form-control" placeholder="e.g. Getting Started" required>
                    </div>
                    @if(isset($kbCategories) && $kbCategories->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.kb_category') }} <small class="text-muted">({{ __('messages.optional') }})</small></label>
                            <select name="kb_category_id" class="form-select">
                                <option value="">{{ __('messages.kb_no_category') }}</option>
                                @foreach($kbCategories as $kbCat)
                                    <option value="{{ $kbCat->id }}">{{ $kbCat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.content') }}</label>
                        <div class="stackedit-tools mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#admin-kb-add-content">
                                <i class="feather-edit me-1"></i> {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                            </button>
                        </div>
                        <textarea name="o_valuer" id="admin-kb-add-content" rows="10" class="form-control" required></textarea>
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

@php
    $displayedArticles = collect();
    if(isset($searchResults)) {
        $displayedArticles = $searchResults;
    } else {
        foreach($categories as $cat) {
            $displayedArticles = $displayedArticles->merge($cat->articles);
        }
        $displayedArticles = $displayedArticles->merge($latestArticles);
    }
    $displayedArticles = $displayedArticles->unique('id');
@endphp

@foreach($displayedArticles as $article)
<!-- View Modal -->
<div class="modal fade" id="viewArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $article->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span class="badge bg-soft-primary mb-3">{{ $article->o_mode }}</span>
                @if($article->kbCategory)
                    <span class="badge bg-soft-success mb-3">{{ $article->kbCategory->name }}</span>
                @endif
                <div class="article-content markdown-content" id="admin-kb-view-{{ $article->id }}">{!! $article->o_valuer !!}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button class="btn btn-primary" data-bs-target="#editArticleModal{{ $article->id }}" data-bs-toggle="modal" data-bs-dismiss="modal">{{ __('messages.edit') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_article') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.knowledgebase.update', $article->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.title') }}</label>
                        <input type="text" name="name" value="{{ $article->name }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.category_fallback') }}</label>
                        <input type="text" name="o_mode" value="{{ $article->o_mode }}" class="form-control" required>
                    </div>
                    @if(isset($kbCategories) && $kbCategories->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.kb_category') }} <small class="text-muted">({{ __('messages.optional') }})</small></label>
                            <select name="kb_category_id" class="form-select">
                                <option value="">{{ __('messages.kb_no_category') }}</option>
                                @foreach($kbCategories as $kbCat)
                                    <option value="{{ $kbCat->id }}" {{ $article->kb_category_id == $kbCat->id ? 'selected' : '' }}>{{ $kbCat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.content') }}</label>
                        <div class="stackedit-tools mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#admin-kb-edit-content-{{ $article->id }}">
                                <i class="feather-edit me-1"></i> {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                            </button>
                        </div>
                        <textarea name="o_valuer" id="admin-kb-edit-content-{{ $article->id }}" rows="10" class="form-control" required>{{ $article->o_valuer }}</textarea>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_article') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_article') }}</h4>
                <p class="text-muted">{{ $article->name }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.knowledgebase.delete', $article->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@include('theme::store.partials.kb-superdesign-formatter')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function renderMarkdown() {
            document.querySelectorAll('.markdown-content').forEach(el => {
                if (!el.getAttribute('data-rendered')) {
                    el.innerHTML = DOMPurify.sanitize(marked.parse(el.innerText || el.innerHTML));
                    el.setAttribute('data-rendered', 'true');
                    el.style.display = 'block';
                    if (window.enhanceSuperdesignKbContent) {
                        window.enhanceSuperdesignKbContent(el);
                    }
                }
            });
        }
        renderMarkdown();

        // Initialize snippets toolbar on admin textareas
        document.querySelectorAll('textarea[name="valuer"]').forEach(txt => {
            if (txt.id && window.initKbSnippetsToolbar) {
                window.initKbSnippetsToolbar(txt.id);
            }
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function () {
                renderMarkdown();
            });
        });

        const stackedit = new Stackedit();
        document.querySelectorAll('.open-stackedit').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const textarea = document.querySelector(targetId);
                const modal = this.closest('.modal-content');
                const nameInput = modal.querySelector('input[name="name"]');
                const articleName = nameInput ? nameInput.value : 'Article Content';

                stackedit.openFile({
                    name: articleName,
                    content: { text: textarea.value }
                });

                const adjustIframe = () => {
                    const iframe = document.querySelector('iframe[src*="stackedit.io"]');
                    if (iframe) {
                        const header = document.querySelector('.header, .nxl-header');
                        if (header) {
                            const headerHeight = header.offsetHeight;
                            iframe.style.top = headerHeight + 'px';
                            iframe.style.height = `calc(100% - ${headerHeight}px)`;
                        }
                    } else {
                        setTimeout(adjustIframe, 50);
                    }
                };
                adjustIframe();

                stackedit.off('fileChange');
                stackedit.on('fileChange', (file) => {
                    textarea.value = file.content.text;
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .markdown-content { display: none; }
    .markdown-content h1, .markdown-content h2, .markdown-content h3 { margin-top: 1rem; margin-bottom: 0.5rem; }
    .markdown-content p { margin-bottom: 0.75rem; }
    .markdown-content pre { background: #f8f9fa; padding: 1rem; border-radius: 5px; overflow-x: auto; margin-bottom: 1rem; }
    .modal-body .markdown-content { color: #333; line-height: 1.6; }

    /* KB Power-User Dashboard */
    .kb-power-dashboard { display: flex; flex-direction: column; gap: 1.25rem; }

    /* Hero */
    .kb-hero {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap;
        background: var(--admin-premium-surface);
        border: 1px solid var(--admin-premium-border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: var(--admin-premium-shadow-soft);
    }
    .kb-hero-left { display: flex; align-items: center; gap: 1rem; }
    .kb-hero-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #615dfa 0%, #8b7cff 100%);
        color: #fff; font-size: 22px;
        box-shadow: 0 8px 16px rgba(97,93,250,0.25);
    }
    .kb-hero-title { font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--admin-premium-text); }
    .kb-hero-sub { margin: 0; color: var(--admin-premium-muted); font-size: 0.875rem; }
    .kb-hero-right { display: flex; gap: 0.5rem; }

    /* Buttons */
    .kb-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
        border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all 0.15s ease;
        line-height: 1.2; white-space: nowrap;
    }
    .kb-btn-sm { padding: 0.35rem 0.7rem; font-size: 0.8125rem; }
    .kb-btn-primary { background: #615dfa; color: #fff; border-color: #615dfa; }
    .kb-btn-primary:hover { background: #5048e8; color: #fff; }
    .kb-btn-ghost { background: transparent; color: var(--admin-premium-text); border-color: var(--admin-premium-border); }
    .kb-btn-ghost:hover { background: var(--admin-premium-surface-alt); color: var(--admin-premium-text); }

    /* Toolbar */
    .kb-toolbar {
        background: var(--admin-premium-surface);
        border: 1px solid var(--admin-premium-border);
        border-radius: 12px; padding: 0.75rem 1rem;
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
    }
    .kb-toolbar-left { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; flex: 1; }
    .kb-search {
        position: relative; flex: 1; min-width: 240px; max-width: 360px;
    }
    .kb-search i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: var(--admin-premium-muted); font-size: 15px; pointer-events: none;
    }
    .kb-search .form-control {
        padding-left: 36px; height: 38px; border-radius: 8px;
        border: 1px solid var(--admin-premium-border); background: var(--admin-premium-surface-alt);
        color: var(--admin-premium-text); font-size: 0.875rem;
    }
    .kb-search .form-control:focus { background: var(--admin-premium-surface); border-color: #615dfa; box-shadow: 0 0 0 3px rgba(97,93,250,0.12); }
    .kb-select {
        height: 38px; border-radius: 8px; border: 1px solid var(--admin-premium-border);
        background: var(--admin-premium-surface-alt); color: var(--admin-premium-text);
        font-size: 0.875rem; padding: 0 0.75rem; min-width: 160px;
    }
    .kb-select:focus { border-color: #615dfa; box-shadow: 0 0 0 3px rgba(97,93,250,0.12); }

    /* Stats */
    .kb-stats {
        display: grid; gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    .kb-stat {
        position: relative; overflow: hidden;
        background: var(--admin-premium-surface);
        border: 1px solid var(--admin-premium-border);
        border-radius: 14px; padding: 1rem 1.25rem;
        display: flex; align-items: center; gap: 0.875rem;
        box-shadow: var(--admin-premium-shadow-soft);
    }
    .kb-stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0;
    }
    .kb-stat-icon-indigo { background: linear-gradient(135deg, #615dfa, #8b7cff); }
    .kb-stat-icon-emerald { background: linear-gradient(135deg, #10b981, #34d399); }
    .kb-stat-icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .kb-stat-icon-rose { background: linear-gradient(135deg, #f43f5e, #fb7185); }
    .kb-stat-body { display: flex; flex-direction: column; }
    .kb-stat-label { font-size: 0.75rem; color: var(--admin-premium-muted); text-transform: uppercase; letter-spacing: 0.04em; font-weight: 600; }
    .kb-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--admin-premium-text); line-height: 1.1; }
    .kb-sparkline { position: absolute; right: 8px; bottom: 6px; width: 70px; height: 22px; opacity: 0.18; color: #615dfa; }
    .kb-stat:nth-child(1) .kb-sparkline { color: #615dfa; }
    .kb-stat:nth-child(2) .kb-sparkline { color: #10b981; }
    .kb-stat:nth-child(3) .kb-sparkline { color: #f59e0b; }
    .kb-stat:nth-child(4) .kb-sparkline { color: #f43f5e; }

    /* Card */
    .kb-card {
        background: var(--admin-premium-surface);
        border: 1px solid var(--admin-premium-border);
        border-radius: 14px; overflow: hidden;
        box-shadow: var(--admin-premium-shadow-soft);
    }
    .kb-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--admin-premium-border);
    }
    .kb-card-title { margin: 0; font-size: 1rem; font-weight: 700; color: var(--admin-premium-text); display: inline-flex; align-items: center; gap: 0.5rem; }
    .kb-count-chip {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 20px; padding: 0 6px; border-radius: 999px;
        background: var(--admin-premium-accent-soft); color: #615dfa; font-size: 0.7rem; font-weight: 700;
    }
    html.app-skin-dark .kb-count-chip { color: #a5b4fc; }
    .kb-card-footer { padding: 0.75rem 1.25rem; border-top: 1px solid var(--admin-premium-border); }

    /* Table */
    .kb-table-wrap { overflow-x: auto; }
    .kb-table { width: 100%; border-collapse: collapse; }
    .kb-table thead th {
        text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;
        color: var(--admin-premium-muted); font-weight: 700;
        padding: 0.75rem 1.25rem; background: var(--admin-premium-surface-alt);
        border-bottom: 1px solid var(--admin-premium-border);
    }
    .kb-table tbody td {
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--admin-premium-border);
        vertical-align: middle; color: var(--admin-premium-text); font-size: 0.875rem;
    }
    .kb-table tbody tr:last-child td { border-bottom: 0; }
    .kb-table tbody tr:hover td { background: var(--admin-premium-surface-alt); }
    .kb-empty { text-align: center; color: var(--admin-premium-muted); padding: 2rem !important; }
    .kb-muted { color: var(--admin-premium-muted); }

    /* Article cell */
    .kb-article-cell { display: flex; align-items: center; gap: 0.75rem; }
    .kb-article-icon {
        width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: var(--admin-premium-accent-soft); color: #615dfa; font-size: 15px;
    }
    html.app-skin-dark .kb-article-icon { color: #a5b4fc; }
    .kb-article-icon-cat { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .kb-article-title {
        display: block; font-weight: 600; color: var(--admin-premium-text); text-decoration: none;
        line-height: 1.3;
    }
    .kb-article-title:hover { color: #615dfa; }
    .kb-article-snippet {
        font-size: 0.75rem; color: var(--admin-premium-muted); margin-top: 2px;
        max-width: 420px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    /* Badges */
    .kb-badge {
        display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;
    }
    .kb-badge-soft-primary { background: var(--admin-premium-accent-soft); color: #615dfa; }
    .kb-badge-soft-success { background: var(--admin-premium-success-soft); color: #10b981; }
    .kb-badge-soft-indigo { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
    html.app-skin-dark .kb-badge-soft-primary { color: #a5b4fc; }
    html.app-skin-dark .kb-badge-soft-success { color: #34d399; }
    html.app-skin-dark .kb-badge-soft-indigo { color: #818cf8; }

    /* Actions */
    .kb-actions { display: inline-flex; gap: 0.25rem; }
    .kb-icon-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--admin-premium-border);
        background: var(--admin-premium-surface-alt); color: var(--admin-premium-muted);
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px;
        transition: all 0.15s ease;
    }
    .kb-icon-btn:hover { background: var(--admin-premium-surface); }
    .kb-icon-btn-primary { color: #615dfa; }
    .kb-icon-btn-primary:hover { background: var(--admin-premium-accent-soft); border-color: var(--admin-premium-border-strong); }
    .kb-icon-btn-danger { color: #ef4444; }
    .kb-icon-btn-danger:hover { background: var(--admin-premium-danger-soft); border-color: rgba(239, 68, 68, 0.25); }
</style>
@endpush
@endsection
