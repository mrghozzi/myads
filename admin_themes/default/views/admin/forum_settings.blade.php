@extends('admin::layouts.admin')

@section('title', __('messages.forum_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.forum_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.forum_settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.forum_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.forum_settings_desc') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success mb-0">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form action="{{ route('admin.forum.settings.update') }}" method="POST" class="row g-4">
                @csrf
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
                <div class="col-md-6">
                    <div class="admin-utility-card h-100">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="attachments_enabled" name="attachments_enabled" value="1" {{ old('attachments_enabled', $forumSettings['attachments_enabled'] ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="attachments_enabled">{{ __('messages.attachments_enabled') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_role_badges" name="show_role_badges" value="1" {{ old('show_role_badges', $forumSettings['show_role_badges'] ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_role_badges">{{ __('messages.show_role_badges') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-1"></i>{{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
