<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
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
    $o_type = "user" ;
 $uid = $sus['id'];
  $name = $sus['username'];
  $o_mode = "0";
   $string = urlencode(mb_ereg_replace('\s+', '-', $name));
   $string = str_replace(array(' '),array('-'),$string);
   $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES (:name,:a_daf,:o_type,:dptdk,:uid,:o_mode) ");
	     $ostmsbs->bindParam(":uid", $uid);
            $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":a_daf", $string);
            $ostmsbs->bindParam(":dptdk", $o_mode);
            $ostmsbs->bindParam(":name", $name);
             $ostmsbs->bindParam(":o_mode", $o_mode);
            if($ostmsbs->execute()){
             $eusrpage = $usrRow['o_valuer'];
             header("Location: {$url_site}/u/{$string}") ;
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
 }else if(isset($_GET['e'])AND isset($_COOKIE['user']) AND ($_GET['e']==$_COOKIE['user'])){
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['e']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - ".$lang['e_profile'] ;
template_mine('header');
 template_mine('user_edit');
 template_mine('footer');
 }else if(isset($_GET['p'])AND isset($_COOKIE['user']) AND ($_GET['p']==$_COOKIE['user'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $_GET['p']);
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
 $title_page = $sus['username']." - ".$lang['edit'] ;
template_mine('header');
 template_mine('user_edit');
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
 }else{
 template_mine('header');
 template_mine('404');
 template_mine('footer');
 }

?>

