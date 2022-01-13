<?php if($s_st=="buyfgeufb"){  ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="main-page">
			   <div class="modal-content modal-info">

                            <?php
                            $ifstorp = "0";
                            if(isset($_GET['e'])){
                           $bn_id = $_GET['e'];
                           $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND id=".$bn_id );
                           $catusz->execute();
                           $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
                           $catdid=$sucat['id'];
                           $catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4,7867) AND tp_id =".$catdid );
                           $catust->execute();
                           $susat=$catust->fetch(PDO::FETCH_ASSOC);
                           $ifstorp = $susat['s_type'];

                             ?>
                        	<div class="modal-header">
                        <?php lang('e_topic'); ?>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                       <form  method="POST" action="<?php url_site();  ?>/requests/edit_status.php" >
                       <input type="hidden" name="tid" value="<?php if(isset($sucat['id'])){ echo $sucat['id']; }  ?>" />
                       <?php }else{  ?>
                       	<div class="modal-header">
                        <?php lang('w_new_tpc'); ?>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                       <form  method="POST" action="<?php url_site();  ?>/requests/status.php" >
                       <?php }  ?>
                       <?php if(isset($ifstorp) AND ($ifstorp != 7867)){ ?>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" value="<?php if(isset($sucat['name'])){ echo $sucat['name']; }  ?>" placeholder="<?php lang('sbj'); ?>" aria-describedby="basic-addon1" required>
                        </div>
                        <?php    }else{  ?>
                        <input type="hidden" name="name" value="<?php if(isset($sucat['name'])){ echo $sucat['name']; }  ?>" />
                        <?php }  ?>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="editor1" class="form-control"  rows="15" placeholder="<?php lang('desc'); ?>" required><?php if(isset($sucat['txt'])){ echo $sucat['txt']; }  ?></textarea>
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
                       <?php if(isset($ifstorp) AND ($ifstorp != 7867)){ ?>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-folder" aria-hidden="true"></i></span>
                       <select class="form-control" name="categ" >
                      <?php $selectdir = $db_con->prepare("SELECT *  FROM f_cat ORDER BY `name` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){

                               if(isset($selrs15['id']) == isset($sucat['cat']) ){
                                 echo "<option value=\"{$selrs15['id']}\" selected >{$selrs15['name']}</option>";
                                 }else{
                                 echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                                 }

                             } ?>
                       </select>
                       </div>
                       <?php    }  ?>
                       <?php if(isset($ifstorp) AND ($ifstorp != 7867)){ ?>
                           <input type="hidden" name="s_type" value="2" />
                           <?php    }if(isset($ifstorp) AND ($ifstorp == 7867)){  ?>
                           <input type="hidden" name="s_type" value="7867" />
                           <?php    }  ?>
                           <input type="hidden" name="set" value="Publish" />
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                              Publish</button>
                           </form>
							</div>
                    	</div>
					</div>
			</div>
		</div>
<?php }else{ echo"404"; }  ?>