<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2019                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "dbconfig.php";
include "include/function.php";
 $title_page = $lang['list']."&nbsp;".$lang['referal'];
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('referral');
 }
 template_mine('footer');


?>

