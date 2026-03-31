@extends('theme::layouts.admin')

@section('title', __('messages.news'))

@section('content')
@php
    $dropdownEmojis = ($emojis ?? collect())->take(10);
    $moreEmojis = ($emojis ?? collect())->slice(10);
@endphp

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.news') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.news') }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                    <i class="feather-plus me-2"></i>{{ __('messages.new_news') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.news') }}</h5>
            </div>
            <div class="card-body custom-card-action">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">{{ __('messages.date') }}</th>
                                <th scope="col">{{ __('messages.title') }}</th>
                                <th scope="col">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                                <tr>
                                    <td><span class="fw-bold">#{{ $item->id }}</span></td>
                                    <td>{{ date('Y-m-d', $item->date) }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0);" class="text-primary" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteNewsModal{{ $item->id }}">
                                                <i class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                        <label class="form-label">{{ __('messages.title') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.content') }}</label>
                            <div class="stackedit-tools mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#news-editor-add">
                                    <i class="feather-edit me-1"></i> {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
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

@foreach($news as $item)
    <div class="modal fade" id="editNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.edit_news') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.news.update', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.title') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.content') }}</label>
                            <div class="stackedit-tools mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#news-editor-{{ $item->id }}">
                                    <i class="feather-edit me-1"></i> {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                                </button>
                            </div>
                            <textarea id="news-editor-{{ $item->id }}" name="text" class="form-control" rows="10" required>{{ $item->text }}</textarea>
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

    <div class="modal fade" id="deleteNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.delete_news') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ __('messages.confirm_delete_news') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <form action="{{ route('admin.news.delete', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // StackEdit Integration
    const stackedit = new Stackedit();
    document.querySelectorAll('.open-stackedit').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const textarea = document.querySelector(targetId);
            const modal = this.closest('.modal-content');
            const nameInput = modal.querySelector('input[name="name"]');
            const articleName = nameInput ? nameInput.value : 'News Content';
            
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
@endsection
