<?php if(isset($s_st)=="buyfgeufb"){ dinstall_d();
if(isset($_GET['e'])){
              $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['e'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('user_settings/nav_settings');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="widget-box">
     <div class="col-md-12 validation-grid"> <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                        <h4><?php lang('e_profile'); ?></h4>
                        <hr />
					   <div class="validation-grid1">
                       <section>
                         <div class="valid-top2">
								 <form id="defaultForm" method="post" class="form-horizontal" action="requests/user.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('us_name'); ?></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="name" value="<?php echo $sus['username']; ?>" autocomplete="off" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?php lang('mail_o'); ?></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="mail" value="<?php echo $sus['email']; ?>" autocomplete="off" required />
                            </div>
                        </div>
                        <hr />
                        <h4><?php lang('e_pass'); ?></h4>
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
            </div>
            </div>
<?php }else{ echo "404"; }
 }else{ echo "404"; }  ?>