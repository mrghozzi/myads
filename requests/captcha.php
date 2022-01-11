<?php

#####################################################################
##                                                                 ##
##                        My ads v2.x.x                            ##
##                 http://www.kariya-host.com                      ##
##                 e-mail: admin@kariya-host.com                   ##
##                                                                 ##
##                       copyright (c) 2018                        ##
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