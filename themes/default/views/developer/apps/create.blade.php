@extends('theme::layouts.app')

@section('title', __('messages.create_app'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom p-4">
                    <h1 class="h4 fw-bold mb-0">@lang('messages.create_app')</h1>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('developer.apps.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.app_name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.domain') <span class="text-danger">*</span></label>
                            <input type="url" name="domain" class="form-control @error('domain') is-invalid @enderror" placeholder="https://example.com" value="{{ old('domain') }}" required>
                            @error('domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('messages.description') <span class="text-danger">*</span></label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">@lang('messages.redirect_uris') <span class="text-danger">*</span></label>
                            <textarea name="redirect_uris" rows="2" class="form-control @error('redirect_uris') is-invalid @enderror" placeholder="https://example.com/callback, https://example.com/auth" required>{{ old('redirect_uris') }}</textarea>
                            <div class="form-text">@lang('messages.redirect_uris_help')</div>
                            @error('redirect_uris') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2">@lang('messages.requested_scopes')</h5>
                        <div class="row mb-4">
                            @foreach($scopes as $scopeId => $scope)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="requested_scopes[]" value="{{ $scopeId }}" id="scope_{{ str_replace('.', '_', $scopeId) }}" @if(is_array(old('requested_scopes')) && in_array($scopeId, old('requested_scopes'))) checked @endif>
                                        <label class="form-check-label" for="scope_{{ str_replace('.', '_', $scopeId) }}">
                                            <strong>@lang($scope['name'])</strong>
                                            @if($scope['is_sensitive']) <span class="badge bg-danger ms-1">@lang('messages.sensitive')</span> @endif
                                            <div class="small text-muted">@lang($scope['description'])</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('developer.apps.index') }}" class="btn btn-light">@lang('messages.cancel')</a>
                            <button type="submit" class="btn btn-primary px-4">@lang('messages.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
