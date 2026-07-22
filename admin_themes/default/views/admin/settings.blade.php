@extends('admin::layouts.admin')

@section('title', __('messages.unified_settings_title') ?? __('messages.settings_site'))

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <ul class="admin-breadcrumb">
                    <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                    <li>{{ __('messages.unified_settings_title') ?? __('messages.settings_site') }}</li>
                </ul>
                <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
                <h1 class="admin-hero__title d-flex align-items-center gap-2">
                    <i class="feather-sliders text-primary"></i>
                    {{ __('messages.unified_settings_title') ?? 'Unified Site & System Settings' }}
                </h1>
                <p class="admin-hero__copy">{{ __('messages.unified_settings_desc') ?? 'Comprehensive control panel to configure site identity, environment mode, sessions, registration rewards, and social OAuth login.' }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-primary text-primary px-3 py-2 fs-12 fw-semibold border border-primary-subtle rounded-pill">
                    <i class="feather-box me-1"></i>v4.4.6
                </span>
                <span class="badge bg-soft-{{ ($systemSettings['APP_ENV'] ?? 'production') === 'production' ? 'success' : 'warning' }} text-{{ ($systemSettings['APP_ENV'] ?? 'production') === 'production' ? 'success' : 'warning' }} px-3 py-2 fs-12 fw-semibold rounded-pill">
                    <i class="feather-server me-1"></i>{{ strtoupper($systemSettings['APP_ENV'] ?? 'production') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Session Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-text bg-success text-white rounded-circle fs-6" style="width: 28px; height: 28px;">
                    <i class="feather-check"></i>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
            <div class="d-flex align-items-start gap-2">
                <div class="avatar-text bg-danger text-white rounded-circle fs-6 flex-shrink-0 mt-1" style="width: 28px; height: 28px;">
                    <i class="feather-alert-octagon"></i>
                </div>
                <div>
                    <strong class="d-block mb-1">{{ __('messages.warning') ?? 'Please check the input errors below:' }}</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <!-- Superdesign Custom Pill Navigation -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
            <div class="card-header bg-white p-2 border-bottom">
                <ul class="nav nav-pills nav-justified gap-2" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link active fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-identity-btn" data-bs-toggle="pill" data-bs-target="#tab-identity" type="button" role="tab" aria-controls="tab-identity" aria-selected="true">
                            <i class="feather-globe text-primary fs-5"></i>
                            <span>{{ __('messages.tab_identity') ?? 'Site Identity' }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-environment-btn" data-bs-toggle="pill" data-bs-target="#tab-environment" type="button" role="tab" aria-controls="tab-environment" aria-selected="false">
                            <i class="feather-cpu text-info fs-5"></i>
                            <span>{{ __('messages.tab_environment') ?? 'Environment & App' }}</span>
                            @if(($systemSettings['APP_DEBUG'] ?? '') === 'true')
                                <span class="badge bg-warning text-dark rounded-circle p-1" title="Debug Enabled"><i class="feather-alert-triangle fs-11"></i></span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-gamification-btn" data-bs-toggle="pill" data-bs-target="#tab-gamification" type="button" role="tab" aria-controls="tab-gamification" aria-selected="false">
                            <i class="feather-gift text-warning fs-5"></i>
                            <span>{{ __('messages.tab_gamification') ?? 'Registration Rewards' }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-sessions-btn" data-bs-toggle="pill" data-bs-target="#tab-sessions" type="button" role="tab" aria-controls="tab-sessions" aria-selected="false">
                            <i class="feather-lock text-success fs-5"></i>
                            <span>{{ __('messages.tab_sessions') ?? 'Sessions & Security' }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-social-btn" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab" aria-controls="tab-social" aria-selected="false">
                            <i class="feather-share-2 text-danger fs-5"></i>
                            <span>{{ __('messages.tab_social') ?? 'Social OAuth Login' }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-item-btn nav-link fw-semibold d-flex align-items-center justify-content-center gap-2 py-2" id="tab-hubs-btn" data-bs-toggle="pill" data-bs-target="#tab-hubs" type="button" role="tab" aria-controls="tab-hubs" aria-selected="false">
                            <i class="feather-grid text-secondary fs-5"></i>
                            <span>{{ __('messages.tab_hubs') ?? 'Admin Hubs' }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="tab-content mb-4" id="settingsTabContent">
            
            <!-- Tab 1: Site Identity & General Info -->
            <div class="tab-pane fade show active" id="tab-identity" role="tabpanel" aria-labelledby="tab-identity-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <h3 class="admin-panel__title">
                            <i class="feather-globe text-primary me-2"></i>{{ __('messages.tab_identity') ?? 'Site Identity & General Information' }}
                        </h3>
                        <p class="text-muted fs-12 mb-0">{{ __('messages.site_name') }} / {{ __('messages.url_link') }} / {{ __('messages.desc') }}</p>
                    </div>
                    <div class="admin-panel__body">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.site_name') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="feather-type"></i></span>
                                    <input type="text" name="titer" class="form-control" value="{{ old('titer', $settings->titer) }}" required placeholder="{{ __('messages.site_name') }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.url_link') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="feather-link"></i></span>
                                    <input type="url" name="url" class="form-control" value="{{ old('url', $settings->url ?? $systemSettings['APP_URL']) }}" required placeholder="https://example.com">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.site_slogan') ?? 'Site Slogan' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="feather-message-square"></i></span>
                                    <input type="text" name="slog" class="form-control" value="{{ old('slog', $settings->slog) }}" placeholder="{{ __('messages.site_slogan') ?? 'Site Slogan' }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.admin_email') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="feather-mail"></i></span>
                                    <input type="email" name="a_mail" class="form-control" value="{{ old('a_mail', $settings->a_mail) }}" placeholder="{{ __('messages.admin_email') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('messages.desc') }} (Meta Description)</label>
                                <textarea rows="3" name="description" class="form-control" placeholder="{{ __('messages.desc') }}">{{ old('description', $settings->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('messages.meta_keywords') ?? 'Meta Keywords' }}</label>
                                <input type="text" name="keyw" class="form-control" value="{{ old('keyw', $settings->keyw) }}" placeholder="myads, ad exchange, social network, surf to earn">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('messages.admin_theme_title') ?? 'Admin Panel Theme' }}</label>
                                <input type="text" name="admin_theme" class="form-control" value="{{ old('admin_theme', $adminTheme ?? 'default') }}" placeholder="default">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 2: Environment & App -->
            <div class="tab-pane fade" id="tab-environment" role="tabpanel" aria-labelledby="tab-environment-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <h3 class="admin-panel__title">
                            <i class="feather-cpu text-info me-2"></i>{{ __('messages.general_environment_settings') }}
                        </h3>
                        <p class="text-muted fs-12 mb-0">{{ __('messages.general_environment_desc') }}</p>
                    </div>
                    <div class="admin-panel__body">
                        <div class="row g-4">
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
                                    $tzCurrent = $systemSettings['APP_TIMEZONE'] ?? $settings->timezone ?? 'UTC';
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
            </div>

            <!-- Tab 3: Registration Rewards -->
            <div class="tab-pane fade" id="tab-gamification" role="tabpanel" aria-labelledby="tab-gamification-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <h3 class="admin-panel__title">
                            <i class="feather-gift text-warning me-2"></i>{{ __('messages.registration_rewards') ?? 'Registration Rewards' }}
                        </h3>
                        <p class="text-muted fs-12 mb-0">{{ __('messages.registration_rewards_desc') ?? 'Configure initial PTS points and ad credits granted to new members upon signup.' }}</p>
                    </div>
                    <div class="admin-panel__body">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.r_pts') ?? 'Registration Points (PTS)' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-warning fw-bold">PTS</span>
                                    <input type="number" step="0.01" min="0" name="r_pts" class="form-control" value="{{ old('r_pts', $settings->r_pts ?? 0) }}" placeholder="100">
                                </div>
                                <div class="form-text text-muted fs-12 mt-1">{{ __('messages.r_pts_help') }}</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.r_vu') ?? 'Banner Ad Credits' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary"><i class="feather-image"></i></span>
                                    <input type="number" min="0" name="r_vu" class="form-control" value="{{ old('r_vu', $settings->r_vu ?? 0) }}" placeholder="50">
                                </div>
                                <div class="form-text text-muted fs-12 mt-1">{{ __('messages.r_vu_help') }}</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.r_nvu') ?? 'Link/Text Ad Credits' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-info"><i class="feather-file-text"></i></span>
                                    <input type="number" min="0" name="r_nvu" class="form-control" value="{{ old('r_nvu', $settings->r_nvu ?? 0) }}" placeholder="50">
                                </div>
                                <div class="form-text text-muted fs-12 mt-1">{{ __('messages.r_nvu_help') }}</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">{{ __('messages.r_nlink') ?? 'Smart Ads Credits' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success"><i class="feather-target"></i></span>
                                    <input type="number" min="0" name="r_nlink" class="form-control" value="{{ old('r_nlink', $settings->r_nlink ?? 0) }}" placeholder="50">
                                </div>
                                <div class="form-text text-muted fs-12 mt-1">{{ __('messages.r_nlink_help') }}</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 4: Sessions & Security -->
            <div class="tab-pane fade" id="tab-sessions" role="tabpanel" aria-labelledby="tab-sessions-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <h3 class="admin-panel__title">
                            <i class="feather-lock text-success me-2"></i>{{ __('messages.session_settings') }}
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
            </div>

            <!-- Tab 5: Social Login -->
            <div class="tab-pane fade" id="tab-social" role="tabpanel" aria-labelledby="tab-social-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <h3 class="admin-panel__title">
                            <i class="feather-share-2 text-danger me-2"></i>{{ __('messages.social_login_settings') }}
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
                </section>
            </div>

            <!-- Tab 6: System Hubs Navigation -->
            <div class="tab-pane fade" id="tab-hubs" role="tabpanel" aria-labelledby="tab-hubs-btn">
                <section class="admin-panel shadow-sm">
                    <div class="admin-panel__header mb-3">
                        <h3 class="admin-panel__title">
                            <i class="feather-grid text-secondary me-2"></i>{{ __('messages.system_hubs_nav') }}
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
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.mail_settings_desc') ?? 'SMTP servers and mail parameters.' }}</p>
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
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.security_settings_desc') ?? 'Manage IP bans & security policies.' }}</p>
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
                                            <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.mobile_settings_title') ?? 'Mobile App API' }}</h5>
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.mobile_settings_hint') ?? 'API keys & mobile app maintenance.' }}</p>
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
                                            <h5 class="fs-14 fw-bold mb-1 text-truncate">{{ __('messages.performance_settings') ?? 'Performance Tuning' }}</h5>
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.performance_settings_desc') ?? 'Feed algorithms and CPU optimization.' }}</p>
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
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.maintenance_desc') ?? 'Toggle emergency site maintenance.' }}</p>
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
                                            <p class="text-muted fs-12 mb-0 text-truncate">{{ __('messages.system_monitor_desc') ?? 'System resource usage & cache.' }}</p>
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
        </div>

        <!-- Sticky Footer Save Action Bar -->
        <div class="card border-0 shadow-lg rounded-3 position-sticky bottom-0 z-3 bg-white mb-4">
            <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 text-muted fs-13">
                    <i class="feather-info text-primary"></i>
                    <span>{{ __('messages.save_changes_hint') ?? 'Click Save to apply changes to database and environment settings.' }}</span>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-4 d-flex align-items-center gap-2 shadow-sm">
                    <i class="feather-save fs-5"></i>
                    <span>{{ __('messages.save') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
