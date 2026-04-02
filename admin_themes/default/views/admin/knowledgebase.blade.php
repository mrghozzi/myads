@extends('admin::layouts.admin')

@section('title', __('messages.knowledgebase'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Header & Search -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.knowledgebase') }}</h2>
        <p class="text-muted">{{ __('messages.kb_description') ?? 'A premium web applications with integrate knowledge base.' }}</p>
        <form action="{{ route('admin.knowledgebase') }}" method="GET" class="my-4 d-none d-sm-block search-form">
            <div class="input-group select-wd-sm">
                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_placeholder') ?? 'Enter your keyword or question here...' }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="feather-search"></i>
                    <span class="ms-2">{{ __('messages.search') }}</span>
                </button>
            </div>
        </form>
        <div class="mt-2 d-none d-sm-block">
            <span class="fs-12 text-muted">{{ __('messages.popular') }}:</span>
            @foreach($categories->take(5) as $cat)
                <a href="javascript:void(0);" class="badge bg-gray-100 shadow-sm text-muted mx-1">{{ $cat->name }}</a>
            @endforeach
        </div>
        <div class="mt-4">
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                <i class="feather-plus me-2"></i> {{ __('messages.add_article') }}
            </button>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4 help-center-main-contet-area overflow-visible">
    @if(isset($searchResults))
        <h3 class="mb-4">{{ __('messages.search_results') }}</h3>
        <div class="row">
            @foreach($searchResults as $article)
                <div class="col-lg-12 mb-3">
                    <div class="card p-4">
                         <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-bold"><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">{{ $article->name }}</a></h5>
                                <p class="text-muted">{{ Str::limit($article->o_valuer, 150) }}</p>
                                <span class="badge bg-soft-primary">{{ $article->o_mode }}</span>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}"><i class="feather-edit-3"></i></button>
                                <button class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}"><i class="feather-trash-2"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $searchResults->links('pagination::bootstrap-5') }}
    @else
        <!-- Quick Stats -->
        <div class="row help-quick-card mb-5">
            <div class="col-lg-4">
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body p-5 text-center">
                        <div class="wd-50 ht-50 d-flex align-items-center justify-content-center mb-4 mx-auto bg-soft-primary rounded-circle">
                            <i class="feather-book-open fs-20 text-primary"></i>
                        </div>
                        <h2 class="fs-16 fw-bold mb-2">{{ __('messages.total_articles') }}</h2>
                        <h3 class="fw-bolder mb-0">{{ $totalArticles }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body p-5 text-center">
                        <div class="wd-50 ht-50 d-flex align-items-center justify-content-center mb-4 mx-auto bg-soft-success rounded-circle">
                            <i class="feather-folder fs-20 text-success"></i>
                        </div>
                        <h2 class="fs-16 fw-bold mb-2">{{ __('messages.cat_s') }}</h2>
                        <h3 class="fw-bolder mb-0">{{ $categories->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body p-5 text-center">
                        <div class="wd-50 ht-50 d-flex align-items-center justify-content-center mb-4 mx-auto bg-soft-warning rounded-circle">
                            <i class="feather-star fs-20 text-warning"></i>
                        </div>
                        <h2 class="fs-16 fw-bold mb-2">{{ __('messages.latest_addition') }}</h2>
                        <p class="mb-0 text-truncate">{{ $latestArticles->first()->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <section class="topic-category-section mb-5">
            <div class="d-flex flex-column align-items-center justify-content-center mb-5">
                <h2 class="fs-20 fw-bold mb-3">{{ __('messages.documentation_category') }}</h2>
                <p class="px-5 mx-5 text-center text-muted text-truncate-3-line">{{ __('messages.browse_by_category_desc') ?? 'Browse our knowledge base by category to find the answers you need.' }}</p>
            </div>
            <div class="row">
                @foreach($categories as $category)
                <div class="col-xl-4 col-lg-6">
                    <div class="card p-4 mb-4 h-100">
                        <div class="d-sm-flex align-items-center mb-4">
                            <div class="wd-50 ht-50 p-2 d-flex align-items-center justify-content-center border rounded-3 bg-gray-100">
                                <i class="feather-folder fs-20 text-muted"></i>
                            </div>
                            <div class="ms-0 ms-sm-3 mt-4 mt-sm-0">
                                <h2 class="fs-14 fw-bold mb-1">{{ $category->name }}</h2>
                                <span class="fs-10 fw-semibold text-uppercase text-muted">{{ $category->count }} {{ __('messages.topics') }}</span>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0 ms-sm-5 ps-sm-3 flex-grow-1">
                            @foreach($category->articles as $article)
                            <li class="mb-2 d-flex justify-content-between align-items-center group" style="position: relative;">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <i class="feather-file-text me-2 fs-13 text-muted"></i>
                                    <a href="javascript:void(0);" class="fs-13 fw-medium text-truncate text-dark" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">{{ $article->name }}</a>
                                </div>
                                <div class="d-flex gap-2 ms-2">
                                    <a href="javascript:void(0);" class="text-primary fs-12" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}"><i class="feather-edit-2"></i></a>
                                    <a href="javascript:void(0);" class="text-danger fs-12" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}"><i class="feather-trash"></i></a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @if($category->count > 5)
                        <div class="mt-4 ms-5 ps-3">
                            <a href="{{ route('admin.knowledgebase', ['category' => $category->name]) }}" class="fs-12">{{ __('messages.more_topics') }} &rarr;</a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Latest Articles -->
        <section class="topic-tranding-section">
             <div class="d-flex flex-column align-items-center justify-content-center mb-5">
                <h2 class="fs-20 fw-bold mb-3">{{ __('messages.trending_topics') }}</h2>
            </div>
            <div class="row">
                @foreach($latestArticles as $article)
                <div class="col-lg-6">
                    <div class="card border rounded-3 mb-3 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between p-3">
                            <div class="d-flex align-items-center overflow-hidden">
                                <div class="wd-40 ht-40 bg-gray-100 me-3 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                                    <i class="feather-file-text text-muted"></i>
                                </div>
                                <a href="javascript:void(0);" class="text-truncate fw-medium text-dark" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">{{ $article->name }}</a>
                            </div>
                             <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0);" class="avatar-text avatar-sm text-primary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}">
                                    <i class="feather-edit-3"></i>
                                </a>
                                <a href="javascript:void(0);" class="avatar-text avatar-sm text-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}">
                                    <i class="feather-trash-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Markdown Rendering
        function renderMarkdown() {
            document.querySelectorAll('.markdown-content').forEach(el => {
                if (!el.getAttribute('data-rendered')) {
                    el.innerHTML = DOMPurify.sanitize(marked.parse(el.innerText || el.innerHTML));
                    el.setAttribute('data-rendered', 'true');
                    el.style.display = 'block';
                }
            });
        }
        renderMarkdown();

        // Re-render when modal is shown (for View modal)
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function () {
                renderMarkdown();
            });
        });

        // StackEdit Integration
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
                    content: {
                        text: textarea.value
                    }
                });

                // Fix for header overlap - position editor below the fixed header
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
                        // Keep checking until iframe is injected
                        setTimeout(adjustIframe, 50);
                    }
                };
                adjustIframe();

                // Set up listener for this specific textarea
                stackedit.off('fileChange');
                stackedit.on('fileChange', (file) => {
                    textarea.value = file.content.text;
                });
            });
        });
    });
</script>
<style>
    .markdown-content { display: none; }
    .markdown-content h1, .markdown-content h2, .markdown-content h3 { margin-top: 1rem; margin-bottom: 0.5rem; }
    .markdown-content p { margin-bottom: 0.75rem; }
    .markdown-content pre { background: #f8f9fa; padding: 1rem; border-radius: 5px; overflow-x: auto; margin-bottom: 1rem; }
    .modal-body .markdown-content { color: #333; line-height: 1.6; }
</style>
@endpush
@endsection
