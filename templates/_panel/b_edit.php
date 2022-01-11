<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">

					<div class="col-md-12  validation-grid">
						<h4><span>Banners </span> Ads</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <?php if(isset($_COOKIE['user']) AND !isset($_COOKIE['admin']) ){ ?>
								 <form id="defaultForm" method="post" class="form-horizontal" action="b_edit.php?id=<?php echo $_GET['id'];  ?>">
                            <?php }
                            else if(isset($_COOKIE['user'])=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){
                                     ?>
                              <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?b_edit=<?php echo $_GET['b_edit'];  ?>">
                            <?php } ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php bnr_echo('name'); ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php bnr_echo('url'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Image Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="img" value="<?php bnr_echo('img'); ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Banner size</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="bn_px" >
                                    <option value="468" <?php if( $slctRow=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                                    <option value="728" <?php if( $slctRow=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                                    <option value="300" <?php if( $slctRow=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                                    <option value="160" <?php if( $slctRow=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>

                                </select>
                            </div>
                        </div>
                         <?php if(isset($_COOKIE['user'])=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){   ?>
                         <div class="form-group">
                            <label class="col-lg-3 control-label">Statu </label>
                            <div class="col-lg-1">
                                <input type="text" class="form-control" name="statu" value="<?php bnr_echo('statu'); ?>" autocomplete="off" />

                            </div>  <p> 1 = ON | 2 = OFF</p>
                        </div>
                        <?php } ?>
                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>       <br /><center><div><img src="<?php bnr_echo('img'); ?>" alt="<?php bnr_echo('name'); ?>" /> </div>    </center>


							</div>
				</div>
					<div class="clearfix"></div>
				</div>
			</div>
<?php }else{ echo"404"; }  ?>