@extends('admin::layouts.admin')

@section('title', __('messages.groups_edit_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.groups.index') }}">{{ __('messages.admin_groups_title') }}</a></li>
                <li>{{ __('messages.Settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ $group->name }}</div>
            <h1 class="admin-hero__title">{{ __('messages.groups_edit_title') }}</h1>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.Settings') }}</span>
                <h2 class="admin-panel__title">{{ $group->name }}</h2>
            </div>
        </div>

        <div class="admin-panel__body">
            <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $group->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.groups_privacy') }}</label>
                        <select name="privacy" class="form-select">
                            <option value="public" {{ old('privacy', $group->privacy) === 'public' ? 'selected' : '' }}>{{ __('messages.groups_public') }}</option>
                            <option value="private_request" {{ old('privacy', $group->privacy) === 'private_request' ? 'selected' : '' }}>{{ __('messages.groups_private') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.owner') }} (User ID)</label>
                    <input type="number" name="owner_id" class="form-control" value="{{ old('owner_id', $group->owner_id) }}" required>
                    <small class="text-muted">{{ __('messages.admin_groups_owner_id_hint') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.short_description') }}</label>
                    <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $group->short_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.about') }}</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description', $group->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.groups_rules') }} (Markdown)</label>
                    <textarea name="rules_markdown" class="form-control" rows="5">{{ old('rules_markdown', $group->rules_markdown) }}</textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>
                        {{ __('messages.save_changes') }}
                    </button>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-secondary ms-2">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
