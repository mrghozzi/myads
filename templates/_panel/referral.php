<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Referral</h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
					<table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Username</th>
                              <th>Start Date</th>
							  <th>PTS</th>
                            </tr>
						</thead>
						<tbody>
              <?php include "include/referral.php";  ?>
               </tbody>
					</table>
				</div>
				</div><div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?> 