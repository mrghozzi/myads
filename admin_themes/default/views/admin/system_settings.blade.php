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
            <p class="admin-hero__copy">{{ __('messages.system_settings_desc') ?? 'Configure app name, environment mode, debug mode, system timezone, sessions, and social OAuth credentials.' }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.system.update') }}" method="POST">
        @csrf

        <!-- General Environment & App Settings -->
        <section class="admin-panel mb-4">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">
                    <i class="feather-settings text-primary me-2"></i>{{ __('messages.general_environment_settings') }}
                </h3>
                <p class="text-muted fs-12 mb-0">{{ __('messages.general_environment_desc') }}</p>
            </div>
            <div class="admin-panel__body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.app_name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-globe"></i></span>
                            <input type="text" name="APP_NAME" class="form-control" value="{{ old('APP_NAME', $systemSettings['APP_NAME'] ?? 'MYADS') }}" required placeholder="MYADS">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.app_url') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-link"></i></span>
                            <input type="url" name="APP_URL" class="form-control" value="{{ old('APP_URL', $systemSettings['APP_URL'] ?? 'http://localhost') }}" required placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.app_env') }}</label>
                        <select name="APP_ENV" class="form-select">
                            <option value="production" {{ ($systemSettings['APP_ENV'] ?? '') === 'production' ? 'selected' : '' }}>{{ __('messages.env_production') }}</option>
                            <option value="local" {{ ($systemSettings['APP_ENV'] ?? '') === 'local' ? 'selected' : '' }}>{{ __('messages.env_local') }}</option>
                            <option value="development" {{ ($systemSettings['APP_ENV'] ?? '') === 'development' ? 'selected' : '' }}>{{ __('messages.env_development') }}</option>
                            <option value="testing" {{ ($systemSettings['APP_ENV'] ?? '') === 'testing' ? 'selected' : '' }}>{{ __('messages.env_testing') }}</option>
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.app_debug') }}</label>
                        <select name="APP_DEBUG" class="form-select {{ ($systemSettings['APP_DEBUG'] ?? '') === 'true' ? 'border-warning' : '' }}">
                            <option value="false" {{ ($systemSettings['APP_DEBUG'] ?? '') === 'false' ? 'selected' : '' }}>{{ __('messages.debug_disabled') }}</option>
                            <option value="true" {{ ($systemSettings['APP_DEBUG'] ?? '') === 'true' ? 'selected' : '' }}>{{ __('messages.debug_enabled') }}</option>
                        </select>
                        <div class="form-text text-muted fs-12 mt-1">
                            <i class="feather-alert-triangle text-warning me-1"></i>{{ __('messages.app_debug_help') }}
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.app_timezone') }}</label>
                        @php
                            $tzCurrent = $systemSettings['APP_TIMEZONE'] ?? 'UTC';
                            $timezones = [
                                'UTC', 'Africa/Tunis', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Algiers',
                                'Asia/Riyadh', 'Asia/Dubai', 'Asia/Amman', 'Asia/Beirut', 'Asia/Baghdad',
                                'Asia/Kuwait', 'Asia/Qatar', 'Asia/Muscat', 'Europe/London', 'Europe/Paris',
                                'Europe/Berlin', 'Europe/Rome', 'Europe/Madrid', 'Europe/Istanbul',
                                'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles',
                                'America/Sao_Paulo', 'Asia/Tokyo', 'Asia/Singapore', 'Australia/Sydney'
                            ];
                        @endphp
                        <select name="APP_TIMEZONE" class="form-select">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}" {{ $tzCurrent === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.default_locale') }}</label>
                        @php
                            $localeCurrent = $systemSettings['APP_LOCALE'] ?? 'en';
                            $locales = [
                                'ar' => 'العربية (Arabic)',
                                'en' => 'English',
                                'fr' => 'Français (French)',
                                'es' => 'Español (Spanish)',
                                'de' => 'Deutsch (German)',
                                'it' => 'Italiano (Italian)',
                                'pt' => 'Português (Portuguese)',
                                'tr' => 'Türkçe (Turkish)',
                                'fa' => 'فارسی (Persian)'
                            ];
                        @endphp
                        <select name="APP_LOCALE" class="form-select">
                            @foreach($locales as $code => $name)
                                <option value="{{ $code }}" {{ $localeCurrent === $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Session & Security Settings -->
        <section class="admin-panel mb-4">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">
                    <i class="feather-lock text-primary me-2"></i>{{ __('messages.session_settings') }}
                </h3>
            </div>
            <div class="admin-panel__body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.session_driver') }}</label>
                        <select name="SESSION_DRIVER" class="form-select">
                            <option value="file" {{ ($systemSettings['SESSION_DRIVER'] ?? '') === 'file' ? 'selected' : '' }}>{{ __('messages.driver_file') }}</option>
                            <option value="cookie" {{ ($systemSettings['SESSION_DRIVER'] ?? '') === 'cookie' ? 'selected' : '' }}>{{ __('messages.driver_cookie') }}</option>
                            <option value="database" {{ ($systemSettings['SESSION_DRIVER'] ?? '') === 'database' ? 'selected' : '' }}>{{ __('messages.driver_database') }}</option>
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">{{ __('messages.session_lifetime') }}</label>
                        <div class="input-group">
                            <input type="number" name="SESSION_LIFETIME" class="form-control" value="{{ old('SESSION_LIFETIME', $systemSettings['SESSION_LIFETIME'] ?? 120) }}" min="1" max="10080" required>
                            <span class="input-group-text"><i class="feather-clock"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Login Settings -->
        <section class="admin-panel mb-4">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">
                    <i class="feather-share-2 text-primary me-2"></i>{{ __('messages.social_login_settings') }}
                </h3>
                <p class="text-muted fs-12 mb-0">{{ __('messages.social_login_config_desc') }}</p>
            </div>
            <div class="admin-panel__body">
                <div class="row g-4">
                    <!-- Facebook Login -->
                    <div class="col-12"><h4 class="h6 mb-0 text-primary"><i class="feather-facebook me-2"></i>{{ __('messages.facebook_login') ?? 'Facebook Login' }}</h4></div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.facebook_client_id') ?? 'Facebook Client ID' }}</label>
                        <input type="text" name="FACEBOOK_CLIENT_ID" class="form-control" value="{{ old('FACEBOOK_CLIENT_ID', $systemSettings['FACEBOOK_CLIENT_ID'] ?? '') }}" placeholder="{{ __('messages.enter_facebook_app_id') ?? 'Enter Facebook App ID' }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.facebook_client_secret') ?? 'Facebook Client Secret' }}</label>
                        <input type="password" name="FACEBOOK_CLIENT_SECRET" class="form-control" value="{{ old('FACEBOOK_CLIENT_SECRET', $systemSettings['FACEBOOK_CLIENT_SECRET'] ?? '') }}" placeholder="{{ __('messages.enter_facebook_app_secret') ?? 'Enter Facebook App Secret' }}">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 px-3 mb-0 fs-12">
                            <strong>{{ __('messages.redirect_uri') }}:</strong> <code>{{ route('social.callback', 'facebook') }}</code>
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <!-- Google Login -->
                    <div class="col-12"><h4 class="h6 mb-0 text-primary"><i class="feather-chrome me-2"></i>{{ __('messages.google_login') ?? 'Google Login' }}</h4></div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.google_client_id') ?? 'Google Client ID' }}</label>
                        <input type="text" name="GOOGLE_CLIENT_ID" class="form-control" value="{{ old('GOOGLE_CLIENT_ID', $systemSettings['GOOGLE_CLIENT_ID'] ?? '') }}" placeholder="{{ __('messages.enter_google_client_id') ?? 'Enter Google Client ID' }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.google_client_secret') ?? 'Google Client Secret' }}</label>
                        <input type="password" name="GOOGLE_CLIENT_SECRET" class="form-control" value="{{ old('GOOGLE_CLIENT_SECRET', $systemSettings['GOOGLE_CLIENT_SECRET'] ?? '') }}" placeholder="{{ __('messages.enter_google_client_secret') ?? 'Enter Google Client Secret' }}">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 px-3 mb-0 fs-12">
                            <strong>{{ __('messages.redirect_uri') }}:</strong> <code>{{ route('social.callback', 'google') }}</code>
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <!-- ADStn Login -->
                    <div class="col-12"><h4 class="h6 mb-0 text-primary"><i class="fa-brands fa-buysellads me-2"></i>{{ __('messages.adstn_login') ?? 'ADStn Login' }}</h4></div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.adstn_client_id') ?? 'ADStn Client ID' }}</label>
                        <input type="text" name="ADSTN_CLIENT_ID" class="form-control" value="{{ old('ADSTN_CLIENT_ID', $systemSettings['ADSTN_CLIENT_ID'] ?? '') }}" placeholder="{{ __('messages.enter_adstn_client_id') ?? 'Enter ADStn Client ID' }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('messages.adstn_client_secret') ?? 'ADStn Client Secret' }}</label>
                        <input type="password" name="ADSTN_CLIENT_SECRET" class="form-control" value="{{ old('ADSTN_CLIENT_SECRET', $systemSettings['ADSTN_CLIENT_SECRET'] ?? '') }}" placeholder="{{ __('messages.enter_adstn_client_secret') ?? 'Enter ADStn Client Secret' }}">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 px-3 mb-0 fs-12">
                            <strong>{{ __('messages.redirect_uri') }}:</strong> <code>{{ route('social.callback', 'adstn') }}</code>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-panel__footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-md">
                    <i class="feather-save me-1"></i>{{ __('messages.save') }}
                </button>
            </div>
        </section>
    </form>

    <!-- System Administration Hubs -->
    <section class="admin-panel">
        <div class="admin-panel__header mb-3">
            <h3 class="admin-panel__title">
                <i class="feather-grid text-primary me-2"></i>{{ __('messages.system_hubs_nav') }}
            </h3>
            <p class="text-muted fs-12 mb-0">{{ __('messages.system_hubs_nav_desc') }}</p>
        </div>
        <div class="admin-panel__body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-primary text-primary rounded-3 flex-shrink-0">
                                <i class="feather-mail fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.mail_settings_title') ?? 'Mail Configuration' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.mail_settings_desc') ?? 'Configure SMTP, credentials, and sender info.' }}</p>
                            </div>
                            <a href="{{ route('admin.settings.mail') }}" class="btn btn-sm btn-outline-primary rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-danger text-danger rounded-3 flex-shrink-0">
                                <i class="feather-shield fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.security_settings') ?? 'Security & IP Bans' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.security_settings_desc') ?? 'Manage IP bans, CAPTCHA, and HTTPS controls.' }}</p>
                            </div>
                            <a href="{{ route('admin.security.index') }}" class="btn btn-sm btn-outline-danger rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-success text-success rounded-3 flex-shrink-0">
                                <i class="feather-smartphone fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.mobile_settings_title') ?? 'Mobile App Settings' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.mobile_settings_hint') ?? 'Manage API key and mobile maintenance mode.' }}</p>
                            </div>
                            <a href="{{ route('admin.settings.mobile') }}" class="btn btn-sm btn-outline-success rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-warning text-warning rounded-3 flex-shrink-0">
                                <i class="feather-zap fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.performance_settings') ?? 'Performance Settings' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.performance_settings_desc') ?? 'Optimize server load and feed caching.' }}</p>
                            </div>
                            <a href="{{ route('admin.settings.performance') }}" class="btn btn-sm btn-outline-warning rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-info text-info rounded-3 flex-shrink-0">
                                <i class="feather-tool fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.maintenance_mode') ?? 'Maintenance Mode' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.maintenance_desc') ?? 'Toggle site maintenance and view status.' }}</p>
                            </div>
                            <a href="{{ route('admin.maintenance') }}" class="btn btn-sm btn-outline-info rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border rounded-3 hover-shadow-sm transition-all">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="avatar-text bg-soft-secondary text-secondary rounded-3 flex-shrink-0">
                                <i class="feather-activity fs-4"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.system_monitor') ?? 'System Monitor' }}</h5>
                                <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.system_monitor_desc') ?? 'Real-time resource usage & cache control.' }}</p>
                            </div>
                            <a href="{{ route('admin.system_monitor') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 flex-shrink-0" title="Manage">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
