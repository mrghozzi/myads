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
         <p class="widget-box-title"><?php lang('bannads'); ?></p>
         <br />
<?php if(isset($_COOKIE['user']) AND isset($_GET['id']) ){ ?>
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/b_edit?id=<?php echo $_GET['id'];  ?>">
<?php }else if((isset($_COOKIE['user'])=="1") AND (isset($_COOKIE['admin'])==$uRow['pass']) AND (isset($_GET['b_edit'])) ){   ?>
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/admincp?b_edit=<?php echo $_GET['b_edit'];  ?>">
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
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label>Banner size</label>
                      <select name="bn_px" required>
                        <option value="468" <?php if($slctRow=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                        <option value="728" <?php if($slctRow=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                        <option value="300" <?php if($slctRow=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                        <option value="160" <?php if($slctRow=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>
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

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Image Link</label>
                      <input type="text" id="form-url" name="img" value="<?php bnr_echo('img'); ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <!-- FORM ITEM -->
<?php if((isset($_COOKIE['user'])=="1") AND (isset($_COOKIE['admin'])==$uRow['pass']) AND (isset($_GET['b_edit'])) ){   ?>
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
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" name="bn_submit" value="bn_submit" class="button primary"><?php lang('edit'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div>
      </form>
      <br /><center><div><img src="<?php bnr_echo('img'); ?>" alt="<?php bnr_echo('name'); ?>" /> </div>    </center>
    </div>
  </div>
</div>
<?php }else{ echo"404"; }  ?>