@extends('theme::layouts.admin')

@section('title', __('messages.languages'))

@section('content')
<!-- Header -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.languages') }}</h2>
        <p class="text-muted">{{ __('messages.languages_desc') ?? 'Manage your platform languages and translations.' }}</p>
        <div class="mt-4">
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                <i class="feather-plus me-2"></i> {{ __('messages.new_language') }}
            </button>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    <div class="card">
        <div class="card-body">
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

            @if($languages->isEmpty())
                <div class="text-center py-5">
                    <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mb-3 mx-auto">
                        <i class="feather-globe"></i>
                    </div>
                    <h4>{{ __('messages.no_languages_found') ?? 'No languages found' }}</h4>
                    <p class="text-muted">{{ __('messages.no_languages_desc') ?? 'Start by adding your first language.' }}</p>
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        {{ __('messages.new_language') }}
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-end" style="min-width: 150px;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($languages as $language)
                            <tr>
                                <td>
                                    <div class="avatar-text bg-soft-primary text-primary rounded">
                                        {{ strtoupper(substr($language->o_valuer, 0, 2)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $language->name }}</div>
                                    <small class="text-muted">{{ __('messages.language_name') ?? 'Language Name' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $language->o_valuer }}</span>
                                </td>
                                <td>
                                    @if($language->has_folder)
                                        <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i> {{ __('messages.folder_ready') ?? 'Ready' }}</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger"><i class="feather-alert-triangle me-1"></i> {{ __('messages.missing_folder') ?? 'Folder Missing' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.languages.terms', $language->id) }}" class="btn btn-sm btn-soft-primary" title="{{ __('messages.edit_terms') ?? 'Edit Terms' }}">
                                            <i class="feather-edit-3"></i>
                                        </a>

                                        @if($language->has_folder)
                                        <a href="{{ route('admin.languages.export', $language->id) }}" class="btn btn-sm btn-soft-info" title="{{ __('messages.export') ?? 'Export (.zip)' }}">
                                            <i class="feather-download"></i>
                                        </a>
                                        @endif
                                        
                                        @if($language->o_valuer !== 'en')
                                        <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteLangModal{{ $language->id }}" title="{{ __('messages.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $languages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('modals')
<!-- Add Language Modal -->
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
                        <i class="feather-info me-2"></i> {{ __('messages.add_language_info') ?? 'Adding a language will copy the default English terms to a new folder so you can start translating them.' }}
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. French" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="o_valuer" class="form-control" placeholder="e.g. fr" required>
                        <div class="form-text">{{ __('messages.code_format_info') ?? 'Must be standard ISO code (en, ar, fr, es, etc.) without spaces.' }}</div>
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

@if(!$languages->isEmpty())
    @foreach($languages as $language)
    @if($language->o_valuer !== 'en')
    <!-- Delete Language Modal -->
    <div class="modal fade" id="deleteLangModal{{ $language->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.delete_language') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                        <i class="feather-trash-2"></i>
                    </div>
                    <h4>{{ __('messages.confirm_delete_language') }}</h4>
                    <p class="text-muted">{{ $language->name }} ({{ $language->o_valuer }})</p>
                    <div class="alert alert-warning mt-3">
                        <i class="feather-alert-triangle me-2"></i>
                        {{ __('messages.delete_language_warning') ?? 'This action cannot be undone. All language files and data will be physically removed from the server permanently.' }}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <form action="{{ route('admin.languages.delete', $language->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
@endif
@endsection
