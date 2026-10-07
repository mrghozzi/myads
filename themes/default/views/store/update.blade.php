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
                                    <textarea id="upd-desc" name="desc" minlength="10" maxlength="2400" style="width:100%;padding:10px;" required>{{ old('desc') }}</textarea>
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
                            <small style="color:#8f94b5;display:block;margin-top:12px;">{{ __('messages.current') }}: <a href="{{ $product->o_mode }}" target="_blank" rel="noopener noreferrer">{{ $product->o_mode }}</a></small>
                        @endif
                    </div>
                </div>

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.media_and_demo') }}</p>
                    <p class="widget-box-text">{{ __('messages.screenshots_gallery') }} &amp; {{ __('messages.live_preview') }}</p>

                    <div class="widget-box-content">
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
                                            <button type="button" class="btn-remove-screenshot" style="position:absolute;top:6px;right:6px;background:rgba(231,76,60,0.85);color:#fff;border:none;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;" title="{{ __('messages.remove_screenshot') }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <input type="hidden" name="screenshots[]" value="{{ $ss->url }}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
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
    function syncUpdateSummary() {
        var version = document.getElementById('upd-version');
        var price = document.getElementById('pts_update');
        var sourcePicker = document.querySelector('[data-store-source-picker]');
        var sourceText = '{{ __('messages.upload') }}';

        if (sourcePicker && sourcePicker.dataset.mode === 'link') {
            sourceText = '{{ __('messages.ext_link') }}';
        }

        document.querySelector('[data-store-update-version]').textContent = version && version.value ? version.value : '{{ $latestVersionName }}';
        document.querySelector('[data-store-update-price]').textContent = price && price.value ? price.value : '{{ $product->o_order }}';
        document.querySelector('[data-store-update-source]').textContent = sourceText;
    }

    document.getElementById('OpenImgUploadUpdate').addEventListener('click', function () {
        document.getElementById('imgupload_update').click();
    });

    $(document).ready(function () {
        var token = $('meta[name="csrf-token"]').attr('content');

        $('#imgupload_update').change(function () {
            $("#showImgUploadUpdate").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div></div>");
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            form.append('_token', token);
            $.ajax({
                url: "{{ route('status.upload_image') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function (response) {
                    $('#showImgUploadUpdate').html(response);
                }
            });
        });

        // Screenshots Uploader for Update Page
        var $screenshotsGridUpdate = $('#screenshots-preview-grid-update');

        function addScreenshotItemUpdate(url, fullUrl) {
            var itemHtml = '<div class="screenshot-preview-item" data-url="' + url + '" style="position:relative;border-radius:10px;overflow:hidden;border:1px solid #3f4863;background:#181f29;box-shadow:0 4px 10px rgba(0,0,0,0.2);aspect-ratio:16/10;">' +
                '<img src="' + fullUrl + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.src=\'{{ theme_asset("img/error_plug.png") }}\'">' +
                '<button type="button" class="btn-remove-screenshot" style="position:absolute;top:6px;right:6px;background:rgba(231,76,60,0.85);color:#fff;border:none;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;" title="{{ __('messages.remove_screenshot') }}">' +
                    '<i class="fa fa-times"></i>' +
                '</button>' +
                '<input type="hidden" name="screenshots[]" value="' + url + '">' +
            '</div>';
            $screenshotsGridUpdate.append(itemHtml);
        }

        $('#btn-browse-screenshots-update').on('click', function() {
            $('#screenshots-file-input-update').click();
        });

        $('#screenshots-file-input-update').on('change', function() {
            var files = this.files;
            if (!files || !files.length) return;

            for (var i = 0; i < files.length; i++) {
                (function(file) {
                    var formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', token);

                    var tempId = 'ss-upd-uploading-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
                    $screenshotsGridUpdate.append('<div id="' + tempId + '" style="border-radius:10px;border:1px dashed #615dfa;background:#181f29;display:flex;align-items:center;justify-content:center;aspect-ratio:16/10;color:#615dfa;font-size:20px;"><i class="fa fa-spinner fa-spin"></i></div>');

                    $.ajax({
                        url: "{{ route('store.upload_screenshot') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: { 'Accept': 'application/json' },
                        success: function(res) {
                            $('#' + tempId).remove();
                            if (res && res.success && res.url) {
                                addScreenshotItemUpdate(res.url, res.full_url || res.url);
                            }
                        },
                        error: function() {
                            $('#' + tempId).remove();
                            alert('{{ __('messages.error_occurred') }}');
                        }
                    });
                })(files[i]);
            }
            $(this).val('');
        });

        $('#btn-add-screenshot-url-update').on('click', function() {
            var url = prompt('{{ __('messages.screenshot_url') }}:');
            if (url && url.trim()) {
                url = url.trim();
                addScreenshotItemUpdate(url, url);
            }
        });

        $(document).on('click', '.btn-remove-screenshot', function() {
            $(this).closest('.screenshot-preview-item').fadeOut(200, function() { $(this).remove(); });
        });

        // AJAX Form Submission for Product Update
        $('#addstore').on('submit', function (event) {
            var form = this;
            if (!form.checkValidity()) {
                event.preventDefault();
                if (typeof form.reportValidity === 'function') form.reportValidity();
                return;
            }

            event.preventDefault();
            var $btn = $(form).find('button[type="submit"]');
            var origBtnHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.updating') }}');

            var formData = new FormData(form);

            $.ajax({
                url: form.action,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                success: function (res) {
                    if (res && res.success && res.redirect_url) {
                        $('#store-update-ajax-alert').html('<div class="alert alert-success"><strong><i class="fa fa-check-circle"></i></strong>&nbsp; ' + (res.message || '{{ __('messages.updated_successfully') }}') + '</div>').show();
                        window.location.href = res.redirect_url;
                    } else if (res && res.redirect_url) {
                        window.location.href = res.redirect_url;
                    } else {
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(origBtnHtml);
                    var errHtml = '<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i></strong>&nbsp; ';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        errHtml += '<ul style="margin: 8px 0 0; padding-inline-start: 20px;">';
                        Object.keys(errors).forEach(function(k) {
                            errors[k].forEach(function(msg) { errHtml += '<li>' + msg + '</li>'; });
                        });
                        errHtml += '</ul>';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errHtml += xhr.responseJSON.message;
                    } else {
                        errHtml += '{{ __('messages.error_occurred') }}';
                    }
                    errHtml += '</div>';
                    $('#store-update-ajax-alert').html(errHtml).show()[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });

        $('#upd-version, #pts_update').on('input', syncUpdateSummary);
        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-store-source-tab]')) {
                window.setTimeout(syncUpdateSummary, 0);
            }
        });

        syncUpdateSummary();
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
