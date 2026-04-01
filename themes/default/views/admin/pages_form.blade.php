@extends('theme::layouts.admin')

@section('title', $mode === 'edit' ? __('messages.e_page') : __('messages.add_page'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="pages-editor-shell">
    <style>
        .pages-editor-shell {
            --pe-bg: #f4f6fb;
            --pe-card-bg: #ffffff;
            --pe-card-alt: #f8faff;
            --pe-border: #dfe5f2;
            --pe-border-strong: #cad4ea;
            --pe-title: #1d2a44;
            --pe-muted: #64708b;
            --pe-primary: #3f66ff;
            --pe-primary-hover: #2f53e2;
            --pe-shadow: 0 10px 30px rgba(26, 42, 84, 0.08);
            --pe-focus: rgba(63, 102, 255, 0.22);
        }

        .app-skin-dark .pages-editor-shell {
            --pe-bg: #111827;
            --pe-card-bg: #1f2937;
            --pe-card-alt: #263246;
            --pe-border: #374151;
            --pe-border-strong: #4b5563;
            --pe-title: #f9fafb;
            --pe-muted: #9ca3af;
            --pe-primary: #5b7cff;
            --pe-primary-hover: #4a69ec;
            --pe-shadow: 0 10px 30px rgba(0, 0, 0, 0.28);
            --pe-focus: rgba(91, 124, 255, 0.28);
        }

        .pages-editor-shell {
            background: var(--pe-bg);
            border: 1px solid var(--pe-border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--pe-shadow);
        }

        .pages-editor-hero {
            background: linear-gradient(120deg, var(--pe-card-bg) 0%, var(--pe-card-alt) 100%);
            border: 1px solid var(--pe-border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 18px;
            display: flex;
            gap: 16px;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .pages-editor-hero h2 {
            color: var(--pe-title);
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .pages-editor-hero p {
            margin: 8px 0 0;
            color: var(--pe-muted);
            font-size: 13px;
        }

        .pe-breadcrumb {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            gap: 8px;
            align-items: center;
            color: var(--pe-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pe-breadcrumb a {
            color: var(--pe-muted);
            text-decoration: none;
        }

        .pe-breadcrumb a:hover {
            color: var(--pe-primary);
        }

        .pe-card {
            border: 1px solid var(--pe-border);
            border-radius: 14px;
            background: var(--pe-card-bg);
            box-shadow: var(--pe-shadow);
            overflow: hidden;
        }

        .pe-card + .pe-card {
            margin-top: 16px;
        }

        .pe-card-header {
            background: var(--pe-card-alt);
            border-bottom: 1px solid var(--pe-border);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pe-card-header h5 {
            margin: 0;
            color: var(--pe-title);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pe-card-body {
            padding: 18px;
        }

        .pages-editor-shell .form-label {
            color: var(--pe-title);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }

        .pages-editor-shell .form-control,
        .pages-editor-shell .form-select,
        .pages-editor-shell .input-group-text {
            border-color: var(--pe-border);
            min-height: 44px;
            border-radius: 10px;
            background: var(--pe-card-bg);
            color: var(--pe-title);
            font-size: 14px;
        }

        .pages-editor-shell .form-control::placeholder {
            color: var(--pe-muted);
        }

        .pages-editor-shell .form-control:focus,
        .pages-editor-shell .form-select:focus {
            border-color: var(--pe-primary);
            box-shadow: 0 0 0 4px var(--pe-focus);
        }

        .pages-editor-shell .input-group-text {
            background: var(--pe-card-alt);
            color: var(--pe-muted);
            font-weight: 600;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        [dir="rtl"] .pages-editor-shell .input-group-text {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .pages-editor-shell .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        [dir="rtl"] .pages-editor-shell .input-group .form-control {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .pe-content-wrap {
            border: 1px solid var(--pe-border);
            border-radius: 12px;
            overflow: hidden;
            background: var(--pe-card-bg);
        }

        .pe-content-note {
            padding: 10px 12px;
            border-bottom: 1px solid var(--pe-border);
            background: var(--pe-card-alt);
            color: var(--pe-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .pe-content-wrap textarea {
            border: 0;
            border-radius: 0;
            min-height: 340px;
        }

        .pe-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pe-btn-primary {
            border: 0;
            color: #fff;
            background: var(--pe-primary);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pe-btn-primary:hover {
            background: var(--pe-primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .pe-btn-secondary {
            border: 1px solid var(--pe-border);
            color: var(--pe-muted);
            background: var(--pe-card-alt);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pe-btn-secondary:hover {
            color: var(--pe-title);
            border-color: var(--pe-border-strong);
        }

        .pe-divider {
            border-color: var(--pe-border);
            margin: 16px 0;
        }

        .pe-sidebar {
            position: sticky;
            top: 90px;
        }

        .pe-widget-list .list-group-item {
            background: transparent;
            border-color: var(--pe-border);
            color: var(--pe-title);
            font-size: 13px;
        }

        .pe-widget-list .badge {
            font-size: 11px;
            font-weight: 700;
        }

        .pages-editor-shell .alert {
            border-radius: 12px;
            border-width: 1px;
        }

        .pages-editor-shell .form-check {
            background: var(--pe-card-alt);
            border: 1px solid var(--pe-border);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pages-editor-shell .form-check-label {
            color: var(--pe-title);
            font-size: 13px;
            font-weight: 600;
            margin-inline-start: 12px;
            flex: 1;
        }

        .pages-editor-shell .form-check-input {
            margin: 0;
            min-width: 42px;
            height: 22px;
        }

        .pages-editor-shell .text-muted {
            color: var(--pe-muted) !important;
        }

        @media (max-width: 1199.98px) {
            .pe-sidebar {
                position: static;
                top: auto;
            }
        }

        @media (max-width: 767.98px) {
            .pages-editor-shell {
                padding: 14px;
                border-radius: 14px;
            }

            .pages-editor-hero {
                padding: 14px;
                margin-bottom: 14px;
            }

            .pe-card-body {
                padding: 14px;
            }

            .pe-actions {
                width: 100%;
            }

            .pe-actions .pe-btn-primary,
            .pe-actions .pe-btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="pages-editor-hero">
        <div>
            <ul class="pe-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><i class="feather-chevron-right"></i></li>
                <li><a href="{{ route('admin.pages') }}">{{ __('messages.pages') }}</a></li>
                <li><i class="feather-chevron-right"></i></li>
                <li>{{ $mode === 'edit' ? __('messages.e_page') : __('messages.add_page') }}</li>
            </ul>
            <h2>{{ $mode === 'edit' ? __('messages.e_page') : __('messages.add_page') }}</h2>
            <p>{{ __('messages.page_content') ?? 'Page Content' }} / {{ __('messages.settings') }} / SEO</p>
        </div>

        <div class="pe-actions">
            <a href="{{ route('admin.pages') }}" class="pe-btn-secondary" id="hero-cancel-link">
                <i class="feather-x"></i>{{ __('messages.cancel') }}
            </a>
            <button type="submit" form="page-form" class="pe-btn-primary" id="hero-submit-btn">
                <i class="feather-save"></i>{{ $mode === 'edit' ? __('messages.save') : __('messages.add') }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="page-form" action="{{ $mode === 'edit' ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}" method="POST">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="pe-card">
                    <div class="pe-card-header">
                        <i class="feather-edit-3 text-primary"></i>
                        <h5>{{ __('messages.page_content') ?? 'Page Content' }}</h5>
                    </div>

                    <div class="pe-card-body">
                        <div class="mb-4">
                            <label class="form-label" for="page-title">{{ __('messages.title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" id="page-title" value="{{ old('title', $page->title ?? '') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="page-slug">{{ __('messages.page_slug') ?? 'Slug' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">/page/</span>
                                <input type="text" name="slug" class="form-control" id="page-slug" value="{{ old('slug', $page->slug ?? '') }}" required pattern="[a-z0-9\-]+">
                            </div>
                            <small class="text-muted">{{ __('messages.slug_hint') ?? 'Only lowercase letters, numbers, and hyphens' }}</small>
                        </div>

                        <div>
                            <label class="form-label" for="page-content">{{ __('messages.content') }}</label>
                            <div class="pe-content-wrap">
                                <div class="pe-content-note">
                                    <i class="feather-type me-1"></i>SCEditor
                                </div>
                                <textarea name="content" id="page-content" rows="16" class="form-control">{{ old('content', $page->content ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="pe-sidebar">
                    <div class="pe-card">
                        <div class="pe-card-header">
                            <i class="feather-sliders text-primary"></i>
                            <h5>{{ __('messages.settings') }}</h5>
                        </div>

                        <div class="pe-card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="published" {{ old('status', $page->status ?? 'published') === 'published' ? 'selected' : '' }}>{{ __('messages.published') ?? 'Published' }}</option>
                                    <option value="draft" {{ old('status', $page->status ?? '') === 'draft' ? 'selected' : '' }}>{{ __('messages.draft') ?? 'Draft' }}</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">{{ __('messages.order') }}</label>
                                <input type="number" name="order" class="form-control" value="{{ old('order', $page->order ?? 0) }}">
                            </div>

                            <hr class="pe-divider">

                            <div class="pe-actions">
                                <button type="submit" class="pe-btn-primary" id="sidebar-submit-btn">
                                    <i class="feather-save"></i>{{ $mode === 'edit' ? __('messages.save') : __('messages.add') }}
                                </button>
                                <a href="{{ route('admin.pages') }}" class="pe-btn-secondary" id="sidebar-cancel-link">
                                    <i class="feather-corner-up-left"></i>{{ __('messages.cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pe-card">
                        <div class="pe-card-header">
                            <i class="feather-layout text-primary"></i>
                            <h5>{{ __('messages.widgets') }}</h5>
                        </div>

                        <div class="pe-card-body">
                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="widget_left" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_left" value="1" id="widget_left" {{ old('widget_left', $page->widget_left ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="widget_left">{{ __('messages.show_left_widgets') ?? 'Show Left Widgets' }}</label>
                            </div>

                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="widget_right" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_right" value="1" id="widget_right" {{ old('widget_right', $page->widget_right ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="widget_right">{{ __('messages.show_right_widgets') ?? 'Show Right Widgets' }}</label>
                            </div>

                            @if($mode === 'edit')
                                <hr class="pe-divider">
                                <p class="text-muted mb-2" style="font-size: 12px;">
                                    {{ __('messages.page_widgets_hint') ?? 'Manage widgets for this page from the Widgets panel.' }}
                                </p>
                                <a href="{{ route('admin.widgets') }}" class="pe-btn-secondary" id="manage-widgets-link">
                                    <i class="feather-layout"></i>{{ __('messages.manage_widgets') ?? 'Manage Widgets' }}
                                </a>

                                @if(isset($leftWidgets) && $leftWidgets->count() > 0)
                                    <div class="mt-3 pe-widget-list">
                                        <small class="fw-semibold text-muted">{{ __('messages.page_left') ?? 'Page Left' }} ({{ $leftWidgets->count() }})</small>
                                        <ul class="list-group list-group-flush mt-1">
                                            @foreach($leftWidgets as $w)
                                                <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                    {{ $w->name }}
                                                    <span class="badge bg-soft-primary text-primary">{{ $w->o_mode }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(isset($rightWidgets) && $rightWidgets->count() > 0)
                                    <div class="mt-3 pe-widget-list">
                                        <small class="fw-semibold text-muted">{{ __('messages.page_right') ?? 'Page Right' }} ({{ $rightWidgets->count() }})</small>
                                        <ul class="list-group list-group-flush mt-1">
                                            @foreach($rightWidgets as $w)
                                                <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                    {{ $w->name }}
                                                    <span class="badge bg-soft-primary text-primary">{{ $w->o_mode }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="pe-card">
                        <div class="pe-card-header">
                            <i class="feather-search text-primary"></i>
                            <h5>SEO</h5>
                        </div>

                        <div class="pe-card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.description') }}</label>
                                <textarea name="meta_description" rows="3" class="form-control" maxlength="500">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">{{ __('messages.meta_keywords') }}</label>
                                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}" maxlength="500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css">

<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.js"></script>
<script>
(function() {
    // --- Slug auto-generation ---
    var titleInput = document.getElementById('page-title');
    var slugInput = document.getElementById('page-slug');
    var slugEdited = false;

    if (slugInput && slugInput.value) {
        slugEdited = true;
    }

    if (slugInput) {
        slugInput.addEventListener('input', function() {
            slugEdited = true;
        });
    }

    if (titleInput) {
        titleInput.addEventListener('input', function() {
            if (!slugEdited || !slugInput.value) {
                var slug = titleInput.value
                    .toLowerCase()
                    .replace(/[^\u0621-\u064Aa-z0-9\s\-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                slugInput.value = slug;
            }
        });
    }

    // --- SCEditor ---
    var textarea = document.getElementById('page-content');
    if (textarea) {
        sceditor.create(textarea, {
            format: 'xhtml',
            style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css',
            toolbar: 'bold,italic,underline,strike|font,size,color,removeformat|left,center,right,justify|bulletlist,orderedlist|link,unlink,image|source',
            width: '100%',
            height: '350px',
            resizeEnabled: true,
            emoticonsEnabled: false
        });
    }
})();
</script>
@endsection
