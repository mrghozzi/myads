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
header('Content-type: text/html; charset=utf-8');
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lang_site  = $ab['lang'];
        $url_site   = $ab['url'];
        if (isset($_COOKIE["lang"])) {
        $lng=$_COOKIE["lang"] ;
                              } else {
        $lng        = $lang_site ;
                              }
include "../content/languages/$lng.php";
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if(isset($_POST['submit'])){

           $bn_time    =   time();
           $bn_desc    = $_POST['desc'];
           $bn_vnbr    = $_POST['vnbr'];
           $bn_linkzip = $_POST['linkzip'];
           $bn_name    = $_GET['name'];
           session_start();

           $str_desc = strlen($bn_desc);
           $str_vnbr = strlen($bn_vnbr);

                 $o_type = "store";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `name` = :o_name AND `o_type` = :o_type  ");
                 $setcats->bindParam(":o_name", $bn_name);
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
              $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
           if((isset($_COOKIE['user']) AND ($_COOKIE['user'] == $catstorRow['o_parent'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
           $bn_uid  = $_SESSION['user'];

           $bn_vu   = "0";
           $bn_type= "7867";
            if (empty($bn_linkzip)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['yhnutf'];
            }else if ((empty($bn_vnbr)) OR ($str_vnbr < 2) OR ($str_vnbr > 12)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['tvnmba'];
            }else if ((empty($bn_desc)) OR ($str_desc < 10) OR ($str_desc > 2400)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['pdmbbacf'];
            }

            if(isset($bn_notvalid) AND ($bn_notvalid == "notvalid")){
           $_SESSION['sdesc']    = $_POST['desc'];
           $_SESSION['svnbr']    = $_POST['vnbr'];
           $_SESSION['slinkzip'] = $_POST['linkzip'];
            header("Location: {$url_site}/update/{$bn_name}");
           }else{

              $bn_tid = $catstorRow['id'];
               $sttid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =:o_parent ORDER BY `o_order`  DESC " );
               $sttid->bindParam(":o_parent",     $bn_tid);
               $sttid->execute();
               $strtid=$sttid->fetch(PDO::FETCH_ASSOC);
              $f_type  = "store_file";
              $bn_sid  = $strtid['o_order']+1;
              $f_vu    = "0";
            $insstfl = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$insstfl->bindParam(":name",      $bn_vnbr);
            $insstfl->bindParam(":o_valuer",  $bn_desc);
            $insstfl->bindParam(":o_type",    $f_type);
            $insstfl->bindParam(":o_parent",  $bn_tid);
            $insstfl->bindParam(":o_order",   $bn_sid);
            $insstfl->bindParam(":o_mode",    $bn_linkzip);
            if($insstfl->execute()){
             $bn_fid = $db_con->lastInsertId();
             $dir_lnk_hash = hash('crc32', $bn_linkzip.$bn_fid );
             $stmsbsh = $db_con->prepare("INSERT INTO short (uid,sho,url,clik,sh_type,tp_id)
            VALUES(:uid,:lnk_hash,:url,:clik,:sh_type,:tp_id)");
			$stmsbsh->bindParam(":uid",      $bn_uid);
            $stmsbsh->bindParam(":lnk_hash", $dir_lnk_hash);
            $stmsbsh->bindParam(":url",      $bn_linkzip);
            $stmsbsh->bindParam(":clik",     $f_vu);
            $stmsbsh->bindParam(":sh_type",  $bn_type);
            $stmsbsh->bindParam(":tp_id",    $bn_fid);
            if($stmsbsh->execute()){

            header("Location: {$url_site}/producer/{$bn_name}");

                    }
                     }
                      }
                       }
                        }
                         }else{
                            echo"404";
                           }
?>