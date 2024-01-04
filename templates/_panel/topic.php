<?php
if($s_st=="buyfgeufb"){ dinstall_d();
  global $susat; global $get_cat;
  $sutcat = $susat;
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$get_cat );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($sucat['id'])) {

$catdid=$sucat['id'];
$catdname=$sucat['name'];
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);

$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);

$ssust = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` =".$catuss['id'] );
$ssust->execute();
$srus=$ssust->fetch(PDO::FETCH_ASSOC);

$catusc = $db_con->prepare("SELECT *  FROM f_cat WHERE  id='{$sucat['cat']}'");
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);
$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE s_type IN (2) AND tp_id ='{$catdid}' " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);

$likeuscm = $db_con->prepare("SELECT  * FROM `like` WHERE uid='{$uRow['id']}' AND sid='{$catdid}' AND  type=2 " );
$likeuscm->execute();
$uslike=$likeuscm->fetch(PDO::FETCH_ASSOC);

if(isset($uslike) AND ($uslike['sid']==$catdid)){
$o_parent = $uslike['id'];
$o_type   = "data_reaction";
$likeuscmr = $db_con->prepare("SELECT  * FROM `options` WHERE o_order='{$uRow['id']}' AND o_parent='{$o_parent}' AND  o_type='{$o_type}' " );
$likeuscmr->execute();
$usliker=$likeuscmr->fetch(PDO::FETCH_ASSOC);
 if(isset($usliker)  AND ($usliker['o_parent']==$o_parent)){
$reaction_img  = "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/{$usliker['o_valuer']}.png\"  width=\"30\" alt=\"reaction-{$usliker['o_valuer']}\">";
$reaction_name = $usliker['o_valuer'];
     if($usliker['o_valuer']=="like"){         $reaction_color = "style=\"color: #1bc8db;\""; }
     else if($usliker['o_valuer']=="love"){    $reaction_color = "style=\"color: #fc1f3b;\""; }
     else if($usliker['o_valuer']=="dislike"){ $reaction_color = "style=\"color: #3f3cf8;\""; }
     else if($usliker['o_valuer']=="sad"){     $reaction_color = "style=\"color: #139dff;\""; }
     else if($usliker['o_valuer']=="angry"){   $reaction_color = "style=\"color: #fa690e;\""; }
     else if($usliker['o_valuer']=="happy"){   $reaction_color = "style=\"color: #ffda21;\""; }
     else if($usliker['o_valuer']=="funny"){   $reaction_color = "style=\"color: #ffda21;\""; }
     else if($usliker['o_valuer']=="wow"){     $reaction_color = "style=\"color: #ffda21;\""; }
     else {                                    $reaction_color = "style=\"color: #ffda21;\""; }
 }else{
$reaction_img   = "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/like.png\"  width=\"30\" alt=\"reaction-like\">";
$reaction_color = "style=\"color: #1bc8db;\"";
$reaction_name  = "like";
 }
}

$time_stt=convertTime($susat['date']);
$namesher =  "{$sucat['name']} - {$title_s}";
$namesher = strip_tags($namesher, '');
$linksher =  "{$url_site}/t{$catdid}";
$linksher = strip_tags($linksher, '');

$comtxt = strip_tags($sucat['txt'], '<p><a><b><br><li><ul><font><span><pre><u><s><img><iframe>');
$comtxt = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt );

 ?>
<style> .forum-content img { margin-top: 24px; width: 75%; height: auto;   border-radius: 12px; } </style>
  <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/discussion-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('forum'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
</div>
    <!-- /SECTION BANNER -->
    <?php ads_site(5); ?>
<div class="section-header">
      <!-- SECTION HEADER INFO -->
      <div class="section-header-info">
        <!-- SECTION TITLE -->
        <h2 class="section-title"><?php echo $sucat['name']; ?></h2>
        <!-- /SECTION TITLE -->
      </div>
      <!-- /SECTION HEADER INFO -->
</div>
<div class="section-filters-bar v7">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions">
        <!-- SECTION FILTERS BAR INFO -->
        <div class="section-filters-bar-info">
          <!-- SECTION FILTERS BAR TITLE -->
          <p class="section-filters-bar-title">
            <a href="<?php url_site();  ?>/forum"><?php lang('forum'); ?></a>
            <span class="separator"></span><a href="<?php url_site();  ?>/f<?php echo $catussc['id']; ?>"><i class="fa <?php echo $catussc['icons']; ?>" aria-hidden="true"></i>&nbsp;<?php echo $catussc['name']; ?></a>
            <span class="separator"></span><a href="<?php url_site();  ?>/t<?php echo $sucat['id']; ?>"><?php echo $sucat['name']; ?></a>
          </p>
          <!-- /SECTION FILTERS BAR TITLE -->

          <!-- SECTION FILTERS BAR TEXT -->
          <div class="section-filters-bar-text small-space">
            <?php echo $time_stt;  ?>
          </div>
          <!-- /SECTION FILTERS BAR TEXT -->
        </div>
        <!-- /SECTION FILTERS BAR INFO -->
      </div>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>
<div class="grid grid post<?php echo $sutcat['id']; ?>" >
 <div class="forum-content" >
    <div class="forum-post-header">
          <!-- FORUM POST HEADER TITLE -->
          <p class="forum-post-header-title"><?php echo $lang['author']; ?></p>
          <!-- /FORUM POST HEADER TITLE -->

          <!-- FORUM POST HEADER TITLE -->
          <p class="forum-post-header-title"><?php echo $lang['Posts']; ?></p>
          <!-- /FORUM POST HEADER TITLE -->
    </div>
    <div class="forum-post-list" >
      <div class="forum-post">
            <!-- FORUM POST META -->
            <div class="forum-post-meta">
              <!-- FORUM POST TIMESTAMP -->
              <p class="forum-post-timestamp"><?php echo date("Y-m-d H:i:s",$susat['date']);  ?></p>
              <!-- /FORUM POST TIMESTAMP -->

              <!-- FORUM POST ACTIONS -->
              <div class="forum-post-actions">
                <!-- FORUM POST ACTION -->
                <p class="forum-post-action">          <!-- WIDGET BOX SETTINGS -->
          <div class="widget-box-settings">
            <!-- POST SETTINGS WRAP -->
            <div class="post-settings-wrap" style="position: relative;">
              <!-- POST SETTINGS -->
              <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <!-- POST SETTINGS ICON -->
                <svg class="post-settings-icon icon-more-dots">
                  <use xlink:href="#svg-more-dots"></use>
                </svg>
                <!-- /POST SETTINGS ICON -->
              </div>
              <!-- /POST SETTINGS -->

              <!-- SIMPLE DROPDOWN -->
              <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
<?php if(((isset($uRow['id'])AND isset($sucat['uid']) AND ($uRow['id']==$sucat['uid'])) OR (isset($_COOKIE['admin'])  AND ($_COOKIE['admin']==$hachadmin) )) ){ ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <a class="simple-dropdown-link" href="<?php echo $url_site; ?>/editor/<?php echo $sucat['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;<?php echo $lang['edit']; ?></a>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
<?php if(((isset($uRow['id']) AND ($uRow['id']==$sucat['uid'])) OR (isset($uRow['id']) AND ($uRow['id']==$sutcat['uid'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']== $hachadmin)))){ ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_delete<?php echo $sutcat['id']; ?>" ><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;<?php echo $lang['delete']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link author_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?> <?php echo $lang['author']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
              </div>
              <!-- /SIMPLE DROPDOWN -->
            </div>
            <!-- /POST SETTINGS WRAP -->
          </div>
          <!-- /WIDGET BOX SETTINGS --></p>
                <!-- /FORUM POST ACTION -->
              </div>
              <!-- /FORUM POST ACTIONS -->
            </div>
            <!-- /FORUM POST META -->

            <!-- FORUM POST CONTENT -->
            <div class="forum-post-content">
              <!-- FORUM POST USER -->
              <div class="forum-post-user">
                <!-- USER AVATAR -->
                <a class="user-avatar no-outline <?php online_us($catuss['id']); ?>" href="<?php url_site();  ?>/u/<?php echo $srus['o_valuer']; ?>">
                  <!-- USER AVATAR CONTENT -->
                  <div class="user-avatar-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-image-68-74" data-src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR CONTENT -->

                  <!-- USER AVATAR PROGRESS BORDER -->
                  <div class="user-avatar-progress-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR PROGRESS BORDER -->
<?php
if(check_us($catuss['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-28-32\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-22-24\" style=\"width: 16px; height: 18px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE CONTENT -->

                              <!-- USER AVATAR BADGE TEXT -->
                              <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\" ></i></p>
                              <!-- /USER AVATAR BADGE TEXT -->
                            </div>
                            <!-- /USER AVATAR BADGE -->       ";
                              }
?>
                 </a>
                <!-- /USER AVATAR -->

                <!-- USER AVATAR -->
                <a class="user-avatar small no-outline <?php online_us($catuss['id']); ?>" href="<?php url_site();  ?>/u/<?php echo $srus['o_valuer']; ?>">
                  <!-- USER AVATAR CONTENT -->
                  <div class="user-avatar-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-image-30-32" data-src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" style="width: 30px; height: 32px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas></div>
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
<?php
                                              if(check_us($catuss['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-22-24\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-16-18\" style=\"width: 16px; height: 18px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE CONTENT -->

                              <!-- USER AVATAR BADGE TEXT -->
                              <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\" ></i></p>
                              <!-- /USER AVATAR BADGE TEXT -->
                            </div>
                            <!-- /USER AVATAR BADGE -->       ";
                              }
?>
                  </a>
                <!-- /USER AVATAR -->

                <!-- FORUM POST USER TITLE -->
                <p class="forum-post-user-title"><a href="<?php url_site();  ?>/u/<?php echo $srus['o_valuer']; ?>"><?php echo $catuss['username']; ?></a></p>
                <!-- /FORUM POST USER TITLE -->

                <!-- FORUM POST USER TITLE -->
                <p class="forum-post-user-text"><a href="<?php url_site();  ?>/u/<?php echo $srus['o_valuer']; ?>">@<?php echo @urldecode($srus['o_valuer']); ?></a></p>
                <!-- /FORUM POST USER TITLE -->

              </div>
              <!-- /FORUM POST USER -->

              <!-- FORUM POST INFO -->
              <div class="forum-post-info">
                <!-- FORUM POST PARAGRAPH -->
                <div   id="post_form<?php echo $sucat['id']; ?>" ><div id="report<?php echo $sucat['id']; ?>" ></div></div>
                <p class="forum-post-paragraph">
                  <?php echo $comtxt; ?>
                </p>
                <!-- /FORUM POST PARAGRAPH -->
               </div>
              <!-- /FORUM POST INFO -->
            </div>
            <!-- /FORUM POST CONTENT -->
          </div>
          <!-- POST OPTIONS -->

    </div>
 </div>

</div>
<div class="post-options post<?php echo $sutcat['id']; ?>">
<?php   if(isset($_COOKIE['user'])){ ?>
            <!-- POST OPTION WRAP -->
            <div class="post-option-wrap" style="position: relative;">
              <!-- POST OPTION -->
              <div class="post-option reaction-options-dropdown-trigger">
              <div id="reaction_image<?php echo $sutcat['id']; ?>" >
<?php if($uslike['sid']==$catdid){  ?>
                <?php echo $reaction_img;  ?>
<?php }else{ ?>
              <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-thumbs-up">
                  <use xlink:href="#svg-thumbs-up" ></use>
                </svg>
                <!-- /POST OPTION ICON -->
<?php } ?>
                </div>
                <!-- POST OPTION TEXT -->
                <p class="post-option-text reaction_txt<?php echo $sutcat['id']; ?>" <?php if($uslike['sid']==$catdid){ echo $reaction_color; } ?> >
                &nbsp;<?php if($uslike['sid']==$catdid){ echo $reaction_name; }else{ echo $lang['react']; } ?></p>
                <!-- /POST OPTION TEXT -->
              </div>
              <!-- /POST OPTION -->

              <!-- REACTION OPTIONS -->
              <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="like" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/like.png" alt="reaction-like">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -22px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Like</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="love" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/love.png" alt="reaction-love">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -23.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Love</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="dislike" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/dislike.png" alt="reaction-dislike">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -28px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Dislike</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="happy" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/happy.png" alt="reaction-happy">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -27.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Happy</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="funny" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/funny.png" alt="reaction-funny">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -27px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Funny</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="wow" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/wow.png" alt="reaction-wow">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -24px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Wow</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="angry" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/angry.png" alt="reaction-angry">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -26.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Angry</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_2_<?php echo $sucat['id']; ?>" data-title="sad" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/sad.png" alt="reaction-sad">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -21.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Sad</p></div></div>
                <!-- /REACTION OPTION -->
              </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION WRAP -->
<?php } ?>
<?php   if(isset($_COOKIE['user'])){ ?>
            <!-- POST OPTION -->
            <div  class="post-option sh_comment_t<?php echo $sutcat['id']; ?>">
              <!-- POST OPTION ICON -->
              <svg class="post-option-icon icon-comment">
                <use xlink:href="#svg-comment"></use>
              </svg>
              <!-- /POST OPTION ICON -->

              <!-- POST OPTION TEXT -->
              <p class="post-option-text"><?php echo $lang['comment']; ?></p>
              <!-- /POST OPTION TEXT -->
            </div>
            <!-- /POST OPTION -->
<?php } ?>
            <!-- POST OPTION -->
            <div class="post-option-wrap" style="position: relative;">
              <!-- POST OPTION -->
              <div class="post-option reaction-options-dropdown-trigger">
                <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-share">
                  <use xlink:href="#svg-share"></use>
                </svg>
                <!-- /POST OPTION ICON -->

                <!-- POST OPTION TEXT -->
                <p class="post-option-text"><?php echo $lang['share']; ?></p>
                <!-- /POST OPTION TEXT -->
              </div>
              <!-- /POST OPTION -->

              <!-- REACTION OPTIONS -->
              <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="facebook" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/facebook-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="twitter" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://twitter.com/intent/tweet?text=<?php echo $namesher; ?>&url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/twitter-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="linkedin" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/linkedin-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="wasp" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.wasp.gq/sharer?url=<?php echo $linksher; ?>&nbsp;<?php echo $namesher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/wasp-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->


               </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION -->
          </div>
<div class="post-comment-list comment_2_<?php echo $sucat['id']; ?> post<?php echo $sutcat['id']; ?>" ></div>
<script>
$(".comment_2_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=2&tid=<?php echo $sucat['id']; ?>');
$(".sh_comment_t<?php echo $susat['id']; ?>").addClass('active');
</script>
<script>
$('.post_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=2&tid=<?php echo $sucat['id']; ?>');
        });
</script>
<script>
$('.author_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=2&tid=<?php echo $sucat['id']; ?>&a_type=99');
        });
</script>
<script>
$('.post_delete<?php echo $sutcat['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_delete.php?sid=<?php echo $sutcat['id']; ?>&dc=1');
        });
</script>
<script>
     $("document").ready(function() {
   $(".reaction_2_<?php echo $sucat['id']; ?>").click(postreaction<?php echo $sutcat['id']; ?>);

});
function postreaction<?php echo $sutcat['id']; ?>(){
    var data_reaction = $(this).attr("data-title");
   $.ajax({
type: "POST",
url: "<?php url_site();  ?>/requests/f_like.php?id=<?php echo $sucat['id']; ?>&f_like=like_up&t=f",
data: "data_reaction=" + data_reaction,
success: function (response) {
// This code will run after the Ajax is successful
$("#reaction_image<?php echo $sutcat['id']; ?>").html(response);
$(".reaction_txt<?php echo $sutcat['id']; ?>").html("");

}
})
}
</script>
<?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>
