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
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-inline-start: 18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="grid grid-6-6" style="gap: 18px;">
        <div class="widget-box" style="margin: 0;">
            <div class="widget-box-content">
                <div class="form-group">
                    <label>{{ __('messages.smart_form_landing_url') }}</label>
                    <input type="url" name="landing_url" class="form-control" value="{{ old('landing_url', $smartAd->landing_url) }}" required>
                    <small class="text-muted">{{ __('messages.smart_form_landing_help') }}</small>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.smart_form_headline_override') }}</label>
                    <input type="text" name="headline_override" class="form-control" value="{{ old('headline_override', $smartAd->headline_override) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('messages.smart_form_description_override') }}</label>
                    <textarea name="description_override" class="form-control" rows="5">{{ old('description_override', $smartAd->description_override) }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.smart_form_image_override') }}</label>
                    <input type="text" name="image" class="form-control" value="{{ old('image', $smartAd->image) }}">
                    <small class="text-muted">{{ __('messages.smart_form_image_help') }}</small>
                </div>
            </div>
        </div>

        <div class="widget-box" style="margin: 0;">
            <div class="widget-box-content">
                <div class="form-group">
                    <label>{{ __('messages.smart_form_target_countries') }}</label>
                    <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}" placeholder="{{ __('messages.smart_form_countries_placeholder') }}">
                    <small class="text-muted">{{ __('messages.smart_form_target_countries_help') }}</small>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.smart_form_target_devices') }}</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 10px;">
                        @foreach($deviceOptions as $value => $label)
                            <label style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff;">
                                <input type="checkbox" name="devices[]" value="{{ $value }}" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted">{{ __('messages.smart_form_target_devices_help') }}</small>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.smart_form_manual_keywords') }}</label>
                    <textarea name="manual_keywords" class="form-control" rows="5" placeholder="{{ __('messages.smart_form_keywords_placeholder') }}">{{ old('manual_keywords', $smartAd->manual_keywords) }}</textarea>
                    <small class="text-muted">{{ __('messages.smart_form_manual_keywords_help') }}</small>
                </div>

                @if(isset($smartAd) && $smartAd->exists)
                    <div class="form-group">
                        <label>{{ __('messages.smart_form_extracted_topic') }}</label>
                        <textarea class="form-control" rows="5" readonly>{{ $smartAd->extracted_keywords }}</textarea>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 18px;">
        <div class="widget-box-content" style="display: grid; gap: 16px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px;">
                <div style="padding: 18px; border: 1px solid #eef2ff; border-radius: 18px; background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);">
                    <p style="margin: 0 0 10px; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #1d4ed8; font-weight: 700;">{{ __('messages.smart_banner_output') }}</p>
                    <p style="margin: 0; color: #4b5563; line-height: 1.7;">{{ __('messages.smart_banner_output_help') }}</p>
                </div>
                <div style="padding: 18px; border: 1px solid #e5f8f8; border-radius: 18px; background: linear-gradient(135deg, #f6fffe 0%, #effcfb 100%);">
                    <p style="margin: 0 0 10px; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #0f766e; font-weight: 700;">{{ __('messages.smart_native_fallback') }}</p>
                    <p style="margin: 0; color: #4b5563; line-height: 1.7;">{{ __('messages.smart_native_fallback_help') }}</p>
                </div>
            </div>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="button secondary">{{ $submitLabel }}</button>
                <a href="{{ route('ads.smart.index') }}" class="button white">{{ __('messages.smart_back_to_list') }}</a>
            </div>
        </div>
    </div>
</form>
