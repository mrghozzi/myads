<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){

                   $myads_last_time_updates = "https://github.com/mrghozzi/myads_check_updates/raw/main/latest_version.txt";
                   $last_time_updates = @file_get_contents($myads_last_time_updates, FILE_USE_INCLUDE_PATH);
                   $last_time_updates = strip_tags($last_time_updates, '');
                   $last_time_updates=substr($last_time_updates,0,5);
                    if($last_time_updates==$versionRow['o_valuer']){
                     $last_time_updates = $last_time_updates."&nbsp;<a href=\"{$url_site}/admincp?updates\" ><i class=\"fa fa-refresh\"></i></a>";
                     $last_time_updates_bg = " ";
                   }else{
                     $last_time_updates = $last_time_updates."&nbsp;<a href=\"{$url_site}/admincp?updates\" class=\"btn btn-primary\"><i class=\"fa fa-download\"></i></a>";
                     $last_time_updates_bg = "bg-warning";
                   }  

?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
   <div class="grid grid-half">
          <!-- STATS DECORATION -->
          <div class="stats-decoration v2 secondary">
            <!-- STATS DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('bannads'); ?></p>
            <!-- /STATS DECORATION TITLE -->

            <!-- STATS DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle"><?php lang('Total'); ?> : <?php nbr_state('banner'); ?></p>
            <!-- /STATS DECORATION SUBTITLE -->

            <!-- STATS DECORATION TEXT -->
            <p class="stats-decoration-text"><?php lang('Views'); ?> : <?php admin_state('banner'); ?></p>
            <p class="stats-decoration-text"><?php lang('Click'); ?> : <?php admin_state('vu'); ?></p>
            <!-- /STATS DECORATION TEXT -->

          </div>
          <!-- /STATS DECORATION -->

          <!-- STATS DECORATION -->
          <div class="stats-decoration v2 primary">
            <!-- STATS DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('textads'); ?></p>
            <!-- /STATS DECORATION TITLE -->

            <!-- STATS DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle"><?php lang('Total'); ?> : <?php nbr_state('link'); ?></p>
            <!-- /STATS DECORATION SUBTITLE -->

            <!-- STATS DECORATION TEXT -->
            <p class="stats-decoration-text"><?php lang('Views'); ?> : <?php admin_state('link'); ?></p>
            <p class="stats-decoration-text"><?php lang('Click'); ?> : <?php admin_state('clik'); ?></p>
            <!-- /STATS DECORATION TEXT -->

          </div>
          <!-- /STATS DECORATION -->

          <!-- STATS DECORATION -->
          <div class="stats-decoration v2 secondary">
            <!-- STATS DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('exvisit'); ?></p>
            <!-- /STATS DECORATION TITLE -->

            <!-- STATS DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle"><?php lang('Total'); ?> : <?php nbr_state('visits'); ?></p>
            <!-- /STATS DECORATION SUBTITLE -->
          </div>
          <!-- /STATS DECORATION -->

          <!-- STATS DECORATION -->
          <div class="stats-decoration v2 primary">
            <!-- STATS DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('users'); ?></p>
            <!-- /STATS DECORATION TITLE -->

            <!-- STATS DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle"><?php lang('Total'); ?> : <?php nbr_state('users'); ?></p>
            <!-- /STATS DECORATION SUBTITLE -->

            <!-- STATS DECORATION TEXT -->
            <p class="stats-decoration-text">Online : <?php online_admin(); ?></p>
            <p class="stats-decoration-text">Posts : <?php nbr_state('status');  ?></p>
            <!-- /STATS DECORATION TEXT -->

          </div>
          <!-- /STATS DECORATION -->
   </div>
   <div class="slider-line">
        <!-- SLIDER CONTROLS -->
        <div id="stat-block-slider-controls" class="slider-controls" aria-label="Carousel Navigation" tabindex="0">
          <!-- SLIDER CONTROL -->
          <div class="slider-control left" aria-controls="stat-block-slider" tabindex="-1" data-controls="prev" aria-disabled="true">
            <!-- SLIDER CONTROL ICON -->
            <svg class="slider-control-icon icon-small-arrow">
              <use xlink:href="#svg-small-arrow"></use>
            </svg>
            <!-- /SLIDER CONTROL ICON -->
          </div>
          <!-- /SLIDER CONTROL -->

          <!-- SLIDER CONTROL -->
          <div class="slider-control right" aria-controls="stat-block-slider" tabindex="-1" data-controls="next">
            <!-- SLIDER CONTROL ICON -->
            <svg class="slider-control-icon icon-small-arrow">
              <use xlink:href="#svg-small-arrow"></use>
            </svg>
            <!-- /SLIDER CONTROL ICON -->
          </div>
          <!-- /SLIDER CONTROL -->
        </div>
        <!-- /SLIDER CONTROLS -->

        <!-- SLIDER SLIDES -->
        <div class="tns-outer" id="stat-block-slider-ow"><div class="tns-liveregion tns-visually-hidden" aria-live="polite" aria-atomic="true">slide <span class="current">1 to 4</span>  of 5</div><div id="stat-block-slider-mw" class="tns-ovh"><div class="tns-inner" id="stat-block-slider-iw"><div id="stat-block-slider" class="slider-slides  tns-slider tns-carousel tns-subpixel tns-calc tns-horizontal" style="transition-duration: 0s; transform: translate3d(0px, 0px, 0px);">
          <!-- SLIDER SLIDE -->
          <div class="slider-slide tns-item tns-slide-active" id="stat-block-slider-item0">
            <!-- STAT BLOCK -->
            <div class="stat-block">
              <!-- STAT BLOCK DECORATION -->
              <div class="stat-block-decoration">
                <!-- STAT BLOCK DECORATION ICON -->
                <svg class="stat-block-decoration-icon icon-members">
                  <use xlink:href="#svg-members"></use>
                </svg>
                <!-- /STAT BLOCK DECORATION ICON -->
              </div>
              <!-- /STAT BLOCK DECORATION -->

              <!-- STAT BLOCK INFO -->
              <div class="stat-block-info">
                <!-- STAT BLOCK TITLE -->
                <p class="stat-block-title"><?php lang('lastrm'); ?></p>
                <!-- /STAT BLOCK TITLE -->

                <!-- STAT BLOCK TEXT -->
                <p class="stat-block-text"><a href="<?php echo $url_site;  ?>/u/<?php last_state('users','id'); ?>"><?php last_state('users','username'); ?></a></p>
                <!-- /STAT BLOCK TEXT -->
              </div>
              <!-- /STAT BLOCK INFO -->
            </div>
            <!-- /STAT BLOCK -->
          </div>
          <!-- /SLIDER SLIDE -->

          <!-- SLIDER SLIDE -->
          <div class="slider-slide tns-item tns-slide-active" id="stat-block-slider-item1">
            <!-- STAT BLOCK -->
            <div class="stat-block">
              <!-- STAT BLOCK DECORATION -->
              <div class="stat-block-decoration">
                <!-- STAT BLOCK DECORATION ICON -->
                <svg class="stat-block-decoration-icon icon-status">
                  <use xlink:href="#svg-status"></use>
                </svg>
                <!-- /STAT BLOCK DECORATION ICON -->
              </div>
              <!-- /STAT BLOCK DECORATION -->

              <!-- STAT BLOCK INFO -->
              <div class="stat-block-info">
                <!-- STAT BLOCK TITLE -->
                <p class="stat-block-title"><?php lang('lastps'); ?></p>
                <!-- /STAT BLOCK TITLE -->
                <?php $last_post = last_state('status','date',1); ?>
                <!-- STAT BLOCK TEXT -->
                <p class="stat-block-text">منذ &nbsp; <?php echo convertTime($last_post);  ?></p>
                <!-- /STAT BLOCK TEXT -->
              </div>
              <!-- /STAT BLOCK INFO -->
            </div>
            <!-- /STAT BLOCK -->
          </div>
          <!-- /SLIDER SLIDE -->

        </div></div></div></div>
        <!-- /SLIDER SLIDES -->
      </div>
      <div class="slider-line">
        <!-- SLIDER CONTROLS -->
        <div id="stat-block-slider-controls" class="slider-controls" aria-label="Carousel Navigation" tabindex="0">
          <!-- SLIDER CONTROL -->
          <div class="slider-control left" aria-controls="stat-block-slider" tabindex="-1" data-controls="prev" aria-disabled="true">
            <!-- SLIDER CONTROL ICON -->
            <svg class="slider-control-icon icon-small-arrow">
              <use xlink:href="#svg-small-arrow"></use>
            </svg>
            <!-- /SLIDER CONTROL ICON -->
          </div>
          <!-- /SLIDER CONTROL -->

          <!-- SLIDER CONTROL -->
          <div class="slider-control right" aria-controls="stat-block-slider" tabindex="-1" data-controls="next">
            <!-- SLIDER CONTROL ICON -->
            <svg class="slider-control-icon icon-small-arrow">
              <use xlink:href="#svg-small-arrow"></use>
            </svg>
            <!-- /SLIDER CONTROL ICON -->
          </div>
          <!-- /SLIDER CONTROL -->
        </div>
        <!-- /SLIDER CONTROLS -->

        <!-- SLIDER SLIDES -->
        <div class="tns-outer" id="stat-block-slider-ow"><div class="tns-liveregion tns-visually-hidden" aria-live="polite" aria-atomic="true">slide <span class="current">1 to 4</span>  of 5</div><div id="stat-block-slider-mw" class="tns-ovh"><div class="tns-inner" id="stat-block-slider-iw"><div id="stat-block-slider" class="slider-slides  tns-slider tns-carousel tns-subpixel tns-calc tns-horizontal" style="transition-duration: 0s; transform: translate3d(0px, 0px, 0px);">
          <!-- SLIDER SLIDE -->
          <div class="slider-slide tns-item tns-slide-active" id="stat-block-slider-item3">
            <!-- STAT BLOCK -->
            <div class="stat-block">
              <!-- STAT BLOCK DECORATION -->
              <div class="stat-block-decoration">
                <!-- STAT BLOCK DECORATION ICON -->
                <svg class="stat-block-decoration-icon icon-thumbs-up">
                  <use xlink:href="#svg-thumbs-up"></use>
                </svg>
                <!-- /STAT BLOCK DECORATION ICON -->
              </div>
              <!-- /STAT BLOCK DECORATION -->

              <!-- STAT BLOCK INFO -->
              <div class="stat-block-info">
                <!-- STAT BLOCK TITLE -->
                <p class="stat-block-title"><?php lang('allreactions'); ?></p>
                <!-- /STAT BLOCK TITLE -->
                 <?php $all_react = nbr_state('`like`',1)-nbr_follows(1); ?>
                <!-- STAT BLOCK TEXT -->
                <p class="stat-block-text"><?php echo $all_react;  ?> <?php lang('react2'); ?></p>
                <!-- /STAT BLOCK TEXT -->
              </div>
              <!-- /STAT BLOCK INFO -->
            </div>
            <!-- /STAT BLOCK -->
          </div>
          <!-- /SLIDER SLIDE -->

          <!-- SLIDER SLIDE -->
          <div class="slider-slide tns-item" id="stat-block-slider-item4" aria-hidden="true" tabindex="-1">
            <!-- STAT BLOCK -->
            <div class="stat-block">
              <!-- STAT BLOCK DECORATION -->
              <div class="stat-block-decoration">
                <!-- STAT BLOCK DECORATION ICON -->
                <svg class="stat-block-decoration-icon icon-add-friend">
                  <use xlink:href="#svg-add-friend"></use>
                </svg>
                <!-- /STAT BLOCK DECORATION ICON -->
              </div>
              <!-- /STAT BLOCK DECORATION -->

              <!-- STAT BLOCK INFO -->
              <div class="stat-block-info">
                <!-- STAT BLOCK TITLE -->
                <p class="stat-block-title"><?php lang('allFollowers'); ?></p>
                <!-- /STAT BLOCK TITLE -->
                <?php $all_follows = nbr_follows(1); ?>
                <!-- STAT BLOCK TEXT -->
                <p class="stat-block-text"><?php echo $all_follows;  ?> <?php lang('Followers'); ?></p>
                <!-- /STAT BLOCK TEXT -->
              </div>
              <!-- /STAT BLOCK INFO -->
            </div>
            <!-- /STAT BLOCK -->
          </div>
          <!-- /SLIDER SLIDE -->
        </div></div></div></div>
        <!-- /SLIDER SLIDES -->
      </div>

</div>
<div class="grid-column" >

  <div class="btn-group-vertical">
     <a href="<?php url_site();  ?>/admincp?report" class="btn btn-primary" >Report
      <span class="badge badge-light">
        <?php $catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM report WHERE statu=1" );
              $catcount->execute();
              $abcat=$catcount->fetch(PDO::FETCH_ASSOC);
              echo $abcat['nbr'];
        ?>
      </span>
    </a>
        <?php if(isset($_GET['sitemap'])){  ?>
    <a href="<?php echo $url_site;  ?>/sitemap" class="btn btn-danger" ><b>Sitemap</b></a>
    <a href="<?php echo $url_site;  ?>/sitemap.xml" class="btn btn-dark" target="_blank">/sitemap.xml&nbsp;<b><i class="fa fa-external-link" ></i></b></a>
        <?php }else{  ?>
    <a href="<?php echo $url_site;  ?>/sitemap" class="btn btn-success" ><b>Sitemap</b></a>
         <?php } ?>
    <a href="https://github.com/mrghozzi/myads/wiki/changelogs" class="btn btn-warning" target="_blank">Changelogs&nbsp;<b><i class="fa fa-external-link" ></i></b></a>
    
  </div>
  <div class="widget-box">
        <div class="widget-box-content no-margin-top">
           <div class="table table-top-friends join-rows">
						<div class="table-header">
                <div class="table-header-column textpost">
                   <center>Devlope by : <a href="https://github.com/mrghozzi">MrGhozzi</a></center>
                </div>
                
						</div>
						<div class="table-body">
							<div class="table-row tiny">
                <div class="table-header-column textpost">
                   <center>Program name : MYads</center>
                </div>
							</div>
						</div>
            <div class="table-body">
							<div class="table-row tiny">
                <div class="table-header-column textpost">
                   <center><?php lang('version');  ?> : v<?php myads_version();  ?></center>
                </div>
							</div>
						</div>
            <div class="table-body <?php echo $last_time_updates_bg; ?>">
							<div class="table-row tiny">
                <div class="table-header-column textpost">
                   <center>Latest version : <?php echo $last_time_updates; ?></center>
                </div>
							</div>
						</div>
		   </div>
        </div>
      </div>
</div>
</div>
<?php }else{ echo"404"; }  ?>