<?php

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2021                        ##
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

            $stmsb = $db_con->prepare("INSERT INTO f_coment (uid,tid,txt,date)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_cmnt);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_tid);
            if($stmsb->execute()){

$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$bn_tid );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
              if($sucat['uid']!=$bn_uid){
            $bn_nurl = "t".$bn_tid;
            $bn_logo  = "comment.png";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $bn_name  = $sus['username']." commented on your posts";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $sucat['uid']);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){

         	}
            }
$comment =  $_POST['comment'] ;
$emojis = array();
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
    $emojis['name'][]=$smlssen['name'];
    $emojis['img'][]="<img src=\"{$smlssen['img']}\" width=\"23\" height=\"23\" />";
}
  if(isset($emojis['name']) && isset($emojis['img']) ) {
         $comment = str_replace($emojis['name'], $emojis['img'], $comment);
}

             $comment = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<b>$1</b>', $comment );
$comment = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<i>$1</i>', $comment );
$comment = preg_replace('/[$]+([A-Za-z0-9-_]+)/', '<s>$1</s>', $comment );
              $catuscm = $db_con->prepare("SELECT *  FROM users WHERE  id='{$bn_uid}'");
              $catuscm->execute();
              $catusscm=$catuscm->fetch(PDO::FETCH_ASSOC);
            $result = "<div class=\" col-md-12 inbox-grid1\">
        <div class=\"panel panel-default\">
  <div class=\"panel-heading\"><a  href=\"{$url_site}/u/{$catusscm['id']}\"   ><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catusscm['img']}\" style=\"width: 35px;\" alt=\"user image\"> {$catusscm['username']} </div>
  <div class=\"panel-body\">
   {$comment}
  </div>
</div>
       </div>" ;
            echo $result;
         	}




    }
    }else if(isset($_GET['trash'])){
       if($_POST['trashid']){
        $bn_tid = $_POST['trashid'];
        if(isset($_COOKIE['admin'])){
         $stmt=$db_con->prepare("DELETE FROM f_coment WHERE id=:id  ");
         $stmt->execute(array(':id'=>$bn_tid));
         echo "DELETE";
           }else if(isset($_SESSION['user'])){

           $bn_uid = $_SESSION['user'];
           $stmt=$db_con->prepare("DELETE FROM f_coment WHERE id=:id AND uid=:uid ");
     	   $stmt->execute(array(':id'=>$bn_tid,':uid'=>$bn_uid));
           echo "DELETE";
           }else{
         echo "NO DELETE";
       }

       }else{
         echo "NO DELETE";
       }
    }else if(isset($_GET['ed'])){
      if($_POST['comment']){
       $k_comment = $_POST['comment'];
       $k_id = $_GET['ed'];
        if(isset($_COOKIE['admin'])){
         $stmt=$db_con->prepare("UPDATE `f_coment` SET `txt` =:ctxt WHERE `id` =:id ");
         $stmt->bindParam(":ctxt", $k_comment);
         $stmt->bindParam(":id", $k_id);
         if($stmt->execute()){
         header('Location: ' . $_SERVER['HTTP_REFERER']);
         }
           }else if(isset($_SESSION['user'])){
             $k_uid = $_SESSION['user'];
           $stmt=$db_con->prepare("UPDATE `f_coment` SET `txt` =:ctxt WHERE `id` =:id AND `uid` =:uid ");
         $stmt->bindParam(":ctxt", $k_comment);
         $stmt->bindParam(":id", $k_id);
         $stmt->bindParam(":uid", $k_uid);
         if($stmt->execute()){
         header('Location: ' . $_SERVER['HTTP_REFERER']);
         }
           }else{
         header('Location: ' . $_SERVER['HTTP_REFERER']);
       }

       }else{
         echo "NO DELETE";
       }
    }else{ echo"404"; }
 }else{ echo"404"; }
?>