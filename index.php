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