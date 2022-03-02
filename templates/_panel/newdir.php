<?php if($s_st=="buyfgeufb"){  ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="main-page">
			  <div class="modal-content modal-info">
						<div class="modal-header"> 
                        <h2><?php lang('addwebsitdir');  ?></h2>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                      <form  method="POST" action="<?php url_site();  ?>/requests/status.php" >
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" value="<?php if(isset($_GET["title"])) { echo $_GET["title"]; }  ?>"  placeholder="Web site name" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-link" aria-hidden="true"></i></span>
                       <input type="url" class="form-control" name="url" value="<?php if(isset($_GET["url"])) { echo $_GET["url"]; }  ?>" placeholder="http://" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="editor1" class="form-control"  rows="15" placeholder="Site Description" required></textarea>
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
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-tag" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="tag" value="<?php  if(isset($_GET["tags"])) { echo $_GET["tags"]; }  ?>"  placeholder="Keywords: Place a comma (,) between words" aria-describedby="basic-addon1">
                       </div>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-folder" aria-hidden="true"></i></span>
                       <select class="form-control" name="categ" >
                      <?php $selectdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 ORDER BY `name` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                             echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                             } ?>
                       </select>
                       </div>
                       <hr />
                        <div class="input-group col-md-2">
                        <span class="input-group-addon" id="basic-addon1"><?php captcha() ;  ?>&nbsp;=&nbsp;</span>
                       <input type="text"  class="form-control" name="capt" required  />
                       </div>
                           <input type="hidden" name="s_type" value="1" />
                           <input type="hidden" name="set" value="Publish" />
                           <center>
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                           <?php lang('spread');  ?></button>
                           <a href="https://www.adstn.gq/kb/myads:Add a new Web site" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                           </center>
                       </form>
							</div>
						</div>
					</div>
			</div>
		</div>
<?php }else{ echo"404"; }  ?>