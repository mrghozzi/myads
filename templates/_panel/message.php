
<?php if($s_st=="buyfgeufb"){
$msgdid = $uRow['id'];
$msgeid = $_GET['m'];
$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$_GET['m']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);  ?>
		<div id="page-wrapper">
			<div class="main-page">
               <div class="col-md-4 table-grid">
               <a href="<?php url_site();  ?>/u/<?php echo $catuss['id']; ?>" >
               <div class="panel panel-widget">
				  <div class="inbox-top">
									<div class="inbox-img">
										<img src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" class="img-responsive" alt="">
									</div>
										<div class="inbox-text">
										<h5><?php echo $catuss['username'];  ?></h5>
									 </div>
									<div class="clearfix"></div>
								</div>
                                   </div>  </a>
                    </div>
             <hr /><br />
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">



                            <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">

<textarea id='comment' class='form-control'></textarea><br />
<center><button id = 'btn' class="btn btn-info" ><i class="fa fa-paper-plane"></i></button>
<button type="button" class="btn btn-lg btn-warning" data-toggle="popover" data-html="true"
title="<span style='color:#FFCC00'><i class='fa fa-smile-o' ></i></span> Emojis"
data-content="
<?php
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
$c = 1;
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
    echo "<span class='label label-default'><b>".$smlssen['name']."</b> = ";
    echo  "<img src='{$smlssen['img']}' width='23' height='23' /></span>";
    if($c == 3){ echo "<br />"; $c = 0; }else{ echo " | "; }
$c++; } ?>
"><i class="fa fa-smile-o" aria-hidden="true"></i></button>
<button type="button" class="btn btn-lg btn-default" data-toggle="popover" data-html="true"
title="<b>Variables when previewing text.</b>"
data-content="@text = <b>text</b> <p><span style='color:#A9A9A9'>// Bold</span></p><br />
              $text = <s>text</s> <p><span style='color:#A9A9A9'>// Strikethrough</span></p><br />
              #text = <i>text</i> <p><span style='color:#A9A9A9'>// Italic</span></p><br />
 "><i class="fa fa-question-circle" aria-hidden="true"></i></button></center>
  </div>
</div>
       </div>
       <div id='new_msg'></div>
 <?php
 include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 30; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`messages` WHERE (us_env='{$msgdid}' AND us_rec='{$msgeid}') OR (us_env='{$msgeid}' AND us_rec='{$msgdid}') ORDER BY `id_msg` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$catsum->execute();
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sutcat['us_env']}'");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
  $bn_state="0";
  $bn_id = $sutcat['id_msg'];
  $stmsb = $db_con->prepare("UPDATE messages SET state=:state
            WHERE id_msg=:id AND us_rec=:uid");
            $stmsb->bindParam(":uid",   $msgdid);
            $stmsb->bindParam(":state", $bn_state);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){

         	}
$comment =  $sutcat['msg'] ;
$emojis = array();
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
    $emojis['name'][]=$smlssen['name'];
    $emojis['img'][]="<img src=\"{$smlssen['img']}\" width=\"23\" height=\"23\" />";
}
 if(isset($emojis['name']) && isset($emojis['img']) ) {
         $comment = str_replace($emojis['name'], $emojis['img'], $comment);
}

$comment = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<b>$1</b>', $comment );
$comment = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<i>$1</i>', $comment );
$comment = preg_replace('/[$]+([A-Za-z0-9-_]+)/', '<s>$1</s>', $comment );
 $time_cmt=convertTime($sutcat['time']);
 ?>
       <div class=" col-md-12 inbox-grid1">
       <?php if($sutcat['state']=="1"){ ?>
        <div class="panel panel-warning">
        <?php }else{ ?>
        <div class="panel panel-info">
        <?php } ?>
  <div class="panel-heading"><b><?php echo $catussen['username']."  "; online_us($catussen['id']); ?></b></div>
  <div class="panel-body">
   <?php echo $comment; ?>
   <hr />
   <p style="text-align: right"><?php echo $time_cmt; ?></p>
  </div>
</div>
       </div>
<?php }   echo pagination($statement,$per_page,$page);  ?>

                </div>	<div class="clearfix"></div>
				</div> 
     <script>
       $(function () {
  $('[data-toggle="popover"]').popover()
});
     $("document").ready(function() {
   $("#btn").click(postComent);

});

function postComent(){
    $("#new_msg").html("posting ...");
    $.ajax({
        url : '<?php url_site();  ?>/requests/msg.php?id=<?php echo $msgeid; ?>',
        data : {
            comment : $("#comment").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $("#new_msg").html(result);
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
     </script>
      <script>
    $(function ()
    {
        $('#comment').keyup(function (e){
            if(e.keyCode == 13){
                var curr = getCaret(this);
                var val = $(this).val();
                var end = val.length;

                $(this).val( val.substr(0, curr) + '<br>' + val.substr(curr, end));
            }

        })
    });

    function getCaret(el) {
        if (el.selectionStart) {
            return el.selectionStart;
        }
        else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange(),
            rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }

</script>
				</div> 	<div class="clearfix"></div>
				</div>
<?php }else{ echo"404"; }  ?>