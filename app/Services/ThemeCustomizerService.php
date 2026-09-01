<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ThemeCustomizerService
{
    public const OPTION_TYPE = 'theme_customizer';

    public const SUPPORTED_FONTS = [
        'Inter' => [
            'name' => 'Inter (Modern Clean)',
            'family' => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'google_font' => 'Inter:wght@300;400;500;600;700;800',
        ],
        'Cairo' => [
            'name' => 'Cairo (Arabic & Latin)',
            'family' => "'Cairo', 'Inter', -apple-system, sans-serif",
            'google_font' => 'Cairo:wght@300;400;500;600;700;800',
        ],
        'Tajawal' => [
            'name' => 'Tajawal (Arabic Sleek)',
            'family' => "'Tajawal', 'Inter', -apple-system, sans-serif",
            'google_font' => 'Tajawal:wght@300;400;500;700;800',
        ],
        'Roboto' => [
            'name' => 'Roboto (Classic Tech)',
            'family' => "'Roboto', -apple-system, BlinkMacSystemFont, sans-serif",
            'google_font' => 'Roboto:wght@300;400;500;700',
        ],
        'Outfit' => [
            'name' => 'Outfit (Contemporary Geometric)',
            'family' => "'Outfit', 'Inter', -apple-system, sans-serif",
            'google_font' => 'Outfit:wght@300;400;500;600;700',
        ],
        'System' => [
            'name' => 'System Native UI',
            'family' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
            'google_font' => null,
        ],
    ];

    public const PALETTE_PRESETS = [
        'default' => [
            'name' => 'MyAds Violet',
            'primary_color' => '#615dfa',
            'secondary_color' => '#23d2e2',
            'bg_color' => '#f8faff',
            'card_bg' => '#ffffff',
            'text_color' => '#3e3f5e',
            'text_muted' => '#8f91ac',
            'header_bg' => '#615dfa',
            'border_radius' => 12,
            'font_family' => 'Inter',
            'glass_blur' => 16,
            'glass_opacity' => 0.85,
        ],
        'emerald' => [
            'name' => 'Emerald Forest',
            'primary_color' => '#059669',
            'secondary_color' => '#10b981',
            'bg_color' => '#f0fdf4',
            'card_bg' => '#ffffff',
            'text_color' => '#1e293b',
            'text_muted' => '#64748b',
            'header_bg' => '#059669',
            'border_radius' => 12,
            'font_family' => 'Inter',
            'glass_blur' => 16,
            'glass_opacity' => 0.85,
        ],
        'ocean' => [
            'name' => 'Deep Ocean Blue',
            'primary_color' => '#0284c7',
            'secondary_color' => '#38bdf8',
            'bg_color' => '#f0f9ff',
            'card_bg' => '#ffffff',
            'text_color' => '#0f172a',
            'text_muted' => '#64748b',
            'header_bg' => '#0284c7',
            'border_radius' => 14,
            'font_family' => 'Inter',
            'glass_blur' => 16,
            'glass_opacity' => 0.85,
        ],
        'sunset' => [
            'name' => 'Sunset Amber',
            'primary_color' => '#ea580c',
            'secondary_color' => '#f59e0b',
            'bg_color' => '#fffbeb',
            'card_bg' => '#ffffff',
            'text_color' => '#292524',
            'text_muted' => '#78716c',
            'header_bg' => '#ea580c',
            'border_radius' => 12,
            'font_family' => 'Inter',
            'glass_blur' => 16,
            'glass_opacity' => 0.85,
        ],
        'cyber' => [
            'name' => 'Cyber Neon Purple',
            'primary_color' => '#7c3aed',
            'secondary_color' => '#ec4899',
            'bg_color' => '#faf5ff',
            'card_bg' => '#ffffff',
            'text_color' => '#1e1b4b',
            'text_muted' => '#6b7280',
            'header_bg' => '#7c3aed',
            'border_radius' => 16,
            'font_family' => 'Outfit',
            'glass_blur' => 20,
            'glass_opacity' => 0.88,
        ],
        'crimson' => [
            'name' => 'Crimson Luxury',
            'primary_color' => '#dc2626',
            'secondary_color' => '#f43f5e',
            'bg_color' => '#fef2f2',
            'card_bg' => '#ffffff',
            'text_color' => '#1c1917',
            'text_muted' => '#78716c',
            'header_bg' => '#dc2626',
            'border_radius' => 10,
            'font_family' => 'Inter',
            'glass_blur' => 14,
            'glass_opacity' => 0.90,
        ],
    ];

    /**
     * Get default tokens for a given theme.
     */
    public function getDefaults(string $themeSlug = 'default'): array
    {
        return self::PALETTE_PRESETS['default'];
    }

    /**
     * Get active variables for a theme (merged with defaults).
     */
    public function getVariables(string $themeSlug = 'default'): array
    {
        $cacheKey = 'theme_customizer_vars_' . $themeSlug;

        return Cache::remember($cacheKey, 3600, function () use ($themeSlug) {
            $defaults = $this->getDefaults($themeSlug);
            $option = Option::query()
                ->where('o_type', self::OPTION_TYPE)
                ->where('name', 'theme_vars_' . $themeSlug)
                ->first();

            if (!$option || empty($option->o_valuer)) {
                return $defaults;
            }

            $decoded = json_decode($option->o_valuer, true);
            if (!is_array($decoded)) {
                return $defaults;
            }

            return array_merge($defaults, $decoded);
        });
    }

    /**
     * Check if a theme has custom variables saved.
     */
    public function hasCustomizations(string $themeSlug = 'default'): bool
    {
        $option = Option::query()
            ->where('o_type', self::OPTION_TYPE)
            ->where('name', 'theme_vars_' . $themeSlug)
            ->first();

        return $option && !empty($option->o_valuer);
    }

    /**
     * Save customized theme variables.
     */
    public function saveVariables(string $themeSlug, array $input): array
    {
        $defaults = $this->getDefaults($themeSlug);
        $sanitized = [];

        // Color Sanitization
        $colorFields = ['primary_color', 'secondary_color', 'bg_color', 'card_bg', 'text_color', 'text_muted', 'header_bg'];
        foreach ($colorFields as $field) {
            if (isset($input[$field])) {
                $val = trim((string) $input[$field]);
                $sanitized[$field] = $this->sanitizeHexColor($val, $defaults[$field]);
            } else {
                $sanitized[$field] = $defaults[$field];
            }
        }

        // Border radius (0 - 32 px)
        $radius = isset($input['border_radius']) ? (int) $input['border_radius'] : (int) $defaults['border_radius'];
        $sanitized['border_radius'] = max(0, min(32, $radius));

        // Font family
        $font = isset($input['font_family']) && array_key_exists((string) $input['font_family'], self::SUPPORTED_FONTS)
            ? (string) $input['font_family']
            : $defaults['font_family'];
        $sanitized['font_family'] = $font;

        // Glass blur (0 - 40 px)
        $blur = isset($input['glass_blur']) ? (int) $input['glass_blur'] : (int) $defaults['glass_blur'];
        $sanitized['glass_blur'] = max(0, min(40, $blur));

        // Glass opacity (0.1 - 1.0)
        $opacity = isset($input['glass_opacity']) ? (float) $input['glass_opacity'] : (float) $defaults['glass_opacity'];
        $sanitized['glass_opacity'] = max(0.1, min(1.0, round($opacity, 2)));

        // Persist to options table
        Option::updateOrCreate(
            [
                'o_type' => self::OPTION_TYPE,
                'name' => 'theme_vars_' . $themeSlug,
            ],
            [
                'o_valuer' => json_encode($sanitized, JSON_UNESCAPED_UNICODE),
            ]
        );

        // Clear Cache
        Cache::forget('theme_customizer_vars_' . $themeSlug);
        Cache::forget('theme_customizer_css_' . $themeSlug);

        // Compile and write CSS file to disk
        $compiledCss = $this->compileCss($themeSlug, $sanitized);
        $this->writeCssFile($themeSlug, $compiledCss);

        return $sanitized;
    }

    /**
     * Compile CSS variables into a complete stylesheet.
     */
    public function compileCss(string $themeSlug, ?array $variables = null): string
    {
        $vars = $variables ?? $this->getVariables($themeSlug);
        $fontKey = $vars['font_family'] ?? 'Inter';
        $fontDef = self::SUPPORTED_FONTS[$fontKey] ?? self::SUPPORTED_FONTS['Inter'];
        $fontFamily = $fontDef['family'];

        $primary = $vars['primary_color'];
        $secondary = $vars['secondary_color'];
        $bg = $vars['bg_color'];
        $cardBg = $vars['card_bg'];
        $text = $vars['text_color'];
        $textMuted = $vars['text_muted'];
        $headerBg = $vars['header_bg'];
        $radius = (int) $vars['border_radius'];
        $blur = (int) $vars['glass_blur'];
        $opacity = (float) $vars['glass_opacity'];

        // Convert hex to rgb for rgba usage
        $primaryRgb = $this->hexToRgb($primary);
        $secondaryRgb = $this->hexToRgb($secondary);
        $bgRgb = $this->hexToRgb($bg);
        $cardBgRgb = $this->hexToRgb($cardBg);

        $googleImport = '';
        if (!empty($fontDef['google_font'])) {
            $googleImport = "@import url('https://fonts.googleapis.com/css2?family=" . urlencode($fontDef['google_font']) . "&display=swap');\n";
        }

        return "/* Auto-generated by MYADS Theme Customizer v" . \App\Support\SystemVersion::CURRENT . " */\n"
            . $googleImport
            . ":root {\n"
            . "    --theme-primary: {$primary};\n"
            . "    --theme-primary-rgb: {$primaryRgb};\n"
            . "    --theme-secondary: {$secondary};\n"
            . "    --theme-secondary-rgb: {$secondaryRgb};\n"
            . "    --theme-bg: {$bg};\n"
            . "    --theme-bg-rgb: {$bgRgb};\n"
            . "    --theme-card-bg: {$cardBg};\n"
            . "    --theme-card-bg-rgb: {$cardBgRgb};\n"
            . "    --theme-text: {$text};\n"
            . "    --theme-text-muted: {$textMuted};\n"
            . "    --theme-header-bg: {$headerBg};\n"
            . "    --theme-radius: {$radius}px;\n"
            . "    --theme-font-family: {$fontFamily};\n"
            . "    --theme-glass-blur: {$blur}px;\n"
            . "    --theme-glass-opacity: {$opacity};\n"
            . "    --primary: {$primary};\n"
            . "    --primary-glow: rgba({$primaryRgb}, 0.4);\n"
            . "    --secondary: {$secondary};\n"
            . "    --bg-base: {$bg};\n"
            . "    --bg-surface: {$cardBg};\n"
            . "    --text-main: {$text};\n"
            . "    --text-muted: {$textMuted};\n"
            . "    --bs-primary: {$primary};\n"
            . "    --bs-primary-rgb: {$primaryRgb};\n"
            . "    --bs-secondary: {$secondary};\n"
            . "    --bs-secondary-rgb: {$secondaryRgb};\n"
            . "    --bs-body-bg: {$bg};\n"
            . "    --bs-body-color: {$text};\n"
            . "    --bs-card-bg: {$cardBg};\n"
            . "    --bs-border-radius: {$radius}px;\n"
            . "}\n\n"
            . "html, body, [data-theme=\"css\"], [data-theme=\"css_d\"], [data-bs-theme=\"light\"], [data-bs-theme=\"dark\"] {\n"
            . "    --theme-primary: {$primary};\n"
            . "    --theme-primary-rgb: {$primaryRgb};\n"
            . "    --theme-secondary: {$secondary};\n"
            . "    --theme-secondary-rgb: {$secondaryRgb};\n"
            . "    --theme-bg: {$bg};\n"
            . "    --theme-bg-rgb: {$bgRgb};\n"
            . "    --theme-card-bg: {$cardBg};\n"
            . "    --theme-card-bg-rgb: {$cardBgRgb};\n"
            . "    --theme-text: {$text};\n"
            . "    --theme-text-muted: {$textMuted};\n"
            . "    --theme-header-bg: {$headerBg};\n"
            . "    --theme-radius: {$radius}px;\n"
            . "    --theme-font-family: {$fontFamily};\n"
            . "    --theme-glass-blur: {$blur}px;\n"
            . "    --theme-glass-opacity: {$opacity};\n"
            . "    --primary: {$primary} !important;\n"
            . "    --primary-glow: rgba({$primaryRgb}, 0.4) !important;\n"
            . "    --secondary: {$secondary} !important;\n"
            . "    --bg-base: {$bg} !important;\n"
            . "    --bg-surface: {$cardBg} !important;\n"
            . "    --text-main: {$text} !important;\n"
            . "    --text-muted: {$textMuted} !important;\n"
            . "    --bs-primary: {$primary} !important;\n"
            . "    --bs-primary-rgb: {$primaryRgb} !important;\n"
            . "    --bs-secondary: {$secondary} !important;\n"
            . "    --bs-secondary-rgb: {$secondaryRgb} !important;\n"
            . "    --bs-body-bg: {$bg} !important;\n"
            . "    --bs-body-color: {$text} !important;\n"
            . "    --bs-card-bg: {$cardBg} !important;\n"
            . "    --bs-border-radius: {$radius}px !important;\n"
            . "}\n\n"
            . "body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6,\n"
            . ".widget-box-title, .user-stat-title, .user-short-description-title,\n"
            . ".section-title, .banner-promo-title, .page-title, .navigation-widget-section-title,\n"
            . "label, .button, .btn, input, select, textarea, .form-title, .menu-main-item-link,\n"
            . ".form-input, .form-select, .form-textarea, .simple-dropdown-link, .interactive-input input {\n"
            . "    font-family: var(--theme-font-family) !important;\n"
            . "}\n\n"
            . "body {\n"
            . "    background-color: var(--theme-bg) !important;\n"
            . "    color: var(--theme-text) !important;\n"
            . "}\n\n"
            . ".header, .navbar, .top-header, .oauth-shell-topbar, .site-header, header.header, .header-navigation {\n"
            . "    background-color: var(--theme-header-bg) !important;\n"
            . "}\n\n"
            . ".button.primary, .btn-primary, .button-primary, .action-request.accept, .banner-promo-button,\n"
            . ".dropdown-box-button.secondary, .btn-main, .quick-post-footer-action.primary, .action-item.active, .tab-box-option.active {\n"
            . "    background-color: var(--theme-primary) !important;\n"
            . "    border-color: var(--theme-primary) !important;\n"
            . "    color: #ffffff !important;\n"
            . "}\n\n"
            . ".button.primary:hover, .btn-primary:hover, .button-primary:hover, .action-request.accept:hover, .banner-promo-button:hover {\n"
            . "    filter: brightness(0.92);\n"
            . "}\n\n"
            . ".button.secondary, .btn-secondary, .button-secondary, .tag-item.secondary, .badge-secondary, .highlight-secondary {\n"
            . "    background-color: var(--theme-secondary) !important;\n"
            . "    border-color: var(--theme-secondary) !important;\n"
            . "    color: #ffffff !important;\n"
            . "}\n\n"
            . ".button.secondary:hover, .btn-secondary:hover {\n"
            . "    filter: brightness(0.92);\n"
            . "}\n\n"
            . ".widget-box, .simple-accordion, .card, .modern-card, .user-preview, .post-preview,\n"
            . ".forum-post, .discussion-preview, .product-preview, .banner-box, .account-stat-box,\n"
            . ".featured-stat-box, .table-wrap, .stat-box, .stat-card, .info-box, .content-box,\n"
            . ".settings-box, .quick-post, .notification-box {\n"
            . "    background-color: var(--theme-card-bg) !important;\n"
            . "    border-radius: var(--theme-radius) !important;\n"
            . "}\n\n"
            . ".button, .btn, .form-input, .form-select, .form-textarea, .interactive-input, .badge, .tag-item, .alert, .dropdown-box {\n"
            . "    border-radius: min(var(--theme-radius), 16px) !important;\n"
            . "}\n\n"
            . ".text-primary, a.text-primary, .color-primary, .highlight-primary,\n"
            . ".navigation-widget-section-link.active, .menu-main-item-link.active {\n"
            . "    color: var(--theme-primary) !important;\n"
            . "}\n\n"
            . ".text-muted, .color-text-alt, .user-stat-text, .widget-box-text, .information-line-title, .paragraph.text-muted, .text-secondary {\n"
            . "    color: var(--theme-text-muted) !important;\n"
            . "}\n\n"
            . ".glassmorphic, .header-dropdown, .dropdown-box, .preview-container, .modal-content.glass, .popup-box.glass {\n"
            . "    backdrop-filter: blur(var(--theme-glass-blur)) !important;\n"
            . "    -webkit-backdrop-filter: blur(var(--theme-glass-blur)) !important;\n"
            . "    background-color: rgba(var(--theme-card-bg-rgb), var(--theme-glass-opacity)) !important;\n"
            . "}\n\n"
            . ".welcome-hero, .welcome-section, .hero-section {\n"
            . "    background-color: var(--theme-bg) !important;\n"
            . "}\n"
            . ".hero-title, .section-main-title {\n"
            . "    color: var(--theme-text) !important;\n"
            . "    font-family: var(--theme-font-family) !important;\n"
            . "}\n"
            . ".hero-gradient-text, .gradient-brand-text {\n"
            . "    background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%) !important;\n"
            . "    -webkit-background-clip: text !important;\n"
            . "    -webkit-text-fill-color: transparent !important;\n"
            . "}\n";
    }

    /**
     * Reset customizations for a given theme.
     */
    public function reset(string $themeSlug): bool
    {
        Option::query()
            ->where('o_type', self::OPTION_TYPE)
            ->where('name', 'theme_vars_' . $themeSlug)
            ->delete();

        Cache::forget('theme_customizer_vars_' . $themeSlug);
        Cache::forget('theme_customizer_css_' . $themeSlug);

        $filePath = public_path("themes/{$themeSlug}/custom_variables.css");
        if (File::exists($filePath)) {
            try {
                File::delete($filePath);
            } catch (\Throwable $e) {
                Log::warning("Could not delete custom theme CSS: " . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Get compiled CSS (cached in memory or dynamically compiled).
     */
    public function getCompiledCss(string $themeSlug = 'default'): string
    {
        $cacheKey = 'theme_customizer_css_' . $themeSlug;

        return Cache::remember($cacheKey, 3600, function () use ($themeSlug) {
            return $this->compileCss($themeSlug);
        });
    }

    /**
     * Get public URL to the compiled CSS file if available on disk.
     */
    public function getPublicCssUrl(string $themeSlug = 'default'): ?string
    {
        $relPath = "themes/{$themeSlug}/custom_variables.css";
        $fullPath = public_path($relPath);

        if (File::exists($fullPath)) {
            return asset($relPath) . '?v=' . File::lastModified($fullPath);
        }

        return null;
    }

    /**
     * Write compiled CSS to public themes directory.
     */
    private function writeCssFile(string $themeSlug, string $cssContent): bool
    {
        try {
            $dir = public_path("themes/{$themeSlug}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::put($dir . '/custom_variables.css', $cssContent);

            return true;
        } catch (\Throwable $e) {
            Log::warning("ThemeCustomizer writeCssFile failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate and sanitize a hex color code.
     */
    private function sanitizeHexColor(string $hex, string $fallback): string
    {
        $hex = trim($hex);
        if (preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $hex)) {
            return $hex;
        }

        return $fallback;
    }

    /**
     * Convert Hex color to "R, G, B" string.
     */
    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } elseif (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            return '97, 93, 250';
        }

        return "{$r}, {$g}, {$b}";
    }
}
