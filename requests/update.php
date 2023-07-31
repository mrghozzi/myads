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

   if(isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))
{
if(isset($_POST['versionnow']) AND (($_POST['versionnow']=="3-0-1") OR ($_POST['versionnow']=="3-0-2"))){

   $q1=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES ('6', '<!-- MyAds code begin -->');" );
   $q1->execute();
   $q2=$db_con->prepare("ALTER TABLE `setting` ADD `e_links` INT(15) NOT NULL DEFAULT '1' AFTER `a_not`;" );
   $q2->execute();
   header("Location: ../admincp?updates");

}else{
  header("Location: ../admincp?updates");
}
}else {  header("Location: 404");  }

?>