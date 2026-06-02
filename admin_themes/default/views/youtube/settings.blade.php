@extends('admin::layouts.admin')

@section('title', __('messages.yt_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.youtube.index') }}">{{ __('messages.yt_campaigns') }}</a></li>
                <li>{{ __('messages.settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.yt_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.yt_exchange_settings_desc') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <a href="{{ route('admin.youtube.index') }}" class="btn btn-secondary admin-icon-btn" title="{{ __('messages.back_to_campaigns') }}">
                    <i class="feather-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.settings') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.yt_exchange') }}</h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            @if(session('success'))
                <div class="alert alert-success m-4">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.youtube.settings.update') }}" method="POST" class="p-4">
                @csrf
                <div class="row gx-4 gy-4">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold text-muted text-uppercase fs-12">{{ __('messages.min_view_duration') }}</label>
                            <input type="number" name="yt_min_duration" class="form-control admin-input" value="{{ App\Models\Option::where('name', 'yt_min_duration')->value('o_valuer') ?? 15 }}" min="15">
                            <small class="text-muted d-block mt-2">{{ __('messages.min_view_duration_help') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold text-muted text-uppercase fs-12">{{ __('messages.min_cost_per_sec') }}</label>
                            <input type="number" step="0.01" name="yt_cost_per_second" class="form-control admin-input" value="{{ App\Models\Option::where('name', 'yt_cost_per_second')->value('o_valuer') ?? 0.05 }}" min="0.01">
                            <small class="text-muted d-block mt-2">{{ __('messages.min_cost_per_sec_help') }}</small>
                        </div>
                    </div>
                </div>

                <hr class="admin-divider my-4">

                <div class="d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="feather-save me-2"></i> {{ __('messages.save_settings') }}
                    </button>
                    <a href="{{ route('admin.youtube.index') }}" class="btn btn-light btn-lg rounded-pill px-4">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
