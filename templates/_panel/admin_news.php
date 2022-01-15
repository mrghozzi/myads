<?php if($s_st=="buyfgeufb"){  ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">News List</h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
 <?php  echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#ADD\" data-target=\".bs-example-modal-lg\" class='btn btn-info' >{$lang['new_topic']}&nbsp;<i class=\"fa fa-plus \"></i></a>
 <div class=\"modal fade\" id=\"ADD\" aria-labelledby=\"myLargeModalLabel\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog-lg\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
 <form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp.php?a_news\">
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\"><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></span>
  <input type=\"text\" class=\"form-control\" name=\"name\"  autocomplete=\"off\" />
  </div>
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\"><i class=\"fa fa-text-width\" aria-hidden=\"true\"></i></span>
  <textarea type=\"text\" id=\"editor1\" class=\"form-control\" name=\"txt\"  autocomplete=\"off\"></textarea></div>
  <div class=\"input-group\">
 <center><button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-info\">{$lang['add']}&nbsp;<i class=\"fa fa-plus \"></i></button></center>
 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button
 </div>
                                    </form>
                             </div>
						</div>
					</div>
				</div>
			</div>  </div>"; ?>
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center><b>#ID</b></center></th>
                              <th><center><b><?php echo $lang['date']; ?></b></center></th>
                              <th><center><b><?php echo $lang['topics']; ?></b></center></th>
                              <th></th>

                           </tr>
						</thead>
			   <tbody>
           <?php lnk_list();  ?>
               </tbody>
					</table>
<script type="text/javascript">
            $(document).ready(function() {
    $('#tablepagination').DataTable({
      "order": [[0, 'DESC']]
    });
} );
</script>                    
				</div>
				</div> <div class="clearfix"></div>
				</div>
				</div>
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
<?php }else{ echo"404"; }  ?>