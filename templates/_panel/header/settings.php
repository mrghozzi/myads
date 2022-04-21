<?php if($s_st=="buyfgeufb"){  ?>
<!-- ACTION ITEM WRAP -->
 <div class="action-item-wrap">
        <!-- ACTION ITEM -->
        <div class="action-item dark header-settings-dropdown-trigger">
          <!-- ACTION ITEM ICON -->
          <svg class="action-item-icon icon-settings">
            <use xlink:href="#svg-settings"></use>
          </svg>
          <!-- /ACTION ITEM ICON -->
        </div>
        <!-- /ACTION ITEM -->
     <!-- DROPDOWN NAVIGATION -->
        <div class="dropdown-navigation header-settings-dropdown">
          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('account'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>"><?php echo $lang['e_profile']; ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/p<?php echo $_COOKIE['user']; ?>">Change Avatar/Cover</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/options/<?php echo $_COOKIE['user']; ?>"><?php echo $lang['options']; ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('ads'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/b_list"><?php lang('list'); echo"&nbsp;"; lang('bannads'); ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/l_list"><?php lang('list'); echo"&nbsp;"; lang('textads'); ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/v_list"><?php lang('list'); echo"&nbsp;"; lang('exvisit'); ?></a>
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/referral"><?php lang('list'); ?> <?php lang('referal'); ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->
          <?php if($_COOKIE['user']=="1" ){ ?>

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('mode_admin'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <?php if(!(isset($_COOKIE['admin'])==isset($hachadmin)) ){ ?>
          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?cont"><?php lang('activate'); ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->
           <?php }else   if(isset($_COOKIE['admin'])==isset($hachadmin)){ ?>
           <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?dcont"><?php lang('close'); ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->
          <?php  } ?>

          <?php  } ?>
          <!-- DROPDOWN NAVIGATION BUTTON -->
          <a href="<?php url_site();  ?>/logout?logout" class="dropdown-navigation-button button small secondary"><?php lang('logout'); ?></a>
          <!-- /DROPDOWN NAVIGATION BUTTON -->
        </div>
        <!-- /DROPDOWN NAVIGATION -->
 </div>
<!-- /ACTION ITEM WRAP -->
<?php }else{ echo"404"; }  ?>