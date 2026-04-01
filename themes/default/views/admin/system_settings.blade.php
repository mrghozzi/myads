@extends('theme::layouts.admin')

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
            <p class="admin-hero__copy">{{ __('messages.social_mail_config') ?? 'Social Login & Mail Configuration' }}</p>
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
                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-mail me-2"></i>{{ __('messages.smtp_settings') ?? 'SMTP Settings' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_host') ?? 'Mail Host' }}</label>
                    <input type="text" name="MAIL_HOST" class="form-control" value="{{ env('MAIL_HOST') }}" placeholder="smtp.mailtrap.io">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_port') ?? 'Mail Port' }}</label>
                    <input type="text" name="MAIL_PORT" class="form-control" value="{{ env('MAIL_PORT') }}" placeholder="587">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_username') ?? 'Mail Username' }}</label>
                    <input type="text" name="MAIL_USERNAME" class="form-control" value="{{ env('MAIL_USERNAME') }}" placeholder="{{ __('messages.username') ?? 'Username' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_password') ?? 'Mail Password' }}</label>
                    <input type="password" name="MAIL_PASSWORD" class="form-control" value="{{ env('MAIL_PASSWORD') }}" placeholder="{{ __('messages.password') ?? 'Password' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_encryption') ?? 'Mail Encryption' }}</label>
                    <select name="MAIL_ENCRYPTION" class="form-select">
                        <option value="null" {{ env('MAIL_ENCRYPTION') == 'null' ? 'selected' : '' }}>{{ __('messages.none') ?? 'None' }}</option>
                        <option value="tls" {{ env('MAIL_ENCRYPTION') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ env('MAIL_ENCRYPTION') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_from_address') ?? 'Mail From Address' }}</label>
                    <input type="email" name="MAIL_FROM_ADDRESS" class="form-control" value="{{ env('MAIL_FROM_ADDRESS') }}" placeholder="hello@example.com">
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
