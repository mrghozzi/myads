@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $showTitle = (bool) ($config['show_title'] ?? false);
    $showHelp = (bool) ($config['show_help'] ?? true);
    $showSitemap = (bool) ($config['show_sitemap'] ?? true);
    $showDeveloper = (bool) ($config['show_developer'] ?? true);
    $showPrivacy = (bool) ($config['show_privacy'] ?? true);
    $showTerms = (bool) ($config['show_terms'] ?? true);
    $showRefund = (bool) ($config['show_refund'] ?? true);
    $customLinksText = $config['custom_links_text'] ?? '';
    
    $showSocial = (bool) ($config['show_social'] ?? false);
    $facebookUrl = $config['facebook_url'] ?? '';
    $twitterUrl = $config['twitter_url'] ?? '';
    $linkedinUrl = $config['linkedin_url'] ?? '';
    $telegramUrl = $config['telegram_url'] ?? '';
    $youtubeUrl = $config['youtube_url'] ?? '';
    $githubUrl = $config['github_url'] ?? '';

    $customCopyright = $config['custom_copyright'] ?? '';
    $showPoweredBy = (bool) ($config['show_powered_by'] ?? true);
    $poweredByText = $config['powered_by_text'] ?? '';
    
    $layoutStyle = $config['layout_style'] ?? 'centered';
    $bgStyle = $config['background_style'] ?? 'card';
@endphp

<div class="admin-form-grid__full mt-3 pt-3 border-top border-light-subtle" style="grid-column: 1 / -1;">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(97, 93, 250, 0.12); color: #615dfa; display: flex; align-items: center; justify-content: center; font-size: 15px;">
            <i class="feather-sliders"></i>
        </div>
        <h5 class="mb-0 fw-bold" style="font-size: 15px; color: var(--admin-text-main, #1e293b);">
            {{ __('messages.landing_footer_customizer') }}
        </h5>
    </div>

    <!-- TABS OR ACCORDION CARD -->
    <div class="card border border-light-subtle shadow-none" style="border-radius: 14px; background: var(--admin-input-bg, #f8fafc); overflow: hidden;">
        <div class="card-body p-3 p-md-4">
            
            <!-- SECTION 1: DISPLAY & LAYOUT -->
            <div class="mb-4">
                <span class="text-uppercase fw-bold text-primary mb-2 d-block" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="feather-layout me-1"></i> {{ __('messages.footer_display_settings') }}
                </span>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.footer_layout_style') }}</label>
                        <select name="landing_footer[layout_style]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="centered" {{ $layoutStyle === 'centered' ? 'selected' : '' }}>{{ __('messages.layout_centered') }}</option>
                            <option value="columns" {{ $layoutStyle === 'columns' ? 'selected' : '' }}>{{ __('messages.layout_columns') }}</option>
                            <option value="compact" {{ $layoutStyle === 'compact' ? 'selected' : '' }}>{{ __('messages.layout_compact') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.footer_bg_style') }}</label>
                        <select name="landing_footer[background_style]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="card" {{ $bgStyle === 'card' ? 'selected' : '' }}>{{ __('messages.bg_style_card') }}</option>
                            <option value="subtle" {{ $bgStyle === 'subtle' ? 'selected' : '' }}>{{ __('messages.bg_style_subtle') }}</option>
                            <option value="transparent" {{ $bgStyle === 'transparent' ? 'selected' : '' }}>{{ __('messages.bg_style_transparent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="landing_footer[show_title]" id="lf_show_title" value="1" {{ $showTitle ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="lf_show_title" style="font-size: 13px;">
                                {{ __('messages.show_widget_title') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-light-subtle my-3">

            <!-- SECTION 2: PRIMARY LINKS TOGGLES -->
            <div class="mb-4">
                <span class="text-uppercase fw-bold text-primary mb-2 d-block" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="feather-link me-1"></i> {{ __('messages.footer_links_settings') }}
                </span>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-circle-question text-primary me-1"></i> {{ __('help.help_center') ?? 'مركز المساعدة' }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_help]" value="1" {{ $showHelp ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-sitemap text-info me-1"></i> {{ __('messages.sitemap') }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_sitemap]" value="1" {{ $showSitemap ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-code text-success me-1"></i> {{ __('messages.developers') }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_developer]" value="1" {{ $showDeveloper ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-shield-halved text-warning me-1"></i> {{ __('messages.privacy_policy') }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_privacy]" value="1" {{ $showPrivacy ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-file-contract text-secondary me-1"></i> {{ __('messages.terms_conditions') }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_terms]" value="1" {{ $showTerms ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="fa-solid fa-rotate-left text-danger me-1"></i> {{ __('messages.refund_policy') }}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_refund]" value="1" {{ $showRefund ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Additional Links -->
                <div class="mt-3">
                    <label class="admin-form-label fw-bold mb-1 d-flex justify-content-between" style="font-size: 12px;">
                        <span>{{ __('messages.custom_footer_links') }}</span>
                        <span class="text-muted" style="font-size: 11px;">{{ __('messages.custom_footer_links_help') }}</span>
                    </label>
                    <textarea name="landing_footer[custom_links_text]" rows="3" class="form-control admin-form-control font-monospace" placeholder="المنتدى | /forum&#10;دليل المواقع | /directory&#10;عن الموقع | /about" style="border-radius: 10px; font-size: 12px; font-family: monospace;">{{ $customLinksText }}</textarea>
                </div>
            </div>

            <hr class="border-light-subtle my-3">

            <!-- SECTION 3: SOCIAL MEDIA NETWORKS -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold text-primary" style="font-size: 11px; letter-spacing: 0.5px;">
                        <i class="feather-share-2 me-1"></i> {{ __('messages.footer_social_settings') }}
                    </span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="landing_footer[show_social]" id="lf_show_social" value="1" {{ $showSocial ? 'checked' : '' }} onchange="document.getElementById('lf_social_fields').style.display = this.checked ? 'flex' : 'none';">
                        <label class="form-check-label fw-semibold" for="lf_show_social" style="font-size: 12px;">
                            {{ __('messages.show_social_icons') }}
                        </label>
                    </div>
                </div>

                <div class="row g-2" id="lf_social_fields" style="display: {{ $showSocial ? 'flex' : 'none' }};">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-facebook text-primary"></i></span>
                            <input type="text" name="landing_footer[facebook_url]" class="form-control" value="{{ $facebookUrl }}" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-x-twitter text-dark"></i></span>
                            <input type="text" name="landing_footer[twitter_url]" class="form-control" value="{{ $twitterUrl }}" placeholder="https://x.com/...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-linkedin text-info"></i></span>
                            <input type="text" name="landing_footer[linkedin_url]" class="form-control" value="{{ $linkedinUrl }}" placeholder="https://linkedin.com/...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-telegram text-primary"></i></span>
                            <input type="text" name="landing_footer[telegram_url]" class="form-control" value="{{ $telegramUrl }}" placeholder="https://t.me/...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-youtube text-danger"></i></span>
                            <input type="text" name="landing_footer[youtube_url]" class="form-control" value="{{ $youtubeUrl }}" placeholder="https://youtube.com/...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa-brands fa-github text-dark"></i></span>
                            <input type="text" name="landing_footer[github_url]" class="form-control" value="{{ $githubUrl }}" placeholder="https://github.com/...">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-light-subtle my-3">

            <!-- SECTION 4: COPYRIGHT & BRANDING -->
            <div>
                <span class="text-uppercase fw-bold text-primary mb-2 d-block" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="feather-award me-1"></i> {{ __('messages.footer_copyright_settings') }}
                </span>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.custom_copyright_notice') }}</label>
                        <input type="text" name="landing_footer[custom_copyright]" class="form-control admin-form-control" value="{{ $customCopyright }}" placeholder="&copy; :year :site. {{ __('messages.all_rights_reserved') }}" style="border-radius: 10px; font-size: 12px;">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">{{ __('messages.custom_copyright_help') }}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="admin-form-label fw-bold mb-0" style="font-size: 12px;">{{ __('messages.custom_powered_by_text') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="landing_footer[show_powered_by]" id="lf_show_powered" value="1" {{ $showPoweredBy ? 'checked' : '' }}>
                                <label class="form-check-label" for="lf_show_powered" style="font-size: 11px;">{{ __('messages.show_powered_by_tag') }}</label>
                            </div>
                        </div>
                        <input type="text" name="landing_footer[powered_by_text]" class="form-control admin-form-control" value="{{ $poweredByText }}" placeholder="Powered by :site SEO Engine | :version" style="border-radius: 10px; font-size: 12px;">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
