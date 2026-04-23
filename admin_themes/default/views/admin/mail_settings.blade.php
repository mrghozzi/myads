@extends('admin::layouts.admin')

@section('title', __('messages.mail_settings_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.mail_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.mail_settings_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.mail_settings_desc') }}</p>
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
            <div class="alert alert-info mb-4">
                <i class="feather-info me-2"></i>{{ __('messages.mail_settings_db_notice') }}
            </div>

            <form action="{{ route('admin.settings.mail.update') }}" method="POST" class="row g-4" id="mail-settings-form">
                @csrf

                {{-- Mail Driver --}}
                <div class="col-12">
                    <h3 class="h5 mb-0 text-primary"><i class="feather-send me-2"></i>{{ __('messages.mail_mailer') }}</h3>
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="mail_mailer">{{ __('messages.mail_mailer') }}</label>
                    <select name="mail_mailer" id="mail_mailer" class="form-select">
                        <option value="smtp" {{ old('mail_mailer', $settings->mail_mailer) === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ old('mail_mailer', $settings->mail_mailer) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="log" {{ old('mail_mailer', $settings->mail_mailer) === 'log' ? 'selected' : '' }}>Log</option>
                        <option value="array" {{ old('mail_mailer', $settings->mail_mailer) === 'array' ? 'selected' : '' }}>Array</option>
                    </select>
                </div>

                <div class="col-12"><hr class="my-0"></div>

                {{-- SMTP Configuration (shown only when mailer=smtp) --}}
                <div id="smtp-section" class="col-12">
                    <div class="row g-4">
                        <div class="col-12">
                            <h3 class="h5 mb-0 text-primary"><i class="feather-server me-2"></i>{{ __('messages.mail_smtp_settings') }}</h3>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mail_host">{{ __('messages.mail_host') }}</label>
                            <input type="text" name="mail_host" id="mail_host" class="form-control"
                                   value="{{ old('mail_host', $settings->mail_host) }}" placeholder="smtp.example.com">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mail_port">{{ __('messages.mail_port') }}</label>
                            <input type="number" name="mail_port" id="mail_port" class="form-control"
                                   value="{{ old('mail_port', $settings->mail_port) }}" placeholder="587" min="1" max="65535">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mail_username">{{ __('messages.mail_username') }}</label>
                            <input type="text" name="mail_username" id="mail_username" class="form-control"
                                   value="{{ old('mail_username', $settings->mail_username) }}" placeholder="{{ __('messages.mail_username') }}">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mail_password">{{ __('messages.mail_password') }}</label>
                            <input type="password" name="mail_password" id="mail_password" class="form-control"
                                   value="" placeholder="{{ __('messages.mail_password_hint') }}">
                            <small class="text-muted">{{ __('messages.mail_password_hint') }}</small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mail_encryption">{{ __('messages.mail_encryption') }}</label>
                            <select name="mail_encryption" id="mail_encryption" class="form-select">
                                <option value="" {{ empty(old('mail_encryption', $settings->mail_encryption)) ? 'selected' : '' }}>{{ __('messages.mail_encryption_none') }}</option>
                                <option value="tls" {{ old('mail_encryption', $settings->mail_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12"><hr class="my-0"></div>

                {{-- Sender Information (always visible) --}}
                <div class="col-12">
                    <h3 class="h5 mb-0 text-primary"><i class="feather-mail me-2"></i>{{ __('messages.mail_sender_info') }}</h3>
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="mail_from_address">{{ __('messages.mail_from_address') }}</label>
                    <input type="email" name="mail_from_address" id="mail_from_address" class="form-control"
                           value="{{ old('mail_from_address', $settings->mail_from_address) }}" placeholder="hello@example.com">
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="mail_from_name">{{ __('messages.mail_from_name') }}</label>
                    <input type="text" name="mail_from_name" id="mail_from_name" class="form-control"
                           value="{{ old('mail_from_name', $settings->mail_from_name) }}" placeholder="{{ config('app.name', 'MyAds') }}">
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var mailerSelect = document.getElementById('mail_mailer');
    var smtpSection = document.getElementById('smtp-section');

    function toggleSmtp() {
        if (mailerSelect.value === 'smtp') {
            smtpSection.style.display = '';
        } else {
            smtpSection.style.display = 'none';
        }
    }

    mailerSelect.addEventListener('change', toggleSmtp);
    toggleSmtp();
})();
</script>
@endpush
