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
$per_page = 15; // Records per page.
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
     $ntfread = "read";
  }else if($wt['state']=="3"){
     $ntfread = "unread";
  }else{
     $ntfread = "";
  }
echo "<div class=\"notification-box\">
            <!-- USER STATUS -->
            <div class=\"user-status notification\">
              <!-- USER STATUS TITLE -->
              <p class=\"user-status-title\"><a href=\"{$url_site}/notif/{$wt['id']}\">{$wt['name']}</a></p>
              <!-- /USER STATUS TITLE -->

              <!-- USER STATUS TIMESTAMP -->
              <p class=\"user-status-timestamp small-space\">{$time_cmt}</p>
              <!-- /USER STATUS TIMESTAMP -->

              <!-- USER STATUS ICON -->
              <div class=\"user-status-icon\">
                <!-- ICON COMMENT -->
                <svg class=\"icon-{$wt['logo']}\">
                  <use xlink:href=\"#svg-{$wt['logo']}\"></use>
                </svg>
                <!-- /ICON COMMENT -->
              </div>
              <!-- /USER STATUS ICON -->
            </div>
            <!-- /USER STATUS -->

            <!-- MARK UNREAD BUTTON -->
            <div class=\"mark-{$ntfread}-button\"></div>
            <!-- /MARK UNREAD BUTTON -->
</div>";

   }$url=$url_site."/messages?ntf&";
   echo pagination($statement,$per_page,$page,$url);
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
     $ntfread = "read";
  }else if($wt['state']=="3"){
     $ntfread = "unread";
  }else{
     $ntfread = "";
  }
echo "<div class=\"notification-box\">
            <!-- USER STATUS -->
            <div class=\"user-status notification\">
              <!-- USER STATUS TITLE -->
              <p class=\"user-status-title\"><a href=\"{$url_site}/message/{$catussen['id']}\">{$catussen['username']}</a></p>
              <!-- /USER STATUS TITLE -->

              <!-- USER STATUS TIMESTAMP -->
              <p class=\"user-status-timestamp small-space\">{$time_cmt}</p>
              <!-- /USER STATUS TIMESTAMP -->

              <!-- USER STATUS ICON -->
              <div class=\"user-status-icon\">
                <!-- ICON COMMENT -->
                <svg class=\"icon-messages\">
                  <use xlink:href=\"#svg-messages\"></use>
                </svg>
                <!-- /ICON COMMENT -->
              </div>
              <!-- /USER STATUS ICON -->
            </div>
            <!-- /USER STATUS -->

            <!-- MARK UNREAD BUTTON -->
            <div class=\"mark-{$ntfread}-button\"></div>
            <!-- /MARK UNREAD BUTTON -->
</div>";

   }echo pagination($statement,$per_page,$page);
    }
   template_mine('header');
   template_mine('messages');
   template_mine('footer');
  }

 }



?>

