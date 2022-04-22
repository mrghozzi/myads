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

 if(isset($_GET['id'])){
   $bid  = $_GET['id'];
   $o_type = "box_widget";
$bnwidgets = $db_con->prepare("SELECT  * FROM `options` WHERE id=:id AND o_type=:o_type " );
$bnwidgets->bindParam(":o_type", $o_type);
$bnwidgets->bindParam(":id", $bid);
$bnwidgets->execute();
$abwidgets=$bnwidgets->fetch(PDO::FETCH_ASSOC);
 ?>
<center>
<?php echo $lang['aysywtd']; ?><br /><?php echo $abwidgets['name']; ?>
</center>
<hr />
<center>
<a class="btn btn-danger" href="<?php echo $url_site;  ?>/requests/delete_widgets.php?id=<?php echo $bid;  ?>" ><?php echo $lang['yes'];  ?></a>
<a class="btn btn-info" id="close" ><?php echo $lang['no'];  ?></a>
</center>

<script>
    $(document).ready(function(){
        $('#close').click(function(e){
          $("#widget_block").load('<?php echo $url_site;  ?>/templates/_panel/admin/widgets/w_block.php?id=<?php echo $bid;  ?>');
        });
    });
</script>
<?php }  ?>
