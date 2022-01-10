<?php
#####################################################################
##                                                                 ##
##                         My ads v2.4(+).x                        ##
##                      http://www.krhost.ga                       ##
##                     e-mail: admin@krhost.ga                     ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################


 $db_host   = 'localhost';                    //  Host Name
 $db_name   = 'myads';                       //  Data Name
 $db_user   = 'root';                       //  Data User
 $db_pass   = '';                          //  Data Pass

$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];
$dsn = "mysql:host=$db_host;dbname=$db_name";
try {
     $db_con = new \PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
        //  License Kay
   $vrf_License="65fgh4t8x5fe58v1rt8se9x";
 ?>