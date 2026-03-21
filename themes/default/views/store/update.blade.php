@extends('theme::layouts.master')

@section('content')
@php
    $updateLinkzipValue = old('linkzip', '');
    $latestVersionName = optional($files->first())->name ?: 'v1.0';
@endphp

@include('theme::store.partials.editor-assets')

<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.update') }} | {{ $product->name }}</p>
    <p class="section-banner-text">{{ __('messages.Version_nbr') }} {{ $latestVersionName }}</p>
</div>

<div class="store-editor-page">
    <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.update.store', $product->name) }}">
        @csrf

        <div class="store-editor-layout">
            <div class="store-editor-main">
                <div class="widget-box store-editor-card">
                    <p class="widget-box-title">{{ __('messages.update') }}</p>
                    <p class="widget-box-text">{{ $product->name }}</p>

                    <div class="widget-box-content">
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
                                <div class="form-input small full">
                                    <label for="upd-desc">{{ __('messages.desc') }}</label>
                                    <textarea id="upd-desc" name="desc" minlength="10" maxlength="2400" required>{{ old('desc') }}</textarea>
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
                    <p class="widget-box-title">{{ __('messages.price_pts') }}</p>
                    <div class="widget-box-content">
                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small">
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
                                    >
                                </div>
                            </div>
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

        $('#upd-version, #pts_update').on('input', syncUpdateSummary);
        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-store-source-tab]')) {
                window.setTimeout(syncUpdateSummary, 0);
            }
        });

        syncUpdateSummary();
    });
</script>
@endsection
