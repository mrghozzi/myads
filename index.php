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


require "dbconfig.php";
require "include/function.php";
 if(isset($s_st) AND ($s_st=="buyfgeufb")){
 template_mine('header');
 if(isset($_COOKIE['user']) =="")
{
  template_mine('index');
}else{
  template_mine('home');
}

 template_mine('footer');
  }else{
  print  401 ;
  }

?>