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
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lang_site  = $ab['lang'];
        $url_site   = $ab['url'];
        if (isset($_COOKIE["lang"])) {
        $lng=$_COOKIE["lang"] ;
                              } else {
        $lng        = $lang_site ;
                              }
include "../content/languages/$lng.php";
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if(isset($vrf_License)=="65fgh4t8x5fe58v1rt8se9x"){
  if(isset($_GET['id'])){
    if(isset($_POST['comment'])){
           $bn_time = time();
           $bn_cmnt = $_POST['comment'];
           $bn_rid = $_GET['id'];
           if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
           $bn_uid = $_SESSION['user'];
              $bn_name = "message";
            $stmsb = $db_con->prepare("INSERT INTO messages (name,us_env,us_rec,msg,time)
            VALUES(:name,:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":name", $bn_name);
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_cmnt);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_rid);
            if($stmsb->execute()){
             echo "execute" ;
         	}else{ echo"err"; }




    }else{ echo"404"; }
     }else{ echo"404"; }
 }else{ echo"404"; }
?>