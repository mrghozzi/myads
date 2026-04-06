@php
    $cookieEnabled = '0';
    $cookiePosition = 'bottom';
    $cookieBgColor  = '#1a1a2e';
    $cookieTextColor = '#ffffff';
    $cookieBtnBg    = '#615dfa';
    $cookieBtnText  = '#ffffff';

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('options')) {
            $cookieOptions = \App\Models\Option::where('o_type', 'cookie_notice')->get()->keyBy('name');
            $cookieEnabled    = $cookieOptions->has('enabled')    ? $cookieOptions['enabled']->o_valuer    : '0';
            $cookiePosition   = $cookieOptions->has('position')   ? $cookieOptions['position']->o_valuer   : 'bottom';
            $cookieBgColor    = $cookieOptions->has('bg_color')   ? $cookieOptions['bg_color']->o_valuer   : '#1a1a2e';
            $cookieTextColor  = $cookieOptions->has('text_color') ? $cookieOptions['text_color']->o_valuer : '#ffffff';
            $cookieBtnBg      = $cookieOptions->has('btn_bg')     ? $cookieOptions['btn_bg']->o_valuer     : '#615dfa';
            $cookieBtnText    = $cookieOptions->has('btn_text')   ? $cookieOptions['btn_text']->o_valuer   : '#ffffff';
        }
    } catch (\Exception $e) {
        // DB not ready yet (fresh install) — skip cookie banner
    }

    // Performance Optimization: Check for consent status in PHP to avoid LCP delay
    $consentStatus = $_COOKIE['cookie_consent_status'] ?? null;
    $shouldRender = ($cookieEnabled == '1' && !$consentStatus);
@endphp

@if($shouldRender)
<style>
    .cookie-notice-banner {
        position: fixed;
        z-index: 99999;
        background-color: {{ $cookieBgColor }};
        color: {{ $cookieTextColor }};
        padding: 20px;
        box-shadow: 0 -12px 30px rgba(0, 0, 0, 0.25);
        box-sizing: border-box;
        opacity: 1;
        transition: opacity 0.3s ease, transform 0.3s ease;
        /* Render visible by default for LCP optimization */
        display: flex; 
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .cookie-notice-banner.position-bottom {
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        text-align: center;
    }

    .cookie-notice-banner.position-top {
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        text-align: center;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .cookie-notice-banner.position-bottom_left {
        bottom: 20px;
        left: 20px;
        max-width: 400px;
        border-radius: 12px;
        display: block;
    }

    .cookie-notice-banner.position-bottom_right {
        bottom: 20px;
        right: 20px;
        max-width: 400px;
        border-radius: 12px;
        display: block;
    }

    .cookie-content {
        margin-bottom: 15px;
    }

    .cookie-content h5 {
        margin: 0 0 8px 0;
        font-size: 1rem;
        font-weight: 700;
        color: {{ $cookieTextColor }};
    }

    .cookie-content p {
        margin: 0;
        font-size: 14px;
        line-height: 1.6;
        opacity: 0.85;
    }

    .cookie-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .cookie-btn {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .cookie-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .cookie-btn-primary {
        background-color: {{ $cookieBtnBg }};
        color: {{ $cookieBtnText }};
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
    }

    .cookie-btn-secondary {
        background-color: transparent;
        color: {{ $cookieTextColor }};
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    @media (min-width: 768px) {
        .cookie-notice-banner.position-bottom, .cookie-notice-banner.position-top {
            flex-direction: row;
            justify-content: space-between;
            text-align: left;
            padding: 24px 40px;
        }
        
        .cookie-notice-banner.position-bottom .cookie-content, .cookie-notice-banner.position-top .cookie-content {
            margin-bottom: 0;
            margin-right: 40px;
        }
    }
</style>

<div id="cookieNoticeBanner" class="cookie-notice-banner position-{{ $cookiePosition }}">
    <div class="cookie-content">
        <h5>{{ __('messages.cookie_consent_title') ?? 'We value your privacy' }}</h5>
        <p>{{ __('messages.cookie_consent_text') ?? 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.' }}</p>
    </div>
    <div class="cookie-actions">
        <button id="cookieRejectBtn" class="cookie-btn cookie-btn-secondary">{{ __('messages.cookie_reject_all') ?? 'Reject All' }}</button>
        <button id="cookieAcceptBtn" class="cookie-btn cookie-btn-primary">{{ __('messages.cookie_accept_all') ?? 'Accept All' }}</button>
    </div>
</div>

<script>
    (function() {
        var banner = document.getElementById('cookieNoticeBanner');
        if (!banner) return;

        // Immediate check for localStorage as a fallback to cookie detection
        var status = localStorage.getItem('cookie_consent_status');
        if (status) {
            banner.style.display = 'none';
        }

        function setConsent(status) {
            localStorage.setItem('cookie_consent_status', status);
            // Set cookie so PHP can detect it on the next page load
            var date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
            document.cookie = "cookie_consent_status=" + status + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";
            
            banner.style.opacity = '0';
            setTimeout(function() {
                banner.style.display = 'none';
            }, 300);
        }

        document.getElementById('cookieAcceptBtn')?.addEventListener('click', function() {
            setConsent('accepted');
        });

        document.getElementById('cookieRejectBtn')?.addEventListener('click', function() {
            setConsent('rejected');
        });
    })();
</script>
@endif
