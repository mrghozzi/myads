{{--
    Theme Customizer Dynamic Head Partial (THEME-07)
    Outputs compiled custom variables CSS directly and provides live real-time postMessage preview support.
--}}
@php
    $activeThemeSlug = $theme_name ?? ($site_settings->styles ?? 'default');
    $themeCustomizer = app(\App\Services\ThemeCustomizerService::class);
    $customCssUrl = $themeCustomizer->getPublicCssUrl($activeThemeSlug);
@endphp

@if($customCssUrl)
    <link id="theme-custom-variables" rel="stylesheet" href="{{ $customCssUrl }}">
@elseif($themeCustomizer->hasCustomizations($activeThemeSlug))
    <style id="theme-custom-variables">
        {!! $themeCustomizer->getCompiledCss($activeThemeSlug) !!}
    </style>
@endif

<script>
    window.addEventListener('message', function(event) {
        if (!event.data || event.data.type !== 'MYADS_THEME_CUSTOMIZER_UPDATE') return;
        const v = event.data.variables;
        if (!v) return;
        let styleTag = document.getElementById('theme-customizer-live-style');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'theme-customizer-live-style';
            document.head.appendChild(styleTag);
        }
        const fontFamily = event.data.fontFamily || (v.font_family ? `'${v.font_family}', sans-serif` : 'inherit');
        styleTag.innerHTML = `
            :root, html, body, [data-theme="css"], [data-theme="css_d"], [data-bs-theme="light"], [data-bs-theme="dark"] {
                --theme-primary: ${v.primary_color};
                --theme-secondary: ${v.secondary_color};
                --theme-bg: ${v.bg_color};
                --theme-card-bg: ${v.card_bg};
                --theme-text: ${v.text_color};
                --theme-text-muted: ${v.text_muted};
                --theme-header-bg: ${v.header_bg};
                --theme-radius: ${v.border_radius}px;
                --theme-font-family: ${fontFamily};
                --theme-glass-blur: ${v.glass_blur}px;
                --theme-glass-opacity: ${v.glass_opacity};
                --primary: ${v.primary_color} !important;
                --secondary: ${v.secondary_color} !important;
                --bg-base: ${v.bg_color} !important;
                --bg-surface: ${v.card_bg} !important;
                --text-main: ${v.text_color} !important;
                --text-muted: ${v.text_muted} !important;
                --bs-primary: ${v.primary_color} !important;
                --bs-secondary: ${v.secondary_color} !important;
                --bs-body-bg: ${v.bg_color} !important;
                --bs-body-color: ${v.text_color} !important;
                --bs-card-bg: ${v.card_bg} !important;
                --bs-border-radius: ${v.border_radius}px !important;
            }
            body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6,
            .widget-box-title, .user-stat-title, .user-short-description-title,
            .section-title, .banner-promo-title, .page-title, .navigation-widget-section-title,
            label, .button, .btn, input, select, textarea, .form-title, .menu-main-item-link,
            .form-input, .form-select, .form-textarea, .simple-dropdown-link, .interactive-input input {
                font-family: ${fontFamily} !important;
            }
            body {
                background-color: ${v.bg_color} !important;
                color: ${v.text_color} !important;
            }
            .header, .navbar, .top-header, .oauth-shell-topbar, .site-header, header.header, .header-navigation {
                background-color: ${v.header_bg} !important;
            }
            .button.primary, .btn-primary, .button-primary, .action-request.accept, .banner-promo-button,
            .dropdown-box-button.secondary, .btn-main, .quick-post-footer-action.primary, .action-item.active, .tab-box-option.active {
                background-color: ${v.primary_color} !important;
                border-color: ${v.primary_color} !important;
                color: #ffffff !important;
            }
            .button.secondary, .btn-secondary, .button-secondary, .tag-item.secondary, .badge-secondary, .highlight-secondary {
                background-color: ${v.secondary_color} !important;
                border-color: ${v.secondary_color} !important;
                color: #ffffff !important;
            }
            .widget-box, .simple-accordion, .card, .modern-card, .user-preview, .post-preview,
            .forum-post, .discussion-preview, .product-preview, .banner-box, .account-stat-box,
            .featured-stat-box, .table-wrap, .stat-box, .stat-card, .info-box, .content-box,
            .settings-box, .quick-post, .notification-box {
                background-color: ${v.card_bg} !important;
                border-radius: ${v.border_radius}px !important;
            }
            .button, .btn, .form-input, .form-select, .form-textarea, .interactive-input, .badge, .tag-item, .alert, .dropdown-box {
                border-radius: min(${v.border_radius}px, 16px) !important;
            }
            .text-primary, a.text-primary, .color-primary, .highlight-primary,
            .navigation-widget-section-link.active, .menu-main-item-link.active {
                color: ${v.primary_color} !important;
            }
            .text-muted, .color-text-alt, .user-stat-text, .widget-box-text, .information-line-title, .paragraph.text-muted, .text-secondary {
                color: ${v.text_muted} !important;
            }
            .welcome-hero, .welcome-section, .hero-section {
                background-color: ${v.bg_color} !important;
            }
            .hero-title, .section-main-title {
                color: ${v.text_color} !important;
                font-family: ${fontFamily} !important;
            }
            .hero-gradient-text, .gradient-brand-text {
                background: linear-gradient(135deg, ${v.primary_color} 0%, ${v.secondary_color} 100%) !important;
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: transparent !important;
            }
        `;
    });
</script>
