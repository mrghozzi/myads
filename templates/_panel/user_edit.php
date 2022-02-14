<?php if(isset($s_st)=="buyfgeufb"){
if(isset($_GET['p'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['p'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);

 ?>
		<div id="page-wrapper">
          <div class="main-page">

               <div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
                        <div class="modal-body btn-warning">
							<div class="more-grids">
                            <form action="<?php url_site();  ?>/requests/file.php" method="post" enctype="multipart/form-data">
                            <center>
                            <hr />
                            <input name="file" type="file" class="btn btn-default" value="file" id="file" />
                            <hr />
                            <input type="submit" name="up" value="<?php lang('upload');  ?>" id="up" class="btn btn-primary" />
                            </center>
                            <div id='old_comment'></div>
                            </form>
                            </div>
                        </div>
                    </div>
               </div>

                     <!-- //modal camera -->

                       <div class="clearfix"></div>
								</div>
               <div class="clearfix"> </div>
            </div>
            <?php }else if(isset($_GET['e'])){
              $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['e'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
?>
   	<div id="page-wrapper">
          <div class="main-page">
     <div class="col-md-12 validation-grid"> <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                        <h4><span>Edit </span> User</h4>
						<div class="validation-grid1">

                       <section>
                         <div class="valid-top2">
								 <form id="defaultForm" method="post" class="form-horizontal" action="requests/user.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Username</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php echo $sus['username']; ?>" autocomplete="off" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email </label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="mail" value="<?php echo $sus['email']; ?>" autocomplete="off" required />
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Old password </label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="o_pass"  autocomplete="off"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">New password </label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="n_pass"  autocomplete="off"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Confirm new password </label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="c_pass"  autocomplete="off"  />
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
   				</div>
               <div class="clearfix"> </div>
            </div>
<?php }else{ echo "404"; }
 }else{ echo "404"; }  ?>