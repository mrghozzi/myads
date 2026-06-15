{{--
    Unified Standalone Footer — shared across welcome, auth, and legal pages.
    Includes its own scoped CSS so it can be @included in any standalone view.

    Usage: @include('theme::partials._standalone_footer')
--}}
<style>
    /* --- Standalone Footer --- */
    .standalone-footer {
        padding: 50px 24px;
        text-align: center;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        background: var(--bg-surface);
        position: relative;
        z-index: 10;
        margin-top: auto;
    }
    [data-theme="css"] .standalone-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    .standalone-footer p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }
    .standalone-footer .footer-links {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .standalone-footer .footer-links a {
        color: var(--text-muted);
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s;
    }
    .standalone-footer .footer-links a i {
        font-size: 1.1rem;
    }
    .standalone-footer .footer-links a:hover {
        color: var(--primary);
    }
</style>

<footer class="standalone-footer">
    <div class="footer-links">
        <a href="{{ url('/sitemap.xml') }}"><i class="fa-solid fa-sitemap"></i> {{ __('messages.sitemap') ?? 'Sitemap' }}</a>
        <a href="{{ url('/developer') }}"><i class="fa-solid fa-code"></i> {{ __('messages.developers') ?? 'Developers' }}</a>
        <a href="{{ url('/privacy') }}"><i class="fa-solid fa-shield-halved"></i> {{ __('messages.privacy_policy') ?? 'Privacy Policy' }}</a>
        <a href="{{ url('/terms') }}"><i class="fa-solid fa-file-contract"></i> {{ __('messages.terms_conditions') ?? 'Terms & Conditions' }}</a>
        <a href="{{ url('/refund') }}"><i class="fa-solid fa-arrow-rotate-left"></i> {{ __('messages.refund_policy') ?? 'Refund Policy' }}</a>
    </div>
    <p>&copy; {{ date('Y') }} {{ $site_settings->titer ?? 'MyAds' }}. {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}</p>
    <p style="margin-top: 10px; font-size: 0.85rem; opacity: 0.7;">Powered by <strong>MyAds</strong> | v{{ \App\Support\SystemVersion::CURRENT ?? '4.3.7' }}</p>
</footer>
