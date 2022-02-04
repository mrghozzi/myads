<?php if($s_st=="buyfgeufb"){
$myads_last_time_updates = "https://www.adstn.gq/latest_version.txt";
                   $last_time_updates = @file_get_contents($myads_last_time_updates);
                    if($last_time_updates==$versionRow['o_valuer']){
                     $last_time_updates = $last_time_updates."&nbsp;<a href=\"{$url_site}/admincp?updates\" ><i class=\"fa fa-refresh\"></i></a>";
                   }else{
                     $last_time_updates = $last_time_updates."&nbsp;<a href=\"{$url_site}/admincp?updates\" class=\"btn btn-info\"><i class=\"fa fa-download\"></i></a>";
                   }  

?>
		<div id="page-wrapper">
        <?php dinstall_d();  ?>
			<div class="main-page">
				<div class="four-grids">
					<div class="col-md-3 four-grid">
						<div class="four-grid1">
							<div class="icon">
								<i class="glyphicon glyphicon-bullhorn" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('bannads'); ?></h3>
								<h4><?php lang('Total'); ?> : <?php nbr_state('banner'); ?></h4>
                                <h4><?php lang('Views'); ?> : <?php admin_state('banner'); ?></h4>
                                <h4><?php lang('Click'); ?> : <?php admin_state('vu'); ?></h4>

							</div>
                       </div>
					</div>
					<div class="col-md-3 four-grid">
						<div class="four-grid2">
							<div class="icon">
								<i class="glyphicon glyphicon-list" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('textads'); ?></h3>
								<h4><?php lang('Total'); ?> : <?php nbr_state('link'); ?></h4>
                                <h4><?php lang('Views'); ?> : <?php admin_state('link'); ?></h4>
                                <h4><?php lang('Click'); ?> : <?php admin_state('clik'); ?></h4>

							</div>
					   </div>
					</div>
					<div class="col-md-3 four-grid">
						<div class="four-grid3">
							<div class="icon">
								<i class="glyphicon glyphicon-transfer" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>State</h3>
								<h3><?php lang('exvisit'); ?> : <?php nbr_state('visits');  ?></h3>


							</div>
					   </div>
                    </div>
					<div class="col-md-3 four-grid">
						<div class="four-grid4">
							<div class="icon">
								<i class="glyphicon glyphicon-user" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('t_users'); ?></h3>
								<h4><?php nbr_state('users'); ?></h4>
                                <h4>Online <?php online_admin(); ?></h4>
                                <h3>Directory Link : <?php nbr_state('directory');  ?></h3>
                                <h3>Forum : <?php nbr_state('forum');  ?></h3>

							</div>
						</div>
					</div>


                    <div class="clearfix"> </div>
			</div>
            <div class="four-grids">
             <div class="col-md-12 photoday-grid">
                 <div class="panel panel-widget">
				<div class="bs-docs-example">
					<table class="table table-striped">
						<thead>
							<tr>
								<th><center>Devlope by</center></th>
								<th><center>Program name</center></th>
								<th><center><?php lang('version');  ?></center></th>
								<th><center>Latest version</center></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><center><a href="http://www.krhost.ga">Kariya host</a></center></td>
								<td><center>MYads</center></td>
								<td><center>v<?php myads_version();  ?></center></td>
								<td><center><?php echo $last_time_updates; ?></center></td>
							</tr>
						</tbody>
					</table>
					</div>
 <a href="<?php url_site();  ?>/admincp?report" class="btn btn-primary" >Report
  <span class="badge"><?php $catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM report WHERE statu=1" );
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);
echo $abcat['nbr']; ?></span></a>
                    <?php if(isset($_GET['sitemap']))
{  ?>
<a href="<?php echo $url_site;  ?>/sitemap" class="btn btn-info" ><b>Sitemap</b></a>
<a href="<?php echo $url_site;  ?>/sitemap.xml" class="btn btn-warning" target="_blank">/sitemap.xml&nbsp;<b><i class="fa fa-external-link" ></i></b></a>
<?php }else{  ?>
<a href="<?php echo $url_site;  ?>/sitemap" class="btn btn-info" ><b>Sitemap</b></a>
<?php } ?>
					</div>
                    <script language="javascript" src="http://apikariya.gq/news/myads.php?v=<?php myads_fversion();  ?>"></script>
                    </div>
      <div class="clearfix"> </div>
			</div>
                  <div class="photoday-section">

						<div class="col-md-12 photoday-grid">
						  <div class="progress-bottom">
									<div class="cal-left">
										<div class="cal">
											<i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
										</div>
										<div class="cal-text">
											<h4><?php echo date("D M j"); ?></h4>
										</div>
									</div>
									<div class="time-right">
									<div class="cal">
									<i class="glyphicon glyphicon-time" aria-hidden="true"></i>
									</div>
									<div class="cal-text">
									<h4><?php echo date("g:i a"); ?></h4>
									</div>
									</div>
								</div>
							</div>
                           <div class="clearfix"></div>
					</div>


            <div class="clearfix"> </div>
			</div> </div>
<?php }else{ echo"404"; }  ?>