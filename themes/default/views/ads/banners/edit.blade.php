@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.edit_banner') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('ads.banners.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('ads.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @php($bannerSizes = \App\Support\BannerSizeCatalog::ordered())
                    <div class="grid grid-6-6" style="gap: 18px;">
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ $banner->name }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.url') }}</label>
                                <input type="url" name="url" class="form-control" value="{{ $banner->url }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.size') }}</label>
                                <select name="px" class="form-control">
                                    @foreach($bannerSizes as $size)
                                        <option value="{{ $size['value'] }}" {{ $banner->px == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Banner Image (Version A) -->
                            <div class="form-group" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background: #fafafa; margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <label style="font-weight: 600; margin-bottom: 0;">{{ __('messages.img') }} (Version A)</label>
                                    <div style="display: inline-flex; background: #e5e7eb; padding: 2px; border-radius: 6px; gap: 2px;">
                                        <button type="button" id="btnEditModeUrlA" onclick="switchEditImgMode('A', 'url')" style="border: none; background: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; color: #1e293b;">
                                            {{ __('messages.enter_image_url') }}
                                        </button>
                                        <button type="button" id="btnEditModeUploadA" onclick="switchEditImgMode('A', 'upload')" style="border: none; background: transparent; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; color: #64748b;">
                                            {{ __('messages.upload_from_device') }}
                                        </button>
                                    </div>
                                </div>

                                <div id="editContainerUrlA">
                                    <input type="text" name="img" id="editInputUrlA" class="form-control" value="{{ old('img', $banner->img) }}" oninput="updateEditPreview('A', this.value)">
                                </div>
                                <div id="editContainerUploadA" style="display: none;">
                                    <input type="file" name="img_file" id="editInputFileA" class="form-control" accept="image/*" onchange="handleEditFileSelected('A', this)">
                                    <small class="text-muted" style="display: block; margin-top: 4px;">JPG, PNG, GIF, WEBP, SVG (Max 5MB)</small>
                                </div>

                                <div id="editPreviewBoxA" style="margin-top: 10px; text-align: center; background: #fff; padding: 8px; border-radius: 6px; border: 1px solid #e5e7eb;">
                                    <span style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 4px;">{{ __('messages.current_image') }} / {{ __('messages.preview') }}</span>
                                    <img id="editPreviewImgA" src="{{ $banner->img }}" alt="Version A" style="max-height: 90px; max-width: 100%; object-fit: contain;">
                                </div>
                            </div>

                            <!-- Banner Image (Version B - Optional) -->
                            <div class="form-group" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background: #fafafa;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <label style="font-weight: 600; margin-bottom: 0;">{{ __('messages.img') }} (Version B - Optional)</label>
                                    <div style="display: inline-flex; background: #e5e7eb; padding: 2px; border-radius: 6px; gap: 2px;">
                                        <button type="button" id="btnEditModeUrlB" onclick="switchEditImgMode('B', 'url')" style="border: none; background: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; color: #1e293b;">
                                            {{ __('messages.enter_image_url') }}
                                        </button>
                                        <button type="button" id="btnEditModeUploadB" onclick="switchEditImgMode('B', 'upload')" style="border: none; background: transparent; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; color: #64748b;">
                                            {{ __('messages.upload_from_device') }}
                                        </button>
                                    </div>
                                </div>

                                <div id="editContainerUrlB">
                                    <input type="text" name="img_b" id="editInputUrlB" class="form-control" value="{{ old('img_b', $banner->img_b) }}" placeholder="https://..." oninput="updateEditPreview('B', this.value)">
                                </div>
                                <div id="editContainerUploadB" style="display: none;">
                                    <input type="file" name="img_file_b" id="editInputFileB" class="form-control" accept="image/*" onchange="handleEditFileSelected('B', this)">
                                    <small class="text-muted" style="display: block; margin-top: 4px;">JPG, PNG, GIF, WEBP, SVG (Max 5MB)</small>
                                </div>

                                <div id="editPreviewBoxB" style="{{ $banner->img_b ? '' : 'display: none;' }} margin-top: 10px; text-align: center; background: #fff; padding: 8px; border-radius: 6px; border: 1px solid #e5e7eb;">
                                    <span style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 4px;">{{ __('messages.preview') }}</span>
                                    <img id="editPreviewImgB" src="{{ $banner->img_b ?? '' }}" alt="Version B" style="max-height: 90px; max-width: 100%; object-fit: contain;">
                                </div>
                                <small class="text-muted" style="display: block; margin-top: 6px;">A/B Testing: Provide a second image to automatically serve the best performing version.</small>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_countries') }}</label>
                                <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}" placeholder="{{ __('messages.smart_form_countries_placeholder') }}">
                                <small class="text-muted">{{ __('messages.smart_form_target_countries_help') }}</small>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_devices') }}</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px;">
                                    @foreach($deviceOptions as $value => $label)
                                        <label style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 13px;">
                                            <input type="checkbox" name="devices[]" value="{{ $value }}" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="button secondary" style="margin-top: 14px;">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchEditImgMode(version, mode) {
    const containerUrl = document.getElementById('editContainerUrl' + version);
    const containerUpload = document.getElementById('editContainerUpload' + version);
    const btnUrl = document.getElementById('btnEditModeUrl' + version);
    const btnUpload = document.getElementById('btnEditModeUpload' + version);

    if (mode === 'url') {
        containerUrl.style.display = 'block';
        containerUpload.style.display = 'none';
        btnUrl.style.background = '#fff';
        btnUrl.style.color = '#1e293b';
        btnUpload.style.background = 'transparent';
        btnUpload.style.color = '#64748b';
        const val = document.getElementById('editInputUrl' + version).value;
        if (val) updateEditPreview(version, val);
    } else {
        containerUrl.style.display = 'none';
        containerUpload.style.display = 'block';
        btnUpload.style.background = '#fff';
        btnUpload.style.color = '#1e293b';
        btnUrl.style.background = 'transparent';
        btnUrl.style.color = '#64748b';
    }
}

function updateEditPreview(version, url) {
    const box = document.getElementById('editPreviewBox' + version);
    const img = document.getElementById('editPreviewImg' + version);
    if (!box || !img) return;
    const trimmed = (url || '').trim();
    if (trimmed) {
        img.src = trimmed;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function handleEditFileSelected(version, input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const box = document.getElementById('editPreviewBox' + version);
        const img = document.getElementById('editPreviewImg' + version);
        if (box && img) {
            img.src = URL.createObjectURL(file);
            box.style.display = 'block';
        }
    }
}
</script>
@endsection
