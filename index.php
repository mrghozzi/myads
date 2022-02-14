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
 header_template();
 template('home');
 footer_template();
  }else{
  print  401 ;
  }

?>