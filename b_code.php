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
include "dbconfig.php";
include "include/function.php";
 $title_page = "Banner Code";
 template_mine('header');
  if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('b_code');
 }
 template_mine('footer');


?>

