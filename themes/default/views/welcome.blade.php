@extends('theme::layouts.master')

@section('title', __('messages.welcome_title') . ' - ' . ($site_settings->titer ?? 'MyAds'))
@section('skip_footer_ad', '1')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* ===== Landing Page Styles ===== */

.landing-page {
    font-family: 'Inter', 'Comfortaa', sans-serif;
    overflow-x: hidden;
    width: 100%;
}

/* --- Hero Section --- */
.landing-hero {
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 100px 20px 80px;
    background: linear-gradient(135deg, #615dfa 0%, #8b5cf6 30%, #a855f7 60%, #c084fc 100%);
    overflow: hidden;
}
.landing-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 70% 30%, rgba(255,255,255,0.05) 0%, transparent 40%);
    animation: landing-float 20s ease-in-out infinite;
}
@keyframes landing-float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(30px, -30px) rotate(1deg); }
    66% { transform: translate(-20px, 20px) rotate(-1deg); }
}
.landing-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 120px;
    background: linear-gradient(to top, var(--landing-bg, #f5f5f5), transparent);
}
.landing-hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
}
.landing-hero h1 {
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
}
.landing-hero h1 span {
    display: block;
    background: linear-gradient(90deg, #fde68a, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.landing-hero p {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgba(255,255,255,0.88);
    max-width: 620px;
    margin: 0 auto 40px;
    line-height: 1.7;
    font-weight: 400;
}
.landing-hero-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}
.landing-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 36px;
    background: #fff;
    color: #615dfa;
    font-size: 1.05rem;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    border: none;
    cursor: pointer;
}
.landing-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    background: #f8f8ff;
}
.landing-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 36px;
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-size: 1.05rem;
    font-weight: 600;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(255,255,255,0.3);
    cursor: pointer;
    backdrop-filter: blur(10px);
}
.landing-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
}

/* Geometric Shapes */
.landing-shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.08;
    background: #fff;
    z-index: 1;
}
.landing-shape-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
.landing-shape-2 { width: 200px; height: 200px; bottom: 10%; left: -60px; }
.landing-shape-3 { width: 120px; height: 120px; top: 30%; right: 10%; border-radius: 30%; animation: landing-spin 30s linear infinite; }
@keyframes landing-spin { to { transform: rotate(360deg); } }

/* --- Section Common --- */
.landing-section {
    padding: 90px 20px;
    max-width: 1200px;
    margin: 0 auto;
}
.landing-section-header {
    text-align: center;
    margin-bottom: 60px;
}
.landing-section-header h2 {
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 800;
    color: #1a1a2e;
    margin-bottom: 16px;
    letter-spacing: -0.01em;
}
.landing-section-header p {
    font-size: 1.1rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* --- Features --- */
.landing-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 28px;
}
.landing-feature-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 32px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f0f0f5;
    position: relative;
    overflow: hidden;
}
.landing-feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #615dfa, #a855f7);
    opacity: 0;
    transition: opacity 0.3s;
}
.landing-feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 60px rgba(97, 93, 250, 0.12);
    border-color: transparent;
}
.landing-feature-card:hover::before {
    opacity: 1;
}
.landing-feature-icon {
    width: 72px;
    height: 72px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 28px;
    color: #fff;
}
.landing-feature-icon-banners { background: linear-gradient(135deg, #615dfa, #8b5cf6); }
.landing-feature-icon-textads { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
.landing-feature-icon-visits { background: linear-gradient(135deg, #10b981, #34d399); }
.landing-feature-card h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 12px;
}
.landing-feature-card p {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.7;
}

/* --- Stats --- */
.landing-stats {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 70px 20px;
    margin: 0;
    max-width: 100%;
}
.landing-stats-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.landing-stat-item {
    text-align: center;
    padding: 20px;
}
.landing-stat-number {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    color: #fff;
    display: block;
    line-height: 1.1;
    margin-bottom: 8px;
}
.landing-stat-number span {
    background: linear-gradient(90deg, #615dfa, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.landing-stat-label {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.55);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

/* --- How It Works --- */
.landing-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    counter-reset: step-counter;
}
.landing-step {
    text-align: center;
    position: relative;
    padding: 40px 24px;
}
.landing-step-number {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #615dfa, #a855f7);
    color: #fff;
    font-size: 1.5rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 8px 24px rgba(97, 93, 250, 0.3);
}
.landing-step h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 12px;
}
.landing-step p {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.6;
}
.landing-step-connector {
    display: none;
}
@media (min-width: 900px) {
    .landing-step-connector {
        display: block;
        position: absolute;
        top: 72px;
        right: -20px;
        width: 40px;
        height: 2px;
        background: linear-gradient(90deg, #615dfa, #a855f7);
        opacity: 0.3;
    }
    [dir="rtl"] .landing-step-connector {
        right: auto;
        left: -20px;
    }
}

/* --- Community --- */
.landing-community-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 28px;
}
.landing-community-card {
    background: linear-gradient(135deg, #f8f7ff 0%, #fff 100%);
    border-radius: 20px;
    padding: 36px 28px;
    border: 1px solid #ede9fe;
    transition: all 0.3s ease;
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.landing-community-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(97, 93, 250, 0.08);
}
.landing-community-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #615dfa, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
}
.landing-community-card h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 8px;
}
.landing-community-card p {
    font-size: 0.9rem;
    color: #6b7280;
    line-height: 1.6;
}

/* --- CTA Bottom --- */
.landing-cta {
    background: linear-gradient(135deg, #615dfa 0%, #8b5cf6 50%, #a855f7 100%);
    padding: 80px 20px;
    text-align: center;
    margin: 0;
    max-width: 100%;
    position: relative;
    overflow: hidden;
}
.landing-cta::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
.landing-cta h2 {
    font-size: clamp(1.6rem, 3.5vw, 2.5rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}
.landing-cta p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.85);
    max-width: 550px;
    margin: 0 auto 36px;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}
.landing-cta .landing-btn-primary {
    position: relative;
    z-index: 1;
    font-size: 1.1rem;
    padding: 18px 44px;
}

/* --- Footer --- */
.landing-footer {
    background: #1a1a2e;
    padding: 30px 20px;
    text-align: center;
}
.landing-footer p {
    color: rgba(255,255,255,0.4);
    font-size: 0.85rem;
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .landing-hero {
        min-height: 70vh;
        padding: 80px 16px 60px;
    }
    .landing-section {
        padding: 60px 16px;
    }
    .landing-stats-inner {
        grid-template-columns: repeat(2, 1fr);
    }
    .landing-stats {
        padding: 50px 16px;
    }
    .landing-features-grid {
        grid-template-columns: 1fr;
    }
    .landing-community-card {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
}

/* --- Dark Mode --- */
[data-theme="css_d"] .landing-section-header h2,
[data-theme="css_d"] .landing-feature-card h3,
[data-theme="css_d"] .landing-step h3,
[data-theme="css_d"] .landing-community-card h3 {
    color: #e2e8f0;
}
[data-theme="css_d"] .landing-section-header p,
[data-theme="css_d"] .landing-feature-card p,
[data-theme="css_d"] .landing-step p,
[data-theme="css_d"] .landing-community-card p {
    color: #94a3b8;
}
[data-theme="css_d"] .landing-feature-card {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="css_d"] .landing-community-card {
    background: linear-gradient(135deg, #1e293b, #0f172a);
    border-color: #334155;
}
[data-theme="css_d"] .landing-hero::after {
    background: linear-gradient(to top, #0f172a, transparent);
}
[data-theme="css_d"] .landing-page {
    --landing-bg: #0f172a;
}

/* --- RTL Support --- */
[dir="rtl"] .landing-community-card {
    text-align: right;
}
@media (max-width: 768px) {
    [dir="rtl"] .landing-community-card {
        text-align: center;
    }
}

/* Scroll Animations */
.landing-fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.landing-fade-up.landing-visible {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endpush

@section('content')
<div class="landing-page">

    {{-- ===== HERO SECTION ===== --}}
    <section class="landing-hero">
        <div class="landing-shape landing-shape-1"></div>
        <div class="landing-shape landing-shape-2"></div>
        <div class="landing-shape landing-shape-3"></div>
        <div class="landing-hero-content landing-fade-up">
            <h1>
                {{ __('messages.landing_hero_title') }}
                <span>{{ __('messages.landing_hero_title_highlight') }}</span>
            </h1>
            <p>{{ __('messages.landing_hero_subtitle') }}</p>
            <div class="landing-hero-buttons">
                <a href="{{ url('/register') }}" class="landing-btn-primary">
                    <i class="fa-solid fa-rocket"></i>
                    {{ __('messages.landing_hero_cta') }}
                </a>
                <a href="{{ route('login') }}" class="landing-btn-secondary">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    {{ __('messages.landing_hero_cta_login') }}
                </a>
            </div>
        </div>
    </section>

    @include('theme::partials.ads', ['id' => 1])

    {{-- ===== FEATURES SECTION ===== --}}
    <section class="landing-section">
        <div class="landing-section-header landing-fade-up">
            <h2>{{ __('messages.landing_features_title') }}</h2>
            <p>{{ __('messages.landing_features_subtitle') }}</p>
        </div>
        <div class="landing-features-grid">
            <div class="landing-feature-card landing-fade-up">
                <div class="landing-feature-icon landing-feature-icon-banners">
                    <i class="fa-solid fa-rectangle-ad"></i>
                </div>
                <h3>{{ __('messages.landing_feature_banners_title') }}</h3>
                <p>{{ __('messages.landing_feature_banners_desc') }}</p>
            </div>
            <div class="landing-feature-card landing-fade-up">
                <div class="landing-feature-icon landing-feature-icon-textads">
                    <i class="fa-solid fa-align-left"></i>
                </div>
                <h3>{{ __('messages.landing_feature_textads_title') }}</h3>
                <p>{{ __('messages.landing_feature_textads_desc') }}</p>
            </div>
            <div class="landing-feature-card landing-fade-up">
                <div class="landing-feature-icon landing-feature-icon-visits">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </div>
                <h3>{{ __('messages.landing_feature_visits_title') }}</h3>
                <p>{{ __('messages.landing_feature_visits_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- ===== STATS SECTION ===== --}}
    @php
        try {
            $totalUsers = \App\Models\User::count();
            $totalBanners = \Illuminate\Support\Facades\Schema::hasTable('banner') ? \DB::table('banner')->count() : 0;
            $totalLinks = \Illuminate\Support\Facades\Schema::hasTable('link') ? \DB::table('link')->count() : 0;
            $totalVisits = \Illuminate\Support\Facades\Schema::hasTable('visit') ? \DB::table('visit')->count() : 0;
            $totalAds = $totalBanners + $totalLinks + $totalVisits;
            $totalSites = \Illuminate\Support\Facades\Schema::hasTable('site') ? \DB::table('site')->count() : $totalVisits;
            $totalPoints = \App\Models\User::sum('pts');
        } catch (\Exception $e) {
            // Fallback if database is disconnected
            $totalUsers = 0;
            $totalAds = 0;
            $totalSites = 0;
            $totalPoints = 0;
        }
    @endphp
    <section class="landing-stats">
        <div class="landing-stats-inner">
            <div class="landing-stat-item landing-fade-up">
                <span class="landing-stat-number" data-count="{{ $totalUsers }}"><span>{{ number_format($totalUsers) }}</span></span>
                <span class="landing-stat-label">{{ __('messages.landing_stats_members') }}</span>
            </div>
            <div class="landing-stat-item landing-fade-up">
                <span class="landing-stat-number" data-count="{{ $totalSites }}"><span>{{ number_format($totalSites) }}</span></span>
                <span class="landing-stat-label">{{ __('messages.landing_stats_sites') }}</span>
            </div>
            <div class="landing-stat-item landing-fade-up">
                <span class="landing-stat-number" data-count="{{ $totalAds }}"><span>{{ number_format($totalAds) }}</span></span>
                <span class="landing-stat-label">{{ __('messages.landing_stats_ads') }}</span>
            </div>
            <div class="landing-stat-item landing-fade-up">
                <span class="landing-stat-number" data-count="{{ $totalPoints }}"><span>{{ number_format($totalPoints) }}</span></span>
                <span class="landing-stat-label">{{ __('messages.landing_stats_points') }}</span>
            </div>
        </div>
    </section>

    {{-- ===== HOW IT WORKS ===== --}}
    <section class="landing-section">
        <div class="landing-section-header landing-fade-up">
            <h2>{{ __('messages.landing_how_title') }}</h2>
        </div>
        <div class="landing-steps">
            <div class="landing-step landing-fade-up">
                <div class="landing-step-number">1</div>
                <h3>{{ __('messages.landing_how_step1_title') }}</h3>
                <p>{{ __('messages.landing_how_step1_desc') }}</p>
                <div class="landing-step-connector"></div>
            </div>
            <div class="landing-step landing-fade-up">
                <div class="landing-step-number">2</div>
                <h3>{{ __('messages.landing_how_step2_title') }}</h3>
                <p>{{ __('messages.landing_how_step2_desc') }}</p>
                <div class="landing-step-connector"></div>
            </div>
            <div class="landing-step landing-fade-up">
                <div class="landing-step-number">3</div>
                <h3>{{ __('messages.landing_how_step3_title') }}</h3>
                <p>{{ __('messages.landing_how_step3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- ===== COMMUNITY SECTION ===== --}}
    <section class="landing-section">
        <div class="landing-section-header landing-fade-up">
            <h2>{{ __('messages.landing_community_title') }}</h2>
            <p>{{ __('messages.landing_community_subtitle') }}</p>
        </div>
        <div class="landing-community-grid">
            <a href="{{ url('/forum') }}" class="landing-community-card landing-fade-up" style="text-decoration:none;">
                <div class="landing-community-icon">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div>
                    <h3>{{ __('messages.forum') }}</h3>
                    <p>{{ __('messages.landing_community_forum_desc') }}</p>
                </div>
            </a>
            <a href="{{ url('/directory') }}" class="landing-community-card landing-fade-up" style="text-decoration:none;">
                <div class="landing-community-icon" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <div>
                    <h3>{{ __('messages.directory') }}</h3>
                    <p>{{ __('messages.landing_community_directory_desc') }}</p>
                </div>
            </a>
            <a href="{{ url('/store') }}" class="landing-community-card landing-fade-up" style="text-decoration:none;">
                <div class="landing-community-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h3>{{ __('messages.store') }}</h3>
                    <p>{{ __('messages.landing_community_store_desc') }}</p>
                </div>
            </a>
        </div>
    </section>

    {{-- ===== FINAL CTA ===== --}}
    <section class="landing-cta">
        <div class="landing-fade-up">
            <h2>{{ __('messages.landing_cta_title') }}</h2>
            <p>{{ __('messages.landing_cta_subtitle') }}</p>
            <a href="{{ url('/register') }}" class="landing-btn-primary">
                <i class="fa-solid fa-user-plus"></i>
                {{ __('messages.landing_cta_button') }}
            </a>
        </div>
    </section>

    @include('theme::partials.ads', ['id' => 6])

    {{-- ===== FOOTER ===== --}}
    <div class="landing-footer">
        <p>&copy; {{ date('Y') }} {{ $site_settings->titer ?? 'MyAds' }}. {{ __('messages.all_rights_reserved') }}</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Intersection Observer for scroll animations
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('landing-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.landing-fade-up').forEach(function(el) {
        observer.observe(el);
    });

    // Animated counters
    document.querySelectorAll('.landing-stat-number[data-count]').forEach(function(el) {
        var target = parseInt(el.dataset.count) || 0;
        var innerSpan = el.querySelector('span');
        if (!innerSpan || target === 0) return;

        var started = false;
        var counterObserver = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && !started) {
                started = true;
                animateCounter(innerSpan, target);
                counterObserver.unobserve(el);
            }
        }, { threshold: 0.5 });
        counterObserver.observe(el);
    });

    function animateCounter(el, target) {
        var duration = 2000;
        var start = 0;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString();
            }
        }
        requestAnimationFrame(step);
    }
});
</script>
@endpush
