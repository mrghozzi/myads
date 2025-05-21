<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){  global  $sus; global $_GET; ?>
    <!-- SECTION NAVIGATION -->
    <nav class="section-navigation">
      <!-- SECTION MENU -->
      <div id="section-navigation-slider" class="section-menu">
        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['u'])){ echo "active"; } ?>" href="<?php url_site();  ?>/u/<?php echo $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-timeline">
            <use xlink:href="#svg-timeline"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->
  
          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('timeline'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->
   
        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['ff'])){ echo "active"; } ?> " href="<?php url_site();  ?>/uFriends/<?php echo $sus['id']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-friend">
            <use xlink:href="#svg-friend"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->

          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('friends'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->

        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['ph'])){ echo "active"; } ?> " href="<?php url_site();  ?>/uPhotos/<?php echo  $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-photos">
            <use xlink:href="#svg-photos"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->
  
          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('photos'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->

        <!-- SECTION MENU ITEM -->
         <a class="section-menu-item <?php if(isset($_GET['blog'])){ echo "active"; } ?>" href="<?php url_site();  ?>/uBlog/<?php echo  $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-blog-posts">
            <use xlink:href="#svg-blog-posts"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->

          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('blogs'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->
 
        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['ulinks'])){ echo "active"; } ?>" href="<?php url_site();  ?>/ulinks/<?php echo  $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          
    <!-- ICON LIST GRID VIEW -->
    <svg class="section-menu-item-icon icon-list-grid-view">
      <use xlink:href="#svg-list-grid-view"></use>
    </svg>
    <!-- /ICON LIST GRID VIEW -->
          <!-- /SECTION MENU ITEM ICON -->

          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('directory'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->

        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['uforum'])){ echo "active"; } ?>" href="<?php url_site();  ?>/uforum/<?php echo  $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-forum">
            <use xlink:href="#svg-forum"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->

          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('forum'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->

        <!-- SECTION MENU ITEM -->
        <a class="section-menu-item <?php if(isset($_GET['ushop'])){ echo "active"; } ?>" href="<?php url_site();  ?>/ushop/<?php echo  $usrRow['o_valuer']; ?>">
          <!-- SECTION MENU ITEM ICON -->
          <svg class="section-menu-item-icon icon-store">
            <use xlink:href="#svg-store"></use>
          </svg>
          <!-- /SECTION MENU ITEM ICON -->

          <!-- SECTION MENU ITEM TEXT -->
          <p class="section-menu-item-text"><?php lang('Store'); ?></p>
          <!-- /SECTION MENU ITEM TEXT -->
        </a>
        <!-- /SECTION MENU ITEM -->
      </div>
      <!-- /SECTION MENU -->

      <!-- SLIDER CONTROLS -->
      <div id="section-navigation-slider-controls" class="slider-controls">
        <!-- SLIDER CONTROL -->
        <div class="slider-control left">
          <!-- SLIDER CONTROL ICON -->
          <svg class="slider-control-icon icon-small-arrow">
            <use xlink:href="#svg-small-arrow"></use>
          </svg>
          <!-- /SLIDER CONTROL ICON -->
        </div>
        <!-- /SLIDER CONTROL -->

        <!-- SLIDER CONTROL -->
        <div class="slider-control right">
          <!-- SLIDER CONTROL ICON -->
          <svg class="slider-control-icon icon-small-arrow">
            <use xlink:href="#svg-small-arrow"></use>
          </svg>
          <!-- /SLIDER CONTROL ICON -->
        </div>
        <!-- /SLIDER CONTROL -->
      </div>
      <!-- /SLIDER CONTROLS -->
    </nav>
    <!-- /SECTION NAVIGATION -->
<?php }else{ echo"404"; }  ?>