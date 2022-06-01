<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/home_banner.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/home_icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('board'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
    </div>
    <!-- /SECTION BANNER -->

    <!-- SECTION HEADER -->
    <div class="grid">
      <!-- SECTION HEADER INFO -->
    
        <!-- SECTION PRETITLE -->
<?php  if(isset($_GET['errMSG'])){  ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong><?php lang('warning'); ?></strong> <?php echo $_GET['errMSG'];  ?>
    </div>
 <?php }
 if(isset($_GET['MSG'])){  ?>
      <div class="alert alert-success alert-dismissible" role="alert">
       <?php echo $_GET['MSG'];  ?>
       <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
<?php }  ?>
        <!-- /SECTION TITLE -->

      <!-- /SECTION HEADER INFO -->
    </div>
    <!-- /SECTION HEADER -->

     <!-- GRID -->
    <div class="grid">
      <!-- GRID -->
      <div class="grid grid-3-3-3-3 centered">
         <!-- banner BOX -->
        <div class="stats-box small " style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/01.jpg) no-repeat center;background-size: cover;">
          <!-- banner BOX VALUE WRAP -->
          <div class="stats-box-value-wrap">
            <!-- banner BOX VALUE -->
            <p class="stats-box-value"><?php vu_state_row('banner','vu'); ?></p>
            <!-- /banner BOX VALUE -->

            <!-- banner BOX DIFF -->
            <div class="stats-box-diff">
              <!-- banner BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <!-- ICON STATUS -->
                <svg class="icon-status" style="fill: #41efff;" >
                <use xlink:href="#svg-status"></use>
                </svg>
                <!-- /ICON STATUS -->
                <!-- /ICON PLUS SMALL -->
              </div>
              <!-- /banner BOX DIFF ICON -->

              <!-- banner BOX DIFF VALUE -->
              <p class="stats-box-diff-value">&nbsp;<?php lang('Views'); ?></p>
              <!-- /banner BOX DIFF VALUE -->
            </div>
            <!-- /banner BOX DIFF -->
          </div>
          <!-- /banner BOX VALUE WRAP -->

          <!-- banner BOX TITLE -->
          <p class="stats-box-title"><?php lang('bannads'); ?></p>
          <!-- /banner BOX TITLE -->

          <!-- banner BOX TEXT -->
          <a class="stats-box-text" href="#Views" ><?php lang('MoreInfo'); ?></a>
          <!-- /banner BOX TEXT -->
        </div>
        <!-- /banner BOX -->
       <!-- link BOX -->
        <div class="stats-box small " style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/02.jpg) no-repeat center;background-size: cover;">
          <!-- link BOX VALUE WRAP -->
          <div class="stats-box-value-wrap">
            <!-- link BOX VALUE -->
            <p class="stats-box-value"><?php vu_state_row('link','clik'); ?></p>
            <!-- /link BOX VALUE -->

            <!-- link BOX DIFF -->
            <div class="stats-box-diff">
              <!-- link BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <!-- ICON STATUS -->
                    <!-- ICON EVENTS WEEKLY -->
                    <svg class="icon-events-weekly"  style="fill: #41efff;" >
                    <use xlink:href="#svg-events-weekly"></use>
                    </svg>
                   <!-- /ICON EVENTS WEEKLY -->
                <!-- /ICON STATUS -->
                <!-- /ICON PLUS SMALL -->
              </div>
              <!-- /link BOX DIFF ICON -->

              <!-- link BOX DIFF VALUE -->
              <p class="stats-box-diff-value">&nbsp;<?php lang('Click'); ?></p>
              <!-- /link BOX DIFF VALUE -->
            </div>
            <!-- /link BOX DIFF -->
          </div>
          <!-- /link BOX VALUE WRAP -->

          <!-- link BOX TITLE -->
          <p class="stats-box-title"><?php lang('textads'); ?></p>
          <!-- /link BOX TITLE -->

          <!-- link BOX TEXT -->
          <a class="stats-box-text" href="#link" ><?php lang('MoreInfo'); ?></a>
          <!-- /link BOX TEXT -->
        </div>
        <!-- /link BOX -->
        <!-- visits BOX -->
        <div class="stats-box small " style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/03.jpg) no-repeat center;background-size: cover;">
          <!-- visits BOX VALUE WRAP -->
          <div class="stats-box-value-wrap">
            <!-- visits BOX VALUE -->
            <p class="stats-box-value"><?php vu_state_row('visits','vu'); ?></p>
            <!-- /visits BOX VALUE -->

            <!-- visits BOX DIFF -->
            <div class="stats-box-diff">
              <!-- visits BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <svg class="icon-timeline"  style="fill: #41efff;" >
                  <use xlink:href="#svg-timeline"></use>
                </svg>
              </div>
              <!-- /visits BOX DIFF ICON -->

              <!-- visits BOX DIFF VALUE -->
              <p class="stats-box-diff-value">&nbsp;<?php lang('visits'); ?></p>
              <!-- /visits BOX DIFF VALUE -->
            </div>
            <!-- /visits BOX DIFF -->
          </div>
          <!-- /visits BOX VALUE WRAP -->

          <!-- visits BOX TITLE -->
          <p class="stats-box-title"><?php lang('exvisit'); ?></p>
          <!-- /visits BOX TITLE -->

          <!-- visits BOX TEXT -->
          <a class="stats-box-text" href="#Exchange" ><?php lang('MoreInfo'); ?></a>
          <!-- /visits BOX TEXT -->
        </div>
        <!-- /visits BOX -->
        <!-- pts BOX -->
        <div class="stats-box small " style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/04.jpg) no-repeat center;background-size: cover;">
          <!-- pts BOX VALUE WRAP -->
          <div class="stats-box-value-wrap">
            <!-- pts BOX VALUE -->
            <p class="stats-box-value"><?php user_row('pts'); ?></p>
            <!-- /pts BOX VALUE -->

            <!-- pts BOX DIFF -->
            <div class="stats-box-diff">
              <!-- pts BOX DIFF ICON -->
              <div>
                    <!-- ICON ITEM -->
                   <svg class="icon-item"  style="fill: #41efff;" >
                   <use xlink:href="#svg-item"></use>
                   </svg>
                   <!-- /ICON ITEM -->
            </div>
              <!-- /pts BOX DIFF ICON -->

              <!-- pts BOX DIFF VALUE -->
              <p class="stats-box-diff-value">&nbsp;PTS</p>
              <!-- /pts BOX DIFF VALUE -->
            </div>
            <!-- /pts BOX DIFF -->
          </div>
          <!-- /pts BOX VALUE WRAP -->

          <!-- pts BOX TITLE -->
          <p class="stats-box-title"><?php lang('pts'); ?></p>
          <!-- /pts BOX TITLE -->

          <!-- pts BOX TEXT -->
          <a class="stats-box-text" href="#pts" ><?php lang('MoreInfo'); ?></a>
          <!-- /pts BOX TEXT -->
        </div>
        <!-- /pts BOX -->
        </div>
        <?php ads_site(2); ?>
          <!-- banner DECORATION -->
          <div class="stats-decoration v2 big secondary" id="Views" style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/05-big.png) repeat-x bottom ;" >
            <!-- banner DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('bannads'); ?></p>
            <!-- /banner DECORATION TITLE -->

            <!-- banner DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle">
            <?php echo $lang['you_have']."&nbsp;"; user_row('nvu'); echo "&nbsp;".$lang['ptvyba']; ?>&nbsp;
            <?php echo $lang['your']."&nbsp;"; vu_state_row('banner','vu'); echo "&nbsp;".$lang['bahbpb']; ?>&nbsp;
            <?php echo $lang['And']."&nbsp;"; vu_state_row('banner','clik'); echo "&nbsp;".$lang['Clik_ads']; ?>
            </p>
            <!-- /banner DECORATION SUBTITLE -->

            <!-- banner DECORATION TEXT -->
            <p class="stats-decoration-text">
              <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
              <a href="https://www.adstn.gq/kb/myads:Banners Ads" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
               &nbsp;
              <?php } ?>
              <a class="button tertiary padded" href="<?php url_site();  ?>/state?ty=banner&st=vu" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
               &nbsp;
              <a  href="<?php url_site();  ?>/b_list.php" class="button secondary padded" ><?php lang('list'); echo"&nbsp;"; lang('bannads'); ?></a>
               &nbsp;
              <a class="button padded" href="<?php url_site();  ?>/b_code" >&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
            </p>
            <!-- /banner DECORATION TEXT -->

            <!-- PERCENTAGE DIFF -->
            <div class="percentage-diff">
              <!-- PERCENTAGE DIFF ICON WRAP -->

              <!-- /PERCENTAGE DIFF ICON WRAP -->

              <!-- PERCENTAGE DIFF TEXT -->

              <!-- /PERCENTAGE DIFF TEXT -->
             </div>
            <!-- /PERCENTAGE DIFF -->
          </div>
          <!-- /banner DECORATION -->
          <!-- link DECORATION -->
          <div class="stats-decoration v2 big secondary" id="link" style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/06-big.png) repeat-x bottom ;" >
            <!-- link DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('textads'); ?></p>
            <!-- /link DECORATION TITLE -->

            <!-- link DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle">
            <?php echo $lang['you_have']."&nbsp;"; user_row('nlink'); echo "&nbsp;".$lang['ptcyta']; ?>&nbsp;
            <?php echo $lang['your']."&nbsp;"; vu_state_row('link','clik'); echo "&nbsp;".$lang['Clik_ads']; ?>
            </p>
            <!-- /link DECORATION SUBTITLE -->

            <!-- link DECORATION TEXT -->
            <p class="stats-decoration-text">
              <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
              <a href="https://www.adstn.gq/kb/myads:Text Ads" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
               &nbsp;
              <?php } ?>
              <a class="button tertiary padded" href="<?php url_site();  ?>/state?ty=link&st=vu" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
               &nbsp;
              <a  href="<?php url_site();  ?>/l_list.php" class="button secondary padded" ><?php lang('list'); echo"&nbsp;"; lang('textads'); ?></a>
               &nbsp;
              <a class="button padded" href="<?php url_site();  ?>/l_code" >&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
            </p>
            <!-- /link DECORATION TEXT -->

            <!-- PERCENTAGE DIFF -->
            <div class="percentage-diff">
              <!-- PERCENTAGE DIFF ICON WRAP -->

              <!-- /PERCENTAGE DIFF ICON WRAP -->

              <!-- PERCENTAGE DIFF TEXT -->

              <!-- /PERCENTAGE DIFF TEXT -->
             </div>
            <!-- /PERCENTAGE DIFF -->
          </div>
          <!-- /link DECORATION -->
          <!-- Exchange DECORATION -->
          <div class="stats-decoration v2 big secondary" id="Exchange" style="background: url(<?php url_site();  ?>/templates/_panel/img/graph/stat/07.png) repeat-x bottom ;" >
            <!-- Exchange DECORATION TITLE -->
            <p class="stats-decoration-title"><?php lang('exvisit'); ?></p>
            <!-- /Exchange DECORATION TITLE -->

            <!-- Exchange DECORATION SUBTITLE -->
            <p class="stats-decoration-subtitle">
            <?php echo $lang['you_have']."&nbsp;"; user_row('vu'); echo "&nbsp;".$lang['ptvysa']; ?>&nbsp;
            <?php echo $lang['yshbv']."&nbsp;:&nbsp;"; vu_state_row('visits','vu');  ?>
            </p>
            <!-- /Exchange DECORATION SUBTITLE -->

            <!-- Exchange DECORATION TEXT -->
            <p class="stats-decoration-text">
              <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
              <a href="https://www.adstn.gq/kb/myads:Exchange" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
               &nbsp;
              <?php } ?>
              <a  href="<?php url_site();  ?>/v_list" class="button secondary padded" ><?php lang('list'); echo"&nbsp;"; lang('exvisit'); ?></a>
               &nbsp;
              <a class="button padded" onClick="window.open('visits.php?id=<?php user_row('id') ; ?>');" href="javascript:void(0);" >
              <i class="fa fa-exchange nav_icon"></i>&nbsp;<?php lang('exvisit'); ?>
              </a>
            </p>
            <!-- /Exchange DECORATION TEXT -->

            <!-- PERCENTAGE DIFF -->
            <div class="percentage-diff">
              <!-- PERCENTAGE DIFF ICON WRAP -->

              <!-- /PERCENTAGE DIFF ICON WRAP -->

              <!-- PERCENTAGE DIFF TEXT -->

              <!-- /PERCENTAGE DIFF TEXT -->
             </div>
            <!-- /PERCENTAGE DIFF -->
          </div>
          <!-- /Exchange DECORATION -->
          <?php ads_site(2); ?>
          <!-- pts DECORATION -->
          <div class="widget-box" id="pts" style="background: url(<?php url_site();  ?>/templates/_panel/img/ad_pattern.png) repeat <?php if($c_lang=="ar"){  ?> direction: rtl; <?php } ?> " >
            <!-- pts DECORATION TITLE -->
            <p class="widget-box-title"><?php lang('Totalpoints'); ?> <?php user_row('pts'); ?> PTS.</p>
            <!-- /pts DECORATION TITLE -->
            <div class="widget-box-content">

            <!-- pts DECORATION SUBTITLE -->
            <p class="switch-option-title">
            <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
            <a href="https://www.adstn.gq/kb/myads:pts" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
            <?php } ?>
            <a class="button padded" href="<?php url_site();  ?>/r_code" ><i class="fa fa-users"></i>&nbsp;<?php lang('referal'); ?></a>

            </p>
            <!-- /pts DECORATION SUBTITLE -->
             <hr />
             <p class="switch-option-title">
             <b><?php lang('Convertpoint'); ?></b>
             </p>
             <br />
            <!-- pts DECORATION TEXT -->
            <div class="switch-option">
            <form action="home.php" method="POST">
                <div class="form-row split">
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input social-input small active">
                      <!-- SOCIAL LINK -->
                      <div class="social-link no-hover twitch">
                        <!-- ICON TWITCH -->
                        <svg class="icon-twitch">
                          <use xlink:href="#svg-item"></use>
                        </svg>
                        <!-- /ICON TWITCH -->
                      </div>
                      <!-- /SOCIAL LINK -->

                      <label for="social-account-twitch"><?php lang('Points'); ?></label>
                      <input type="text" id="Points" name="pts" >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <div class="form-select">
                      <label for="profile-social-stream-schedule-monday"><?php lang('to'); ?></label>
                      <select id="profile-social-stream-schedule-monday" name="to">
                        <option value="link" ><?php lang('tostads'); ?></option>
                        <option value="banners"  ><?php lang('towthbaner'); ?></option>
                        <option value="exchv" ><?php lang('toexchvisi'); ?></option>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                </div>
                <div class="form-row split">
                <button type="submit" class="button tertiary padded" name="bt_pts" value="bt_pts" ><?php lang('Conversion'); ?></button>
                </div>
            </form>
            </div>
            <!-- /pts DECORATION TEXT -->
             </div>
            <!-- /PERCENTAGE DIFF -->
          </div>
          <!-- /pts DECORATION -->
          <?php ads_site(2); ?>
 </div>
<?php }else{ echo"404"; }  ?>