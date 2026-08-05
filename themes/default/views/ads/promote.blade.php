@extends('theme::layouts.master')

@section('content')
<div class="promotion-shell superdesign-post-container">
    @php
        $activeTab = request('p', 'all');
        $bannerSizes = \App\Support\BannerSizeCatalog::ordered();
    @endphp

    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero">
        <div class="superdesign-hero-header">
            <div class="superdesign-hero-title-wrap">
                <div class="superdesign-hero-icon-badge">
                    <i class="fa fa-bullhorn"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title">
                        {{ __('messages.promote_your_site') }}
                    </h1>
                    <p class="superdesign-hero-subtitle">
                        {{ __('messages.get_traffic') }}
                    </p>
                </div>
            </div>
            <div class="superdesign-hero-badges">
                <span class="superdesign-pill-badge">
                    <i class="fa fa-ad"></i>
                    {{ __('messages.ad_exchange') ?? 'تبادل الإعلانات' }}
                </span>
                <span class="superdesign-pill-badge">
                    <i class="fa fa-coins"></i>
                    {{ __('messages.pts_short') ?? 'PTS' }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border-radius: 14px; border: none; padding: 15px 25px;">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 14px; border: none;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa fa-exclamation-triangle me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid Architecture -->
    <div class="superdesign-post-grid">
        <!-- Sidebar Navigation & Tips -->
        <div class="superdesign-sidebar-col">
            <div class="superdesign-sidebar-card">
                <h3 class="superdesign-sidebar-title">
                    <i class="fa fa-layer-group"></i>
                    {{ __('messages.ad_types') ?? 'أنواع الإعلانات' }}
                </h3>
                <div class="superdesign-nav-list">
                    <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="superdesign-nav-item {{ $activeTab === 'banners' ? 'active' : '' }}">
                        <i class="fa fa-image"></i>
                        <span>{{ __('messages.bannads') }}</span>
                    </a>
                    <a href="{{ route('ads.promote', ['p' => 'link']) }}" class="superdesign-nav-item {{ $activeTab === 'link' ? 'active' : '' }}">
                        <i class="fa fa-font"></i>
                        <span>{{ __('messages.textads') }}</span>
                    </a>
                    <a href="{{ route('ads.promote', ['p' => 'exchange']) }}" class="superdesign-nav-item {{ $activeTab === 'exchange' ? 'active' : '' }}">
                        <i class="fa fa-exchange-alt"></i>
                        <span>{{ __('messages.exvisit') }}</span>
                    </a>
                    <div style="height: 1px; background: #f1f5f9; margin: 10px 0;"></div>
                    <a href="{{ route('ads.promote') }}" class="superdesign-nav-item {{ $activeTab === 'all' ? 'active' : '' }}">
                        <i class="fa fa-th-large"></i>
                        <span>{{ __('messages.all_methods') }}</span>
                    </a>
                </div>
            </div>

            <!-- Quick Tip Card -->
            <div class="superdesign-pts-tip">
                <div class="superdesign-pts-tip-title">
                    <i class="fa fa-lightbulb"></i>
                    <span>{{ __('messages.quick_tip') }}</span>
                </div>
                <span>
                    {{ __('messages.promote_tip_desc') }}
                </span>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="superdesign-main-content">
            
            <!-- BANNERS SECTION -->
            @if($activeTab === 'banners' || $activeTab === 'all')
                <div class="superdesign-composer-card" style="margin-bottom: 28px;">
                    <div class="superdesign-section-header">
                        <div class="superdesign-section-title">
                            <i class="fa fa-image text-primary"></i>
                            <h2>{{ __('messages.bannads') }}</h2>
                        </div>
                        <span class="superdesign-cost-badge">
                            -1 {{ __('messages.point') }}
                        </span>
                    </div>

                    <form method="post" action="{{ route('ads.banners.store') }}">
                        @csrf
                        <div class="superdesign-fields-row">
                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-heading"></i>
                                    {{ __('messages.name_ads') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        class="superdesign-input" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="{{ __('messages.name_ads_placeholder') }}"
                                    >
                                </div>
                            </div>

                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-link"></i>
                                    {{ __('messages.url_link') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="url" 
                                        name="url" 
                                        class="superdesign-input" 
                                        value="{{ old('url') }}" 
                                        required 
                                        placeholder="{{ __('messages.url_link_placeholder') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="superdesign-fields-row">
                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-ruler-combined"></i>
                                    {{ __('messages.banner_size') }}
                                </label>
                                <select name="px" class="superdesign-select" required>
                                    @foreach($bannerSizes as $size)
                                        <option value="{{ $size['value'] }}" {{ old('px') == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-image"></i>
                                    {{ __('messages.image_link') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="img" 
                                        class="superdesign-input" 
                                        value="{{ old('img') }}" 
                                        required 
                                        placeholder="{{ __('messages.image_link_placeholder') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="superdesign-actions-bar" style="margin-top: 14px; border-top: none; padding-top: 0;">
                            <div></div>
                            <button type="submit" class="superdesign-btn-primary">
                                <i class="fa fa-plus"></i>
                                {{ __('messages.add') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- TEXT ADS SECTION -->
            @if($activeTab === 'link' || $activeTab === 'all')
                <div class="superdesign-composer-card" style="margin-bottom: 28px;">
                    <div class="superdesign-section-header">
                        <div class="superdesign-section-title">
                            <i class="fa fa-font text-info"></i>
                            <h2>{{ __('messages.textads') }}</h2>
                        </div>
                        <span class="superdesign-cost-badge" style="background: rgba(35, 210, 226, 0.12); color: #23d2e2;">
                            -1 {{ __('messages.point') }}
                        </span>
                    </div>

                    <form method="post" action="{{ route('ads.links.store') }}">
                        @csrf
                        <div class="superdesign-fields-row">
                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-heading"></i>
                                    {{ __('messages.name_ads') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        class="superdesign-input" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="{{ __('messages.text_ads_placeholder') }}"
                                    >
                                </div>
                            </div>

                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-link"></i>
                                    {{ __('messages.url_link') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="url" 
                                        name="url" 
                                        class="superdesign-input" 
                                        value="{{ old('url') }}" 
                                        required 
                                        placeholder="{{ __('messages.url_link_placeholder') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="superdesign-field-group">
                            <label class="superdesign-field-label">
                                <i class="fa fa-align-left"></i>
                                {{ __('messages.text_p') }}
                            </label>
                            <div class="superdesign-input-wrapper">
                                <textarea 
                                    name="txt" 
                                    class="superdesign-textarea" 
                                    rows="4" 
                                    required 
                                    placeholder="{{ __('messages.was_desc') }}"
                                >{{ old('txt') }}</textarea>
                            </div>
                        </div>

                        <div class="superdesign-actions-bar" style="margin-top: 14px; border-top: none; padding-top: 0;">
                            <div></div>
                            <button type="submit" class="superdesign-btn-primary" style="background: linear-gradient(135deg, #23d2e2 0%, #00d2ff 100%);">
                                <i class="fa fa-plus"></i>
                                {{ __('messages.add') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- VISIT EXCHANGE SECTION -->
            @if($activeTab === 'exchange' || $activeTab === 'all')
                <div class="superdesign-composer-card">
                    <div class="superdesign-section-header">
                        <div class="superdesign-section-title">
                            <i class="fa fa-exchange-alt text-danger"></i>
                            <h2>{{ __('messages.exvisit') }}</h2>
                        </div>
                        <span class="superdesign-cost-badge" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                            {{ __('messages.dynamic_cost') }}
                        </span>
                    </div>

                    <form method="post" action="{{ route('visits.store') }}">
                        @csrf
                        <div class="superdesign-fields-row">
                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-heading"></i>
                                    {{ __('messages.name_ads') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        class="superdesign-input" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="{{ __('messages.exchange_name_placeholder') }}"
                                    >
                                </div>
                            </div>

                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-link"></i>
                                    {{ __('messages.url_link') }}
                                </label>
                                <div class="superdesign-input-wrapper">
                                    <input 
                                        type="url" 
                                        name="url" 
                                        class="superdesign-input" 
                                        value="{{ old('url') }}" 
                                        required 
                                        placeholder="{{ __('messages.url_link_placeholder') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="superdesign-fields-row">
                            <div class="superdesign-field-group">
                                <label class="superdesign-field-label">
                                    <i class="fa fa-clock"></i>
                                    {{ __('messages.visits_time') }}
                                </label>
                                <select name="tims" class="superdesign-select" required>
                                    <option value="1" {{ old('tims') == '1' ? 'selected' : '' }}>10s / -1 {{ __('messages.pts_short') }}</option>
                                    <option value="2" {{ old('tims') == '2' ? 'selected' : '' }}>20s / -2 {{ __('messages.pts_short') }}</option>
                                    <option value="3" {{ old('tims') == '3' ? 'selected' : '' }}>30s / -5 {{ __('messages.pts_short') }}</option>
                                    <option value="4" {{ old('tims') == '4' ? 'selected' : '' }}>60s / -10 {{ __('messages.pts_short') }}</option>
                                </select>
                            </div>

                            <div class="superdesign-field-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="superdesign-btn-primary" style="width: 100%; background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);">
                                    <i class="fa fa-plus"></i>
                                    {{ __('messages.add') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* --- @.superdesign Core Tokens for /ads/promote --- */
.superdesign-post-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 15px 60px;
}

/* Glassmorphic Hero Banner */
.superdesign-post-hero {
    position: relative;
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.14) 0%, rgba(35, 210, 226, 0.09) 50%, rgba(27, 200, 219, 0.03) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 28px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.superdesign-post-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(97, 93, 250, 0.25) 0%, rgba(97, 93, 250, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.superdesign-hero-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.superdesign-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.superdesign-hero-icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.35);
    flex-shrink: 0;
}
.superdesign-hero-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px 0;
    color: #1e293b;
    letter-spacing: -0.02em;
}
.superdesign-hero-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}
.superdesign-hero-badges {
    display: flex;
    align-items: center;
    gap: 10px;
}
.superdesign-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(97, 93, 250, 0.25);
    color: #615dfa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

/* Grid Architecture */
.superdesign-post-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 28px;
}
@media (max-width: 991px) {
    .superdesign-post-grid {
        grid-template-columns: 1fr;
    }
}

/* Main Composer Card */
.superdesign-composer-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 28px;
}

.superdesign-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}
.superdesign-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.superdesign-section-title h2 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: #1e293b;
}
.superdesign-cost-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 700;
    background: rgba(97, 93, 250, 0.12);
    color: #615dfa;
}

.superdesign-fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .superdesign-fields-row {
        grid-template-columns: 1fr;
    }
}

.superdesign-field-group {
    margin-bottom: 20px;
}
.superdesign-field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}
.superdesign-field-label i {
    color: #615dfa;
}
.superdesign-input-wrapper {
    position: relative;
}
.superdesign-input {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
}
.superdesign-input:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px 16px;
    font-size: 14.5px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
    resize: vertical;
}
.superdesign-textarea:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-select {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 14.5px;
    font-weight: 600;
    color: #334155;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%20615dfa' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat calc(100% - 16px) center;
    background-size: 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    cursor: pointer;
    transition: all 0.25s ease;
}
html[dir="rtl"] .superdesign-select {
    background-position: 16px center;
}
.superdesign-select:focus {
    background-color: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}

/* Sidebar Navigation Items */
.superdesign-nav-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.superdesign-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s ease;
}
.superdesign-nav-item:hover {
    background: #f1f5f9;
    color: #615dfa;
}
.superdesign-nav-item.active {
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    color: #ffffff !important;
    box-shadow: 0 6px 16px rgba(97, 93, 250, 0.3);
}

/* Buttons */
.superdesign-actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.superdesign-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    border: none;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.3);
    cursor: pointer;
    transition: all 0.25s ease;
}
.superdesign-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(97, 93, 250, 0.4);
    color: #ffffff;
}

/* Sidebar Widgets */
.superdesign-sidebar-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 22px;
    margin-bottom: 20px;
}
.superdesign-sidebar-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.superdesign-sidebar-title i {
    color: #615dfa;
}
.superdesign-pts-tip {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.08) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 16px;
    padding: 16px;
    font-size: 13px;
    color: #475569;
    line-height: 1.5;
}
.superdesign-pts-tip-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    color: #615dfa;
    margin-bottom: 6px;
}

/* --- Dark Mode Parity --- */
body[data-theme="css_d"] .superdesign-post-hero,
html.app-skin-dark .superdesign-post-hero,
.dark-mode .superdesign-post-hero {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.1) 100%), #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
}
body[data-theme="css_d"] .superdesign-hero-title,
html.app-skin-dark .superdesign-hero-title,
.dark-mode .superdesign-hero-title {
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-hero-subtitle,
html.app-skin-dark .superdesign-hero-subtitle,
.dark-mode .superdesign-hero-subtitle {
    color: #94a3b8;
}
body[data-theme="css_d"] .superdesign-pill-badge,
html.app-skin-dark .superdesign-pill-badge,
.dark-mode .superdesign-pill-badge {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(97, 93, 250, 0.35);
    color: #818cf8;
}

body[data-theme="css_d"] .superdesign-composer-card,
body[data-theme="css_d"] .superdesign-sidebar-card,
html.app-skin-dark .superdesign-composer-card,
html.app-skin-dark .superdesign-sidebar-card,
.dark-mode .superdesign-composer-card,
.dark-mode .superdesign-sidebar-card {
    background: #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
body[data-theme="css_d"] .superdesign-section-title h2,
body[data-theme="css_d"] .superdesign-field-label,
body[data-theme="css_d"] .superdesign-sidebar-title,
html.app-skin-dark .superdesign-section-title h2,
html.app-skin-dark .superdesign-field-label,
html.app-skin-dark .superdesign-sidebar-title,
.dark-mode .superdesign-section-title h2,
.dark-mode .superdesign-field-label,
.dark-mode .superdesign-sidebar-title {
    color: #f1f5f9;
}
body[data-theme="css_d"] .superdesign-input,
body[data-theme="css_d"] .superdesign-textarea,
body[data-theme="css_d"] .superdesign-select,
html.app-skin-dark .superdesign-input,
html.app-skin-dark .superdesign-textarea,
html.app-skin-dark .superdesign-select,
.dark-mode .superdesign-input,
.dark-mode .superdesign-textarea,
.dark-mode .superdesign-select {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-input:focus,
body[data-theme="css_d"] .superdesign-textarea:focus,
body[data-theme="css_d"] .superdesign-select:focus,
html.app-skin-dark .superdesign-input:focus,
html.app-skin-dark .superdesign-textarea:focus,
html.app-skin-dark .superdesign-select:focus,
.dark-mode .superdesign-input:focus,
.dark-mode .superdesign-textarea:focus,
.dark-mode .superdesign-select:focus {
    background-color: #1e293b;
    border-color: #615dfa;
}
body[data-theme="css_d"] .superdesign-nav-item,
html.app-skin-dark .superdesign-nav-item,
.dark-mode .superdesign-nav-item {
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-nav-item:hover,
html.app-skin-dark .superdesign-nav-item:hover,
.dark-mode .superdesign-nav-item:hover {
    background: #334155;
    color: #615dfa;
}
body[data-theme="css_d"] .superdesign-pts-tip,
html.app-skin-dark .superdesign-pts-tip,
.dark-mode .superdesign-pts-tip {
    background: rgba(97, 93, 250, 0.12);
    border-color: rgba(97, 93, 250, 0.3);
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-section-header,
html.app-skin-dark .superdesign-section-header,
.dark-mode .superdesign-section-header {
    border-bottom-color: #334155;
}
</style>
@endsection
