<?PHP

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
 include "dbconfig.php";
 include "include/function.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 if(isset($_GET['forgot-password']))
{
  $title_page = "Forgot your password?";
   if(isset($_POST['rpassword']))
{
     $email = $_POST['username'];

    $stmtus = $db_con->prepare("SELECT * FROM users WHERE  username=:username OR email=:email  " );
    $stmtus->bindParam(":username", $email);
    $stmtus->bindParam(":email", $email);

    $stmtus->execute();
    $row=$stmtus->fetch(PDO::FETCH_ASSOC);
    if($stmtus->rowCount() == 1)
	{

     $bn_uid = $row['id'];
     $o_type ="recover-password";
     $bn_time = time();
     $bn_cmnt = "";
     $bn_tid  = "";
     $name = md5($bn_time);
     $enm = $row['username'];

     $stmsb = $db_con->prepare("INSERT INTO options (name,o_type,o_order,o_parent,o_valuer,o_mode)
     VALUES(:name,:o_type,:o_order,:o_parent,:o_valuer,:o_mode)");
	 $stmsb->bindParam(":name",     $name);
	 $stmsb->bindParam(":o_type",   $o_type);
	 $stmsb->bindParam(":o_order",  $bn_uid);
     $stmsb->bindParam(":o_valuer", $bn_cmnt);
     $stmsb->bindParam(":o_mode",   $bn_time);
     $stmsb->bindParam(":o_parent", $bn_tid);
     if($stmsb->execute()){
       $to      = $row['email'];
       $subject = 'To reset your password';
       $message = '<p>Hello {$enm}, <br /> <br /> To reset your password <a href="{$url_site}/recover?{recover}">Click Here</a> <br /><br /> {$title_s} Team, <br /> Regards,</p>';
       $headers = 'From: {$mail_site}' . "\r\n" .
                  'Reply-To: {$mail_site}' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();
       mail($to, $subject, $message, $headers);

      $bnerrMSG = "Email sent to ".$row['email'];
      $bn_get= "login.php?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
     }


    }else{
      $bnerrMSG ="email or username does not exists !";
      $bn_get= "login.php?forgot-password&bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }

}else{
   //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('forgot-password');

}else{
  template_mine('404');
 }
 template_mine('footer');
 }
}else{
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
  $title_page = $lang['login'];
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
}


   
   
       ?>
