<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
        <div class="validation-section">
					<h2>Place a text ads in the Kariya Support Forum.</h2>
					<div class="col-md-12 validation-grid">
						<h4><span>Through this service you can add a text ads</span>   in the Kariya forum for 30 days .</h4>
						<div class="validation-grid1">
                       <section>
                        <?php if($_GET['errMSG']){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['errMSG'];  ?></div>
                        <?php }  ?>
                            <div class="valid-top2">
								 <form id="defaultForm" method="POST" class="form-horizontal" action="krhost.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Name</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php echo $_GET['le_name']; ?>" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Url Link </label>
                            <div class="col-lg-5">
                                <input type="url" class="form-control" name="url" value="<?php echo $_GET['le_url']; ?>" autocomplete="off" />
                            </div>
                        </div>
                         <div class="form-group">

                            <input type="hidden" name="type" value="L" />
                            <label class="col-lg-3 control-label">Description</label>
                            <div class="col-lg-5">
                                <textarea name="desc" class="form-control" cols="50" ><?php echo $_GET['le_desc']; ?></textarea>
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
     <div class="clearfix"></div>
				</div>
            </div>
<?php }else{ echo"404"; }  ?>