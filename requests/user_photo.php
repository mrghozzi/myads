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
 $lng=$ab['lang'];
 $url_site   = $ab['url'];
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
if(isset($_COOKIE['user']))
{
   if(isset($_POST['submit'])){
    if(isset($_POST['av_type']) AND ($_POST['av_type']=="1")){
      $bn_uid=$_COOKIE['user'];
      if(isset($_POST['img']))   { $bn_img    = $_POST['img'];
      $stmsb = $db_con->prepare("UPDATE users SET img=:img WHERE id=:id");
      $stmsb->bindParam(":img",   $bn_img);
      $stmsb->bindParam(":id",    $bn_uid);
      if($stmsb->execute()){
          header("Location: {$url_site}/p{$bn_uid}");
        }  }else{ header("Location: {$url_site}/p{$bn_uid}"); }
    }else if(isset($_POST['av_type']) AND ($_POST['av_type']=="2")){
      $bn_uid=$_COOKIE['user'];
      if(isset($_POST['img']))   { $bn_img    = $_POST['img'];
      $o_type = "user";
      $stmsb = $db_con->prepare("UPDATE options SET o_mode=:img WHERE o_type=:o_type AND o_order=:id ");
      $stmsb->bindParam(":img",    $bn_img);
      $stmsb->bindParam(":o_type", $o_type);
      $stmsb->bindParam(":id",     $bn_uid);
      if($stmsb->execute()){
          header("Location: {$url_site}/p{$bn_uid}");
        }  }else{ header("Location: {$url_site}/p{$bn_uid}"); }
    }else{ echo"400"; }

 }else{ echo"400"; }
 }else{ echo"401"; }
 }else{ echo"404"; }
?>