@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.active_sessions') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="sessions-superdesign-shell">
            <!-- Modern Superdesign Hero Header -->
            <div class="sessions-hero-card">
                <div class="sessions-hero-inner">
                    <div class="sessions-hero-text">
                        <div class="sessions-hero-badge">
                            <i class="fa-solid fa-shield-cat"></i>
                            <span>{{ __('messages.security_title') ?? 'Security & Privacy' }}</span>
                        </div>
                        <h3 class="sessions-hero-title">{{ __('messages.manage_sessions') }}</h3>
                        <p class="sessions-hero-subtitle">{{ __('messages.security_member_sessions_desc') ?? 'Monitor and manage your active login sessions across all your devices.' }}</p>
                    </div>

                    <!-- Live KPI Counters -->
                    <div class="sessions-kpi-group">
                        <div class="sessions-kpi-pill">
                            <span class="kpi-label">{{ __('messages.active') }}</span>
                            <span class="kpi-value is-active" id="activeSessionsCount">{{ (int) ($activeCount ?? 0) }}</span>
                        </div>
                        <div class="sessions-kpi-pill">
                            <span class="kpi-label">{{ __('messages.security_member_sessions_title') }}</span>
                            <span class="kpi-value" id="totalSessionsCount">{{ (int) ($totalCount ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Abstract Visual Ornaments -->
                <div class="sessions-hero-orb orb-1"></div>
                <div class="sessions-hero-orb orb-2"></div>
                <i class="fa-solid fa-shield-halved sessions-hero-watermark"></i>
            </div>

            <!-- Dynamic Feedback Notification -->
            <div id="sessionsAjaxAlert" class="sessions-toast-alert" style="display: none;" role="alert">
                <i class="fa-solid fa-circle-check toast-icon"></i>
                <span class="toast-message"></span>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="border-radius: 14px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="border-radius: 14px; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

            @if(!empty($upgradeNotice))
                @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
            @endif

            <!-- AJAX Container With Loading State Overlay -->
            <div class="sessions-wrapper position-relative">
                <div id="sessionsLoadingOverlay" class="sessions-loading-overlay" style="display: none;">
                    <div class="sessions-spinner">
                        <div class="spinner-ring"></div>
                        <span>{{ __('messages.loading') ?? 'Loading...' }}</span>
                    </div>
                </div>

                <div id="sessionsDynamicContent">
                    @include('theme::profile.partials.sessions_list', ['sessions' => $sessions, 'featureAvailable' => $featureAvailable])
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Superdesign Sessions Styling */
    .sessions-superdesign-shell {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sessions-hero-card {
        background: linear-gradient(135deg, var(--primary-color, #23d2e2) 0%, #1c9bb0 55%, #187e90 100%);
        padding: 34px 38px;
        border-radius: 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(35, 210, 226, 0.22);
    }

    .sessions-hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .sessions-hero-text {
        max-width: 580px;
    }

    .sessions-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .sessions-hero-title {
        font-weight: 800;
        font-size: 26px;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
        color: #ffffff !important;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .sessions-hero-subtitle {
        color: rgba(255, 255, 255, 0.95) !important;
        font-size: 14.5px;
        line-height: 1.6;
        margin: 0;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .sessions-kpi-group {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .sessions-kpi-pill {
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.28);
        padding: 12px 20px;
        border-radius: 18px;
        min-width: 110px;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .sessions-kpi-pill .kpi-label {
        display: block;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(255, 255, 255, 0.95) !important;
        font-weight: 700;
        margin-bottom: 4px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    }

    .sessions-kpi-pill .kpi-value {
        display: block;
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        color: #ffffff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .sessions-kpi-pill .kpi-value.is-active {
        color: #a7f3d0;
    }

    .sessions-hero-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .sessions-hero-orb.orb-1 {
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, 0.12);
    }

    .sessions-hero-orb.orb-2 {
        bottom: -40px;
        left: 12%;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.06);
    }

    .sessions-hero-watermark {
        position: absolute;
        right: 30px;
        bottom: -28px;
        font-size: 130px;
        color: rgba(255, 255, 255, 0.08);
        transform: rotate(-12deg);
        pointer-events: none;
    }

    /* Card list styling */
    .sessions-cards-stack {
        display: grid;
        gap: 18px;
    }

    .session-card {
        background: var(--widget-box-bg, #fff);
        border: 1px solid var(--border-color, #ebebeb);
        border-radius: 20px;
        padding: 22px 26px;
        display: flex;
        align-items: center;
        gap: 22px;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .session-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    .session-card.current {
        border-color: var(--primary-color, #23d2e2);
        box-shadow: 0 8px 24px rgba(35, 210, 226, 0.14);
    }

    .session-card-current-bar {
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--primary-color, #23d2e2);
    }

    .device-icon-box {
        width: 60px;
        height: 60px;
        background: var(--dark-light-color, #f4f6fa);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.25s ease;
        color: var(--text-color-alt, #888);
        font-size: 26px;
    }

    .device-icon-box.is-current {
        background: linear-gradient(135deg, rgba(35, 210, 226, 0.15) 0%, rgba(35, 210, 226, 0.28) 100%);
        color: var(--primary-color, #23d2e2);
    }

    .session-card:hover .device-icon-box {
        transform: scale(1.05);
    }

    .session-info-main {
        flex-grow: 1;
        min-width: 0;
    }

    .session-info-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .session-device-name {
        font-weight: 700;
        font-size: 16.5px;
        margin: 0;
        color: var(--text-color, #283c50);
        letter-spacing: -0.01em;
    }

    .session-status-badge {
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-width: 1px;
        border-style: solid;
    }

    .session-status-badge.badge-current {
        background: #eefdf3;
        color: #16a34a;
        border-color: rgba(22, 163, 74, 0.25);
    }

    .session-status-badge.badge-active {
        background: #eff6ff;
        color: #2563eb;
        border-color: rgba(37, 99, 235, 0.25);
    }

    .session-status-badge.badge-revoked {
        background: #fef2f2;
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.25);
    }

    .session-status-badge.badge-ended {
        background: #f3f4f6;
        color: #6b7280;
        border-color: rgba(107, 114, 128, 0.25);
    }

    .session-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #16a34a;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.25);
        animation: pulseLiveDot 1.8s infinite;
    }

    @keyframes pulseLiveDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    .session-meta-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        color: var(--text-color-alt, #7b819d);
        font-size: 13.5px;
    }

    .session-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .session-meta-item code {
        font-size: 12.5px;
        padding: 2px 6px;
        border-radius: 6px;
        background: var(--dark-light-color, #f4f6fa);
        color: inherit;
        border: 1px solid var(--border-color, #ebebeb);
    }

    .session-actions-wrap {
        flex-shrink: 0;
    }

    .btn-session-action {
        border-radius: 12px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .btn-session-action:hover {
        transform: translateY(-2px);
    }

    /* Empty state */
    .sessions-empty-state {
        background: var(--widget-box-bg, #fff);
        border: 1px solid var(--border-color, #ebebeb);
        border-radius: 22px;
        padding: 60px 24px;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .sessions-empty-icon {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: var(--dark-light-color, #f4f6fa);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        font-size: 36px;
        color: var(--border-color, #ccc);
    }

    .sessions-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-color, #283c50);
        margin-bottom: 6px;
    }

    .sessions-empty-desc {
        color: var(--text-color-alt, #888);
        font-size: 14px;
        max-width: 440px;
        margin: 0 auto;
    }

    /* Pagination controls styling */
    .sessions-pagination-holder {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .sessions-pagination-holder .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    .sessions-pagination-holder .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 14px;
        border-radius: 12px;
        background: var(--widget-box-bg, #fff);
        border: 1px solid var(--border-color, #ebebeb);
        color: var(--text-color, #333);
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .sessions-pagination-holder .page-item.active .page-link {
        background: var(--primary-color, #23d2e2);
        border-color: var(--primary-color, #23d2e2);
        color: #fff;
        box-shadow: 0 4px 14px rgba(35, 210, 226, 0.35);
    }

    .sessions-pagination-holder .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--dark-light-color, #f4f6fa);
        border-color: var(--primary-color, #23d2e2);
        color: var(--primary-color, #23d2e2);
        transform: translateY(-2px);
    }

    .sessions-pagination-holder .page-item.disabled .page-link {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* Loading overlay */
    .sessions-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: opacity 0.2s ease;
    }

    html[data-theme="css_d"] .sessions-loading-overlay {
        background: rgba(29, 35, 51, 0.75);
    }

    .sessions-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-color, #333);
    }

    .spinner-ring {
        width: 38px;
        height: 38px;
        border: 3.5px solid rgba(35, 210, 226, 0.2);
        border-top-color: var(--primary-color, #23d2e2);
        border-radius: 50%;
        animation: sessionsSpin 0.75s linear infinite;
    }

    @keyframes sessionsSpin {
        to { transform: rotate(360deg); }
    }

    /* Toast notification */
    .sessions-toast-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        background: #eefdf3;
        border: 1px solid rgba(22, 163, 74, 0.3);
        color: #166534;
        box-shadow: 0 4px 16px rgba(22, 163, 74, 0.1);
        margin-bottom: 16px;
        animation: toastSlideDown 0.3s ease;
    }

    .sessions-toast-alert.is-error {
        background: #fef2f2;
        border-color: rgba(220, 38, 38, 0.3);
        color: #991b1b;
        box-shadow: 0 4px 16px rgba(220, 38, 38, 0.1);
    }

    @keyframes toastSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* RTL adaptations */
    [dir="rtl"] .session-meta-item i {
        margin-right: 0;
        margin-left: 6px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sessions-hero-card {
            padding: 26px 20px;
            border-radius: 20px;
        }

        .sessions-hero-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .sessions-kpi-group {
            width: 100%;
        }

        .sessions-kpi-pill {
            flex: 1 1 calc(50% - 7px);
        }

        .session-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
        }

        .device-icon-box {
            width: 50px;
            height: 50px;
            font-size: 22px;
        }

        .session-actions-wrap {
            width: 100%;
        }

        .session-actions-wrap form,
        .btn-session-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contentContainer = document.getElementById('sessionsDynamicContent');
    const loadingOverlay = document.getElementById('sessionsLoadingOverlay');
    const toastAlert = document.getElementById('sessionsAjaxAlert');
    const activeCountElem = document.getElementById('activeSessionsCount');
    const totalCountElem = document.getElementById('totalSessionsCount');

    function showLoading() {
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }

    function showToast(message, isError = false) {
        if (!toastAlert) return;
        toastAlert.className = 'sessions-toast-alert' + (isError ? ' is-error' : '');
        const icon = toastAlert.querySelector('.toast-icon');
        if (icon) {
            icon.className = isError ? 'fa-solid fa-triangle-exclamation toast-icon' : 'fa-solid fa-circle-check toast-icon';
        }
        const text = toastAlert.querySelector('.toast-message');
        if (text) text.textContent = message;
        toastAlert.style.display = 'flex';

        setTimeout(() => {
            toastAlert.style.display = 'none';
        }, 4500);
    }

    // Load page via AJAX
    function loadSessionsPage(url, pushState = true) {
        showLoading();
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.html !== undefined && contentContainer) {
                contentContainer.innerHTML = data.html;
            }
            if (data.active !== undefined && activeCountElem) {
                activeCountElem.textContent = data.active;
            }
            if (data.total !== undefined && totalCountElem) {
                totalCountElem.textContent = data.total;
            }
            if (pushState) {
                window.history.pushState({ path: url }, '', url);
            }
            window.scrollTo({ top: contentContainer.offsetTop - 100, behavior: 'smooth' });
        })
        .catch(err => {
            console.error('AJAX Pagination error:', err);
            // Fallback to normal navigation
            window.location.href = url;
        })
        .finally(() => {
            hideLoading();
        });
    }

    // Handle AJAX Pagination clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#sessionsPaginationHolder .page-link');
        if (!link) return;
        const href = link.getAttribute('href');
        if (href && href !== '#' && !link.closest('.disabled') && !link.closest('.active')) {
            e.preventDefault();
            loadSessionsPage(href, true);
        }
    });

    // Handle Browser Back / Forward buttons
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.path) {
            loadSessionsPage(e.state.path, false);
        } else {
            loadSessionsPage(window.location.href, false);
        }
    });

    // Handle AJAX Session Revocation
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.js-revoke-form');
        if (!form) return;

        e.preventDefault();

        const confirmMsg = '{{ __('messages.confirm_revoke_session') }}';
        if (!window.confirm(confirmMsg)) {
            return;
        }

        const isCurrent = form.getAttribute('data-is-current') === '1';
        const sessionId = form.getAttribute('data-session-id');
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (data.success) {
                showToast(data.message || '{{ __('messages.session_revoked_success') }}');

                if (data.active !== undefined && activeCountElem) {
                    activeCountElem.textContent = data.active;
                }
                if (data.total !== undefined && totalCountElem) {
                    totalCountElem.textContent = data.total;
                }

                // Smoothly fade out card
                const card = document.getElementById('session-card-' + sessionId);
                if (card) {
                    card.style.transition = 'all 0.35s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        // Refresh current page via AJAX to keep pagination accurate
                        loadSessionsPage(window.location.href, false);
                    }, 350);
                } else {
                    loadSessionsPage(window.location.href, false);
                }
            } else {
                showToast(data.message || 'Error revoking session', true);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            }
        })
        .catch(err => {
            console.error('Revoke AJAX error:', err);
            // Fallback submit form normally
            form.submit();
        });
    });
});
</script>
@endpush
@endsection
