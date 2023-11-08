<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################


include "dbconfig.php";
include "include/function.php";
$title_page = $lang['home'];
 if(isset($_POST['bt_pts'])){

           $le_name = $_POST['pts'];
           $le_type = $_POST['to'];

           if($le_name<0){  $le_name = 0;  } else if (!is_numeric($le_name)) {    $le_name = 0;       }              // Error


        if(isset($uRow['pts']) AND ($uRow['pts']<$le_name)){
         $errMSG= $lang['tnopmtrnon']." : ".$uRow['pts']."</b>";
       }else if($le_name=="0"){
         $errMSG= $lang['cnc0p'];
       }
       if(isset($errMSG))
       {
    $le_get= "?errMSG=".$errMSG;
       }
           if(!isset($errMSG))
		{
            $o_type  = "hest_pts";
            $bn_desc = "-".$le_name;
            if($le_type=="link"){
              $bn_name = "tostads";
              }else if($le_type=="banners"){
              $bn_name = "towthbaner";
              }else if($le_type=="exchv"){
              $bn_name = "toexchvisi";
              }
            $bn_uid = $uRow['id'];
            $bn_sid = "0";
            $o_time = time();
            $inshest = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$inshest->bindParam(":name",      $bn_name);
            $inshest->bindParam(":o_valuer",  $bn_desc);
            $inshest->bindParam(":o_type",    $o_type);
            $inshest->bindParam(":o_parent",  $bn_uid);
            $inshest->bindParam(":o_order",   $bn_sid);
            $inshest->bindParam(":o_mode",    $o_time);
            if($inshest->execute()){
          if($le_type=="link"){
            $le_go = $le_name/2;
            $stms = $db_con->prepare("UPDATE users SET nlink=nlink+:a_da,pts=pts-:pts
            WHERE id=:uid");
			$stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
            $stms->bindParam(":a_da", $le_go);
            $stms->bindParam(":uid", $uRow['id']);
         	if($stms->execute()){
         	  $comment = str_replace("[le_go]", $le_go, $lang['phbdp']);
              $comment = str_replace("[le_name]", $le_name, $comment);
         	  $MSG=$comment;
         	  $le_get= "?MSG=".$MSG;
             header("Location: home.php{$le_get}");
         	}}else if($le_type=="banners"){
            $le_go = $le_name/2;
            $stms = $db_con->prepare("UPDATE users SET nvu=nvu+:a_da,pts=pts-:pts
            WHERE id=:uid");
			$stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
            $stms->bindParam(":a_da", $le_go);
            $stms->bindParam(":uid", $uRow['id']);
         	if($stms->execute()){
         	  $comment = str_replace("[le_go]", $le_go, $lang['phbdb']);
              $comment = str_replace("[le_name]", $le_name, $comment);
         	  $MSG=$comment;
         	  $le_get= "?MSG=".$MSG;
             header("Location: home.php{$le_get}");
         	}}else if($le_type=="exchv"){
            $le_go = $le_name/4;
            $stms = $db_con->prepare("UPDATE users SET vu=vu+:a_da,pts=pts-:pts
            WHERE id=:uid");
			$stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
            $stms->bindParam(":a_da", $le_go);
            $stms->bindParam(":uid", $uRow['id']);
         	if($stms->execute()){
         	  $comment = str_replace("[le_go]", $le_go, $lang['phbdv']);
              $comment = str_replace("[le_name]", $le_name, $comment);
         	  $MSG=$comment;
         	  $le_get= "?MSG=".$MSG;
             header("Location: home.php{$le_get}");
         	}}
              }
    }else{
      header("Location: home.php{$le_get}");
    }
     }

 template_mine('header');
 if(isset($_COOKIE['user']) =="")
{
 template_mine('404');
}else{
 template_mine('home');
 }
 template_mine('footer');


?>

