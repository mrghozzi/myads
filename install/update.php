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

if(isset($_GET['v']) AND ($_GET['v']=="2-3-x")){
$q1=$db_con->prepare("INSERT INTO options (id, name, o_valuer, o_type, o_parent, o_order, o_mode) VALUES (NULL, 'script', '0', 'storecat', '0', '0', 'script'), (NULL, 'plugins', '0', 'storecat', '0', '0', 'plugins'), (NULL, 'templates', '0', 'storecat', '0', '0', 'templates'), (NULL, 'blogs', '0', 'scriptcat', '0', '0', 'blogs'), (NULL, 'cms', '0', 'scriptcat', '0', '0', 'cms'), (NULL, 'forums', '0', 'scriptcat', '0', '0', 'forums'), (NULL, 'socialnetwor', '0', 'scriptcat', '0', '0', 'socialnetwor'), (NULL, 'admanager', '0', 'scriptcat', '0', '0', 'admanager'), (NULL, 'games', '0', 'scriptcat', '0', '0', 'games'), (NULL, 'ecommerce', '0', 'scriptcat', '0', '0', 'ecommerce'), (NULL, 'educational', '0', 'scriptcat', '0', '0', 'educational'), (NULL, 'directory', '0', 'scriptcat', '0', '0', 'directory'), (NULL, 'others', '0', 'scriptcat', '0', '0', 'others')" );
$q1->execute();
$q2=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES ('6', '<!-- MyAds code begin -->');" );
$q2->execute();
$q3=$db_con->prepare("ALTER TABLE `setting` ADD `e_links` INT(15) NOT NULL DEFAULT '1' AFTER `a_not`;" );
$q3->execute();
$q4=$db_con->prepare("ALTER TABLE `f_cat` ADD `txt` TEXT NOT NULL AFTER `icons`, ADD `ordercat` INT(15) NOT NULL AFTER `txt`" );
$q4->execute();
if(isset($q1)) {
 $echoup = "<p style='color:#04B404' >Update Table 'options'</p>";
}else{
 $echoup = "<p style='color:#FF0000' >Update Table '<b>options</b>'</p>";
}
if(isset($q2)) {
 $echoup = $echoup."<br /><p style='color:#04B404' >Update Table 'ads'</p>";
}else{
 $echoup = $echoup."<br /><p style='color:#FF0000' >Update Table '<b>ads</b>'</p>";
}
if(isset($q3)) {
 $echoup = $echoup."<br /><p style='color:#04B404' >Update Table 'setting'</p>";
}else{
 $echoup = $echoup."<br /><p style='color:#FF0000' >Update Table '<b>setting</b>'</p>";
}
if(isset($q4)) {
    $echoup = $echoup."<br /><p style='color:#04B404' >Update Table 'f_cat'</p>";
   }else{
    $echoup = $echoup."<br /><p style='color:#FF0000' >Update Table '<b>f_cat</b>'</p>";
   }
}else if(isset($_GET['v']) AND ($_GET['v']=="3-1-0")){
$q1=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES ('6', '<!-- MyAds code begin -->');" );
$q1->execute();
$q2=$db_con->prepare("ALTER TABLE `setting` ADD `e_links` INT(15) NOT NULL DEFAULT '1' AFTER `a_not`;" );
$q2->execute();
$q3=$db_con->prepare("ALTER TABLE `f_cat` ADD `txt` TEXT NOT NULL AFTER `icons`, ADD `ordercat` INT(15) NOT NULL AFTER `txt`" );
$q3->execute();
if(isset($q1)) {
 $echoup = "<p style='color:#04B404' >Update Table 'ads'</p>";
}else{
 $echoup = "<p style='color:#FF0000' >Update Table '<b>ads</b>'</p>";
}
if(isset($q2)) {
 $echoup = $echoup."<br /><p style='color:#04B404' >Update Table 'setting'</p>";
}else{
 $echoup = $echoup."<br /><p style='color:#FF0000' >Update Table '<b>setting</b>'</p>";
}
if(isset($q3)) {
    $echoup = $echoup."<br /><p style='color:#04B404' >Update Table 'f_cat'</p>";
   }else{
    $echoup = $echoup."<br /><p style='color:#FF0000' >Update Table '<b>f_cat</b>'</p>";
   }
}else{
 $echoup = "<p>You have the latest version</p>";
}

?>