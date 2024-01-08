<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="widget-box">
              <div class="modal-content modal-info">
                    	<div class="modal-header">
                         <?php
                            $ifstorp = "0";
                            if(isset($_GET['e'])){
                           $bn_id = $_GET['e'];
                           $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND id=".$bn_id );
                           $catusz->execute();
                           $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
                           $catdid=$sucat['id'];
                           $catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,7867) AND tp_id =".$catdid );
                           $catust->execute();
                           $susat=$catust->fetch(PDO::FETCH_ASSOC);
                           $ifstorp = $susat['s_type'];

                             ?>
                        <h2><?php lang('e_topic');  ?></h2>
                        </div>
						<div class="modal-body">
                        	<div class="more-grids">

                 <form  method="POST" action="<?php url_site();  ?>/requests/edit_status.php?tid=<?php if(isset($sucat['id'])){ echo $sucat['id']; }  ?>" >
                 <?php }else{  ?>
                                         <h2><?php lang('w_new_tpc');  ?></h2>
                        </div>
						<div class="modal-body">
                        	<div class="more-grids">

                 <form  method="POST" action="<?php url_site();  ?>/requests/status.php" >
                 <?php }  ?>
                 <?php if(isset($ifstorp) AND ($ifstorp != 7867)){ ?>
                 <div class="form-row split" >
                  <div class="form-item">
                    <div class="form-input social-input small active">
                      <!-- name -->
                      <div class="social-link no-hover name">
                        <!-- ICON  -->
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        <!-- /ICON  -->
                      </div>
                      <!-- /name -->
                      <label for="name"><?php lang('sbj'); ?></label>
                      <input type="text" id="name" name="name" value="<?php if(isset($sucat['name'])){ echo $sucat['name']; }  ?>">
                    </div>
                 </div>
                 </div>
                 <?php    }else{  ?>
                  <input type="hidden" name="name" value="<?php if(isset($sucat['name'])){ echo $sucat['name']; }  ?>" />
                 <?php }  ?>
                 <div class="form-row">
                  <!-- FORM ITEM -->
                   <div class="form-item">
                     <!-- FORM INPUT -->
                      <div class="form-input">
                       <textarea id="editor1" name="txt" rows="16" ><?php if(isset($sucat['txt'])){ echo $sucat['txt']; }  ?></textarea>
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
                      </div>
                     <!-- /FORM INPUT -->
                   </div>
                  <!-- /FORM ITEM -->
                 </div>
<?php if(isset($ifstorp) AND ($ifstorp != 7867)){ ?>
                 <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label for="profile-status"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;Category</label>
                      <select id="profile-status" name="categ">
                      <?php $selectdir = $db_con->prepare("SELECT *  FROM f_cat ORDER BY `name` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){

                               if(isset($selrs15['id']) AND isset($sucat['cat']) AND ($selrs15['id']==$sucat['cat'])){
                                 echo "<option value=\"{$selrs15['id']}\" selected >{$selrs15['name']}</option>";
                                 }else{
                                 echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                                 }

                             } ?>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                    <!-- /FORM SELECT -->
                  </div>
                  <!-- /FORM ITEM -->
                </div>
<?php    }  ?>
                <hr />

               <div class="form-item split">
              <!-- FORM SELECT -->
              <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
              <a href="https://www.adstn.gq/kb/myads:Add a new Topic" class="button default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
              <?php } ?>
              <?php if(isset($ifstorp) AND ($ifstorp == 2)){ ?>
              <input type="hidden" name="s_type" value="2" />
              <?php    }else if(isset($ifstorp) AND ($ifstorp == 7867)){  ?>
              <input type="hidden" name="s_type" value="7867" />
              <?php    }else{  ?>
              <input type="hidden" name="s_type" value="2" />
              <?php    }  ?>
              <input type="hidden" name="set" value="Publish" />
              <!-- BUTTON -->
              <button type="submit" name="submit" value="Publish" class="button primary"><?php lang('spread');  ?></button>
              </div>
              <!-- /BUTTON -->

              </form>
                        	</div>
						</div>
					</div>
			</div>
		</div>
<?php }else{ echo"404"; }  ?>