@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $skipFooterAd = trim($__env->yieldContent('skip_footer_ad')) === '1';
    $yieldedTitle = trim($__env->yieldContent('title'));
    $resolvedTitle = $yieldedTitle !== '' ? $yieldedTitle : trim((string) ($seo->title ?? ''));
    $resolvedTitle = $resolvedTitle !== '' ? $resolvedTitle : ($site_settings->titer ?? 'MyAds');
@endphp
<!DOCTYPE HTML>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <title>{{ $resolvedTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Myads" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="{{ $seo->robots ?? 'index,follow' }}">
    @if(!empty($seo->description))
        <meta name="description" content="{{ $seo->description }}">
    @endif
    @if(!empty($seo->keywords))
        <meta name="keywords" content="{{ $seo->keywords }}">
    @endif
    @if(!empty($seo->canonical_url))
        <link rel="canonical" href="{{ $seo->canonical_url }}">
    @endif

    @if(!empty($seo->og))
        @foreach($seo->og as $property => $content)
            <meta property="og:{{ $property }}" content="{{ $content }}">
        @endforeach
    @endif

    @if(!empty($seo->twitter))
        @foreach($seo->twitter as $name => $content)
            <meta name="twitter:{{ $name }}" content="{{ $content }}">
        @endforeach
    @endif

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ theme_asset('img/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ theme_asset('img/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ theme_asset('img/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ theme_asset('img/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ theme_asset('img/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ theme_asset('img/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ theme_asset('img/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ theme_asset('img/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ theme_asset('img/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ theme_asset('img/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ theme_asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ theme_asset('img/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ theme_asset('img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ theme_asset('img/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#615dfa">
    <meta name="msapplication-TileImage" content="{{ theme_asset('img/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#615dfa">

    <!-- CSS -->
    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $css_path = $mode == 'css_d' ? 'css_d' : 'css';
    @endphp
    <script>
        (function() {
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
                } catch (e) {
                }
                const cookieMode = readCookie('modedark');
                return cookieMode === 'css' || cookieMode === 'css_d' ? cookieMode : null;
            }
            const mode = readStoredMode() || '{{ $css_path }}';
            document.documentElement.dataset.theme = mode;
            window.__themeMode = mode;
        })();
    </script>
    <link id="theme-bootstrap" data-theme-link="true" href="{{ theme_asset($css_path . '/bootstrap.min.css') }}" rel='stylesheet' type='text/css' />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel='stylesheet' type='text/css' />
    <link id="theme-styles" data-theme-link="true" href="{{ theme_asset($css_path . '/styles.min.css') }}" rel='stylesheet' type='text/css' />
    <link id="theme-prestyle" data-theme-link="true" href="{{ theme_asset($css_path . '/prestyle.css') }}" rel='stylesheet' type='text/css' />
    <link id="theme-simplebar" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/simplebar.css') }}">
    <link id="theme-tiny-slider" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/tiny-slider.css') }}">
    <link id="theme-dataTables" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/dataTables.css') }}">
    <link id="theme-forum-activity-super" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/forum-activity-superdesign.css') }}">
    @if(is_locale_rtl())
        <link id="theme-rtl" data-theme-link="true" href="{{ theme_asset($css_path . '/rtl.css') }}" rel="stylesheet" type="text/css" />
    @endif
    <link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Comfortaa:400,700,300' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Muli:400,300,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,500,600,700,800,900' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Sanchez:400,400italic' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>

    <!-- JS -->
    <script type="text/javascript" src="{{ theme_asset('js/jquery-3.6.0.min.js') }}"></script>

    <style>
        /* Fix for header dropdown interaction */
        .header .header-actions .header-dropdown,
        .header .header-actions .header-settings-dropdown {
            z-index: 999999 !important;
        }
        .header-dropdown-trigger,
        .header-settings-dropdown-trigger {
            cursor: pointer;
            position: relative;
            z-index: 10002;
            pointer-events: auto !important;
        }
        .header-dropdown-trigger.active + .header-dropdown,
        .header-settings-dropdown-trigger.active + .header-settings-dropdown {
            pointer-events: auto !important;
        }
        .header-dropdown-trigger.active + .header-dropdown *,
        .header-settings-dropdown-trigger.active + .header-settings-dropdown * {
            pointer-events: auto !important;
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
        body[data-theme="css"] {
            --notification-ui-badge-bg: #e94b5f;
            --notification-ui-badge-shadow: 0 10px 24px rgba(233, 75, 95, 0.28);
            --notification-ui-summary-icon-bg: linear-gradient(135deg, rgba(97, 93, 250, 0.12), rgba(35, 210, 226, 0.18));
            --notification-ui-summary-icon-fill: #615dfa;
            --notification-ui-summary-heading: #3e3f5e;
            --notification-ui-muted: #8f94b5;
            --notification-ui-summary-surface: linear-gradient(180deg, #f7f8fd 0%, #eef2ff 100%);
            --notification-ui-summary-label: #7b819d;
            --notification-ui-count-bg: rgba(233, 75, 95, 0.12);
            --notification-ui-count-fill: #d54358;
            --notification-ui-card-bg: #fff;
            --notification-ui-card-border: #edf0f7;
            --notification-ui-card-shadow: 0 18px 38px rgba(94, 92, 154, 0.08);
            --notification-ui-card-shadow-hover: 0 24px 44px rgba(94, 92, 154, 0.12);
            --notification-ui-card-unread-bg: linear-gradient(180deg, #fffafb 0%, #fff 100%);
            --notification-ui-card-unread-border: rgba(233, 75, 95, 0.22);
            --notification-ui-card-unread-dot: #e94b5f;
            --notification-ui-card-unread-glow: 0 0 0 6px rgba(233, 75, 95, 0.12);
            --notification-ui-card-badge-bg: linear-gradient(180deg, #f4f7ff 0%, #eef3ff 100%);
            --notification-ui-card-badge-fill: #615dfa;
            --notification-ui-card-badge-bg-unread: linear-gradient(180deg, #fff1f3 0%, #ffe5ea 100%);
            --notification-ui-card-badge-fill-unread: #e94b5f;
            --notification-ui-card-time: #7f85a3;
            --notification-ui-card-icon: #adb2cb;
        }
        body[data-theme="css_d"] {
            --notification-ui-badge-bg: #ff5b73;
            --notification-ui-badge-shadow: 0 10px 24px rgba(255, 91, 115, 0.22);
            --notification-ui-summary-icon-bg: linear-gradient(135deg, rgba(119, 80, 248, 0.2), rgba(79, 244, 97, 0.12));
            --notification-ui-summary-icon-fill: #4ff461;
            --notification-ui-summary-heading: #fff;
            --notification-ui-muted: #9aa4bf;
            --notification-ui-summary-surface: linear-gradient(180deg, #242d40 0%, #20283a 100%);
            --notification-ui-summary-label: #9aa4bf;
            --notification-ui-count-bg: rgba(255, 91, 115, 0.18);
            --notification-ui-count-fill: #ff92a1;
            --notification-ui-card-bg: #1f2637;
            --notification-ui-card-border: #2c3547;
            --notification-ui-card-shadow: 0 18px 38px rgba(0, 0, 0, 0.18);
            --notification-ui-card-shadow-hover: 0 26px 46px rgba(0, 0, 0, 0.24);
            --notification-ui-card-unread-bg: linear-gradient(180deg, rgba(255, 91, 115, 0.06) 0%, #1f2637 100%);
            --notification-ui-card-unread-border: rgba(255, 91, 115, 0.34);
            --notification-ui-card-unread-dot: #ff5b73;
            --notification-ui-card-unread-glow: 0 0 0 6px rgba(255, 91, 115, 0.14);
            --notification-ui-card-badge-bg: linear-gradient(180deg, #293249 0%, #242c3d 100%);
            --notification-ui-card-badge-fill: #4ff461;
            --notification-ui-card-badge-bg-unread: linear-gradient(180deg, rgba(255, 91, 115, 0.16) 0%, rgba(255, 91, 115, 0.1) 100%);
            --notification-ui-card-badge-fill-unread: #ff92a1;
            --notification-ui-card-time: #9aa4bf;
            --notification-ui-card-icon: #7f879f;
        }
        .action-list .action-list-item.notification-trigger,
        .floaty-bar .action-list .action-list-item.notification-trigger {
            position: relative;
        }
        .action-list .action-list-item.notification-trigger.unread::after,
        .floaty-bar .action-list .action-list-item.notification-trigger.unread::after {
            display: none;
        }
        .header .header-actions .header-action-count,
        .floaty-bar .notification-action-count {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background-color: var(--notification-ui-badge-bg);
            color: #fff;
            font-size: 0.625rem;
            font-weight: 700;
            line-height: 20px;
            text-align: center;
            box-shadow: var(--notification-ui-badge-shadow);
        }
        .header .header-actions .header-action-count {
            top: 12px;
            right: 0;
        }
        .floaty-bar .notification-action-count {
            position: absolute;
            top: 7px;
            right: 8px;
            z-index: 2;
        }
        .header .header-actions .header-action-count[hidden],
        .floaty-bar .notification-action-count[hidden],
        .notification-feed-count[hidden],
        .notification-summary-button[hidden] {
            display: none !important;
        }
        .notification-center-banner .section-banner-text {
            margin-top: 12px;
            max-width: 420px;
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.875rem;
            font-weight: 700;
            line-height: 1.57;
        }
        .notification-center-grid {
            align-items: start;
        }
        .notification-summary-card,
        .notification-feed-card {
            overflow: hidden;
        }
        .notification-summary-card .widget-box-content,
        .notification-feed-card .widget-box-content {
            padding-top: 0;
        }
        .notification-summary-head {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .notification-summary-icon,
        .notification-empty-state-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: var(--notification-ui-summary-icon-bg);
            flex-shrink: 0;
        }
        .notification-summary-icon svg,
        .notification-empty-state-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--notification-ui-summary-icon-fill);
        }
        .notification-summary-copy {
            min-width: 0;
        }
        .notification-summary-heading,
        .notification-summary-stat-value,
        .notification-card-title,
        .notification-empty-state-title {
            color: var(--notification-ui-summary-heading);
        }
        .notification-summary-copy-text,
        .notification-summary-note,
        .notification-feed-subtitle,
        .notification-empty-state-text {
            color: var(--notification-ui-muted);
        }
        .notification-summary-copy-text,
        .notification-feed-subtitle {
            margin-top: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.6;
        }
        .notification-summary-stat {
            margin-top: 24px;
            padding: 20px 22px;
            border-radius: 18px;
            background: var(--notification-ui-summary-surface);
        }
        .notification-summary-stat-value {
            display: block;
            font-size: 2rem;
            font-weight: 900;
            line-height: 1;
        }
        .notification-summary-stat-label {
            display: inline-flex;
            margin-top: 8px;
            color: var(--notification-ui-summary-label);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .notification-summary-button {
            width: 100%;
            margin-top: 20px;
            justify-content: center;
        }
        .notification-summary-note {
            margin-top: 14px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.6;
        }
        .notification-feed-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }
        .notification-feed-count {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 999px;
            background: var(--notification-ui-count-bg);
            color: var(--notification-ui-count-fill);
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }
        .notification-center-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .notification-card {
            position: relative;
            border: 1px solid var(--notification-ui-card-border);
            border-radius: 18px;
            background-color: var(--notification-ui-card-bg);
            box-shadow: var(--notification-ui-card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
            overflow: hidden;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--notification-ui-card-shadow-hover);
        }
        .notification-card.unread {
            border-color: var(--notification-ui-card-unread-border);
            background: var(--notification-ui-card-unread-bg);
        }
        .notification-card.unread::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--notification-ui-card-unread-dot);
            box-shadow: var(--notification-ui-card-unread-glow);
            position: absolute;
            top: 18px;
            right: 20px;
        }
        .notification-card-link {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 22px 24px;
            color: inherit;
            text-decoration: none !important;
        }
        .notification-card-link:hover {
            text-decoration: none !important;
        }
        .notification-card-badge,
        .notification-card-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }
        .notification-card-badge {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: var(--notification-ui-card-badge-bg);
            color: var(--notification-ui-card-badge-fill);
        }
        .notification-card.unread .notification-card-badge {
            background: var(--notification-ui-card-badge-bg-unread);
            color: var(--notification-ui-card-badge-fill-unread);
        }
        .notification-card-badge svg,
        .notification-card-icon svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }
        .notification-card-body {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }
        .notification-card-title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.45;
        }
        .notification-card-time {
            margin-top: 6px;
            color: var(--notification-ui-card-time);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .notification-card-icon {
            color: var(--notification-ui-card-icon);
        }
        .notification-empty-state {
            padding: 48px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .notification-empty-state-title {
            margin-top: 18px;
            font-size: 1.125rem;
            font-weight: 700;
        }
        .notification-empty-state-text {
            margin-top: 10px;
            max-width: 360px;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.7;
        }
        .notification-pagination-fallback {
            margin-top: 24px;
        }
        [dir="rtl"] .notification-card.unread::after {
            right: auto;
            left: 20px;
        }
        @media screen and (max-width: 768px) {
            .notification-feed-header {
                flex-direction: column;
            }
            .notification-card-link {
                padding: 18px;
                gap: 14px;
            }
            .notification-card-badge {
                width: 46px;
                height: 46px;
                border-radius: 16px;
            }
            .notification-card-title {
                font-size: 0.9375rem;
            }
            .notification-summary-stat {
                padding: 18px;
            }
        }
    </style>

    @if(!empty($seo->head_snippets))
        @foreach($seo->head_snippets as $snippet)
            {!! $snippet !!}
        @endforeach
    @endif

    @if(!empty($seo->schema_blocks))
        @foreach($seo->schema_blocks as $schemaBlock)
            <script type="application/ld+json">{!! json_encode($schemaBlock, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach
    @endif

    @stack('head')
</head>
<body data-theme="{{ $css_path }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">

    @include('theme::partials.header.nav')
    @include('theme::partials.header.sidemenu')
    @include('theme::partials.header.desktop_sidebar')
    @include('theme::partials.header.mobile_sidebar')
    @include('theme::partials.header.floaty_bar')

    <div class="content-grid">
        @yield('content')
    </div>

    @unless($skipFooterAd)
        @include('theme::partials.ads', ['id' => 6])
    @endunless

    <!-- Footer -->
    <footer>
        <!-- Place footer content here if any -->
    </footer>

    <!-- Scripts -->
    <script src="{{ theme_asset('js/app.js') }}"></script>
    <script src="{{ theme_asset('js/simplebar.min.js') }}"></script>
    <script src="{{ theme_asset('js/tiny-slider.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_accordion.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_dropdown.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_hexagon.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_popup.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_progressBar.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_tab.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_tooltip.min.js') }}"></script>
    <script src="{{ theme_asset('js/global.hexagons.js') }}"></script>
    <script>
        // Mark all hexagon elements initialized by global.hexagons.js so initHexagons() skips them
        document.querySelectorAll('.hexagon-image-30-32, .hexagon-border-40-44, .hexagon-22-24, .hexagon-dark-16-18').forEach(function(el) {
            el.dataset.hexInit = '1';
        });
    </script>
    <script src="{{ theme_asset('js/global.tooltips.js') }}"></script>
    <script src="{{ theme_asset('js/header.js') }}"></script>
    <script src="{{ theme_asset('js/sidebar.js') }}"></script>
    <script src="{{ theme_asset('js/content.js') }}"></script>
    <script src="{{ theme_asset('js/form.utils.js') }}"></script>
    <script src="{{ theme_asset('js/svg-loader.js') }}"></script>

    <script>
        function applyThemeLinks(mode) {
            document.body.dataset.theme = mode;
            document.documentElement.dataset.theme = mode;
            const links = document.querySelectorAll('link[data-theme-link="true"]');
            links.forEach(function(link) {
                const href = link.getAttribute('href');
                if (!href) {
                    return;
                }
                const nextHref = href.replace(/\/css_d\//, '/' + mode + '/').replace(/\/css\//, '/' + mode + '/');
                if (nextHref !== href) {
                    link.setAttribute('href', nextHref);
                }
            });
            const toggle = document.querySelector('.theme-toggle');
            if (toggle) {
                const isDark = mode === 'css_d';
                toggle.classList.toggle('is-dark', isDark);
                toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                toggle.setAttribute('title', isDark ? 'Light Mode' : 'Dark Mode');
            }
        }

        function setThemeMode(mode, persist = true) {
            applyThemeLinks(mode);
            if (persist) {
                try {
                    localStorage.setItem('themeMode', mode);
                } catch (e) {
                }
                document.cookie = 'modedark=' + mode + ';path=/;max-age=31536000';
            }
            window.__themeMode = mode;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.theme-toggle');
            const initialMode = window.__themeMode === 'css_d' ? 'css_d' : 'css';
            applyThemeLinks(initialMode);
            requestAnimationFrame(function() {
                applyThemeLinks(initialMode);
            });
            setTimeout(function() {
                applyThemeLinks(initialMode);
            }, 200);
            if (toggle) {
                toggle.classList.toggle('is-dark', initialMode === 'css_d');
                toggle.setAttribute('aria-pressed', initialMode === 'css_d' ? 'true' : 'false');
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    const nextMode = document.body.dataset.theme === 'css_d' ? 'css' : 'css_d';
                    setThemeMode(nextMode);
                });
            }
        });

        window.addEventListener('storage', function(event) {
            if (event.key === 'themeMode' && (event.newValue === 'css' || event.newValue === 'css_d')) {
                setThemeMode(event.newValue, false);
            }
        });

        function initHexagons(scope) {
            if (typeof app === 'undefined' || !app.plugins || !app.plugins.createHexagon) {
                return;
            }

            var searchRoot = (scope && typeof scope.querySelectorAll === 'function') ? scope : document;

            var hexConfigs = [
                {
                    selector: '.hexagon-image-30-32',
                    opts: { width: 30, height: 32, roundedCorners: true, roundedCornerRadius: 1, clip: true }
                },
                {
                    selector: '.hexagon-border-40-44',
                    opts: { width: 40, height: 44, lineWidth: 3, roundedCorners: true, roundedCornerRadius: 1, lineColor: '#e7e8ee' }
                },
                {
                    selector: '.hexagon-22-24',
                    opts: { width: 22, height: 24, roundedCorners: true, roundedCornerRadius: 1, lineColor: '#fff', fill: true }
                },
                {
                    selector: '.hexagon-dark-16-18',
                    opts: { width: 16, height: 18, roundedCorners: true, roundedCornerRadius: 1, fill: true, lineColor: '#4e4ac8' }
                }
            ];

            hexConfigs.forEach(function(cfg) {
                var elements = searchRoot.querySelectorAll(cfg.selector);
                elements.forEach(function(el) {
                    // Skip already-initialized elements
                    if (el.dataset.hexInit === '1') {
                        return;
                    }
                    // Remove any pre-existing empty canvas from server-rendered HTML
                    // so XM_Hexagon creates a fresh one with the image drawn on it
                    var existingCanvas = el.querySelector('canvas');
                    if (existingCanvas) {
                        existingCanvas.remove();
                    }
                    el.dataset.hexInit = '1';
                    try {
                        app.plugins.createHexagon(Object.assign({}, cfg.opts, {
                            containerElement: el
                        }));
                    } catch (e) {
                        // silently ignore
                    }
                });
            });
        }

        window.__afterInfiniteScrollRenderCallbacks = window.__afterInfiniteScrollRenderCallbacks || [];

        window.registerAfterInfiniteScrollRender = function(callback) {
            if (typeof callback !== 'function') {
                return;
            }

            window.__afterInfiniteScrollRenderCallbacks.push(callback);
        };

        window.runAfterInfiniteScrollRender = function(scope) {
            const targetScope = scope && typeof scope.querySelectorAll === 'function' ? scope : document;

            window.__afterInfiniteScrollRenderCallbacks.forEach(function(callback) {
                try {
                    callback(targetScope);
                } catch (error) {
                    console.error('afterInfiniteScrollRender callback failed:', error);
                }
            });
        };

        function markActivityDropdowns(scope) {
            if (!scope || typeof scope.querySelectorAll !== 'function') {
                return;
            }

            scope.querySelectorAll('.widget-box-post-settings-dropdown-trigger').forEach(function(trigger) {
                trigger.dataset.activityDropdownReady = '1';
            });
        }

        function initActivityDropdownGroup(scope, selector, containerSelector, options) {
            if (!scope || typeof scope.querySelectorAll !== 'function') {
                return;
            }

            if (!window.app || !app.plugins || typeof app.plugins.createDropdown !== 'function') {
                return;
            }

            scope.querySelectorAll(selector).forEach(function(trigger) {
                if (trigger.dataset.activityDropdownReady === '1') {
                    return;
                }

                const container = trigger.parentElement ? trigger.parentElement.querySelector(containerSelector) : null;
                if (!container) {
                    return;
                }

                app.plugins.createDropdown(Object.assign({
                    triggerElement: trigger,
                    containerElement: container,
                }, options));

                trigger.dataset.activityDropdownReady = '1';
            });
        }

        function hydrateActivityFeed(scope) {
            initActivityDropdownGroup(scope, '.widget-box-post-settings-dropdown-trigger', '.widget-box-post-settings-dropdown', {
                offset: {
                    top: 30,
                    right: 9
                },
                animation: {
                    type: 'translate-top',
                    speed: 0.3,
                    translateOffset: {
                        vertical: 20
                    }
                }
            });

            if (typeof window.initHexagons === 'function') {
                window.initHexagons(scope);
            }
        }

        window.hydrateActivityFeed = hydrateActivityFeed;

        function setActivityMenuState(wrap, isOpen) {
            if (!wrap) {
                return;
            }

            const trigger = wrap.querySelector('[data-activity-menu-trigger]');
            const panel = wrap.querySelector('[data-activity-menu-panel]');

            if (!panel) {
                return;
            }

            panel.style.opacity = isOpen ? '1' : '0';
            panel.style.visibility = isOpen ? 'visible' : 'hidden';
            panel.style.transform = isOpen ? 'translate(0px, 0px)' : 'translate(0px, 20px)';
            wrap.dataset.activityMenuOpen = isOpen ? '1' : '0';

            if (trigger) {
                trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                trigger.classList.toggle('active', isOpen);
            }
        }

        function closeActivityMenus(exceptWrap = null) {
            document.querySelectorAll('[data-activity-menu-wrap]').forEach(function(wrap) {
                if (exceptWrap && wrap === exceptWrap) {
                    return;
                }

                setActivityMenuState(wrap, false);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            markActivityDropdowns(document);
            window.registerAfterInfiniteScrollRender(function(scope) {
                hydrateActivityFeed(scope);
            });
        });

        function focusComment(id) {
            let el = document.getElementById('txt_comment' + id);
            if (el) {
                el.focus();
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function toggleReaction(id, type, reaction) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch('{{ route("reaction.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    reaction: reaction
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    let prefix = 'reaction-btn-';
                    if (type.includes('comment')) {
                        prefix = 'reaction-btn-comment-';
                    }
                    let btn = document.getElementById(prefix + id);
                    if (btn) {
                        btn.innerHTML = data.html;
                    }
                } else if (data.error) {
                    console.error(data.error);
                    alert('{{ __('messages.error_prefix') }}' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function loadComments(id, type, limit = 5) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let selector = '.post-comment-list-' + id;
            
            return fetch('{{ route("comment.load") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    limit: limit
                })
            })
            .then(response => response.text())
            .then(html => {
                let el = document.querySelector(selector);
                if(el) {
                    el.innerHTML = html;
                    initHexagons();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deletePost(id, type, containerSelector) {
            console.log("deletePost called with ID:", id, "Type:", type, "Selector:", containerSelector);
            showConfirmModal('{{ __('messages.confirm_delete') }}', function() {
                let url = '';
                let method = 'POST';
                let body = { id: id };

                if (type == 'forum' || type == 2 || type == 4 || type == 100) {
                    url = '{{ route("forum.delete") }}';
                } else if (type == 'store' || type == 7867) {
                    url = '{{ route("store.delete") }}';
                } else if (type == 'directory' || type == 1) {
                    url = '{{ route("directory.delete") }}';
                } else if (type == 'order' || type == 6) {
                    url = '{{ route("orders.destroy", ":id") }}'.replace(':id', id);
                    method = 'DELETE';
                    body = {};
                }

                if (!url) {
                    console.error('Unknown post type:', type);
                    return;
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: method === 'DELETE' ? null : JSON.stringify(body)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (containerSelector) {
                            const el = document.querySelector(containerSelector);
                            if (el) {
                                el.remove();
                                return;
                            }
                        }
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => console.error('Error:', error));
            }, id, containerSelector);
        }

        function buildReportForm(containerId, title) {
            let container = document.getElementById('report' + containerId);
            if (!container) return null;

            let textareaId = 'report_txt_' + containerId;
            let submitId = 'report_submit_' + containerId;
            let closeId = 'report_close_' + containerId;

            container.innerHTML = `
<hr />
<h4><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;${title}</h4>
<br />
<textarea class="quicktext form-control" id="${textareaId}"></textarea>
<hr />
<center>
<div class="btn-group">
<button id="${submitId}" class="btn btn-warning">${REPORT_TEXTS.confirm}</button>&nbsp;
<button id="${closeId}" class="btn btn-danger">${REPORT_TEXTS.close}</button>
</div>
</center>
`;

            return { container, textareaId, submitId, closeId };
        }

        function submitReportForm(form, tpId, sType) {
            let textarea = document.getElementById(form.textareaId);
            if (!textarea) return;

            let reason = textarea.value.trim();
            if (!reason) return;

            form.container.innerHTML = `<hr /><div class="alert alert-warning alert-dismissible fade show" role="alert">${REPORT_TEXTS.pending}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;

            fetch('{{ route("forum.report") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ tp_id: tpId, s_type: sType, txt: reason })
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    let message = data && data.error ? data.error : REPORT_TEXTS.errorPrefix;
                    form.container.innerHTML = `<hr /><div class="alert alert-danger" role="alert">${message}</div>`;
                }
            })
            .catch(error => {
                form.container.innerHTML = `<hr /><div class="alert alert-danger" role="alert">${REPORT_TEXTS.errorPrefix}</div>`;
                console.error('Error:', error);
            });
        }

        const REPORT_TEXTS = {
            report: @json(__('messages.report')),
            reportAuthor: @json(__('messages.report_author')),
            confirm: @json(__('messages.confirm')),
            close: @json(__('messages.close')),
            pending: @json(__('messages.pending')),
            errorPrefix: @json(__('messages.error_prefix')),
        };

        function reportPost(id, type, containerId = null) {
            let targetId = containerId || id;
            let form = buildReportForm(targetId, REPORT_TEXTS.report);
            if (!form) return;

            document.getElementById(form.submitId).addEventListener('click', function() {
                submitReportForm(form, id, type);
            });
            document.getElementById(form.closeId).addEventListener('click', function() {
                form.container.innerHTML = '';
            });
        }

        function reportUser(uid, containerId = null) {
            let targetId = containerId || uid;
            let form = buildReportForm(targetId, REPORT_TEXTS.reportAuthor);
            if (!form) return;

            document.getElementById(form.submitId).addEventListener('click', function() {
                submitReportForm(form, uid, 99);
            });
            document.getElementById(form.closeId).addEventListener('click', function() {
                form.container.innerHTML = '';
            });
        }

        function sharePost(social, url, title) {
            let shareUrls = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
                twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
                telegram: `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
            };
            if (shareUrls[social]) {
                window.open(shareUrls[social], '_blank', 'width=600,height=400');
            }
        }

        function postEdit(id, type, extra) {
             if (type == 100 || type == 4) {
                 let container = document.getElementById('post_form' + id);
                 if (!container) return;
                 
                 if (container.querySelector('textarea')) return;

                 let currentText = container.innerText;
                 
                 let html = `
                    <form onsubmit="event.preventDefault(); savePost(${id}, ${type}, this);">
                        <textarea class="form-control" name="txt" style="width:100%; height:100px;">${currentText.trim()}</textarea>
                        <div class="mt-2">
                            <button type="submit" class="button primary small" style="display:inline-block;">{{ __('messages.save') }}</button>
                            <button type="button" class="button white small" style="display:inline-block;" onclick="cancelEdit(${id});">{{ __('messages.cancel') }}</button>
                        </div>
                    </form>
                 `;
                 
                 container.dataset.originalContent = container.innerHTML;
                 container.innerHTML = html;
             } else if (type == 2) {
                 window.location.href = '{{ url("editor") }}/' + id;
             } else if (type == 1) {
                 window.location.href = '{{ url("directory") }}/' + id + '/edit';
             } else if (type == 7867) {
                 if (extra) {
                     window.location.href = '{{ url("store") }}/' + extra + '/update';
                 } else {
                     alert('Please use the edit button on the product page.');
                 }
             }
        }

        function savePost(id, type, form) {
            let txt = form.txt.value;
            fetch('{{ route("forum.update", ":id") }}'.replace(':id', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ 
                    name: 'post', 
                    txt: txt,
                    cat: 0 
                })
            })
            .then(response => {
                if(response.ok) {
                    window.location.reload();
                } else {
                    response.json().then(data => {
                        alert('Error: ' + (data.message || 'Error saving post'));
                    }).catch(() => {
                        alert('Error saving post');
                    });
                }
            });
        }

        function cancelEdit(id) {
            let container = document.getElementById('post_form' + id);
            if (container && container.dataset.originalContent) {
                container.innerHTML = container.dataset.originalContent;
            }
        }

        function postComment(id, type) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let input = document.getElementById('txt_comment' + id);
            if (!input) return;

            let text = input.value;
            if (!text.trim()) return;

            fetch('{{ route("comment.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    comment: text
                })
            })
            .then(async response => {
                const fallbackError = @json(__('messages.error_prefix'));
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok || data.error) {
                        throw new Error(data.error || fallbackError);
                    }

                    return data.html || '';
                }

                const html = await response.text();
                if (!response.ok) {
                    throw new Error(fallbackError);
                }

                return html;
            })
            .then(html => {
                let el = document.querySelector('.post-comment-list-' + id);
                if (el && html) {
                    el.innerHTML = html;
                    initHexagons();
                }

                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.focus();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || @json(__('messages.error_prefix')));
            });
        }

        function deleteComment(trashid, type) {
            showConfirmModal('{{ __("messages.confirm_delete") }}', function() {
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('{{ route("comment.delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        trashid: trashid,
                        type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        let el = document.querySelector('.coment' + trashid);
                        if (el) el.remove();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            }, trashid); // Uses default prefix post_form but wait, comments use .comentid
        }

        let pendingConfirmCallback = null;
        let confirmPopup = null;

        function showConfirmModal(body, callback, targetId, containerSelector) {
            console.log("showConfirmModal triggered for targetId:", targetId, "Selector:", containerSelector);
            
            // Try in-place confirmation first
            let container = null;
            if (containerSelector) {
                container = document.querySelector(containerSelector);
                // Special case for our post_form as its often nested
                if (container && container.querySelector('.textpost')) {
                    const inner = container.querySelector('.textpost');
                    if(inner.id.includes('post_form')) container = inner;
                }
            } else if (targetId) {
                container = document.getElementById('post_form' + targetId) || document.querySelector('.coment' + targetId);
            }

            if (container) {
                const originalContent = container.innerHTML;
                const confirmHtml = `
                    <div class="confirmation-box" style="padding: 20px; border: 1px solid #615dfa; border-radius: 12px; background: rgba(97, 93, 250, 0.05); text-align: center; margin: 10px 0;">
                        <p style="font-weight: 700; margin-bottom: 15px; color: #3e3f5e;">${body}</p>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button type="button" class="button primary small" id="inline-confirm-yes">${@json(__('messages.delete'))}</button>
                            <button type="button" class="button white small" id="inline-confirm-no">${@json(__('messages.cancel'))}</button>
                        </div>
                    </div>
                `;
                container.innerHTML = confirmHtml;
                
                container.querySelector('#inline-confirm-yes').onclick = function() {
                    callback();
                };
                
                container.querySelector('#inline-confirm-no').onclick = function() {
                    container.innerHTML = originalContent;
                };
                
                return;
            }

            // Fallback to confirm()
            if (confirm(body)) {
                callback();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-popup-confirm-button');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    console.log("Confirm button clicked in popup");
                    if (pendingConfirmCallback) {
                        pendingConfirmCallback();
                        pendingConfirmCallback = null;
                    }
                    if (confirmPopup) confirmPopup.hide();
                });
            }
        });

        function getCsrfToken() {
            let tokenMeta = document.querySelector('meta[name="csrf-token"]');
            return tokenMeta ? tokenMeta.getAttribute('content') : '';
        }

        function deletePostByUrl(url, id, containerSelector) {
            showConfirmModal('{{ __('messages.confirm_delete') }}', function() {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (containerSelector) {
                            let container = document.querySelector(containerSelector);
                            if (container) {
                                container.remove();
                                return;
                            }
                        }
                        window.location.reload();
                    } else if (data.error) {
                        alert(data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            }, id, containerSelector);
        }

        function reportPostByUrl(url, id, type) {
            let reason = prompt('{{ __('messages.report_reason') }}');
            if (!reason) return;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ tp_id: id, s_type: type, txt: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ __('messages.report_sent') }}');
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        document.addEventListener('click', function(event) {
            let menuTrigger = event.target.closest('[data-activity-menu-trigger]');
            if (menuTrigger) {
                event.preventDefault();

                let menuWrap = menuTrigger.closest('[data-activity-menu-wrap]');
                if (!menuWrap) {
                    return;
                }

                let isOpen = menuWrap.dataset.activityMenuOpen === '1';
                closeActivityMenus(menuWrap);
                setActivityMenuState(menuWrap, !isOpen);
                return;
            }

            let menuPanel = event.target.closest('[data-activity-menu-panel]');
            if (menuPanel) {
                closeActivityMenus();
                return;
            }

            if (!event.target.closest('[data-activity-menu-wrap]')) {
                closeActivityMenus();
            }

            let commentTrigger = event.target.closest('[data-activity-comment]');
            if (commentTrigger) {
                event.preventDefault();

                let id = parseInt(commentTrigger.dataset.commentId || '0', 10);
                let type = commentTrigger.dataset.commentType;
                let shouldFocus = commentTrigger.dataset.commentFocus === '1';

                if (!id || !type) {
                    return;
                }

                loadComments(id, type)
                    .then(() => {
                        commentTrigger.classList.add('active');

                        if (shouldFocus) {
                            focusComment(id);
                        }
                    })
                    .catch(error => console.error('Error:', error));

                return;
            }

            let target = event.target.closest('[data-post-action]');
            if (!target) return;

            let action = target.dataset.postAction;
            let id = target.dataset.postId;
            let type = target.dataset.postType;
            let editUrl = target.dataset.editUrl;
            let deleteUrl = target.dataset.deleteUrl;
            let reportUrl = target.dataset.reportUrl;
            let containerSelector = target.dataset.postContainer;

            if (action === 'edit' && editUrl) {
                event.preventDefault();
                window.location.href = editUrl;
                return;
            }

            if (action === 'delete' && deleteUrl && id) {
                event.preventDefault();
                deletePostByUrl(deleteUrl, id, containerSelector);
                return;
            }

            if (action === 'report' && reportUrl && id && type) {
                event.preventDefault();
                reportPostByUrl(reportUrl, id, type);
            }
        });
    </script>
    
    <!-- Deletion Confirmation Popup (Vikinger Style) -->
    <div id="confirm-popup" class="xm-popup-container">
        <div class="xm-popup-overlay"></div>
        <div class="popup-box">
            <div class="popup-box-header">
                <p class="popup-box-header-title text-header">{{ __('messages.confirm') }}</p>
                <div class="popup-box-header-close-button popup-close-trigger">
                    <svg class="popup-box-header-close-button-icon icon-cross">
                        <use xlink:href="#svg-cross"></use>
                    </svg>
                </div>
            </div>
            <div class="popup-box-body">
                <p class="popup-box-body-text" style="color: #3e3f5e; line-height: 1.6;">{{ __('messages.confirm_delete') }}</p>
            </div>
            <div class="popup-box-actions" style="padding: 20px; display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid #eaeaf5;">
                <button type="button" class="button white small popup-close-trigger">{{ __('messages.cancel') }}</button>
                <button type="button" class="button primary small" id="confirm-popup-confirm-button">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    @include('theme::partials._cookie_consent')

    @stack('scripts')
</body>
</html>
