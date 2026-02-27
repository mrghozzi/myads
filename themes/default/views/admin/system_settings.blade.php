@extends('theme::layouts.admin')

@section('title', __('messages.system_settings'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.system_settings') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.system_settings') }}</li>
        </ul>
    </div>
</div>

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.social_mail_config') ?? 'Social Login & Mail Configuration' }}</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('admin.settings.system.update') }}" method="POST">
            @csrf
            
            <h6 class="mb-3 text-primary"><i class="feather-facebook me-2"></i>{{ __('messages.facebook_login') ?? 'Facebook Login' }}</h6>
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.facebook_client_id') ?? 'Facebook Client ID' }}</label>
                    <input type="text" name="FACEBOOK_CLIENT_ID" class="form-control" value="{{ env('FACEBOOK_CLIENT_ID') }}" placeholder="{{ __('messages.enter_facebook_app_id') ?? 'Enter Facebook App ID' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.facebook_client_secret') ?? 'Facebook Client Secret' }}</label>
                    <input type="password" name="FACEBOOK_CLIENT_SECRET" class="form-control" value="{{ env('FACEBOOK_CLIENT_SECRET') }}" placeholder="{{ __('messages.enter_facebook_app_secret') ?? 'Enter Facebook App Secret' }}">
                </div>
            </div>

            <hr class="my-4">

            <h6 class="mb-3 text-primary"><i class="feather-chrome me-2"></i>{{ __('messages.google_login') ?? 'Google Login' }}</h6>
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.google_client_id') ?? 'Google Client ID' }}</label>
                    <input type="text" name="GOOGLE_CLIENT_ID" class="form-control" value="{{ env('GOOGLE_CLIENT_ID') }}" placeholder="{{ __('messages.enter_google_client_id') ?? 'Enter Google Client ID' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.google_client_secret') ?? 'Google Client Secret' }}</label>
                    <input type="password" name="GOOGLE_CLIENT_SECRET" class="form-control" value="{{ env('GOOGLE_CLIENT_SECRET') }}" placeholder="{{ __('messages.enter_google_client_secret') ?? 'Enter Google Client Secret' }}">
                </div>
            </div>

            <hr class="my-4">

            <h6 class="mb-3 text-primary"><i class="feather-mail me-2"></i>{{ __('messages.smtp_settings') ?? 'SMTP Settings' }}</h6>
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.mail_host') ?? 'Mail Host' }}</label>
                    <input type="text" name="MAIL_HOST" class="form-control" value="{{ env('MAIL_HOST') }}" placeholder="smtp.mailtrap.io">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_port') ?? 'Mail Port' }}</label>
                    <input type="text" name="MAIL_PORT" class="form-control" value="{{ env('MAIL_PORT') }}" placeholder="587">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.mail_username') ?? 'Mail Username' }}</label>
                    <input type="text" name="MAIL_USERNAME" class="form-control" value="{{ env('MAIL_USERNAME') }}" placeholder="{{ __('messages.username') ?? 'Username' }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.mail_password') ?? 'Mail Password' }}</label>
                    <input type="password" name="MAIL_PASSWORD" class="form-control" value="{{ env('MAIL_PASSWORD') }}" placeholder="{{ __('messages.password') ?? 'Password' }}">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
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
            </div>

            <div class="alert alert-warning">
                <i class="feather-info me-2"></i> التغييرات هنا ستؤثر مباشرة على ملف الـ <strong>.env</strong>. تأكد من صحة البيانات قبل الحفظ.
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
        </form>
    </div>
</div>
@endsection
