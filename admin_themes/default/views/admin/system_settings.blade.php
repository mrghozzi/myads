@extends('admin::layouts.admin')

@section('title', __('messages.system_settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.system_settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.system_settings') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.social_login_config') ?? 'Social Login Configuration' }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form action="{{ route('admin.settings.system.update') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-facebook me-2"></i>{{ __('messages.facebook_login') ?? 'Facebook Login' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.facebook_client_id') ?? 'Facebook Client ID' }}</label>
                    <input type="text" name="FACEBOOK_CLIENT_ID" class="form-control" value="{{ env('FACEBOOK_CLIENT_ID') }}" placeholder="{{ __('messages.enter_facebook_app_id') ?? 'Enter Facebook App ID' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.facebook_client_secret') ?? 'Facebook Client Secret' }}</label>
                    <input type="password" name="FACEBOOK_CLIENT_SECRET" class="form-control" value="{{ env('FACEBOOK_CLIENT_SECRET') }}" placeholder="{{ __('messages.enter_facebook_app_secret') ?? 'Enter Facebook App Secret' }}">
                </div>
                <div class="col-12 mt-2">
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.85rem;">
                        <strong>{{ __('messages.redirect_uri') }}:</strong> 
                        <code>{{ route('social.callback', 'facebook') }}</code>
                        <div class="mt-1 text-muted">{{ __('messages.redirect_uri_hint') }}</div>
                    </div>
                </div>
                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-chrome me-2"></i>{{ __('messages.google_login') ?? 'Google Login' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.google_client_id') ?? 'Google Client ID' }}</label>
                    <input type="text" name="GOOGLE_CLIENT_ID" class="form-control" value="{{ env('GOOGLE_CLIENT_ID') }}" placeholder="{{ __('messages.enter_google_client_id') ?? 'Enter Google Client ID' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.google_client_secret') ?? 'Google Client Secret' }}</label>
                    <input type="password" name="GOOGLE_CLIENT_SECRET" class="form-control" value="{{ env('GOOGLE_CLIENT_SECRET') }}" placeholder="{{ __('messages.enter_google_client_secret') ?? 'Enter Google Client Secret' }}">
                </div>
                <div class="col-12 mt-2">
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.85rem;">
                        <strong>{{ __('messages.redirect_uri') }}:</strong> 
                        <code>{{ route('social.callback', 'google') }}</code>
                        <div class="mt-1 text-muted">{{ __('messages.redirect_uri_hint') }}</div>
                    </div>
                </div>
                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="fa-brands fa-buysellads me-2"></i>{{ __('messages.adstn_login') ?? 'ADStn Login' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.adstn_client_id') ?? 'ADStn Client ID' }}</label>
                    <input type="text" name="ADSTN_CLIENT_ID" class="form-control" value="{{ env('ADSTN_CLIENT_ID') }}" placeholder="{{ __('messages.enter_adstn_client_id') ?? 'Enter ADStn Client ID' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.adstn_client_secret') ?? 'ADStn Client Secret' }}</label>
                    <input type="password" name="ADSTN_CLIENT_SECRET" class="form-control" value="{{ env('ADSTN_CLIENT_SECRET') }}" placeholder="{{ __('messages.enter_adstn_client_secret') ?? 'Enter ADStn Client Secret' }}">
                </div>
                <div class="col-12 mt-2">
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.85rem;">
                        <strong>{{ __('messages.redirect_uri') }}:</strong> 
                        <code>{{ route('social.callback', 'adstn') }}</code>
                        <div class="mt-1 text-muted">{{ __('messages.redirect_uri_hint') }}</div>
                    </div>
                </div>
                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="feather-mail me-2"></i>
                        {{ __('messages.mail_settings_title') }}:
                        <a href="{{ route('admin.settings.mail') }}" class="fw-bold">{{ route('admin.settings.mail') }}</a>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        <i class="feather-info me-2"></i>{{ __('messages.warning') ?? 'Warning' }} <strong>.env</strong>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
