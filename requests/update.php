<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

   if(isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))
{
if(isset($_POST['versionnow']) AND ($_POST['versionnow']=="3-0-3")
      OR ($_POST['versionnow']=="3-0-4")
      OR ($_POST['versionnow']=="3-0-5")
      OR ($_POST['versionnow']=="3-0-6")
      ){
   if(isset($_POST['versionnow']) AND (($_POST['versionnow']=="3-0-1") OR ($_POST['versionnow']=="3-0-2"))){

   $q1=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES ('6', '<!-- MyAds code begin -->');" );
   $q1->execute();
   $q2=$db_con->prepare("ALTER TABLE `setting` ADD `e_links` INT(15) NOT NULL DEFAULT '1' AFTER `a_not`;" );
   $q2->execute();
    }
    $q3=$db_con->prepare("ALTER TABLE `f_cat` ADD `txt` TEXT NOT NULL AFTER `icons`, ADD `ordercat` INT(15) NOT NULL AFTER `txt`" );
    $q3->execute();
    
    header("Location: ../admincp?updates");
}else{
   header("Location: ../admincp?updates");
 }
}else {  header("Location: 404");  }

?>