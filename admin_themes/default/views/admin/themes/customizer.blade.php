@extends('admin::layouts.admin')

@section('title', __('messages.theme_customizer') ?? 'مخصص القوالب المباشر')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="main-content container-fluid px-3 px-xl-4 py-2">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="feather-check-circle me-3 fs-18"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="theme-customizer-shell">
        <!-- Top Toolbar Header -->
        <div class="customizer-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 bg-surface rounded-4 shadow-sm border">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.themes') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i>
                    <span>{{ __('messages.back_to_themes') ?? 'العودة للقوالب' }}</span>
                </a>
                <div class="vr mx-1 d-none d-sm-block"></div>
                <div>
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                        <span>{{ __('messages.theme_customizer') ?? 'مخصص القوالب المباشر' }}</span>
                    </h5>
                    <small class="text-muted">{{ __('messages.customize_appearance_desc') ?? 'تخصيص ألوان وخطوط وزوايا القالب مع معاينة حية فورية' }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Theme Switcher -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="feather-layout me-1"></i>
                        <span class="fw-semibold">{{ $themeSlug }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        @foreach($themes as $t)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ $t['slug'] === $themeSlug ? 'active' : '' }}" href="{{ route('admin.themes.customizer', ['theme' => $t['slug']]) }}">
                                    <span>{{ $t['name'] }}</span>
                                    @if($t['is_active'])
                                        <span class="badge bg-success-subtle text-success ms-2">{{ __('messages.active') ?? 'نشط' }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Device Preview Switcher -->
                <div class="btn-group btn-group-sm bg-light p-1 rounded-pill border" role="group">
                    <button type="button" class="btn btn-sm rounded-pill active px-3" id="device-desktop" title="Desktop View" onclick="setPreviewDevice('desktop')">
                        <i class="feather-monitor"></i>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3" id="device-tablet" title="Tablet View" onclick="setPreviewDevice('tablet')">
                        <i class="feather-tablet"></i>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3" id="device-mobile" title="Mobile View" onclick="setPreviewDevice('mobile')">
                        <i class="feather-smartphone"></i>
                    </button>
                </div>

                <!-- Action Buttons -->
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="btn-reset-theme" onclick="resetToDefaults()">
                    <i class="feather-rotate-ccw me-1"></i>
                    <span>{{ __('messages.reset_defaults') ?? 'استعادة الافتراضي' }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" id="btn-save-theme" onclick="saveCustomizer()">
                    <i class="feather-save me-1"></i>
                    <span>{{ __('messages.save_changes') ?? 'حفظ التعديلات' }}</span>
                </button>
            </div>
        </div>

        <!-- Split Screen Layout -->
        <div class="row g-3">
            <!-- Left Controls Panel -->
            <div class="col-lg-4 col-xl-3">
                <div class="customizer-sidebar bg-surface rounded-4 shadow-sm border p-3" style="max-height: calc(100vh - 170px); overflow-y: auto;">
                    <!-- Presets Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-uppercase fs-12 text-muted mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="feather-grid me-1"></i> {{ __('messages.color_presets') ?? 'لوحات الألوان الجاهزة' }}</span>
                        </label>
                        <div class="row g-2">
                            @foreach($presets as $key => $preset)
                                <div class="col-6">
                                    <button type="button" class="btn btn-sm w-100 text-start p-2 rounded-3 border preset-btn d-flex align-items-center gap-2" onclick="applyPreset('{{ $key }}')">
                                        <span class="preset-dot" style="background: {{ $preset['primary_color'] }}; width: 14px; height: 14px; border-radius: 50%; display: inline-block;"></span>
                                        <span class="text-truncate fs-12">{{ $preset['name'] }}</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form id="customizer-form" method="POST" action="{{ route('admin.themes.customizer.update') }}">
                        @csrf
                        <input type="hidden" name="theme_slug" id="theme_slug" value="{{ $themeSlug }}">

                        <!-- Section: Brand & Accents -->
                        <div class="mb-4">
                            <h6 class="fw-bold fs-13 text-uppercase text-muted border-bottom pb-2 mb-3">
                                <i class="feather-droplet me-1"></i> {{ __('messages.brand_colors') ?? 'الألوان الأساسية' }}
                            </h6>

                            <!-- Primary Color -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.primary_color') ?? 'اللون الأساسي' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_primary_color" value="{{ $variables['primary_color'] }}" oninput="syncColorInput('primary_color', this.value)">
                                    <input type="text" class="form-control font-monospace" name="primary_color" id="primary_color" value="{{ $variables['primary_color'] }}" oninput="syncColorPicker('primary_color', this.value)">
                                </div>
                            </div>

                            <!-- Secondary / Accent Color -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.accent_color') ?? 'لون التمييز (Accent)' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_secondary_color" value="{{ $variables['secondary_color'] }}" oninput="syncColorInput('secondary_color', this.value)">
                                    <input type="text" class="form-control font-monospace" name="secondary_color" id="secondary_color" value="{{ $variables['secondary_color'] }}" oninput="syncColorPicker('secondary_color', this.value)">
                                </div>
                            </div>

                            <!-- Header Background -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.header_bg') ?? 'خلفية الشريط العلوي' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_header_bg" value="{{ $variables['header_bg'] }}" oninput="syncColorInput('header_bg', this.value)">
                                    <input type="text" class="form-control font-monospace" name="header_bg" id="header_bg" value="{{ $variables['header_bg'] }}" oninput="syncColorPicker('header_bg', this.value)">
                                </div>
                            </div>
                        </div>

                        <!-- Section: Surfaces & Text -->
                        <div class="mb-4">
                            <h6 class="fw-bold fs-13 text-uppercase text-muted border-bottom pb-2 mb-3">
                                <i class="feather-sun me-1"></i> {{ __('messages.surfaces_and_text') ?? 'الخلفيات والنصوص' }}
                            </h6>

                            <!-- Background Color -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.page_background') ?? 'خلفية الصفحة' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_bg_color" value="{{ $variables['bg_color'] }}" oninput="syncColorInput('bg_color', this.value)">
                                    <input type="text" class="form-control font-monospace" name="bg_color" id="bg_color" value="{{ $variables['bg_color'] }}" oninput="syncColorPicker('bg_color', this.value)">
                                </div>
                            </div>

                            <!-- Card Surface Background -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.card_surface') ?? 'خلفية البطاقات' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_card_bg" value="{{ $variables['card_bg'] }}" oninput="syncColorInput('card_bg', this.value)">
                                    <input type="text" class="form-control font-monospace" name="card_bg" id="card_bg" value="{{ $variables['card_bg'] }}" oninput="syncColorPicker('card_bg', this.value)">
                                </div>
                            </div>

                            <!-- Text Color -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.text_color') ?? 'لون النص الرئيسي' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_text_color" value="{{ $variables['text_color'] }}" oninput="syncColorInput('text_color', this.value)">
                                    <input type="text" class="form-control font-monospace" name="text_color" id="text_color" value="{{ $variables['text_color'] }}" oninput="syncColorPicker('text_color', this.value)">
                                </div>
                            </div>

                            <!-- Text Muted Color -->
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.text_muted') ?? 'لون النص الفرعي' }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color rounded-start" id="picker_text_muted" value="{{ $variables['text_muted'] }}" oninput="syncColorInput('text_muted', this.value)">
                                    <input type="text" class="form-control font-monospace" name="text_muted" id="text_muted" value="{{ $variables['text_muted'] }}" oninput="syncColorPicker('text_muted', this.value)">
                                </div>
                            </div>
                        </div>

                        <!-- Section: Typography -->
                        <div class="mb-4">
                            <h6 class="fw-bold fs-13 text-uppercase text-muted border-bottom pb-2 mb-3">
                                <i class="feather-type me-1"></i> {{ __('messages.typography') ?? 'الخطوط والطباعة' }}
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fs-12 fw-semibold mb-1">{{ __('messages.primary_font_family') ?? 'عائلة الخط' }}</label>
                                <select class="form-select form-select-sm" name="font_family" id="font_family" onchange="triggerPreviewUpdate()">
                                    @foreach($fonts as $fKey => $fVal)
                                        <option value="{{ $fKey }}" {{ $variables['font_family'] === $fKey ? 'selected' : '' }}>
                                            {{ $fVal['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Section: Geometry & Effects -->
                        <div class="mb-4">
                            <h6 class="fw-bold fs-13 text-uppercase text-muted border-bottom pb-2 mb-3">
                                <i class="feather-sliders me-1"></i> {{ __('messages.geometry_and_effects') ?? 'الزوايا والتأثيرات' }}
                            </h6>

                            <!-- Border Radius -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-12 fw-semibold mb-0">{{ __('messages.border_radius') ?? 'انحناء الزوايا' }}</label>
                                    <span class="badge bg-light text-dark font-monospace" id="val_border_radius">{{ $variables['border_radius'] }}px</span>
                                </div>
                                <input type="range" class="form-range" min="0" max="32" step="2" name="border_radius" id="border_radius" value="{{ $variables['border_radius'] }}" oninput="document.getElementById('val_border_radius').innerText = this.value + 'px'; triggerPreviewUpdate();">
                            </div>

                            <!-- Glass Blur -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-12 fw-semibold mb-0">{{ __('messages.glass_blur') ?? 'تمويه الزجاج (Blur)' }}</label>
                                    <span class="badge bg-light text-dark font-monospace" id="val_glass_blur">{{ $variables['glass_blur'] }}px</span>
                                </div>
                                <input type="range" class="form-range" min="0" max="30" step="2" name="glass_blur" id="glass_blur" value="{{ $variables['glass_blur'] }}" oninput="document.getElementById('val_glass_blur').innerText = this.value + 'px'; triggerPreviewUpdate();">
                            </div>

                            <!-- Glass Opacity -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fs-12 fw-semibold mb-0">{{ __('messages.glass_opacity') ?? 'شفافية الزجاج' }}</label>
                                    <span class="badge bg-light text-dark font-monospace" id="val_glass_opacity">{{ $variables['glass_opacity'] }}</span>
                                </div>
                                <input type="range" class="form-range" min="0.2" max="1.0" step="0.05" name="glass_opacity" id="glass_opacity" value="{{ $variables['glass_opacity'] }}" oninput="document.getElementById('val_glass_opacity').innerText = this.value; triggerPreviewUpdate();">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Preview Panel -->
            <div class="col-lg-8 col-xl-9">
                <div class="preview-container bg-surface rounded-4 shadow-sm border p-2 d-flex flex-column" style="height: calc(100vh - 170px);">
                    <div class="preview-browser-bar d-flex align-items-center justify-content-between px-3 py-2 bg-light rounded-top-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ff5f56; display: inline-block;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ffbd2e; display: inline-block;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #27c93f; display: inline-block;"></span>
                        </div>
                        <div class="preview-url-pill bg-white px-4 py-1 rounded-pill fs-12 text-muted border text-truncate" style="max-width: 400px;">
                            <i class="feather-lock me-1 text-success fs-10"></i>
                            <span id="preview-url-text">{{ url('/?theme_preview=1') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-icon text-muted" onclick="reloadPreview()" title="Reload Preview">
                                <i class="feather-refresh-cw"></i>
                            </button>
                        </div>
                    </div>

                    <div class="preview-frame-wrapper flex-grow-1 d-flex justify-content-center align-items-stretch overflow-hidden p-2 bg-light rounded-bottom-3 position-relative">
                        <iframe id="theme-preview-frame" src="{{ url('/?theme_preview=1') }}" class="preview-iframe shadow-sm border-0 rounded-3 w-100 h-100" style="transition: width 0.3s ease;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const PRESETS = @json($presets);
    const FONTS = @json($fonts);

    function syncColorInput(field, hex) {
        document.getElementById(field).value = hex;
        triggerPreviewUpdate();
    }

    function syncColorPicker(field, hex) {
        if (/^#([a-f0-9]{3}|[a-f0-9]{6})$/i.test(hex)) {
            document.getElementById('picker_' + field).value = hex;
            triggerPreviewUpdate();
        }
    }

    function applyPreset(key) {
        const p = PRESETS[key];
        if (!p) return;

        for (const [field, val] of Object.entries(p)) {
            const input = document.getElementById(field);
            if (input) {
                input.value = val;
                const picker = document.getElementById('picker_' + field);
                if (picker) picker.value = val;
                const badge = document.getElementById('val_' + field);
                if (badge) badge.innerText = (field === 'border_radius' || field === 'glass_blur') ? val + 'px' : val;
            }
        }
        triggerPreviewUpdate();
    }

    function setPreviewDevice(device) {
        document.querySelectorAll('#device-desktop, #device-tablet, #device-mobile').forEach(btn => btn.classList.remove('active'));
        document.getElementById('device-' + device).classList.add('active');

        const frame = document.getElementById('theme-preview-frame');
        if (device === 'mobile') {
            frame.style.width = '375px';
        } else if (device === 'tablet') {
            frame.style.width = '768px';
        } else {
            frame.style.width = '100%';
        }
    }

    function reloadPreview() {
        const frame = document.getElementById('theme-preview-frame');
        frame.src = frame.src;
    }

    function getCurrentVariables() {
        return {
            primary_color: document.getElementById('primary_color').value,
            secondary_color: document.getElementById('secondary_color').value,
            header_bg: document.getElementById('header_bg').value,
            bg_color: document.getElementById('bg_color').value,
            card_bg: document.getElementById('card_bg').value,
            text_color: document.getElementById('text_color').value,
            text_muted: document.getElementById('text_muted').value,
            font_family: document.getElementById('font_family').value,
            border_radius: document.getElementById('border_radius').value,
            glass_blur: document.getElementById('glass_blur').value,
            glass_opacity: document.getElementById('glass_opacity').value,
        };
    }

    function triggerPreviewUpdate() {
        const vars = getCurrentVariables();
        const fontDef = FONTS[vars.font_family] || FONTS['Inter'];
        const frame = document.getElementById('theme-preview-frame');

        if (frame && frame.contentWindow) {
            frame.contentWindow.postMessage({
                type: 'MYADS_THEME_CUSTOMIZER_UPDATE',
                variables: vars,
                fontFamily: fontDef.family
            }, '*');
        }
    }

    // When preview frame loads, send initial variables
    document.getElementById('theme-preview-frame').addEventListener('load', function() {
        triggerPreviewUpdate();
    });

    async function saveCustomizer() {
        const btn = document.getElementById('btn-save-theme');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const form = document.getElementById('customizer-form');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                btn.innerHTML = '<i class="feather-check me-1"></i> Saved!';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = origHtml;
                }, 2000);
            } else {
                throw new Error('Save failed');
            }
        } catch (e) {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert('Failed to save theme customizations.');
        }
    }

    async function resetToDefaults() {
        if (!confirm('Are you sure you want to reset this theme to its default settings?')) {
            return;
        }

        const form = document.getElementById('customizer-form');
        const themeSlug = document.getElementById('theme_slug').value;
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('theme_slug', themeSlug);

        try {
            const response = await fetch('{{ route('admin.themes.customizer.reset') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success && data.defaults) {
                for (const [field, val] of Object.entries(data.defaults)) {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = val;
                        const picker = document.getElementById('picker_' + field);
                        if (picker) picker.value = val;
                        const badge = document.getElementById('val_' + field);
                        if (badge) badge.innerText = (field === 'border_radius' || field === 'glass_blur') ? val + 'px' : val;
                    }
                }
                triggerPreviewUpdate();
                alert('Theme customizations reset to defaults successfully.');
            }
        } catch (e) {
            alert('Failed to reset theme customizations.');
        }
    }
</script>

<style>
    .theme-customizer-shell {
        font-family: inherit;
    }
    .customizer-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    .customizer-sidebar::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.12);
        border-radius: 4px;
    }
    .preset-btn:hover {
        background-color: var(--bs-light);
        border-color: var(--bs-primary) !important;
    }
    .preview-iframe {
        background: #ffffff;
    }
    .fs-12 { font-size: 0.75rem; }
    .fs-13 { font-size: 0.8125rem; }
</style>
@endsection
