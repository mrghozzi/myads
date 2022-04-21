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
if($s_st=="buyfgeufb"){ 

 //  user Session
session_start();
 if(isset($_COOKIE['userha'])!="")
{

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
class session{
  private static function _RegenerateId()
    {
        session_regenerate_id(true);
    }
  public static function init(){
   session_start();
  }
  public static function set($key,$value){
    $_SESSION[$key]=$value;
    session::_RegenerateId();
  }
  public static function get($key, $secondKey = false){
    session::_RegenerateId();
    if(isset($_SESSION[$key]))
    return $_SESSION[$key];
        if ($secondKey == true)
        {
            if (isset($_SESSION[$key][$secondKey]))
            return $_SESSION[$key][$secondKey];
        }
        else
        {
            if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        }

  }
  public static function destroy(){
    session_destroy();
  }
} }
 // admin
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;

}else{ echo"404"; }
 ?>