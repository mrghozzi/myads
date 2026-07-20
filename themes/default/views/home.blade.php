@extends('theme::layouts.master')

@section('content')
<style>
    :root {
        --myads-primary: #615dfa;
        --myads-primary-hover: #4e4ac8;
        --myads-accent: #23d2e2;
        --myads-green: #4ff461;
        --myads-amber: #fbbf24;
        --myads-dark: #0f172a;
        --myads-darker: #0b1120;
        --myads-surface-light: #ffffff;
        --myads-surface-dark: #1e293b;
        --myads-text-light: #334155;
        --myads-text-dark: #f8fafc;
        --myads-text-muted: #64748b;
    }

    body.dark-mode {
        --surface-bg: var(--myads-surface-dark);
        --text-color: var(--myads-text-dark);
        --border-color: rgba(255,255,255,0.1);
        --card-bg: rgba(30, 41, 59, 0.8);
    }
    
    body:not(.dark-mode) {
        --surface-bg: var(--myads-surface-light);
        --text-color: var(--myads-text-light);
        --border-color: rgba(0,0,0,0.05);
        --card-bg: #ffffff;
    }

    .modern-dashboard {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Welcome Banner */
    .modern-banner {
        background: linear-gradient(135deg, var(--myads-primary) 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 32px 24px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(97, 93, 250, 0.2);
        position: relative;
        overflow: hidden;
    }

    .modern-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .modern-banner-icon {
        font-size: 48px;
        background: rgba(255,255,255,0.2);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        backdrop-filter: blur(10px);
    }

    .modern-banner-content h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .modern-banner-content p {
        margin: 4px 0 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Stats Grid */
    .modern-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .modern-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .modern-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    .modern-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .modern-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .icon-violet { background: rgba(97, 93, 250, 0.1); color: var(--myads-primary); }
    .icon-cyan { background: rgba(35, 210, 226, 0.1); color: var(--myads-accent); }
    .icon-green { background: rgba(79, 244, 97, 0.1); color: var(--myads-green); }
    .icon-amber { background: rgba(251, 191, 36, 0.1); color: var(--myads-amber); }

    .modern-stat-title {
        font-size: 14px;
        color: var(--text-color);
        font-weight: 600;
        margin: 0;
        opacity: 0.8;
    }

    .modern-stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-color);
        margin: 0;
        line-height: 1;
    }

    .modern-stat-desc {
        font-size: 13px;
        color: var(--myads-text-muted);
        margin: 8px 0 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modern-stat-link {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    /* Marketing Blocks */
    .modern-service-block {
        border-radius: 20px;
        padding: 32px;
        color: #fff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .modern-service-block .service-bg-shape {
        position: absolute;
        inset: auto -50px -50px auto;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        z-index: 0;
    }

    .service-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: space-between;
    }

    .service-info {
        max-width: 600px;
    }

    .service-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.15);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        backdrop-filter: blur(4px);
    }

    .service-title {
        margin: 16px 0 12px;
        font-size: 28px;
        font-weight: 800;
        line-height: 1.2;
    }

    .service-desc {
        margin: 0;
        font-size: 15px;
        opacity: 0.9;
        line-height: 1.6;
    }

    .service-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
        margin-top: auto;
    }

    .btn-glass {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(8px);
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-glass:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-solid {
        background: #fff;
        color: var(--block-color, #333);
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }
    
    .btn-solid:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .service-stats {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .service-stat-box {
        background: rgba(255,255,255,0.1);
        padding: 16px 20px;
        border-radius: 16px;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.1);
        min-width: 140px;
    }

    .service-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        opacity: 0.8;
        margin: 0 0 4px;
        font-weight: 600;
    }

    .service-stat-value {
        font-size: 24px;
        font-weight: 800;
        margin: 0;
    }

    /* Blocks Variations */
    .block-smart { background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); --block-color: #2563eb; }
    .block-youtube { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); --block-color: #dc2626; }
    .block-custom { background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); --block-color: #6366f1; }
    .block-seo { background: linear-gradient(135deg, #10b981 0%, #059669 100%); --block-color: #059669; }

    /* Widgets Grid */
    .modern-widgets-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    @media (min-width: 992px) {
        .modern-widgets-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .modern-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    .modern-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-color);
        margin: 0 0 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .modern-card-title i {
        color: var(--myads-primary);
    }

    .modern-form-group {
        margin-bottom: 16px;
    }

    .modern-form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 8px;
    }

    .modern-input, .modern-select {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-color);
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .modern-input:focus, .modern-select:focus {
        outline: none;
        border-color: var(--myads-primary);
    }

    .btn-primary-action {
        background: var(--myads-primary);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary-action:hover {
        background: var(--myads-primary-hover);
    }
</style>

<div class="modern-dashboard">
    <!-- ALERTS -->
    @if(session('errMSG'))
        <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ __('messages.warning') }}</strong> {{ session('errMSG') }}
        </div>
    @endif
    @if(session('MSG'))
        <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 12px;">
            {{ session('MSG') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- WELCOME BANNER -->
    <div class="modern-banner">
        <div class="modern-banner-icon">
            <i class="fa-solid fa-chart-pie"></i>
        </div>
        <div class="modern-banner-content">
            <h1>{{ __('messages.board') }}</h1>
            <p>{{ __('messages.welcome_dashboard_desc') ?? 'Manage your campaigns, track statistics, and convert your points directly from your dashboard.' }}</p>
        </div>
    </div>

    <!-- MAIN STATS GRID -->
    <div class="modern-stats-grid">
        <!-- Banner Ads -->
        <div class="modern-stat-card">
            <a href="{{ url('/b_list') }}" class="modern-stat-link"></a>
            <div class="modern-stat-header">
                <p class="modern-stat-title">{{ __('messages.bannads') }}</p>
                <div class="modern-stat-icon icon-violet"><i class="fa-solid fa-image"></i></div>
            </div>
            <p class="modern-stat-value">{{ $bannerStats['vu'] }}</p>
            <p class="modern-stat-desc">
                <i class="fa-solid fa-eye" style="color: var(--myads-primary)"></i>
                {{ __('messages.Views') }} ({{ $user->nvu }} {{ __('messages.available') ?? 'Available' }})
            </p>
        </div>

        <!-- Text Ads -->
        <div class="modern-stat-card">
            <a href="{{ url('/l_list') }}" class="modern-stat-link"></a>
            <div class="modern-stat-header">
                <p class="modern-stat-title">{{ __('messages.textads') }}</p>
                <div class="modern-stat-icon icon-cyan"><i class="fa-solid fa-link"></i></div>
            </div>
            <p class="modern-stat-value">{{ $linkStats['clik'] }}</p>
            <p class="modern-stat-desc">
                <i class="fa-solid fa-hand-pointer" style="color: var(--myads-accent)"></i>
                {{ __('messages.Click') }} ({{ $user->nlink }} {{ __('messages.available') ?? 'Available' }})
            </p>
        </div>

        <!-- Visits Exchange -->
        <div class="modern-stat-card">
            <a href="{{ url('/v_list') }}" class="modern-stat-link"></a>
            <div class="modern-stat-header">
                <p class="modern-stat-title">{{ __('messages.exvisit') }}</p>
                <div class="modern-stat-icon icon-green"><i class="fa-solid fa-arrows-rotate"></i></div>
            </div>
            <p class="modern-stat-value">{{ $visitStats['vu'] }}</p>
            <p class="modern-stat-desc">
                <i class="fa-solid fa-users" style="color: var(--myads-green)"></i>
                {{ __('messages.visits') }} ({{ $user->vu }} {{ __('messages.available') ?? 'Available' }})
            </p>
        </div>

        <!-- Smart Ads -->
        <div class="modern-stat-card">
            <a href="{{ route('ads.smart.index') }}" class="modern-stat-link"></a>
            <div class="modern-stat-header">
                <p class="modern-stat-title">{{ __('messages.smart_ads') }}</p>
                <div class="modern-stat-icon icon-amber"><i class="fa-solid fa-bolt"></i></div>
            </div>
            <p class="modern-stat-value">{{ $smartAdStats['impressions'] }}</p>
            <p class="modern-stat-desc">
                <i class="fa-solid fa-eye" style="color: var(--myads-amber)"></i>
                {{ __('messages.smart_impressions') }}
            </p>
        </div>
    </div>

    {!! ads_site(2) !!}

    <!-- PROMOTIONAL BLOCKS -->
    
    <!-- Smart Ads Block -->
    <div class="modern-service-block block-smart">
        <div class="service-bg-shape"></div>
        <div class="service-content">
            <div class="service-info">
                <div class="service-badge"><i class="fa-solid fa-bolt"></i> {{ __('messages.smart_ads') }}</div>
                <h2 class="service-title">{{ __('messages.smart_ads_campaign_pitch') }}</h2>
                <p class="service-desc">{{ __('messages.smart_targeting_intro') }}</p>
            </div>
            <div class="service-stats">
                <div class="service-stat-box">
                    <p class="service-stat-label">{{ __('messages.smart_admin_balance') }}</p>
                    <p class="service-stat-value">{{ number_format((float) $user->nsmart, 2) }}</p>
                </div>
                <div class="service-stat-box">
                    <p class="service-stat-label">{{ __('messages.smart_clicks_label') }}</p>
                    <p class="service-stat-value">{{ $smartAdStats['clicks'] }}</p>
                </div>
            </div>
        </div>
        <div class="service-actions">
            <a href="{{ route('ads.smart.index') }}" class="btn-glass"><i class="fa-solid fa-list"></i> {{ __('messages.smart_list_ads') }}</a>
            <a href="{{ route('ads.smart.create') }}" class="btn-solid"><i class="fa-solid fa-plus"></i> {{ __('messages.smart_create_ad') }}</a>
            <a href="{{ route('legacy.state', ['ty' => 'smart', 'st' => 'vu']) }}" class="btn-glass"><i class="fa-solid fa-chart-line"></i> {{ __('messages.stats') }}</a>
        </div>
    </div>

    <!-- Custom Ads Block -->
    <div class="modern-service-block block-custom">
        <div class="service-bg-shape"></div>
        <div class="service-content">
            <div class="service-info">
                <div class="service-badge"><i class="fa-solid fa-bullseye"></i> {{ __('messages.custom_ads') }}</div>
                <h2 class="service-title">{{ __('messages.custom_ads_title') }}</h2>
                <p class="service-desc">{{ __('messages.custom_ads_desc') }}</p>
            </div>
        </div>
        <div class="service-actions">
            <a href="{{ url('/ads/custom') }}" class="btn-solid"><i class="fa-solid fa-gauge"></i> {{ __('messages.custom_ads_dashboard') }}</a>
            <a href="{{ url('/ads/custom/placements/create') }}" class="btn-glass"><i class="fa-solid fa-plus"></i> {{ __('messages.custom_ads_create') }}</a>
            <a href="{{ url('/ads/custom/marketplace') }}" class="btn-glass"><i class="fa-solid fa-shop"></i> {{ __('messages.custom_ads_marketplace') }}</a>
        </div>
    </div>

    <!-- YouTube Exchange Block -->
    <div class="modern-service-block block-youtube">
        <div class="service-bg-shape"></div>
        <div class="service-content">
            <div class="service-info">
                <div class="service-badge"><i class="fa-brands fa-youtube"></i> {{ __('messages.yt_exchange') }} <span style="background: #fff; color: #dc2626; padding: 2px 6px; border-radius: 4px; font-size: 9px; margin-left: 4px;">BETA</span></div>
                <h2 class="service-title">{{ __('messages.yt_views_exchange') }}</h2>
                <p class="service-desc">{{ __('messages.yt_exchange_desc') }}</p>
            </div>
            <div class="service-stats">
                <div class="service-stat-box">
                    <p class="service-stat-label">Available Points</p>
                    <p class="service-stat-value">{{ number_format((float) $user->pts, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="service-actions">
            <a href="{{ route('youtube.exchange.index') }}" class="btn-glass"><i class="fa-solid fa-play"></i> {{ __('messages.yt_watch_earn_btn') }}</a>
            <a href="{{ route('youtube.advertiser.index') }}" class="btn-solid"><i class="fa-solid fa-plus"></i> {{ __('messages.yt_add_campaign') }}</a>
        </div>
    </div>

    <!-- SEO Checker Block -->
    <div class="modern-service-block block-seo">
        <div class="service-bg-shape"></div>
        <div class="service-content">
            <div class="service-info">
                <div class="service-badge"><i class="fa-solid fa-magnifying-glass-chart"></i> {{ __('messages.seo_checker') }} <span style="background: #fff; color: #059669; padding: 2px 6px; border-radius: 4px; font-size: 9px; margin-left: 4px;">FREE</span></div>
                <h2 class="service-title">{{ __('messages.seo_checker') }}</h2>
                <p class="service-desc">{{ __('messages.seo_checker_desc') }}</p>
            </div>
            <div class="service-stats" style="flex-grow: 1; max-width: 400px; display: flex; align-items: center;">
                <form action="{{ route('seo_checker.analyze') }}" method="POST" style="display: flex; gap: 8px; width: 100%;">
                    @csrf
                    <input type="url" name="url" placeholder="https://example.com" required style="flex-grow: 1; padding: 12px 16px; border-radius: 12px; border: none; outline: none; font-size: 14px; color: #333;">
                    <button type="submit" class="btn-solid" style="color: #059669">{{ __('messages.seo_analyze_now') }}</button>
                </form>
            </div>
        </div>
        <div class="service-actions">
            <a href="{{ route('seo_checker.index') }}" class="btn-glass"><i class="fa-solid fa-chart-line"></i> {{ __('messages.seo_checker') }}</a>
        </div>
    </div>

    <!-- POINTS & FINANCIAL WIDGETS -->
    <div class="modern-widgets-grid">
        
        <!-- Points Conversion -->
        <div class="modern-card">
            <h3 class="modern-card-title"><i class="fa-solid fa-coins"></i> {{ __('messages.Convertpoint') }}</h3>
            <p style="font-size: 14px; color: var(--myads-text-muted); margin-bottom: 20px;">
                {{ __('messages.Totalpoints') }} <strong>{{ number_format($user->pts, 2) }} PTS</strong>
            </p>
            
            <form action="{{ url('/home') }}" method="POST">
                @csrf
                <div class="modern-form-group">
                    <label for="Points">{{ __('messages.Points') }}</label>
                    <input type="number" id="Points" name="pts" class="modern-input" required placeholder="0.00">
                </div>
                <div class="modern-form-group">
                    <label for="profile-social-stream-schedule-monday">{{ __('messages.to') }}</label>
                    <select id="profile-social-stream-schedule-monday" name="to" class="modern-select">
                        <option value="link">{{ __('messages.tostads') }}</option>
                        <option value="banners">{{ __('messages.towthbaner') }}</option>
                        <option value="exchv">{{ __('messages.toexchvisi') }}</option>
                        <option value="smartads">{{ __('messages.smart_convert_option') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary-action" name="bt_pts" value="bt_pts">
                    <i class="fa-solid fa-exchange-alt"></i> {{ __('messages.Conversion') }}
                </button>
            </form>
        </div>

        <!-- Transfer Points -->
        <div class="modern-card">
            <h3 class="modern-card-title"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.transfer_pts') }}</h3>
            <form action="{{ route('dashboard.pts.transfer') }}" method="POST">
                @csrf
                <div class="modern-form-group">
                    <label for="transfer_username">{{ __('messages.username') }}</label>
                    <input type="text" id="transfer_username" name="username" class="modern-input" required placeholder="User_Name">
                </div>
                <div class="modern-form-group">
                    <label for="transfer_amount">{{ __('messages.Points') }}</label>
                    <input type="number" id="transfer_amount" name="amount" min="1" step="0.01" class="modern-input" required placeholder="0.00">
                </div>
                <button type="submit" class="btn-primary-action">
                    <i class="fa-solid fa-paper-plane"></i> {{ __('messages.send') }}
                </button>
            </form>
        </div>

        <!-- Generate Voucher -->
        <div class="modern-card">
            <h3 class="modern-card-title"><i class="fa-solid fa-ticket"></i> {{ __('messages.generate_voucher') }}</h3>
            <form action="{{ route('dashboard.pts.voucher.generate') }}" method="POST">
                @csrf
                <div class="modern-form-group">
                    <label for="voucher_amount">{{ __('messages.Points') }}</label>
                    <div style="display: flex; gap: 12px;">
                        <input type="number" id="voucher_amount" name="amount" min="1" step="0.01" class="modern-input" required placeholder="0.00" style="flex: 1;">
                        <button type="submit" class="btn-primary-action" style="width: auto;">{{ __('messages.generate') }}</button>
                    </div>
                </div>
            </form>
            
            @if(isset($vouchers) && $vouchers->count() > 0)
            <div style="margin-top: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: var(--text-color);">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 10px; text-align: start;">{{ __('messages.code') }}</th>
                            <th style="padding: 10px; text-align: start;">{{ __('messages.amount') }}</th>
                            <th style="padding: 10px; text-align: start;">{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vouchers->take(3) as $voucher)
                        <tr style="border-bottom: 1px dashed var(--border-color);">
                            <td style="padding: 10px;"><code>{{ $voucher->code }}</code></td>
                            <td style="padding: 10px; font-weight: 600;">{{ $voucher->amount }}</td>
                            <td style="padding: 10px;">
                                @if($voucher->is_used)
                                    <span style="color: var(--myads-text-muted);"><i class="fa-solid fa-check"></i> {{ __('messages.used') }}</span>
                                @else
                                    <span style="color: var(--myads-green);"><i class="fa-solid fa-circle-dot"></i> {{ __('messages.unused') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Claim Voucher -->
        <div class="modern-card">
            <h3 class="modern-card-title"><i class="fa-solid fa-hand-holding-dollar"></i> {{ __('messages.claim_voucher') }}</h3>
            <form action="{{ route('dashboard.pts.voucher.claim') }}" method="POST">
                @csrf
                <div class="modern-form-group">
                    <label for="claim_code">{{ __('messages.code') }}</label>
                    <input type="text" id="claim_code" name="code" class="modern-input" required placeholder="XXXX-XXXX-XXXX">
                </div>
                <button type="submit" class="btn-primary-action" style="background: var(--myads-accent); color: var(--myads-dark);">
                    <i class="fa-solid fa-check-circle"></i> {{ __('messages.claim') }}
                </button>
            </form>
            
            <!-- Quick Links -->
            <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ url('/referral') }}" class="btn-glass" style="background: rgba(128,128,128,0.1); color: var(--text-color); border-color: var(--border-color); font-size: 13px; padding: 8px 16px;">
                    <i class="fa-solid fa-list"></i> {{ __('messages.list') }} {{ __('messages.referal') }}
                </a>
                <a href="{{ url('/r_code') }}" class="btn-glass" style="background: rgba(128,128,128,0.1); color: var(--text-color); border-color: var(--border-color); font-size: 13px; padding: 8px 16px;">
                    <i class="fa-solid fa-users"></i> {{ __('messages.referal') }}
                </a>
            </div>
        </div>

    </div>
    
    {!! ads_site(2) !!}
</div>
@endsection
