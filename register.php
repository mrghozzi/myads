<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
 include "dbconfig.php";
 include "include/function.php";
 $title_page = $lang['p_sign_up'];
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_POST['submit']))
{
    if(isset($_POST['username']))  { $usern = $_POST['username']; }
    if(isset($_POST['email']))     { $email = $_POST['email'];    }
    if(isset($_POST['pass1']))     { $upas1 = $_POST['pass1'];    }
    if(isset($_POST['pass2']))     { $upas2 = $_POST['pass2'];    }
    if(isset($_POST['capt']))      { $capt  = $_POST['capt'];     }

             if((int)isset($_SESSION['CAPCHA'])){
             $capt_sess=$_SESSION['CAPCHA'];
             }else{
              $capt_sess="no";
             }
             if($capt==$capt_sess){
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (strlen($upas1) <= '8') {
        $bnerrMSG = "Your password Must Contain At Least 8 Characters!";
        $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    } else {
    if($upas1==$upas2){
	$stmtus = $db_con->prepare("SELECT COUNT(id) as nbr FROM users WHERE username=:usern OR email=:email " );
    $stmtus->bindParam(":email", $email);
    $stmtus->bindParam(":usern", $usern);
    $stmtus->execute();
    $row=$stmtus->fetch(PDO::FETCH_ASSOC);

    if($row['nbr']==0)
	{
     $passhach = password_hash($upas2, PASSWORD_DEFAULT);
     $stmtq = $db_con->prepare("INSERT INTO users(username,email,pass)
        VALUES(:usern,:email,:passhach)");
     $stmtq->bindParam(":email", $email);
     $stmtq->bindParam(":usern", $usern);
     $stmtq->bindParam(":passhach", $passhach);
      if($stmtq->execute())
		{
          $stmtref = $db_con->prepare("SELECT * FROM users WHERE username=:usern AND email=:email AND pass=:passhach " );
        $stmtref->bindParam(":email", $email);
        $stmtref->bindParam(":usern", $usern);
        $stmtref->bindParam(":passhach", $passhach);
        $stmtref->execute();
		$refrow=$stmtref->fetch(PDO::FETCH_ASSOC);
		 if(isset($_COOKIE['ref'])){

        $lasteid=$refrow['id'];
        $d_dkt = date('Y-m-d');
        $dfgrdj= $_COOKIE['ref'];
        $stmtqr = $db_con->prepare("INSERT INTO referral(uid,ruid,date)
        VALUES('$dfgrdj','$lasteid','$d_dkt')");
        if($stmtqr->execute())
		{
            $o_type  = "hest_pts";
            $bn_desc = "10";
            $bn_name = "referal";
            $bn_uid = $dfgrdj;
            $bn_sid = $lasteid;
            $o_time = time();
            $inshest = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
			$inshest->bindParam(":name",      $bn_name);
            $inshest->bindParam(":o_valuer",  $bn_desc);
            $inshest->bindParam(":o_type",    $o_type);
            $inshest->bindParam(":o_parent",  $bn_uid);
            $inshest->bindParam(":o_order",   $bn_sid);
            $inshest->bindParam(":o_mode",    $o_time);
            if($inshest->execute()){ }
        $stmsb = $db_con->prepare("UPDATE users SET pts=pts+10 ,vu=vu+10 ,nvu=nvu+10 ,nlink=nlink+10
            WHERE id=:ertb");
			 $stmsb->bindParam(":ertb", $dfgrdj);
         	if($stmsb->execute()){
            setcookie("ref", "", time()-3600);
        }}}
         $o_type = "user" ;
 $uid = $refrow['id'];
  $name = $refrow['username'];
  $o_mode = "0";
   $string = urlencode(mb_ereg_replace('\s+', '-', $name));
   $string = str_replace(array(' '),array('-'),$string);
   $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES (:name,:a_daf,:o_type,:dptdk,:uid,:o_mode) ");
	     $ostmsbs->bindParam(":uid", $uid);
            $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":a_daf", $string);
            $ostmsbs->bindParam(":dptdk", $o_mode);
            $ostmsbs->bindParam(":name", $name);
             $ostmsbs->bindParam(":o_mode", $o_mode);
            if($ostmsbs->execute()){
              header("Location: login") ;
         	}

		}
		else{
      $bnerrMSG ="Problem 502 !";
      $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
       }


    }else{
      $bnerrMSG ="sorry username or email already taken !";
      $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }
    }else{
      $bnerrMSG ="password not Confirm !";
      $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }
    }
    } else {
     $bnerrMSG = "Email address '$email' is considered invalid.\n";
      $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
     }
    } else {
     $bnerrMSG = "verification code is considered invalid.\n";
      $bn_get= "register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
     }

 }else{
   include "requests/captcha.php";
   //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('signup');

}else{
  template_mine('404');
 }
 template_mine('footer');
}

   ?>
