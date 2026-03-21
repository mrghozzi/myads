@extends('theme::layouts.master')

@section('content')
@php
    $createLinkzipValue = old('linkzip', '');
@endphp

@include('theme::store.partials.editor-assets')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>

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
                                <div class="form-input small active">
                                    <label for="cat_s">{{ __('messages.cat') }}</label>
                                    <div id="storecat">
                                        <select class="form-control cat_s" id="cat_s" name="cat_s" required>
                                            <option value="">-- Select a categorie --</option>
                                            @foreach($storeCategories as $category)
                                                <option value="{{ $category->name }}" @selected(old('cat_s') === $category->name)>{{ __($category->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input">
                                    <label for="editor1">{{ __('messages.topic') }}</label>
                                    <textarea name="txt" id="editor1" rows="15" required>{{ old('txt') }}</textarea>
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
                                <strong data-store-create-category>{{ old('cat_s') ? __(old('cat_s')) : '--' }}</strong>
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

        $(document).on('change', '#cat_s', function () {
            $("#storecat").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var cat_s = $(this).val();

            $.ajax({
                type: "POST",
                url: "{{ route('store.categories') }}",
                data: { cat_s: cat_s, _token: token },
                cache: false,
                success: function (html) {
                    $("#storecat").html(html);
                    syncCreateSummary();
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
</script>

<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/{{ app()->getLocale() }}.js"></script>

<script>
    var textarea = document.getElementById('editor1');
    sceditor.create(textarea, {
        format: 'xhtml',
        locale: '{{ app()->getLocale() }}',
        emoticons: {
            dropdown: {
                @php $c = 1; @endphp
                @foreach($emojis as $emoji)
                    @if($c == 11)
                        }, more: {
                    @endif
                    '{{ $emoji->name }}': '{{ theme_asset($emoji->img) }}',
                    @php $c++; @endphp
                @endforeach
            }
        },
        style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
    });
</script>
@endsection
