@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
    $css_path = $mode == 'css_d' ? 'css_d' : 'css';
    $seo = app(\App\Services\SeoManager::class)->resolve(request());
    $resolvedTitle = trim((string) ($seo->title ?? ''));
    $resolvedTitle = $resolvedTitle !== '' ? $resolvedTitle : (__('messages.sign_in') . ' - ' . ($site_settings->titer ?? 'MyAds'));
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-theme="{{ $css_path }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resolvedTitle }}</title>
    @include('theme::partials._seo_head')
    
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

        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        .checkbox-wrap input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .checkbox-wrap label { margin-bottom: 0; cursor: pointer; color: var(--text-muted); }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .form-options a {
            color: var(--primary);
            font-weight: 500;
        }
        .form-options a:hover { text-decoration: underline; }

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
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px var(--primary-glow);
        }

        .social-login-text {
            text-align: center;
            position: relative;
            margin: 30px 0 20px;
        }
        .social-login-text::before {
            content: ''; position: absolute; top: 50%; left: 0; width: 100%; height: 1px;
            background: rgba(255, 255, 255, 0.1); z-index: 1;
        }
        [data-theme="css"] .social-login-text::before { background: rgba(0, 0, 0, 0.1); }
        .social-login-text span {
            position: relative; z-index: 2; background: var(--bg-surface); padding: 0 15px;
            color: var(--text-muted); font-size: 0.9rem;
        }

        .social-buttons { display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; }
        .btn-social {
            flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; color: #fff; transition: all 0.3s;
        }
        .btn-facebook { background: #3b5998; }
        .btn-google { background: #dd4b39; }
        .btn-adstn { background: rgb(84, 56, 163); }
        .btn-social:hover { transform: translateY(-2px); opacity: 0.9; color: #fff; }

        .switch-auth {
            text-align: center; margin-top: 20px; color: var(--text-muted);
        }
        .switch-auth a { color: var(--primary); font-weight: 600; }
        .switch-auth a:hover { text-decoration: underline; }



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
                <a href="{{ url('/register') }}" class="btn-super d-none d-sm-block">{{ __('messages.register') ?? 'Get Started' }}</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="auth-wrapper">
        <div class="auth-box scroll-reveal active">
            <h2>{{ __('messages.sign_in') }}</h2>
            
            @if(session('error'))
                <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 12px 16px; border-radius: 8px; font-size: 0.9rem;" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 12px 16px; border-radius: 8px; font-size: 0.9rem;" role="alert">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="login-username">{{ __('messages.usermail') }}</label>
                    <input type="text" class="form-control" id="login-username" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="form-group">
                    <label for="login-password">{{ __('messages.password') }}</label>
                    <input type="password" class="form-control" id="login-password" name="password" required>
                </div>

                @if(\App\Support\SecuritySettings::get('captcha_enabled_for_login'))
                    <div class="form-group">
                        <label for="capt">{{ __('messages.verification_code') ?? 'Verification Code' }}</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <img src="{{ route('captcha.generate') }}" id="captcha-img" style="cursor: pointer; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);" title="Click to refresh" onclick="document.getElementById('captcha-img').src='{{ route('captcha.generate') }}?'+Math.random()">
                            <input type="text" class="form-control" id="capt" name="capt" required style="width: 120px;">
                        </div>
                    </div>
                @endif

                <div class="form-options">
                    <div class="checkbox-wrap">
                        <input type="checkbox" id="login-remember" name="remember" checked>
                        <label for="login-remember">{{ __('messages.remember_me') }}</label>
                    </div>
                    <a href="{{ route('password.request') }}">{{ __('messages.forgot_password') }}</a>
                </div>

                <button class="btn-submit" name="login" type="submit">{{ __('messages.login') }}</button>
            </form>

            @if(env('FACEBOOK_CLIENT_ID') || env('GOOGLE_CLIENT_ID') || env('ADSTN_CLIENT_ID'))
                <div class="social-login-text">
                    <span>{{ __('messages.login_with_social') }}</span>
                </div>
                <div class="social-buttons">
                    @if(env('FACEBOOK_CLIENT_ID'))
                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social btn-facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(env('GOOGLE_CLIENT_ID'))
                    <a href="{{ route('social.redirect', 'google') }}" class="btn-social btn-google">
                        <i class="fa-brands fa-google"></i>
                    </a>
                    @endif
                    @if(env('ADSTN_CLIENT_ID'))
                    <a href="{{ route('social.redirect', 'adstn') }}" class="btn-social btn-adstn">
                        <i class="fa-brands fa-buysellads"></i>
                    </a>
                    @endif
                </div>
            @endif

            <div class="switch-auth">
                {{ __('messages.donthaacc') }} <a href="{{ route('register') }}">{{ __('messages.sign_up') }}</a>
            </div>
        </div>
    </section>

    @include('theme::partials._standalone_footer')


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
