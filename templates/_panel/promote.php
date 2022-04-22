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
	   <?php if(isset($_GET['p']) AND ($_GET['p'] =="banners")){  ?>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title"><?php lang('bannads'); ?></p>
         <br />
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['bn_name'])){ echo $_GET['bn_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['bn_url'])){ echo $_GET['bn_url']; }  ?>" required>
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
                        <option value="468" <?php if(isset($_GET['bn_px'])=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                        <option value="728" <?php if(isset($_GET['bn_px'])=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                        <option value="300" <?php if(isset($_GET['bn_px'])=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                        <option value="160" <?php if(isset($_GET['bn_px'])=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>
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
                      <input type="text" id="form-url" name="img" value="<?php if(isset($_GET['bn_img'])){ echo $_GET['bn_img']; }  ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" name="bn_submit" value="bn_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div>
      </form>
    </div>
  </div>
</div>
       <?php }else if(isset($_GET['p']) AND ($_GET['p'] =="link")){  ?>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title"><?php lang('textads'); ?></p>
         <br />
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" required>
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
                      <textarea id="profile-description" name="desc" placeholder="<?php lang('was_desc'); ?>" required><?php if(isset($_GET['le_desc'])){ echo $_GET['le_desc']; }  ?></textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <input type="hidden" name="type" value="L" />
                           <button type="submit" name="le_submit" value="bn_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div><br /><br />
      </form>
    </div>
  </div>
</div>
       <?php }else if(isset($_GET['p']) AND ($_GET['p']  =="exchange")){  ?>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title"><?php lang('exvisit'); ?></p>
         <br />
      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <div class="form-item">
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label>Visits tims</label>
                      <select name="exch" required>
                        <option value="1" <?php if(isset($_GET['le_exch'])=="1") {echo "selected"; } ?> >10s/-1pts to Visit</option>
                        <option value="2" <?php if(isset($_GET['le_exch'])=="2") {echo "selected"; } ?> >20s/-2pts to Visit</option>
                        <option value="3" <?php if(isset($_GET['le_exch'])=="3") {echo "selected"; } ?> >30s/-5pts to Visit</option>
                        <option value="4" <?php if(isset($_GET['le_exch'])=="4") {echo "selected"; } ?> >60s/-10pts to Visit</option>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                    <!-- /FORM SELECT -->
                  </div>
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <input type="hidden" name="type" value="E" />
                           <button type="submit" name="le_submit" value="le_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div>
      </form>
    </div>
  </div>
</div>
       <?php }else{   ?>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="tab-box">
          <!-- TAB BOX OPTIONS -->
          <div class="tab-box-options">
            <!-- TAB BOX OPTION -->
            <div class="tab-box-option active">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('bannads'); ?></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('textads'); ?></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('exvisit'); ?></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->
          </div>
          <!-- /TAB BOX OPTIONS -->

          <!-- TAB BOX ITEMS -->
          <div class="tab-box-items">
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: block;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <br />
                      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['bn_name'])){ echo $_GET['bn_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['bn_url'])){ echo $_GET['bn_url']; }  ?>" required>
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
                        <option value="468" <?php if(isset($_GET['bn_px'])=="468") {echo "selected"; } ?> >468x60 (-1 pts)</option>
                        <option value="728" <?php if(isset($_GET['bn_px'])=="728") {echo "selected"; } ?> >728x90 (-1 pts)</option>
                        <option value="300" <?php if(isset($_GET['bn_px'])=="300") {echo "selected"; } ?> >300x250 (-1 pts)</option>
                        <option value="160" <?php if(isset($_GET['bn_px'])=="160") {echo "selected"; } ?> >160x600 (-1 pts)</option>
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
                      <input type="text" id="form-url" name="img" value="<?php if(isset($_GET['bn_img'])){ echo $_GET['bn_img']; }  ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" name="bn_submit" value="bn_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div>
      </form>
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <br />
                      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" required>
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
                      <textarea id="profile-description" name="desc" placeholder="<?php lang('was_desc'); ?>" required><?php if(isset($_GET['le_desc'])){ echo $_GET['le_desc']; }  ?></textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <input type="hidden" name="type" value="L" />
                           <button type="submit" name="le_submit" value="le_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div> <br /><br />
      </form>
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <br />
                      <form id="defaultForm" method="post" class="form" action="<?php url_site();  ?>/promote">
         <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-name">Name ADS</label>
                      <input type="text" name="name" value="<?php if(isset($_GET['le_name'])){ echo $_GET['le_name']; } ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="profile-tagline">Url Link</label>
                      <input type="text" id="form-url" name="url" value="<?php if(isset($_GET['le_url'])){ echo $_GET['le_url']; }  ?>" required>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
         </div>
         <div class="form-row split">
                    <div class="form-item">
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label>Visits tims</label>
                      <select name="exch" required>
                        <option value="1" <?php if(isset($_GET['le_exch'])=="1") {echo "selected"; } ?> >10s/-1pts to Visit</option>
                        <option value="2" <?php if(isset($_GET['le_exch'])=="2") {echo "selected"; } ?> >20s/-2pts to Visit</option>
                        <option value="3" <?php if(isset($_GET['le_exch'])=="3") {echo "selected"; } ?> >30s/-5pts to Visit</option>
                        <option value="4" <?php if(isset($_GET['le_exch'])=="4") {echo "selected"; } ?> >60s/-10pts to Visit</option>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                    <!-- /FORM SELECT -->
                  </div>
                    <!-- FORM SELECT -->
                    <div class="form-item">
                      <div class="form-row split">
                           <input type="hidden" name="type" value="E" />
                           <button type="submit" name="le_submit" value="le_submit" class="button primary"><?php lang('add'); ?></button>
                      </div>
                    </div>
                  <!-- /FORM ITEM -->
         </div>
      </form>
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
          </div>
          </div>
          <!-- /TAB BOX ITEMS -->
  </div>
</div>
       <?php  }  ?>
<?php }else{ echo"404"; }  ?>