<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "../dbconfig.php";
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
  session_start();
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lang_site=$ab['lang'];
        $url_site   = $ab['url'];
        if (isset($_COOKIE["lang"])) {
      $lng=$_COOKIE["lang"] ;
       } else {  $lng=$lang_site ; }
   include "../content/languages/$lng.php";

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}

if(isset($_POST['submit'])){
  $capt    = $_POST['capt'];
  $bn_txt  = $_POST['txt'];
  if(isset($_POST['name'])){
             $bn_name    = $_POST['name'];
          }else if(isset($_GET['name'])){
          $bn_name    = $_GET['name'];
           }
  if(isset($_SESSION['user'])){
           $bn_uid  = $_SESSION['user'];
           }else{
            $bn_uid  = "0";
           }
   $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
 if((int)isset($_SESSION['CAPCHA'])){
             $capt_sess=$_SESSION['CAPCHA'];
             }else{
              $capt_sess="no";
             }

    if($capt_sess=="no"){
             header("Location: {$url_site}/404");
           }else if($capt==$capt_sess) {
             $str_txt  = strlen($bn_txt) ;
                if ((empty($bn_txt)) OR ($str_txt < 10)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['ssbalcl'];
            }
            }else{
               $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = "captcha".$lang['invalid'];
            }

     if(isset($bn_notvalid) AND ($bn_notvalid == "notvalid")){
		   $_SESSION['stxt']     = $_POST['txt'];
           if(isset($_POST['name'])){
             $_SESSION['sname']    = $_POST['name'];
           header("Location: {$url_site}/kb/{$_POST['submit']}:{$_POST['name']}");
           }else if(isset($_GET['name'])){
           header("Location: {$url_site}/edk/{$_POST['submit']}:{$_GET['name']}");
           }
        }else{

$k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type  " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":o_mode", $_POST['submit']);
                 $storknow->bindParam(":name", $bn_name);
                 $storknow->execute();
                 $sknowled=$storknow->fetch(PDO::FETCH_ASSOC);
$o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->bindParam(":name", $sknowled['o_mode']);
                 $stormt->execute();
                 $tpstorRow=$stormt->fetch(PDO::FETCH_ASSOC);
if(isset($sknowled['name']) AND ($sknowled['name']==$bn_name)){
      if((isset($_SESSION['user']) AND ($_SESSION['user']==$tpstorRow['o_parent']) )
  OR (isset($_SESSION['user']) AND ($_SESSION['user']==$sknowled['o_parent']) )
  OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
  $bn_stat = "0";
  }else{
    $bn_stat = "1";
  }
  }else{
    $bn_stat = "0";
  }

  if($bn_stat=="0"){
    $bn_o_order = "2";
    $u_type = "knowledgebase";
    $stmsb = $db_con->prepare("UPDATE options SET o_order=:o_order   WHERE name=:name AND o_order=0 AND o_mode=:o_mode AND o_type=:o_type ");
            $stmsb->bindParam(":o_mode",   $_POST['submit']);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":o_order",    $bn_o_order);
            $stmsb->bindParam(":o_type",    $u_type);
            if($stmsb->execute()){ }
  }
  $d_type = "knowledgebase";
   $insstr = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$insstr->bindParam(":name",      $bn_name);
            $insstr->bindParam(":o_valuer",  $bn_txt);
            $insstr->bindParam(":o_type",    $d_type);
            $insstr->bindParam(":o_parent",  $bn_uid);
            $insstr->bindParam(":o_order",   $bn_stat);
            $insstr->bindParam(":o_mode",    $_POST['submit']);
            if($insstr->execute()){
              header("Location: {$url_site}/kb/{$_POST['submit']}:{$bn_name}");
         	}

}
}else if(isset($_POST['pr']) AND isset($_POST['pg'])){
  $u_type = "knowledgebase";
       $bn_o_order = "2";
    $stmsb = $db_con->prepare("UPDATE options SET o_order=:o_order   WHERE name=:name AND o_order=0 AND o_mode=:o_mode AND o_type=:o_type ");
            $stmsb->bindParam(":o_mode",   $_POST['pr']);
            $stmsb->bindParam(":name",  $_GET['pg']);
            $stmsb->bindParam(":o_order",    $bn_o_order);
            $stmsb->bindParam(":o_type",    $u_type);
            if($stmsb->execute()){
  $bn_o_order = "0";
    $stmsb = $db_con->prepare("UPDATE options SET o_order=:o_order   WHERE id=:id AND  o_mode=:o_mode AND o_type=:o_type ");
            $stmsb->bindParam(":o_mode",   $_POST['pr']);
            $stmsb->bindParam(":id",  $_POST['pg']);
            $stmsb->bindParam(":o_order",    $bn_o_order);
            $stmsb->bindParam(":o_type",    $u_type);
            if($stmsb->execute()){
    $k_type = "knowledgebase";
                $n_o_order = "1";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=:o_order ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":name", $_GET['pg']);
                 $storknow->bindParam(":o_mode", $_POST['pr']);
                 $storknow->bindParam(":o_order",    $n_o_order);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {
                    $k_type = "knowledgebase";
                   $delkbid =  $sknowled['id'];
                 $stmt=$db_con->prepare("DELETE FROM options WHERE id=:id AND o_type=:o_type ");
                 $stmt->bindParam(":id", $delkbid);
                 $stmt->bindParam(":o_type", $k_type);
                 if($stmt->execute()){     }
                  }
                  header("Location: {$url_site}/kb/{$_POST['pr']}:{$_GET['pg']}");
             }
             }
}else{
  header("Location: {$url_site}/404");
}
 }else{ echo"404"; }
?>