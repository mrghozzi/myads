<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "../dbconfig.php";
header('Content-type: text/html; charset=utf-8');
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
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
  if(isset($_COOKIE['admin'])){
    if(isset($_GET['id'])){

          $o_type = "box_widget" ;
          $stmtst=$db_con->prepare("DELETE FROM options WHERE ( o_type=:o_type AND id=:id ) ");
	      $stmtst->execute(array(':o_type'=>$o_type,':id'=>$_GET['id']));
          header("Location: {$url_site}/admincp?widgets");

}else{   echo "400";  }
}else{   echo "401";  }
}else{   echo "404";  }
?>