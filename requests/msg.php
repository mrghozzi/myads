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

include "../dbconfig.php";
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if(isset($vrf_License)=="65fgh4t8x5fe58v1rt8se9x"){
  if(isset($_GET['id'])){
    if(isset($_POST['comment'])){
           $bn_time = time();
           $bn_cmnt = $_POST['comment'];
           $bn_rid = $_GET['id'];
           if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
           $bn_uid = $_SESSION['user'];
              $bn_name = "message";
            $stmsb = $db_con->prepare("INSERT INTO messages (name,us_env,us_rec,msg,time)
            VALUES(:name,:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":name", $bn_name);
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_cmnt);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_rid);
            if($stmsb->execute()){
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
        <div class=\"panel panel-warning\">
  <div class=\"panel-heading\"><b>{$catusscm['username']}</b></div>
  <div class=\"panel-body\">
   {$comment}
  </div>
</div>
       </div>" ;
            echo $result;
         	}




    }else{ echo"404"; }
     }else{ echo"404"; }
 }else{ echo"404"; }
?>