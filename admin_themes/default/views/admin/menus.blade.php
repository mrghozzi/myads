@extends('admin::layouts.admin')

@section('title', __('messages.menu'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.menu') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.style') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.menu') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.new_menu') }} / {{ __('messages.navigation_menu_list') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.menu') }}</span>
                    <span class="admin-summary-value">{{ number_format($menus->total()) }}</span>
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
                    <span class="admin-panel__eyebrow">{{ __('messages.new_menu') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.add') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.menus.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-12">
                        <label for="name" class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                    </div>
                    <div class="col-md-12">
                        <label for="dir" class="form-label">{{ __('messages.url') }}</label>
                        <input type="text" class="form-control" id="dir" name="dir" required autocomplete="off">
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
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.navigation_menu_list') }}</span>
                    <h2 class="admin-panel__title">{{ number_format($menus->total()) }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.url') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td data-label="#ID">#{{ $menu->id_m }}</td>
                                    <td data-label="{{ __('messages.name') }}">
                                        <form action="{{ route('admin.menus.update', $menu->id_m) }}" method="POST" id="edit-form-{{ $menu->id_m }}" class="admin-inline-form">
                                            @csrf
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $menu->name }}" required>
                                    </td>
                                    <td data-label="{{ __('messages.url') }}">
                                            <input type="text" name="dir" class="form-control form-control-sm" value="{{ $menu->dir }}" required>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                            <div class="admin-action-cluster justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="feather-save"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger js-menu-delete"
                                                    data-delete-action="{{ route('admin.menus.delete', $menu->id_m) }}"
                                                    data-menu-name="{{ $menu->name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#menuDeleteModal"
                                                >
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('messages.no_menus_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($menus->hasPages())
                    <div class="mt-4">{{ $menus->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="menuDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mx-auto mb-3"><i class="feather-trash-2"></i></div>
                <h3 class="h5 mb-2">{{ __('messages.delete') }}</h3>
                <p class="text-muted mb-0" id="menuDeleteLabel">{{ __('messages.confirm_delete_menu') }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="" method="POST" id="menuDeleteForm">
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
    const deleteForm = document.getElementById('menuDeleteForm');
    const deleteLabel = document.getElementById('menuDeleteLabel');

    document.querySelectorAll('.js-menu-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            deleteForm.action = this.dataset.deleteAction;
            deleteLabel.textContent = "{{ __('messages.confirm_delete_menu') }}" + ' ' + (this.dataset.menuName || '');
        });
    });
});
</script>
@endpush
