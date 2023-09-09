<?php if($s_st=="buyfgeufb"){
   if(isset($_COOKIE['user']))
{
 ?>
<div class="quick-post">
<style> .result img { margin-top: 24px; width: 100%; height: auto;   border-radius: 12px; } </style>
          <!-- QUICK POST BODY -->
          <div class="quick-post-body">
            <!-- FORM -->
            <form class="form" action="<?php url_site();  ?>/requests/status.php" method="POST" enctype="multipart/form-data" >
              <!-- FORM ROW -->
              <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                  <!-- FORM TEXTAREA -->
                  <div class="form-textarea">
                    <textarea id="txt" class="quicktext" name="txt" placeholder="Hi <?php echo $uRow['username']; ?>! Share your post here..."></textarea>
                    <div class="result" ></div>
                    <!-- FORM TEXTAREA LIMIT TEXT -->
                    <p class="form-textarea-limit-text"></p>
                    <!-- /FORM TEXTAREA LIMIT TEXT -->
                  </div>
                  <div class="add_link"></div>
                  <div class="ed_type"><input type="hidden" name="s_type" value="100" /></div>
                  <!-- /FORM TEXTAREA -->
                </div>
                <!-- /FORM ITEM -->
              </div>
              <input type="file" id="imgupload" accept=".jpg, .jpeg, .png, .gif" style="display:none"/>
              <input type="hidden" name="submit_post" value="submit" />
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
              <div id="OpenImgUpload" class="quick-post-footer-action text-tooltip-tft-medium" data-title="Insert Photo" style="position: relative;">
                <!-- QUICK POST FOOTER ACTION ICON -->
                <svg class="quick-post-footer-action-icon icon-camera">
                  <use xlink:href="#svg-camera"></use>
                </svg>
                <!-- /QUICK POST FOOTER ACTION ICON -->
              <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -32px; left: 50%; margin-left: -42.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;">
              <p class="xm-tooltip-text">Insert Photo</p>
              </div>
              </div>
              <!-- /QUICK POST FOOTER ACTION -->

              <!-- QUICK POST FOOTER ACTION -->
              <div id="Open_link" class="quick-post-footer-action text-tooltip-tft-medium" data-title="Insert Link" style="position: relative; color: #adafca;">
                <!-- QUICK POST FOOTER ACTION ICON -->
                <i class="fa fa-link" ></i>
                <!-- /QUICK POST FOOTER ACTION ICON -->
              <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -32px; left: 50%; margin-left: -35.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;">
              <p class="xm-tooltip-text">Insert Link</p>
              </div>
              </div>
              <!-- /QUICK POST FOOTER ACTION -->

            </div>
            <!-- /QUICK POST FOOTER ACTIONS -->

              <!-- QUICK POST FOOTER ACTIONS -->
              <div class="quick-post-footer-actions">
                <!-- BUTTON -->
                <p id="Open_post" class="button small void">&nbsp;<i class="fa fa-text-width" aria-hidden="true"></i>&nbsp;</p>
                <!-- /BUTTON -->

                <!-- BUTTON -->
                <p class="button small secondary" id="btnpost" >Post</p>
                <!-- /BUTTON -->
              </div>
              <!-- /QUICK POST FOOTER ACTIONS -->
            </div>
          <!-- /QUICK POST FOOTER -->
</div>

<script>$('#OpenImgUpload').click(function(){ $('#imgupload').trigger('click'); });</script>
<script>$('#Open_link').click(function(){
  $(".add_link").html("<div class='input-group'><span class='input-group-text'><i class='fa fa-edit' ></i></span><input type='txt' class='form-control' name='name' id='name'  placeholder='Name' autocomplete='off' required /></div><div class='input-group'><span class='input-group-text'><i class='fa fa-link' ></i></span><input type='url' class='form-control' name='url' id='url'  placeholder='http(s)://' autocomplete='off' required /></div><div class='input-group'><span class='input-group-text'><i class='fa fa-tag' ></i></span><select class='form-control' name='categ' id='categ' ><?php $selectdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 ORDER BY `name` ASC "); $selectdir->execute(); while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){ echo "<option value='{$selrs15['id']}'>{$selrs15['name']}</option>"; } ?></select></div><div class='input-group'><span class='input-group-text'><i class='fa fa-folder' ></i></span><input type='txt' class='form-control' name='tag' id='tag'  placeholder='tags' autocomplete='off' required /></div>");
  $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='1' />");
  $('.result').html("");
});</script>
<script>$('#Open_post').click(function(){
  $(".add_link").html("<div class='input-group'></div>");
  $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='100' />");
  $('.result').html("");
});</script>
<script>
    $(document).ready(function(){
        $('#imgupload').change(function(){
          $(".ed_type").html("<input type='hidden' name='s_type' id='s_type' value='4' />");
          $('.add_link').html("");
         });
     });
</script>
<script>$('#btnpost').click(function(){ $('.form').trigger('submit'); });</script>
<script>
    $(document).ready(function(){
        $('#imgupload').change(function(e){
          $(".result").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fimg', file);
            $.ajax({
                url : "<?php url_site();  ?>/requests/up_image.php",
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
    });
</script>
<?php }
 }else{ echo"404"; }  ?>