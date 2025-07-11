<?php

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