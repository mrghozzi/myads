@extends('theme::layouts.admin')

@section('title', __('messages.emojis'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.emojis') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.emojis') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.emojis') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.add_emoji') }} / {{ __('messages.emoji_shortcut') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.emojis') }}</span>
                    <span class="admin-summary-value">{{ number_format($emojis->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="admin-workspace-grid">
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.add_emoji') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.emoji_shortcut') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.emojis.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label">{{ __('messages.emoji_shortcut') }}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder=":smile:" required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label for="img" class="form-label">{{ __('messages.emoji_icon') }} (URL)</label>
                        <input type="text" class="form-control" id="img" name="img" placeholder="https://example.com/smile.png" required autocomplete="off">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>{{ __('messages.add') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__body">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>{{ __('messages.emoji_shortcut') }}</th>
                                <th>{{ __('messages.emoji_icon') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emojis as $emoji)
                                <tr>
                                    <td data-label="#ID">#{{ $emoji->id }}</td>
                                    <td data-label="{{ __('messages.emoji_shortcut') }}">{{ $emoji->name }}</td>
                                    <td data-label="{{ __('messages.emoji_icon') }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $emoji->img }}" alt="{{ $emoji->name }}" style="width: 24px; height: 24px; object-fit: contain;">
                                            <span class="text-muted small text-truncate">{{ $emoji->img }}</span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger js-emoji-delete"
                                            data-delete-action="{{ route('admin.emojis.delete', $emoji->id) }}"
                                            data-emoji-name="{{ $emoji->name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#emojiDeleteModal"
                                        >
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($emojis->hasPages())
                    <div class="mt-4">{{ $emojis->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="emojiDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mx-auto mb-3"><i class="feather-trash-2"></i></div>
                <h3 class="h5 mb-2">{{ __('messages.delete') }}</h3>
                <p class="text-muted mb-0" id="emojiDeleteLabel">{{ __('messages.confirm_delete_emoji') }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="" method="POST" id="emojiDeleteForm">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForm = document.getElementById('emojiDeleteForm');
    const deleteLabel = document.getElementById('emojiDeleteLabel');

    document.querySelectorAll('.js-emoji-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            deleteForm.action = this.dataset.deleteAction;
            deleteLabel.textContent = "{{ __('messages.confirm_delete_emoji') }}" + ' ' + (this.dataset.emojiName || '');
        });
    });
});
</script>
@endpush
