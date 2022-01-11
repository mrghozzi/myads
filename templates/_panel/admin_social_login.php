<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('social_login'); ?></h2>

			<div class="clearfix"></div>
			</div>
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
				</div><div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>