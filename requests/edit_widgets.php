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
    if(isset($_POST['submit'])){

           $bn_time    =   time();
           $gt_name    = $_GET['name'];
 if(isset($gt_name) AND ($gt_name=="widget_html")){          // widget_html
     $bn_name     = $_POST['name'];
     $bn_desc     = $_POST['txt'];
     $bn_id       = $_POST['id_w'];
     $bn_order    = $_POST['p_order'];
 }

          $o_mode = $gt_name;
          $o_type = "box_widget";
          $insstr = $db_con->prepare("INSERT INTO options (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES(:name,:o_valuer,:o_type,:o_parent,:o_order,:o_mode)");
          $insstr = $db_con->prepare("UPDATE options SET name=:name,o_valuer=:o_valuer,o_order=:o_order
            WHERE id=:id AND o_type=:o_type AND o_mode=:o_mode ");
		  $insstr->bindParam(":name",      $bn_name);
          $insstr->bindParam(":o_valuer",  $bn_desc);
          $insstr->bindParam(":o_type",    $o_type);
          $insstr->bindParam(":o_order",   $bn_order);
          $insstr->bindParam(":o_mode",    $o_mode);
          $insstr->bindParam(":id",        $bn_id);
          if($insstr->execute()){
            header("Location: {$url_site}/admincp?widgets");
           }
}else{   echo "400";  }
}else{   echo "401";  }
}else{   echo "404";  }
?>