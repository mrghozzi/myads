<?php
if($s_st=="buyfgeufb"){ 
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$_GET['t'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($sucat['id'])) {

$catdid=$sucat['id'];
$catdname=$sucat['name'];
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);

$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);
$catusc = $db_con->prepare("SELECT *  FROM f_cat WHERE  id='{$sucat['cat']}'");
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);
$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE s_type IN (2,4) AND tp_id ='{$catdid}' " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);

if($susat['s_type'] == 4) {
$sscatust = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'service' AND `o_parent` =".$catdid );
$sscatust->execute();
$srvat=$sscatust->fetch(PDO::FETCH_ASSOC);
}

$time_stt=convertTime($susat['date']);
$comtxt = preg_replace('/[ˆ]+([A-Za-z0-9-_]+)/', '<b>$1</b>', $sucat['txt'] );
$comtxt = preg_replace('/[~]+([A-Za-z0-9-_]+)/', '<i>$1</i>', $comtxt );
$comtxt = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt );


  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="inbox-section">
                    <div class="inbox-grids">
						<div class="col-md-3 inbox-grid">
                        <a href="<?php url_site();  ?>/u/<?php echo $catuss['id']; ?>" >
							<div class="grid-inbox">
								<div class="inbox-top">
									<div class="inbox-img">
										<img src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" class="img-responsive" alt="">
									</div>
										<div class="inbox-text">
										<h5><?php echo $catuss['username']; check_us($catuss['id']);  ?></h5>
								   </div>
									<div class="clearfix"></div>
								</div>
<?php if($susat['s_type'] == 4) {

 $sasrvst = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'a_service' AND `o_parent` =".$catdid." ORDER BY `id` DESC LIMIT 15 " );
$sasrvst->execute();
while($aservt=$sasrvst->fetch(PDO::FETCH_ASSOC)){
$srvatus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$aservt['name']}'");
$srvatus->execute();
$servtuss=$srvatus->fetch(PDO::FETCH_ASSOC);
echo "<a>#".$aservt['id']."</a>    ".$servtuss['username'];
}
 ?>
                                <a href="<?php url_site();  ?>/portal" class="compose" ><i class="fa fa-globe"></i> Portal</a>
								 <?php ads_site(5);  ?>
<?php }else{   ?>
                                <a href="<?php url_site();  ?>/portal" class="compose" ><i class="fa fa-globe"></i> Portal</a>
								<a href="<?php url_site();  ?>/forum" class="compose" ><i class="fa fa-comments-o"></i> <?php lang('forum');  ?></a> <br />

                                <?php ads_site(5);  ?>
           <?php } ?>
                           </div>
                           </a>
						</div>
						<div class="col-md-9 inbox-grid1">
							<div class="mailbox-content">
                            <div class="panel panel-primary">
                            <div class="panel-heading"><center><b><?php echo $sucat['name']; ?></b></center></div>
                            <?php if($susat['s_type'] == 4) {
?>
                            <div class="panel-panel-info"><center><hr><?php echo $srvat['o_valuer']; ?></center></div>
                            <?php   } ?>
  <div class="panel-body">
								<div class=" col-md-8 compose-btn">
<?php if($susat['s_type'] == 4) {
?>
<a class="btn btn-sm btn-success"  ><i class="fa fa-image"></i> Image </a>
<?php  }else{
if($catussc['id'] > 0) {  ?>
                                    <a class="btn btn-sm btn-primary" href="<?php url_site();  ?>/f<?php echo $catussc['id']; ?>" class="btn btn-sm btn-primary" ><b><i class="fa <?php echo $catussc['icons']; ?>" aria-hidden="true"></i> <?php echo $catussc['name']; ?></b></a>
<?php }  } ?>                         <?php if($susat['s_type'] == 2) {
?>
                                    <?php if((isset($uRow['id'])==isset($catuss['id'])) OR (isset($_COOKIE['admin'])==isset($hachadmin))){  ?>
                                    <a class="btn btn-sm btn-primary" href="<?php url_site();  ?>/editor/<?php echo $sucat['id']; ?>"><i class="fa fa-pencil-square-o"></i> <?php lang('edit'); ?></a>
                                    <?php }  } ?>

            <a class="btn btn-sm btn-primary" href="#old_comment"><i class="fa fa-reply"></i> Reply</a>

                                </div>
								<div class="col-md-4 text-right">
                                      <p class="date"> <?php echo $time_stt;  ?></p>
                                  </div>

                                  </div> </div>
                                  <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">
								  <div class="view-mail">
								   <?php echo $comtxt; ?>
                                    </div>
						   <div class="compose-btn pull-left">
                                  <a class="btn btn-sm btn-primary" href="#old_comment"><i class="fa fa-reply"></i> Reply</a>

                                    <p  class="btn" id="heart<?php echo $sucat['id'];  ?>">
                                    <?php
                                    $likeuscm = $db_con->prepare("SELECT  * FROM `like` WHERE uid='{$uRow['id']}' AND sid='{$catdid}' AND  type=2 " );
                                    $likeuscm->execute();
                                     $uslike=$likeuscm->fetch(PDO::FETCH_ASSOC);
                                     $likenbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE sid='{$catdid}' AND  type=2 " );
                                     $likenbcm->execute();
                                     $abdlike=$likenbcm->fetch(PDO::FETCH_ASSOC);
                                     if(isset($_COOKIE['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin) )  ){
                                       if($uslike['sid']==$catdid){
                                       echo "<a style=\"color: #FF0000;\"  href=\"javascript:void(0);\" id=\"ulike{$sucat['id']}\" ><i class=\"fa fa-heart\" style=\"color: #FF0000;\"  aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                                       <input type=\"hidden\" id=\"lval\" value=\"test_like\" />";
                                       }else{
                                       echo "<a href=\"javascript:void(0);\" id=\"like{$sucat['id']}\"   ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                                       <input type=\"hidden\" id=\"lval\" value=\"test_like\" />
                                       ";
                                       }
                                       echo "
                                              <script>
     \$(\"document\").ready(function() {
   \$(\"#like{$sucat['id']}\").click(postlike{$sucat['id']});

});

function postlike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-up' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_up&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
 \$(\"document\").ready(function() {
   \$(\"#ulike{$sucat['id']}\").click(postulike{$sucat['id']});

});

function postulike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-down' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_down&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
     </script>";
                                       }else{
                                       echo "<a href=\"javascript:void(0);\" data-toggle=\"modal\" data-target=\"#mlike{$sucat['id']}\" ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                                       <!-- //modal like {$sucat['id']} -->
              <div class=\"modal fade\" id=\"mlike{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        You do not have an account!
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                   <a href=\"{$url_site}/login\" class=\"btn btn-success\" ><i class=\"fa fa-sign-in\"></i>{$lang['login']}</a>
                        <a href=\"{$url_site}/register\" class=\"btn btn-danger\" ><i class=\"fa fa-user-plus\"></i>{$lang['sign_up']}</a>
                                    </center>
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal like {$sucat['id']} -->";
                                       }  ?>
                                    </p>

                              </div>
							</div> <div class="clearfix"></div>
                              </div>

                                  </div> </div>
                            <hr />
   <?php if(isset($_COOKIE['user'])){  ?>
                            <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">

<textarea id='comment' class='form-control'></textarea><br />
<center><button id = 'btn' class="btn btn-info" >Post Comment</button></center>
  </div>
</div>
       </div>
       <div id='old_comment'></div>
 <?php        }
 include_once('include/pagination.php');
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 30; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`f_coment` WHERE tid='{$catdid}' ORDER BY `id` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} ");
$catsum->execute();



if ($catsum->rowCount() == 0) {
    echo "<h2><center>No Coment</center></h2>";
} else {
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catuscm = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sutcat['uid']}'");
$catuscm->execute();
$catusscm=$catuscm->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($sutcat['date']);
$comment =  $sutcat['txt'] ;
$emojis = array();
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
    $emojis['name'][]=$smlssen['name'];
    $emojis['img'][]= "<img src=\"{$smlssen['img']}\" width=\"23\" height=\"23\" />";
}

 if(isset($emojis['name']) && isset($emojis['img']) ) {
         $comment = str_replace($emojis['name'], $emojis['img'], $comment);
}

$comment = preg_replace('/[ˆ]+([A-Za-z0-9-_]+)/', '<b>$1</b>', $comment);
$comment = preg_replace('/[~]+([A-Za-z0-9-_]+)/', '<i>$1</i>', $comment );
$comment = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comment );
$comment = strip_tags($comment, '<p><a><b>');
$ecomment = preg_replace("/[\r\n]*/","",$sutcat['txt']);
 ?>
       <div class=" col-md-12 inbox-grid1" id="coment<?php echo $sutcat['id'];  ?>" >
        <div class="panel panel-default cmt<?php echo $sutcat['id'];  ?>">
  <div class="panel-heading"><b><?php echo  "<a  href=\"{$url_site}/u/{$catusscm['id']}\"   ><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catusscm['img']}\" style=\"width: 35px;\" alt=\"user image\"> {$catusscm['username']} ";
            online_us($catusscm['id']);
            check_us($catusscm['id']);
            echo "</a>  " ;  ?></b><p style="text-align: right"><?php echo $time_cmt; ?></p></div>
  <div class="panel-body">
   <?php echo $comment; ?>
  </div>
  <?php if((isset($_COOKIE['user']) AND ($_COOKIE['user']==$sutcat['uid']) ) OR ((isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$uRow['pass'])))){  ?>
  <div class="bttn<?php echo $sutcat['id'];  ?>" >
  <input type="hidden" id="trashid" value="<?php echo $sutcat['id'];  ?>'>" />
  <button id = 'btntrash<?php echo $sutcat['id'];  ?>' class="btn btn-danger" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
  <button id = 'btnedit<?php echo $sutcat['id'];  ?>' class="btn btn-success edit<?php echo $sutcat['id'];  ?>" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
  </div>
  <?php }  ?>
</div>
       </div>
       <script>
    $(document).ready(function(){
        $('.edit<?php echo $sutcat['id'];  ?>').click(function(){
        $(".cmt<?php echo $sutcat['id'];  ?>").html("<form action=\"<?php url_site();  ?>/requests/f_coment.php?ed=<?php echo $sutcat['id']; ?>\" method=\"POST\"><textarea id='comment<?php echo $sutcat['id'];  ?>' name='comment' class='form-control'><?php echo $ecomment; ?> </textarea><br /><center><button type=\"submit\" name=\"btned\" id =\"btnedite<?php echo $sutcat['id'];  ?>\" class=\"btn btn-primary edite<?php echo $sutcat['id'];  ?>\" ><i class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></button></center></form>");
        });
    });
     </script>

       <script>
     $("document").ready(function() {
   $("#btntrash<?php echo $sutcat['id'];  ?>").click(btntrashComent<?php echo $sutcat['id'];  ?>);

});

function btntrashComent<?php echo $sutcat['id'];  ?>(){
    $("#trash_comment<?php echo $sutcat['id'];  ?>").html("trash comment ...");
    $.ajax({
        url : '<?php url_site();  ?>/requests/f_coment.php?trash=<?php echo $sutcat["id"];  ?>',
        data : {
            trashid : $("#trashid").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $("#coment<?php echo $sutcat['id'];  ?>").html("");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
     </script>
<?php }   echo pagination($statement,$per_page,$page); } ?>
                           </div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			</div>
     <script>
     $("document").ready(function() {
   $("#btn").click(postComent);

});

function postComent(){
    $("#old_comment").html("posting comment ...");
    $.ajax({
        url : '<?php url_site();  ?>/requests/f_coment.php?id=<?php echo $catdid; ?>',
        data : {
            comment : $("#comment").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $("#old_comment").html(result);
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
     </script>

   <script>

                   $(function(){


function objectifyForm(formArray) {//serialize data function

var returnArray = {};
for (var i = 0; i < formArray.length; i++){
returnArray[formArray[i]['name']] = formArray[i]['value'];
}
returnArray['submit'] = "Valider";
return returnArray;
}

                     $('form').on('submit', function (e) {
                       e.preventDefault();
                    var returnArray = {};
                    var getSelected = $(this).parent().find('input[name="set"]');
                    var link = "";
                    var getval = getSelected.val();

                    if(getval=="share"){
                    var typeId = $(this).parent().find('input[name="tid"]');
                     returnArray['tid'] = typeId.val();
                     var sType = $(this).parent().find('input[name="s_type"]');
                     returnArray['s_type'] = sType.val();
                     returnArray['submit'] = "Valider";
                     link="<?php url_site();  ?>/requests/share.php" ;
                        }else if(getval=="delete"){
                    var typeId = $(this).parent().find('input[name="did"]');
                    returnArray['did'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/delete.php" ;
                    alert(link);
                        }else if(getval=="Publish"){
                    var typeId = $(this).parent().find('input[name="name"]');
                    returnArray['name'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="categ"]');
                    returnArray['categ'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/status.php" ;
                    }else if(getval=="edit"){
                    var typeId = $(this).parent().find('input[name="name"]');
                    returnArray['name'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="categ"]');
                    returnArray['categ'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="tid"]');
                    returnArray['tid'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/edit_status.php" ;
                    }else if(getval=="Report"){
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                     var typeId = $(this).parent().find('input[name="tid"]');
                    returnArray['tid'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/report.php" ;
                    }
        $.ajax({
type:"POST",
data:returnArray,
url: link,
success:function(){ $(".alert-success").fadeIn();},
error: function(){ $(".alert-danger").fadeIn(); }

});
    });
});

                   </script>
<?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>
          