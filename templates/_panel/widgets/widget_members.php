<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ ?>
<div class="widget-box">
          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><?php echo $abwidgets['name']; ?></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
           <div class="user-status-list" >
           <?php  if(isset($_COOKIE['user'])){
                  $s_usid =$_COOKIE['user'];
                }else{
                  $s_usid = 0;
                }

     $stt = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM users where NOT ( id IN( SELECT sid FROM `like` WHERE uid={$s_usid} AND type=1 ) ) AND NOT (id={$s_usid}) ORDER BY m LIMIT 5 " );
     $stt->execute();
     while($usb=$stt->fetch(PDO::FETCH_ASSOC)){
      $str_username = mb_strlen($usb['username'], 'utf8');
      if($str_username > 15){
      $username = substr($usb['username'],0,15)."&nbsp;...";
       }else{
      $username = $usb['username'];
       }
      ?>
	  <div class="user-status request-small">
                <!-- USER STATUS AVATAR -->
                <a class="user-status-avatar" href="<?php url_site();  ?>/u/<?php echo $usb['id']; ?>">
                  <!-- USER AVATAR -->
                  <div class="user-avatar small no-outline <?php online_us($usb['id']); ?> ">
                    <!-- USER AVATAR CONTENT -->
                    <div class="user-avatar-content">
                      <!-- HEXAGON -->
                      <div class="hexagon-image-30-32" data-src="<?php url_site();  ?>/<?php echo $usb['img']; ?>" style="width: 30px; height: 32px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR CONTENT -->

                   <!-- USER AVATAR PROGRESS BORDER -->
                    <div class="user-avatar-progress-border">
                      <!-- HEXAGON -->
                      <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR PROGRESS BORDER -->
                    <?php if(check_us($usb['id'],1)==1){  ?>
                    <!-- USER AVATAR BADGE -->
                    <div class="user-avatar-badge">
                      <!-- USER AVATAR BADGE BORDER -->
                      <div class="user-avatar-badge-border">
                        <!-- HEXAGON -->
                        <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas></div>
                        <!-- /HEXAGON -->
                      </div>
                      <!-- /USER AVATAR BADGE BORDER -->

                      <!-- USER AVATAR BADGE CONTENT -->
                      <div class="user-avatar-badge-content">
                        <!-- HEXAGON -->
                        <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas></div>
                        <!-- /HEXAGON -->
                      </div>
                      <!-- /USER AVATAR BADGE CONTENT -->

                      <!-- USER AVATAR BADGE TEXT -->
                      <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check" ></i></p>
                      <!-- /USER AVATAR BADGE TEXT -->
                    </div>
                    <!-- /USER AVATAR BADGE -->
                    <?php } ?>
                  </div>
                  <!-- /USER AVATAR -->
                </a>
                <!-- /USER STATUS AVATAR -->

                <!-- USER STATUS TITLE -->
                <p class="user-status-title"><a class="bold" href="<?php url_site();  ?>/u/<?php echo $usb['id']; ?>"><?php echo $username; ?></a></p>
                <!-- /USER STATUS TITLE -->

                <!-- USER STATUS TEXT -->
                <p class="user-status-text small"><?php lang('Followers'); ?>&nbsp;<?php nbr_follow($usb['id'],"sid"); ?>&nbsp;|&nbsp;<?php lang('Following'); ?>&nbsp;<?php nbr_follow($usb['id'],"uid"); ?>&nbsp;|&nbsp;<?php lang('Posts'); ?>&nbsp;<?php nbr_posts($usb['id']); ?></p>
                <!-- /USER STATUS TEXT -->

                <!-- ACTION REQUEST LIST -->
                <div class="action-request-list">
                  <!-- ACTION REQUEST -->
                  <a class="action-request accept" href="<?php url_site();  ?>/requests/follow.php?follow=<?php echo $usb['id']; ?>">
                    <!-- ACTION REQUEST ICON -->
                    <svg class="action-request-icon icon-add-friend">
                      <use xlink:href="#svg-add-friend"></use>
                    </svg>
                    <!-- /ACTION REQUEST ICON -->
                  </a>
                  <!-- /ACTION REQUEST -->
                </div>
                <!-- ACTION REQUEST LIST -->
              </div>
     <?php } ?>
           </div>
          </div>
          <!-- /WIDGET BOX CONTENT -->
        </div>
<?php } ?>
