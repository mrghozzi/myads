{{-- ===== GLOBAL FOOTER ===== --}}
<style>
.site-footer {
    background: #1a1a2e;
    padding: 60px 20px;
    text-align: center;
    border-top: 1px solid rgba(255,255,255,0.05);
    width: 100%;
    position: relative;
    z-index: 10;
    margin-top: 40px;
}
[data-theme="css"] .site-footer {
    background: #fdfdff;
    border-top: 1px solid #edf2f7;
}
.site-footer-links {
    margin-bottom: 25px;
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}
.site-footer-links a {
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
[data-theme="css"] .site-footer-links a {
    color: #7b819d;
}
.site-footer-links a:hover {
    color: #fff;
    transform: translateY(-2px);
}
[data-theme="css"] .site-footer-links a:hover {
    color: #615dfa;
}
.site-footer-links a i {
    font-size: 0.85rem;
    opacity: 0.7;
}
.site-footer p {
    color: rgba(255,255,255,0.3);
    font-size: 0.85rem;
    margin-top: 10px;
}
[data-theme="css"] .site-footer p {
    color: #adb2cb;
}
.site-footer-brand {
    opacity: 0.5; 
    margin-top: 8px; 
    font-size: 0.75rem; 
    letter-spacing: 0.02em;
}
[data-theme="css"] .site-footer-brand {
    opacity: 0.8;
}

/* Scroll Animations */
.footer-fade-up {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.footer-fade-up.footer-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 768px) {
    .site-footer {
        padding: 40px 16px;
    }
    .site-footer-links {
        gap: 20px;
    }
}
</style>

<div class="site-footer">
    <div class="site-footer-links footer-fade-up">
        <a href="{{ route('sitemap.xml') }}">
            <i class="fa-solid fa-sitemap"></i>
            {{ __('messages.sitemap') }}
        </a>
        <a href="{{ route('developer.index') }}">
            <i class="fa-solid fa-code"></i>
            {{ __('messages.developers') }}
        </a>
        <a href="{{ route('privacy') }}">
            <i class="fa-solid fa-shield-halved"></i>
            {{ __('messages.privacy_policy') }}
        </a>
        <a href="{{ route('terms') }}">
            <i class="fa-solid fa-file-contract"></i>
            {{ __('messages.terms_conditions') }}
        </a>
    </div>
    <p class="footer-fade-up">&copy; {{ date('Y') }} {{ $site_settings->titer ?? 'MyAds' }}. {{ __('messages.all_rights_reserved') }}</p>
    <p class="footer-fade-up site-footer-brand">
        Powered by <strong>MyAds SEO Engine</strong> | {{ \App\Support\SystemVersion::tag() }}
    </p>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var footerObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('footer-visible');
                footerObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.footer-fade-up').forEach(function(el) {
        footerObserver.observe(el);
    });
});
</script>
@endpush
