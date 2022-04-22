<?php

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
session_start();

if(!isset($_SESSION['user']))
{
	@header("Location: index.php");
}
else if(isset($_SESSION['user'])!="")
{
	header("Location: index.php");
}

if(isset($_GET['logout']))
{
	session_destroy();
	unset($_SESSION['user']);
    setcookie("userha", "", time()-3600, "/");
    setcookie("user", "", time()-3600, "/");
    setcookie("userha", "", time()-3600);
    setcookie("user", "", time()-3600);
    setcookie("admin", "", time()-3600);
	@header("Location: index.php");
}
?>