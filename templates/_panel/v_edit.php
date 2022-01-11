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
                                  <?php if($_COOKIE['user'] AND !isset($_COOKIE['admin']) ){ ?>
								 <form id="defaultForm" method="post" class="form-horizontal" action="v_edit.php?id=<?php echo $_GET['id'];  ?>">
                            <?php }
                            else if($_COOKIE['user']=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){
                                     ?>
                              <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?v_edit=<?php echo $_GET['v_edit'];  ?>">
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
                            <label class="col-lg-3 control-label">Visits tims</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="exch" >
                                    <option value="1" <?php if($slctRow=="1") {echo "selected"; } ?> >10s/-1pts to Visit</option>
                                    <option value="2" <?php if($slctRow=="2") {echo "selected"; } ?> >20s/-2pts to Visit</option>
                                    <option value="3" <?php if($slctRow=="3") {echo "selected"; } ?> >30s/-5pts to Visit</option>
                                    <option value="4" <?php if($slctRow=="4") {echo "selected"; } ?> >60s/-10pts to Visit</option>

                                </select>
                            </div>
                        </div>
                          <?php if($_COOKIE['user']=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){   ?>
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
            </section>


							</div>
				</div>
					<div class="clearfix"></div>
				</div>
			</div>
<?php }else{ echo"404"; }  ?>