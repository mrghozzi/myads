@extends('theme::layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>

<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.update') }} | {{ $product->name }}</p>
</div>

<div class="grid grid">
    <div class="widget-box no-padding">
        <div class="widget-box-status">
            <div class="widget-box-status-content">
                <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.update.store', $product->name) }}">
                    @csrf
                    {{-- Version --}}
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="vnbr">{{ __('messages.Version_nbr') }}</label>
                                <input type="text" id="vnbr" name="vnbr" placeholder="{{ __('messages.version') }} | EX: v1.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$" required>
                            </div>
                        </div>
                    </div>
                    {{-- Description --}}
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small full">
                                <label for="desc">{{ __('messages.desc') }}</label>
                                <textarea id="desc" name="desc" minlength="10" maxlength="2400" required></textarea>
                            </div>
                        </div>
                    </div>
                    {{-- ZIP / Link Toggle --}}
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label style="background-color: #8e44ad; color: #fff;">{{ __('messages.file') }}</label>
                                <div style="display:flex;gap:8px;margin-bottom:8px;">
                                    <button type="button" class="button secondary zip-tab-btn active" id="upd_tab_upload" onclick="switchUpdateZipTab('upload')" style="flex:1;"><i class="fa fa-upload"></i>&nbsp;{{ __('messages.upload') }}</button>
                                    <button type="button" class="button secondary zip-tab-btn" id="upd_tab_link" onclick="switchUpdateZipTab('link')" style="flex:1;"><i class="fa fa-link"></i>&nbsp;{{ __('messages.ext_link') ?? 'External Link' }}</button>
                                </div>
                                <div id="upd_zip_upload_area">
                                    <input type="file" class="form-control" style="font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff;" accept=".zip" name="fzip" id="media">
                                    <br />
                                    <div class="result">
                                        <input type="txt" style="visibility:hidden" value="" name="linkzip" id="upd_text" required>
                                    </div>
                                </div>
                                <div id="upd_zip_link_area" style="display:none;">
                                    <input type="text" class="form-control" id="upd_ext_link_input" placeholder="https://example.com/file.zip" style="margin-top:4px;">
                                    <input type="text" style="visibility:hidden" value="" name="linkzip" id="upd_text_link" required>
                                    <small style="color:#aaa;">{{ __('messages.ext_link_hint') ?? 'Paste a direct download URL to your file' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Optional: Cover Image --}}
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small">
                                <label>{{ __('messages.img') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') ?? 'Optional' }})</small></label>
                                <div id="OpenImgUploadUpdate" class="upload-box" style="cursor:pointer;">
                                    <svg class="upload-box-icon icon-photos">
                                        <use xlink:href="#svg-photos"></use>
                                    </svg>
                                    <p class="upload-box-title">{{ __('messages.upload') }}</p>
                                    <p class="upload-box-text">{{ __('messages.img') }}</p>
                                </div>
                                <center><br /><div id="showImgUploadUpdate"><input type="text" name="img" style="display:none"></div></center>
                                <input type="file" id="imgupload_update" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                                @if($product->o_mode)
                                    <small style="color:#aaa;display:block;margin-top:4px;">{{ __('messages.current') ?? 'Current' }}: <a href="{{ $product->o_mode }}" target="_blank">{{ $product->o_mode }}</a></small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Optional: Price --}}
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small">
                                <label for="pts_update">{{ __('messages.price_pts') }} <small style="font-weight:normal;opacity:.7;">({{ __('messages.optional') ?? 'Optional' }})</small></label>
                                <input type="number" id="pts_update" name="pts" placeholder="{{ __('messages.current') ?? 'Current' }}: {{ $product->o_order }} — {{ __('messages.pmbno') ?? 'Leave blank to keep' }}" min="0" max="999999" style="width:100%;">
                            </div>
                        </div>
                    </div>

                    <div class="form-item split">
                        <button type="submit" name="submit" id="button" value="Publish" class="button primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
            <div class="widget-box-status-content">
                @if(session('success'))
                    <div class="alert alert-success"><i class="fa fa-check-circle"></i>&nbsp; {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i>&nbsp; {{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
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
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // ZIP / Link toggle for update page
    function switchUpdateZipTab(tab) {
        if (tab === 'upload') {
            document.getElementById('upd_zip_upload_area').style.display = '';
            document.getElementById('upd_zip_link_area').style.display = 'none';
            document.getElementById('upd_tab_upload').classList.add('active');
            document.getElementById('upd_tab_link').classList.remove('active');
            document.getElementById('upd_text').removeAttribute('disabled');
            document.getElementById('upd_text_link').setAttribute('disabled', 'disabled');
        } else {
            document.getElementById('upd_zip_upload_area').style.display = 'none';
            document.getElementById('upd_zip_link_area').style.display = '';
            document.getElementById('upd_tab_upload').classList.remove('active');
            document.getElementById('upd_tab_link').classList.add('active');
            document.getElementById('upd_text').setAttribute('disabled', 'disabled');
            document.getElementById('upd_text_link').removeAttribute('disabled');
            document.getElementById('upd_text_link').value = document.getElementById('upd_ext_link_input').value;
        }
    }
    // Init
    document.getElementById('upd_text_link').setAttribute('disabled', 'disabled');
    document.getElementById('upd_ext_link_input').addEventListener('input', function() {
        document.getElementById('upd_text_link').value = this.value;
    });

    // Optional cover image upload
    document.getElementById('OpenImgUploadUpdate').addEventListener('click', function() {
        document.getElementById('imgupload_update').click();
    });
</script>

<script>
    $(document).ready(function(){
        var token = $('meta[name="csrf-token"]').attr('content');

        // ZIP file upload via AJAX
        $('#media').change(function(e){
            $(".result").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> {{ __('messages.uploading') }} </div></div>");
            var file = this.files[0];
            var form = new FormData();
            form.append('fzip', file);
            form.append('_token', token);
            $.ajax({
                url: "{{ route('store.upload_zip') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function(response){
                    $('.result').html(response);
                }
            });
        });

        // Optional cover image upload
        $('#imgupload_update').change(function(e){
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
                success: function(response){
                    $('#showImgUploadUpdate').html(response);
                }
            });
        });
    });
</script>
@endsection
