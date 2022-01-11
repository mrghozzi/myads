<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Notification</h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                 <table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th></th>
							  <th>Time</th>
                            </tr>
						</thead>
						<tbody>
                        <?php ntf_list();  ?>
               </tbody>
					</table>
                </div>
				</div> <div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>