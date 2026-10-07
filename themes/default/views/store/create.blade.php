@extends('theme::layouts.master')

@section('content')
@php
    $createLinkzipValue = old('linkzip', '');
@endphp

@include('theme::store.partials.editor-assets')

<div class="store-editor-page">
    <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.store') }}">
        @csrf

        <div class="store-editor-layout">
            <div class="store-editor-main">
                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.add_product') }}</p>
                    <p class="widget-box-text">{{ __('messages.store') }}</p>

                    <div class="widget-box-content">
                        <div id="store-create-ajax-alert" style="display:none; margin-bottom: 20px;"></div>

                        <div class="store-editor-alerts">
                            @if(session('error'))
                                <div class="alert alert-danger" role="alert"><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></strong>&nbsp; {{ session('error') }}</div>
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
                                <div class="form-input small">
                                    <label for="store-name">{{ __('messages.titer') }}</label>
                                    <input
                                        type="text"
                                        id="store-name"
                                        class="form-control sname"
                                        name="name"
                                        value="{{ old('name') }}"
                                        minlength="3"
                                        maxlength="70"
                                        required
                                    >
                                    <small style="color: #8f94b5; display:block; margin-top:5px; font-size:12px;">
                                        <i class="fa fa-info-circle"></i> {{ __('messages.product_name_arabic_hint') }}
                                    </small>
                                    <div id="msg_name">
                                        <input type="hidden" value="{{ old('vname') }}" name="vname">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="store-desc">{{ __('messages.desc') }}</label>
                                    <input type="text" id="store-desc" class="form-control" name="desc" value="{{ old('desc') }}" minlength="10" maxlength="2400" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="store-version">{{ __('messages.Version_nbr') }}</label>
                                    <input
                                        type="text"
                                        id="store-version"
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
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="store-price">{{ __('messages.price_pts') }}</label>
                                    <input
                                        type="text"
                                        id="store-price"
                                        name="pts"
                                        value="{{ old('pts') }}"
                                        placeholder="{{ __('messages.pmbno') }}"
                                        minlength="1"
                                        maxlength="6"
                                        pattern="[0-9]+"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale-price">{{ __('messages.sale_price') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="number"
                                        id="sale-price"
                                        name="sale_price"
                                        value="{{ old('sale_price') }}"
                                        placeholder="{{ __('messages.sale_price_hint') }}"
                                        min="0"
                                        max="999999"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale-start">{{ __('messages.sale_start') }}</label>
                                    <input
                                        type="datetime-local"
                                        id="sale-start"
                                        name="sale_start"
                                        value="{{ old('sale_start') }}"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="sale-end">{{ __('messages.sale_end') }}</label>
                                    <input
                                        type="datetime-local"
                                        id="sale-end"
                                        name="sale_end"
                                        value="{{ old('sale_end') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div id="storecat">
                                    @include('theme::store.partials.category-selector')
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input" style="padding: 10px;">
                                    <label for="editor1" style="display:block;margin-bottom:10px;font-weight:bold;">{{ __('messages.topic') }}</label>
                                    <div class="stackedit-tools mb-2" style="margin-bottom:10px;">
                                        <button type="button" class="button secondary small open-stackedit" data-target="#editor1">
                                            <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp; {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                                        </button>
                                    </div>
                                    <textarea name="txt" id="editor1" rows="15" style="width:100%;padding:10px;" required>{{ old('txt') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('theme::store.partials.source-picker', [
                    'linkzipValue' => $createLinkzipValue,
                    'linkInputId' => 'store-create-direct-link',
                ])

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.img') }}</p>
                    <div class="widget-box-content">
                        <div id="OpenImgUpload" class="upload-box">
                            <svg class="upload-box-icon icon-photos">
                                <use xlink:href="#svg-photos"></use>
                            </svg>
                            <p class="upload-box-title">{{ __('messages.upload') }}</p>
                            <p class="upload-box-text">{{ __('messages.img') }}</p>
                        </div>
                        <center><br /><div id="showImgUpload"><input type="text" name="img" value="{{ old('img') }}" style="display:none" required></div></center>
                        <input type="file" id="imgupload" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                    </div>
                </div>

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.media_and_demo') }}</p>
                    <p class="widget-box-text">{{ __('messages.screenshots_gallery') }} &amp; {{ __('messages.live_preview') }}</p>

                    <div class="widget-box-content">
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="store-demo-url"><i class="fa fa-external-link"></i> {{ __('messages.live_preview_url') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="url"
                                        id="store-demo-url"
                                        name="demo_url"
                                        value="{{ old('demo_url') }}"
                                        placeholder="https://preview.example.com"
                                    >
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="store-video-url"><i class="fa fa-video-camera"></i> {{ __('messages.video_preview_url') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') }})</small></label>
                                    <input
                                        type="url"
                                        id="store-video-url"
                                        name="video_url"
                                        value="{{ old('video_url') }}"
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
                                    <button type="button" class="button secondary small" id="btn-browse-screenshots">
                                        <i class="fa fa-folder-open"></i>&nbsp; {{ __('messages.upload') }}
                                    </button>
                                    <button type="button" class="button white small" id="btn-add-screenshot-url">
                                        <i class="fa fa-link"></i>&nbsp; {{ __('messages.ext_link') }}
                                    </button>
                                </div>
                                <input type="file" id="screenshots-file-input" multiple accept="image/*" style="display: none;">
                            </div>

                            <div id="screenshots-preview-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-top: 16px;">
                                <!-- Uploaded screenshots preview items -->
                            </div>
                            <div id="screenshots-hidden-inputs"></div>
                        </div>
                    </div>
                </div>
            </div>


            <aside class="store-editor-aside">
                <div class="widget-box store-editor-card store-editor-sticky">
                    <p class="widget-box-title">{{ __('messages.add_product') }}</p>
                    <div class="widget-box-content">
                        <div class="store-editor-summary-list">
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.Version_nbr') }}</span>
                                <strong data-store-create-version>{{ old('vnbr') ?: 'v1.0' }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.price_pts') }}</span>
                                <strong data-store-create-price>{{ old('pts') ?: '--' }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.cat') }}</span>
                                <strong data-store-create-category>{{ $selectedStoreCategory ? __('messages.' . $selectedStoreCategory) : '--' }}</strong>
                            </div>
                            <div class="store-editor-summary-item">
                                <span>{{ __('messages.file') }}</span>
                                <strong data-store-create-source>{{ filter_var($createLinkzipValue, FILTER_VALIDATE_URL) ? __('messages.ext_link') : __('messages.upload') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.save') }}</p>
                    <div class="widget-box-content">
                        <div class="store-editor-actions">
                            <a href="https://github.com/mrghozzi/myads/wiki/store:update" class="button default" target="_blank">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>&nbsp;</a>
                            <button type="submit" name="submit" id="button" value="Publish" class="button primary">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var openImgUpload = document.getElementById('OpenImgUpload');
        var imguploadInput = document.getElementById('imgupload');
        if (openImgUpload && imguploadInput) {
            openImgUpload.addEventListener('click', function () {
                imguploadInput.click();
            });
        }
    });
</script>

<script>
    window.triggerCategoryUpdate = function (selectElement) {
        if (typeof jQuery === 'undefined') {
            console.warn("jQuery is not yet loaded, retrying in 100ms...");
            setTimeout(function() { window.triggerCategoryUpdate(selectElement); }, 100);
            return;
        }

        var $ = jQuery;
        var token = $('meta[name="csrf-token"]').attr('content');
        var cat_s = $(selectElement).val();
        var sc_cat = $('#sc_cat').val() || '';
        
        console.log("triggerCategoryUpdate running for: " + cat_s);

        if (!cat_s) {
            $("#storecat").html($("#storecat").attr('data-original') || $("#storecat").html());
            return;
        }

        // Store original markup for error recovery if not already stored
        if (!$("#storecat").attr('data-original')) {
            $("#storecat").attr('data-original', $("#storecat").html());
        }

        $("#storecat").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");

        $.ajax({
            type: "POST",
            url: "{{ route('store.categories') }}",
            data: { cat_s: cat_s, sc_cat: sc_cat, _token: token },
            cache: false,
            success: function (html) {
                console.log("AJAX Success for " + cat_s);
                $("#storecat").html(html);
                if (typeof syncCreateSummary === 'function') syncCreateSummary();
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: " + error);
                $("#storecat").html($("#storecat").attr('data-original'));
                if (typeof syncCreateSummary === 'function') syncCreateSummary();
            }
        });
    };
</script>

<script>
    function syncCreateSummary() {
        var version = document.getElementById('store-version');
        var price = document.getElementById('store-price');
        var category = document.getElementById('cat_s');
        var sourcePicker = document.querySelector('[data-store-source-picker]');
        var categoryText = '--';
        var sourceText = '{{ __('messages.upload') }}';

        if (category && category.options && category.selectedIndex >= 0 && category.value) {
            categoryText = category.options[category.selectedIndex].text;
        }

        if (sourcePicker && sourcePicker.dataset.mode === 'link') {
            sourceText = '{{ __('messages.ext_link') }}';
        }

        document.querySelector('[data-store-create-version]').textContent = version && version.value ? version.value : 'v1.0';
        document.querySelector('[data-store-create-price]').textContent = price && price.value ? price.value : '--';
        document.querySelector('[data-store-create-category]').textContent = categoryText;
        document.querySelector('[data-store-create-source]').textContent = sourceText;
    }

    function initStoreReady() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initStoreReady, 50);
            return;
        }

        var $ = jQuery;
        $(document).ready(function () {
            var token = $('meta[name="csrf-token"]').attr('content');

            $('#imgupload').change(function () {
                $("#showImgUpload").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
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
                        $('#showImgUpload').html(response);
                    }
                });
            });

            $('.sname').change(function () {
                $("#msg_name").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'>{{ __('messages.review') }}</div> </div> ");
                var sname = $(this).val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('store.verify_name') }}",
                    data: { sname: sname, _token: token },
                    cache: false,
                    success: function (html) {
                        $("#msg_name").html(html);
                    }
                });
            });

            // Screenshots Uploader
            var $screenshotsGrid = $('#screenshots-preview-grid');
            var $screenshotsInputs = $('#screenshots-hidden-inputs');

            function addScreenshotItem(url, fullUrl) {
                var itemHtml = '<div class="screenshot-preview-item" data-url="' + url + '" style="position:relative;border-radius:10px;overflow:hidden;border:1px solid #3f4863;background:#181f29;box-shadow:0 4px 10px rgba(0,0,0,0.2);aspect-ratio:16/10;">' +
                    '<img src="' + fullUrl + '" style="width:100%;height:100%;object-fit:cover;" onerror="this.src=\'{{ theme_asset("img/error_plug.png") }}\'">' +
                    '<button type="button" class="btn-remove-screenshot" style="position:absolute;top:6px;right:6px;background:rgba(231,76,60,0.85);color:#fff;border:none;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;" title="{{ __('messages.remove_screenshot') }}">' +
                        '<i class="fa fa-times"></i>' +
                    '</button>' +
                    '<input type="hidden" name="screenshots[]" value="' + url + '">' +
                '</div>';
                $screenshotsGrid.append(itemHtml);
            }

            $('#btn-browse-screenshots').on('click', function() {
                $('#screenshots-file-input').click();
            });

            $('#screenshots-file-input').on('change', function() {
                var files = this.files;
                if (!files || !files.length) return;

                for (var i = 0; i < files.length; i++) {
                    (function(file) {
                        var formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', token);

                        var tempId = 'ss-uploading-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
                        $screenshotsGrid.append('<div id="' + tempId + '" style="border-radius:10px;border:1px dashed #615dfa;background:#181f29;display:flex;align-items:center;justify-content:center;aspect-ratio:16/10;color:#615dfa;font-size:20px;"><i class="fa fa-spinner fa-spin"></i></div>');

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
                                    addScreenshotItem(res.url, res.full_url || res.url);
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

            $('#btn-add-screenshot-url').on('click', function() {
                var url = prompt('{{ __('messages.screenshot_url') }}:');
                if (url && url.trim()) {
                    url = url.trim();
                    addScreenshotItem(url, url);
                }
            });

            $(document).on('click', '.btn-remove-screenshot', function() {
                $(this).closest('.screenshot-preview-item').fadeOut(200, function() { $(this).remove(); });
            });

            // AJAX Form Submission
            $('#addstore').on('submit', function (event) {
                var form = this;

                if (!form.checkValidity()) {
                    event.preventDefault();

                    if (typeof form.reportValidity === 'function') {
                        form.reportValidity();
                    }

                    var firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (typeof firstInvalid.focus === 'function' && firstInvalid.type !== 'hidden') {
                            firstInvalid.focus({ preventScroll: true });
                        }
                    }
                    return;
                }

                event.preventDefault();
                var $btn = $(form).find('button[type="submit"]');
                var origBtnHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp; {{ __('messages.publishing') }}');

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
                            $('#store-create-ajax-alert').html('<div class="alert alert-success"><strong><i class="fa fa-check-circle"></i></strong>&nbsp; ' + (res.message || '{{ __('messages.product_added_successfully') }}') + '</div>').show();
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
                        $('#store-create-ajax-alert').html(errHtml).show()[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            });

            $('#store-version, #store-price').on('input', syncCreateSummary);
            document.addEventListener('click', function (event) {
                if (event.target.closest('[data-store-source-tab]')) {
                    window.setTimeout(syncCreateSummary, 0);
                }
            });

            syncCreateSummary();
        });
    }


    initStoreReady();
</script>

<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stackedit = new Stackedit();
    document.querySelectorAll('.open-stackedit').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const textarea = document.querySelector(targetId);
            const nameInput = document.getElementById('store-name');
            const articleName = nameInput && nameInput.value ? nameInput.value : 'Product Content';
            
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
