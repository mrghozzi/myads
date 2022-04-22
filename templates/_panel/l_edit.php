<?php if($s_st=="buyfgeufb"){  ?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/04.jpg) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/promote.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('Promotysite'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php if(isset($_SERVER['HTTP_REFERER'])){ echo $_SERVER['HTTP_REFERER']; } ?>">
          <i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;<?php lang('go_back'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<?php if(isset($_GET['bnerrMSG'])){  ?>
<div class="grid grid" >
  <div class="grid-column" >
      <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
  </div>
</div>
<?php }  ?>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title"><?php lang('textads'); ?></p>
         <br />
<?php if(isset($_COOKIE['user']) AND !isset($_COOKIE['admin']) ){ ?>
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/l_edit?id=<?php echo $_GET['id'];  ?>">
<?php }else if(isset($_COOKIE['user'])=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){   ?>
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/admincp?l_edit=<?php echo $_GET['l_edit'];  ?>">
<?php } ?>
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php bnr_echo('name'); ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php bnr_echo('url'); ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small full">
                      <textarea id="profile-description" name="desc" placeholder="<?php lang('was_desc'); ?>" required><?php bnr_echo('txt'); ?></textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
<?php if(isset($_COOKIE['user'])=="1" AND isset($_COOKIE['admin'])==$uRow['pass'] ){   ?>
                  <div class="form-item">
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label>Statu</label>
                      <select  name="statu" required>
                        <option value="1" <?php if($statuRow=="1") { echo "selected"; } ?> >ON</option>
                        <option value="2" <?php if($statuRow=="2") { echo "selected"; } ?> >OFF</option>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                    <!-- /FORM SELECT -->
                  </div>
                  <!-- /FORM ITEM -->
                    <!-- FORM SELECT -->
<?php } ?>
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <input type="hidden" name="type" value="L" />
                           <button type="submit" name="ed_submit" value="ed_submit" class="button primary"><?php lang('edit'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div><br /><br />
      </form>
    </div>
  </div>
</div>

<?php }else{ echo"404"; }  ?>