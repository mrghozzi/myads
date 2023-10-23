<?php if($s_st=="buyfgeufb"){ 
    $stausrff = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` = :o_order ");
    $stausrff->bindParam(":o_order", $catussen['id']);
    $stausrff->execute();
    $usrff=$stausrff->fetch(PDO::FETCH_ASSOC);
     if($usrff['o_mode']=="0"){
        $us_cover = $url_site."/upload/cover.jpg";
      }else{
        $us_cover = $url_site."/".$usrff['o_mode'];
      } 
      $fluszff = $db_con->prepare("SELECT *  FROM `like` WHERE uid=:u_id AND sid=:s_id AND type=1 ");
      $fluszff->bindParam(":u_id", $my_user_1);
      $fluszff->bindParam(":s_id", $usrff['o_order']);
      $fluszff->execute();
      if($flsusff=$fluszff->fetch(PDO::FETCH_ASSOC)){
       $followff = "<a class=\"profile-header-info-action button tertiary\" href=\"{$url_site}/requests/follow.php?unfollow={$catussen['id']}\" ><svg class=\"button-icon icon-add-friend\"><use xlink:href=\"#svg-remove-friend\"></use></svg></a>";
        }else{
       $followff = "<a class=\"profile-header-info-action button secondary\" href=\"{$url_site}/requests/follow.php?follow={$catussen['id']}\" style=\"color: #fff;\" ><svg class=\"button-icon icon-add-friend\"><use xlink:href=\"#svg-add-friend\"></use></svg></a>";
        }      
    ?>
    <div class="user-preview landscape">
          <!-- USER PREVIEW COVER -->
          <figure class="user-preview-cover liquid" style="background: url(<?php echo $us_cover;  ?>) center center / cover no-repeat;">
            <img src="<?php echo $us_cover;  ?>" alt="cover-04" style="display: none;">
          </figure>
          <!-- /USER PREVIEW COVER -->
      
          <!-- USER PREVIEW INFO -->
          <div class="user-preview-info">
            <!-- USER SHORT DESCRIPTION -->
            <div class="user-short-description landscape tiny">
              <!-- USER SHORT DESCRIPTION AVATAR -->
              <a class="user-short-description-avatar user-avatar small <?php online_us($catussen['id']); ?> " href="<?php echo "{$url_site}/u/{$catussen['id']}"; ?>">
                <!-- USER AVATAR BORDER -->
                <div class="user-avatar-border">
                  <!-- HEXAGON -->
                  <div class="hexagon-50-56" style="width: 50px; height: 56px; position: relative;"><canvas width="50" height="56" ></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR BORDER -->
            
                <!-- USER AVATAR CONTENT -->
                <div class="user-avatar-content">
                  <!-- HEXAGON -->
                  <div class="hexagon-image-30-32" data-src="<?php echo "{$url_site}/{$catussen['img']}"; ?>" ><canvas width="30" height="32" ></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR CONTENT -->
                <div class="user-avatar-progress-border">
                  <!-- HEXAGON -->
                  <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"><canvas width="40" height="44" style="position: absolute; top: 0px; left: 0px;"></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- USER AVATAR PROGRESS BORDER -->
<?php
         if(check_us($catussen['id'],1)==1){
            echo   " <!-- USER AVATAR BADGE -->
                      <div class=\"user-avatar-badge\">
                        <!-- USER AVATAR BADGE BORDER -->
                        <div class=\"user-avatar-badge-border\">
                         <!-- HEXAGON -->
                         <div class=\"hexagon-22-24\" ></div>
                         <!-- /HEXAGON -->
                        </div>
                       <!-- /USER AVATAR BADGE BORDER -->
                               
                       <!-- USER AVATAR BADGE CONTENT -->
                        <div class=\"user-avatar-badge-content\">
                         <!-- HEXAGON -->
                         <div class=\"hexagon-dark-16-18\" ></div>
                         <!-- /HEXAGON -->
                        </div>
                       <!-- /USER AVATAR BADGE CONTENT -->
                               
                       <!-- USER AVATAR BADGE TEXT -->
                        <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\" ></i></p>
                       <!-- /USER AVATAR BADGE TEXT -->
                      </div>
                       <!-- /USER AVATAR BADGE -->       ";
     } ?>                                                             

              </a>
              <!-- /USER SHORT DESCRIPTION AVATAR -->
        
              <!-- USER SHORT DESCRIPTION TITLE -->
              <p class="user-short-description-title"><a href="<?php echo "{$url_site}/u/{$catussen['id']}"; ?>"><?php echo "{$catussen['username']}"; ?></a></p>
              <!-- /USER SHORT DESCRIPTION TITLE -->
        
              <!-- USER SHORT DESCRIPTION TEXT -->
              <p class="user-short-description-text"><?php echo $lang['lastcontact']."&nbsp;".convertTime($catussen['online']); ?></p>
              <!-- /USER SHORT DESCRIPTION TEXT -->
            </div>
            <!-- /USER SHORT DESCRIPTION -->
        
            <!-- BADGE LIST -->
            <div class="badge-list small">
              <!-- BADGE ITEM -->
              <?php echo $lang['ago']."&nbsp;".$time_cmt; ?>
              <!-- /BADGE ITEM -->
            </div>
            <!-- /BADGE LIST -->
      
            <!-- USER STATS -->
            <div class="user-stats">
              <!-- USER STAT -->
              <div class="user-stat">
                <!-- USER STAT TITLE -->
                <p class="user-stat-title"><?php nbr_posts($catussen['id']); ?></p>
                <!-- /USER STAT TITLE -->
        
                <!-- USER STAT TEXT -->
                <p class="user-stat-text"><?php lang('Posts'); ?></p>
                <!-- /USER STAT TEXT -->
              </div>
              <!-- /USER STAT -->
        
              <!-- USER STAT -->
              <div class="user-stat">
                <!-- USER STAT TITLE -->
                <p class="user-stat-title"><?php nbr_follow($catussen['id'],"sid"); ?></p>
                <!-- /USER STAT TITLE -->
        
                <!-- USER STAT TEXT -->
                <p class="user-stat-text"><?php lang('Followers'); ?></p>
                <!-- /USER STAT TEXT -->
              </div>
              <!-- /USER STAT -->
        
              <!-- USER STAT -->
              <div class="user-stat">
                <!-- USER STAT TITLE -->
                <p class="user-stat-title"><?php nbr_follow($catussen['id'],"uid"); ?></p>
                <!-- /USER STAT TITLE -->
        
                <!-- USER STAT TEXT -->
                <p class="user-stat-text"><?php lang('Following'); ?></p>
                <!-- /USER STAT TEXT -->
              </div>
              <!-- /USER STAT -->
            </div>
            <!-- /USER STATS -->

            <!-- SOCIAL LINKS -->
            <div class="social-links small">
            <?php if(isset($_COOKIE['user']) AND isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin)  ){  ?>
          <!-- PROFILE HEADER INFO ACTION -->
          <a class="social-link patreon" href="<?php url_site();  ?>/admincp?us_edit=<?php echo $catussen['id']; ?>" style="color: #fff;">
          <i class="fa fa-edit" aria-hidden="true"></i>
          </a>
          <!-- /PROFILE HEADER INFO ACTION -->
          <?php } ?>
            </div>
            <!-- /SOCIAL LINKS -->
            <?php if($my_user_1 != $usrff['o_order']){?>
            <!-- USER PREVIEW ACTIONS -->
            <div class="user-preview-actions">
              <!-- BUTTON -->
              <?php echo $followff; ?>
              <!-- /BUTTON -->
              <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']!=$usrff['o_order'])){  ?>
              <!-- BUTTON -->
              <a class="profile-header-info-action button primary" href="<?php url_site();  ?>/message/<?php echo $catussen['id']; ?>">
                <!-- BUTTON ICON -->
                <svg class="button-icon icon-comment">
                  <use xlink:href="#svg-comment"></use>
                </svg>
                <!-- /BUTTON ICON -->
              </a>
              <?php } ?>
              <!-- /BUTTON -->
            </div>
            <?php } ?>
            <!-- /USER PREVIEW ACTIONS -->
          </div>
          <!-- /USER PREVIEW INFO -->
        </div>  
<?php }else{ echo"404"; }  ?>