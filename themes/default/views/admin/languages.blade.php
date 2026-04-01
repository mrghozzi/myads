@extends('theme::layouts.admin')

@section('title', __('messages.languages'))

@section('content')
@php
    $visibleLanguages = collect($languages->items());
    $readyCount = $visibleLanguages->where('has_folder', true)->count();
    $missingCount = $visibleLanguages->where('has_folder', false)->count();
@endphp

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.languages') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.languages') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.languages') }} / {{ __('messages.folder_ready') }} / {{ __('messages.missing_folder') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.languages') }}</span>
                    <span class="admin-stat-value">{{ number_format($languages->total()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.folder_ready') }}</span>
                    <span class="admin-stat-value">{{ number_format($readyCount) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.missing_folder') }}</span>
                    <span class="admin-stat-value">{{ number_format($missingCount) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card justify-content-between">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.new_language') }}</span>
                    <div class="admin-muted">{{ __('messages.languages') }}</div>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                    <i class="feather-plus me-2"></i>{{ __('messages.new_language') }}
                </button>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('errors'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.languages') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.languages') }}</h2>
            </div>
            <div class="admin-chip-list">
                <span class="admin-chip"><i class="feather-check-circle"></i>{{ $readyCount }}</span>
                <span class="admin-chip"><i class="feather-alert-triangle"></i>{{ $missingCount }}</span>
            </div>
        </div>

        <div class="admin-panel__body">
            @if($languages->isEmpty())
                <div class="admin-empty-state">
                    <span class="admin-avatar-circle"><i class="feather-globe"></i></span>
                    <h4>{{ __('messages.no_languages_found') }}</h4>
                    <p class="admin-muted mb-0">{{ __('messages.no_languages_desc') }}</p>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        {{ __('messages.new_language') }}
                    </button>
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($languages as $language)
                                <tr>
                                    <td data-label="#">
                                        <span class="admin-avatar-circle" style="height: 44px; width: 44px; border-radius: 14px;">
                                            {{ strtoupper(substr($language->o_valuer, 0, 2)) }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('messages.name') }}">
                                        <div class="admin-section-stack gap-1">
                                            <strong>{{ $language->name }}</strong>
                                            <span class="admin-muted">{{ __('messages.language_name') ?? 'Language Name' }}</span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('messages.code') }}">
                                        <span class="badge bg-light text-dark border">{{ $language->o_valuer }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.status') }}">
                                        @if($language->has_folder)
                                            <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i>{{ __('messages.folder_ready') }}</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger"><i class="feather-alert-triangle me-1"></i>{{ __('messages.missing_folder') }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                        <div class="admin-action-cluster">
                                            <a href="{{ route('admin.languages.terms', $language->id) }}" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.edit_terms') ?? 'Edit Terms' }}">
                                                <i class="feather-edit-3 text-primary"></i>
                                            </a>
                                            @if($language->has_folder)
                                                <a href="{{ route('admin.languages.export', $language->id) }}" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.export') ?? 'Export (.zip)' }}">
                                                    <i class="feather-download text-info"></i>
                                                </a>
                                            @endif
                                            @if($language->o_valuer !== 'en')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-light admin-icon-btn language-delete-trigger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteLanguageModal"
                                                    data-id="{{ $language->id }}"
                                                    data-name="{{ $language->name }}"
                                                    data-code="{{ $language->o_valuer }}"
                                                >
                                                    <i class="feather-trash-2 text-danger"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $languages->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.new_language') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.languages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="feather-info me-2"></i>{{ __('messages.add_language_info') ?? 'Adding a language will copy the default English terms to a new folder so you can start translating them.' }}
                    </div>

                    <div class="mb-3">
                        <label class="admin-form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. French" required>
                    </div>

                    <div class="mb-0">
                        <label class="admin-form-label">{{ __('messages.code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="o_valuer" class="form-control" placeholder="e.g. fr" required>
                        <div class="admin-form-note">{{ __('messages.code_format_info') ?? 'Must be standard ISO code (en, ar, fr, es, etc.) without spaces.' }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteLanguageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_language') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="admin-modal-icon is-danger">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_language') }}</h4>
                <p class="text-muted mb-3" id="deleteLanguageName"></p>
                <div class="alert alert-warning mb-0">
                    <i class="feather-alert-triangle me-2"></i>{{ __('messages.delete_language_warning') ?? 'This action cannot be undone. All language files and data will be physically removed from the server permanently.' }}
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="" method="POST" id="deleteLanguageForm" class="d-inline">
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
        const deleteModal = document.getElementById('deleteLanguageModal');
        const deleteForm = document.getElementById('deleteLanguageForm');
        const deleteName = document.getElementById('deleteLanguageName');

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const languageId = trigger.getAttribute('data-id');
                const languageName = trigger.getAttribute('data-name') || '';
                const languageCode = trigger.getAttribute('data-code') || '';

                deleteForm.setAttribute('action', "{{ route('admin.languages.delete', ['id' => '__ID__']) }}".replace('__ID__', languageId));
                deleteName.textContent = languageName + (languageCode ? ' (' + languageCode + ')' : '');
            });
        }
    });
</script>
@endpush
