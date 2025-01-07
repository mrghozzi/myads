<?PHP

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
header('Content-type: text/html; charset=utf-8');
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
   if($_POST['ed_submit']){

           $bn_name  = $_POST['name'];
           $us_mail  = $_POST['mail'];
           $o_pass   = $_POST['o_pass'];
           $n_pass   = $_POST['n_pass'];
           $c_pass   = $_POST['c_pass'];

           $us_uid   = $_COOKIE['user'];

           if (filter_var($us_mail, FILTER_VALIDATE_EMAIL)) {
             if(empty($bn_name)){
               $bnerrMSG = "Please Enter Username.";
            $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
             header("Location: {$bn_get}") ;
                } else {
             if(empty($o_pass)){
               $stmsb = $db_con->prepare("UPDATE users SET username=:a_da,email=:opm
            WHERE id=:ertb");
			$stmsb->bindParam(":opm", $us_mail);
            $stmsb->bindParam(":a_da", $bn_name);

            $stmsb->bindParam(":ertb", $us_uid);
         	if($stmsb->execute()){
             header("Location: ../u/{$us_uid}");
                }
        }else{

       $stmtus = $db_con->prepare("SELECT * FROM users WHERE id=:id " );
       $stmtus->bindParam(":id", $us_uid);
       $stmtus->execute();
       $row=$stmtus->fetch(PDO::FETCH_ASSOC);
     if(password_verify($o_pass, $row['pass']))
	{
         if(empty($n_pass)){
               $bnerrMSG = "Please Enter New password.";
            $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
             header("Location: {$bn_get}") ;
                } else {
         if (strlen($n_pass) <= '8') {
        $bnerrMSG = "Your New password Must Contain At Least 8 Characters!";
        $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    } else {
         if($n_pass==$c_pass){
          $passhach = password_hash($c_pass, PASSWORD_DEFAULT);
          $stmsb = $db_con->prepare("UPDATE users SET username=:a_da,email=:opm,pass=:passhach
            WHERE id=:ertb");
			$stmsb->bindParam(":opm", $us_mail);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":passhach", $passhach);

            $stmsb->bindParam(":ertb", $us_uid);
         	if($stmsb->execute()){
             header("Location: ../u/{$us_uid}");
                }



        }else{
      $bnerrMSG ="New password not Confirm !";
      $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }
     }
     }
        } else{
      $bnerrMSG ="Old password does not exists !";
      $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
      header("Location: {$bn_get}") ;
    }


        }

		}
             } else {
             $bnerrMSG = "Email address '$us_mail' is considered invalid.\n";
             $bn_get= "../user?e={$us_uid}&bnerrMSG=".$bnerrMSG;
             header("Location: {$bn_get}") ;   }

    }
  }else{ echo "404"; }
?>