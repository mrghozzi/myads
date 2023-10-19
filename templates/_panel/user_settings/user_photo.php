<?php if(isset($s_st)=="buyfgeufb"){ dinstall_d();
if(isset($_GET['p'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['p'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('user_settings/nav_settings');  ?>
</div>
<div class="grid-column" >
<div class="grid grid-3-3-3 centered">
            <!-- USER PREVIEW -->
            <div class="user-preview small fixed-height">
              <!-- USER PREVIEW COVER -->
              <figure class="user-preview-cover liquid" >
                <img src="<?php echo $us_cover; ?>" alt="cover" style="display: none;">
              </figure>
              <!-- /USER PREVIEW COVER -->

              <!-- USER PREVIEW INFO -->
              <div class="user-preview-info">
                <!-- USER SHORT DESCRIPTION -->
                <div class="user-short-description small">
                  <!-- USER SHORT DESCRIPTION AVATAR -->
                  <div class="user-short-description-avatar user-avatar">
                    <!-- USER AVATAR BORDER -->
                    <div class="user-avatar-border">
                      <!-- HEXAGON -->
                      <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR BORDER -->

                    <!-- USER AVATAR CONTENT -->
                    <div class="user-avatar-content">
                      <!-- HEXAGON -->
                      <div class="hexagon-image-68-74" data-src="<?php echo $url_site."/".$sus['img']; ?>" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR CONTENT -->

                    <!-- USER AVATAR PROGRESS BORDER -->
                    <div class="user-avatar-progress-border">
                      <!-- HEXAGON -->
                      <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR PROGRESS BORDER -->

                 </div>
                  <!-- /USER SHORT DESCRIPTION AVATAR -->
                </div>
                <!-- /USER SHORT DESCRIPTION -->
              </div>
              <!-- /USER PREVIEW INFO -->
            </div>
            <!-- /USER PREVIEW -->

            <!-- UPLOAD BOX -->
            <div class="upload-box" id="AvatarUpload">
              <!-- UPLOAD BOX ICON -->
              <svg class="upload-box-icon icon-members">
                <use xlink:href="#svg-members"></use>
              </svg>
              <!-- /UPLOAD BOX ICON -->

              <!-- UPLOAD BOX TITLE -->
              <p class="upload-box-title">Change Avatar</p>
              <!-- /UPLOAD BOX TITLE -->

              <!-- UPLOAD BOX TEXT -->
              <p class="upload-box-text">110x110px size minimum</p>
              <!-- /UPLOAD BOX TEXT -->
            </div>
            <!-- /UPLOAD BOX -->
             <input type="file" id="Avatarload" accept=".jpg, .jpeg, .png, .gif" style="display:none">
            <!-- UPLOAD BOX -->
            <div class="upload-box" id="CoverUpload">
              <!-- UPLOAD BOX ICON -->
              <svg class="upload-box-icon icon-photos">
                <use xlink:href="#svg-photos"></use>
              </svg>
              <!-- /UPLOAD BOX ICON -->

              <!-- UPLOAD BOX TITLE -->
              <p class="upload-box-title">Change Cover</p>
              <!-- /UPLOAD BOX TITLE -->

              <!-- UPLOAD BOX TEXT -->
              <p class="upload-box-text">1184x300px size minimum</p>
              <!-- /UPLOAD BOX TEXT -->

            </div>
            <!-- /UPLOAD BOX -->
            <input type="file" id="Coverload" accept=".jpg, .jpeg, .png, .gif" style="display:none">
          </div>
          <hr />
          <form action="<?php url_site();  ?>/requests/user_photo.php" method="POST" enctype="multipart/form-data">
          <center><br /><div  id="showImgUpload" >
          <input type="txt" name="img" style="display:none" required></div></center>
          </form>
</div>
</div>
<script> $('#AvatarUpload').click(function(){  $('#Avatarload').trigger('click'); }); </script>
<script>
    $(document).ready(function(){
        $('#Avatarload').change(function(e){
          $("#showImgUpload").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            $.ajax({
                url : "<?php url_site();  ?>/requests/up_image.php?avatar=1",
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
    });
</script>
<script>$('#CoverUpload').click(function(){ $('#Coverload').trigger('click'); });</script>
<script>
    $(document).ready(function(){
        $('#Coverload').change(function(e){
          $("#showImgUpload").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            $.ajax({
                url : "<?php url_site();  ?>/requests/up_image.php?cover=1",
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
    });
</script>
<?php }else{ echo "404"; }
 }else{ echo "404"; }  ?>