<?php
if($s_st=="buyfgeufb"){
  $gproducer =  $_GET['producer'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` ='".$gproducer."' " );
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
$sttid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." ORDER BY `o_order`  DESC " );
$sttid->execute();
$strtid=$sttid->fetch(PDO::FETCH_ASSOC);
$servictp = "store_type" ;
$catustp = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$servictp}' AND o_parent='{$strname['id']}' ) ");
$catustp->execute();
$catusstp=$catustp->fetch(PDO::FETCH_ASSOC);
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$catusstp['o_order'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($sucat['id'])) {

$catdid=$sucat['id'];
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4,7867) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);
$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);
$catusc = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_type' AND `o_parent` =".$strname['id'] );
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);

$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE s_type IN (2,4,7867) AND tp_id ='{$catdid}' " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);



$time_stt=convertTime($susat['date']);
$comtxt = preg_replace('/[ˆ]+([A-Za-z0-9-_]+)/', '<b>$1</b>', $sucat['txt'] );
$comtxt = preg_replace('/[~]+([A-Za-z0-9-_]+)/', '<i>$1</i>', $comtxt );
$comtxt = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt );
$ndfk = $strtid['id'];

                 $sdf= $strtid['o_mode'];
                 $dir_lnk_hash = $url_site."/download/".hash('crc32', $sdf.$ndfk );
                  $contfils = 0;
                  $sttnid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." " );
                  $sttnid->execute();
                  while($strtnid=$sttnid->fetch(PDO::FETCH_ASSOC)){
                    $ndfkn = $strtnid['id'];
                 $stormfnb = $db_con->prepare("SELECT  clik FROM short WHERE sh_type=7867 AND tp_id=:tp_id  " );
                 $stormfnb->bindParam(":tp_id", $ndfkn);
                 $stormfnb->execute();
                 $sfilenbr=$stormfnb->fetch(PDO::FETCH_ASSOC);
                 $contfils += $sfilenbr['clik'];
                 }

                 if(isset($strname['o_order']) AND ($strname['o_order']>0)){
                   $storepts = $strname['o_order']."&nbspPTS";
                 }else{
                    $storepts = $lang['free'];
                 }

  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="inbox-section">
                    <div class="inbox-grids">
						<div class="col-md-3 inbox-grid">
                        <div class="grid-inbox">
                        <a href="<?php url_site();  ?>/u/<?php echo $catuss['id']; ?>" >

								<div class="inbox-top">
									<div class="inbox-img">
										<img src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" class="img-responsive" alt="">
									</div>
										<div class="inbox-text">
										<h5><?php echo $catuss['username']; check_us($catuss['id']);  ?></h5>
								   </div>
									<div class="clearfix"></div>
								</div>
                                </a>
                                <a class="btn-default compose" ><img src="<?php echo $strname['o_mode']; ?>" class="img-responsive" alt=""></a>

                                <a href="<?php url_site();  ?>/portal" class="compose" ><i class="fa fa-globe"></i> Portal</a>
								<a href="<?php url_site();  ?>/store" class="compose" ><i class="fa fa-shopping-cart"></i> <?php lang('Store');  ?>&nbsp;
                                <span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></a> <br />



           <div class="inbox-bottom">
									<ul>
										<li><a href="<?php url_site();  ?>/message/<?php echo $catuss['id']; ?>"><i class="fa fa-support" aria-hidden="true"></i> <?php lang('helprequest');  ?></a></li>
                                                                               <li><a href="<?php url_site();  ?>/kb/<?php echo $strname['name']; ?>"><i class="fa fa-database" aria-hidden="true"></i> <?php lang('knowledgebase');  ?> </a></li>

                                        <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){  ?>
										<li><a href="<?php url_site();  ?>/update/<?php echo $strname['name']; ?>"><i class="fa fa-refresh" aria-hidden="true"></i> <?php lang('update');  ?></a></li>
                                        <li><a href=""><i class="fa fa-trash" aria-hidden="true"></i>  <?php lang('delete');  ?></a></li>
                                                                                <?php     }  ?>

									</ul>
								</div>

     <?php ads_site(5);  ?>
                           </div>

						</div>
						<div class="col-md-9 inbox-grid1">
							<div class="mailbox-content">
                            <div class="panel panel-primary">
                            <div class="panel-heading"><center><b><?php echo $strname['name']; ?></b>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $storepts; ?></b><br></font></span></center></div>

  <div class="panel-body">
								<div class=" col-md-8 compose-btn">
  <a href="javascript:void(0);" data-toggle="modal" data-target="#Versions" class="btn btn-sm btn-default"><?php lang('Version_nbr');   echo $strtid['name']; ?></a>
<?php
 $catname = $catussc['name'];
 $catname = $lang["{$catname}"];

if(isset($catussc["name"]) AND (($catussc["name"]=="plugins") OR ($catussc["name"]=="templates"))) {
?>
                                    <a class="btn btn-sm btn-primary"  class="btn btn-sm btn-primary" ><b> <?php echo $catname; ?></b></a>
                                  <?php
                  if(isset($catussc['o_mode']) AND ($catussc['o_mode']=="others")){
               ?>
   <a class="btn btn-sm btn-warning"   ><b> <?php lang('others'); ?></b></a>
                   <?php
                  }else{

                  $catval   = $catussc['o_mode'];
                 $settps = $db_con->prepare("SELECT * FROM `options` WHERE id = :id  ");
                 $settps->bindParam(":id", $catval);
                 $settps->execute();
                 $tpstorRow=$settps->fetch(PDO::FETCH_ASSOC);
                  ?>
      <a class="btn btn-sm btn-success"  href="<?php url_site();  ?>/producer/<?php echo $tpstorRow['name']; ?>" ><b> <?php echo $tpstorRow['name']; ?></b></a>

<?php }  }else if(isset($catussc["name"]) AND ($catussc["name"]=="script")){
$scatname = $catussc['o_mode'];
 $scatname = $lang["{$scatname}"];
  ?>
 <a   class="btn btn-sm btn-primary" ><b> <?php echo $catname; ?></b></a>
 <a   class="btn btn-sm btn-info" ><b> <?php echo $scatname; ?></b></a>
<?php   } ?>
 <?php if(isset($_COOKIE['user'])){ ?>
        <a href="javascript:void(0);" data-toggle="modal" data-target="#Download" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfils; ?></b><br></font></span></a>
        <?php }else{ ?>
        <a href="javascript:void(0);" data-toggle="modal" data-target="#Dlogin" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfils; ?></b><br></font></span></a>
        <?php     }  ?>

            <a class="btn btn-sm btn-primary" href="#old_comment"><i class="fa fa-reply"></i> Reply</a>
                                </div>
								<div class="col-md-4 text-right">
                                      <p class="date"> <?php echo $time_stt;  ?></p>
                                  </div>

                                  </div> </div>
                                  <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">
  <?php if(isset($strtid['o_order']) AND ($strtid['o_order'] >= 1)){  ?>
   <div style="background: #EFF0DF;
    border: 1px solid #33CC00;
    border-left: 3px solid #33CC00;
    color: #666;
    page-break-inside: avoid;
    font-family: monospace;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 1.6em;
    max-width: 100%;
    overflow: auto;
    padding: 1em 1.5em;
    display: block;
    word-wrap: break-word;
    border-radius: 10px; " >
    <div style="border: 2px solid #3c0;border-radius: 7px;" class="btn pull-left">
    new <br /> <?php echo $strtid['name']; ?>
    </div>&nbsp;
             <?php echo $strtid['o_valuer']; ?>
   </div>
      <?php     }  ?>
  <div class="topic">
								   <?php echo $comtxt; ?>
                                   <input type="hidden" class="svtxt" name="svtxt" value="1" />

                                 <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){  ?>
                                 <div class="compose-btn pull-right">
                                <br /> <a class="btn btn-sm btn-primary edit" href="javascript:void(0);"><i class="fa fa-pencil-square-o"></i> <?php lang('edit'); ?></a>
                                 </div>
                                  <?php } ?>
                                        <script>
    $(document).ready(function(){
        $('.edit').click(function(){
          $(".edit").html("Loading ...");
   var svtxt=$(".svtxt").val();
  var dataString = 'svtxt='+ svtxt;

  $.ajax
  ({
   type: "POST",
   url: "<?php url_site();  ?>/requests/store_edit.php?id=<?php echo $catdid;  ?>",
   data: dataString,
   cache: false,
   success: function(html)
   {
       $(".edit").html("<i class=\"fa fa-pencil-square-o\"></i> <?php lang('edit'); ?>");
       $(".topic").html(html);
   }
   });

        });
    });
     </script>
                                    </div> <hr />
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
 <?php  if(isset($strname['o_order']) AND ($strname['o_order']>0)){
                   $storeinfo = $strname['o_order']."&nbspPTS";
                 }else{
                    $storeinfo = $lang['tpfree'];
                 }

       echo " <!-- //modal Versions -->
              <div class=\"modal fade\" id=\"Versions\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>&nbsp;{$storeinfo}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">

                                  ";  ?>
                                  <table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center><?php lang('Version_nbr'); ?></center></th>
							  <th><center><?php lang('download');  ?></center></th>
                              <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){ ?>
                              <th><center><?php lang('options');  ?></center></th>
                              <?php } ?>
                            </tr>
						</thead>
						<tbody>
                  <?php
                  $sttidv = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." ORDER BY `o_order`  DESC " );
                  $sttidv->execute();
                  while($strtidv=$sttidv->fetch(PDO::FETCH_ASSOC)){
                 $comtxtv = strip_tags($strtidv['o_valuer'], '');
echo "<tr>
      <td ><center><b>{$strtidv['id']}</b></center></td>
      <td ><center><b data-toggle=\"tooltip\" data-placement=\"left\" title=\"{$comtxtv}\" >{$strtidv['name']}</b></center></td>
      <td><center>";
                 $sdfv = $strtidv['o_mode'];
                 $ndfkv = $strtidv['id'];
                 $dir_lnk_hash_v = $url_site."/download/".hash('crc32', $sdfv.$ndfkv );
                 $contfilsv = 0;
                 $stormfnbv = $db_con->prepare("SELECT  clik FROM short WHERE sh_type=7867 AND tp_id=:tp_id  " );
                 $stormfnbv->bindParam(":tp_id", $ndfkv);
                 $stormfnbv->execute();
                 $sfilenbrv=$stormfnbv->fetch(PDO::FETCH_ASSOC);
                 $contfilsv += $sfilenbrv['clik'];

      if(isset($_COOKIE['user'])){ ?>
<<<<<<< HEAD
        <a href="<?php echo $url_site."/".$sdfv; ?>"  id="V<?php echo $strtidv['id']; ?>" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfilsv; ?></b><br></font></span></a>
=======
        <a href="<?php echo $url_site."/".$sdfv; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $comtxtv; ?>" id="V<?php echo $strtidv['id']; ?>" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfilsv; ?></b><br></font></span></a>
>>>>>>> cd931b3987facfe1aac5b3374aa47d8931ee8f55
        <?php }else{ ?>
        <a href="javascript:void(0);" data-toggle="modal" data-target="#Dlogin" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfilsv; ?></b><br></font></span></a>
        <?php     }
        echo "</center></td>";
        if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
       echo " <td><center></center></td>";
       }
      echo " </tr>
      <script>
<<<<<<< HEAD
=======
      \$(function () {
  \$('[data-toggle=\"tooltip\"]').tooltip()
});
>>>>>>> cd931b3987facfe1aac5b3374aa47d8931ee8f55
     \$(\"document\").ready(function() {
   \$(\"#V{$strtidv['id']}\").click(postlike{$strtidv['id']});

});

function postlike{$strtidv['id']}(){
    \$.ajax({
        url : '$dir_lnk_hash_v',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {

        },
        error : function() {

        }
    });
}
   </script>
   ";
                  }


                    ?>

                               </tbody>
               <tfoot>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center><?php lang('Version_nbr'); ?></center></th>
							  <th><center><?php lang('download');  ?></center></th>
                              <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){ ?>
                              <th><center><?php lang('options');  ?></center></th>
                              <?php } ?>
                            </tr>
						</tfoot>
					</table>
                              <script type="text/javascript">
            $(document).ready(function() {
    $('#tablepagination').DataTable({
      "order": [[0, 'DESC']]
    });
} );
</script>
                                  <?php
                                 echo "
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal Versions  -->";
                          echo " <!-- //modal Download -->
              <div class=\"modal fade\" id=\"Download\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>&nbsp;{$storeinfo}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                   <a  href=\"{$url_site}/{$sdf}\" id=\"D{$strname['id']}\" class=\"btn btn-success\" ><i class=\"fa fa-download\"></i>&nbsp;{$lang['download']}</a>
                                   <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\"><i class=\"fa fa-times-circle\"></i>&nbsp;{$lang['close']}</span></button>
                                    </center>
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>
          <script>
     \$(\"document\").ready(function() {
   \$(\"#D{$strname['id']}\").click(postlike{$strname['id']});

});

function postlike{$strname['id']}(){
    \$.ajax({
        url : '$dir_lnk_hash',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {

        },
        error : function() {

        }
    });
}
   </script>
	   <!-- //modal Download  -->";
    echo " <!-- //modal Dlogin  -->
              <div class=\"modal fade\" id=\"Dlogin\" tabindex=\"-1\" role=\"dialog\">
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

	   <!-- //modal Dlogin  -->";
        ?>
                            <hr />
   <?php if(isset($_COOKIE['user'])){  ?>
                            <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
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
$comment = strip_tags($comment, '<p><a><b><br><li><ul><font><span><pre>');
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
        $('.cmt<?php echo $sutcat['id'];  ?>').html('<form action="<?php url_site();  ?>/requests/f_coment.php?ed=<?php echo $sutcat['id']; ?>" method="POST"><textarea id="editor1" name="comment" class="form-control"><?php echo $ecomment; ?> </textarea><br /><center><button type="submit" name="submit" id ="btnedite<?php echo $sutcat['id'];  ?>" class="btn btn-primary edite<?php echo $sutcat['id'];  ?>" ><i class="fa fa-floppy-o" aria-hidden="true"></i></button></center></form>');
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

<?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>
