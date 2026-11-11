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
            <div class="admin-hero__actions">
                <a href="{{ route('groups.show', $group) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="feather-external-link me-1"></i> {{ __('messages.view') }}
                </a>
            </div>
        </section>

                            <div class="row mt-4">
                                <div class="col-lg-8">
                                    <section class="admin-panel mb-4">
                                        <div class="admin-panel__header">
                                            <div>
                                                <span class="admin-panel__eyebrow">{{ __('messages.Settings') }}</span>
                                                <h2 class="admin-panel__title">{{ __('messages.general_settings') }}</h2>
                                            </div>
                                        </div>

                                        <div class="admin-panel__body">
                                            <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">{{ __('messages.name') }}</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ old('name', $group->name) }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label
                                                            class="form-label fw-bold">{{ __('messages.groups_category') }}</label>
                                                        <select name="category" class="form-select">
                                                            @foreach($categories as $catKey => $catLabelKey)
                                                                <option value="{{ $catKey }}" {{ old('category', $group->category) === $catKey ? 'selected' : '' }}>
                                                                    {{ __($catLabelKey) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label
                                                                class="form-label fw-bold">{{ __('messages.groups_privacy') }}</label>
                                                            <select name="privacy" class="form-select">
                                                                <option value="public" {{ old('privacy', $group->privacy) === 'public' ? 'selected' : '' }}>
                                                                    {{ __('messages.groups_public') }}</option>
                                                                <option value="private_request" {{ old('privacy', $group->privacy) === 'private_request' ? 'selected' : '' }}>{{ __('messages.groups_private') }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label
                                                                class="form-label fw-bold">{{ __('messages.groups_posting_permission') }}</label>
                                                            <select name="posting_permission" class="form-select">
                                                                <option value="all_members" {{ old('posting_permission', $group->posting_permission) === 'all_members' ? 'selected' : '' }}>
                                                                    {{ __('messages.groups_posting_all') }}
                                                                </option>
                                                                <option value="moderators_only" {{ old('posting_permission', $group->posting_permission) === 'moderators_only' ? 'selected' : '' }}>
                                                                    {{ __('messages.groups_posting_moderators') }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label
                                                                class="form-label fw-bold">{{ __('messages.groups_join_fee') }}</label>
                                                            <input type="number" min="0" max="100000" name="join_fee_pts"
                                                                class="form-control"
                                                                value="{{ old('join_fee_pts', $group->join_fee_pts) }}">
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('messages.owner') }} (User
                                                            ID)</label>
                                                        <input type="number" name="owner_id" class="form-control"
                                                            value="{{ old('owner_id', $group->owner_id) }}" required>
                                                        <small
                                                            class="text-muted">{{ __('messages.admin_groups_owner_id_hint') ?? 'User ID of the community owner' }}</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label fw-bold">{{ __('messages.short_description') }}</label>
                                                        <textarea name="short_description" class="form-control"
                                                            rows="2">{{ old('short_description', $group->short_description) }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('messages.about') }}</label>
                                                        <textarea name="description" class="form-control"
                                                            rows="5">{{ old('description', $group->description) }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">{{ __('messages.groups_rules') }}
                                                            (Markdown)</label>
                                                        <textarea name="rules_markdown" class="form-control"
                                                            rows="5">{{ old('rules_markdown', $group->rules_markdown) }}</textarea>
                                                    </div>

                                                    <div class="mt-4 pt-3 border-top">
                                                        <button type="submit" class="btn btn-primary px-4">
                                                            <i class="feather-save me-2"></i>
                                                            {{ __('messages.save_changes') }}
                                                        </button>
                                                        <a href="{{ route('admin.groups.index') }}"
                                                            class="btn btn-outline-secondary ms-2">
                                                            {{ __('messages.cancel') }}
                                                        </a>
                                                    </div>
                                            </form>
                                        </div>
                                    </section>
                                </div>

                                    <div class="col-lg-4">
                                        <!-- Media Moderation Box -->
                                        <section class="admin-panel mb-4">
                                            <div class="admin-panel__header">
                                                <div>
                                                    <span
                                                        class="admin-panel__eyebrow">{{ __('messages.media') ?? 'Media' }}</span>
                                                    <h2 class="admin-panel__title">
                                                        {{ __('messages.images') ?? 'Group Images' }}</h2>
                                                </div>
                                            </div>
                                            <div class="admin-panel__body">
                                                <div class="mb-4">
                                                    <label
                                                        class="form-label fw-bold d-block">{{ __('messages.avatar') ?? 'Avatar' }}</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $group->avatarUrl() }}" alt="Avatar"
                                                            style="width: 60px; height: 60px; border-radius: 12px; object-fit: cover; border: 2px solid #e8ebf5;">
                                                        @if($group->avatar_path)
                                                            <form method="POST"
                                                                action="{{ route('admin.groups.media.clear', $group) }}"
                                                                onsubmit="return confirm('Clear custom avatar?')">
                                                                @csrf
                                                                <input type="hidden" name="type" value="avatar">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="feather-trash me-1"></i>
                                                                    {{ __('messages.delete') }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge bg-light text-muted border">Default</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label fw-bold d-block">{{ __('messages.cover') ?? 'Cover' }}</label>
                                                        <div class="mb-2">
                                                            <img src="{{ $group->coverUrl() }}" alt="Cover"
                                                                style="width: 100%; height: 90px; border-radius: 12px; object-fit: cover; border: 2px solid #e8ebf5;">
                                                        </div>
                                                        @if($group->cover_path)
                                                            <form method="POST"
                                                                action="{{ route('admin.groups.media.clear', $group) }}"
                                                                onsubmit="return confirm('Clear custom cover?')">
                                                                @csrf
                                                                <input type="hidden" name="type" value="cover">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="feather-trash me-1"></i>
                                                                    {{ __('messages.delete') }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge bg-light text-muted border">Default</span>
                                                        @endif
                                                    </div>
                                                </div>
        </section>
    </div>
    </div>
    </div>
@endsection