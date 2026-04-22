@extends('theme::layouts.app')

@section('title', $app->name)

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('developer.index') }}">@lang('messages.dev_platform')</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('developer.apps.index') }}">@lang('messages.my_apps')</a></li>
                    <li class="breadcrumb-item active">{{ $app->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0">{{ $app->name }}</h1>
        </div>
        <div class="col-auto">
            <span class="badge bg-{{ $app->status === 'active' ? 'success' : ($app->status === 'draft' ? 'secondary' : 'warning') }} fs-6 px-3 py-2">
                @lang('messages.app_status_' . $app->status)
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($app->status === 'draft')
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i> @lang('messages.app_draft_notice')
            </div>
            <form action="{{ route('developer.apps.submit', $app->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">@lang('messages.submit_for_review')</button>
            </form>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">@lang('messages.app_settings')</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('developer.apps.update', $app->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.app_name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $app->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.domain') <span class="text-danger">*</span></label>
                            <input type="url" name="domain" class="form-control @error('domain') is-invalid @enderror" value="{{ old('domain', $app->domain) }}" required>
                            @error('domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.description') <span class="text-danger">*</span></label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $app->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">@lang('messages.redirect_uris') <span class="text-danger">*</span></label>
                            <textarea name="redirect_uris" rows="2" class="form-control @error('redirect_uris') is-invalid @enderror" required>{{ old('redirect_uris', implode(', ', $app->redirect_uris ?? [])) }}</textarea>
                            <div class="form-text">@lang('messages.redirect_uris_help')</div>
                            @error('redirect_uris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2">@lang('messages.requested_scopes')</h5>
                        <div class="row mb-4">
                            @foreach($scopes as $scopeId => $scope)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="requested_scopes[]" value="{{ $scopeId }}" id="scope_{{ str_replace('.', '_', $scopeId) }}" @if(is_array($app->requested_scopes) && in_array($scopeId, $app->requested_scopes)) checked @endif>
                                        <label class="form-check-label" for="scope_{{ str_replace('.', '_', $scopeId) }}">
                                            <strong>@lang($scope['name'])</strong>
                                            @if($scope['is_sensitive']) <span class="badge bg-danger ms-1">@lang('messages.sensitive')</span> @endif
                                            <div class="small text-muted">@lang($scope['description'])</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('messages.save_changes')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">@lang('messages.api_credentials')</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1">Client ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" value="{{ $app->client_id }}" readonly id="clientIdInput">
                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('clientIdInput').value)"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1">Client Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control font-monospace" value="{{ $app->client_secret }}" readonly id="clientSecretInput">
                            <button class="btn btn-outline-secondary" type="button" onclick="const input = document.getElementById('clientSecretInput'); input.type = input.type === 'password' ? 'text' : 'password';"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('clientSecretInput').value)"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>

                    <form action="{{ route('developer.apps.rotate_secret', $app->id) }}" method="POST" onsubmit="return confirm('@lang('messages.rotate_secret_confirm')')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-sync-alt me-1"></i> @lang('messages.rotate_secret')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
