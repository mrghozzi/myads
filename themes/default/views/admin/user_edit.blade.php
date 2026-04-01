@extends('theme::layouts.admin')

@section('title', __('messages.edit_user'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.users') }}">{{ __('messages.users') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.edit_user') }}</div>
            <h1 class="admin-hero__title">{{ $user->username }}</h1>

            <div class="admin-profile-card mt-4">
                <div class="admin-profile-card__avatar">
                    <img src="{{ $user->img ? asset($user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $user->username }}">
                </div>
                <div>
                    <div class="admin-person__name mb-1">
                        {{ $user->username }}
                        @if($user->ucheck == 1)
                            <i class="bi bi-patch-check-fill text-primary" title="{{ __('messages.Verified') }}"></i>
                        @endif
                    </div>
                    <div class="admin-inline-meta">
                        <span><i class="feather-mail"></i>{{ $user->email }}</span>
                        <span><i class="feather-hash"></i>{{ $slug }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-summary-grid mt-4">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.points') }}</span>
                    <span class="admin-summary-value">{{ number_format((float) $user->pts, 2) }}</span>
                    <span class="admin-summary-meta">PTS</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('Exchange Visits PTS') }}</span>
                    <span class="admin-summary-value">{{ number_format((float) $user->vu, 2) }}</span>
                    <span class="admin-summary-meta">VU</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.smart_ads') }}</span>
                    <span class="admin-summary-value">{{ number_format((float) $user->nsmart, 2) }}</span>
                    <span class="admin-summary-meta">{{ __('messages.smart_ads_credits_admin') }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-link-grid w-100">
                <a href="{{ route('admin.users') }}" class="btn btn-primary admin-block-link">
                    <i class="feather-users"></i>
                    <span>{{ __('List Users') }}</span>
                </a>
                <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="btn btn-info admin-block-link text-white">
                    <i class="feather-user"></i>
                    <span>{{ __('messages.view_profile') }}</span>
                </a>
                <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="btn btn-warning admin-block-link text-dark">
                    <i class="feather-link"></i>
                    <span>{{ __('messages.Banners') }}</span>
                </a>
                <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="btn btn-success admin-block-link">
                    <i class="feather-eye"></i>
                    <span>{{ __('messages.Links') }}</span>
                </a>
                <a href="{{ route('admin.smart_ads', ['user_id' => $user->id]) }}" class="btn btn-dark admin-block-link">
                    <i class="feather-target"></i>
                    <span>{{ __('messages.smart_ads') }}</span>
                </a>
            </div>
        </div>
    </section>

    <div class="admin-split-grid">
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.edit_user') }}</span>
                    <h2 class="admin-panel__title">{{ __('Edit User Details') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="admin-section-stack">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-grid">
                        <div>
                            <label class="admin-form-label">{{ __('messages.username') }}</label>
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                            <span class="admin-form-note">{{ __('Login Identity') }}</span>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('User Slug') }}</label>
                            <input type="text" class="form-control" name="slug" value="{{ $slug }}" required>
                            <span class="admin-form-note">{{ __('Profile URL Handle') }}</span>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('Verified Account') }}</label>
                            <select class="form-select" name="ucheck">
                                <option value="0" {{ $user->ucheck == 0 ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                <option value="1" {{ $user->ucheck == 1 ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('messages.pts') }}</label>
                            <input type="number" step="0.01" class="form-control" name="pts" value="{{ $user->pts }}" required>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('Exchange Visits PTS') }} (vu)</label>
                            <input type="number" step="0.01" class="form-control" name="vu" value="{{ $user->vu }}" required>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('Banner Ads PTS') }} (nvu)</label>
                            <input type="number" step="0.01" class="form-control" name="nvu" value="{{ $user->nvu }}" required>
                        </div>
                        <div>
                            <label class="admin-form-label">{{ __('Text Ads PTS') }} (nlink)</label>
                            <input type="number" step="0.01" class="form-control" name="nlink" value="{{ $user->nlink }}" required>
                        </div>
                        <div class="admin-form-grid__full">
                            <label class="admin-form-label">{{ __('messages.smart_ads_credits_admin') }}</label>
                            <input type="number" step="0.01" class="form-control" name="nsmart" value="{{ $user->nsmart }}" required>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-primary">{{ __('Update User') }}</button>
                        <a href="{{ route('admin.users') }}" class="btn btn-light">{{ __('messages.back') ?? 'Back' }}</a>
                    </div>
                </form>
            </div>
        </section>

        <aside class="admin-section-stack">
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.new_password') }}</span>
                        <h2 class="admin-panel__title">{{ __('Change Password') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <form action="{{ route('admin.users.password', $user->id) }}" method="POST" class="admin-section-stack">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="admin-form-label">{{ __('messages.new_password') }}</label>
                            <input type="password" class="form-control" name="password" required minlength="8" autocomplete="new-password">
                            <span class="admin-form-note">{{ __('Minimum 8 characters') }}</span>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark">{{ __('Update Password') }}</button>
                    </form>
                </div>
            </section>

            <section class="admin-note-card">
                <span class="admin-note-label">{{ __('messages.actions') }}</span>
                <span class="admin-note-copy">{{ __('messages.users') }} / {{ __('messages.edit_user') }}</span>
                <ul class="admin-compact-list mt-3">
                    <li><i class="feather-link"></i><span>{{ __('messages.Banners') }}</span></li>
                    <li><i class="feather-eye"></i><span>{{ __('messages.Links') }}</span></li>
                    <li><i class="feather-target"></i><span>{{ __('messages.smart_ads') }}</span></li>
                </ul>
            </section>
        </aside>
    </div>
</div>
@endsection
