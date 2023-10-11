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
if(isset($_GET['sid']))   { $bn_tid = $_GET['sid'];
    $catus = $db_con->prepare("SELECT *  FROM status WHERE  id='{$bn_tid}'");
    $catus->execute();
    $catuss=$catus->fetch(PDO::FETCH_ASSOC);
    $bn_id = $catuss['tp_id'];
    $bn_s_type=$catuss['s_type'];
if(isset($bn_s_type) AND (($bn_s_type==100) OR ($bn_s_type==4)OR ($bn_s_type==2) OR ($bn_s_type==7867))){
 $s_type = "forum";
}else if(isset($bn_s_type) AND ($bn_s_type==1)){
 $s_type = "directory";
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$bn_id );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
$comtxt2 = $sucat['txt'];
if(isset($bn_s_type) AND ($bn_s_type==100)){
$comtxt1 = strip_tags($comtxt2, '<br><iframe>');
}else{
$comtxt1 = strip_tags($comtxt2, '<br>');
}
$comtxt3 = preg_replace('/ #([^\s]+)/', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt1 );
$comtxt=substr($comtxt3,0,600);

?>
<h4><?php echo $lang['aysywtd']; ?>&nbsp;"<?php echo $sucat['name']; ?>"?</h4>
<center>
<input type="hidden" id="trashid<?php echo $catuss['id'];  ?>" value="<?php echo $catuss['id'];  ?>" />
<div class="btn-group">
<button  id="btn_trash<?php echo $catuss['id']; ?>" class="btn btn-danger" >
<?php echo $lang['delete']; ?>
</button>&nbsp;
<button  id="post_close<?php echo $catuss['id']; ?>" class="btn btn-secondary" >
<?php echo $lang['close']; ?>
</button>
</div>
</center>
<div id="report<?php echo $sucat['id']; ?>" ></div>
<script>
     $("document").ready(function() {
   $("#btn_trash<?php echo $catuss['id']; ?>").click(posttrash<?php echo $catuss['id']; ?>);


});

function posttrash<?php echo $catuss['id']; ?>(){
  var trashid<?php echo $catuss['id'];  ?> = $("#trashid<?php echo $catuss['id'];  ?>").val();
    $("#post_form<?php echo $sucat['id']; ?>").html("trash post ...<div id='report<?php echo $sucat['id']; ?>' ></div>");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/delete.php?submit=submit',
        data : {
            did : trashid<?php echo $catuss['id'];  ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {

                $(".post<?php echo $catuss['id']; ?>").html("");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
</script>
<?php if(isset($_GET['dc']) AND (($_GET['dc']==1))){  ?>
<script>
$('#post_close<?php echo $catuss['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").html("<div id='report<?php echo $sucat['id']; ?>' ></div>");
        });
</script>
<?php }else if(isset($bn_s_type) AND ($bn_s_type==7867)){  ?>
<script>
$('#post_close<?php echo $catuss['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").html("<div id='report<?php echo $sucat['id']; ?>' ></div>");
        });
</script>
<?php }else{  ?>
<script>
$('#post_close<?php echo $catuss['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").html("<?php echo $comtxt ?><div id='report<?php echo $sucat['id']; ?>' ></div>");
        });
</script>
<?php } ?>
<?php
 }
  }
?>