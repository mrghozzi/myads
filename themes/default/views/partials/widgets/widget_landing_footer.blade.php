@php
    $config = [];
    if (!empty($widget->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $siteTitle = $site_settings->titer ?? config('app.name', 'Platform');
    $showTitle = (bool) ($config['show_title'] ?? false);
    $showHelp = (bool) ($config['show_help'] ?? true);
    $showSitemap = (bool) ($config['show_sitemap'] ?? true);
    $showDeveloper = (bool) ($config['show_developer'] ?? true);
    $showPrivacy = (bool) ($config['show_privacy'] ?? true);
    $showTerms = (bool) ($config['show_terms'] ?? true);
    $showRefund = (bool) ($config['show_refund'] ?? true);
    $showSocial = (bool) ($config['show_social'] ?? false);
    $showPoweredBy = (bool) ($config['show_powered_by'] ?? true);
    
    $layoutStyle = $config['layout_style'] ?? 'centered';
    $bgStyle = $config['background_style'] ?? 'card';

    // Social Links Fallbacks
    $facebookUrl = !empty($config['facebook_url']) ? $config['facebook_url'] : ($site_settings->facebook ?? '');
    $twitterUrl = !empty($config['twitter_url']) ? $config['twitter_url'] : ($site_settings->twitter ?? '');
    $linkedinUrl = !empty($config['linkedin_url']) ? $config['linkedin_url'] : ($site_settings->linkedin ?? '');
    $telegramUrl = $config['telegram_url'] ?? '';
    $youtubeUrl = $config['youtube_url'] ?? '';
    $githubUrl = $config['github_url'] ?? '';

    // Custom Extra Links
    $customLinks = [];
    if (!empty($config['custom_links_text'])) {
        $lines = explode("\n", str_replace("\r", "", $config['custom_links_text']));
        foreach ($lines as $line) {
            $parts = explode("|", trim($line));
            if (count($parts) >= 2) {
                $customLinks[] = [
                    'label' => trim($parts[0]),
                    'url' => trim($parts[1]),
                ];
            }
        }
    }

    // Copyright Text
    $copyrightText = !empty($config['custom_copyright'])
        ? str_replace([':year', ':site'], [date('Y'), $siteTitle], $config['custom_copyright'])
        : ('&copy; ' . date('Y') . ' ' . $siteTitle . '. ' . (__('messages.all_rights_reserved') ?? 'All rights reserved.'));

    // Powered By Text
    $poweredByText = !empty($config['powered_by_text'])
        ? str_replace([':site', ':version'], [$siteTitle, \App\Support\SystemVersion::tag()], $config['powered_by_text'])
        : ('Powered by <strong>' . $siteTitle . ' SEO Engine</strong> | ' . \App\Support\SystemVersion::tag());
@endphp

<div class="widget-box landing-footer-widget-box landing-footer-bg-{{ $bgStyle }}">
    @if($showTitle && !empty($widget->name))
        <p class="widget-box-title">{{ $widget->name }}</p>
    @endif

    <div class="widget-box-content landing-footer-layout-{{ $layoutStyle }}">
        
        <!-- Social Networks Bar (If Enabled) -->
        @if($showSocial && ($facebookUrl || $twitterUrl || $linkedinUrl || $telegramUrl || $youtubeUrl || $githubUrl))
            <div class="landing-footer-social">
                @if($facebookUrl)
                    <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn facebook" title="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                @endif
                @if($twitterUrl)
                    <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn twitter" title="Twitter / X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                @endif
                @if($linkedinUrl)
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn linkedin" title="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                @endif
                @if($telegramUrl)
                    <a href="{{ $telegramUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn telegram" title="Telegram">
                        <i class="fa-brands fa-telegram"></i>
                    </a>
                @endif
                @if($youtubeUrl)
                    <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn youtube" title="YouTube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                @endif
                @if($githubUrl)
                    <a href="{{ $githubUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn github" title="GitHub">
                        <i class="fa-brands fa-github"></i>
                    </a>
                @endif
            </div>
        @endif

        <!-- Primary Navigation Links -->
        <div class="landing-footer-links">
            @if($showHelp)
                <a href="{{ route('help.index') }}" class="landing-footer-link">
                    <i class="fa-solid fa-circle-question"></i>
                    <span>{{ __('help.help_center') ?? 'مركز المساعدة' }}</span>
                </a>
            @endif

            @if($showSitemap)
                <a href="{{ route('sitemap.xml') }}" class="landing-footer-link">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>{{ __('messages.sitemap') }}</span>
                </a>
            @endif

            @if($showDeveloper)
                <a href="{{ route('developer.index') }}" class="landing-footer-link">
                    <i class="fa-solid fa-code"></i>
                    <span>{{ __('messages.developers') }}</span>
                </a>
            @endif

            @if($showPrivacy)
                <a href="{{ route('privacy') }}" class="landing-footer-link">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ __('messages.privacy_policy') }}</span>
                </a>
            @endif

            @if($showTerms)
                <a href="{{ route('terms') }}" class="landing-footer-link">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>{{ __('messages.terms_conditions') }}</span>
                </a>
            @endif

            @if($showRefund)
                <a href="{{ route('refund') }}" class="landing-footer-link">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>{{ __('messages.refund_policy') }}</span>
                </a>
            @endif

            <!-- Custom Additional Links -->
            @foreach($customLinks as $cLink)
                <a href="{{ $cLink['url'] }}" class="landing-footer-link custom-link">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem;"></i>
                    <span>{{ $cLink['label'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Copyright & Powered By -->
        <div class="landing-footer-copyright">
            <p class="landing-footer-copy-text">{!! $copyrightText !!}</p>
            @if($showPoweredBy)
                <p class="landing-footer-powered">
                    {!! $poweredByText !!}
                </p>
            @endif
        </div>
    </div>

    <style>
        .landing-footer-widget-box {
            position: relative;
            transition: all 0.3s ease;
        }

        .landing-footer-bg-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #eaeaf5);
            border-radius: var(--myads-radius-lg, 16px);
            padding: 24px 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        }

        .landing-footer-bg-subtle {
            background: var(--myads-glass-bg, rgba(255, 255, 255, 0.7));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--myads-glass-border, rgba(255, 255, 255, 0.6));
            border-radius: var(--myads-radius-lg, 16px);
            padding: 24px 20px;
        }

        .landing-footer-bg-transparent {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 16px 8px;
        }

        /* Layout Variations */
        .landing-footer-layout-centered {
            text-align: center;
        }

        .landing-footer-layout-columns {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .landing-footer-layout-compact .landing-footer-links {
            gap: 8px 12px;
        }

        /* Social Media Icons Bar */
        .landing-footer-social {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .social-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: #64748b;
            background: rgba(97, 93, 250, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }

        .social-icon-btn:hover {
            transform: translateY(-3px) scale(1.08);
            color: #ffffff;
        }

        .social-icon-btn.facebook:hover { background: #1877f2; border-color: #1877f2; box-shadow: 0 4px 12px rgba(24, 119, 242, 0.35); }
        .social-icon-btn.twitter:hover { background: #000000; border-color: #000000; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35); }
        .social-icon-btn.linkedin:hover { background: #0a66c2; border-color: #0a66c2; box-shadow: 0 4px 12px rgba(10, 102, 194, 0.35); }
        .social-icon-btn.telegram:hover { background: #229ed9; border-color: #229ed9; box-shadow: 0 4px 12px rgba(34, 158, 217, 0.35); }
        .social-icon-btn.youtube:hover { background: #ff0000; border-color: #ff0000; box-shadow: 0 4px 12px rgba(255, 0, 0, 0.35); }
        .social-icon-btn.github:hover { background: #24292e; border-color: #24292e; box-shadow: 0 4px 12px rgba(36, 41, 46, 0.35); }

        /* Links Bar */
        .landing-footer-links {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px 18px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .landing-footer-link {
            color: var(--myads-text-main, #3e3f5e);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 10px;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .landing-footer-link i {
            font-size: 0.85rem;
            color: var(--myads-primary, #615dfa);
            transition: transform 0.2s ease;
        }

        .landing-footer-link:hover {
            color: var(--myads-primary, #615dfa);
            background: rgba(97, 93, 250, 0.08);
            transform: translateY(-2px);
        }

        .landing-footer-link:hover i {
            transform: scale(1.15);
        }

        /* Copyright & Powered By */
        .landing-footer-copyright {
            padding-top: 14px;
            border-top: 1px solid var(--myads-surface-border, rgba(0, 0, 0, 0.05));
        }

        .landing-footer-copy-text {
            color: var(--myads-text-muted, #8f91ac);
            font-size: 0.82rem;
            margin: 0;
            line-height: 1.6;
        }

        .landing-footer-powered {
            color: var(--myads-text-light, #adafca);
            font-size: 0.74rem;
            margin-top: 6px;
            letter-spacing: 0.02em;
            opacity: 0.85;
        }

        .landing-footer-powered strong {
            color: var(--myads-text-main, #3e3f5e);
            font-weight: 700;
        }

        /* Dark Mode */
        [data-theme="css_d"] .landing-footer-bg-card {
            background: var(--myads-surface, #1d2333);
            border-color: var(--myads-surface-border, #2f3749);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        [data-theme="css_d"] .landing-footer-bg-subtle {
            background: rgba(29, 35, 51, 0.7);
            border-color: rgba(255, 255, 255, 0.08);
        }

        [data-theme="css_d"] .landing-footer-link {
            color: var(--myads-text-muted, #9aa4bf);
        }

        [data-theme="css_d"] .landing-footer-link:hover {
            color: #ffffff;
            background: rgba(97, 93, 250, 0.15);
        }

        [data-theme="css_d"] .social-icon-btn {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.08);
            color: #9aa4bf;
        }

        [data-theme="css_d"] .landing-footer-copyright {
            border-color: rgba(255, 255, 255, 0.06);
        }

        [data-theme="css_d"] .landing-footer-copy-text {
            color: #7a86a3;
        }

        [data-theme="css_d"] .landing-footer-powered {
            color: #5d6780;
        }

        [data-theme="css_d"] .landing-footer-powered strong {
            color: #b0bad4;
        }
    </style>
</div>
