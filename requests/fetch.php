<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.2.x                           ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
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