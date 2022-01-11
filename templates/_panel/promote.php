<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
       <?php if(isset($_GET['p']) AND ($_GET['p'] =="banners")){  ?>

        	<div class="col-md-12 validation-grid">
                        <center><h2>Promote Your Site</h2></center>
                        <br />
						<h4><span>Banners </span> Ads</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="post" class="form-horizontal" action="promote.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php if(isset($_GET['bn_name'])){ echo $_GET['bn_name']; } ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php if(isset($_GET['bn_url'])){ echo $_GET['bn_url']; }  ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Image Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="img" value="<?php if(isset($_GET['bn_img'])){ echo $_GET['bn_img']; }  ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Banner size</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="bn_px" >
                                    <option value="468" <?php if(isset($_GET['bn_px'])=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                                    <option value="728" <?php if(isset($_GET['bn_px'])=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                                    <option value="300" <?php if(isset($_GET['bn_px'])=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                                    <option value="160" <?php if(isset($_GET['bn_px'])=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>

                                </select>
                            </div>
                        </div>

                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="bn_submit" value="bn_submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

							</div>
				</div>
         <?php }else if(isset($_GET['p']) AND ($_GET['p'] =="link")){  ?>
            <div class="validation-section">
					<h2>Promote Your Site</h2>
					<div class="col-md-12 validation-grid">
						<h4><span>Link</span> Click</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['errMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['errMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="POST" class="form-horizontal" action="promote.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; }  ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" autocomplete="off" />
                            </div>
                        </div>
                         <div class="form-group">

                            <input type="hidden" name="type" value="L" />
                            <label class="col-lg-3 control-label">Description</label>
                            <div class="col-lg-5">
                                <textarea name="desc" class="form-control" cols="50" ><?php if(isset($_GET['le_desc'])){ echo $_GET['le_desc']; }  ?></textarea>
                            </div>
                        </div>

                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="le_submit" value="le_submit" class="btn btn-primary"><?php lang("add"); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

							</div>
						</div>
					</div>

         <?php }else if(isset($_GET['p']) AND ($_GET['p']  =="exchange")){  ?>

         <div class="validation-section">
					<h2>Promote Your Site</h2>
					<div class="col-md-12 validation-grid">
						<h4><span>Link</span>Exchange Visits</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['errMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['errMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="POST" class="form-horizontal" action="promote.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; } ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" autocomplete="off" />
                            </div>
                        </div>
                        <input type="hidden" name="type" value="E"  />
                        <div class="form-group">

                            <label class="col-lg-3 control-label">Visits tims</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="exch" >
                                    <option value="1" <?php if(isset($_GET['le_exch'])=="1") {echo "selected"; } ?> >10s/-1pts to Visit</option>
                                    <option value="2" <?php if(isset($_GET['le_exch'])=="2") {echo "selected"; } ?> >20s/-2pts to Visit</option>
                                    <option value="3" <?php if(isset($_GET['le_exch'])=="3") {echo "selected"; } ?> >30s/-5pts to Visit</option>
                                    <option value="4" <?php if(isset($_GET['le_exch'])=="4") {echo "selected"; } ?> >60s/-10pts to Visit</option>

                                </select>
                            </div>
                        </div>


                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="le_submit" value="le_submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

							</div>
						</div>
					</div>


         <?php }else{   ?>
             <div class="validation-section">
					<h2>Promote Your Site</h2>
					<div class="col-md-6 validation-grid">
						<h4><span>Link</span> Click OR Exchange Visits</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['errMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['errMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="POST" class="form-horizontal" action="promote.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; }   ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" autocomplete="off" />
                            </div>
                        </div>
                          <div class="well">
                          <div class="form-group">
                          <p class="col-lg-3"><input type="radio" name="type" value="L"  />Link Click</p>

                            <label class="col-lg-3 control-label">Description</label>
                            <div class="col-lg-5">
                                <textarea name="desc" class="form-control" cols="50" ><?php if(isset($_GET['le_desc'])){ echo $_GET['le_desc']; }  ?></textarea>
                            </div>
                        </div>
                          </div>
                        <div class="well">
                        <div class="form-group">
                        <p class="col-lg-3"><input type="radio" name="type" value="E"  />Exchange Visits</p>
                            <label class="col-lg-3 control-label">Visits tims</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="exch" >
                                    <option value="1" <?php if(isset($_GET['le_exch'])=="1") {echo "selected"; } ?> >10s/-1pts to Visit</option>
                                    <option value="2" <?php if(isset($_GET['le_exch'])=="2") {echo "selected"; } ?> >20s/-2pts to Visit</option>
                                    <option value="3" <?php if(isset($_GET['le_exch'])=="3") {echo "selected"; } ?> >30s/-5pts to Visit</option>
                                    <option value="4" <?php if(isset($_GET['le_exch'])=="4") {echo "selected"; } ?> >60s/-10pts to Visit</option>

                                </select>
                            </div>
                        </div>
                          </div>

                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="le_submit" value="le_submit" class="btn btn-primary"><?php lang('add'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

							</div>
						</div>
					</div>
					<div class="col-md-6 validation-grid">
						<h4><span>Banners </span> Ads</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="post" class="form-horizontal" action="promote.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php if(isset($_GET['bn_name'])){ echo $_GET['bn_name']; }  ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php if(isset($_GET['bn_url'])){ echo $_GET['bn_url']; } ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Image Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="img" value="<?php if(isset($_GET['bn_img'])){ echo $_GET['bn_img']; }   ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Banner size</label>
                            <div class="col-lg-5">
                                <select class="form-control" name="bn_px" >
                                    <option value="468" <?php if(isset($_GET['bn_px'])=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                                    <option value="728" <?php if(isset($_GET['bn_px'])=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                                    <option value="300" <?php if(isset($_GET['bn_px'])=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                                    <option value="160" <?php if(isset($_GET['bn_px'])=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>

                                </select>
                            </div>
                        </div>

                         <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" name="bn_submit" value="bn_submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

							</div>
				</div>

       <?php  }  ?>
                <div class="clearfix"></div>
				</div>
            </div>
<?php }else{ echo"404"; }  ?>