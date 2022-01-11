<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
       <?php
        dinstall_d();
       if(isset($_GET['errMSG'])){  ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong><?php lang('warning'); ?></strong> <?php echo $_GET['errMSG'];  ?>
</div>
 <?php }  ?>
        <?php if(isset($_GET['MSG'])){  ?>
        <div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <?php echo $_GET['MSG'];  ?>
</div>
 <?php }  ?>
			<div class="main-page">
				<div class="four-grids">

					<div class="col-md-3 four-grid">
						<div class="four-grid1">
							<div class="icon">
								<i class="glyphicon glyphicon-bullhorn" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('bannads'); ?></h3>
								<h4><?php vu_state_row('banner','vu'); ?>&nbsp;<?php lang('Views'); ?></h4>
                            </div>
							<a href="#" data-toggle="modal" data-target="#Views"><?php lang('MoreInfo'); ?></a>
						</div>
					</div>
					<div class="col-md-3 four-grid">
						<div class="four-grid2">
							<div class="icon">
								<i class="glyphicon glyphicon-list" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('textads'); ?></h3>
								<h4><?php vu_state_row('link','clik'); ?>&nbsp;<?php lang('Click'); ?></h4>
							</div>
							<a href="#" data-toggle="modal" data-target="#link"><?php lang('MoreInfo'); ?></a>
						</div>
					</div>
					<div class="col-md-3 four-grid">
						<div class="four-grid3">
							<div class="icon">
								<i class="glyphicon glyphicon-transfer" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('exvisit'); ?></h3>
								<h4><?php vu_state_row('visits','vu'); ?>&nbsp;<?php lang('visits'); ?></h4>
							</div>
							<a href="#" data-toggle="modal" data-target="#Exchange"><?php lang('MoreInfo'); ?></a>
						</div>
					</div>
					<div class="col-md-3 four-grid">
						<div class="four-grid4">
							<div class="icon">
								<i class="glyphicon glyphicon-gift" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3><?php lang('pts'); ?></h3>
								<h4><?php user_row('pts'); ?></h4>
							</div>
							<a href="#" data-toggle="modal" data-target="#pts"><?php lang('MoreInfo'); ?></a>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
              <!-- //modal 1 -->
              <div class="modal fade" id="pts" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
									<h3><?php lang('Points'); ?> </h3>
									<p><?php lang('Totalpoints'); ?> <?php user_row('pts'); ?> PTS.<br />
                                   <?php if(isset($errMSG)){ echo $errMSG; }   ?>
                                           <h3><?php lang('Convertpoint'); ?></h3>
                                      <form action="home.php" method="POST">

                                 <table>
  <tr>
    <td><label class="col-lg-3 control-label"><?php lang('Points'); ?></label><input type="text" class="form-control" name="pts"  autocomplete="off" required="true" /> </td>
    <td><label class="col-lg-3 control-label"><?php lang('to'); ?></label><select class="form-control" name="to" >
                                    <option value="link" ><?php lang('tostads'); ?></option>
                                    <option value="banners"  ><?php lang('towthbaner'); ?></option>
                                    <option value="exchv" ><?php lang('toexchvisi'); ?></option>
                                </select></td>
    <td><button type="submit" class="btn btn-info" name="bt_pts" value="bt_pts" ><?php lang('Conversion'); ?></button>
      </td>
  </tr>

</table>
</form>
                                </p>  <a href="https://www.adstn.gq/kb/myads:pts" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close'); ?></button>

							</div>
						</div>
					</div>
				</div>
			</div>
            <!-- //modal 2 -->
              <div class="modal fade" id="Exchange" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
									<h3><?php lang('exvisit'); ?></h3>
									<p><?php echo $lang['you_have']."&nbsp;"; user_row('vu'); echo "&nbsp;".$lang['ptvysa']; ?><br />
                                    <?php echo $lang['yshbv']."&nbsp;:&nbsp;"; vu_state_row('visits','vu');  ?></p>
                                      <center><a onclick="ourl('visits.php?id=<?php user_row('id') ; ?>');" href="javascript:void(0);" class="btn btn-success" ><i class="fa fa-exchange nav_icon"></i><h4><b><?php lang('exvisit'); ?></b></h4></a></center>
                                      <a href="https://www.adstn.gq/kb/myads:Exchange" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close'); ?></button>

							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- //modal 2 -->
             <!-- //modal 3 -->
              <div class="modal fade" id="Views" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                                    <h3><?php echo $lang['bannads']; ?></h3>
									<p><?php echo $lang['you_have']."&nbsp;"; user_row('nvu'); echo "&nbsp;".$lang['ptvyba']; ?><br />
                                    <?php echo $lang['your']."&nbsp;"; vu_state_row('banner','vu'); echo "&nbsp;".$lang['bahbpb']; ?><br />
                                    <?php echo $lang['And']."&nbsp;"; vu_state_row('banner','clik'); echo "&nbsp;".$lang['Clik_ads']; ?></p>
                                      <center>
                                      <a  href="b_list.php" class="btn btn-success" ><?php lang('list'); echo"&nbsp;"; lang('bannads'); ?></a></center>
                                      <a href="https://www.adstn.gq/kb/myads:Banners Ads" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
									  <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close'); ?></button>
                                      <a class="btn btn-info" href="state.php?ty=banner&st=vu" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
                             </div>
						</div>
					</div>
				</div>
              </div>

			<!-- //modal 3 -->
            <!-- //modal 3 -->
              <div class="modal fade" id="link" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
									<h3><?php lang('textads'); ?></h3>
									<p><?php echo $lang['you_have']."&nbsp;"; user_row('nlink'); echo "&nbsp;".$lang['ptcyta']; ?><br />
                                    <?php echo $lang['your']."&nbsp;"; vu_state_row('link','clik'); echo "&nbsp;".$lang['Clik_ads']; ?></p>
                                      <center><a  href="l_list.php" class="btn btn-success" ><?php lang('list'); echo"&nbsp;"; lang('textads'); ?></a></center>
                                      <a href="https://www.adstn.gq/kb/myads:Text Ads" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
									  <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close'); ?></button>
                                      <a class="btn btn-info" href="state.php?ty=link&st=vu" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- //modal 3 -->
            <div class="weathers-grids">

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

                    	<div class="clearfix"> </div>
        	</div>
					<div class="photoday-section">

						<div class="col-md-4 photoday-grid">
							<div class="message-top">
								<div class="message-left">
								<h3>News <?php title_site(''); ?></h3>
								</div>
								<div class="message-right">
								<i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
								</div>
								<div class="clearfix"></div>
								</div>
								<?php news_site(); ?>
							</div>
							<div class="col-md-4 photoday-grid">
                                 <?php ads_site(2); ?>

							</div>


							<div class="clearfix"></div>
					</div>

            	<div class="clearfix"> </div>
            </div>

			</div>
<?php }else{ echo"404"; }  ?>