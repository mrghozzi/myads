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
}else if(isset($_GET['v']) AND ($_GET['v']=="3-0-0")){
if(isset($_GET['admin'])){
   $q1=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES ('6', '<!-- MyAds code begin -->');" );
   $q1->execute();
   $q2=$db_con->prepare("ALTER TABLE `setting` ADD `e_links` INT(15) NOT NULL DEFAULT '1' AFTER `a_not`;" );
   $q2->execute();
   header("Location: ../admincp?updates");
   }
}else{
 if(isset($_GET['admin'])){ header("Location: ../admincp?updates"); }
}
}else {  header("Location: 404");  }

?>