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
  $title_page = $lang['msgs'];

 if(!isset($_COOKIE['user'])!="")
{
$title_page = "404";
template_mine('header');
template_mine('404');
template_mine('footer');
}else{
  if(isset($_GET['m'])){

   template_mine('header');
   template_mine('message');
   template_mine('footer');
  }else if(isset($_GET['n'])){
$ntf_usen = $db_con->prepare("SELECT *  FROM notif
        WHERE id='{$_GET['n']}' AND uid='{$_COOKIE['user']}' ");
$ntf_usen->execute();
$ntf_ssen=$ntf_usen->fetch(PDO::FETCH_ASSOC);
$ntf_state="0";
$stntfb = $db_con->prepare("UPDATE notif SET state=:state
            WHERE id=:id AND uid=:uid");
            $stntfb->bindParam(":uid",   $ntf_ssen['uid']);
            $stntfb->bindParam(":state", $ntf_state);
            $stntfb->bindParam(":id",    $ntf_ssen['id']);
            if($stntfb->execute()){
            header("Location: {$url_site}/{$ntf_ssen['nurl']}") ;
         	}

  }else if(isset($_GET['ntf'])){
    $title_page = "Notification";
    $msgusid = $_COOKIE['user'];
    include_once('include/pagination.php');
    $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`notif` WHERE uid='{$msgusid}' ORDER BY `time` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();
    function ntf_list() {
      global  $results;  global  $statement; global  $per_page; global  $page; global $url_site; global $msgusid; global $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$fgft=$wt['uid'];
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time']);
 if($wt['state']=="1"){
     echo "<tr class=\"active\" >";
  }else{
     echo "<tr>";
  }
echo "<td>#{$wt['id']}</td>
  <td><b><a href=\"{$url_site}/notif/{$wt['id']}\"><div class=\"user_img\"><img src=\"{$url_site}/templates/_panel/images/{$wt['logo']}\" alt=\"\"></div>{$wt['name']}</a></b></td>
  <td><a href=\"{$url_site}/notif/{$wt['id']}\">{$time_cmt}</a></td>
</tr>";

   }echo pagination($statement,$per_page,$page);
     }
   template_mine('header');
   template_mine('notification');
   template_mine('footer');
  }else{
    $msgusid = $_COOKIE['user'];
    include_once('include/pagination.php');
    $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`messages` WHERE us_rec='{$msgusid}' OR us_env='{$msgusid}' ORDER BY `time` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();
    function msg_list() {
      global  $results;  global  $statement; global  $per_page; global  $page; global $url_site; global $msgusid; global $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
if($wt['us_rec']==$msgusid){ $fgft=$wt['us_env']; } else if($wt['us_env']==$msgusid){ $fgft=$wt['us_rec']; }
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time']);
 if($wt['state']=="1"){
     echo "<tr class=\"active\" >";
  }else{
     echo "<tr>";
  }
echo "<td>#{$wt['id_msg']}</td>
  <td><b>@<a href=\"{$url_site}/message/{$catussen['id']}\">{$catussen['username']}  ";
  online_us($catussen['id']);
  echo "</a></b></td>
  <td><a href=\"{$url_site}/message/{$catussen['id']}\">{$time_cmt}</a></td>
</tr>";

   }echo pagination($statement,$per_page,$page);
    }
   template_mine('header');
   template_mine('messages');
   template_mine('footer');
  }

 }



?>

