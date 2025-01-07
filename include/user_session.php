<?PHP

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
if($s_st=="buyfgeufb"){ 
  
 //  user Session
 if(isset($_COOKIE['userha'])!="")
{
  session_start();
  $_SESSION['user']=$_COOKIE['user'];

 $stmus = $db_con->prepare("SELECT * FROM users WHERE id=:user ");
 $stmus->bindParam(":user", $_SESSION['user']);
 $stmus->execute();
 $uRow=$stmus->fetch(PDO::FETCH_ASSOC);
 $bn_online = time();
 $conf_us_log=$uRow['id'].$uRow['username'].$uRow['email'];
 $md5_cook_us=$_COOKIE['userha'];
 $stmsbus = $db_con->prepare("UPDATE users SET online=:online WHERE id=:user  ");
            $stmsbus->bindParam(":user", $uRow['id']);
            $stmsbus->bindParam(":online",  $bn_online);
            if($stmsbus->execute()){

         	}
 if(md5($conf_us_log)==$md5_cook_us){ $_SESSION['user']=$uRow['id'];}
 else{ header("Location: {$url_site}/logout?logout"); }
 }
 // admin
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;

}else{ echo"404"; }
 ?>