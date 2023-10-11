<?php if($s_st=="buyfgeufb"){  ?>
   <!-- NAVIGATION WIDGET -->
  <nav id="navigation-widget" class="navigation-widget navigation-widget-desktop sidebar left hidden" data-simplebar>
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
    <!-- MENU -->
    <ul class="menu">
    <?php nev_menu();  ?>
    </ul>
    <!-- /MENU -->
  </nav>
  <!-- /NAVIGATION WIDGET -->
<?php }else{ echo"404"; }  ?>