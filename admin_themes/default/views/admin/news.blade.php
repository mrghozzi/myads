@extends('admin::layouts.admin')

@section('title', __('messages.news'))

@section('content')
@php
    $latestNews = $news->first();
@endphp

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.news') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.news_site') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.news') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.news_posts') }} / {{ __('messages.date') }} / {{ __('messages.content') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.news_posts') }}</span>
                    <span class="admin-stat-value">{{ number_format($news->count()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.date') }}</span>
                    <span class="admin-stat-value">{{ $latestNews ? date('Y-m-d', $latestNews->date) : '--' }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.emojis') }}</span>
                    <span class="admin-stat-value">{{ number_format(($emojis ?? collect())->count()) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card justify-content-between">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.add_news') }}</span>
                    <div class="admin-muted">{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}</div>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                    <i class="feather-plus me-2"></i>{{ __('messages.new_news') }}
                </button>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.news') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.news_posts') }}</h2>
            </div>
            <div class="admin-chip-list">
                @if($latestNews)
                    <span class="admin-chip"><i class="feather-clock"></i>{{ date('Y-m-d', $latestNews->date) }}</span>
                @endif
                <span class="admin-chip"><i class="feather-file-text"></i>{{ $news->count() }}</span>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.title') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td data-label="#ID"><strong>#{{ $item->id }}</strong></td>
                                <td data-label="{{ __('messages.date') }}">{{ date('Y-m-d', $item->date) }}</td>
                                <td data-label="{{ __('messages.title') }}">
                                    <div class="admin-section-stack gap-1">
                                        <strong>{{ $item->name }}</strong>
                                        <span class="admin-muted admin-text-truncate">{{ \Illuminate\Support\Str::limit(strip_tags($item->text), 140) }}</span>
                                    </div>
                                    <script type="application/json" id="news-payload-{{ $item->id }}">
                                        @json(['id' => $item->id, 'name' => $item->name, 'text' => $item->text], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
                                    </script>
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <div class="admin-action-cluster">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light admin-icon-btn news-edit-trigger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editNewsModal"
                                            data-payload-id="news-payload-{{ $item->id }}"
                                        >
                                            <i class="feather-edit-2 text-primary"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light admin-icon-btn news-delete-trigger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteNewsModal"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->name }}"
                                        >
                                            <i class="feather-trash-2 text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-file-text"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_data') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.new_news') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.news.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="admin-form-label">{{ __('messages.title') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="admin-form-label">{{ __('messages.content') }}</label>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <span class="admin-muted">{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}</span>
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#news-editor-add">
                                <i class="feather-edit me-1"></i>{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                            </button>
                        </div>
                        <textarea id="news-editor-add" name="text" class="form-control" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_news') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editNewsForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="admin-form-label">{{ __('messages.title') }}</label>
                        <input type="text" name="name" id="editNewsName" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="admin-form-label">{{ __('messages.content') }}</label>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <span class="admin-muted">{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}</span>
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#news-editor-edit">
                                <i class="feather-edit me-1"></i>{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                            </button>
                        </div>
                        <textarea id="news-editor-edit" name="text" class="form-control" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_news') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="admin-modal-icon is-danger">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.delete_news') }}</h4>
                <p class="text-muted mb-0" id="deleteNewsText">{{ __('messages.confirm_delete_news') }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="" method="POST" id="deleteNewsForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editNewsModal');
        const deleteModal = document.getElementById('deleteNewsModal');
        const editForm = document.getElementById('editNewsForm');
        const editName = document.getElementById('editNewsName');
        const editText = document.getElementById('news-editor-edit');
        const deleteForm = document.getElementById('deleteNewsForm');
        const deleteText = document.getElementById('deleteNewsText');
        const stackedit = new Stackedit();

        function adjustIframe() {
            const iframe = document.querySelector('iframe[src*="stackedit.io"]');
            if (!iframe) {
                setTimeout(adjustIframe, 50);
                return;
            }

            const header = document.querySelector('.header, .nxl-header');
            if (!header) {
                return;
            }

            const headerHeight = header.offsetHeight;
            iframe.style.top = headerHeight + 'px';
            iframe.style.height = 'calc(100% - ' + headerHeight + 'px)';
        }

        document.querySelectorAll('.open-stackedit').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-target');
                const textarea = document.querySelector(targetId);
                if (!textarea) {
                    return;
                }

                const modal = button.closest('.modal-content');
                const titleInput = modal ? modal.querySelector('input[name="name"]') : null;
                const articleName = titleInput && titleInput.value ? titleInput.value : "{{ __('messages.news') }}";

                stackedit.openFile({
                    name: articleName,
                    content: {
                        text: textarea.value
                    }
                });

                adjustIframe();
                stackedit.off('fileChange');
                stackedit.on('fileChange', function (file) {
                    textarea.value = file.content.text;
                });
            });
        });

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const payloadId = trigger.getAttribute('data-payload-id');
                const payloadNode = payloadId ? document.getElementById(payloadId) : null;
                if (!payloadNode) {
                    return;
                }

                const payload = JSON.parse(payloadNode.textContent);
                editForm.setAttribute('action', "{{ route('admin.news.update', ['id' => '__ID__']) }}".replace('__ID__', payload.id));
                editName.value = payload.name || '';
                editText.value = payload.text || '';
            });
        }

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const newsId = trigger.getAttribute('data-id');
                const title = trigger.getAttribute('data-title') || '';

                deleteForm.setAttribute('action', "{{ route('admin.news.delete', ['id' => '__ID__']) }}".replace('__ID__', newsId));
                deleteText.textContent = "{{ __('messages.confirm_delete_news') }}" + (title ? ' [' + title + ']' : '');
            });
        }
    });
</script>
@endpush
