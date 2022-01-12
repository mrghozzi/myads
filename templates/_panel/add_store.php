<?php if(isset($s_st)=="buyfgeufb"){

 ?>
     <link rel="stylesheet" href="<?php url_site();  ?>/templates/_panel/editor/minified/themes/default.min.css" />
<script src="<?php url_site();  ?>/templates/_panel/editor/minified/sceditor.min.js"></script>
   	<div id="page-wrapper">
          <div class="main-page">
     <div class="col-md-12 validation-grid">
                        <h4><span><i class="fa fa-plus" aria-hidden="true"></i> </span>&nbsp;<?php lang('add_product');  ?></h4>
						<div class="validation-grid1">

                       <section>
                         <div class="valid-top2">
								 <form id="addstore" method="post" class="form-horizontal" action="requests/add_product.php">

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('titer');  ?></label>
                            <div class="col-lg-5">

          <?php if(isset($_SESSION['sname'])){  $sname="1"; } ?>
                                <input type="text" class="form-control sname" name="name" value="<?php if_gstore('sname');  ?>" minlength="3" maxlength="35" pattern="^[-a-zA-Z0-9_]+$"  autocomplete="off" required />
                               <div id="msg_name" >
          <?php if(isset($sname) AND ($sname=="1")){ }else{    ?>
                                <input type="txt" style="visibility:hidden" value="" name="vname"  required>
                 <?php } ?>
                                </div>
                             </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('desc');  ?> </label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="desc" value="<?php if_gstore('sdesc');  ?>" minlength="10" maxlength="240" autocomplete="off" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('Version_nbr');  ?> </label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="vnbr" value="<?php if_gstore('svnbr');  ?>" placeholder="<?php lang('version');  ?> | EX: v1.0" minlength="2" maxlength="10" pattern="^[-a-zA-Z0-9.]+$" autocomplete="off" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('cat');  ?> </label>
                            <div class="col-lg-5" id="storecat">
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
                  ?>    <script>
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

                        <div class="input-group">
                        <label class="col-lg-3 control-label"><?php lang('topic');  ?> </label>
                           <div class="col-lg-10">
                       <textarea name="txt" id="editor1" class="form-control"  minlength="10" rows="15" placeholder="<?php lang('topic');  ?>" required><?php if_gstore('stxt');  ?></textarea>

                              </div>
                            </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('file');  ?> </label>
                            <div class="col-lg-5">
                                <input type="file" class="form-control" style=" font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff; " name="fzip" id="media" autocomplete="off"  />
                                <br /><div class="result">
                                <?php if(isset($_SESSION['slinkzip'])){  echo "<img src=\"{$url_site}/templates/_panel/images/zip.png\"  />&nbsp; ";
                                echo $_SESSION['slinkzip'];
                                echo "<br />" ; }  ?>
                                <input type="txt" style="visibility:hidden" value="<?php if_gstore('slinkzip');  ?>" name="linkzip" id="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('img');  ?> </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="img"  value="<?php if_gstore('simg');  ?>" placeholder="http(s)://" autocomplete="off" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <center><button type="submit" name="submit" id="button" value="Publish" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; <?php lang('save');  ?></button></center>
                            </div>
                        </div>
     <?php if(isset($_SESSION['snotvalid'])){ echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp; ";
     if_gstore('snotvalid');
     echo "</div>" ; }  ?>

                    </form>
                </div>
            </section>

							</div>
				</div>
   				</div>
               <div class="clearfix"> </div>
            </div>

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

              <script src="<?php url_site();  ?>/templates/_panel/editor/minified/formats/xhtml.min.js"></script>
                       <script src="<?php url_site();  ?>/templates/_panel/editor/minified/jquery.sceditor.min.js"></script>
                       <script src="<?php url_site();  ?>/templates/_panel/editor/languages/<?php lang('lg'); ?>.js"></script>

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
style: '<?php url_site();  ?>/templates/_panel/editor/minified/themes/content/default.min.css'
});
</script>
<?php
 }else{ echo "404"; }  ?>