@extends('theme::layouts.master')

@section('content')
<style>
    .ap-checkout-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
        position: relative;
        overflow: hidden;
    }
    .ap-checkout-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.03) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(147,130,220,0.05) 0%, transparent 40%);
        animation: ap-float 15s ease-in-out infinite;
    }
    @keyframes ap-float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(-20px, -10px) rotate(1deg); }
    }
    .ap-card {
        position: relative;
        width: 100%;
        max-width: 480px;
        background: rgba(30, 30, 40, 0.85);
        backdrop-filter: blur(40px);
        -webkit-backdrop-filter: blur(40px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 48px 36px 36px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.04) inset;
        z-index: 2;
    }
    .ap-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 32px;
    }
    .ap-logo-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #333 0%, #111 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .ap-logo-text {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        letter-spacing: -0.3px;
    }
    .ap-badge-beta {
        display: inline-block;
        padding: 3px 10px;
        background: linear-gradient(135deg, #f5a623, #f7c948);
        color: #1a1a2e;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-left: 6px;
        vertical-align: middle;
    }
    .ap-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        margin: 24px 0;
    }
    .ap-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }
    .ap-detail-label {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 400;
    }
    .ap-detail-value {
        font-size: 14px;
        color: #fff;
        font-weight: 500;
    }
    .ap-total-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding: 16px 0 4px;
    }
    .ap-total-label {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }
    .ap-total-value {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.5px;
    }
    .ap-total-currency {
        font-size: 14px;
        font-weight: 400;
        color: rgba(255,255,255,0.5);
        margin-left: 4px;
    }
    .ap-pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 16px 24px;
        margin-top: 28px;
        background: #fff;
        color: #000;
        border: none;
        border-radius: 14px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 20px rgba(255,255,255,0.1);
        letter-spacing: -0.3px;
    }
    .ap-pay-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(255,255,255,0.15);
        background: #f0f0f0;
    }
    .ap-pay-btn:active {
        transform: translateY(0);
    }
    .ap-pay-btn .ap-apple-icon {
        font-size: 22px;
    }
    .ap-cancel-link {
        display: block;
        text-align: center;
        margin-top: 16px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 13px;
        text-decoration: none;
        transition: color 0.2s;
    }
    .ap-cancel-link:hover {
        color: rgba(255, 255, 255, 0.7);
    }
    .ap-secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 20px;
        color: rgba(255, 255, 255, 0.3);
        font-size: 12px;
    }
    /* Overlay */
    .ap-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .ap-overlay.active { display: flex; }
    .ap-sheet {
        width: 100%;
        max-width: 400px;
        background: rgba(40, 40, 55, 0.95);
        backdrop-filter: blur(30px);
        border-radius: 20px;
        padding: 40px 32px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.06);
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6);
        animation: ap-sheet-in 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes ap-sheet-in {
        from { opacity: 0; transform: scale(0.9) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .ap-sheet-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #333, #111);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fff;
        box-shadow: 0 6px 25px rgba(0,0,0,0.4);
    }
    .ap-sheet-title {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 8px;
    }
    .ap-sheet-text {
        font-size: 14px;
        color: rgba(255,255,255,0.5);
        margin-bottom: 24px;
    }
    .ap-fingerprint {
        width: 56px;
        height: 56px;
        margin: 0 auto 20px;
        border-radius: 50%;
        border: 3px solid rgba(76, 175, 80, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: ap-pulse 1.5s ease-in-out infinite;
    }
    @keyframes ap-pulse {
        0%, 100% { border-color: rgba(76, 175, 80, 0.3); box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.2); }
        50% { border-color: rgba(76, 175, 80, 0.7); box-shadow: 0 0 0 10px rgba(76, 175, 80, 0); }
    }
    .ap-fingerprint i {
        font-size: 24px;
        color: #4caf50;
    }
    .ap-processing-spinner {
        display: none;
        width: 44px;
        height: 44px;
        margin: 0 auto 20px;
        border: 3px solid rgba(255,255,255,0.1);
        border-top-color: #4caf50;
        border-radius: 50%;
        animation: ap-spin 0.8s linear infinite;
    }
    @keyframes ap-spin {
        to { transform: rotate(360deg); }
    }
    .ap-success-check {
        display: none;
        width: 56px;
        height: 56px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #4caf50, #45a049);
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        animation: ap-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes ap-pop {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
    .ap-note {
        display: inline-block;
        margin-top: 20px;
        padding: 8px 16px;
        background: rgba(245, 166, 35, 0.12);
        border: 1px solid rgba(245, 166, 35, 0.25);
        border-radius: 10px;
        color: #f5a623;
        font-size: 12px;
        font-weight: 500;
    }
</style>

<div class="ap-checkout-wrapper">
    <div class="ap-card">
        <div class="ap-logo">
            <div class="ap-logo-icon"><i class="fab fa-apple"></i></div>
            <span class="ap-logo-text"> Pay <span class="ap-badge-beta">BETA</span></span>
        </div>

        <div class="ap-divider"></div>

        <div class="ap-detail-row">
            <span class="ap-detail-label">{{ __('messages.plan') }}</span>
            <span class="ap-detail-value">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')) }}</span>
        </div>
        <div class="ap-detail-row">
            <span class="ap-detail-label">{{ __('messages.billing_order_id') ?? 'Order' }}</span>
            <span class="ap-detail-value">{{ $order->order_number }}</span>
        </div>
        <div class="ap-detail-row">
            <span class="ap-detail-label">{{ __('messages.gateway') }}</span>
            <span class="ap-detail-value"> Pay</span>
        </div>

        <div class="ap-divider"></div>

        <div class="ap-total-row">
            <span class="ap-total-label">{{ __('messages.amount') }}</span>
            <span class="ap-total-value">
                {{ number_format((float)$order->display_amount, 2) }}
                <span class="ap-total-currency">{{ $order->currency_code }}</span>
            </span>
        </div>

        <button type="button" class="ap-pay-btn" id="applePayBtn" onclick="beginApplePay()">
            <span class="ap-apple-icon"><i class="fab fa-apple"></i></span>
            Pay with  Pay
        </button>

        <a href="{{ route('billing.orders.show', $order->id) }}" class="ap-cancel-link">
            {{ __('messages.cancel') }}
        </a>

        <div class="ap-secure-badge">
            <i class="fas fa-lock"></i>
            <span>{{ __('messages.billing_apple_secure_note') ?? 'Secured by Apple Pay' }}</span>
        </div>

        <div style="text-align: center;">
            <span class="ap-note"><i class="fas fa-flask" style="margin-right:4px;"></i> {{ __('messages.billing_apple_simulation_notice') ?? 'Simulation Mode – No real charge' }}</span>
        </div>
    </div>
</div>

<!-- Payment Sheet Overlay -->
<div class="ap-overlay" id="apOverlay">
    <div class="ap-sheet">
        <div class="ap-sheet-icon"><i class="fab fa-apple"></i></div>
        <div class="ap-sheet-title" id="apSheetTitle">Confirm with  Pay</div>
        <div class="ap-sheet-text" id="apSheetText">
            {{ number_format((float)$order->display_amount, 2) }} {{ $order->currency_code }}
        </div>

        <div class="ap-fingerprint" id="apFingerprint">
            <i class="fas fa-fingerprint"></i>
        </div>

        <div class="ap-processing-spinner" id="apSpinner"></div>

        <div class="ap-success-check" id="apCheck">
            <i class="fas fa-check"></i>
        </div>

        <p class="ap-sheet-text" id="apInstructions">Double-click to confirm</p>
    </div>
</div>

<script>
function beginApplePay() {
    document.getElementById('apOverlay').classList.add('active');
    document.getElementById('apFingerprint').style.display = 'flex';
    document.getElementById('apSpinner').style.display = 'none';
    document.getElementById('apCheck').style.display = 'none';
    document.getElementById('apSheetTitle').textContent = 'Confirm with  Pay';
    document.getElementById('apInstructions').textContent = 'Double-click to confirm';

    var clickCount = 0;
    var timer = null;
    var overlay = document.getElementById('apOverlay');

    overlay.onclick = function(e) {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            return;
        }
        clickCount++;
        if (clickCount === 1) {
            clearTimeout(timer);
            timer = setTimeout(function() { clickCount = 0; }, 600);
        }
        if (clickCount >= 2) {
            clickCount = 0;
            clearTimeout(timer);
            processPayment();
        }
    };
}

function processPayment() {
    document.getElementById('apFingerprint').style.display = 'none';
    document.getElementById('apSpinner').style.display = 'block';
    document.getElementById('apSheetTitle').textContent = 'Processing…';
    document.getElementById('apInstructions').textContent = 'Please wait';

    setTimeout(function() {
        document.getElementById('apSpinner').style.display = 'none';
        document.getElementById('apCheck').style.display = 'flex';
        document.getElementById('apSheetTitle').textContent = 'Payment Successful';
        document.getElementById('apInstructions').textContent = 'Redirecting…';

        setTimeout(function() {
            var url = @json($returnUrl);
            window.location.href = url + '?status=success&transaction_id=ap_sim_' + Date.now();
        }, 1200);
    }, 2000);
}
</script>
@endsection
