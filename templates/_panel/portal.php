<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>

  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/newsfeed-icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('Community'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
    </div>
    <!-- /SECTION BANNER -->

<div class="grid grid-3-6-3 mobile-prefer-content" >
<div class="grid-column" >
<?php widgets(1); ?>
</div>
<div class="grid-column" >
<?php template_mine('status/add_post');  ?>
<?php   global  $tabitem_portal; if(isset ($_COOKIE['user']) AND  isset($tabitem_portal)){ ?> 
        <!-- SIMPLE TAB ITEMS -->
        <div class="simple-tab-items">
          <!-- SIMPLE TAB ITEM -->
          <a href="<?php echo $url_site; ?>/portal/all" class="simple-tab-item <?php   if($tabitem_portal=="all"){ echo "active"; } ?>">All Updates</a>
          <!-- /SIMPLE TAB ITEM -->

          <!-- SIMPLE TAB ITEM -->
          <a href="<?php echo $url_site; ?>/portal" class="simple-tab-item <?php   if($tabitem_portal=="me"){ echo "active"; } ?>">Following</a>
          <!-- /SIMPLE TAB ITEM -->

        </div>
        <!-- /SIMPLE TAB ITEMS -->
<?php } ?>        
<?php   forum_tpc_list();   ?>
</div>
<div class="grid-column" >
<?php widgets(2); ?>
</div>
</div>

<?php }else{ echo"404"; }  ?>