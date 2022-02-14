<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
                     <center>
                     <a href="<?php echo $url_site; ?>/admincp?users" class="btn btn-primary" ><i class="fa fa-users"></i></a>
                     <a href="<?php echo $url_site; ?>/u/<?php echo $_GET['us_edit']; ?>" class="btn btn-primary" ><i class="fa fa-user"></i></a>
                     <a href="<?php echo $url_site; ?>/admincp?state&ty=banner&st=<?php echo $_GET['us_edit']; ?>" class="btn btn-warning" ><i class="fa fa-link"></i></a>
                     <a href="<?php echo $url_site; ?>/admincp?state&ty=link&st=<?php echo $_GET['us_edit']; ?>" class="btn btn-success" ><i class="fa fa-eye "></i></a>
                     </center>
                     <br />
					<div class="col-md-12  validation-grid">
						<h4><span>Edit </span> User</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
                                 <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?us_edit=<?php echo $_GET['us_edit'];  ?>">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Username</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php eus_echo('username'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">User Slug</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="slug" value="<?php sus_echo('o_valuer'); ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email </label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="mail" value="<?php eus_echo('email'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">PTS </label>
                            <div class="col-lg-2">
                              <input type="text" class="form-control" name="pts" value="<?php eus_echo('pts'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-5 control-label">Exchange Visits PTS </label>
                            <div class="col-lg-2">
                              <input type="text" class="form-control" name="vu" value="<?php eus_echo('vu'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-5 control-label">Banners Ads PTS </label>
                            <div class="col-lg-2">
                              <input type="text" class="form-control" name="nvu" value="<?php eus_echo('nvu'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-5 control-label">Text Ads PTS </label>
                            <div class="col-lg-2">
                              <input type="text" class="form-control" name="nlink" value="<?php eus_echo('nlink'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-5 control-label">Membership authentication option <i class="fa fa-fw fa-check-circle" style="color: #0066CC;" ></i></label>
                            <div class="col-lg-2">
                              <select class="form-control" name="check" >
                                    <?php eus_selec() ?>
                                 </select>
                            </div>
                        </div>
                       <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <center><button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary"><?php lang('edit'); ?></button></center>
                            </div>
                        </div>
                    </form>
                </div>
            </section>


							</div>
				</div>
					<div class="clearfix"></div>
				</div>
			</div>
<?php }else{ echo"404"; }  ?>