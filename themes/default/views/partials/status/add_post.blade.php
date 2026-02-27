@auth
@php
    $categories = \App\Models\DirectoryCategory::where('statu', 1)->orderBy('name', 'ASC')->get();
@endphp
<div class="quick-post">
    <style> .result img { margin-top: 24px; width: 100%; height: auto; border-radius: 12px; } </style>
    <!-- QUICK POST BODY -->
    <div class="quick-post-body">
        <!-- FORM -->
        <form class="form" action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data" id="quick-post-form">
            @csrf
            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM TEXTAREA -->
                    <div class="form-textarea">
                        <textarea id="txt" class="quicktext" name="txt" placeholder="{{ __('messages.whats_on_your_mind', ['username' => auth()->user()->username]) }}"></textarea>
                        <div class="result"></div>
                        <!-- FORM TEXTAREA LIMIT TEXT -->
                        <p class="form-textarea-limit-text"></p>
                        <!-- /FORM TEXTAREA LIMIT TEXT -->
                    </div>
                    <div class="add_link"></div>
                    <div class="ed_type"><input type="hidden" name="s_type" id="s_type" value="100" /></div>
                    <!-- /FORM TEXTAREA -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <input type="file" id="imgupload" name="fimg" accept=".jpg, .jpeg, .png, .gif" style="display:none"/>
            <input type="submit" name="submit_post" id="submit_post" value="submit" style="display:none"/>
            <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
    </div>
    <!-- /QUICK POST BODY -->

    <!-- QUICK POST FOOTER -->
    <div class="quick-post-footer">
        <!-- QUICK POST FOOTER ACTIONS -->
        <div class="quick-post-footer-actions">
            <!-- QUICK POST FOOTER ACTION -->
            <div id="OpenImgUpload" class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertphoto') }}" style="position: relative; cursor: pointer;">
                <!-- QUICK POST FOOTER ACTION ICON -->
                <svg class="quick-post-footer-action-icon icon-camera">
                    <use xlink:href="#svg-camera"></use>
                </svg>
            </div>
            <!-- /QUICK POST FOOTER ACTION -->

            <!-- QUICK POST FOOTER ACTION -->
            <div id="Open_link" class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertlink') }}" style="position: relative; color: #adafca; cursor: pointer;">
                <!-- QUICK POST FOOTER ACTION ICON -->
                <i class="fa fa-link"></i>
                <!-- /QUICK POST FOOTER ACTION ICON -->
            </div>
            <!-- /QUICK POST FOOTER ACTION -->
        </div>
        <!-- /QUICK POST FOOTER ACTIONS -->

        <!-- QUICK POST FOOTER ACTIONS -->
        <div class="quick-post-footer-actions">
            <!-- BUTTON -->
            <p id="Open_post" class="button small void" style="cursor: pointer;">&nbsp;<i class="fa fa-text-width" aria-hidden="true"></i>&nbsp;</p>
            <!-- /BUTTON -->

            <!-- BUTTON -->
            <p class="button small secondary" id="btnpost" style="cursor: pointer;">{{ __('messages.spread') }}</p>
            <!-- /BUTTON -->
        </div>
        <!-- /QUICK POST FOOTER ACTIONS -->
    </div>
    <!-- /QUICK POST FOOTER -->
</div>

<script>
    $(document).ready(function(){
        // Trigger submit
        $('#btnpost').click(function(){
            $('#submit_post').trigger('click');
        });

        // Trigger file upload
        $('#OpenImgUpload').click(function(){
            $('#imgupload').trigger('click');
        });

        // Handle file selection and AJAX upload
        $('#imgupload').change(function(e){
            $(".result").html("<div class='progress' style='margin-top:10px'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%; padding:5px; background:#40d04f; color:white; border-radius:4px; text-align:center'> {{ __('messages.uploading') }} </div> </div>");
            
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            form.append('_token', "{{ csrf_token() }}");
            
            $.ajax({
                url: "{{ route('status.upload_image') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function(response){
                    $('.result').html(response);
                    $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='4' />");
                    $('.add_link').html("");
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    $('.result').html("<p style='color: #ff0000f7;border: 1px dashed #f00;'>{{ __('messages.upload_failed') }}</p>");
                }
            });
        });

        // Handle Link Post
        $('#Open_link').click(function(){
            let options = "";
            @foreach($categories as $category)
                options += "<option value='{{ $category->id }}'>{{ addslashes($category->name) }}</option>";
            @endforeach

            let html = `
                <div class='input-group' style='margin-top:10px; display:flex; align-items:center; gap:5px;'><span class='input-group-text'><i class='fa fa-edit' ></i></span><input type='text' class='form-control' name='name' id='name' placeholder='{{ __('messages.name_placeholder') }}' autocomplete='off' required style='width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;' /></div>
                <div class='input-group' style='margin-top:10px; display:flex; align-items:center; gap:5px;'><span class='input-group-text'><i class='fa fa-link' ></i></span><input type='url' class='form-control' name='url' id='url' placeholder='{{ __('messages.url_placeholder') }}' autocomplete='off' required style='width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;' /></div>
                <div class='input-group' style='margin-top:10px; display:flex; align-items:center; gap:5px;'><span class='input-group-text'><i class='fa fa-tag' ></i></span>
                <select class='form-control' name='categ' id='categ' style='width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;'>
                    ${options}
                </select></div>
                <div class='input-group' style='margin-top:10px; display:flex; align-items:center; gap:5px;'><span class='input-group-text'><i class='fa fa-folder' ></i></span><input type='text' class='form-control' name='tag' id='tag' placeholder='{{ __('messages.tags_placeholder') }}' autocomplete='off' required style='width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;' /></div>
            `;
            $(".add_link").html(html);
            $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='1' />");
            $('.result').html("");
        });

        // Handle Text Post (Reset)
        $('#Open_post').click(function(){
            $(".add_link").html("");
            $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='100' />");
            $('.result').html("");
        });
    });
</script>
@endauth
