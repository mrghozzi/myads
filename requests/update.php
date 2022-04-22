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

   if(isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))
{
if(isset($_GET['v']) AND ($_GET['v']=="2-3-x")){
if(isset($_GET['admin'])){ header("Location: ../admincp?updates"); }
}else{
 if(isset($_GET['admin'])){ header("Location: ../admincp?updates"); }
}
}else {  header("Location: 404");  }

?>