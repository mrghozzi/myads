@extends('theme::layouts.master')

@section('content')
@php
    $refUrl = url('/') . '?ref=' . $user->id;
    $siteTitle = $site_settings->titer ?? config('app.name');
    $banner728 = theme_asset('img/banner/728x90.gif');
    $banner300 = theme_asset('img/banner/300x250.gif');
    $banner160 = theme_asset('img/banner/160x600.gif');
    $banner468 = theme_asset('img/banner/468x60.gif');
    $code728 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner728}\" width=\"728\" height=\"90\" alt=\"{$siteTitle}\"></a><!-- ADStn code end -->";
    $code300 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner300}\" width=\"300\" height=\"250\" alt=\"{$siteTitle}\"></a><!-- ADStn code end -->";
    $code160 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner160}\" width=\"160\" height=\"600\" alt=\"{$siteTitle}\"></a><!-- ADStn code end -->";
    $code468 = "<!-- ADStn code begin --><a href=\"{$refUrl}\"><img src=\"{$banner468}\" width=\"468\" height=\"60\" alt=\"{$siteTitle}\"></a><!-- ADStn code end -->";
@endphp

<div class="promotion-shell superdesign-post-container">

    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero" style="background: linear-gradient(135deg, rgba(52, 84, 209, 0.15) 0%, rgba(97, 93, 250, 0.1) 100%); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; margin-bottom: 24px; backdrop-filter: blur(10px);">
        <div class="superdesign-hero-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div class="superdesign-hero-title-wrap" style="display: flex; align-items: center; gap: 16px;">
                <div class="superdesign-hero-icon-badge" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #3454d1, #615dfa); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 8px 20px rgba(52, 84, 209, 0.3);">
                    <i class="fa-solid fa-share-nodes"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title" style="font-size: 24px; font-weight: 800; margin: 0; line-height: 1.2;">
                        {{ __('messages.referral_dashboard') }}
                    </h1>
                    <p class="superdesign-hero-subtitle" style="margin: 4px 0 0 0; opacity: 0.8; font-size: 14px;">
                        {{ __('messages.referral_description_hero') }}
                    </p>
                </div>
            </div>
            
            <div class="superdesign-hero-badges" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <div class="stat-badge" style="background: var(--admin-premium-surface, rgba(255,255,255,0.08)); border: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding: 8px 16px; border-radius: 14px; text-align: center;">
                    <span style="display: block; font-size: 11px; opacity: 0.7; text-transform: uppercase;">{{ __('messages.total_referrals') }}</span>
                    <strong style="font-size: 16px; color: #17c666;">{{ number_format($totalReferrals) }}</strong>
                </div>
                <div class="stat-badge" style="background: var(--admin-premium-surface, rgba(255,255,255,0.08)); border: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding: 8px 16px; border-radius: 14px; text-align: center;">
                    <span style="display: block; font-size: 11px; opacity: 0.7; text-transform: uppercase;">{{ __('messages.points_earned_referral') }}</span>
                    <strong style="font-size: 16px; color: #ffa21d;">{{ number_format($totalEarnedPts) }} PTS</strong>
                </div>
                <span class="superdesign-pill-badge" style="background: rgba(23, 198, 102, 0.15); color: #17c666; border: 1px solid rgba(23, 198, 102, 0.3); padding: 8px 14px; border-radius: 20px; font-weight: 700; font-size: 12px;">
                    <i class="fa-solid fa-coins me-1"></i> {{ __('messages.pts_per_referral') }}
                </span>
            </div>
        </div>
        
        <!-- Navigation Tab Switcher -->
        <div style="margin-top: 24px; display: flex; gap: 10px; border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding-bottom: 12px;">
            <a href="{{ route('ads.referrals') }}" class="ref-tab-link active" style="padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 14px; background: #3454d1; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(52, 84, 209, 0.3);">
                <i class="fa-solid fa-code"></i> {{ __('messages.referral_banners_codes') }}
            </a>
            <a href="{{ route('legacy.referral') }}" class="ref-tab-link" style="padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; background: rgba(128,128,128,0.1); color: var(--text-color); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users"></i> {{ __('messages.my_referrals_list') }} ({{ $totalReferrals }})
            </a>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px;">
        
        <!-- Left Main Content Column: Banner & Embed Code Selector -->
        <div class="superdesign-main-col">
            <div class="modern-card" style="background: var(--admin-premium-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)); border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                
                <h3 style="font-size: 18px; font-weight: 700; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-rectangle-ad" style="color: #3454d1;"></i>
                    {{ __('messages.referral_banners_codes') }}
                </h3>

                <!-- Banner Size Tabs -->
                <div class="banner-tabs-nav" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; background: rgba(128,128,128,0.06); padding: 6px; border-radius: 14px;">
                    <button type="button" class="b-tab-btn active" data-target="b728" style="flex: 1; min-width: 90px; padding: 8px 12px; border: none; border-radius: 10px; background: #3454d1; color: #fff; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                        728x90
                    </button>
                    <button type="button" class="b-tab-btn" data-target="b300" style="flex: 1; min-width: 90px; padding: 8px 12px; border: none; border-radius: 10px; background: transparent; color: var(--text-color); font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                        300x250
                    </button>
                    <button type="button" class="b-tab-btn" data-target="b160" style="flex: 1; min-width: 90px; padding: 8px 12px; border: none; border-radius: 10px; background: transparent; color: var(--text-color); font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                        160x600
                    </button>
                    <button type="button" class="b-tab-btn" data-target="b468" style="flex: 1; min-width: 90px; padding: 8px 12px; border: none; border-radius: 10px; background: transparent; color: var(--text-color); font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s;">
                        468x60
                    </button>
                </div>

                <!-- Tab Content 728x90 -->
                <div class="banner-tab-pane active" id="b728">
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block; opacity: 0.8;">
                            <i class="fa-solid fa-code me-1"></i> {{ __('messages.sponsorship_tag') }} (728x90)
                        </label>
                        <div style="position: relative;">
                            <textarea id="code728Text" readonly class="form-control" style="width: 100%; height: 90px; font-family: monospace; font-size: 12px; border-radius: 12px; padding: 12px; border: 1px solid var(--border-color, #ddd); background: var(--input-bg, rgba(0,0,0,0.02)); color: var(--text-color); resize: none;">{{ $code728 }}@if($extensions_code)

{{ $extensions_code }}@endif</textarea>
                            <button type="button" class="btn-copy-code" data-clipboard-target="#code728Text" style="position: absolute; top: 8px; {{ is_locale_rtl() ? 'left: 8px;' : 'right: 8px;' }} background: #3454d1; color: #fff; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy_code') }}
                            </button>
                        </div>
                    </div>

                    <div style="background: rgba(128,128,128,0.05); padding: 16px; border-radius: 14px; text-align: center; border: 1px dashed var(--border-color, #ccc);">
                        <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 10px; font-weight: 600;">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('messages.live_preview') }} (728x90)
                        </span>
                        <div style="max-width: 100%; overflow-x: auto;">
                            <a href="{{ $refUrl }}" target="_blank">
                                <img src="{{ $banner728 }}" width="728" height="90" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" alt="728x90">
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tab Content 300x250 -->
                <div class="banner-tab-pane" id="b300" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block; opacity: 0.8;">
                            <i class="fa-solid fa-code me-1"></i> {{ __('messages.sponsorship_tag') }} (300x250)
                        </label>
                        <div style="position: relative;">
                            <textarea id="code300Text" readonly class="form-control" style="width: 100%; height: 90px; font-family: monospace; font-size: 12px; border-radius: 12px; padding: 12px; border: 1px solid var(--border-color, #ddd); background: var(--input-bg, rgba(0,0,0,0.02)); color: var(--text-color); resize: none;">{{ $code300 }}@if($extensions_code)

{{ $extensions_code }}@endif</textarea>
                            <button type="button" class="btn-copy-code" data-clipboard-target="#code300Text" style="position: absolute; top: 8px; {{ is_locale_rtl() ? 'left: 8px;' : 'right: 8px;' }} background: #3454d1; color: #fff; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy_code') }}
                            </button>
                        </div>
                    </div>

                    <div style="background: rgba(128,128,128,0.05); padding: 16px; border-radius: 14px; text-align: center; border: 1px dashed var(--border-color, #ccc);">
                        <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 10px; font-weight: 600;">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('messages.live_preview') }} (300x250)
                        </span>
                        <div style="display: flex; justify-content: center;">
                            <a href="{{ $refUrl }}" target="_blank">
                                <img src="{{ $banner300 }}" width="300" height="250" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" alt="300x250">
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tab Content 160x600 -->
                <div class="banner-tab-pane" id="b160" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block; opacity: 0.8;">
                            <i class="fa-solid fa-code me-1"></i> {{ __('messages.sponsorship_tag') }} (160x600)
                        </label>
                        <div style="position: relative;">
                            <textarea id="code160Text" readonly class="form-control" style="width: 100%; height: 90px; font-family: monospace; font-size: 12px; border-radius: 12px; padding: 12px; border: 1px solid var(--border-color, #ddd); background: var(--input-bg, rgba(0,0,0,0.02)); color: var(--text-color); resize: none;">{{ $code160 }}@if($extensions_code)

{{ $extensions_code }}@endif</textarea>
                            <button type="button" class="btn-copy-code" data-clipboard-target="#code160Text" style="position: absolute; top: 8px; {{ is_locale_rtl() ? 'left: 8px;' : 'right: 8px;' }} background: #3454d1; color: #fff; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy_code') }}
                            </button>
                        </div>
                    </div>

                    <div style="background: rgba(128,128,128,0.05); padding: 16px; border-radius: 14px; text-align: center; border: 1px dashed var(--border-color, #ccc);">
                        <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 10px; font-weight: 600;">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('messages.live_preview') }} (160x600)
                        </span>
                        <div style="display: flex; justify-content: center;">
                            <a href="{{ $refUrl }}" target="_blank">
                                <img src="{{ $banner160 }}" width="160" height="600" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" alt="160x600">
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tab Content 468x60 -->
                <div class="banner-tab-pane" id="b468" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <label style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: block; opacity: 0.8;">
                            <i class="fa-solid fa-code me-1"></i> {{ __('messages.sponsorship_tag') }} (468x60)
                        </label>
                        <div style="position: relative;">
                            <textarea id="code468Text" readonly class="form-control" style="width: 100%; height: 90px; font-family: monospace; font-size: 12px; border-radius: 12px; padding: 12px; border: 1px solid var(--border-color, #ddd); background: var(--input-bg, rgba(0,0,0,0.02)); color: var(--text-color); resize: none;">{{ $code468 }}@if($extensions_code)

{{ $extensions_code }}@endif</textarea>
                            <button type="button" class="btn-copy-code" data-clipboard-target="#code468Text" style="position: absolute; top: 8px; {{ is_locale_rtl() ? 'left: 8px;' : 'right: 8px;' }} background: #3454d1; color: #fff; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy_code') }}
                            </button>
                        </div>
                    </div>

                    <div style="background: rgba(128,128,128,0.05); padding: 16px; border-radius: 14px; text-align: center; border: 1px dashed var(--border-color, #ccc);">
                        <span style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 10px; font-weight: 600;">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('messages.live_preview') }} (468x60)
                        </span>
                        <div style="max-width: 100%; overflow-x: auto;">
                            <a href="{{ $refUrl }}" target="_blank">
                                <img src="{{ $banner468 }}" width="468" height="60" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" alt="468x60">
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Sidebar Column: Direct Link & Social Sharing -->
        <div class="superdesign-sidebar-col">
            
            <!-- Quick Link Box Card -->
            <div class="modern-card" style="background: var(--admin-premium-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)); border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); margin-bottom: 20px;">
                <h4 style="font-size: 16px; font-weight: 700; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-link" style="color: #615dfa;"></i>
                    {{ __('messages.your_referral_link') }}
                </h4>
                
                <div style="position: relative; margin-bottom: 14px;">
                    <input type="text" id="directRefUrl" value="{{ $refUrl }}" readonly style="width: 100%; padding: 10px 12px; border-radius: 12px; border: 1px solid var(--border-color, #ddd); font-size: 13px; font-weight: 600; background: var(--input-bg, rgba(0,0,0,0.02)); color: var(--text-color);">
                </div>
                
                <button type="button" id="copyDirectLinkBtn" style="width: 100%; background: linear-gradient(135deg, #3454d1, #615dfa); color: #fff; border: none; padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(52, 84, 209, 0.25); transition: all 0.2s;">
                    <i class="fa-solid fa-copy"></i>
                    <span id="copyBtnText">{{ __('messages.copy_link') }}</span>
                </button>
            </div>

            <!-- Social Media Share Card -->
            <div class="modern-card" style="background: var(--admin-premium-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)); border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                <h4 style="font-size: 16px; font-weight: 700; margin-top: 0; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-share-nodes" style="color: #17c666;"></i>
                    {{ __('messages.share_on_social') }}
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <!-- ADStn Network -->
                    <a href="https://www.adstn.ovh/share?text={{ urlencode($siteTitle) }}&url={{ urlencode($refUrl) }}" target="_blank" style="background: linear-gradient(135deg, #3454d1, #615dfa); color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; grid-column: span 2; box-shadow: 0 4px 12px rgba(52, 84, 209, 0.25); transition: opacity 0.2s;">
                        <i class="fa-brands fa-buysellads" style="font-size: 18px;"></i> ADStn
                    </a>
                    
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($siteTitle . ' - ' . $refUrl) }}" target="_blank" style="background: #25D366; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-brands fa-whatsapp" style="font-size: 16px;"></i> WhatsApp
                    </a>
                    
                    <!-- Telegram -->
                    <a href="https://telegram.me/share/url?url={{ urlencode($refUrl) }}&text={{ urlencode($siteTitle) }}" target="_blank" style="background: #0088cc; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-brands fa-telegram" style="font-size: 16px;"></i> Telegram
                    </a>
                    
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($refUrl) }}" target="_blank" style="background: #1877F2; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-brands fa-facebook-f" style="font-size: 16px;"></i> Facebook
                    </a>
                    
                    <!-- X / Twitter -->
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($siteTitle) }}&url={{ urlencode($refUrl) }}" target="_blank" style="background: #14171A; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-brands fa-x-twitter" style="font-size: 16px;"></i> X
                    </a>
                    
                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($refUrl) }}" target="_blank" style="background: #0A66C2; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-brands fa-linkedin-in" style="font-size: 16px;"></i> LinkedIn
                    </a>
                    
                    <!-- Email -->
                    <a href="mailto:?subject={{ urlencode($siteTitle) }}&body={{ urlencode($refUrl) }}" style="background: #ea4d4d; color: #fff; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.2s;">
                        <i class="fa-solid fa-envelope" style="font-size: 16px;"></i> Email
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Interactive Tab & Copy Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Banner Size Tab Switching
    const tabBtns = document.querySelectorAll('.b-tab-btn');
    const tabPanes = document.querySelectorAll('.banner-tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');

            tabBtns.forEach(b => {
                b.style.background = 'transparent';
                b.style.color = 'var(--text-color)';
                b.classList.remove('active');
            });
            this.style.background = '#3454d1';
            this.style.color = '#fff';
            this.classList.add('active');

            tabPanes.forEach(pane => {
                pane.style.display = (pane.id === targetId) ? 'block' : 'none';
            });
        });
    });

    // Copy Referral Link Direct Button
    const copyDirectBtn = document.getElementById('copyDirectLinkBtn');
    const copyDirectInput = document.getElementById('directRefUrl');
    const copyBtnText = document.getElementById('copyBtnText');

    if (copyDirectBtn && copyDirectInput) {
        copyDirectBtn.addEventListener('click', function() {
            copyDirectInput.select();
            document.execCommand('copy');
            const originalText = copyBtnText.innerText;
            copyBtnText.innerText = "{{ __('messages.link_copied') }}";
            copyDirectBtn.style.background = '#17c666';

            setTimeout(function() {
                copyBtnText.innerText = originalText;
                copyDirectBtn.style.background = 'linear-gradient(135deg, #3454d1, #615dfa)';
            }, 2500);
        });
    }

    // Copy Embed Code Buttons
    const codeCopyBtns = document.querySelectorAll('.btn-copy-code');
    codeCopyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetSelector = this.getAttribute('data-clipboard-target');
            const targetEl = document.querySelector(targetSelector);
            if (targetEl) {
                targetEl.select();
                document.execCommand('copy');
                const origHtml = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check me-1"></i> {{ __("messages.code_copied") }}';
                this.style.background = '#17c666';
                
                setTimeout(() => {
                    this.innerHTML = origHtml;
                    this.style.background = '#3454d1';
                }, 2500);
            }
        });
    });
});
</script>
@endsection
