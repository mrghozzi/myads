@extends('theme::layouts.master')

@section('title', $title ?? __('messages.error_404_title'))

@section('content')
<div class="error-page-wrapper">
    <div class="error-container">
        <div class="error-card">
            <div class="error-content">
                <div class="error-illustration">
                    <div class="error-code">
                        <span>{{ $code ?? '404' }}</span>
                    </div>
                    <div class="error-shadow"></div>
                </div>
                
                <h1 class="error-title">{{ $title ?? __('messages.error_404_title') }}</h1>
                <p class="error-message">{{ $message ?? __('messages.error_404_text') }}</p>
                
                <div class="error-actions">
                    <a href="{{ url('/') }}" class="button primary">
                        <i class="fa-solid fa-house me-2"></i>
                        {{ __('messages.back_to_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
    padding: 40px 20px;
}

.error-container {
    width: 100%;
    max-width: 600px;
}

.error-card {
    background: var(--notification-ui-card-bg);
    border: 1px solid var(--notification-ui-card-border);
    border-radius: 20px;
    box-shadow: var(--notification-ui-card-shadow);
    padding: 60px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.error-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #615dfa, #23d2e2);
}

.error-illustration {
    position: relative;
    margin-bottom: 40px;
}

.error-code {
    font-size: 120px;
    font-weight: 900;
    line-height: 1;
    color: #615dfa;
    letter-spacing: -5px;
    position: relative;
    z-index: 1;
    text-shadow: 0 10px 30px rgba(97, 93, 250, 0.2);
    animation: float 3s ease-in-out infinite;
}

.error-shadow {
    width: 100px;
    height: 10px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    margin: 10px auto 0;
    filter: blur(5px);
    animation: shadowScale 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes shadowScale {
    0%, 100% { transform: scale(1); opacity: 0.4; }
    50% { transform: scale(1.5); opacity: 0.2; }
}

.error-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--notification-ui-summary-heading);
    margin-bottom: 15px;
}

.error-message {
    font-size: 16px;
    font-weight: 500;
    color: var(--notification-ui-muted);
    margin-bottom: 40px;
    line-height: 1.6;
}

.error-actions {
    display: flex;
    justify-content: center;
}

.error-actions .button {
    padding: 0 35px;
    height: 52px;
    line-height: 52px;
    font-weight: 700;
    font-size: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.error-actions .button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(97, 93, 250, 0.2);
}

body[data-theme="css_d"] .error-shadow {
    background: rgba(255, 255, 255, 0.05);
}

body[data-theme="css_d"] .error-code {
    color: #4ff461;
    text-shadow: 0 10px 30px rgba(79, 244, 97, 0.2);
}

@media (max-width: 480px) {
    .error-card {
        padding: 40px 20px;
    }
    .error-code {
        font-size: 80px;
    }
    .error-title {
        font-size: 22px;
    }
}
</style>
@endsection
