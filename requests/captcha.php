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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function  captcha(){
$str_1 = rand(1,10);
$str_2 = rand(1,10);
$AllAnd1And2 = $str_1 + $str_2;
$_SESSION['CAPCHA'] = $AllAnd1And2;
echo $str_1." + ".$str_2;
}
?>