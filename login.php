<?php

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
 include "dbconfig.php";
 include "include/function.php";
 $title_page = $lang['login'];
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_POST['login']))
{
    $email = $_POST['username'];
    $upass = $_POST['pass'];

	$stmtus = $db_con->prepare("SELECT * FROM users WHERE  username=:username OR email=:email  " );
    $stmtus->bindParam(":username", $email);
    $stmtus->bindParam(":email", $email);

    $stmtus->execute();
    $row=$stmtus->fetch(PDO::FETCH_ASSOC);
    if($stmtus->rowCount() == 1)
	{
        if(password_verify($upass, $row['pass']))
	{

		$userSession = $row['id'];
        $enm = $row['username'];
		$eml = $row['email'];
        	   $o_type = "user" ;


        $nextWeek = time() + (365 * 24 * 60 * 60);
        setcookie("user", $userSession, $nextWeek);
        $l_md5=$userSession.$enm.$eml;
        $userh = md5($l_md5);
        $n_md5 = time() + (365 * 24 * 60 * 60);
        setcookie("userha", $userh, $n_md5);
        header("Location: home.php") ;

     }else{
      $bnerrMSG ="email or username or password does not exists !";
      $bn_get= "login.php?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }


    }else{
      $bnerrMSG ="email or username or password does not exists !";
      $bn_get= "login.php?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }

 }else{
   //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('login');

}else{
  template_mine('404');
 }
 template_mine('footer');
}


   
   
       ?>
