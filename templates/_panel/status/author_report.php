<?php
include "../../../dbconfig.php";
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
        $url_site   = $ab['url'];
   include "../../../content/languages/$lng.php";

  if(isset($_COOKIE['user']))
{
 if(isset($_GET['tid']))   { 
   $bn_id = $_GET['tid'];   
if(isset($_GET['s_type']) AND (($_GET['s_type']==100) OR ($_GET['s_type']==4))){
 $s_type ="forum";
 $bn_cat  = "0";
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$bn_id );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);

?>
<hr />
<h4><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?></h4>
<br />
<textarea class="quicktext" id="txt<?php echo $sucat['id']; ?>"  ></textarea>
<hr />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $sucat['id']; ?>" class="btn btn-warning" >
<?php echo $lang['confirm']; ?>
</button>&nbsp;
<button  id="post_close<?php echo $sucat['id']; ?>" class="btn btn-danger" >
<?php echo $lang['close']; ?>
</button>
</div>
</center>
<script>
$("document").ready(function() {
   $("#btn_edit<?php echo $sucat['id']; ?>").click(postedit<?php echo $sucat['id']; ?>);
});

function postedit<?php echo $sucat['id']; ?>(){
  var txt<?php echo $sucat['id']; ?> = $("#txt<?php echo $sucat['id']; ?>").val();
    $("#report<?php echo $sucat['id']; ?>").html("posting report ...");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/report.php?submit=submit&s_type=100&tid=<?php echo $sucat['id']; ?>',
        data : {
            txt : txt<?php echo $sucat['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {

                $("#report<?php echo $sucat['id']; ?>").html("<hr /><div class='alert alert-warning' role='alert'><?php echo $lang['pending']; ?></div>");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
</script>
<script>
$('#post_close<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").html("");
        });
</script>
<?php }else{ echo "404";}
      }else{ echo "404";}
       ?>