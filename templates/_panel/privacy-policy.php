<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
<style> body{  background-image: url(<?php url_site();  ?>/templates/_panel/img/background.webp); } </style>
<div class="grid grid-12" >
  <div class="grid-column" >
    <div class="widget-box" >
    <div class="calendar-events-preview small">
              <!-- PREVIEW TITLE -->
              <p class="calendar-events-preview-title"><?php title_site('Privacy Policy'); ?></p>
              <!-- /CPREVIEW TITLE -->

              <!-- PREVIEW LIST -->
              <div class="calendar-event-preview-list">
                <!-- PREVIEW -->
                <div class="calendar-event-preview small primary">
                  <!-- PREVIEW INFO -->
                  <div class="calendar-event-preview-info">
                    <!-- PREVIEW TITLE -->
                    <p class="calendar-event-preview-title">Privacy Policy</p>
                    <!-- /PREVIEW TITLE -->

                    <!-- PREVIEW TEXT -->
                    <p class="calendar-event-preview-text">In common with other websites, log files are stored on the web server saving details such as the visitor's IP address, browser type, referring page and time of visit.</p>
                    <p class="calendar-event-preview-text">Cookies may be used to remember visitor preferences when interacting with the website.</p>
                    <p class="calendar-event-preview-text">Where registration is required, the visitor's email and a username will be stored on the server.</p>
                    <!-- /PREVIEW TEXT -->
                  </div>
                  <!-- /CPREVIEW INFO -->
                </div>
                <!-- /PREVIEW -->
                <!-- PREVIEW -->
                <div class="calendar-event-preview small primary">
                  <!-- PREVIEW INFO -->
                  <div class="calendar-event-preview-info">
                    <!-- PREVIEW TITLE -->
                    <p class="calendar-event-preview-title">How the Information is used</p>
                    <!-- /PREVIEW TITLE -->

                    <!-- PREVIEW TEXT -->
                    <p class="calendar-event-preview-text">The information is used to enhance the vistor's experience when using the website to display personalised content and possibly advertising.</p>
                    <p class="calendar-event-preview-text">E-mail addresses will not be sold, rented or leased to 3rd parties.</p>
                    <p class="calendar-event-preview-text">E-mail may be sent to inform you of news of our services or offers by us or our affiliates.</p>
                    <!-- /PREVIEW TEXT -->
                  </div>
                  <!-- /PREVIEW INFO -->
                </div>
                <!-- /PREVIEW -->
                <!-- REVIEW -->
                <div class="calendar-event-preview small primary">
                  <!-- PREVIEW INFO -->
                  <div class="calendar-event-preview-info">
                    <!-- PREVIEW TITLE -->
                    <p class="calendar-event-preview-title">Visitor Options</p>
                    <!-- /PREVIEW TITLE -->

                    <!-- PREVIEW TEXT -->
                    <p class="calendar-event-preview-text">If you have subscribed to one of our services, you may unsubscribe by following the instructions which are included in e-mail that you receive.</p>
                    <p class="calendar-event-preview-text">You may be able to block cookies via your browser settings but this may prevent you from access to certain features of the website.</p>
                    <!-- /PREVIEW TEXT -->
                  </div>
                  <!-- /PREVIEW INFO -->
                </div>
                <!-- /PREVIEW -->
                <!-- PREVIEW -->
                <div class="calendar-event-preview small primary">
                  <!-- PREVIEW INFO -->
                  <div class="calendar-event-preview-info">
                    <!-- PREVIEW TITLE -->
                    <p class="calendar-event-preview-title">Cookies</p>
                    <!-- /PREVIEW TITLE -->

                    <!-- PREVIEW TEXT -->
                    <p class="calendar-event-preview-text">Cookies are small digital signature files that are stored by your web browser that allow your preferences to be recorded when visiting the website. Also they may be used to track your return visits to the website.</p>
                    <p class="calendar-event-preview-text">3rd party advertising companies may also use cookies for tracking purposes.</p>
                    <!-- /PREVIEW TEXT -->
                  </div>
                  <!-- /PREVIEW INFO -->
                </div>
                <!-- /PREVIEW -->
              </div>
              <!-- /PREVIEW LIST -->
        </div>
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