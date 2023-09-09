<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################



include "dbconfig.php";
include "include/function.php";
header('Content-type: text/html; charset=utf-8');
 if(isset($_GET['t'])){     //   Categories
  $get_cat=$_GET['t'];
  $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$_GET['t'] );
  $catusz->execute();
  $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
   $title_page = "Forum - ".$sucat['name'];
   $description_page = strip_tags($sucat['txt'], '');
   $catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
   $catus->execute();
   $catuss=$catus->fetch(PDO::FETCH_ASSOC);
   $username_topic = $catuss['username'] ;
   $catdid=$sucat['id'];
   $catdname=$sucat['name'];
   try{
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4,7867,100) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
      header("Location: {$url_site}/404") ;
    }
if($susat['s_type'] == 7867){
  header("Location: {$url_site}/producer/{$catdname}");
}else if($susat['s_type'] == 100){
      $posttxt = strip_tags($sucat['txt'], '');
      $description_page = strip_tags($sucat['txt'], '');
     $title_page = $sucat['name']."&nbsp;-&nbsp;".$posttxt;
   template_mine('header');
   ads_site(5);
   tpl_post_stt($susat,0);
   ?>
<script>
$(".comment_100_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=100&tid=<?php echo $sucat['id']; ?>');
$(".sh_comment_p<?php echo $susat['id']; ?>").addClass('active');
</script>
<?php

   template_mine('footer');
}else if($susat['s_type'] == 4){
     $imgtxt = strip_tags($sucat['txt'], '');
     $title_page = $sucat['name']."&nbsp;-&nbsp;".$imgtxt;
     $image_post = "image_post" ;
     $catusc = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$image_post}' AND o_parent='{$sucat['id']}' ) ");
     $catusc->execute();
     $catussc=$catusc->fetch(PDO::FETCH_ASSOC);
     $image_page = $catussc['o_valuer'];
   template_mine('header');
   ads_site(5);
   tpl_image_stt($susat,0);
   ?>
<script>
$(".comment_4_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=4&tid=<?php echo $sucat['id']; ?>');
$(".sh_comment_i<?php echo $susat['id']; ?>").addClass('active');
</script>
<?php
   template_mine('footer');
}else{
   template_mine('header');
   template_mine('topic');
   template_mine('footer');
}

  }else if(isset($_GET['p']) OR isset($_GET['e'])){     //   new post

   template_mine('header');
   if(isset($_COOKIE['user'])){ 
   template_mine('post');
   }else{
   template_mine('404');
   }
   template_mine('footer');
   }else if(isset($_GET['f'])){                      //   ALL Categories
 $title_page = "Forum";
 $get_cat =  $_GET['f'];
 include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();
$statement = "`status` WHERE (  tp_id IN(
  SELECT id
  FROM `forum`
  WHERE cat='{$get_cat}'  AND statu=1
)  AND s_type=2 AND date<={$stt_time_go} ) ORDER BY `id` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$catsum->execute();
function forum_tpc_list() {
  global  $db_con;
  global  $catsum;
  global  $statement;
  global  $per_page;
  global  $page;
  global  $uRow;
  global  $lang;
  global  $url_site;
  global  $title_s;
  global  $_GET;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($sucat['statu'])=="1") {   tpl_topic_stt($sutcat,0);   }
 }   echo pagination($statement,$per_page,$page);
      }
    template_mine('header');
    template_mine('forum');
    template_mine('footer');
     }else{

    template_mine('header');
    template_mine('fcat');
    template_mine('footer');
     }
?>