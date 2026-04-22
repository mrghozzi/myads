@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $yieldedTitle = trim($__env->yieldContent('title'));
    $resolvedTitle = $yieldedTitle !== '' ? $yieldedTitle : trim((string) data_get($seo ?? null, 'title', ''));
    $resolvedTitle = $resolvedTitle !== '' ? $resolvedTitle : ($site_settings->titer ?? config('app.name', 'MYADS'));
    $resolvedRobots = data_get($seo ?? null, 'robots', 'noindex,nofollow');
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="generator" content="Myads">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="{{ $resolvedRobots }}">
    <title>{{ $resolvedTitle }}</title>

    <link rel="apple-touch-icon" sizes="57x57" href="{{ theme_asset('img/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ theme_asset('img/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ theme_asset('img/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ theme_asset('img/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ theme_asset('img/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ theme_asset('img/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ theme_asset('img/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ theme_asset('img/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ theme_asset('img/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ theme_asset('img/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ theme_asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ theme_asset('img/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ theme_asset('img/favicon-16x16.png') }}">

    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $css_path = $mode === 'css_d' ? 'css_d' : 'css';
    @endphp
    <script>
        (function () {
            function readCookie(name) {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            }

            function readStoredMode() {
                try {
                    const value = localStorage.getItem('themeMode');
                    if (value === 'css' || value === 'css_d') {
                        return value;
                    }
                } catch (error) {}

                const cookieMode = readCookie('modedark');
                return cookieMode === 'css' || cookieMode === 'css_d' ? cookieMode : null;
            }

            const mode = readStoredMode() || '{{ $css_path }}';
            document.documentElement.dataset.theme = mode;
            window.__themeMode = mode;
        })();
    </script>

    <link id="theme-bootstrap" data-theme-link="true" href="{{ theme_asset($css_path . '/bootstrap.min.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    <link id="theme-styles" data-theme-link="true" href="{{ theme_asset($css_path . '/styles.min.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    <link id="theme-prestyle" data-theme-link="true" href="{{ theme_asset($css_path . '/prestyle.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    @if(is_locale_rtl())
        <link id="theme-rtl" data-theme-link="true" href="{{ theme_asset($css_path . '/rtl.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    @endif

    <link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet"></noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;700&family=Inter:wght@300;400;500;600;700;800;900&family=Muli:ital,wght@0,300;0,400;1,300;1,400&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Rajdhani:wght@400;500;600;700&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <style>
        body[data-theme="css"] {
            --oauth-shell-bg: radial-gradient(circle at top left, rgba(97, 93, 250, 0.12), transparent 36%), radial-gradient(circle at top right, rgba(35, 210, 226, 0.11), transparent 32%), linear-gradient(180deg, #f8faff 0%, #eef2ff 100%);
            --oauth-shell-surface: rgba(255, 255, 255, 0.82);
            --oauth-shell-surface-border: rgba(97, 93, 250, 0.12);
            --oauth-shell-text: #3e3f5e;
            --oauth-shell-muted: #7f85a3;
            --oauth-shell-shadow: 0 18px 44px rgba(94, 92, 154, 0.1);
            --oauth-shell-brand-bg: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
            --oauth-shell-footer: #7f85a3;
        }

        body[data-theme="css_d"] {
            --oauth-shell-bg: radial-gradient(circle at top left, rgba(97, 93, 250, 0.22), transparent 34%), radial-gradient(circle at top right, rgba(79, 244, 97, 0.14), transparent 28%), linear-gradient(180deg, #141926 0%, #1a2131 100%);
            --oauth-shell-surface: rgba(31, 38, 55, 0.84);
            --oauth-shell-surface-border: rgba(140, 138, 255, 0.18);
            --oauth-shell-text: #f5f7ff;
            --oauth-shell-muted: #9aa4bf;
            --oauth-shell-shadow: 0 22px 54px rgba(0, 0, 0, 0.24);
            --oauth-shell-brand-bg: linear-gradient(135deg, #615dfa 0%, #4ff461 100%);
            --oauth-shell-footer: #9aa4bf;
        }

        body.oauth-layout-body {
            min-height: 100vh;
            margin: 0;
            background: var(--oauth-shell-bg);
            color: var(--oauth-shell-text);
            font-family: "Inter", sans-serif;
        }

        .oauth-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .oauth-shell-topbar {
            padding: 18px 16px 0;
        }

        .oauth-shell-topbar-inner {
            width: min(100%, 1120px);
            margin: 0 auto;
            padding: 14px 18px;
            border: 1px solid var(--oauth-shell-surface-border);
            border-radius: 24px;
            background: var(--oauth-shell-surface);
            backdrop-filter: blur(18px);
            box-shadow: var(--oauth-shell-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .oauth-shell-brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            min-width: 0;
        }

        .oauth-shell-brand:hover {
            text-decoration: none;
        }

        .oauth-shell-brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: var(--oauth-shell-brand-bg);
            box-shadow: 0 12px 26px rgba(97, 93, 250, 0.24);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .oauth-shell-brand-copy {
            min-width: 0;
            display: grid;
            gap: 2px;
        }

        .oauth-shell-brand-copy strong {
            color: var(--oauth-shell-text);
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .oauth-shell-brand-copy span {
            color: var(--oauth-shell-muted);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .oauth-shell-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .oauth-shell-link {
            min-width: 0;
            text-decoration: none;
        }

        .oauth-shell-link:hover {
            text-decoration: none;
        }

        .oauth-shell-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px 16px 20px;
        }

        .oauth-shell-container {
            width: min(100%, 760px);
        }

        .oauth-shell-footer {
            width: min(100%, 1120px);
            margin: 0 auto;
            padding: 0 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            color: var(--oauth-shell-footer);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .oauth-shell-footer a {
            color: inherit;
            text-decoration: none;
        }

        .oauth-shell-footer a:hover {
            color: var(--oauth-shell-text);
            text-decoration: none;
        }

        .oauth-shell-footer-links {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .theme-toggle {
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }

        .theme-toggle .theme-toggle-track {
            position: relative;
            width: 54px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            padding: 2px;
            gap: 8px;
            transition: background 0.2s ease;
        }

        .theme-toggle .theme-toggle-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .theme-toggle .theme-toggle-icon {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            z-index: 1;
            width: 16px;
            text-align: center;
        }

        .theme-toggle.is-dark .theme-toggle-track {
            background: rgba(97, 93, 250, 0.35);
        }

        .theme-toggle.is-dark .theme-toggle-thumb {
            transform: translateX(26px);
            background: #0f1014;
        }

        @media screen and (max-width: 680px) {
            .oauth-shell-topbar {
                padding-top: 14px;
            }

            .oauth-shell-topbar-inner {
                padding: 14px;
                border-radius: 20px;
            }

            .oauth-shell-actions {
                width: 100%;
                justify-content: space-between;
            }

            .oauth-shell-link {
                order: 1;
            }

            .oauth-shell-content {
                align-items: flex-start;
                padding-top: 24px;
            }

            .oauth-shell-footer {
                justify-content: center;
                text-align: center;
                padding-bottom: 24px;
            }

            .oauth-shell-footer-links {
                justify-content: center;
            }
        }
    </style>

    @stack('head')
</head>
<body data-theme="{{ $css_path }}" class="oauth-layout-body {{ $pageDirection }}">
    <div class="oauth-shell">
        <header class="oauth-shell-topbar">
            <div class="oauth-shell-topbar-inner">
                <a href="{{ url('/') }}" class="oauth-shell-brand">
                    <span class="oauth-shell-brand-mark">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <span class="oauth-shell-brand-copy">
                        <strong>{{ $site_settings->titer ?? config('app.name', 'MYADS') }}</strong>
                        <span>{{ __('messages.authorize_app') }}</span>
                    </span>
                </a>

                <div class="oauth-shell-actions">
                    @auth
                        <a href="{{ route('profile.apps') }}" class="button white small oauth-shell-link">{{ __('messages.authorized_apps') }}</a>
                    @endauth

                    <button type="button" class="theme-toggle" title="Toggle Theme" aria-label="Toggle Theme" aria-pressed="false">
                        <span class="theme-toggle-track">
                            <span class="theme-toggle-thumb"></span>
                            <span class="theme-toggle-icon"><i class="fa-solid fa-sun"></i></span>
                            <span class="theme-toggle-icon"><i class="fa-solid fa-moon"></i></span>
                        </span>
                    </button>
                </div>
            </div>
        </header>

        <main class="oauth-shell-content">
            <div class="oauth-shell-container">
                @yield('content')
            </div>
        </main>

        <footer class="oauth-shell-footer">
            <span>&copy; {{ date('Y') }} {{ $site_settings->titer ?? config('app.name', 'MYADS') }}</span>
            <div class="oauth-shell-footer-links">
                <a href="{{ route('privacy') }}">{{ __('messages.privacy') }}</a>
                <a href="{{ route('terms') }}">{{ __('messages.terms') }}</a>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            function applyThemeLinks(mode) {
                document.body.dataset.theme = mode;
                document.documentElement.dataset.theme = mode;

                document.querySelectorAll('link[data-theme-link="true"]').forEach(function (link) {
                    const href = link.getAttribute('href');
                    if (!href) {
                        return;
                    }

                    const nextHref = href
                        .replace(/\/css_d\//, '/' + mode + '/')
                        .replace(/\/css\//, '/' + mode + '/');

                    if (nextHref !== href) {
                        link.setAttribute('href', nextHref);
                    }
                });

                const toggle = document.querySelector('.theme-toggle');
                if (toggle) {
                    const isDark = mode === 'css_d';
                    toggle.classList.toggle('is-dark', isDark);
                    toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                }
            }

            function setThemeMode(mode, persist) {
                applyThemeLinks(mode);
                if (persist !== false) {
                    try {
                        localStorage.setItem('themeMode', mode);
                    } catch (error) {}
                    document.cookie = 'modedark=' + mode + ';path=/;max-age=31536000';
                }
                window.__themeMode = mode;
            }

            document.addEventListener('DOMContentLoaded', function () {
                const toggle = document.querySelector('.theme-toggle');
                const initialMode = window.__themeMode === 'css_d' ? 'css_d' : 'css';
                applyThemeLinks(initialMode);

                if (toggle) {
                    toggle.addEventListener('click', function (event) {
                        event.preventDefault();
                        const nextMode = document.body.dataset.theme === 'css_d' ? 'css' : 'css_d';
                        setThemeMode(nextMode, true);
                    });
                }
            });

            window.addEventListener('storage', function (event) {
                if (event.key === 'themeMode' && (event.newValue === 'css' || event.newValue === 'css_d')) {
                    applyThemeLinks(event.newValue);
                }
            });
        })();
    </script>

    @include('theme::partials._cookie_consent')

    @stack('scripts')
</body>
</html>
