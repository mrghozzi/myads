<?php
include "../../../../dbconfig.php";
if(isset($_COOKIE['admin']) AND isset($_COOKIE['user']) AND isset($_GET['id']))
{
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
   $cat_id = $_GET['id'];
   $statement = "`f_cat` WHERE id =".$cat_id;
   $results =$db_con->prepare("SELECT * FROM {$statement} ");
   $results->execute();
   $wt=$results->fetch(PDO::FETCH_ASSOC);

echo "
   <div class=\"widget-box\">
    <div class=\"widget-box-content\">
    <h3>{$lang['delete']} !</h3>
    <center>
    <p>Sure to Delete ID no {$wt['id']} ? </p>
    <br />
    <b><i class=\"fa {$wt['icons']}\"></i> {$wt['name']}</b>
    <hr />
    <a  href=\"admincp?f_cat_b={$wt['id']}\" class=\"btn btn-danger\" ><i class=\"fa-solid fa-trash-can fa-shake\"></i> {$lang['delete']}</a>
    <p  id=\"close\" class=\"btn btn-secondary\" ><i class=\"fa-solid fa-circle-xmark\"></i>  {$lang['close']}</p>
    <br />
    </center>
  </div>
</div>"; ?>
<script>
    $(document).ready(function(){
        $('#close').click(function(e){
          $("#widget_block").html('');
        });
    });
</script>
<?php  }else{ echo"404"; } ?>