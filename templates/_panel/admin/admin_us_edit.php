<?php if($s_st=="buyfgeufb"){  ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
           <div class="widget-box">
                     <center>
                     <a href="<?php echo $url_site; ?>/admincp?users" class="btn btn-primary" ><i class="fa fa-users"></i></a>
                     <a href="<?php echo $url_site; ?>/u/<?php echo $_GET['us_edit']; ?>" class="btn btn-primary" ><i class="fa fa-user"></i></a>
                     <a href="<?php echo $url_site; ?>/admincp?state&ty=banner&st=<?php echo $_GET['us_edit']; ?>" class="btn btn-warning" ><i class="fa fa-link"></i></a>
                     <a href="<?php echo $url_site; ?>/admincp?state&ty=link&st=<?php echo $_GET['us_edit']; ?>" class="btn btn-success" ><i class="fa fa-eye "></i></a>
                     </center>
           </div>
		   <div class="widget-box">
						<h4><span>Edit </span> User</h4>
						<div class="widget-box-content">

                        <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
          <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?us_edit=<?php echo $_GET['us_edit'];  ?>">
                    <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Username</label>
                      <input type="text" id="profile-name" name="name" value="<?php eus_echo('username'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">User Slug</label>
                      <input type="text" id="profile-Slug" name="slug" value="<?php sus_echo('o_valuer'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
                </div>
                <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-select">
                    <!-- FORM INPUT -->

                      <label for="profile-name">Email</label>
                      <input type="text" id="profile-email" name="mail" value="<?php eus_echo('email'); ?>">

                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-select">
                      <label for="profile-verified">verified account <i class="fa fa-fw fa-check-circle" style="color: #0066CC;" ></i></label>
                      <select id="profile-verified" name="check" >
                      <?php eus_selec() ?>
                      </select>
                      <svg class="form-select-icon icon-small-arrow">
                          <use xlink:href="#svg-small-arrow"></use>
                        </svg>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">PTS</label>
                      <input type="text" id="profile-Slug" name="pts" value="<?php eus_echo('pts'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
                </div>
                  <!-- /FORM ITEM -->
                </div>
                <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Exchange Visits PTS</label>
                      <input type="text" id="profile-name" name="vu" value="<?php eus_echo('vu'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Banners Ads PTS</label>
                      <input type="text" id="profile-Slug" name="nvu" value="<?php eus_echo('nvu'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Text Ads PTS</label>
                      <input type="text" id="profile-Slug" name="nlink" value="<?php eus_echo('nlink'); ?>">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
                </div>
                <div class="form-row">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary"><?php lang('edit'); ?></button>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                 </div>
          </form>
         </div>
     </div>
     <div class="widget-box">
             <h4><?php lang('e_pass'); ?></h4>
		  <div class="widget-box-content">
             <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?us_edit=<?php echo $_GET['us_edit'];  ?>">
              <div class="form-row">
               <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline"><?php lang('n_pass'); ?></label>
                      <input type="password" id="profile-Slug" name="n_pass" autocomplete="off">
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                 <!-- /FORM ITEM -->
               </div>
               <div class="form-row">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <button type="submit" name="ps_submit" value="ps_submit" class="btn btn-primary"><?php lang('edit'); ?></button>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
               </div>
             </form>
          </div>
     </div>
</div>
</div>
<?php }else{ echo"404"; }  ?>