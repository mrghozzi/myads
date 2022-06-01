<?php if(isset($s_st)=="buyfgeufb"){

 ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
    <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/marketplace-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><span><i class="fa fa-cart-plus" aria-hidden="true"></i> </span>&nbsp;<?php lang('add_product');  ?></p>
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
           <form id="addstore" method="post" class="form-horizontal" action="requests/add_product.php" >
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small">
                      <label for="titer"><?php lang('titer');  ?></label>
                      <input type="text" class="form-control sname" name="name" value="<?php if_gstore('sname');  ?>" minlength="3" maxlength="35" pattern="^[-a-zA-Z0-9_]+$" required >
                      <?php if(isset($_SESSION['sname'])){  $sname="1"; } ?>
                       <div id="msg_name" >
                        <?php if(isset($sname) AND ($sname=="1")){ }else{    ?>
                         <input type="txt" style="visibility:hidden" value="" name="vname"  required>
                        <?php } ?>
                       </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="desc"><?php lang('desc');  ?></label>
                      <input type="text" class="form-control" name="desc" value="<?php if_gstore('sdesc');  ?>" minlength="10" maxlength="2400" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="Version_nbr"><?php lang('Version_nbr');  ?></label>
                      <input type="text" id="profile-name" name="vnbr" value="<?php if_gstore('svnbr');  ?>" placeholder="<?php lang('version');  ?> | EX: v1.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="cat"><?php lang('cat');  ?></label>
                      <div id="storecat" >
                      <?php
                 $o_type = "storecat";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                  if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                    echo "<select class=\"form-control cat_s\" id=\"cat_s\" name=\"cat_s\" required>
                    <option value=\"\">-- Select a categorie --</option>";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($storecat=$stormt->fetch(PDO::FETCH_ASSOC) ) {
                  $catname = $storecat['name'];
                  $catval  = $storecat['name'];
                  $catname = $lang["{$catname}"];
                    echo "<option value=\"{$catval}\">{$catname}</option>";

                 }  echo " </select> ";
                  }
                  ?>

<script>
    $(document).ready(function(){
        $('#cat_s').change(function(){
          $("#storecat").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
   var cat_s=$(this).val();
  var dataString = 'cat_s='+ cat_s;

  $.ajax
  ({
   type: "POST",
   url: "<?php url_site();  ?>/requests/store_cat.php",
   data: dataString,
   cache: false,
   success: function(html)
   {
      $("#storecat").html(html);
   }
   });

        });
    });
</script>
                    </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                      <label for="profile-name"><?php lang('topic');  ?></label>
                      <textarea name="txt" id="editor1"  rows="15" required><?php if_gstore('stxt');  ?></textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name" style=" background-color: #8e44ad; color: #fff; "><?php lang('file');  ?></label>
                      <input type="file" class="form-control" style=" font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff; " accept=".zip" name="fzip" id="media" >
                      <br />
                       <div class="result">
                        <?php if(isset($_SESSION['slinkzip'])){  echo "<img src=\"{$url_site}/templates/_panel/img/zip.png\"  />&nbsp; ";
                                echo $_SESSION['slinkzip'];
                                echo "<br />" ; }  ?>
                        <input type="txt" style="visibility:hidden" value="<?php if_gstore('slinkzip');  ?>" name="linkzip" id="text" required>
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
              <p class="upload-box-title"><?php lang('upload');  ?></p>
              <!-- /UPLOAD BOX TITLE -->

              <!-- UPLOAD BOX TEXT -->
              <p class="upload-box-text"><?php lang('img');  ?></p>
              <!-- /UPLOAD BOX TEXT -->
            </div>
            <center><br /><div  id="showImgUpload" ><input type="txt" name="img" style="display:none" required></div></center>
            <input type="file" id="imgupload" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <hr />
             <div class="form-item split">
               <!-- FORM SELECT -->
               <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
               <a href="https://www.adstn.gq/kb/myads:store:update" class="button default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
               <?php } ?>
               <!-- BUTTON -->
               <button type="submit" name="submit" id="button" value="Publish" class="button primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; <?php lang('save');  ?></button>
             </div>
           </form>
          </div>
          <div class="widget-box-status-content" >
           <?php if(isset($_SESSION['snotvalid'])){ echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp; ";
            if_gstore('snotvalid');
           echo "</div>" ; }  ?>
           <hr />
          </div>
		 </div>
	 </div>
</div>
<script>$('#OpenImgUpload').click(function(){ $('#imgupload').trigger('click'); });</script>
<script>
    $(document).ready(function(){
        $('#imgupload').change(function(e){
          $("#showImgUpload").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
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
                    $('#showImgUpload').html(response)
                }
            });
        });
    });
</script>
 <script>
    $(document).ready(function(){
        $('.sname').change(function(){
          $("#msg_name").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'><?php lang('review');  ?></div> </div> ");
   var sname=$(this).val();
  var dataString = 'sname='+ sname;

  $.ajax
  ({
   type: "POST",
   url: "<?php url_site();  ?>/requests/verfiy_storname.php",
   data: dataString,
   cache: false,
   success: function(html)
   {
      $("#msg_name").html(html);
   }
   });

        });
    });
     </script>

            <script>

    $(document).ready(function(){
        $('#media').change(function(e){
          $(".result").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fzip', file);
            $.ajax({
                url : "<?php url_site();  ?>/requests/filezip.php",
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

<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/<?php lang('lg'); ?>.js"></script>

                       <script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('editor1');
sceditor.create(textarea, {
	format: 'xhtml',
    locale : 'ar',
<?php
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
$c = 1;
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
  if($c == 1){
  ?> emoticons: {
  dropdown: {
    <?php
  }else if($c == 11){
    ?>
    },
  more: {
    <?php
    }
   ?>
   '<?php echo $smlssen['name'];  ?>': '<?php echo $smlssen['img'];  ?>',
   <?php


$c++; }

if($c >= 2){
  echo "}
  },";
}
 ?>
style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
<?php
 }else{ echo "404"; }  ?>