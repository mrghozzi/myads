<?php if(isset($s_st)=="buyfgeufb"){ dinstall_d();
if((int)isset($_GET['fl'])){ $string = $_GET['fl']; }
if((int)isset($_GET['fg'])){ $string = $_GET['fg']; }
 
$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` = :o_order ");
$stausr->bindParam(":o_order", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if(isset($_COOKIE['user'])){ $my_user_1=$_COOKIE['user'];  }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
if($sus=$usz->fetch(PDO::FETCH_ASSOC)){
$flusz = $db_con->prepare("SELECT *  FROM `like` WHERE uid=:u_id AND sid=:s_id AND type=1 ");
$flusz->bindParam(":u_id", $my_user_1);
$flusz->bindParam(":s_id", $usrRow['o_order']);
$flusz->execute();
if($flsus=$flusz->fetch(PDO::FETCH_ASSOC)){
 $follow = "<a class=\"profile-header-info-action button tertiary\" href=\"{$url_site}/requests/follow.php?unfollow={$sus['id']}\" ><span class=\"hide-text-mobile\">unfollow</span>&nbsp;<i class=\"fa fa-user-times\" aria-hidden=\"true\"></i></a>";
  }else{
 $follow = "<a class=\"profile-header-info-action button secondary\" href=\"{$url_site}/requests/follow.php?follow={$sus['id']}\" style=\"color: #fff;\" ><span class=\"hide-text-mobile\">follow</span>&nbsp;<i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i></a>";
  }
  if($usrRow['o_mode']=="0"){
	$us_cover = $url_site."/upload/cover.jpg";
  }else{
	$us_cover = $url_site."/".$usrRow['o_mode'];
  }
 ?>
<div class="profile-header">
      <!-- PROFILE HEADER COVER -->
      <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url(<?php echo $us_cover;  ?>) no-repeat scroll center center / cover;">
        <img src="<?php echo $us_cover;  ?>" alt="cover-<?php echo $sus['username']; ?>" style="display: none;">
      </figure>
      <!-- /PROFILE HEADER COVER -->

      <!-- PROFILE HEADER INFO -->
      <div class="profile-header-info">
        <!-- USER SHORT DESCRIPTION -->
        <div class="user-short-description big">
          <!-- USER SHORT DESCRIPTION AVATAR -->
          <a class="user-short-description-avatar user-avatar big <?php online_us($sus['id']); ?> " href="<?php url_site();  ?>/u/<?php echo $usrRow['o_valuer']; ?>">
            <!-- USER AVATAR BORDER -->
            <div class="user-avatar-border">
              <!-- HEXAGON -->
              <div class="hexagon-148-164" style="width: 148px; height: 164px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="148" height="164"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR BORDER -->

            <!-- USER AVATAR CONTENT -->
            <div class="user-avatar-content">
              <!-- HEXAGON -->
              <div class="hexagon-image-100-110" data-src="<?php url_site();  ?>/<?php echo $sus['img']; ?>" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR CONTENT -->

            <!-- USER AVATAR PROGRESS BORDER -->
            <div class="user-avatar-progress-border">
              <!-- HEXAGON -->
              <div class="hexagon-border-124-136" style="width: 124px; height: 136px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="124" height="136"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR PROGRESS BORDER -->
<?php
if(check_us($sus['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-40-44\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-32-34\" style=\"width: 16px; height: 18px; position: relative;\"></div>
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
          <!-- /USER SHORT DESCRIPTION AVATAR -->

          <!-- USER SHORT DESCRIPTION AVATAR -->
          <a class="user-short-description-avatar user-short-description-avatar-mobile user-avatar medium <?php online_us($sus['id']); ?>" href="<?php url_site();  ?>/u/<?php echo $usrRow['o_valuer']; ?>">
            <!-- USER AVATAR BORDER -->
            <div class="user-avatar-border">
              <!-- HEXAGON -->
              <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="120" height="132"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR BORDER -->

            <!-- USER AVATAR CONTENT -->
            <div class="user-avatar-content">
              <!-- HEXAGON -->
              <div class="hexagon-image-82-90" data-src="<?php url_site();  ?>/<?php echo $sus['img']; ?>" style="width: 82px; height: 90px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="82" height="90"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR CONTENT -->

            <!-- USER AVATAR PROGRESS BORDER -->
            <div class="user-avatar-progress-border">
              <!-- HEXAGON -->
              <div class="hexagon-border-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
              <!-- /HEXAGON -->
            </div>
            <!-- /USER AVATAR PROGRESS BORDER -->
<?php
if(check_us($sus['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-32-34\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-26-28\" style=\"width: 16px; height: 18px; position: relative;\"></div>
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
          <!-- /USER SHORT DESCRIPTION AVATAR -->

          <!-- USER SHORT DESCRIPTION TITLE -->
          <p class="user-short-description-title"><a href="<?php url_site();  ?>/u/<?php echo $usrRow['o_valuer']; ?>"><?php echo $sus['username']; ?></a></p>
          <!-- /USER SHORT DESCRIPTION TITLE -->

          <!-- USER SHORT DESCRIPTION TEXT -->
          <p class="user-short-description-text"><?php echo $lang['lastcontact']."&nbsp;".convertTime($sus['online']); ?></p>
          <!-- /USER SHORT DESCRIPTION TEXT -->
        </div>
        <!-- /USER SHORT DESCRIPTION -->

        <!-- USER STATS -->
        <div class="user-stats">
          <!-- USER STAT -->
          <div class="user-stat big">
            <!-- USER STAT TITLE -->
            <p class="user-stat-title"><a href="<?php url_site();  ?>/followers/<?php echo $sus['id']; ?>"><?php nbr_follow($sus['id'],"sid"); ?></a></p>
            <!-- /USER STAT TITLE -->

            <!-- USER STAT TEXT -->
            <p class="user-stat-text"><?php lang('Followers'); ?></p>
            <!-- /USER STAT TEXT -->
          </div>
          <!-- /USER STAT -->

          <!-- USER STAT -->
          <div class="user-stat big">
            <!-- USER STAT TITLE -->
            <p class="user-stat-title"><a href="<?php url_site();  ?>/following/<?php echo $sus['id']; ?>"><?php nbr_follow($sus['id'],"uid"); ?></a></p>
            <!-- /USER STAT TITLE -->

            <!-- USER STAT TEXT -->
            <p class="user-stat-text"><?php lang('Following'); ?></p>
            <!-- /USER STAT TEXT -->
          </div>
          <!-- /USER STAT -->

          <!-- USER STAT -->
          <div class="user-stat big">
            <!-- USER STAT TITLE -->
            <p class="user-stat-title"><?php nbr_posts($sus['id']); ?></p>
            <!-- /USER STAT TITLE -->

            <!-- USER STAT TEXT -->
            <p class="user-stat-text"><?php lang('Posts'); ?></p>
            <!-- /USER STAT TEXT -->
          </div>
          <!-- /USER STAT -->
          <div class="user-stat big">
          <?php if(isset($_COOKIE['user']) AND isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin)  ){  ?>
          <!-- PROFILE HEADER INFO ACTION -->
          <a class="social-link patreon" href="<?php url_site();  ?>/admincp?us_edit=<?php echo $sus['id']; ?>" style="color: #fff;">
          <i class="fa fa-edit" aria-hidden="true"></i>
          </a>
          <!-- /PROFILE HEADER INFO ACTION -->
          <?php } ?>
          </div>
        </div>
        <!-- /USER STATS -->

        <!-- PROFILE HEADER INFO ACTIONS -->
        <div class="profile-header-info-actions">
        <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
          <!-- PROFILE HEADER INFO ACTION -->
          <a href="<?php url_site();  ?>/e<?php echo $sus['id']; ?>" class="profile-header-info-action button secondary" style="color: #fff;">
          <span class="hide-text-mobile"><?php lang('edit'); ?></span>&nbsp;<i class="fa fa-edit" aria-hidden="true"></i>
          </a>
          <!-- /PROFILE HEADER INFO ACTION -->
        <?php }else{ ?>
          <!-- PROFILE HEADER INFO ACTION -->
          <?php echo $follow; ?>
          <!-- /PROFILE HEADER INFO ACTION -->
        <?php } ?>
        <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']!=$usrRow['o_order'])){  ?>
          <!-- PROFILE HEADER INFO ACTION -->
          <a class="profile-header-info-action button primary" href="<?php url_site();  ?>/message/<?php echo $sus['id']; ?>">
          <span class="hide-text-mobile">Send Message</span>&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>
          </a>
          <!-- /PROFILE HEADER INFO ACTION -->
        <?php } ?>
        </div>
        <!-- /PROFILE HEADER INFO ACTIONS -->
      </div>
      <!-- /PROFILE HEADER INFO -->
    </div>
<?php 
if(isset($_GET['fl'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['fl'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$msgusid=$sus['id'];
 ?>
 <section class="section">
  
        <!-- SECTION HEADER INFO -->
        <div class="section-header-info">
          <!-- SECTION PRETITLE -->
          <p class="section-pretitle">@<?php echo $sus['username']; ?></p>
          <!-- /SECTION PRETITLE -->
  
          <!-- SECTION TITLE -->
          <h2 class="section-title"><?php lang('Followers'); ?> <span class="highlighted"><?php nbr_follow($sus['id'],"sid"); ?></span></h2>
          <!-- /SECTION TITLE -->
        </div>
        <!-- /SECTION HEADER INFO -->
        <div class="grid">
		<?php include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 20; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`like` WHERE sid='{$msgusid}' AND type=1 ORDER BY `time_t` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$fgft=$wt['uid'];
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time_t']);
if($catussen['id'] != ""){
include "templates/_panel/users_templates/user_list.php";	

 } }$url=$url_site."/user?fl=".$_GET['fl']."&";
    echo pagination($statement,$per_page,$page,$url);
      ?>
    </div>	
 </section>

<?php }else if(isset($_GET['fg'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['fg'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$msgusid=$sus['id'];
 ?>
 <section class="section">
  
  <!-- SECTION HEADER INFO -->
  <div class="section-header-info">
    <!-- SECTION PRETITLE -->
    <p class="section-pretitle">@<?php echo $sus['username']; ?></p>
    <!-- /SECTION PRETITLE -->

    <!-- SECTION TITLE -->
    <h2 class="section-title"><?php lang('Following'); ?> <span class="highlighted"><?php nbr_follow($sus['id'],"uid"); ?></span></h2>
    <!-- /SECTION TITLE -->
  </div>
  <!-- /SECTION HEADER INFO -->
  <div class="grid">

<?php include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 20; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`like` WHERE uid='{$msgusid}' AND type=1 ORDER BY `time_t` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$fgft=$wt['sid'];
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time_t']);
if($catussen['id'] != ""){
include "templates/_panel/users_templates/user_list.php";
 } }$url=$url_site."/user?fg=".$_GET['fg']."&";
    echo pagination($statement,$per_page,$page,$url);
      ?>    
		</div>	
 </section>
<?php }else{ echo "404"; }
   ?>
 <?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>