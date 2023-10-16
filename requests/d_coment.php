<?php
#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "../dbconfig.php";
session_start();
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){

    //  setting
   $stmt = $db_con->prepare("SELECT *  FROM setting   " );
 $stmt->execute();
 $stt=$stmt->fetch(PDO::FETCH_ASSOC);
  $url_site   = $stt['url'];
  
  if(isset($_GET['id'])){
    if($_POST['comment']){

           $bn_time = time();
           $bn_cmnt = $_POST['comment'];
           $bn_tid = $_GET['id'];
           if (session_status() == PHP_SESSION_NONE) {

}
           $bn_uid = $_SESSION['user'];
           $o_type ="d_coment";
           $name   ="coment_dir";
            $stmsb = $db_con->prepare("INSERT INTO options (name,o_type,o_order,o_parent,o_valuer,o_mode)
            VALUES(:name,:o_type,:o_order,:o_parent,:o_valuer,:o_mode)");
			$stmsb->bindParam(":name",     $name);
			$stmsb->bindParam(":o_type",   $o_type);
			$stmsb->bindParam(":o_order",  $bn_uid);
            $stmsb->bindParam(":o_valuer", $bn_cmnt);
            $stmsb->bindParam(":o_mode",   $bn_time);
            $stmsb->bindParam(":o_parent", $bn_tid);
            if($stmsb->execute()){

$catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$bn_tid );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
              if(($sucat['uid']!=$bn_uid) AND ($sucat['uid']!=0)){
            $bn_nurl = "dr".$bn_tid;
            $bn_logo  = "comment";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $bn_name  = $sus['username']." commented on your posts";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid",  $sucat['uid']);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){
            $stmsb = $db_con->prepare("UPDATE users SET pts=pts+1
            WHERE id=:usid");
			$stmsb->bindParam(":usid", $sucat['uid']);
         	if($stmsb->execute()){
               $stmsc = $db_con->prepare("UPDATE users SET pts=pts+2
               WHERE id=:usid");
			   $stmsc->bindParam(":usid", $bn_uid);
         	   if($stmsc->execute()){  }
             }
         	}
            }
$comment =  $_POST['comment'] ;
$comment = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comment );
$comment = strip_tags($comment, '<p><a><b><br><li><ul><font><span><pre><u><s><img>');

              $catuscm = $db_con->prepare("SELECT *  FROM users WHERE  id='{$bn_uid}'");
              $catuscm->execute();
              $catusscm=$catuscm->fetch(PDO::FETCH_ASSOC);
 ?>          <hr />
             <!-- POST COMMENT -->
            <div class="post-comment">
              <!-- USER AVATAR -->
              <a class="user-avatar small no-outline online" href="<?php echo "{$url_site}/u/{$catusscm['id']}"; ?>">
                <!-- USER AVATAR CONTENT -->
                <div class="user-avatar-content ">
                  <!-- HEXAGON -->
                  <div class="hexagon-image-30-32" data-src="<?php echo "{$url_site}/{$catusscm['img']}"; ?>" style="width: 30px; height: 32px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas></div>
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
 <?php  if($catusscm['ucheck']=="1"){  ?>
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
<?php } ?>
                <!-- /USER AVATAR BADGE -->
              </a>
              <!-- /USER AVATAR -->

              <!-- POST COMMENT TEXT -->
              <p class="post-comment-text">
              <a class="post-comment-text-author" href="<?php echo "{$url_site}/u/{$catusscm['id']}"; ?>"><?php echo $catusscm['username']; ?></a>
              <?php echo $comment; ?>
              </p>
              <!-- /POST COMMENT TEXT -->

            </div>
            <!-- /POST COMMENT -->
<script src="<?php echo $url_site;  ?>/templates/_panel/js/global.hexagons.js"></script>
 <?php

         	}




    }
    }else if(isset($_GET['trash'])){
       if($_POST['trashid']){
        $bn_tid = $_POST['trashid'];
        if(isset($_COOKIE['admin'])){
         $stmt=$db_con->prepare("DELETE FROM options WHERE id=:id  ");
         $stmt->execute(array(':id'=>$bn_tid));
         echo "DELETE";
           }else if(isset($_SESSION['user'])){

           $bn_uid = $_SESSION['user'];
           $stmt=$db_con->prepare("DELETE FROM options WHERE id=:id AND o_order=:uid ");
     	   $stmt->execute(array(':id'=>$bn_tid,':uid'=>$bn_uid));
           $stmsb = $db_con->prepare("UPDATE users SET pts=pts-2
            WHERE id=:usid");
			$stmsb->bindParam(":usid", $bn_uid);
         	if($stmsb->execute()){ }
           echo "DELETE";
           }else{
         echo "NO DELETE";
       }

       }else{
         echo "NO DELETE";
       }
    }else{ echo"404"; }
 }else{ echo"404"; }
?>