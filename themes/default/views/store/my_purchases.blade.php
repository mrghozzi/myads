@extends('theme::layouts.master')

@section('content')
<style>
    :root {
        --myads-primary: #615dfa;
        --myads-primary-hover: #4e4ac8;
        --myads-accent: #23d2e2;
        --myads-green: #4ff461;
        --myads-dark: #0f172a;
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

    .my-purchases-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .purchases-banner {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        padding: 32px 24px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
        position: relative;
        overflow: hidden;
    }

    .purchases-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .purchases-banner-icon {
        font-size: 40px;
        background: rgba(255,255,255,0.2);
        width: 72px;
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .purchases-banner-content {
        flex-grow: 1;
        position: relative;
        z-index: 1;
    }

    .purchases-banner-content h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .purchases-banner-content p {
        margin: 4px 0 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .purchases-banner-action {
        position: relative;
        z-index: 1;
    }

    .purchases-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    @media (max-width: 992px) {
        .purchases-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .purchases-grid {
            grid-template-columns: 1fr;
        }
    }

    .purchase-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }

    .purchase-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }

    .purchase-image-container {
        position: relative;
        width: 100%;
        padding-top: 52%;
        background: rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .purchase-image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .purchase-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .purchase-category {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--myads-primary);
        margin-bottom: 6px;
    }

    .purchase-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 8px;
        color: var(--text-color);
    }

    .purchase-title a {
        color: inherit;
        text-decoration: none;
    }

    .purchase-seller {
        font-size: 12px;
        color: var(--myads-text-muted);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .purchase-license-box {
        background: rgba(0,0,0,0.03);
        border: 1px dashed var(--border-color);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 16px;
    }

    .purchase-license-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--myads-text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .purchase-license-key {
        font-family: monospace;
        font-size: 13px;
        font-weight: 700;
        color: var(--myads-primary);
        letter-spacing: 0.5px;
        word-break: break-all;
    }

    .purchase-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--border-color);
    }

    .modern-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .modern-btn-primary {
        background: var(--myads-primary);
        color: #fff !important;
    }

    .modern-btn-primary:hover {
        background: var(--myads-primary-hover);
    }

    .modern-btn-secondary {
        background: rgba(0,0,0,0.06);
        color: var(--text-color) !important;
    }

    .copy-btn {
        padding: 2px 8px;
        font-size: 11px;
        border-radius: 6px;
        background: rgba(97, 93, 250, 0.1);
        color: var(--myads-primary);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .copy-btn:hover {
        background: rgba(97, 93, 250, 0.2);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--myads-text-muted);
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<div class="my-purchases-page">
    {{-- Banner --}}
    <div class="purchases-banner">
        <div class="purchases-banner-icon">
            <i class="fa fa-box-open"></i>
        </div>
        <div class="purchases-banner-content">
            <h1>{{ __('messages.my_purchases') ?? 'My Purchases' }}</h1>
            <p>{{ __('messages.my_purchases_desc') ?? 'Manage your purchased digital products, license keys, and direct file downloads.' }}</p>
        </div>
        <div class="purchases-banner-action">
            <a href="{{ route('store.index') }}" class="modern-btn" style="background: rgba(255,255,255,0.25); color: #fff; padding: 10px 18px; border-radius: 10px;">
                <i class="fa fa-shopping-bag"></i>
                <span>{{ __('messages.store') }}</span>
            </a>
        </div>
    </div>

    {{-- Content --}}
    @if($purchases->count() > 0)
        <div class="purchases-grid">
            @foreach($purchases as $item)
                @php
                    $prodImage = $item->product_image ? (str_starts_with($item->product_image, 'http') ? $item->product_image : asset($item->product_image)) : theme_asset('img/error_plug.png');
                @endphp
                <div class="purchase-card">
                    <a href="{{ route('store.show', $item->product_name) }}" class="purchase-image-container">
                        <img src="{{ $prodImage }}" alt="{{ $item->product_name }}" onerror="this.src='{{ theme_asset('img/error_plug.png') }}'">
                    </a>
                    <div class="purchase-body">
                        @if($item->category)
                            <div class="purchase-category">
                                {{ __('messages.' . $item->category) != 'messages.' . $item->category ? __('messages.' . $item->category) : ucfirst($item->category) }}
                            </div>
                        @endif
                        <h3 class="purchase-title">
                            <a href="{{ route('store.show', $item->product_name) }}">{{ $item->product_name }}</a>
                        </h3>
                        <div class="purchase-seller">
                            <i class="fa fa-user"></i>
                            <span>{{ __('messages.seller') ?? 'Seller' }}:</span>
                            @if($item->seller_username)
                                <a href="{{ route('profile.show', $item->seller_username) }}" style="color: inherit; font-weight: 600;">{{ $item->seller_username }}</a>
                            @else
                                <span>{{ __('messages.unknown') ?? 'Unknown' }}</span>
                            @endif
                            <span class="ms-auto" style="font-size: 11px;">
                                {{ \Carbon\Carbon::parse($item->purchased_at)->format('Y-m-d') }}
                            </span>
                        </div>

                        {{-- License Box --}}
                        <div class="purchase-license-box">
                            <div class="purchase-license-label">
                                <span>{{ __('messages.license_key') ?? 'License Key' }}</span>
                                <button type="button" class="copy-btn" onclick="copyLicense('{{ $item->license_key }}', this)">
                                    <i class="fa fa-copy"></i>
                                    <span>{{ __('messages.copy') ?? 'Copy' }}</span>
                                </button>
                            </div>
                            <div class="purchase-license-key">{{ $item->license_key }}</div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="purchase-footer">
                            @if($item->download_hash)
                                <a href="{{ route('store.download.hash', $item->download_hash) }}" class="modern-btn modern-btn-primary flex-grow-1">
                                    <i class="fa fa-download"></i>
                                    <span>{{ __('messages.download') }} ({{ $item->latest_version }})</span>
                                </a>
                            @else
                                <a href="{{ route('store.show', $item->product_name) }}" class="modern-btn modern-btn-primary flex-grow-1">
                                    <i class="fa fa-eye"></i>
                                    <span>{{ __('messages.view') }}</span>
                                </a>
                            @endif
                            <a href="{{ route('kb.index', $item->product_name) }}" class="modern-btn modern-btn-secondary" title="{{ __('messages.knowledgebase') }}">
                                <i class="fa fa-book"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($purchases->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $purchases->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fa fa-box-open d-block"></i>
            <h3 class="fw-bold mb-2">{{ __('messages.no_purchases_yet') ?? 'No purchases yet' }}</h3>
            <p class="text-muted mb-4">{{ __('messages.no_purchases_desc') ?? 'You have not purchased any products from the store yet. Explore our marketplace to find scripts, templates, and plugins!' }}</p>
            <a href="{{ route('store.index') }}" class="modern-btn modern-btn-primary" style="padding: 10px 24px;">
                <i class="fa fa-shopping-bag me-1"></i>
                <span>{{ __('messages.explore_store') ?? 'Explore Store' }}</span>
            </a>
        </div>
    @endif
</div>

<script>
    function copyLicense(text, button) {
        navigator.clipboard.writeText(text).then(function() {
            var originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fa fa-check"></i> <span>{{ __("messages.copied") ?? "Copied!" }}</span>';
            button.style.background = 'rgba(16, 185, 129, 0.2)';
            button.style.color = '#10b981';
            setTimeout(function() {
                button.innerHTML = originalHtml;
                button.style.background = '';
                button.style.color = '';
            }, 2000);
        }).catch(function() {
            prompt("{{ __('messages.copy_license') ?? 'Copy license key:' }}", text);
        });
    }
</script>
@endsection
