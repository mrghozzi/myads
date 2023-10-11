<?php if($s_st=="buyfgeufb"){  ?>
  <!-- NAVIGATION WIDGET -->
  <nav id="navigation-widget-mobile" class="navigation-widget navigation-widget-mobile sidebar left hidden" data-simplebar>
    <!-- NAVIGATION WIDGET CLOSE BUTTON -->
    <div class="navigation-widget-close-button">
      <!-- NAVIGATION WIDGET CLOSE BUTTON ICON -->
      <svg class="navigation-widget-close-button-icon icon-back-arrow">
        <use xlink:href="#svg-back-arrow"></use>
      </svg>
      <!-- NAVIGATION WIDGET CLOSE BUTTON ICON -->
    </div>
    <!-- /NAVIGATION WIDGET CLOSE BUTTON -->
     <?php if(isset($_COOKIE['user'])){ ?>
    <!-- NAVIGATION WIDGET INFO WRAP -->
    <div class="navigation-widget-info-wrap">
      <!-- NAVIGATION WIDGET INFO -->
      <div class="navigation-widget-info">
        <!-- USER AVATAR -->
        <a class="user-avatar small no-outline" href="<?php url_site();  ?>/u/<?php user_row('id'); ?>">
          <!-- USER AVATAR CONTENT -->
          <div class="user-avatar-content">
            <!-- HEXAGON -->
            <div class="hexagon-image-30-32" data-src="<?php url_site();  ?>/<?php user_row('img'); ?>"></div>
            <!-- /HEXAGON -->
          </div>
          <!-- /USER AVATAR CONTENT -->

          <!-- USER AVATAR PROGRESS BORDER -->
          <div class="user-avatar-progress-border">
            <!-- HEXAGON -->
            <div class="hexagon-border-40-44"></div>
            <!-- /HEXAGON -->
          </div>
          <!-- /USER AVATAR PROGRESS BORDER -->

       </a>
        <!-- /USER AVATAR -->

        <!-- NAVIGATION WIDGET INFO TITLE -->
        <p class="navigation-widget-info-title"><a href="<?php url_site();  ?>/u/<?php user_row('id'); ?>"><?php user_row('username'); ?></a></p>
        <!-- /NAVIGATION WIDGET INFO TITLE -->

        <!-- NAVIGATION WIDGET INFO TEXT -->
        <p class="navigation-widget-info-text">Welcome Back!</p>
        <!-- /NAVIGATION WIDGET INFO TEXT -->
      </div>
      <!-- /NAVIGATION WIDGET INFO -->

      <!-- NAVIGATION WIDGET BUTTON -->
      <a href="<?php url_site();  ?>/logout?logout" class="navigation-widget-info-button button small secondary"><?php lang('logout'); ?></a>
      <!-- /NAVIGATION WIDGET BUTTON -->
    </div>
    <!-- /NAVIGATION WIDGET INFO WRAP -->
    <?php }else{  ?>
    <div class="navigation-widget-info-wrap">
    <a href="<?php url_site();  ?>/login" class="navigation-widget-info-button button small secondary"><?php lang('login'); ?></a>
    <a href="<?php url_site();  ?>/register" class="navigation-widget-info-button button small secondary"><?php lang('sign_up'); ?></a>
    </div>
    <?php }  ?>
    <!-- NAVIGATION WIDGET SECTION TITLE -->
    <p class="navigation-widget-section-title">Sections</p>
    <!-- /NAVIGATION WIDGET SECTION TITLE -->
    <!-- MENU -->
    <ul class="menu">
      <!-- MENU ITEM -->
      <li class="menu-item">
        <!-- MENU ITEM LINK -->
        <a class="menu-item-link text-tooltip-tfr" href="<?php url_site();  ?>/portal" >
          <!-- MENU ITEM LINK ICON -->
          <svg class="menu-item-link-icon icon-newsfeed">
            <use xlink:href="#svg-newsfeed"></use>
          </svg>
          <!-- /MENU ITEM LINK ICON -->
          <?php lang('Community'); ?>
        </a>
        <!-- /MENU ITEM LINK -->
      </li>
      <!-- /MENU ITEM -->
     <?php if(isset($_COOKIE['user'])){ ?>
      <!-- MENU ITEM -->
      <li class="menu-item active">
        <!-- MENU ITEM LINK -->
        <a class="menu-item-link text-tooltip-tfr" href="<?php url_site();  ?>/home" >
          <!-- MENU ITEM LINK ICON -->
          <svg class="menu-item-link-icon icon-overview">
            <use xlink:href="#svg-overview"></use>
          </svg>
          <!-- /MENU ITEM LINK ICON -->
          <?php lang('board'); ?>
        </a>
        <!-- /MENU ITEM LINK -->
      </li>
      <!-- /MENU ITEM -->
     <?php }  ?>

      <!-- MENU ITEM -->
      <li class="menu-item">
        <!-- MENU ITEM LINK -->
        <a class="menu-item-link text-tooltip-tfr" href="<?php url_site();  ?>/forum" >
          <!-- MENU ITEM LINK ICON -->
          <svg class="menu-item-link-icon icon-forums">
            <use xlink:href="#svg-forums"></use>
          </svg>
          <!-- /MENU ITEM LINK ICON -->
          <?php lang('forum');  ?>
        </a>
        <!-- /MENU ITEM LINK -->
      </li>
      <!-- /MENU ITEM -->

       <!-- MENU ITEM -->
      <li class="menu-item">
        <!-- MENU ITEM LINK -->
        <a class="menu-item-link text-tooltip-tfr" href="<?php url_site();  ?>/directory" >
          <!-- MENU ITEM LINK ICON -->
          <svg class="menu-item-link-icon icon-list-grid-view">
            <use xlink:href="#svg-list-grid-view"></use>
          </svg>
          <!-- /MENU ITEM LINK ICON -->
          <?php lang('directory');  ?>
        </a>
        <!-- /MENU ITEM LINK -->
      </li>
      <!-- /MENU ITEM -->

      <!-- MENU ITEM -->
      <li class="menu-item">
        <!-- MENU ITEM LINK -->
        <a class="menu-item-link text-tooltip-tfr" href="<?php url_site();  ?>/store" >
          <!-- MENU ITEM LINK ICON -->
          <svg class="menu-item-link-icon icon-marketplace">
            <use xlink:href="#svg-marketplace"></use>
          </svg>
          <!-- /MENU ITEM LINK ICON -->
          <?php lang('Store');  ?>
        </a>
        <!-- /MENU ITEM LINK -->
      </li>
      <!-- /MENU ITEM -->
    </ul>
    <!-- /MENU -->
    <?php if(isset($_COOKIE['user'])){ ?>
    <!-- NAVIGATION WIDGET SECTION TITLE -->
    <p class="navigation-widget-section-title"><?php lang('account'); ?></p>
    <!-- /NAVIGATION WIDGET SECTION TITLE -->

    <!-- NAVIGATION WIDGET SECTION LINK -->
    <a class="navigation-widget-section-link" href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>"><?php echo $lang['e_profile']; ?></a>
    <a class="navigation-widget-section-link" href="<?php url_site();  ?>/p<?php echo $_COOKIE['user']; ?>">Change Avatar/Cover</a>
    <a class="navigation-widget-section-link" href="<?php url_site();  ?>/options"><?php echo $lang['options']; ?></a>
    <!-- /NAVIGATION WIDGET SECTION LINK -->
    <?php  } ?>
    <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']=="1") ){ ?>
    <!-- NAVIGATION WIDGET SECTION TITLE -->
    <p class="navigation-widget-section-title"><?php lang('mode_admin'); ?></p>
    <!-- /NAVIGATION WIDGET SECTION TITLE -->

    <!-- NAVIGATION WIDGET SECTION LINK -->
    <?php if(!(isset($_COOKIE['admin'])==isset($hachadmin)) ){ ?>
    <a class="navigation-widget-section-link" href="<?php url_site();  ?>/admincp?cont"><?php lang('activate'); ?></a>
    <?php }else   if(isset($_COOKIE['admin'])==isset($hachadmin)){ ?>
    <a class="navigation-widget-section-link" href="<?php url_site();  ?>/admincp?dcont"><?php lang('close'); ?></a>
    <?php  } ?>
    <!-- /NAVIGATION WIDGET SECTION LINK -->
    <?php  } ?>
    <!-- NAVIGATION WIDGET SECTION TITLE -->
    <p class="navigation-widget-section-title">Main Links</p>
    <!-- /NAVIGATION WIDGET SECTION TITLE -->

    <!-- NAVIGATION WIDGET SECTION LINK -->
    <?php mob_menu();  ?>
    <!-- /NAVIGATION WIDGET SECTION LINK -->

    <!-- /NAVIGATION WIDGET SECTION LINK -->
  </nav>
  <!-- /NAVIGATION WIDGET -->
<?php }else{ echo"404"; }  ?>