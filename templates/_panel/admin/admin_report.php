<?php if($s_st=="buyfgeufb"){  ?>
<style>
select {
  font-family: 'FontAwesome', 'sans-serif';
}</style>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Report&nbsp;<span>!</span></h2>

            <div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center><b>#ID</b></center></th>
                              <th><center><b>Username</b></center></th>
                              <th><center><b>Messages</b></center></th>
                              <th><center><b>Report</b></center></th>
                           </tr>
						</thead>
						<tbody>
                  <?php report_list();  ?>
                        </tbody>
					</table>
				</div>

				</div>

				</div>
				</div>
<?php }else{ echo"404"; }  ?>