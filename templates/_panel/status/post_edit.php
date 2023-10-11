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
if(isset($_GET['tid']))   { $bn_id = $_GET['tid'];   }
if(isset($_GET['s_type']) AND (($_GET['s_type']==100) OR ($_GET['s_type']==4))){
 $s_type ="forum";
}else if(isset($_GET['s_type']) AND ($_GET['s_type']==1)){
 $s_type ="directory";
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$bn_id );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);

$comtxt2 = preg_replace('/ #([^\s]+)/', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $sucat['txt'] );
$comtxt1 = strip_tags($comtxt2, '<a><br><iframe>');
$comtxt=substr($comtxt1,0,600);
$comtxt3 = strip_tags($sucat['txt'], '');
$ecomtxt = preg_replace("/[\r\n]*/","",$comtxt3);
?>

<textarea class="quicktext" id="txt<?php echo $sucat['id']; ?>"  >
<?php echo $ecomtxt; ?>
</textarea>
<hr />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $sucat['id']; ?>" class="btn btn-primary" >
<?php echo $lang['spread']; ?>
</button>&nbsp;
<button  id="post_close<?php echo $sucat['id']; ?>" class="btn btn-danger" >
<?php echo $lang['close']; ?>
</button>
</div>
</center>
<div id="report<?php echo $sucat['id']; ?>" ></div>
<script>
     $("document").ready(function() {
   $("#btn_edit<?php echo $sucat['id']; ?>").click(postedit<?php echo $sucat['id']; ?>);


});

function postedit<?php echo $sucat['id']; ?>(){
  var txt<?php echo $sucat['id']; ?> = $("#txt<?php echo $sucat['id']; ?>").val();
    $("#post_form<?php echo $sucat['id']; ?>").html("posting edit ...<div id='report<?php echo $sucat['id']; ?>' ></div>");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/edit_status.php?submit=submit&s_type=<?php echo $_GET['s_type']; ?>&tid=<?php echo $sucat['id']; ?>',
        data : {
            txt : txt<?php echo $sucat['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {

                $("#post_form<?php echo $sucat['id']; ?>").html(result);
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
</script>
<script>
$('#post_close<?php echo $sucat['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").html("<?php echo $comtxt ?><div id='report<?php echo $sucat['id']; ?>' ></div>");
        });
</script>
<?php } ?>