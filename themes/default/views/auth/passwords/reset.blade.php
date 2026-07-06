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
    <title>{{ __('messages.reset_password') }} - {{ $site_settings->titer ?? 'MyAds' }}</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ theme_asset('css/fontawesome6.min.css') }}" rel="stylesheet">
    
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

        /* --- Navbar --- */
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

        /* --- Auth Box --- */
        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 24px 80px;
            position: relative;
            z-index: 10;
        }
        .auth-box {
            width: 100%;
            max-width: 480px;
            background: var(--bg-surface);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(20px);
        }
        [data-theme="css"] .auth-box {
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.05);
        }
        .auth-box h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: var(--text-main);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-main);
            font-size: 1rem;
            transition: all 0.3s;
        }
        [data-theme="css"] .form-control {
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            background: rgba(0, 0, 0, 0.3);
        }
        [data-theme="css"] .form-control:focus { background: #fff; }

        .btn-submit {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 20px var(--primary-glow);
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px var(--primary-glow);
        }

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
        .btn-lang { background: transparent; border: none; color: var(--text-main); font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 8px 12px; font-size: 0.9rem; }
        .lang-dropdown { position: absolute; top: 100%; right: 0; background: var(--bg-surface); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; min-width: 150px; display: none; flex-direction: column; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); margin-top: 10px; }
        [data-theme="css"] .lang-dropdown { border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .lang-dropdown.show { display: flex; }
        .lang-dropdown a { padding: 10px 20px; color: var(--text-main); transition: background 0.2s; font-size: 0.9rem; }
        .lang-dropdown a:hover { background: rgba(97, 93, 250, 0.1); color: var(--primary); }
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
                <div class="lang-switcher">
                    <button class="btn-lang" onclick="document.getElementById('langDropdown').classList.toggle('show')">
                        {{ strtoupper(app()->getLocale()) }} <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="lang-dropdown" id="langDropdown">
                        @foreach($available_languages ?? [] as $lang)
                            <a href="?lang={{ $lang->code }}">{{ $lang->name }}</a>
                        @endforeach
                    </div>
                </div>
                <button class="btn-theme-toggle" id="themeToggle" title="Toggle Theme">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <a href="{{ route('login') }}" class="btn-glass d-none d-sm-block">{{ __('messages.login') ?? 'Login' }}</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="auth-wrapper">
        <div class="auth-box scroll-reveal active">
            <h2>{{ __('messages.reset_password') }}</h2>
            
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input id="email" type="text" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    
                    @error('email')
                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('messages.password') }}</label>
                    <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">
                    
                    @error('password')
                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">{{ __('messages.confirm_password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>

                <button class="btn-submit" type="submit">{{ __('messages.reset_password') }}</button>
            </form>
        </div>
    </section>

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
        <p style="margin-top: 10px; font-size: 0.85rem; opacity: 0.7;">Powered by <strong>MyAds SEO Engine</strong> | v{{ \App\Support\SystemVersion::CURRENT ?? '4.3.5' }}</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Navbar Scrolled Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Close lang dropdown on click outside
        document.addEventListener('click', (e) => {
            if(!e.target.closest('.lang-switcher')) {
                const dropdown = document.getElementById('langDropdown');
                if(dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        themeToggle.addEventListener('click', () => {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'css' ? 'css_d' : 'css';
            html.setAttribute('data-theme', newTheme);
            
            // Icon switch
            themeToggle.innerHTML = newTheme === 'css' ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
            
            // Save preference via cookie
            document.cookie = `modedark=${newTheme}; path=/; max-age=31536000`;
        });
        
        // Initial icon state
        if(document.documentElement.getAttribute('data-theme') === 'css_d') {
            themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
        }
    </script>
</body>
</html>
