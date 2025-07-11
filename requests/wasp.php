<?php

#####################################################################
##                                                                 ##
##                         MYads  v3.2.x                           ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

  include "../dbconfig.php";
   $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lang_site=$ab['lang'];
        $url_site   = $ab['url'];
  session_start();
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set($ab['timezone']);
}
  $bn_o_type = "ext_login_ext";
$statement = "`options` WHERE o_type=:o_type ";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->bindParam(":o_type", $bn_o_type);
$results->execute();
$wt=$results->fetch(PDO::FETCH_ASSOC);
   if(isset($_GET['code'])){
    $app_id = $wt['o_valuer']; // your application app id
	$app_secret = $wt['o_mode'];; // your application app secret
	$code = $_GET['code']; // the GET parameter you got in the callback: http://yourdomain/?code=XXX

	$get = file_get_contents("https://www.wasp.gq/authorize?app_id={$app_id}&app_secret={$app_secret}&code={$code}");
    $json = json_decode($get, true);

        if (!empty($json['access_token'])) {
	$access_token = $json['access_token']; // your access token
	$type = "get_user_data"; // or posts_data
	$get = file_get_contents("https://www.wasp.gq/app_api?access_token={$access_token}&type={$type}");
    $json = json_decode($get, true);

            if(isset($json['status']))
{
    $email = $json['user_data']['email'] ;

	$stmtus = $db_con->prepare("SELECT * FROM users WHERE  email=:email  " );
    $stmtus->bindParam(":email", $email);

    $stmtus->execute();
    $row=$stmtus->fetch(PDO::FETCH_ASSOC);
    if($stmtus->rowCount() == 1)
	{
        $userSession = $row['id'];
        $enm = $row['username'];
		$eml = $row['email'];
        $nextWeek = time() + (365 * 24 * 60 * 60);
        setcookie("user", $userSession, $nextWeek, "/");
        $l_md5=$userSession.$enm.$eml;
        $userh = md5($l_md5);
        $n_md5 = time() + (365 * 24 * 60 * 60);
        setcookie("userha", $userh, $n_md5, "/");
       header("Location: {$url_site}/portal") ;
     }else{
    $usern = $json['user_data']['username'];
    $email = $json['user_data']['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	$stmtus = $db_con->prepare("SELECT COUNT(id) as nbr FROM users WHERE email=:email " );
    $stmtus->bindParam(":email", $email);
    $stmtus->execute();
    $row=$stmtus->fetch(PDO::FETCH_ASSOC);

    if($row['nbr']==0)
	{
	  $upas2 = $_GET['code'];
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
              $userSession = $refrow['id'];
        $enm = $refrow['username'];
		$eml = $refrow['email'];
        $nextWeek = time() + (365 * 24 * 60 * 60);
        setcookie("user", $userSession, $nextWeek, "/");
        $l_md5=$userSession.$enm.$eml;
        $userh = md5($l_md5);
        $n_md5 = time() + (365 * 24 * 60 * 60);
        setcookie("userha", $userh, $n_md5, "/");
        header("Location: {$url_site}/portal") ;
                                     }
    }else{
      $bnerrMSG ="Problem 502 !";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
       }
    }else{
      $bnerrMSG ="sorry username or email already taken !";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }
    } else {
     $bnerrMSG = "Email address '$email' is considered invalid.\n";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
          }
           }
   }else{
      $bnerrMSG ="problem connexion !";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
       }
   }else{
      $bnerrMSG ="problem connexion !";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
       }
    }else{
      $bnerrMSG ="problem connexion !";
      $bn_get= $url_site."/register?bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
       }
?>