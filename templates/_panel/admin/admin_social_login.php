<?php if($s_st=="buyfgeufb"){  ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
			<div class="widget-box">
			   <h2 class="hdg"><?php lang('social_login'); ?></h2>
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
					<table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Name</th>
                              <th>App ID</th>
                              <th>App secret</th>
                           </tr>
						</thead>
						<tbody>
   
                        <?php lnk_list();  ?>
               </tbody>
					</table>
				</div>
				</div>
                </div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>