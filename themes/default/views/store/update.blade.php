@extends('theme::layouts.master')

@section('content')
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
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="vnbr">{{ __('messages.Version_nbr') }}</label>
                                <input type="text" id="vnbr" name="vnbr" placeholder="{{ __('messages.version') }} | EX: v1.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small full">
                                <label for="desc">{{ __('messages.desc') }}</label>
                                <textarea id="desc" name="desc" minlength="10" maxlength="2400" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="media" style="background-color: #8e44ad; color: #fff;">{{ __('messages.file') }}</label>
                                <input type="file" class="form-control" style="font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff;" accept=".zip" name="fzip" id="media">
                                <br />
                                <div class="result">
                                    <input type="txt" style="visibility:hidden" value="" name="linkzip" id="text" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-item split">
                        <button type="submit" name="submit" id="button" value="Publish" class="button primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
            <div class="widget-box-status-content">
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
    $(document).ready(function(){
        var token = $('meta[name="csrf-token"]').attr('content');
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
    });
</script>
@endsection
