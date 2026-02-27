@extends('theme::layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>

<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><span><i class="fa fa-cart-plus" aria-hidden="true"></i> </span>&nbsp;{{ __('messages.add_product') }}</p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
</div>
<!-- /SECTION BANNER -->

<div class="grid grid">
     <div class="widget-box no-padding">
         <div class="widget-box-status">
          <div class="widget-box-status-content" >
           <form id="addstore" method="post" class="form-horizontal" action="{{ route('store.store') }}" >
             @csrf
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small">
                      <label for="titer">{{ __('messages.titer') }}</label>
                      <input type="text" class="form-control sname" name="name" value="{{ old('name') }}" minlength="3" maxlength="35" pattern="^[-a-zA-Z0-9_]+$" required >
                       <div id="msg_name" >
                         <input type="text" style="visibility:hidden" value="" name="vname" required>
                       </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="desc">{{ __('messages.desc') }}</label>
                      <input type="text" class="form-control" name="desc" value="{{ old('desc') }}" minlength="10" maxlength="2400" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row split" >
                 <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="Version_nbr">{{ __('messages.Version_nbr') }}</label>
                      <input type="text" id="profile-name" name="vnbr" value="{{ old('vnbr') }}" placeholder="{{ __('messages.version') }} | EX: v1.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="price_pts">{{ __('messages.price_pts') }}</label>
                      <input type="text" id="profile-name" name="pts" value="{{ old('pts') }}" placeholder="{{ __('messages.pmbno') }}" minlength="1" maxlength="6" pattern="[0-9]+" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="cat">{{ __('messages.cat') }}</label>
                      <div id="storecat" >
                        <select class="form-control cat_s" id="cat_s" name="cat_s" required>
                            <option value="">-- Select a categorie --</option>
                            @foreach($storeCategories as $category)
                                <option value="{{ $category->name }}">{{ __($category->name) }}</option>
                            @endforeach
                        </select>
                      </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                      <label for="profile-name">{{ __('messages.topic') }}</label>
                      <textarea name="txt" id="editor1" rows="15" required>{{ old('txt') }}</textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name" style=" background-color: #8e44ad; color: #fff; ">{{ __('messages.file') }}</label>
                      <input type="file" class="form-control" style=" font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff; " accept=".zip" name="fzip" id="media" >
                      <br />
                       <div class="result">
                        <input type="text" style="visibility:hidden" value="{{ old('linkzip') }}" name="linkzip" id="text" required>
                       </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
               <div id="OpenImgUpload" class="upload-box">
              <!-- UPLOAD BOX ICON -->
              <svg class="upload-box-icon icon-photos">
                <use xlink:href="#svg-photos"></use>
              </svg>
              <!-- /UPLOAD BOX ICON -->

              <!-- UPLOAD BOX TITLE -->
              <p class="upload-box-title">{{ __('messages.upload') }}</p>
              <!-- /UPLOAD BOX TITLE -->

              <!-- UPLOAD BOX TEXT -->
              <p class="upload-box-text">{{ __('messages.img') }}</p>
              <!-- /UPLOAD BOX TEXT -->
            </div>
            <center><br /><div  id="showImgUpload" ><input type="text" name="img" style="display:none" required></div></center>
            <input type="file" id="imgupload" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <hr />
             <div class="form-item split">
               <!-- FORM SELECT -->
               <a href="https://github.com/mrghozzi/myads/wiki/store:update" class="button default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
               <!-- BUTTON -->
               <button type="submit" name="submit" id="button" value="Publish" class="button primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; {{ __('messages.save') }}</button>
             </div>
           </form>
          </div>
          <div class="widget-box-status-content" >
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
           <hr />
          </div>
		 </div>
	 </div>
</div>

<script>$('#OpenImgUpload').click(function(){ $('#imgupload').trigger('click'); });</script>
<script>
    $(document).ready(function(){
        var token = $('meta[name="csrf-token"]').attr('content');
        $('#imgupload').change(function(e){
          $("#showImgUpload").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            form.append('_token', token);
            $.ajax({
                url : "{{ route('status.upload_image') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data : form,
                success: function(response){
                    $('#showImgUpload').html(response)
                }
            });
        });

        $('.sname').change(function(){
          $("#msg_name").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'>{{ __('messages.review') }}</div> </div> ");
          var sname=$(this).val();
          
          $.ajax({
           type: "POST",
           url: "{{ route('store.verify_name') }}",
           data: { sname: sname, _token: token },
           cache: false,
           success: function(html)
           {
              $("#msg_name").html(html);
           }
           });
        });

        $('#media').change(function(e){
          $(".result").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fzip', file);
            form.append('_token', token);
            $.ajax({
                url : "{{ route('store.upload_zip') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data : form,
                success: function(response){
                    $('.result').html(response)
                }
            });
        });

        $(document).on('change', '#cat_s', function(){
             $("#storecat").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
             var cat_s=$(this).val();
             
             $.ajax({
               type: "POST",
               url: "{{ route('store.categories') }}",
               data: { cat_s: cat_s, _token: token },
               cache: false,
               success: function(html)
               {
                  $("#storecat").html(html);
               }
             });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/{{ app()->getLocale() }}.js"></script>

<script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('editor1');
sceditor.create(textarea, {
	format: 'xhtml',
    locale : '{{ app()->getLocale() }}',
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