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

if(isset($_GET['u']) AND is_numeric($_GET['u'])){
  $stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` LIKE :o_order ");
$stausr->bindParam(":o_order", $_GET['u']);
 $stausr->execute();
 $usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
 if(isset($_GET['u']) AND isset($usrRow['o_order']) AND ($_GET['u']==$usrRow['o_order'])){
      $eusrpage = $usrRow['o_valuer'];
  header("Location: {$url_site}/u/{$eusrpage}") ;
 }else{
   $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['u']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
  if($_GET['u']==$sus['id']){
   $o_type = "user" ;
   $uid = $sus['id'];
    if ( is_numeric($sus['username']) ) {
    $refrow_hash = hash('crc32', $sus['username']);
    $name = $refrow_hash."_".$sus['username'];
    } else {
    $name = $sus['username'];
    }
   $usname = $sus['username'];
   $o_mode = "upload/cover.jpg";
   $string = urlencode(mb_ereg_replace('\s+', '-', $name));
   $string = str_replace(array(' '),array('-'),$string);
   $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
                                 VALUES (:name,:a_daf,:o_type,:dptdk,:uid,:o_mode) ");
	$ostmsbs->bindParam(":uid", $uid);
    $ostmsbs->bindParam(":o_type", $o_type);
    $ostmsbs->bindParam(":a_daf", $string);
    $ostmsbs->bindParam(":dptdk", $o_mode);
    $ostmsbs->bindParam(":name", $usname);
    $ostmsbs->bindParam(":o_mode", $o_mode);
     if($ostmsbs->execute()){
        $eusrpage = $usrRow['o_valuer'];
        header("Location: {$url_site}/u/{$string}") ;
     }
  }else{
   template_mine('header');
   template_mine('404');
   template_mine('footer');
 }
 }

}else if(isset($_GET['u'])){
   //   ALL Categories
 include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['u']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
 if($usrRow['o_mode']=="0"){
   $us_cover = $url_site."/upload/cover.jpg";
 }else{
   $us_cover = $url_site."/".$usrRow['o_mode'];
 }
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} ORDER BY `date` DESC";
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
  global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==1){
 $s_type ="directory";
}else if($sutcat['s_type']==2){
 $s_type ="forum";
}else if($sutcat['s_type']==4){
 $s_type ="forum";
}else if($sutcat['s_type']==7867){
 $s_type ="forum";
}else if($sutcat['s_type']==100){
 $s_type ="forum";
}
if(   ($sutcat['s_type']==1)
   OR ($sutcat['s_type']==2)
   OR ($sutcat['s_type']==4)
   OR ($sutcat['s_type']==7867)
   OR ($sutcat['s_type']==100)
){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
 if($sutcat['s_type']==1){
 tpl_site_stt($sutcat,0);
}else if($sutcat['s_type']==2){
 tpl_topic_stt($sutcat,0);
}else if($sutcat['s_type']==4){
 tpl_image_stt($sutcat,0);
}else if($sutcat['s_type']==7867){
 tpl_store_stt($sutcat,0);
}else if($sutcat['s_type']==100){
 tpl_post_stt($sutcat,0);
}
 }
  }
 }$url=$url_site."/user?u=".$_GET['u']."&";
    echo pagination($statement,$per_page,$page,$url);
            }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - Profile" ;
 template_mine('header');
 template_mine('user');
 template_mine('footer');
 }else if(isset($_GET['ph'])){
  //   ALL Categories
include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['ph']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if($usrRow['o_mode']=="0"){
  $us_cover = $url_site."/upload/cover.jpg";
}else{
  $us_cover = $url_site."/".$usrRow['o_mode'];
}
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} AND s_type=4 ORDER BY `date` DESC";
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
 global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==4){
$s_type ="forum";
}
if($sutcat['s_type']==4){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
if($sutcat['s_type']==4){
tpl_image_stt($sutcat,0);
}
}
 }
}$url=$url_site."/user?ph=".$_GET['ph']."&";
   echo pagination($statement,$per_page,$page,$url);
           }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$title_page = $sus['username']." - Profile" ;
template_mine('header');
template_mine('user');
template_mine('footer');
}else if(isset($_GET['blog'])){
  //   ALL Categories
include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['blog']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if($usrRow['o_mode']=="0"){
  $us_cover = $url_site."/upload/cover.jpg";
}else{
  $us_cover = $url_site."/".$usrRow['o_mode'];
}
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} AND s_type=100 ORDER BY `date` DESC";
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
 global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==100){
$s_type ="forum";
}
if($sutcat['s_type']==100){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
if($sutcat['s_type']==100){
  tpl_post_stt($sutcat,0);
}
}
 }
}$url=$url_site."/user?blog=".$_GET['blog']."&";
   echo pagination($statement,$per_page,$page,$url);
           }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$title_page = $sus['username']." - Profile" ;
template_mine('header');
template_mine('user');
template_mine('footer');
}else if(isset($_GET['uforum'])){
  //   ALL Categories
include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['uforum']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if($usrRow['o_mode']=="0"){
  $us_cover = $url_site."/upload/cover.jpg";
}else{
  $us_cover = $url_site."/".$usrRow['o_mode'];
}
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} AND s_type=2 ORDER BY `date` DESC";
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
 global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==2){
$s_type ="forum";
}
if($sutcat['s_type']==2){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
if($sutcat['s_type']==2){
  tpl_topic_stt($sutcat,0);
}
}
 }
}$url=$url_site."/user?uforum=".$_GET['uforum']."&";
   echo pagination($statement,$per_page,$page,$url);
           }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$title_page = $sus['username']." - Profile" ;
template_mine('header');
template_mine('user');
template_mine('footer');
}else if(isset($_GET['ulinks'])){
  //   ALL Categories
include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['ulinks']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if($usrRow['o_mode']=="0"){
  $us_cover = $url_site."/upload/cover.jpg";
}else{
  $us_cover = $url_site."/".$usrRow['o_mode'];
}
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} AND s_type=1 ORDER BY `date` DESC";
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
 global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==1){
$s_type ="directory";
}
if($sutcat['s_type']==1){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
if($sutcat['s_type']==1){
  tpl_site_stt($sutcat,0);
}
}
 }
}$url=$url_site."/user?ulinks=".$_GET['ulinks']."&";
   echo pagination($statement,$per_page,$page,$url);
           }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$title_page = $sus['username']." - Profile" ;
template_mine('header');
template_mine('user');
template_mine('footer');
}else if(isset($_GET['ushop'])){
  //   ALL Categories
include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 21; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$stt_time_go=time();


$string = urlencode(mb_ereg_replace('\s+', '-', $_GET['ushop']));

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_valuer` LIKE :o_valuer ");
$stausr->bindParam(":o_valuer", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
if($usrRow['o_mode']=="0"){
  $us_cover = $url_site."/upload/cover.jpg";
}else{
  $us_cover = $url_site."/".$usrRow['o_mode'];
}
$guser_id = $usrRow['o_order'];
$statement = "`status` WHERE uid='{$guser_id}' AND date<={$stt_time_go} AND s_type=7867 ORDER BY `date` DESC";
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
 global  $usrRow;
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{  if($sutcat['s_type']==7867){
$s_type ="forum";
}
if($sutcat['s_type']==7867){
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=:tp_id ");
$catusz->bindParam(":tp_id", $sutcat['tp_id']);
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {
if($sutcat['s_type']==7867){
  tpl_store_stt($sutcat,0);
}
}
 }
}$url=$url_site."/user?ushop=".$_GET['ushop']."&";
   echo pagination($statement,$per_page,$page,$url);
           }
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$title_page = $sus['username']." - Profile" ;
template_mine('header');
template_mine('user');
template_mine('footer');
}else if(isset($_GET['e'])AND isset($_COOKIE['user']) AND ($_GET['e']==$_COOKIE['user'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['e']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - ".$lang['e_profile'] ;
template_mine('header');
 template_mine('user_settings/user_edit');
 template_mine('footer');
 }else if(isset($_GET['p'])AND isset($_COOKIE['user']) AND ($_GET['p']==$_COOKIE['user'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['p']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$string = $sus['id'];

$stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order`=:o_order ");
$stausr->bindParam(":o_order", $string);
$stausr->execute();
$usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
 if($usrRow['o_mode']=="0"){
   $us_cover = $url_site."/upload/cover.jpg";
 }else{
   $us_cover = $url_site."/".$usrRow['o_mode'];
 }
 $title_page = $sus['username']." - ".$lang['edit'] ;
template_mine('header');
 template_mine('user_settings/user_photo');
 template_mine('footer');
 }else if((int)isset($_GET['fl'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['fl']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - Followers" ;

  template_mine('header');
  template_mine('follow');
  template_mine('footer');
 }else if((int)isset($_GET['fg'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['fg']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - Following" ;
  template_mine('header');
  template_mine('follow');
  template_mine('footer');
 }else if((int)isset($_GET['ff'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['ff']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - Following" ;
  template_mine('header');
  template_mine('follow');
  template_mine('footer');
 }else if((int)isset($_GET['o'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['o']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - ".$lang['options'] ;
  template_mine('header');
 template_mine('user_settings/user_options');
 template_mine('footer');
 }else if((int)isset($_GET['h'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
  $usz->bindParam(":u_id", $_GET['o']);
  $usz->execute();
  $sus=$usz->fetch(PDO::FETCH_ASSOC);
   $title_page = $sus['username']." - ".$lang['options'] ;
    template_mine('header');
   template_mine('user_settings/uesr_history');
   template_mine('footer');
   }else{
 template_mine('header');
 template_mine('404');
 template_mine('footer');
 }

?>

