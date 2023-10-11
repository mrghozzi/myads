<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
<style> body{  background-image: url(<?php url_site();  ?>/templates/_panel/img/background.webp); } </style>
<div class="grid grid-12" >
  <div class="grid-column" >
    <div class="widget-box" >
    <center><h1><?php title_site(''); ?></h1></center>
    <hr />
    <center><p class="progress-arc-summary-text"><?php  descr_site(); ?></p></center>
    </div>
    <div class="gird" >
     <div class="grid grid-2-2-2-2-2-2 centered">
        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-active-users">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-photos">
              <use xlink:href="#svg-photos"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('banner'); ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('bannads'); ?></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

          <!-- ACCOUNT STAT BOX TEXT -->
          <p class="account-stat-box-text"><?php lang('Views'); ?> : <?php admin_state('banner'); ?></p>
          <!-- /ACCOUNT STAT BOX TEXT -->
        </div>
        <!-- /ACCOUNT STAT BOX -->

        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-visits">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-events-weekly">
              <use xlink:href="#svg-events-weekly"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('link'); ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('textads'); ?></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

          <!-- ACCOUNT STAT BOX TEXT -->
          <p class="account-stat-box-text"><?php lang('Views'); ?> : <?php admin_state('link'); ?></p>
          <!-- /ACCOUNT STAT BOX TEXT -->
        </div>
        <!-- /ACCOUNT STAT BOX -->

        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-session-duration">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-return">
              <use xlink:href="#svg-return"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('visits');  ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('exvisit'); ?></p>
          <p class="account-stat-box-text"><br /></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

        </div>
        <!-- /ACCOUNT STAT BOX -->
        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-active-users">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-members">
              <use xlink:href="#svg-members"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('users');  ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('users'); ?></p>
          <p class="account-stat-box-text"><br /></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

        </div>
        <!-- /ACCOUNT STAT BOX -->

        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-visits">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-forums">
              <use xlink:href="#svg-forums"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('forum');  ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('topics'); ?></p>
          <p class="account-stat-box-text"><br /></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

        </div>
        <!-- /ACCOUNT STAT BOX -->

        <!-- ACCOUNT STAT BOX -->
        <div class="account-stat-box account-stat-session-duration">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-list-grid-view">
              <use xlink:href="#svg-list-grid-view"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

          <!-- ACCOUNT STAT BOX TITLE -->
          <p class="account-stat-box-title"><?php nbr_state('directory');  ?></p>
          <!-- /ACCOUNT STAT BOX TITLE -->

          <!-- ACCOUNT STAT BOX SUBTITLE -->
          <p class="account-stat-box-subtitle"><?php lang('directory'); ?></p>
          <p class="account-stat-box-text"><br /></p>
          <!-- /ACCOUNT STAT BOX SUBTITLE -->

        </div>
        <!-- /ACCOUNT STAT BOX -->
      </div>
    </div>
    <div class="widget-box">
    <center><h1><?php lang('ads'); ?></h1></center>
    <hr>
    <center><?php ads_site(1);  ?></center>
    </div>
    <div class="widget-box" >
          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><?php lang('iysstat'); ?></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
            <!-- TIMELINE INFORMATION LIST -->
            <div class="timeline-information-list">
              <!-- TIMELINE INFORMATION -->
              <div class="timeline-information">
                <!-- TIMELINE INFORMATION TITLE -->
                <p class="timeline-information-title">مميزات التبادل الإعلاني لدينا</p>
                <!-- /TIMELINE INFORMATION TITLE -->

                <!-- TIMELINE INFORMATION DATE -->
                <p class="timeline-information-date">سهولة التسجيل والاضافة والتعديل على الإعلانات.</p>
                <!-- /TIMELINE INFORMATION DATE -->

                <!-- TIMELINE INFORMATION TEXT -->
                <p class="timeline-information-text">إعلانات آمنة خالية من المواقع المضرة والصفحات المنبثقة لتبادل اعلاني آمن.</p>
                <p class="timeline-information-text">بكل وضوح وشفافية يمكنك معاينة كافة إعلانات المشتركين ومواقعهم في أي وقت لمعرفة اين ستظهر اعلاناتك ونوعية الإعلانات التي ستظهر في موقعك.</p>
                <p class="timeline-information-text">نظام فلتر أي بي والذي يضمن عدم وجود ضغطات وهمية لتبادل اعلاني حقيقي.</p>
                <!-- /TIMELINE INFORMATION TEXT -->
              </div>
              <!-- /TIMELINE INFORMATION -->

              <!-- TIMELINE INFORMATION -->
              <div class="timeline-information">
                <!-- TIMELINE INFORMATION TITLE -->
                <p class="timeline-information-title"><?php lang('bannads'); ?></p>
                <!-- /TIMELINE INFORMATION TITLE -->

                <!-- TIMELINE INFORMATION TEXT -->
                <p class="timeline-information-text"><?php lang('pboysit'); ?></p>
                <!-- /TIMELINE INFORMATION TEXT -->
              </div>
              <!-- /TIMELINE INFORMATION -->

              <!-- TIMELINE INFORMATION -->
              <div class="timeline-information">
                <!-- TIMELINE INFORMATION TITLE -->
                <p class="timeline-information-title"><?php lang('textads'); ?></p>
                <!-- /TIMELINE INFORMATION TITLE -->

                <!-- TIMELINE INFORMATION TEXT -->
                <p class="timeline-information-text"><?php lang('ptaoyws'); ?></p>
                <!-- /TIMELINE INFORMATION TEXT -->
              </div>
              <!-- /TIMELINE INFORMATION -->

              <!-- TIMELINE INFORMATION -->
              <div class="timeline-information">
                <!-- TIMELINE INFORMATION TITLE -->
                <p class="timeline-information-title"><?php lang('exvisit'); ?></p>
                <!-- /TIMELINE INFORMATION TITLE -->

                <!-- TIMELINE INFORMATION TEXT -->
                <p class="timeline-information-text"><?php lang('ryrialx'); ?></p>
                <!-- /TIMELINE INFORMATION TEXT -->
              </div>
              <!-- /TIMELINE INFORMATION -->
            </div>
            <!-- /TIMELINE INFORMATION LIST -->
          </div>
          <!-- /WIDGET BOX CONTENT -->
        </div>
    <div class="widget-box">
    <center><h1><?php lang('ads'); ?></h1></center>
    <hr>
    <center><script language="javascript" src="<?php url_site();  ?>/bn.php?ID=1&px=responsive"></script></center>
    </div>
    <div class="widget-box">
    <center>
    <?php echo "All rights reserved &nbsp;&copy;".date("Y")."&nbsp;"; title_site(''); ?>&trade;
    | <a href="<?php url_site();  ?>/privacy-policy">PRIVACY POLICY</a>
    | `MyAds v<?php myads_version();  ?>`  Devlope by <a href="http://www.krhost.ga/">Kariya Host</a>
    </center>
    </div>
  </div>
</div>
<?php }else{ echo"404"; }  ?>