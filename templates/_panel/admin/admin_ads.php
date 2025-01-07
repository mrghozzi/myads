<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){   ?>
<link href="<?php echo $url_site;  ?>/templates/_panel/css/codemirror.css" rel='stylesheet' type='text/css' />
<div class="grid grid-3-9 medium-space" >
 <div class="grid-column" >
  <?php template_mine('admin/admin_nav');  ?>
 </div>
 <div class="grid-column" >
	<!--buttons-->
	<div class="widget-box">

			<!--buttons-->
			<div class="grids-section">
			   <h2 class="hdg">Ads Site</h2>
            </div>
            <div id="widget_block">
                <?php if(isset($_GET['bnerrMSG'])){  ?>
               <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                 <?php }  ?>
                 <div class="form-input">
				  <?php lnk_list();  ?>
                 </div>
            </div>
	   </div>

 </div>
</div>

<?php }else{ echo"404"; }  ?>