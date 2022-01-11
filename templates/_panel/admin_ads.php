<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Ads Site</h2>

			<div class="clearfix"></div>
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
                <div class="clearfix"></div>
				</div>
				</div>
                <div class="clearfix"></div>
				</div>
<?php }else{ echo"404"; }  ?>