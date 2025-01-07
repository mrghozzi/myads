<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################



include "dbconfig.php";
include "include/function.php";
header('Content-type: text/html; charset=utf-8');
 $title_page = "Forum - Portal";
   //   ALL Categories

  include_once('include/pagination.php');

 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 30; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();
$stt_time_dw=time()-(7 * 24 * 60 * 60);

if(isset($_GET['tag'] )){  
$tagkdsfh="#".$_GET['tag'];
$tagkdsfh=preg_replace("/\'/", "&prime;", $tagkdsfh);
$statement = "`status`
WHERE (
 (  tp_id  IN( SELECT id  FROM `directory` WHERE txt LIKE '%{$tagkdsfh}%' AND statu=1   ) AND  s_type=1 )
   OR
 ( tp_id IN(   SELECT id    FROM `forum`  WHERE txt LIKE '%{$tagkdsfh}%' AND statu=1  )
   AND 
 ( s_type=2 OR s_type=4 OR s_type=100 OR s_type=7867 ) )  )
   AND 
  date<={$stt_time_go}
ORDER BY `date` DESC";
}else if(isset($_GET['search'] )){
$tagkdsfh = $_GET['search'];
$tagkdsfh=preg_replace("/\'/", "&prime;", $tagkdsfh);
$statement = "`status`
WHERE (
 (  tp_id
   IN( SELECT id  FROM `directory` WHERE txt LIKE '%{$tagkdsfh}%' AND statu=1   ) AND  s_type=1 )
   OR
 ( tp_id
   IN(   SELECT id    FROM `forum`  WHERE txt LIKE '%{$tagkdsfh}%' AND statu=1  )
   AND ( s_type=2 OR s_type=4 OR s_type=100 OR s_type=7867 ) )  )
   AND date<={$stt_time_go}
ORDER BY `date` DESC";
}else if(isset($_COOKIE['user']) AND  isset($_GET['all'] )){
  $tabitem_portal = "all";
  $statement = "`status` WHERE date<={$stt_time_go} ORDER BY `date` DESC";
}else if(isset($_COOKIE['user'])){
  $tabitem_portal = "me";
  $my_user_1=$uRow['id'];
$statement = "`status`
WHERE ( uid
    IN( SELECT sid  FROM `like`  WHERE uid = {$my_user_1} AND type=1 )
    OR uid=1
    OR uid={$my_user_1}  )
    AND date<={$stt_time_go}
ORDER BY `date` DESC";
}else{
$statement = "`status` WHERE date<={$stt_time_go} ORDER BY `date` DESC";
}
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page}  " );
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
  global  $stt_time_go;
  global  $stt_time_dw;

  $sp_stat = 0;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==1){
 $s_type ="directory";            // directory
}else if($sutcat['s_type']==2){
 $s_type ="forum";                // topic
}else if($sutcat['s_type']==3){
 $s_type ="news";                 // news
}else if($sutcat['s_type']==4){
 $s_type ="forum";                // image
}else if($sutcat['s_type']==7867){
 $s_type ="forum";                // store
}else if($sutcat['s_type']==100){
 $s_type ="forum";                // text
}
 if(isset($s_type)){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1  AND id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);

if($sucat['statu']=="1") {
 if($sutcat['s_type']==1){
 tpl_site_stt($sutcat,0);        // directory
}else if($sutcat['s_type']==2){
 tpl_topic_stt($sutcat,0);       // topic
}else if($sutcat['s_type']==3){
 tpl_news_stt($sutcat);          // news
}else if($sutcat['s_type']==4){
 tpl_image_stt($sutcat,0);       // image
}else if($sutcat['s_type']==7867){
 tpl_store_stt($sutcat,0);       // store
}else if($sutcat['s_type']==100){
 tpl_post_stt($sutcat,0);       // text
}

  }
   }


  if(($sp_stat==4)OR($sp_stat==8)OR($sp_stat==16)OR($sp_stat==25)){

$spstusz = $db_con->prepare("SELECT *  FROM `status` WHERE date<={$stt_time_go} AND date>={$stt_time_dw}  order by RAND() LIMIT 1 " );
$spstusz->execute();
$spstt=$spstusz->fetch(PDO::FETCH_ASSOC);
if($spstt['s_type']==1){
 $sp_type ="directory";       // directory
}else if($spstt['s_type']==2){
 $sp_type ="forum";           // topic
}else if($spstt['s_type']==4){
$sp_type ="forum";            // image
}else if($spstt['s_type']==100){
 $sp_type ="forum";                // text
}
if(isset($sp_type)){
$spcatusz = $db_con->prepare("SELECT *  FROM `{$sp_type}` WHERE statu=1 AND id=".$spstt['tp_id'] );
$spcatusz->execute();
$spcat=$spcatusz->fetch(PDO::FETCH_ASSOC);

    if($spcat['statu']=="1") {
 if($spstt['s_type']==1){
 tpl_site_stt($spstt,1);    // directory
}else if($spstt['s_type']==2){
 tpl_topic_stt($spstt,1);   // topic
}else if($spstt['s_type']==4){
 tpl_image_stt($spstt,1);   // image
}else if($spstt['s_type']==100){
 tpl_post_stt($spstt,0);       // text
}
   }
     }
      }
  $sp_stat++;
 }  echo pagination($statement,$per_page,$page);
       }
    template_mine('header');
    template_mine('portal');
    template_mine('footer');

?>