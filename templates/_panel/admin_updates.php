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
                   $myads_last_updates = 'https://www.adstn.gq/last_updates.txt';
                   $last_updates = @file_get_contents($myads_last_updates);
                   $file_get = @fopen($last_updates, 'r');
        $To ="upload/";
        $Tob =$_SERVER['DOCUMENT_ROOT']."/ads";
        @file_put_contents($To."Tmpfile.zip", $file_get);

		$zip = new ZipArchive;
		$file = $To.'Tmpfile.zip';
		//$path = pathinfo(realpath($file), PATHINFO_DIRNAME);
		if ($zip->open($file) === TRUE) {
		    $zip->extractTo($Tob);

		    $ziped = 1;
		} else {
		   $ziped = 0;
		}

                  echo $ziped;
                   }
                    ?>
				</div>
				</div><div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>