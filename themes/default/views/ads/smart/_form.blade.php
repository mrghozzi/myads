@php
    $smartAd = $smartAd ?? new \App\Models\SmartAd();
    $targetCountries = $targetCountries ?? '';
    $selectedDevices = $selectedDevices ?? [];
    $deviceOptions = $deviceOptions ?? [];
    $formAction = $formAction ?? route('ads.smart.store');
    $formMethod = $formMethod ?? 'POST';
    $submitLabel = $submitLabel ?? __('messages.smart_form_save');
@endphp

@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 14px;">
        <ul style="margin: 0; padding-inline-start: 18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning mb-4" style="border-radius: 14px;">
        <i class="fa fa-exclamation-circle me-2"></i>
        {{ session('warning') }}
    </div>
@endif

<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="superdesign-field-group">
        <label for="landing_url" class="superdesign-field-label">
            <i class="fa fa-globe"></i>
            {{ __('messages.smart_form_landing_url') }}
        </label>
        <div class="superdesign-input-wrapper">
            <input 
                type="url" 
                id="landing_url" 
                name="landing_url" 
                class="superdesign-input" 
                value="{{ old('landing_url', $smartAd->landing_url) }}" 
                required 
                placeholder="https://example.com/page"
            >
        </div>
        <small class="superdesign-field-help">{{ __('messages.smart_form_landing_help') }}</small>
    </div>

    <div class="superdesign-fields-row">
        <div class="superdesign-field-group">
            <label for="headline_override" class="superdesign-field-label">
                <i class="fa fa-heading"></i>
                {{ __('messages.smart_form_headline_override') }}
            </label>
            <div class="superdesign-input-wrapper">
                <input 
                    type="text" 
                    id="headline_override" 
                    name="headline_override" 
                    class="superdesign-input" 
                    value="{{ old('headline_override', $smartAd->headline_override) }}"
                    placeholder="{{ __('messages.optional') ?? 'اختياري...' }}"
                >
            </div>
        </div>

        <div class="superdesign-field-group">
            <label for="image" class="superdesign-field-label">
                <i class="fa fa-image"></i>
                {{ __('messages.smart_form_image_override') }}
            </label>
            <div class="superdesign-input-wrapper">
                <input 
                    type="text" 
                    id="image" 
                    name="image" 
                    class="superdesign-input" 
                    value="{{ old('image', $smartAd->image) }}"
                    placeholder="https://example.com/banner.jpg"
                >
            </div>
            <small class="superdesign-field-help">{{ __('messages.smart_form_image_help') }}</small>
        </div>
    </div>

    <div class="superdesign-field-group">
        <label for="description_override" class="superdesign-field-label">
            <i class="fa fa-align-left"></i>
            {{ __('messages.smart_form_description_override') }}
        </label>
        <div class="superdesign-input-wrapper">
            <textarea 
                id="description_override" 
                name="description_override" 
                class="superdesign-textarea" 
                rows="3"
                placeholder="{{ __('messages.optional') ?? 'اختياري...' }}"
            >{{ old('description_override', $smartAd->description_override) }}</textarea>
        </div>
    </div>

    <div class="superdesign-fields-row">
        <div class="superdesign-field-group">
            <label for="countries" class="superdesign-field-label">
                <i class="fa fa-flag"></i>
                {{ __('messages.smart_form_target_countries') }}
            </label>
            <div class="superdesign-input-wrapper">
                <input 
                    type="text" 
                    id="countries" 
                    name="countries" 
                    class="superdesign-input" 
                    value="{{ old('countries', $targetCountries) }}" 
                    placeholder="{{ __('messages.smart_form_countries_placeholder') }}"
                >
            </div>
            <small class="superdesign-field-help">{{ __('messages.smart_form_target_countries_help') }}</small>
        </div>

        <div class="superdesign-field-group">
            <label class="superdesign-field-label">
                <i class="fa fa-laptop-mobile"></i>
                {{ __('messages.smart_form_target_devices') }}
            </label>
            <div class="superdesign-device-chips">
                @foreach($deviceOptions as $value => $label)
                    <label class="superdesign-device-chip">
                        <input type="checkbox" name="devices[]" value="{{ $value }}" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                        <span>
                            @if($value === 'desktop') <i class="fa fa-desktop"></i>
                            @elseif($value === 'mobile') <i class="fa fa-mobile-alt"></i>
                            @elseif($value === 'tablet') <i class="fa fa-tablet-alt"></i>
                            @endif
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
            <small class="superdesign-field-help">{{ __('messages.smart_form_target_devices_help') }}</small>
        </div>
    </div>

    <div class="superdesign-field-group">
        <label for="manual_keywords" class="superdesign-field-label">
            <i class="fa fa-tags"></i>
            {{ __('messages.smart_form_manual_keywords') }}
        </label>
        <div class="superdesign-input-wrapper">
            <textarea 
                id="manual_keywords" 
                name="manual_keywords" 
                class="superdesign-textarea" 
                rows="3" 
                placeholder="{{ __('messages.smart_form_keywords_placeholder') }}"
            >{{ old('manual_keywords', $smartAd->manual_keywords) }}</textarea>
        </div>
        <small class="superdesign-field-help">{{ __('messages.smart_form_manual_keywords_help') }}</small>
    </div>

    @if(isset($smartAd) && $smartAd->exists)
        <div class="superdesign-field-group">
            <label class="superdesign-field-label">
                <i class="fa fa-robot"></i>
                {{ __('messages.smart_form_extracted_topic') }}
            </label>
            <div class="superdesign-input-wrapper">
                <textarea class="superdesign-textarea" rows="3" readonly style="background: #f1f5f9; color: #475569;">{{ $smartAd->extracted_keywords }}</textarea>
            </div>
        </div>
    @endif

    <!-- Action Buttons Bar -->
    <div class="superdesign-actions-bar">
        <a href="{{ route('ads.smart.index') }}" class="superdesign-btn-secondary">
            <i class="fa fa-arrow-left"></i>
            {{ __('messages.smart_back_to_list') }}
        </a>

        <button type="submit" class="superdesign-btn-primary">
            <i class="fa fa-paper-plane"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

<style>
/* Smart Form Controls & Chips */
.superdesign-fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .superdesign-fields-row {
        grid-template-columns: 1fr;
    }
}
.superdesign-field-group {
    margin-bottom: 22px;
}
.superdesign-field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}
.superdesign-field-label i {
    color: #615dfa;
}
.superdesign-field-help {
    display: block;
    margin-top: 6px;
    font-size: 12.5px;
    color: #64748b;
}
.superdesign-input-wrapper {
    position: relative;
}
.superdesign-input {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
}
.superdesign-input:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-textarea {
    width: 100%;
    padding: 12px 16px;
    font-size: 14.5px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
    resize: vertical;
}
.superdesign-textarea:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}

/* Device Chips */
.superdesign-device-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 4px;
}
.superdesign-device-chip {
    cursor: pointer;
    margin: 0;
}
.superdesign-device-chip input[type="checkbox"] {
    display: none;
}
.superdesign-device-chip span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s ease;
}
.superdesign-device-chip input[type="checkbox"]:checked + span {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(35, 210, 226, 0.12) 100%);
    border-color: #615dfa;
    color: #615dfa;
    box-shadow: 0 2px 8px rgba(97, 93, 250, 0.15);
}

/* Form Actions */
.superdesign-actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}
.superdesign-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    border: none;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.3);
    cursor: pointer;
    transition: all 0.25s ease;
}
.superdesign-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(97, 93, 250, 0.4);
    color: #ffffff;
}

/* --- Dark Mode Parity --- */
body[data-theme="css_d"] .superdesign-field-label,
html.app-skin-dark .superdesign-field-label,
.dark-mode .superdesign-field-label {
    color: #f1f5f9;
}
body[data-theme="css_d"] .superdesign-field-help,
html.app-skin-dark .superdesign-field-help,
.dark-mode .superdesign-field-help {
    color: #94a3b8;
}
body[data-theme="css_d"] .superdesign-input,
body[data-theme="css_d"] .superdesign-textarea,
html.app-skin-dark .superdesign-input,
html.app-skin-dark .superdesign-textarea,
.dark-mode .superdesign-input,
.dark-mode .superdesign-textarea {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-input:focus,
body[data-theme="css_d"] .superdesign-textarea:focus,
html.app-skin-dark .superdesign-input:focus,
html.app-skin-dark .superdesign-textarea:focus,
.dark-mode .superdesign-input:focus,
.dark-mode .superdesign-textarea:focus {
    background-color: #1e293b;
    border-color: #615dfa;
}
body[data-theme="css_d"] .superdesign-device-chip span,
html.app-skin-dark .superdesign-device-chip span,
.dark-mode .superdesign-device-chip span {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-device-chip input[type="checkbox"]:checked + span,
html.app-skin-dark .superdesign-device-chip input[type="checkbox"]:checked + span,
.dark-mode .superdesign-device-chip input[type="checkbox"]:checked + span {
    background: rgba(97, 93, 250, 0.25);
    border-color: #615dfa;
    color: #818cf8;
}
body[data-theme="css_d"] .superdesign-actions-bar,
html.app-skin-dark .superdesign-actions-bar,
.dark-mode .superdesign-actions-bar {
    border-top-color: #334155;
}
</style>
