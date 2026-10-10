@extends('theme::layouts.master')

@section('content')
@php
    $updateLinkzipValue = old('linkzip', '');
    $latestVersionName = optional($files->first())->name ?: 'v1.0';
@endphp

@include('theme::store.partials.editor-assets')

<div class="store-editor-page">
    <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.update.store', $product->name) }}">
        @csrf

        <div class="store-editor-layout">
            <div class="store-editor-main">
                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.update') }}</p>
                    <p class="widget-box-text">{{ $product->name }}</p>

                    <div class="widget-box-content">
                        <div id="store-update-ajax-alert" style="display:none; margin-bottom: 20px;"></div>

                        <div class="store-editor-alerts">
                            @if(session('error'))
                                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp; {{ session('error') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="upd-version">{{ __('messages.Version_nbr') }}</label>
                                    <input
                                        type="text"
                                        id="upd-version"
                                        name="vnbr"
                                        value="{{ old('vnbr') }}"
                                        placeholder="{{ __('messages.version') }} | EX: v1.0"
                                        minlength="2"
                                        maxlength="12"
                                        pattern="^[-a-zA-Z0-9.]+$"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small full" style="padding: 10px;">
                                    <label for="upd-desc" style="display:block;margin-bottom:10px;font-weight:bold;">{{ __('messages.desc') }}</label>
                                    <div class="stackedit-tools mb-2" style="margin-bottom:10px;">
                                        <button type="button" class="button secondary small open-stackedit" data-target="#upd-desc">
                                            <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                                        </button>
                                    </div>
                                    <textarea id="upd-desc" name="desc" minlength="10" maxlength="65535" style="width:100%;padding:10px;" required>{{ old('desc') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('theme::store.partials.source-picker', [
                    'linkzipValue' => $updateLinkzipValue,
                    'linkInputId' => 'store-update-direct-link',
                ])

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.img') }}</p>
                    <div class="widget-box-content">
                        <div id="store-img-ajax-alert" style="display:none; margin-bottom: 12px;"></div>
                        <div id="OpenImgUploadUpdate" class="upload-box" style="cursor:pointer;">
                            <svg class="upload-box-icon icon-photos">
                                <use xlink:href="#svg-photos"></use>
                            </svg>
                            <p class="upload-box-title">{{ __('messages.upload') }}</p>
                            <p class="upload-box-text">{{ __('messages.img') }}</p>
                        </div>
                        <center><br /><div id="showImgUploadUpdate"><input type="text" name="img" value="{{ old('img') }}" style="display:none"></div></center>
                        <input type="file" id="imgupload_update" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                        @if($product->o_mode)
                            <small id="current-cover-display" style="color:#8f94b5;display:block;margin-top:12px;">{{ __('messages.current') }}: <a href="{{ $product->o_mode }}" target="_blank" rel="noopener noreferrer">{{ $product->o_mode }}</a></small>
                        @endif
                        <div style="display:flex; justify-content:flex-end; align-items:center; gap:10px; margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <span id="img-save-status" style="font-size:12px; color:#8f94b5;"></span>
                            <button type="button" id="btn-save-cover" class="button secondary small">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.media_and_demo') }}</p>
                    <p class="widget-box-text">{{ __('messages.screenshots_gallery') }} &amp; {{ __('messages.live_preview') }}</p>

                    <div class="widget-box-content">
                        <div id="store-media-ajax-alert" style="display:none; margin-bottom: 16px;"></div>

                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="upd-demo-url"><i class="fa fa-external-link"></i> {{ __('messages.live_preview_url') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="url"
                                        id="upd-demo-url"
                                        name="demo_url"
                                        value="{{ old('demo_url', $liveDemoUrl) }}"
                                        placeholder="https://preview.example.com"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="upd-video-url"><i class="fa fa-video-camera"></i> {{ __('messages.video_preview_url') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="url"
                                        id="upd-video-url"
                                        name="video_url"
                                        value="{{ old('video_url', $videoPreviewUrl) }}"
                                        placeholder="https://www.youtube.com/watch?v=..."
                                    >
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px;">
                            <label style="display: block; font-weight: bold; margin-bottom: 8px;">
                                <i class="fa fa-picture-o"></i> {{ __('messages.screenshots_gallery') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small>
                            </label>

                            <input type="hidden" name="has_screenshots_section" value="1">

                            <div class="store-screenshots-dropzone" style="border: 2px dashed #3f4863; border-radius: 12px; padding: 22px 16px; text-align: center; background: rgba(0,0,0,0.06); transition: all .2s ease;">
                                <div style="font-size: 32px; color: #615dfa; margin-bottom: 6px;">
                                    <i class="fa fa-cloud-upload"></i>
                                </div>
                                <p style="margin: 0 0 6px; font-weight: 600;">{{ __('messages.add_screenshot') }}</p>
                                <p style="margin: 0 0 14px; font-size: 13px; color: #8f94b5;">PNG, JPG, WEBP &bull; Max 10MB</p>
                                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                    <button type="button" class="button secondary small" id="btn-browse-screenshots-update">
                                        <i class="fa fa-folder-open"></i>&nbsp; {{ __('messages.upload') }}
                                    </button>
                                    <button type="button" class="button white small" id="btn-add-screenshot-url-update">
                                        <i class="fa fa-link"></i>&nbsp; {{ __('messages.ext_link') }}
                                    </button>
                                </div>
                                <input type="file" id="screenshots-file-input-update" multiple accept="image/*" style="display: none;">
                            </div>

                            <div id="screenshots-preview-grid-update" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-top: 16px;">
                                @if(isset($screenshots))
                                    @foreach($screenshots as $ss)
                                        <div class="screenshot-preview-item" data-url="{{ $ss->url }}" style="position:relative;border-radius:10px;overflow:hidden;border:1px solid #3f4863;background:#181f29;box-shadow:0 4px 10px rgba(0,0,0,0.2);aspect-ratio:16/10;">
                                            <img src="{{ Str::startsWith($ss->url, ['http://', 'https://']) ? $ss->url : asset($ss->url) }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='{{ theme_asset('img/error_plug.png') }}'">
                                            <button type="button" class="btn-remove-screenshot" style="position:absolute;top:6px;right:6px;background:rgba(231,76,60,0.85);color:#fff;border:none;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;transition:transform .15s ease;" title="{{ __('messages.remove_screenshot') }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <input type="hidden" name="screenshots[]" value="{{ $ss->url }}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top: 22px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <span id="media-save-status" style="font-size:13px; color:#8f94b5;"></span>
                            <button type="button" id="btn-save-media" class="button primary">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="widget-box store-editor-card">

                    <p class="widget-box-title">{{ __('messages.price_pts') }}</p>
                    <div class="widget-box-content">
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="pts_update">{{ __('messages.price_pts') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="number"
                                        id="pts_update"
                                        name="pts"
                                        value="{{ old('pts') }}"
                                        placeholder="{{ __('messages.current') }}: {{ $product->o_order }}"
                                        min="0"
                                        max="999999"
                                        style="width:100%;"
                                        form="priceForm"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale_price">{{ __('messages.sale_price') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="number"
                                        id="sale_price"
                                        name="sale_price"
                                        value="{{ old('sale_price', $product->sale ? $product->sale->sale_price : '') }}"
                                        placeholder="{{ __('messages.sale_price_hint') }}"
                                        min="0"
                                        max="999999"
                                        style="width:100%;"
                                        form="priceForm"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="form-row split" style="margin-top: 15px;">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale_start">{{ __('messages.sale_start') }}</label>
                                    <input
                                        type="datetime-local"
                                        id="sale_start"
                                        name="sale_start"
                                        value="{{ old('sale_start', $product->sale && $product->sale->start_date ? $product->sale->start_date->format('Y-m-d\TH:i') : '') }}"
                                        style="width:100%;"
                                        form="priceForm"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale_end">{{ __('messages.sale_end') }}</label>
                                    <input
                                        type="datetime-local"
                                        id="sale_end"
                                        name="sale_end"
                                        value="{{ old('sale_end', $product->sale && $product->sale->end_date ? $product->sale->end_date->format('Y-m-d\TH:i') : '') }}"
                                        style="width:100%;"
                                        form="priceForm"
                                    >
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:flex-end; margin-top: 15px;">
                            <button type="submit" form="priceForm" class="button primary">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="store-editor-aside">
                <div class="widget-box store-editor-card store-editor-sticky">
                    <p class="widget-box-title">{{ $product->name }}</p>
                    <div class="widget-box-content">
                        <div class="store-editor-summary-list">
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.current') }}</span>
                                <strong>{{ $product->name }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.Version_nbr') }}</span>
                                <strong data-store-update-version>{{ old('vnbr') ?: $latestVersionName }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.price_pts') }}</span>
                                <strong data-store-update-price>{{ old('pts') !== null && old('pts') !== '' ? old('pts') : $product->o_order }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.file_versions') }}</span>
                                <strong>{{ $files->count() }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.file') }}</span>
                                <strong data-store-update-source>{{ filter_var($updateLinkzipValue, FILTER_VALIDATE_URL) ? __('messages.ext_link') : __('messages.upload') }}</strong>
                            </div>
                        </div>

                        <div class="store-editor-actions" style="margin-top:18px;">
                            <a href="{{ route('store.show', $product->name) }}" class="button secondary">{{ $product->name }}</a>
                            <button type="submit" name="submit" id="button" value="Publish" class="button primary">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>

    <form id="priceForm" method="post" action="{{ route('store.update.price', $product->name) }}">
        @csrf
    </form>

    <details class="widget-box store-editor-card store-editor-history">
        <summary>
            <span>{{ __('messages.file_versions') }}</span>
            <span class="store-editor-history__badge">{{ $files->count() }}</span>
        </summary>

        <div class="widget-box-content">
            @if($files->count() > 0)
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th><center>ID</center></th>
                            <th><center>{{ __('messages.version') }}</center></th>
                            <th><center>{{ __('messages.download') }}</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            @php
                                $fileHash = hash('crc32', $file->o_mode . $file->id);
                                $fileDownloads = \App\Models\Short::where('sh_type', 7867)->where('tp_id', $file->id)->value('clik') ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $file->id }}</td>
                                <td><center><b>{{ $file->name }}</b></center></td>
                                <td>
                                    <center>
                                        <a href="{{ route('store.download.hash', $fileHash) }}" class="button secondary" style="color: #fff;">&nbsp;<i class="fa fa-download"></i>&nbsp;{{ __('messages.download') }}&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b>{{ $fileDownloads }}</b></font></span>&nbsp;</a>
                                    </center>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="widget-box-text">{{ __('messages.no_files') }}</p>
            @endif
        </div>
    </details>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '';

        // Summary sync
        function syncUpdateSummary() {
            var version = document.getElementById('upd-version');
            var price = document.getElementById('pts_update');
            var sourcePicker = document.querySelector('[data-store-source-picker]');
            var sourceText = '{{ __('messages.upload') }}';

            if (sourcePicker && sourcePicker.dataset.mode === 'link') {
                sourceText = '{{ __('messages.ext_link') }}';
            }

            var verStrong = document.querySelector('[data-store-update-version]');
            if (verStrong) verStrong.textContent = version && version.value ? version.value : '{{ $latestVersionName }}';

            var priceStrong = document.querySelector('[data-store-update-price]');
            if (priceStrong) priceStrong.textContent = price && price.value ? price.value : '{{ $product->o_order }}';

            var sourceStrong = document.querySelector('[data-store-update-source]');
            if (sourceStrong) sourceStrong.textContent = sourceText;
        }

        var updVersionEl = document.getElementById('upd-version');
        var ptsUpdateEl = document.getElementById('pts_update');
        if (updVersionEl) updVersionEl.addEventListener('input', syncUpdateSummary);
        if (ptsUpdateEl) ptsUpdateEl.addEventListener('input', syncUpdateSummary);

        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-store-source-tab]')) {
                window.setTimeout(syncUpdateSummary, 0);
            }
        });
        syncUpdateSummary();

        // Cover Image Uploader & Saver
        var openImgUploadUpdate = document.getElementById('OpenImgUploadUpdate');
        var imguploadUpdate = document.getElementById('imgupload_update');
        if (openImgUploadUpdate && imguploadUpdate) {
            openImgUploadUpdate.addEventListener('click', function () {
                imguploadUpdate.click();
            });
        }

        if (imguploadUpdate) {
            imguploadUpdate.addEventListener('change', function () {
                var file = this.files[0];
                if (!file) return;

                var showBox = document.getElementById('showImgUploadUpdate');
                if (showBox) {
                    showBox.innerHTML = "<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div></div>";
                }

                var formData = new FormData();
                formData.append('fimg', file);
                formData.append('_token', csrfToken);

                fetch("{{ route('status.upload_image') }}", {
                    method: "POST",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(function (res) { return res.text(); })
                .then(function (html) {
                    if (showBox) showBox.innerHTML = html;
                })
                .catch(function () {
                    if (showBox) showBox.innerHTML = '<span style="color:#e74c3c;">{{ __('messages.error_occurred') }}</span>';
                });
            });
        }

        var btnSaveCover = document.getElementById('btn-save-cover');
        if (btnSaveCover) {
            btnSaveCover.addEventListener('click', function () {
                var imgInput = document.querySelector('input[name="img"]');
                var imgVal = imgInput ? imgInput.value : '';
                var alertDiv = document.getElementById('store-img-ajax-alert');

                if (!imgVal) {
                    if (alertDiv) {
                        alertDiv.innerHTML = '<div class="alert alert-warning"><i class="fa fa-info-circle"></i>&nbsp; {{ __('messages.select_file') ?? 'الرجاء اختيار صورة أولاً' }}</div>';
                        alertDiv.style.display = 'block';
                    }
                    return;
                }

                var origBtnHtml = btnSaveCover.innerHTML;
                btnSaveCover.disabled = true;
                btnSaveCover.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.saving') ?? 'جاري الحفظ...' }}';

                fetch("{{ route('store.update.media', $product->name) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        img: imgVal,
                        _token: csrfToken
                    })
                })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function (res) {
                    btnSaveCover.disabled = false;
                    btnSaveCover.innerHTML = origBtnHtml;
                    if (alertDiv) {
                        if (res.ok && res.data && res.data.success) {
                            alertDiv.innerHTML = '<div class="alert alert-success"><i class="fa fa-check-circle"></i>&nbsp; ' + (res.data.message || '{{ __('messages.updated_successfully') }}') + '</div>';
                            alertDiv.style.display = 'block';
                        } else {
                            alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp; ' + (res.data?.message || res.data?.error || '{{ __('messages.error_occurred') }}') + '</div>';
                            alertDiv.style.display = 'block';
                        }
                    }
                })
                .catch(function () {
                    btnSaveCover.disabled = false;
                    btnSaveCover.innerHTML = origBtnHtml;
                    if (alertDiv) {
                        alertDiv.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp; {{ __('messages.error_occurred') }}</div>';
                        alertDiv.style.display = 'block';
                    }
                });
            });
        }

        // Screenshots Gallery
        var gridUpdate = document.getElementById('screenshots-preview-grid-update');
        var fileInputUpdate = document.getElementById('screenshots-file-input-update');
        var btnBrowseUpdate = document.getElementById('btn-browse-screenshots-update');
        var btnAddUrlUpdate = document.getElementById('btn-add-screenshot-url-update');
        var dropzoneUpdate = document.querySelector('.store-screenshots-dropzone');

        function addScreenshotItem(url, fullUrl) {
            if (!gridUpdate) return;
            var div = document.createElement('div');
            div.className = 'screenshot-preview-item';
            div.dataset.url = url;
            div.setAttribute('style', 'position:relative;border-radius:10px;overflow:hidden;border:1px solid #3f4863;background:#181f29;box-shadow:0 4px 10px rgba(0,0,0,0.2);aspect-ratio:16/10;');
            div.innerHTML = '<img src="' + fullUrl + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.src=\'{{ theme_asset("img/error_plug.png") }}\'">' +
                '<button type="button" class="btn-remove-screenshot" style="position:absolute;top:6px;right:6px;background:rgba(231,76,60,0.85);color:#fff;border:none;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;transition:transform .15s ease;" title="{{ __('messages.remove_screenshot') }}">' +
                    '<i class="fa fa-times"></i>' +
                '</button>' +
                '<input type="hidden" name="screenshots[]" value="' + url + '">';
            gridUpdate.appendChild(div);
        }

        if (btnBrowseUpdate && fileInputUpdate) {
            btnBrowseUpdate.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInputUpdate.click();
            });
        }

        function uploadScreenshotFiles(files) {
            if (!files || !files.length) return;
            for (var i = 0; i < files.length; i++) {
                (function(file) {
                    var tempId = 'ss-upd-uploading-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
                    var placeholder = document.createElement('div');
                    placeholder.id = tempId;
                    placeholder.setAttribute('style', 'border-radius:10px;border:1px dashed #615dfa;background:#181f29;display:flex;align-items:center;justify-content:center;aspect-ratio:16/10;color:#615dfa;font-size:20px;');
                    placeholder.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    if (gridUpdate) gridUpdate.appendChild(placeholder);

                    var formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', csrfToken);

                    fetch("{{ route('store.upload_screenshot') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function(res) {
                        var el = document.getElementById(tempId);
                        if (el) el.remove();
                        if (res.ok && res.data && res.data.success && res.data.url) {
                            addScreenshotItem(res.data.url, res.data.full_url || res.data.url);
                        } else {
                            alert(res.data?.message || '{{ __('messages.error_occurred') }}');
                        }
                    })
                    .catch(function() {
                        var el = document.getElementById(tempId);
                        if (el) el.remove();
                        alert('{{ __('messages.error_occurred') }}');
                    });
                })(files[i]);
            }
        }

        if (fileInputUpdate) {
            fileInputUpdate.addEventListener('change', function() {
                uploadScreenshotFiles(this.files);
                this.value = '';
            });
        }

        if (btnAddUrlUpdate) {
            btnAddUrlUpdate.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var url = prompt('{{ __('messages.screenshot_url') }}:');
                if (url && url.trim()) {
                    url = url.trim();
                    addScreenshotItem(url, url);
                }
            });
        }

        // Drag & drop on dropzone
        if (dropzoneUpdate) {
            ['dragenter', 'dragover'].forEach(function(ev) {
                dropzoneUpdate.addEventListener(ev, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneUpdate.style.borderColor = '#615dfa';
                    dropzoneUpdate.style.background = 'rgba(97,93,250,0.12)';
                }, false);
            });
            ['dragleave', 'drop'].forEach(function(ev) {
                dropzoneUpdate.addEventListener(ev, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneUpdate.style.borderColor = '#3f4863';
                    dropzoneUpdate.style.background = 'rgba(0,0,0,0.06)';
                }, false);
            });
            dropzoneUpdate.addEventListener('drop', function(e) {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    uploadScreenshotFiles(e.dataTransfer.files);
                }
            }, false);
        }

        // Remove screenshot handler
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.btn-remove-screenshot');
            if (btn) {
                var item = btn.closest('.screenshot-preview-item');
                if (item) {
                    item.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.85)';
                    setTimeout(function() { item.remove(); }, 200);
                }
            }
        });

        // Dedicated Save Media & Live Preview Handler
        var btnSaveMedia = document.getElementById('btn-save-media');
        if (btnSaveMedia) {
            btnSaveMedia.addEventListener('click', function(e) {
                e.preventDefault();

                var demoUrl = document.getElementById('upd-demo-url')?.value || '';
                var videoUrl = document.getElementById('upd-video-url')?.value || '';
                var coverImg = document.querySelector('input[name="img"]')?.value || '';

                var screenshotInputs = document.querySelectorAll('#screenshots-preview-grid-update input[name="screenshots[]"]');
                var screenshots = [];
                screenshotInputs.forEach(function(inp) {
                    if (inp.value && inp.value.trim()) screenshots.push(inp.value.trim());
                });

                var origBtnHtml = btnSaveMedia.innerHTML;
                btnSaveMedia.disabled = true;
                btnSaveMedia.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.saving') ?? 'جاري الحفظ...' }}';

                var alertDiv = document.getElementById('store-media-ajax-alert');
                if (alertDiv) alertDiv.style.display = 'none';

                var payload = {
                    demo_url: demoUrl,
                    video_url: videoUrl,
                    screenshots: screenshots,
                    has_screenshots_section: 1,
                    img: coverImg,
                    _token: csrfToken
                };

                fetch("{{ route('store.update.media', $product->name) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function(result) {
                    btnSaveMedia.disabled = false;
                    btnSaveMedia.innerHTML = origBtnHtml;

                    if (alertDiv) {
                        if (result.ok && result.data && result.data.success) {
                            alertDiv.innerHTML = '<div class="alert alert-success" style="border-radius:10px; padding:12px 16px;"><i class="fa fa-check-circle"></i>&nbsp; ' + (result.data.message || '{{ __('messages.media_updated_successfully') }}') + '</div>';
                            alertDiv.style.display = 'block';
                            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        } else {
                            var errMsg = result.data?.message || result.data?.error || '{{ __('messages.error_occurred') }}';
                            if (result.data?.errors) {
                                errMsg += '<ul style="margin: 8px 0 0; padding-inline-start: 20px;">';
                                Object.keys(result.data.errors).forEach(function(k) {
                                    result.data.errors[k].forEach(function(m) { errMsg += '<li>' + m + '</li>'; });
                                });
                                errMsg += '</ul>';
                            }
                            alertDiv.innerHTML = '<div class="alert alert-danger" style="border-radius:10px; padding:12px 16px;"><i class="fa fa-exclamation-triangle"></i>&nbsp; ' + errMsg + '</div>';
                            alertDiv.style.display = 'block';
                            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                })
                .catch(function() {
                    btnSaveMedia.disabled = false;
                    btnSaveMedia.innerHTML = origBtnHtml;
                    if (alertDiv) {
                        alertDiv.innerHTML = '<div class="alert alert-danger" style="border-radius:10px; padding:12px 16px;"><i class="fa fa-exclamation-triangle"></i>&nbsp; {{ __('messages.error_occurred') }}</div>';
                        alertDiv.style.display = 'block';
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            });
        }

        // Version Update Form Submission
        var addStoreForm = document.getElementById('addstore');
        if (addStoreForm) {
            addStoreForm.addEventListener('submit', function (event) {
                if (!addStoreForm.checkValidity()) {
                    event.preventDefault();
                    if (typeof addStoreForm.reportValidity === 'function') addStoreForm.reportValidity();
                    return;
                }

                event.preventDefault();
                var submitBtn = addStoreForm.querySelector('button[type="submit"]:not([form])');
                var origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.updating') }}';
                }

                var formData = new FormData(addStoreForm);

                fetch(addStoreForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function (result) {
                    var res = result.data;
                    if (result.ok && res && res.success && res.redirect_url) {
                        var alertEl = document.getElementById('store-update-ajax-alert');
                        if (alertEl) {
                            alertEl.innerHTML = '<div class="alert alert-success"><strong><i class="fa fa-check-circle"></i></strong>&nbsp; ' + (res.message || '{{ __('messages.updated_successfully') }}') + '</div>';
                            alertEl.style.display = 'block';
                        }
                        window.location.href = res.redirect_url;
                    } else if (res && res.redirect_url) {
                        window.location.href = res.redirect_url;
                    } else if (result.ok) {
                        window.location.reload();
                    } else {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origBtnHtml;
                        }
                        var errHtml = '<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i></strong>&nbsp; ';
                        if (res && res.errors) {
                            errHtml += '<ul style="margin: 8px 0 0; padding-inline-start: 20px;">';
                            Object.keys(res.errors).forEach(function(k) {
                                res.errors[k].forEach(function(msg) { errHtml += '<li>' + msg + '</li>'; });
                            });
                            errHtml += '</ul>';
                        } else if (res && res.message) {
                            errHtml += res.message;
                        } else if (res && res.error) {
                            errHtml += res.error;
                        } else {
                            errHtml += '{{ __('messages.error_occurred') }}';
                        }
                        errHtml += '</div>';
                        var alertBox = document.getElementById('store-update-ajax-alert');
                        if (alertBox) {
                            alertBox.innerHTML = errHtml;
                            alertBox.style.display = 'block';
                            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                })
                .catch(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnHtml;
                    }
                    var alertBox = document.getElementById('store-update-ajax-alert');
                    if (alertBox) {
                        alertBox.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp; {{ __('messages.error_occurred') }}</div>';
                        alertBox.style.display = 'block';
                        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            });
        }
    });
</script>

<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stackedit = new Stackedit();
    document.querySelectorAll('.open-stackedit').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const textarea = document.querySelector(targetId);
            const articleName = '{{ $product->name }} Update Notes';
            
            stackedit.openFile({
                name: articleName,
                content: {
                    text: textarea.value
                }
            });

            const adjustIframe = () => {
                const iframe = document.querySelector('iframe[src*="stackedit.io"]');
                if (iframe) {
                    const header = document.querySelector('.header, .nxl-header');
                    if (header) {
                        const headerHeight = header.offsetHeight;
                        iframe.style.top = headerHeight + 'px';
                        iframe.style.height = `calc(100% - ${headerHeight}px)`;
                    } else {
                        iframe.style.top = '80px';
                        iframe.style.height = 'calc(100% - 80px)';
                    }
                } else {
                    setTimeout(adjustIframe, 50);
                }
            };
            adjustIframe();

            stackedit.off('fileChange');
            stackedit.on('fileChange', (file) => {
                textarea.value = file.content.text;
            });
        });
    });
});
</script>
@endsection
