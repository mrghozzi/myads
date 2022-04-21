<?php if($s_st=="buyfgeufb"){  ?>
<link href="<?php echo $url_site;  ?>/templates/_panel/css/codemirror.css" rel='stylesheet' type='text/css' />
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
	<!--buttons-->
	<div class="widget-box">
       <div class="col-md-12  validation-grid">
			<!--buttons-->
			<div class="grids-section">
			   <h2 class="hdg">Ads Site</h2>
            </div>
            <div class="col-md-12 table-grid">
                <?php if(isset($_GET['bnerrMSG'])){  ?>
               <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                 <?php }  ?>
               <div class="panel panel-widget">
				  <table class="table table-hover">
					 <tbody>
                      <?php lnk_list();  ?>
                     </tbody>
				  </table>
			   </div>
            </div>
	   </div>
	</div>
</div>
</div>

<?php }else{ echo"404"; }  ?>