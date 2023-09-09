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
include"../dbconfig.php";
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
if(isset($_POST['view'])){
if($_POST["view"] != '')
{
    $stntfb = $db_con->prepare("UPDATE notif SET state= 3
            WHERE state= 1 AND uid=:uid");
            $stntfb->bindParam(":uid", $_COOKIE['user']);
            $stntfb->execute();
}
}
 }else{ echo"404"; }
?>