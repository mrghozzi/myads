<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.1.x                            ##
##                     https://www.adstn.gq                        ##
##                    e-mail: admin@adstn.gq                       ##
##                                                                 ##
##                       copyright (c) 2024                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################


function  captcha(){ global  $url_site;
$str_1 = rand(1,10);
$str_2 = rand(1,10);
$AllAnd1And2 = $str_1 + $str_2;
$_SESSION['CAPCHA'] = $AllAnd1And2;

echo "<img src=\"{$url_site}/requests/imagecreatetrue.php?str1={$str_1}&str2={$str_2}\">";
}
?>