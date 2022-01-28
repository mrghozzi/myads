<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<center><h2 class="hdg">Updates MYads</h2></center>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
                   <?php
                   $myads_last_time_updates = 'https://www.adstn.gq/latest_version.txt';
                   $last_time_updates = @file_get_contents($myads_last_time_updates);
                    if($last_time_updates==$versionRow['o_valuer']){
                     echo $lang['latest_version'];
                   }else{
                     echo 2;
                   }
                    ?>
				</div>
				</div><div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>