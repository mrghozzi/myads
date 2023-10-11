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
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if($_GET['submit']){

           $bn_tid = $_POST['did'];


    $catus = $db_con->prepare("SELECT *  FROM status WHERE  id='{$bn_tid}'");
    $catus->execute();
    $catuss=$catus->fetch(PDO::FETCH_ASSOC);
    $bn_did=$catuss['tp_id'];
    $bn_s_type=$catuss['s_type'];
    if($catuss['s_type']==1){
 $s_type ="directory";
}else if($catuss['s_type']==2){
 $s_type ="forum";
}else if($catuss['s_type']==4){
 $s_type ="forum";
}else if($catuss['s_type']==100){
 $s_type ="forum";
}else if($catuss['s_type']==7867){
 $s_type ="forum";
}
    $catusd = $db_con->prepare("SELECT *  FROM {$s_type} WHERE  id='{$bn_did}'");
    $catusd->execute();
    $catussd=$catusd->fetch(PDO::FETCH_ASSOC);
    if($_COOKIE['admin']==$hachadmin){
     $bn_uid = $catuss['uid'];
           }else{
           session_start();
           $bn_uid = $_SESSION['user'];
           }
    if($catussd['uid']==$bn_uid){
    $stmt=$db_con->prepare("DELETE FROM {$s_type} WHERE id=:id AND uid=:uid ");
	$stmt->execute(array(':id'=>$bn_did,':uid'=>$bn_uid));
 if($catuss['s_type']==7867){
   $servictp = "store_type" ;
   $catustp = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$servictp}' AND o_order='{$bn_did}' ) ");
   $catustp->execute();
   $catusstp=$catustp->fetch(PDO::FETCH_ASSOC);
    $Tob      = $_SERVER['DOCUMENT_ROOT'];
    $servicst = "store" ;
    $stmtst=$db_con->prepare("DELETE FROM options WHERE ( o_type=:o_type AND id=:id AND o_parent=:uid ) ");
	$stmtst->execute(array(':o_type'=>$servicst,':id'=>$catusstp['o_parent'],':uid'=>$bn_uid));
    $sttnid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$catusstp['o_parent']." " );
    $sttnid->execute();
     while($strtnid=$sttnid->fetch(PDO::FETCH_ASSOC)){
    $servicfl = "store_file" ;
    $stmtst=$db_con->prepare("DELETE FROM options WHERE ( o_type=:o_type AND id=:id ) ");
	$stmtst->execute(array(':o_type'=>$servicfl,':id'=>$strtnid['id']));
    unlink($Tob.'/'.$strtnid['o_mode']);
     }
    $servicst = "store_type" ;
    $stmtst=$db_con->prepare("DELETE FROM options WHERE ( o_type=:o_type AND id=:id AND o_parent=:o_parent ) ");
	$stmtst->execute(array(':o_type'=>$servicst,':id'=>$catusstp['id'],':o_parent'=>$catusstp['o_parent']));
 }
    $catusdd = $db_con->prepare("SELECT *  FROM status WHERE  tp_id='{$bn_did}' AND s_type='{$bn_s_type}' ");
    $catusdd->execute();
    while($catudd=$catusdd->fetch(PDO::FETCH_ASSOC)){
    $catudid=$catudd['id'];
    $stmt=$db_con->prepare("DELETE FROM status WHERE id=:id AND uid=:uid ");
	$stmt->execute(array(':id'=>$catudid,':uid'=>$bn_uid));
    }
    }else{
    $stmt=$db_con->prepare("DELETE FROM status WHERE id=:id AND uid=:uid ");
	$stmt->execute(array(':id'=>$bn_tid,':uid'=>$bn_uid));
    }


    }
 }else{ echo"404"; }
?>