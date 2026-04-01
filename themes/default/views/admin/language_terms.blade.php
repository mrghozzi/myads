@extends('theme::layouts.admin')

@section('title', __('messages.edit_terms') ?? 'Edit Terms')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Header -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.edit_terms') ?? 'Edit Terms' }} ({{ $language->name }})</h2>
        <p class="text-muted">{{ __('messages.edit_terms_desc') ?? 'Translate the platform strings into the target language.' }}</p>
        <div class="mt-4">
             <a href="{{ route('admin.languages') }}" class="btn btn-secondary">
                <i class="feather-arrow-left me-2"></i> {{ __('messages.back') ?? 'Back' }}
            </a>
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

            <form action="{{ route('admin.languages.terms.update', $language->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 30%;">{{ __('messages.translation_key') ?? 'Key / English Default' }}</th>
                                <th>{{ __('messages.translation_value') ?? 'Translation Value' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($defaultTerms as $key => $defaultVal)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $key }}</div>
                                    <small class="text-muted d-block mt-1 p-2 bg-light rounded border">{{ $defaultVal }}</small>
                                </td>
                                <td>
                                    <input type="text" name="terms[{{ $key }}]" class="form-control" value="{{ $terms[$key] ?? '' }}" placeholder="{{ __('messages.enter_translation') ?? 'Enter translation...' }}" >
                                </td>
                            </tr>
                            @endforeach
                            <!-- Loop any extra keys that might only exist in the target language but not default -->
                            @foreach($terms as $key => $val)
                                @if(!array_key_exists($key, $defaultTerms))
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $key }}</div>
                                        <small class="text-warning d-block mt-1 p-2 bg-light rounded border">{{ __('messages.key_not_in_default') ?? 'Not in default English file' }}</small>
                                    </td>
                                    <td>
                                        <input type="text" name="terms[{{ $key }}]" class="form-control" value="{{ $val }}">
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="feather-save me-2"></i> {{ __('messages.save_changes') ?? 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
