@extends('theme::layouts.master')

@php
    $maintenanceSettings = $maintenanceSettings ?? \App\Support\MaintenanceSettings::all();
    $message = $maintenanceSettings['message'] ?: __('messages.maintenance_default_message');
    $logoPath = $maintenanceSettings['logo_path'] ?? '';
@endphp

@section('title', __('messages.error_503_title'))

@section('content')
<div class="maintenance-page-shell">
    <div class="maintenance-page-card">
        <div class="maintenance-page-copy">
            <span class="maintenance-page-chip">{{ __('messages.maintenance_status_enabled') }}</span>
            <h1 class="maintenance-page-title">{{ __('messages.maintenance_page_title') }}</h1>
            <p class="maintenance-page-text">{{ $message }}</p>
            <div class="maintenance-page-meta">
                <div class="maintenance-meta-item">
                    <span>{{ __('messages.error_503_title') }}</span>
                    <strong>503</strong>
                </div>
                <div class="maintenance-meta-item">
                    <span>{{ __('messages.maintenance_retry_notice_label') }}</span>
                    <strong>{{ __('messages.maintenance_retry_notice') }}</strong>
                </div>
            </div>
        </div>
        <div class="maintenance-page-visual">
            @if($logoPath)
                <div class="maintenance-page-logo-card">
                    <img src="{{ asset($logoPath) }}" alt="{{ __('messages.maintenance_logo_label') }}" class="maintenance-page-logo">
                </div>
            @else
                <div class="maintenance-page-icon-card">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
            @endif
            <div class="maintenance-page-ring maintenance-ring-one"></div>
            <div class="maintenance-page-ring maintenance-ring-two"></div>
        </div>
    </div>
</div>

<style>
    .maintenance-page-shell {
        min-height: 75vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px 48px;
    }
    .maintenance-page-card {
        width: min(1080px, 100%);
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
        padding: 32px;
        border-radius: 32px;
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.08), rgba(35, 210, 226, 0.08));
        border: 1px solid rgba(97, 93, 250, 0.12);
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }
    .maintenance-page-copy {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .maintenance-page-chip {
        display: inline-flex;
        align-self: flex-start;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 92, 120, 0.12);
        color: #ff5c78;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .maintenance-page-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 900;
        color: var(--notification-ui-summary-heading);
        margin-bottom: 18px;
    }
    .maintenance-page-text {
        font-size: 1.05rem;
        color: var(--notification-ui-muted);
        line-height: 1.8;
        margin-bottom: 28px;
        max-width: 580px;
    }
    .maintenance-page-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }
    .maintenance-meta-item {
        min-width: 170px;
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.66);
        border: 1px solid rgba(97, 93, 250, 0.1);
    }
    .maintenance-meta-item span {
        display: block;
        font-size: 12px;
        color: var(--notification-ui-muted);
        margin-bottom: 6px;
    }
    .maintenance-meta-item strong {
        color: var(--notification-ui-summary-heading);
        font-size: 1rem;
    }
    .maintenance-page-visual {
        position: relative;
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .maintenance-page-icon-card,
    .maintenance-page-logo-card {
        width: 180px;
        height: 180px;
        border-radius: 40px;
        background: linear-gradient(145deg, #615dfa, #23d2e2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 72px;
        position: relative;
        z-index: 2;
        box-shadow: 0 24px 50px rgba(97, 93, 250, 0.28);
    }
    .maintenance-page-logo-card {
        padding: 22px;
        background: rgba(255, 255, 255, 0.92);
    }
    .maintenance-page-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .maintenance-page-ring {
        position: absolute;
        border-radius: 50%;
        border: 1px dashed rgba(97, 93, 250, 0.28);
    }
    .maintenance-ring-one {
        width: 280px;
        height: 280px;
    }
    .maintenance-ring-two {
        width: 360px;
        height: 360px;
        border-color: rgba(35, 210, 226, 0.24);
    }
    @media (max-width: 900px) {
        .maintenance-page-card {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .maintenance-page-chip {
            align-self: center;
        }
        .maintenance-page-text {
            max-width: none;
        }
        .maintenance-page-meta {
            justify-content: center;
        }
    }
</style>
@endsection
