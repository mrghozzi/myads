<div class="widget-box">
    @if($widget->name)
        <p class="widget-box-title">{{ $widget->name }}</p>
    @endif

    <div class="widget-box-content">
        <div class="landing-footer-links">
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
        <div class="landing-footer-copyright">
            <p>&copy; {{ date('Y') }} {{ $site_settings->titer ?? 'MyAds' }}. {{ __('messages.all_rights_reserved') }}</p>
            <p class="landing-footer-powered">
                Powered by <strong>MyAds SEO Engine</strong> | {{ \App\Support\SystemVersion::tag() }}
            </p>
        </div>
    </div>

    <style>
        .landing-footer-links {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .landing-footer-links a {
            color: #3e3f5e; /* Default dark text for light mode */
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .landing-footer-links a:hover {
            color: #615dfa;
            transform: translateY(-2px);
        }
        .landing-footer-links a i {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        .landing-footer-copyright p {
            color: #adafca; /* Muted text for light mode */
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.6;
            text-align: center;
        }
        .landing-footer-powered {
            margin-top: 5px !important;
            opacity: 0.6;
            font-size: 0.7rem !important;
            letter-spacing: 0.02em;
        }
        
        /* Dark Mode Adjustments */
        [data-theme="css_d"] .landing-footer-links a {
            color: #9aa4bf;
        }
        [data-theme="css_d"] .landing-footer-links a:hover {
            color: #fff;
        }
        [data-theme="css_d"] .landing-footer-copyright p {
            color: #667191;
        }
    </style>
</div>
