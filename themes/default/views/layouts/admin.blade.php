@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $adminUser = auth()->user();
    $canAdmin = fn (?string $module = null): bool => $module === null
        ? (bool) ($adminUser?->hasAdminAccess())
        : (bool) ($adminUser?->canAccessAdminModule($module));
    $canAnyAdminSection = function (array $modules) use ($canAdmin): bool {
        foreach ($modules as $module) {
            if ($canAdmin($module)) {
                return true;
            }
        }

        return false;
    };
    $accountSettingsUrl = $canAdmin('settings')
        ? route('admin.settings')
        : route('profile.edit');
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ trim($__env->yieldContent('title')) !== '' ? $__env->yieldContent('title') . ' - ' : '' }}{{ config('app.name', 'MyAds Admin') }} - {{ __('messages.admin_panel') ?? 'Admin Panel' }}</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="{{ theme_asset('admin-duralux/images/favicon.ico') }}">
    <script>
        (function() {
            function readCookie(name) {
                var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            }
            function safeGet(key) {
                try {
                    return localStorage.getItem(key);
                } catch (e) {
                    return null;
                }
            }
            var storedTheme = safeGet('themeMode');
            var storedSkin = safeGet('app-skin');
            var cookieMode = readCookie('modedark');
            var isDark = storedTheme === 'css_d' || storedSkin === 'app-skin-dark' || cookieMode === 'css_d';
            if (isDark) {
                document.documentElement.classList.add('app-skin-dark');
            } else {
                document.documentElement.classList.remove('app-skin-dark');
            }
            window.__adminThemeMode = isDark ? 'css_d' : 'css';
        })();
    </script>
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('admin-duralux/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('admin-duralux/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('admin-duralux/css/theme.min.css') }}">
    @if(is_locale_rtl())
        <link rel="stylesheet" type="text/css" href="{{ theme_asset('admin-duralux/css/rtl.css') }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('admin-duralux/css/admin-redesign.css') }}">
    
    <!-- FontAwesome for compatibility -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS for compatibility (Legacy Views) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            corePlugins: {
                preflight: false, // Disable preflight to avoid conflict with Bootstrap
            }
        }
    </script>
</head>
<body data-dir="{{ $pageDirection }}" class="{{ $pageDirection }} admin-premium-shell">
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('admin.index') }}" class="b-brand">
                    <img src="{{ theme_asset('admin-duralux/images/logo-full.png') }}" alt="" class="logo logo-lg">
                    <img src="{{ theme_asset('admin-duralux/images/logo-abbr.png') }}" alt="" class="logo logo-sm">
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>{{ __('messages.navigation') ?? 'Navigation' }}</label>
                    </li>

                    @if($canAdmin('dashboard'))
                        <li class="nxl-item">
                            <a href="{{ route('admin.index') }}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-airplay"></i></span>
                                <span class="nxl-mtext">{{ __('messages.board') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($canAdmin('pages'))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-file-text"></i></span>
                                <span class="nxl-mtext">{{ __('messages.pages') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.pages') }}">{{ __('messages.t_pages') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.pages.create') }}">{{ __('messages.add_page') }}</a></li>
                            </ul>
                        </li>
                    @endif

                    @if($canAdmin('users'))
                        <li class="nxl-item">
                            <a href="{{ route('admin.users') }}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">{{ __('messages.users') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($canAdmin('ads'))
                        <li class="nxl-item nxl-hasmenu">
                             <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-monitor"></i></span>
                                <span class="nxl-mtext">{{ __('messages.ads') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.ads.posts.index') }}">{{ __('messages.status_promotions_title') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.ads.posts.settings') }}">{{ __('messages.status_promotion_settings_title') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.banners') }}">{{ __('messages.bannads') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.links') }}">{{ __('messages.textads') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.smart_ads') }}">{{ __('messages.smart_ads') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.visits') }}">{{ __('messages.exvisit') }}</a></li>
                            </ul>
                        </li>
                    @endif

                    @if($canAdmin('community'))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-settings"></i></span>
                                <span class="nxl-mtext">{{ __('messages.Comusetting') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.knowledgebase') }}">{{ __('messages.knowledgebase') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.forum_categories') }}">{{ __('messages.forum_cats') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.forum.settings') }}">{{ __('messages.forum_settings') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.forum.moderators') }}">{{ __('messages.forum_moderators') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.directory_categories') }}">{{ __('messages.dir_cats') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.emojis') }}">{{ __('messages.emojis') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.news') }}">{{ __('messages.news_site') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.reports') }}">{{ __('messages.reports') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.products') }}">{{ __('messages.products') ?? 'Products' }}</a></li>
                            </ul>
                        </li>
                    @endif

                    @if($canAnyAdminSection(['design', 'plugins', 'themes']))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-layout"></i></span>
                                <span class="nxl-mtext">{{ __('messages.style') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                             <ul class="nxl-submenu">
                                @if($canAdmin('design'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.widgets') }}">{{ __('messages.widgets') }}</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.menus') }}">{{ __('messages.menu') }}</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.site_ads') }}">{{ __('messages.e_ads') }}</a></li>
                                @endif
                                @if($canAdmin('plugins'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.plugins') }}">{{ __('messages.plugins') }}</a></li>
                                @endif
                                @if($canAdmin('themes'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.themes') }}">{{ __('messages.themes') }}</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if($canAdmin('seo'))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-search"></i></span>
                                <span class="nxl-mtext">{{ __('messages.seo') ?? 'SEO' }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.seo.index') }}">{{ __('messages.seo_dashboard') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.seo.settings') }}">{{ __('messages.seo_settings') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.seo.head') }}">{{ __('messages.seo_head_meta') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.seo.rules') }}">{{ __('messages.seo_rules') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.seo.indexing') }}">{{ __('messages.seo_indexing') }}</a></li>
                            </ul>
                        </li>
                    @endif

                    @if($canAdmin('security'))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-shield"></i></span>
                                <span class="nxl-mtext">{{ __('messages.security_title') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.security.index') }}">{{ __('messages.security_settings_title') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.security.ip-bans') }}">{{ __('messages.security_ip_bans_title') }}</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.security.sessions') }}">{{ __('messages.security_member_sessions_title') }}</a></li>
                            </ul>
                        </li>
                    @endif

                    @if($canAnyAdminSection(['settings', 'languages', 'updates', 'maintenance', 'administrators']))
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-sliders"></i></span>
                                <span class="nxl-mtext">{{ __('messages.options') }}</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                @if($canAdmin('settings'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.settings') }}">{{ __('messages.settings') }}</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.settings.system') }}">{{ __('messages.system_settings') }}</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.cookie_notice') }}">{{ __('messages.cookie_notice_settings') }}</a></li>
                                @endif
                                @if($canAdmin('languages'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.languages') }}">{{ __('messages.languages') }}</a></li>
                                @endif
                                @if($canAdmin('updates'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.updates') }}">{{ __('messages.updates') }}</a></li>
                                @endif
                                @if($canAdmin('maintenance'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.maintenance') }}">{{ __('messages.maintenance') }}</a></li>
                                @endif
                                @if($canAdmin('administrators'))
                                    <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.admins') }}">{{ __('messages.site_admins') }}</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- Hook for Plugins -->
                    {!! \App\Helpers\Hooks::do_action('admin_sidebar_menu') !!}
                </ul>
            </div>
        </div>
    </nav>

    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    {{-- Back to Site --}}
                    <div class="nxl-h-item me-3">
                        <a href="{{ url('/') }}" class="nxl-head-link me-0" title="{{ __('messages.back_to_site') ?? 'Back to Site' }}">
                            <i class="feather-external-link"></i>
                            <span class="d-none d-md-inline-block ms-1 fs-12 fw-medium">{{ __('messages.back_to_site') ?? 'Back to Site' }}</span>
                        </a>
                    </div>

                    {{-- Language Switcher --}}
                    <div class="dropdown nxl-h-item me-3">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" role="button" data-bs-auto-close="true">
                            <i class="feather-globe"></i>
                            <span class="ms-1 fs-12 fw-medium">{{ strtoupper(app()->getLocale()) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown" style="min-width: 160px;">
                            <div class="dropdown-header">
                                <h6 class="text-dark mb-0">{{ __('messages.languages') }}</h6>
                            </div>
                            @foreach($available_languages as $lang)
                                <a class="dropdown-item {{ app()->getLocale() === $lang->code ? 'active' : '' }}" href="?lang={{ $lang->code }}">
                                    <span>{{ $lang->name }}</span>
                                    @if(app()->getLocale() === $lang->code)
                                        <i class="feather-check ms-auto"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Dark/Light Toggle --}}
                    <div class="nxl-h-item dark-light-theme me-3">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>

                    {{-- User Dropdown --}}
                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <img src="{{ auth()->user()->img ? asset(auth()->user()->img) : theme_asset('admin-duralux/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar me-0">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <img src="{{ auth()->user()->img ? asset(auth()->user()->img) : theme_asset('admin-duralux/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar">
                                    <div>
                                        <h6 class="text-dark mb-0">{{ auth()->user()->username }} <span class="badge bg-soft-primary text-primary ms-1">PRO</span></h6>
                                        <span class="fs-12 fw-medium text-muted">{{ __('messages.administrator') ?? 'Administrator' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('profile.show', auth()->user()->username) }}" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>{{ __('messages.profile_details') ?? 'Profile Details' }}</span>
                            </a>
                            <a href="{{ $accountSettingsUrl }}" class="dropdown-item">
                                <i class="feather-settings"></i>
                                <span>{{ __('messages.account_settings') ?? 'Account Settings' }}</span>
                            </a>
                            @if($canAdmin('settings'))
                                <a href="{{ route('admin.cookie_notice') }}" class="dropdown-item">
                                    <i class="feather-shield"></i>
                                    <span>{{ __('messages.cookie_notice_settings') ?? 'Cookie Notice' }}</span>
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a href="{{ url('/') }}" class="dropdown-item">
                                <i class="feather-external-link"></i>
                                <span>{{ __('messages.back_to_site') ?? 'Back to Site' }}</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="feather-log-out"></i>
                                    <span>{{ __('messages.logout') ?? 'Logout' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.home') ?? 'Home' }}</a></li>
                        <li class="breadcrumb-item">{{ __('messages.dashboard') ?? 'Dashboard' }}</li>
                    </ul>
                </div>
            </div>
            
            <div class="main-content">
                @yield('content')
            </div>
        </div>

        <footer class="footer d-flex flex-wrap justify-content-between align-items-center gap-2">
             <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright © {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}</span>
            </p>
            <p class="fs-11 text-muted fw-medium mb-0">
                <span class="badge bg-light text-dark border">{{ \App\Support\SystemVersion::tag() }}</span>
            </p>
        </footer>
    </main>

    @yield('modals')

    <script src="{{ theme_asset('admin-duralux/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ theme_asset('admin-duralux/js/common-init.min.js') }}"></script>
    <script src="{{ theme_asset('admin-duralux/js/theme-customizer-init.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @stack('scripts')
    <script>
        (function() {
            function safeGet(key) {
                try {
                    return localStorage.getItem(key);
                } catch (e) {
                    return null;
                }
            }
            function safeSet(key, value) {
                try {
                    localStorage.setItem(key, value);
                } catch (e) {
                }
            }
            function updateButtons(mode) {
                var darkBtn = document.querySelector('.dark-light-theme .dark-button');
                var lightBtn = document.querySelector('.dark-light-theme .light-button');
                if (!darkBtn || !lightBtn) {
                    return;
                }
                if (mode === 'css_d') {
                    darkBtn.style.display = 'none';
                    lightBtn.style.display = 'inline-flex';
                } else {
                    lightBtn.style.display = 'none';
                    darkBtn.style.display = 'inline-flex';
                }
            }
            function applyMode(mode, persist) {
                if (mode === 'css_d') {
                    document.documentElement.classList.add('app-skin-dark');
                    document.documentElement.classList.remove('app-skin-light');
                } else {
                    document.documentElement.classList.remove('app-skin-dark');
                    document.documentElement.classList.add('app-skin-light');
                }
                updateButtons(mode);
                if (persist) {
                    safeSet('themeMode', mode);
                    safeSet('app-skin', mode === 'css_d' ? 'app-skin-dark' : 'app-skin-light');
                    document.cookie = 'modedark=' + mode + ';path=/;max-age=31536000';
                }
                window.__adminThemeMode = mode;
            }
            document.addEventListener('DOMContentLoaded', function() {
                var storedTheme = safeGet('themeMode');
                var storedSkin = safeGet('app-skin');
                var mode = storedTheme === 'css_d' || storedSkin === 'app-skin-dark' ? 'css_d' : 'css';
                applyMode(mode, true);
                var darkBtn = document.querySelector('.dark-light-theme .dark-button');
                var lightBtn = document.querySelector('.dark-light-theme .light-button');
                if (darkBtn) {
                    darkBtn.addEventListener('click', function(event) {
                        event.preventDefault();
                        applyMode('css_d', true);
                    });
                }
                if (lightBtn) {
                    lightBtn.addEventListener('click', function(event) {
                        event.preventDefault();
                        applyMode('css', true);
                    });
                }
            });
            window.addEventListener('storage', function(event) {
                if (event.key === 'themeMode' && (event.newValue === 'css' || event.newValue === 'css_d')) {
                    applyMode(event.newValue, false);
                }
                if (event.key === 'app-skin' && (event.newValue === 'app-skin-light' || event.newValue === 'app-skin-dark')) {
                    applyMode(event.newValue === 'app-skin-dark' ? 'css_d' : 'css', false);
                }
            });
        })();
    </script>
</body>
</html>
