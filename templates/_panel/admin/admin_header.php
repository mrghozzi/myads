<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/state_banner.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/Settings-icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title">ADMIN</p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"><?php lang('board'); ?></p>
      <!-- /SECTION BANNER TEXT -->
    </div>
    <!-- /SECTION BANNER -->
    <script language="javascript" src="https://api.adstn.gq/news/myads_news.php?v=<?php myads_fversion();  ?>"></script>
<?php }else{ echo"404"; }  ?>