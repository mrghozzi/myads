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
           $bn_name    = $_POST['name'];
           $bn_desc    = $_POST['desc'];
           $bn_vnbr    = $_POST['vnbr'];
           $bn_cat_s   = $_POST['cat_s'];
           $bn_sc_cat  = $_POST['sc_cat'];
           $bn_txt     = $_POST['txt'];
           $bn_linkzip = $_POST['linkzip'];
           $bn_img     = $_POST['img'];

           $bn_pts     = "0";    // Determine the number of points (in the future)

           session_start();

           $str_name = strlen($bn_name);
           $str_desc = strlen($bn_desc);
           $str_vnbr = strlen($bn_vnbr);
           $str_txt  = strlen($bn_txt) ;

                 $o_type = "store";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `name` = :o_name AND `o_type` = :o_type  ");
                 $setcats->bindParam(":o_name", $bn_name);
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);

           if(isset($_COOKIE['user'])){
           $bn_uid  = $_SESSION['user'];
           }
           $bn_vu   = "0";
           $bn_type= "7867";
           if (filter_var($bn_img, FILTER_VALIDATE_URL) === FALSE) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['timlii'];
            }else if (empty($bn_linkzip)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['yhnutf'];
            }else if ((empty($bn_txt)) OR ($str_txt < 10)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['ssbalcl'];
            }else if ((empty($bn_vnbr)) OR ($str_vnbr < 2) OR ($str_vnbr > 12)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['tvnmba'];
            }else if ((empty($bn_desc)) OR ($str_desc < 10) OR ($str_desc > 2400)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['pdmbbac'];
            }else if ((empty($bn_name)) OR ($str_name < 3) OR ($str_name > 35)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['ttmbnlt'];
            }else if (!preg_match('/^[-a-zA-Z_]+$/i', $bn_name)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['olanwas'];
            }else if (isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['ptaexis'];
            }else if ((empty($bn_cat_s)) OR empty($bn_sc_cat)) {
            $bn_notvalid = "notvalid";
            $_SESSION['snotvalid'] = $lang['ymsac'];
            }





		    if(isset($bn_notvalid) AND ($bn_notvalid == "notvalid")){
		   $_SESSION['sname']    = $_POST['name'];
           $_SESSION['sdesc']    = $_POST['desc'];
           $_SESSION['svnbr']    = $_POST['vnbr'];
           $_SESSION['scat_s']   = $_POST['cat_s'];
           $_SESSION['ssc_cat']  = $_POST['sc_cat'];
           $_SESSION['stxt']     = $_POST['txt'];
           $_SESSION['slinkzip'] = $_POST['linkzip'];
           $_SESSION['simg']     = $_POST['img'];
            header("Location: {$url_site}/add_store");
           }else{
          $insstr = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$insstr->bindParam(":name",      $bn_name);
            $insstr->bindParam(":o_valuer",  $bn_desc);
            $insstr->bindParam(":o_type",    $o_type);
            $insstr->bindParam(":o_parent",  $bn_uid);
            $insstr->bindParam(":o_order",   $bn_pts);
            $insstr->bindParam(":o_mode",    $bn_img);
            if($insstr->execute()){
              $bn_tid = $db_con->lastInsertId();
              $bn_cat    = "0";
              $bn_statu  = "1";
              $stmsb = $db_con->prepare("INSERT INTO forum (uid,name,txt,cat,statu)
            VALUES(:uid,:name,:txt,:cat,:statu)");
			$stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt );
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":statu", $bn_statu);
            if($stmsb->execute()){
              $bn_sid = $db_con->lastInsertId();
              $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsbs->bindParam(":uid",  $bn_uid);
            $stmsbs->bindParam(":opm",  $bn_type);
            $stmsbs->bindParam(":a_da", $bn_time);
            $stmsbs->bindParam(":ptdk", $bn_sid);
            if($stmsbs->execute()){
              $f_type  = "store_file";
              $f_order   = "0";
              $f_vu    = "0";
            $insstfl = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$insstfl->bindParam(":name",      $bn_vnbr);
            $insstfl->bindParam(":o_valuer",  $bn_desc);
            $insstfl->bindParam(":o_type",    $f_type);
            $insstfl->bindParam(":o_parent",  $bn_tid);
            $insstfl->bindParam(":o_order",   $f_order);
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
              $c_type    = "store_type";
              $c_valuer  = "";

            $insstcat = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$insstcat->bindParam(":name",      $bn_cat_s);
            $insstcat->bindParam(":o_valuer",  $c_valuer);
            $insstcat->bindParam(":o_type",    $c_type);
            $insstcat->bindParam(":o_parent",  $bn_tid);
            $insstcat->bindParam(":o_order",   $bn_sid);
            $insstcat->bindParam(":o_mode",    $bn_sc_cat);
            if($insstcat->execute()){
            header("Location: {$url_site}/producer/{$bn_name}");
                  }
                   }
                    }
                     }
                      }
                       }
                        }
                         }
                          }else{
                            echo"404";
                           }
?>