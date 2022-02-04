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

if(isset($_GET['v']) AND ($_GET['v']=="2-3-x")){
$q1=$db_con->prepare("INSERT INTO options (id, name, o_valuer, o_type, o_parent, o_order, o_mode) VALUES (NULL, 'script', '0', 'storecat', '0', '0', 'script'), (NULL, 'plugins', '0', 'storecat', '0', '0', 'plugins'), (NULL, 'templates', '0', 'storecat', '0', '0', 'templates'), (NULL, 'blogs', '0', 'scriptcat', '0', '0', 'blogs'), (NULL, 'cms', '0', 'scriptcat', '0', '0', 'cms'), (NULL, 'forums', '0', 'scriptcat', '0', '0', 'forums'), (NULL, 'socialnetwor', '0', 'scriptcat', '0', '0', 'socialnetwor'), (NULL, 'admanager', '0', 'scriptcat', '0', '0', 'admanager'), (NULL, 'games', '0', 'scriptcat', '0', '0', 'games'), (NULL, 'ecommerce', '0', 'scriptcat', '0', '0', 'ecommerce'), (NULL, 'educational', '0', 'scriptcat', '0', '0', 'educational'), (NULL, 'directory', '0', 'scriptcat', '0', '0', 'directory'), (NULL, 'others', '0', 'scriptcat', '0', '0', 'others')" );
$q1->execute();
if(isset($q1)) {
 $echoup = "<p style='color:#04B404' >CREATE TABLE 'update options'</p>";
}else{
 $echoup = "<p style='color:#FF0000' >CREATE TABLE '<b>update options</b>'</p>";
}
 if(isset($_GET['admin'])){ header("Location: ../admincp?d_install"); }
}else{
 $echoup = "<p>You have the latest version</p>";
 if(isset($_GET['admin'])){ header("Location: ../admincp?d_install"); }
}

?>