<?php if($s_st=="buyfgeufb"){  ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
		  <!--buttons-->
		  <div class="widget-box">
			 <center><h2 class="hdg">Updates MYads</h2></center>
          </div>
          <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                     <center><?php echo $_GET['bnerrMSG'];  ?></center>
                     </div>
                        <?php }  ?>
                <div class="panel panel-widget">
                   <?php
                   $myads_last_time_updates = "https://apikariya.gq/myads/latest_version.txt";
                   $last_time_updates = @file_get_contents($myads_last_time_updates);
                    if($last_time_updates==$versionRow['o_valuer']){
                     echo "<center><div class=\"alert alert-success alert-dismissible\" role=\"alert\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                        <span aria-hidden=\"true\">&times;</span></button><h2>"
                        .$lang['latest_version']
                        ."&nbsp;<a href=\"{$url_site}/admincp?updates\" ><i class=\"fa fa-refresh\"></i></a>
                        <br /><a><b>MyAds v{$last_time_updates}</b></a></h2></div></center>";
                   }else{      
                   $versionnow = $versionRow['name'];
                  echo "<center><div class=\"alert alert-info alert-dismissible\" role=\"alert\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                        <span aria-hidden=\"true\">&times;</span></button>"
                        .$lang['there_update']
                        ."&nbsp;<a href=\"{$url_site}/admincp?updates\" ><i class=\"fa fa-refresh\"></i></a>
                        </div></center>";
                  echo "<center><div class=\"alert alert-warning alert-dismissible\" role=\"alert\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                        <span aria-hidden=\"true\">&times;</span></button>"
                        .$lang['import_update']
                        ."</div></center>";
                  echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp.php?e_update\">
                        <input type=\"hidden\" name=\"versionnow\" value=\"{$versionnow}\" />
                        <center><button type=\"submit\" name=\"up_submit\" value=\"up_submit\" class=\"btn btn-primary\">
                        {$lang['now_update']}&nbsp;<i class=\"fa fa-download \"></i></button></center>
                        </form>";
                   }
                    ?>
				</div>
		  </div>
</div>
</div>
<?php }else{ echo"404"; }  ?>