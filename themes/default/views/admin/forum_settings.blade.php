@extends('theme::layouts.admin')

@section('title', __('messages.forum_settings'))

@section('content')
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-1">{{ __('messages.forum_settings') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.forum_settings_desc') }}</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.forum.settings.update') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.topics_per_page') }}</label>
                            <input type="number" min="1" max="100" class="form-control" name="topics_per_page" value="{{ old('topics_per_page', $forumSettings['topics_per_page'] ?? 21) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.max_attachments_per_topic') }}</label>
                            <input type="number" min="1" max="20" class="form-control" name="max_attachments_per_topic" value="{{ old('max_attachments_per_topic', $forumSettings['max_attachments_per_topic'] ?? 5) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.max_attachment_size') }} (KB)</label>
                            <input type="number" min="512" max="51200" class="form-control" name="max_attachment_size_kb" value="{{ old('max_attachment_size_kb', $forumSettings['max_attachment_size_kb'] ?? 10240) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('messages.allowed_attachment_extensions') }}</label>
                            <input type="text" class="form-control" name="allowed_attachment_extensions" value="{{ old('allowed_attachment_extensions', $forumSettings['allowed_attachment_extensions'] ?? '') }}" required>
                            <small class="text-muted">{{ __('messages.allowed_attachment_extensions_hint') }}</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="attachments_enabled" name="attachments_enabled" value="1" {{ old('attachments_enabled', $forumSettings['attachments_enabled'] ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="attachments_enabled">{{ __('messages.attachments_enabled') }}</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_role_badges" name="show_role_badges" value="1" {{ old('show_role_badges', $forumSettings['show_role_badges'] ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_role_badges">{{ __('messages.show_role_badges') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i>{{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
