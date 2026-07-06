@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
    $css_path = $mode == 'css_d' ? 'css_d' : 'css';
    $seo = app(\App\Services\SeoManager::class)->resolve(request());
    $resolvedTitle = trim((string) ($seo->title ?? ''));
    $resolvedTitle = $resolvedTitle !== '' ? $resolvedTitle : ((__('messages.welcome_title') ?? 'Welcome') . ' - ' . ($site_settings->titer ?? 'MyAds'));
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

        @keyframes floatY {
            0% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); }
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
        .delay-3 { transition-delay: 0.3s; }

        /* --- Background blobs --- */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
            animation: blobBounce 15s infinite alternate;
        }
        .blob-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: var(--primary);
        }
        .blob-2 {
            top: 20%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: var(--secondary);
            animation-delay: 2s;
        }
        .blob-3 {
            bottom: -20%;
            left: 20%;
            width: 600px;
            height: 600px;
            background: #8b5cf6;
            animation-delay: 4s;
        }

        /* --- Navbar (Standalone) --- */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .logo {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo span {
            color: var(--primary);
        }
        .nav-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .btn-theme-toggle {
            background: transparent;
            border: none;
            color: var(--text-main);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s;
        }
        .btn-theme-toggle:hover {
            color: var(--primary);
        }

        /* --- Buttons --- */
        .btn-glass {
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        [data-theme="css"] .btn-glass {
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        [data-theme="css"] .btn-glass:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .btn-super {
            padding: 14px 36px;
            border-radius: 50px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border: none;
            box-shadow: 0 10px 30px var(--primary-glow);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-super::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #8b5cf6, var(--primary));
            z-index: -1;
            transition: opacity 0.3s;
            opacity: 0;
        }
        .btn-super:hover::before { opacity: 1; }
        .btn-super:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px var(--primary-glow);
            color: #fff;
        }

        /* --- Hero Section --- */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 24px 80px;
            overflow: hidden;
        }
        .hero-content {
            max-width: 900px;
            text-align: center;
            z-index: 10;
        }
        .badge-premium {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            background: rgba(97, 93, 250, 0.1);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 24px;
            border: 1px solid rgba(97, 93, 250, 0.2);
            box-shadow: 0 0 20px rgba(97, 93, 250, 0.1);
        }
        .hero h1 {
            font-size: clamp(3rem, 6vw, 5rem);
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .hero-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* --- Visual Elements --- */
        .dashboard-preview {
            margin-top: 60px;
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            background: var(--bg-surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            padding: 20px;
            transform: perspective(1000px) rotateX(5deg);
            transition: transform 0.5s;
            animation: floatY 6s ease-in-out infinite;
        }
        [data-theme="css"] .dashboard-preview {
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        }
        .dashboard-preview:hover {
            transform: perspective(1000px) rotateX(0deg);
        }
        .dashboard-header {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot.r { background: #ef4444; }
        .dot.y { background: #f59e0b; }
        .dot.g { background: #10b981; }
        .dashboard-body {
            height: 400px;
            border-radius: 12px;
            background: var(--bg-base);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-body i {
            font-size: 5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.5;
        }

        /* --- Features Section --- */
        .features {
            padding: 120px 24px;
            position: relative;
            z-index: 10;
        }
        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }
        .section-header h2 {
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            margin-bottom: 16px;
        }
        .section-header p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-card {
            background: var(--bg-surface);
            border-radius: 24px;
            padding: 40px 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        [data-theme="css"] .feature-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        [data-theme="css"] .feature-card:hover {
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(97, 93, 250, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }
        .feature-card:hover .feature-icon {
            background: var(--primary);
            color: #fff;
            transform: rotate(-10deg) scale(1.1);
        }
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .feature-card p {
            color: var(--text-muted);
            line-height: 1.7;
        }


        /* --- Lang Switcher --- */
        .lang-switcher {
            position: relative;
        }
        .btn-lang {
            background: transparent;
            border: none;
            color: var(--text-main);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        .lang-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--bg-surface);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            min-width: 150px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-top: 10px;
        }
        [data-theme="css"] .lang-dropdown {
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .lang-dropdown.show { display: flex; }
        .lang-dropdown a {
            padding: 10px 20px;
            color: var(--text-main);
            transition: background 0.2s;
            font-size: 0.9rem;
        }
        .lang-dropdown a:hover {
            background: rgba(97, 93, 250, 0.1);
            color: var(--primary);
        }

        /* --- Stats Section --- */
        .stats-section {
            padding: 80px 24px;
            position: relative;
            z-index: 10;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .stat-card {
            background: var(--bg-surface);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s;
        }
        [data-theme="css"] .stat-card { border: 1px solid rgba(0, 0, 0, 0.05); }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-value {
            font-size: 2.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-label {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 500;
        }



        /* Responsive */
        @media (max-width: 768px) {
            .hero { padding-top: 150px; }
            .hero-actions { flex-direction: column; width: 100%; max-width: 300px; margin: 0 auto; }
            .btn-super, .btn-glass { width: 100%; text-align: center; }
            .dashboard-preview { height: 250px; }
            .dashboard-body { height: 200px; }
        }
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="badge-premium scroll-reveal">
                <i class="fa-solid fa-bolt text-warning me-2"></i> Premium Network Platform
            </div>
            <h1 class="scroll-reveal delay-1">
                {{ __('messages.landing_hero_title') ?? 'Discover, Connect &' }} 
                <span class="gradient-text">{{ __('messages.landing_hero_title_highlight') ?? 'Grow Your Audience' }}</span>
            </h1>
            <p class="scroll-reveal delay-2">
                {{ __('messages.landing_hero_subtitle') ?? 'Join the ultimate social network and ad exchange platform. Boost your website traffic, engage with the community, and manage your premium ads all in one place.' }}
            </p>
            <div class="hero-actions scroll-reveal delay-3">
                <a href="{{ url('/register') }}" class="btn-super">
                    <i class="fa-solid fa-rocket me-2"></i> {{ __('messages.landing_hero_cta') ?? 'Start Now for Free' }}
                </a>
                <a href="{{ route('login') }}" class="btn-glass">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> {{ __('messages.landing_hero_cta_login') ?? 'Sign In' }}
                </a>
            </div>

            <div class="dashboard-preview scroll-reveal delay-3">
                <div class="dashboard-header">
                    <div class="dot r"></div>
                    <div class="dot y"></div>
                    <div class="dot g"></div>
                </div>
                <div class="dashboard-body">
                    <img src="{{ theme_asset('img/dashboard-mockup.png') }}" alt="Dashboard Preview" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.95;">
                </div>
            </div>
        </div>
    </section>

    <!-- Ad #1 -->
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 24px; z-index: 10; position: relative;" class="scroll-reveal delay-3">
        @include('theme::partials.ads', ['id' => 1])
    </div>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card scroll-reveal">
                <div class="stat-value">{{ number_format(\App\Models\User::count()) }}</div>
                <div class="stat-label">{{ __('messages.members') ?? 'Members' }}</div>
            </div>
            <div class="stat-card scroll-reveal delay-1">
                <div class="stat-value">{{ number_format(\App\Models\Status::count()) }}</div>
                <div class="stat-label">{{ __('messages.posts') ?? 'Posts' }}</div>
            </div>
            <div class="stat-card scroll-reveal delay-2">
                <div class="stat-value">{{ number_format(\App\Models\Banner::where('statu', 1)->count() + \App\Models\Link::where('statu', 1)->count() + \App\Models\SmartAd::where('statu', 1)->count()) }}</div>
                <div class="stat-label">{{ __('messages.active_ads') ?? 'Active Ads' }}</div>
            </div>
            <div class="stat-card scroll-reveal delay-3">
                <div class="stat-value">{{ number_format(\App\Models\Visit::count() + \App\Models\SeoDailyMetric::sum('page_views')) }}</div>
                <div class="stat-label">{{ __('messages.daily_visits') ?? 'Total Visits' }}</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header scroll-reveal">
            <h2>{{ __('messages.landing_features_title') ?? 'Everything You Need' }}</h2>
            <p>{{ __('messages.landing_features_subtitle') ?? 'Powerful tools to scale your presence, monetize traffic, and build meaningful connections.' }}</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card scroll-reveal">
                <div class="feature-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <h3>{{ __('messages.landing_feature_banners_title') ?? 'Smart Ad Exchange' }}</h3>
                <p>{{ __('messages.landing_feature_banners_desc') ?? 'Monetize your space and drive targeted traffic with our intelligent banner, text, and native ad exchange algorithms.' }}</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card scroll-reveal delay-1" onclick="window.location.href='{{ url('/portal') }}'" style="cursor: pointer;">
                <div class="feature-icon" style="background: rgba(35, 210, 226, 0.1); color: var(--secondary);">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3>{{ __('messages.landing_community_title') ?? 'Vibrant Community' }}</h3>
                <p>{{ __('messages.landing_community_subtitle') ?? 'Share posts, interact with media, react, comment, and grow your followers in a fully-featured social feed.' }}</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card scroll-reveal delay-2">
                <div class="feature-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--accent);">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <h3>{{ __('messages.quests') ?? 'Gamification & Rewards' }}</h3>
                <p>{{ __('messages.quests_desc') ?? 'Earn points, unlock exclusive badges, and complete daily quests while engaging with the platform.' }}</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card scroll-reveal" onclick="window.location.href='{{ url('/store') }}'" style="cursor: pointer;">
                <div class="feature-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h3>{{ __('messages.store') ?? 'Marketplace & Store' }}</h3>
                <p>{{ __('messages.store_desc') ?? 'Buy and sell digital products, scripts, and services securely. Find what you need to elevate your website.' }}</p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card scroll-reveal delay-1" onclick="window.location.href='{{ url('/directory') }}'" style="cursor: pointer;">
                <div class="feature-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <h3>{{ __('messages.directory') ?? 'Web Directory' }}</h3>
                <p>{{ __('messages.directory_desc') ?? 'Submit your website to our categorized directory to boost your SEO and get discovered by thousands of users.' }}</p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card scroll-reveal delay-2" onclick="window.location.href='{{ url('/forum') }}'" style="cursor: pointer;">
                <div class="feature-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3>{{ __('messages.forum') ?? 'Discussion Forums' }}</h3>
                <p>{{ __('messages.forum_desc') ?? 'Join specialized topics, share knowledge, and get support from experts and fellow website owners.' }}</p>
            </div>

            <!-- Feature 7 -->
            <div class="feature-card scroll-reveal delay-3" onclick="window.location.href='{{ route('seo_checker.index') }}'" style="cursor: pointer;">
                <div class="feature-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                </div>
                <h3>{{ __('messages.seo_checker') ?? 'Free SEO Checker' }}</h3>
                <p>{{ __('messages.seo_checker_desc') ?? 'Analyze your website\'s SEO performance, check backlinks, and get actionable insights to improve your rankings for free.' }}</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <div style="max-width: 1200px; margin: 0 auto 40px auto; padding: 0 24px; z-index: 10; position: relative;">
        @include('theme::partials.ads', ['id' => 6])
    </div>
    
    @include('theme::partials._standalone_footer')


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

            // Intersection Observer for Scroll Animations
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

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
            
            // Initial Icon State
            updateIcon(htmlTag.getAttribute('data-theme'));

            themeToggleBtn.addEventListener('click', () => {
                let currentTheme = htmlTag.getAttribute('data-theme');
                let newTheme = currentTheme === 'css_d' ? 'css' : 'css_d';
                
                htmlTag.setAttribute('data-theme', newTheme);
                document.cookie = "modedark=" + newTheme + ";path=/;max-age=31536000";
                
                updateIcon(newTheme);
            });

            // Close lang dropdown when clicking outside
            window.addEventListener('click', function(e) {
                if (!document.querySelector('.lang-switcher').contains(e.target)) {
                    document.getElementById('langDropdown').classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
