<?php if($s_st=="buyfgeufb"){  ?>
<?php if($_COOKIE['user']=="1" ){ ?>
<?php if(isset($_COOKIE['admin'])==isset($hachadmin)){ ?>
<!-- ACTION ITEM WRAP -->
 <div class="action-item-wrap">
        <!-- ACTION ITEM -->
        <div class="action-item dark header-settings-dropdown-trigger">
          <!-- ACTION ITEM ICON -->
          <svg class="action-item-icon icon-private">
            <use xlink:href="#svg-private"></use>
          </svg>
          <!-- /ACTION ITEM ICON -->
        </div>
        <!-- /ACTION ITEM -->
     <!-- DROPDOWN NAVIGATION -->
        <div class="dropdown-navigation header-settings-dropdown">
        <div class="dropdown-box-header">
              <!-- DROPDOWN BOX HEADER TITLE -->
              <p class="dropdown-box-header-title">Admin Board</p>
              <!-- /DROPDOWN BOX HEADER TITLE -->

             </div>
          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('list'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->
          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?home"><?php echo $lang['board']; ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?users"><?php echo $lang['users']; ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?report">Report</a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?widgets"><?php echo $lang['widgets']; ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?settings"><?php echo $lang['settings']; ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?updates">Updates</a>
          <!-- /DROPDOWN NAVIGATION LINK -->


          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('mode_admin'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION BUTTON -->
          <a href="<?php url_site();  ?>/admincp?dcont" class="dropdown-navigation-button button small secondary"><?php lang('close'); ?></a>
          <!-- /DROPDOWN NAVIGATION BUTTON -->

        </div>
        <!-- /DROPDOWN NAVIGATION -->
 </div>
<!-- /ACTION ITEM WRAP -->
<?php  } ?>
          <?php  } ?>
<?php }else{ echo"404"; }  ?>