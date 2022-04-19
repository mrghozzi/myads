<?php
include "../../../../dbconfig.php";
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
        $url_site   = $ab['url'];
 $s_st="buyfgeufb";
  if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
   include "../../../../content/languages/$lng.php";
   include "../../../../include/convertTime.php";

 if(isset($_GET['name']) AND ($_GET['name']=="")){

 }else if(isset($_GET['id'])){
   $bid  = $_GET['id'];
   $o_type = "box_widget";
$bnwidgets = $db_con->prepare("SELECT  * FROM `options` WHERE id=:id AND o_type=:o_type " );
$bnwidgets->bindParam(":o_type", $o_type);
$bnwidgets->bindParam(":id", $bid);
$bnwidgets->execute();
$abwidgets=$bnwidgets->fetch(PDO::FETCH_ASSOC);
   $bname = $abwidgets['o_mode'];
   include "{$bname}.php";
 }else if(isset($_GET['name'])){
   $bname  = $_GET['name'];
   include "{$bname}.php";
 }


 ?>
