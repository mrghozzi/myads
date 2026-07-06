@extends('admin::layouts.admin')

@section('title', __('messages.seo_checker_settings'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="seo-shell">
    <section class="admin-hero" style="background: linear-gradient(135deg, rgba(6,78,59,0.98) 0%, rgba(5,150,105,0.96) 52%, rgba(16,185,129,0.92) 100%);">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.seo_checker_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow"><i class="feather-sliders"></i> {{ __('Settings') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.seo_checker_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.seo_checker_manage_access') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('seo_checker.index') }}" target="_blank" class="btn btn-light">
                        <i class="feather-external-link me-2"></i>{{ __('messages.seo_checker') }}
                    </a>
                </div>
            </div>
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_speed_access') }}</span>
                    <span class="admin-summary-value text-capitalize">{{ $settings['speed'] }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_errors_access') }}</span>
                    <span class="admin-summary-value text-capitalize">{{ $settings['errors'] }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_backlinks_access') }}</span>
                    <span class="admin-summary-value text-capitalize">{{ $settings['backlinks'] }}</span>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success mt-4">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card seo-card mt-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.seo_checker.settings') }}" method="POST" class="row g-4">
                @csrf
                
                <div class="col-lg-4">
                    <div class="p-3 border rounded h-100 bg-light-subtle">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded p-2 me-3 shadow-sm">
                                <i class="feather-zap fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ __('messages.seo_speed_access') }}</h5>
                        </div>
                        <p class="text-muted small mb-3">{{ __('messages.seo_speed_desc') }}</p>
                        <select name="speed" class="form-select border-primary-subtle shadow-sm">
                            <option value="guest" {{ $settings['speed'] === 'guest' ? 'selected' : '' }}>{{ __('messages.seo_guest_everyone') }}</option>
                            <option value="member" {{ $settings['speed'] === 'member' ? 'selected' : '' }}>{{ __('messages.seo_member_only') }}</option>
                            <option value="premium" {{ $settings['speed'] === 'premium' ? 'selected' : '' }}>{{ __('messages.seo_premium_only') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-3 border rounded h-100 bg-light-subtle">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger text-white rounded p-2 me-3 shadow-sm">
                                <i class="feather-alert-triangle fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ __('messages.seo_errors_access') }}</h5>
                        </div>
                        <p class="text-muted small mb-3">{{ __('messages.seo_errors_desc') }}</p>
                        <select name="errors" class="form-select border-danger-subtle shadow-sm">
                            <option value="guest" {{ $settings['errors'] === 'guest' ? 'selected' : '' }}>{{ __('messages.seo_guest_everyone') }}</option>
                            <option value="member" {{ $settings['errors'] === 'member' ? 'selected' : '' }}>{{ __('messages.seo_member_only') }}</option>
                            <option value="premium" {{ $settings['errors'] === 'premium' ? 'selected' : '' }}>{{ __('messages.seo_premium_only') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-3 border rounded h-100 bg-light-subtle">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded p-2 me-3 shadow-sm">
                                <i class="feather-link fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ __('messages.seo_backlinks_access') }}</h5>
                        </div>
                        <p class="text-muted small mb-3">{{ __('messages.seo_backlinks_desc') }}</p>
                        <select name="backlinks" class="form-select border-info-subtle shadow-sm">
                            <option value="guest" {{ $settings['backlinks'] === 'guest' ? 'selected' : '' }}>{{ __('messages.seo_guest_everyone') }}</option>
                            <option value="member" {{ $settings['backlinks'] === 'member' ? 'selected' : '' }}>{{ __('messages.seo_member_only') }}</option>
                            <option value="premium" {{ $settings['backlinks'] === 'premium' ? 'selected' : '' }}>{{ __('messages.seo_premium_only') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 mt-4 text-end border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="feather-save me-2"></i> {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
