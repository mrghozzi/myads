@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
    $css_path = $mode == 'css_d' ? 'css_d' : 'css';
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-theme="{{ $css_path }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.privacy_policy') }} - {{ $site_settings->titer ?? 'MyAds' }}</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet">
    
    <!-- Core CSS -->
    <link href="{{ theme_asset($css_path . '/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #615dfa;
            --primary-glow: rgba(97, 93, 250, 0.4);
            --secondary: #23d2e2;
            --accent: #f59e0b;
            --bg-base: #0f172a;
            --bg-surface: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        [data-theme="css"] {
            --bg-base: #f8faff;
            --bg-surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            overflow-x: hidden;
            transition: background-color 0.4s ease, color 0.4s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            margin-bottom: 0;
        }

        a {
            text-decoration: none;
        }

        /* --- Animations --- */
        @keyframes blobBounce {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.2s; }

        /* --- Background blobs --- */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
            animation: blobBounce 15s infinite alternate;
        }
        .blob-1 { top: -10%; left: -10%; width: 500px; height: 500px; background: var(--primary); }
        .blob-2 { top: 20%; right: -10%; width: 400px; height: 400px; background: var(--secondary); animation-delay: 2s; }
        .blob-3 { bottom: -20%; left: 20%; width: 600px; height: 600px; background: #8b5cf6; animation-delay: 4s; }

        /* --- Navbar (Standalone) --- */
        .top-nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 20px 0;
            z-index: 1000;
            transition: all 0.3s ease;
            backdrop-filter: blur(0px);
            background: transparent;
        }
        .top-nav.scrolled {
            padding: 15px 0;
            backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        [data-theme="css"] .top-nav.scrolled {
            background: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .nav-container {
            display: flex; justify-content: space-between; align-items: center;
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
        }
        .logo { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
        .nav-actions { display: flex; gap: 16px; align-items: center; }
        
        .btn-theme-toggle { background: transparent; border: none; color: var(--text-main); font-size: 1.2rem; cursor: pointer; transition: color 0.3s; }
        .btn-theme-toggle:hover { color: var(--primary); }

        .btn-glass {
            padding: 10px 24px; border-radius: 50px; font-weight: 600; color: var(--text-main);
            background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px); transition: all 0.3s;
        }
        [data-theme="css"] .btn-glass { background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.05); }
        .btn-glass:hover { background: rgba(255, 255, 255, 0.1); transform: translateY(-2px); }
        [data-theme="css"] .btn-glass:hover { background: rgba(0, 0, 0, 0.08); }

        .btn-super {
            padding: 12px 30px; border-radius: 50px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, var(--primary), #8b5cf6); border: none;
            box-shadow: 0 10px 30px var(--primary-glow); transition: all 0.3s;
        }
        .btn-super:hover { transform: translateY(-3px); box-shadow: 0 15px 40px var(--primary-glow); color: #fff; }

        /* --- Footer --- */
        .footer {
            padding: 40px 24px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: var(--bg-surface); position: relative; z-index: 10; margin-top: auto;
        }
        [data-theme="css"] .footer { border-top: 1px solid rgba(0, 0, 0, 0.05); }
        .footer p { color: var(--text-muted); font-size: 0.95rem; }
        .footer-links {
            display: flex; justify-content: center; gap: 30px; margin-bottom: 25px; flex-wrap: wrap;
        }
        .footer-links a {
            color: var(--text-muted); font-size: 0.95rem; font-weight: 500; display: flex; align-items: center; gap: 8px; transition: color 0.3s;
        }
        .footer-links a i { font-size: 1.1rem; }
        .footer-links a:hover { color: var(--primary); }

        /* Lang Dropdown */
        .lang-switcher { position: relative; }
        .btn-lang {
            background: transparent; border: none; color: var(--text-main); font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 5px; padding: 8px 12px; font-size: 0.9rem;
        }
        .lang-dropdown {
            position: absolute; top: 100%; right: 0; background: var(--bg-surface); border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px; min-width: 150px; display: none; flex-direction: column; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); margin-top: 10px;
        }
        [data-theme="css"] .lang-dropdown { border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .lang-dropdown.show { display: flex; }
        .lang-dropdown a { padding: 10px 20px; color: var(--text-main); transition: background 0.2s; font-size: 0.9rem; }
        .lang-dropdown a:hover { background: rgba(97, 93, 250, 0.1); color: var(--primary); }

        /* --- Legal Page specific CSS --- */
        .legal-wrapper {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 120px 24px 80px;
            position: relative;
            z-index: 10;
        }
        .legal-page {
            width: 100%;
            max-width: 800px;
            background: var(--bg-surface);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(20px);
        }
        [data-theme="css"] .legal-page {
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.05);
        }
        
        .legal-page h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 10px; color: var(--text-main); }
        .legal-page .legal-updated { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 30px; }
        .legal-page .legal-intro {
            font-size: 1.05rem; line-height: 1.7; color: var(--text-muted); margin-bottom: 30px; padding: 20px;
            background: rgba(97, 93, 250, 0.05); border-radius: 12px; border-left: 4px solid var(--primary);
        }
        [data-theme="css"] .legal-page .legal-intro { background: rgba(97, 93, 250, 0.05); }
        .legal-section { margin-bottom: 28px; }
        .legal-section h2 { font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.05); }
        [data-theme="css"] .legal-section h2 { border-bottom: 2px solid rgba(0,0,0,0.05); }
        .legal-section p { font-size: 0.95rem; line-height: 1.7; color: var(--text-muted); }
    </style>
</head>
<body>

    <!-- Animated Blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    <div class="bg-blob blob-3"></div>

    <!-- Navigation -->
    <nav class="top-nav" id="navbar">
        <div class="nav-container">
            <a href="{{ url('/') }}" class="logo">
                <picture>
                    <source srcset="{{ theme_asset('img/logo_w.webp') }}" type="image/webp">
                    <img src="{{ theme_asset('img/logo_w.png') }}" style="max-height: 40px;" alt="logo">
                </picture>
            </a>
            <div class="nav-actions">
                <button class="btn-theme-toggle" id="themeToggle" title="Toggle Theme">
                    <i class="fa-solid fa-moon"></i>
                </button>
                @auth
                <a href="{{ route('profile.short', auth()->user()->publicRouteIdentifier()) }}" style="margin-left: 20px; display: inline-flex; align-items: center; justify-content: center; position: relative; width: 40px; height: 44px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <!-- Hexagon Border -->
                    <svg width="40" height="44" viewBox="0 0 40 44" style="position: absolute; top: 0; left: 0; z-index: 1;">
                        <polygon points="20,1.5 38.5,12 38.5,32 20,42.5 1.5,32 1.5,12" fill="none" stroke="{{ auth()->user()->profileBadgeColor() ?: 'var(--text-muted)' }}" stroke-width="2.5" stroke-linejoin="round" />
                    </svg>
                    <!-- Hexagon Image -->
                    <div style="position: absolute; z-index: 2; width: 30px; height: 32px; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); background-image: url('{{ auth()->user()->avatarUrl() }}'); background-size: cover; background-position: center; border-radius: 2px;"></div>
                    
                    @if(auth()->user()->hasVerifiedBadge())
                    <!-- Verified Badge -->
                    <div style="position: absolute; bottom: -2px; right: -4px; z-index: 3; width: 22px; height: 24px;">
                        <svg width="22" height="24" viewBox="0 0 22 24" style="position: absolute; top: 0; left: 0;">
                            <polygon points="11,1 21,6.5 21,17.5 11,23 1,17.5 1,6.5" fill="var(--bg-surface)" />
                            <polygon points="11,3 18,7.5 18,16.5 11,21 4,16.5 4,7.5" fill="#1da1f2" />
                        </svg>
                        <i class="fa-solid fa-check" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 8px; color: white;"></i>
                    </div>
                    @endif
                </a>
                @else
                <a href="{{ route('login') }}" class="btn-glass d-none d-sm-block">{{ __('messages.login') ?? 'Login' }}</a>
                <a href="{{ url('/register') }}" class="btn-super d-none d-sm-block">{{ __('messages.register') ?? 'Get Started' }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="legal-wrapper">
        <div class="legal-page scroll-reveal">
            <h1>{{ __('messages.privacy_policy') }}</h1>
            <p class="legal-updated">{{ __('messages.privacy_last_updated') }}: {{ date('Y-m-d') }}</p>

            <div class="legal-intro">
                {{ __('messages.privacy_intro') }}
            </div>

            <div class="legal-section">
                <h2>1. {{ __('messages.privacy_info_collect') }}</h2>
                <p>{{ __('messages.privacy_info_collect_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>2. {{ __('messages.privacy_how_use') }}</h2>
                <p>{{ __('messages.privacy_how_use_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>3. {{ __('messages.privacy_cookies') }}</h2>
                <p>{{ __('messages.privacy_cookies_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>4. {{ __('messages.privacy_data_sharing') }}</h2>
                <p>{{ __('messages.privacy_data_sharing_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>5. {{ __('messages.privacy_security') }}</h2>
                <p>{{ __('messages.privacy_security_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>6. {{ __('messages.privacy_rights') }}</h2>
                <p>{{ __('messages.privacy_rights_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>7. {{ __('messages.privacy_changes') }}</h2>
                <p>{{ __('messages.privacy_changes_desc') }}</p>
            </div>

            <div class="legal-section">
                <h2>8. {{ __('messages.privacy_contact') }}</h2>
                <p>{{ __('messages.privacy_contact_desc') }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-links">
            <a href="{{ url('/sitemap.xml') }}"><i class="fa-solid fa-sitemap"></i> Sitemap</a>
            <a href="{{ url('/developer') }}"><i class="fa-solid fa-code"></i> Developers</a>
            <a href="{{ url('/privacy') }}"><i class="fa-solid fa-shield-halved"></i> Privacy Policy</a>
            <a href="{{ url('/terms') }}"><i class="fa-solid fa-file-contract"></i> Terms & Conditions</a>
            <a href="{{ url('/refund') }}"><i class="fa-solid fa-arrow-rotate-left"></i> Refund Policy</a>
        </div>
        <p>&copy; {{ date('Y') }} {{ $site_settings->titer ?? 'MyAds' }}. {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}</p>
        <p style="margin-top: 10px; font-size: 0.85rem; opacity: 0.7;">Powered by <strong>MyAds SEO Engine</strong> | v{{ \App\Support\SystemVersion::CURRENT ?? '4.3.4' }}</p>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Intersection Observer for animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.scroll-reveal').forEach((el) => {
                observer.observe(el);
            });

            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = themeToggleBtn.querySelector('i');
            const htmlTag = document.documentElement;
            
            function updateIcon(theme) {
                if(theme === 'css_d') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            }
            
            updateIcon(htmlTag.getAttribute('data-theme'));

            themeToggleBtn.addEventListener('click', () => {
                let currentTheme = htmlTag.getAttribute('data-theme');
                let newTheme = currentTheme === 'css_d' ? 'css' : 'css_d';
                htmlTag.setAttribute('data-theme', newTheme);
                document.cookie = "modedark=" + newTheme + ";path=/;max-age=31536000";
                updateIcon(newTheme);
            });
        });
    </script>
</body>
</html>
