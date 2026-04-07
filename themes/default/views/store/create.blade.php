@extends('theme::layouts.master')

@section('content')
@php
    $createLinkzipValue = old('linkzip', '');
@endphp

@include('theme::store.partials.editor-assets')

<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title"><span><i class="fa fa-cart-plus" aria-hidden="true"></i></span>&nbsp;{{ __('messages.add_product') }}</p>
    <p class="section-banner-text">{{ __('messages.landing_community_store_desc') }}</p>
</div>

<div class="store-editor-page">
    <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.store') }}">
        @csrf

        <div class="store-editor-layout">
            <div class="store-editor-main">
                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.add_product') }}</p>
                    <p class="widget-box-text">{{ __('messages.store') }}</p>

                    <div class="widget-box-content">
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
                                        maxlength="35"
                                        pattern="^[-a-zA-Z0-9_]+$"
                                        required
                                    >
                                    <div id="msg_name">
                                        <input type="text" style="visibility:hidden" value="{{ old('vname') }}" name="vname" required>
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
    $('#OpenImgUpload').click(function () {
        $('#imgupload').trigger('click');
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

        // window.triggerCategoryUpdate is now defined globally above to avoid race conditions with deferred jQuery.


        $('#store-version, #store-price').on('input', syncCreateSummary);
        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-store-source-tab]')) {
                window.setTimeout(syncCreateSummary, 0);
            }
        });

        syncCreateSummary();
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
