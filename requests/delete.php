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
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if($_POST['submit']){

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