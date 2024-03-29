<?php
#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                      http://www.krhost.ga                       ##
##                     e-mail: admin@krhost.ga                     ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################



include "dbconfig.php";
include "include/function.php";

 if(isset($_GET['cat'])){     //   Categories
  $get_cat=$_GET['cat'];
  include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 30; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`status` WHERE (  tp_id IN(
  SELECT id
  FROM `directory`
  WHERE cat=:dcat  AND statu=1
)  AND s_type=1 ) ORDER BY `id` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$catsum->bindParam(":dcat", $get_cat);
$catsum->execute();
function dir_cat_list() {
  global  $db_con;
  global  $catsum;
  global  $statement;
  global  $per_page;
  global  $page;
  global  $uRow;
  global  $lang;
  global  $url_site;
  global  $title_s;
  global  $get_cat;
if ($catsum->rowCount() == 0) {
    echo "<h2><center>No Sites</center></h2>";
} else {
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {  tpl_site_stt($sutcat,0);   }
 }  $url=$url_site."/directory?cat=".$_GET['cat']."&";
$statements = "`status` WHERE (  tp_id IN(
  SELECT id
  FROM `directory`
  WHERE cat='{$get_cat}'  AND statu=1
)  AND s_type=1 ) ORDER BY `id` DESC";
    echo pagination($statements,$per_page,$page,$url);
       }
                      }
$catdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 AND id=:dcat " );
$catdir->bindParam(":dcat", $_GET['cat']);
$catdir->execute();
$catdirs=$catdir->fetch(PDO::FETCH_ASSOC);
   $title_page = "Directory - ".$catdirs['name'];
   template_mine('header');
   if(isset($catdirs['id']) AND ($catdirs['id']==$_GET['cat'])){
 template_mine('directory');
 }else{
 template_mine('404');
 }
   template_mine('footer');
  }else if(isset($_GET['dir'])){       //     Site
$ndfk=$_GET['dir'];
$catusd = $db_con->prepare("SELECT *  FROM `short` WHERE  sh_type=1 AND sho='{$ndfk}'" );
$catusd->execute();
if($catussd=$catusd->fetch(PDO::FETCH_ASSOC)){
$catdid=$catussd['tp_id'];
$catdurl=$catussd['url'];
$stmdr = $db_con->prepare("UPDATE directory SET vu=vu+1 WHERE id=:ertb");
$stmdr->bindParam(":ertb", $catdid);
  if($stmdr->execute()){
    if (filter_var($catdurl, FILTER_VALIDATE_URL) === FALSE) {
    header("Location: {$url_site}/404");
           }else{
    header("Location: {$catdurl}");
           }
 }
    }else{

    template_mine('header');
    template_mine('404');
    template_mine('footer');

    }
 }else if(isset($_GET['p'])){   // new site
 $title_page = "دليل مواقع - أضف موقعك";
 include "requests/captcha.php";
 template_mine('header');
 template_mine('newdir');
 template_mine('footer');
 }else if(isset($_GET['s'])){   // new site
  $get_cat=$_GET['s'];
  $ifdir_vst = "yes";
  $catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$_GET['s'] );
  $catusz->execute();
  $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
   $title_page = $sucat['name'];
   $metakeywords_page = $sucat['metakeywords'];
   $description_page = strip_tags($sucat['txt'], '');
   $catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
   $catus->execute();
   $catuss=$catus->fetch(PDO::FETCH_ASSOC);
   $username_topic = $catuss['username'] ;
   $catdid=$sucat['id'];
   $catdname=$sucat['name'];
 if(isset($catdid) AND ($catdid==$get_cat)){
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (1) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);

   template_mine('header');
   ads_site(5);
   tpl_site_vst($susat,0);
   }else{

    template_mine('header');
    template_mine('404');
    template_mine('footer');

    }
 ?>
<script>
$(".comment_1_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=1&tid=<?php echo $sucat['id']; ?>');
$(".sh_comment_s<?php echo $susat['id']; ?>").addClass('active');
</script>
<?php
   template_mine('footer');

 }else{                      //   ALL Categories
 $title_page = "Directory - ALL Categories";
 include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 25; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`status` WHERE s_type=1 ORDER BY `id` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$catsum->execute();
function dir_cat_list() {
  global  $db_con;
  global  $catsum;
  global  $statement;
  global  $per_page;
  global  $page;
  global  $uRow;
  global  $lang;
  global  $url_site;
  global  $title_s;
if ($catsum->rowCount() == 0) {
    echo "<h2><center>No Sites</center></h2>";
} else {
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if($sucat['statu']=="1") {   tpl_site_stt($sutcat,0);   }
 }   echo pagination($statement,$per_page,$page);
       }
                      }
    template_mine('header');
    template_mine('directory');
    template_mine('footer');
}
?>