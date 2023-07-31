<?php if($s_st=="buyfgeufb"){   global $admin_page;
  include "requests/captcha.php";
  if(isset($_GET['st'])){
 $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=0 " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":o_mode", $_GET['kb']);
                 $storknow->bindParam(":name", $_GET['st']);
                 $storknow->execute();
                 $sknowled=$storknow->fetch(PDO::FETCH_ASSOC);
                 if(isset($sknowled['name']) AND ($sknowled['name']==$_GET['st'])){
                  $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->bindParam(":name", $_GET['kb']);
                 $stormt->execute();
                 $tpstorRow=$stormt->fetch(PDO::FETCH_ASSOC);
                 $n_type = "knowledgebase";
                 $n_order = "1";
                 $tonknow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=:o_order " );
                 $tonknow->bindParam(":o_type", $n_type);
                 $tonknow->bindParam(":o_order", $n_order);
                 $tonknow->bindParam(":o_mode", $_GET['kb']);
                 $tonknow->bindParam(":name", $_GET['st']);
                 $tonknow->execute();
                 $stonknow=$tonknow->fetch(PDO::FETCH_ASSOC);
                 $contknow= $stonknow['nbr'];

 ?>

            <div id="page-wrapper" style="min-height: 486px;">
			<div class="main-page">
				<!--buttons-->
				<div class="inbox-section">
                    <div class="inbox-grids">
						<div class="col-md-3 inbox-grid">
                          <div class="grid-inbox">
                            <div class="inbox-top">
                               <div class="inbox-text">
								  <center><h5><b><?php lang('knowledgebase');  ?></b></h5></center>
							   </div>
							</div>
                            <hr />
                            <a class="btn-default compose" href="<?php url_site();  ?>/producer/<?php echo $tpstorRow['name']; ?>" >
                            <img src="<?php echo $tpstorRow['o_mode']; ?>" class="img-responsive" alt="">
                            <br />
                            <font face="Comic Sans MS"><b><?php echo $tpstorRow['name']; ?></b></font>
                            </a>
                          </div>
                           <hr />
						</div>
<div class="col-md-9 inbox-grid1">
 <div class="mailbox-content">
    <div class="panel panel-primary">
            <div class="panel-heading"><center><b><?php echo $sknowled['name']; ?></b></center></div>
            <div class="panel-body">
				  <div class=" col-md-8 compose-btn">
                      <a class="btn btn-sm btn-default" href="<?php url_site();  ?>/pgk/<?php echo $tpstorRow['name']; ?>:<?php echo $sknowled['name']; ?>"><?php lang('pending');  ?>
                      &nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contknow; ?></b><br></font></span></a>
                      <a class="btn btn-sm btn-success" href="<?php url_site();  ?>/producer/<?php echo $tpstorRow['name']; ?>"><b> <?php echo $tpstorRow['name']; ?></b></a>
                      <a class="btn btn-sm btn-primary" href="<?php url_site();  ?>/edk/<?php echo $tpstorRow['name']; ?>:<?php echo $sknowled['name']; ?>">
                      <i class="fa fa-pencil-square-o"></i> <?php lang('edit'); ?></a>
                      <a class="btn btn-sm btn-info" href="<?php url_site();  ?>/hkd/<?php echo $tpstorRow['name']; ?>:<?php echo $sknowled['name']; ?>">
                      <i class="fa fa-history"></i>&nbsp;<?php lang('history');  ?></a>
                  </div>
            </div>
    </div>
 <div class=" col-md-12 inbox-grid1">
  <hr />
  <div class="panel panel-default">
    <div class="panel-body">
     <div class="topic">
	  <p><?php echo $sknowled['o_valuer']; ?></p>
     </div>
    </div>
    <hr />
  </div>
 </div>
</div>
</div>
</div>
</div>
</div>
</div>
 <?php }else{    ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="main-page">
			   <div class="modal-content modal-info">
						<div class="modal-header">
                        <?php echo "<i class=\"fa fa-support \" ></i>&nbsp;".$_GET['kb']; ?>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                       <form  method="POST" action="<?php url_site();  ?>/requests/kb_edit.php" >
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" value="<?php echo $_GET['st']; ?>"  placeholder="<?php lang('name_o');  ?>" aria-describedby="basic-addon1" required>
                       </div>
                <div class="form-row">
                  <!-- FORM ITEM -->
                   <div class="form-item">
                     <!-- FORM INPUT -->
                      <div class="form-input">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="editor1" class="form-control"  rows="15" placeholder="<?php lang('topic');  ?>" required>
                       <?php if_gstore('stxt');  ?></textarea>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/<?php lang('lg'); ?>.js"></script>

                       <script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('editor1');
sceditor.create(textarea, {
	format: 'xhtml',
    locale : 'ar',
<?php
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
$c = 1;
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
  if($c == 1){
  ?> emoticons: {
  dropdown: {
    <?php
  }else if($c == 11){
    ?>
    },
  more: {
    <?php
    }
   ?>
   '<?php echo $smlssen['name'];  ?>': '<?php echo $smlssen['img'];  ?>',
   <?php


$c++; }

if($c >= 2){
  echo "}
  },";
}
 ?>
style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
                       </div>
                       </div>
                       </div>
                           <div class="input-group col-md-2">
                        <span class="input-group-addon" id="basic-addon1"><?php captcha() ;  ?>&nbsp;=&nbsp;</span>
                       <input type="text"  class="form-control" name="capt" required  />
                       </div>
                       <?php if(isset($_SESSION['snotvalid'])){ echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp; ";
     if_gstore('snotvalid');
     echo "</div>" ; }  ?>
                           <center><button  type="submit" name="submit" value="<?php echo $_GET['kb']; ?>" class="btn btn-primary" >
                              <?php lang('save');  ?></button></center>
                           </form>
							</div>
                    	</div>
					</div>
			</div>
		</div>
 <?php } ?>
 <?php }else if(isset($_GET['ed'])){
  $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=0 " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":o_mode", $_GET['tr']);
                 $storknow->bindParam(":name", $_GET['ed']);
                 $storknow->execute();
                 $sknowled=$storknow->fetch(PDO::FETCH_ASSOC);

 ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="main-page">
			   <div class="modal-content modal-info">
						<div class="modal-header">
                        <?php echo $sknowled['o_mode']."&nbsp;<i class=\"fa fa-chevron-right\" aria-hidden=\"true\"></i>&nbsp;".$sknowled['name']; ?>
						</div>
						<div class="modal-body">
							<div class="more-grids">


                       <form  method="POST" action="<?php url_site();  ?>/requests/kb_edit.php?name=<?php echo $_GET['ed']; ?>" >

                <div class="form-row">
                  <!-- FORM ITEM -->
                   <div class="form-item">
                     <!-- FORM INPUT -->
                      <div class="form-input">                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="editor1" class="form-control"  rows="15" placeholder="<?php lang('topic');  ?>" required>
                       <?php if(isset($sknowled['o_valuer'])){ echo $sknowled['o_valuer']; }  ?></textarea>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/<?php lang('lg'); ?>.js"></script>

                       <script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('editor1');
sceditor.create(textarea, {
	format: 'xhtml',
    locale : 'ar',
<?php
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
$c = 1;
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
  if($c == 1){
  ?> emoticons: {
  dropdown: {
    <?php
  }else if($c == 11){
    ?>
    },
  more: {
    <?php
    }
   ?>
   '<?php echo $smlssen['name'];  ?>': '<?php echo $smlssen['img'];  ?>',
   <?php


$c++; }

if($c >= 2){
  echo "}
  },";
}
 ?>
style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
                       </div>
                       </div>
                       </div>
                           <div class="input-group col-md-2">
                        <span class="input-group-addon" id="basic-addon1"><?php captcha() ;  ?>&nbsp;=&nbsp;</span>
                       <input type="text"  class="form-control" name="capt" required  />
                       </div>
                       <?php if(isset($_SESSION['snotvalid'])){ echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp; ";
     if_gstore('snotvalid');
     echo "</div>" ; }  ?>
                           <center><button  type="submit" name="submit" value="<?php echo $_GET['tr']; ?>" class="btn btn-primary" >
                              <?php lang('save');  ?></button></center>
                           </form>
							</div>
                    	</div>
					</div>
			</div>
		</div>
 <?php }else if(isset($_GET['kb'])){
                 $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->bindParam(":name", $_GET['kb']);
                 $stormt->execute();
                 $store=$stormt->fetch(PDO::FETCH_ASSOC);
                 //file
                 $f_type = "store_file";
                 $stormf = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormf->bindParam(":o_type", $f_type);
                 $stormf->bindParam(":o_parent", $store['id']);
                 $stormf->execute();
                 $storefile=$stormf->fetch(PDO::FETCH_ASSOC);
                 // PTS
                 if(isset($store['o_order']) AND ($store['o_order']>0)){
                   $storepts = $store['o_order']."&nbspPTS";
                 }else{
                    $storepts = $lang['free'];
                 }
                  // by User
                 $o_parent = $store['o_parent'];
                 $catusen = $db_con->prepare("SELECT *  FROM users WHERE  id=:id ");
                 $catusen->bindParam(":id",$o_parent );
                 $catusen->execute();
                 $catussen=$catusen->fetch(PDO::FETCH_ASSOC);
                 $servictp = "store_type" ;
                 $catustp = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$servictp}' AND o_parent='{$store['id']}' ) ");
                 $catustp->execute();
                 $catusstp=$catustp->fetch(PDO::FETCH_ASSOC);
                 $catdid=$catusstp['o_order'];
                 $catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (7867) AND tp_id =".$catdid );
                 $catust->execute();
                 $susat=$catust->fetch(PDO::FETCH_ASSOC);
 ?>
     <!-- GRID -->
    <div class="grid">
      <!-- GRID -->
      <div class="grid grid-12 centered">
      <?php tpl_store_stt($susat,0); ?>
				<!--buttons-->
				<div class="grids-section">
				   <div class="panel panel-widget">
				  <div class="inbox-top">
                     <a href="javascript:void(0);" data-toggle="modal" data-target="#addkb" class="btn btn-info" role="button" ><?php lang('add'); ?></a>
								</div>
                                   </div>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">

                <div class="modal fade" id="addkb" data-backdrop="" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
                        <i class="fa fa-info-circle" aria-hidden="true"></i><?php lang('add'); ?>&nbsp;>&nbsp;<?php lang('knowledgebase'); ?>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                                 <center>
                     <form  method="GET" action="<?php url_site();  ?>/store.php" >
                 <input type="text" class="form-control" name="st"   placeholder="<?php lang('name_o');  ?>" aria-describedby="basic-addon1" required>
                 <br />
                 <button  type="submit" name="kb" value="<?php echo $_GET['kb']; ?>" class="btn btn-primary" >
                              <?php lang('add');  ?></button>
                       </form>
                                  </center>
                            <br>

							</div>
						</div>
					</div>
				</div>
			</div>
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Name</th>
                              <th><?php lang('pending');  ?></th>
                            </tr>
						</thead>
						<tbody>
                         <?php
                          $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE o_mode=:o_mode AND o_type=:o_type AND o_order=0 ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":o_mode", $store['name']);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {
                   $n_type = "knowledgebase";
                 $n_order = "1";
                 $tonknow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=:o_order " );
                 $tonknow->bindParam(":o_type", $n_type);
                 $tonknow->bindParam(":o_order", $n_order);
                 $tonknow->bindParam(":o_mode", $sknowled['o_mode']);
                 $tonknow->bindParam(":name", $sknowled['name']);
                 $tonknow->execute();
                 $stonknow=$tonknow->fetch(PDO::FETCH_ASSOC);
                 $contknow= $stonknow['nbr'];
                       echo "<tr>
                              <th>#{$sknowled['id']}</th>
							  <th><a href=\"{$url_site}/kb/{$store['name']}:{$sknowled['name']}\">{$sknowled['name']}</a>
            <div class=\"modal fade\" id=\"{$sknowled['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                    {$sknowled['o_valuer']}
                                  </center>
                            <br>

							</div>
						</div>
					</div>
				</div>
			</div>
                              </th>
                              <th><span class=\"badge badge-info\"><font face=\"Comic Sans MS\"><b>{$contknow}</b><br></font></span></th>
                            </tr>";

                          }
                          ?>
               </tbody>
					</table>
                </div>
				</div>  <div class="clearfix"></div>

				</div>
				</div>
        <?php }else if(isset($_GET['pr']) AND isset($_GET['pg'])){
           $p_type = "knowledgebase";
                 $storknowp = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=0 ORDER BY `id` " );
                 $storknowp->bindParam(":o_type", $p_type);
                 $storknowp->bindParam(":name", $_GET['pg']);
                 $storknowp->bindParam(":o_mode", $_GET['pr']);
                 $storknowp->execute();
                 $sknowledp=$storknowp->fetch(PDO::FETCH_ASSOC);
 ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
				   <div class="panel panel-widget">
				  <div class="inbox-top">
                                       <div class="inbox-text">
                                       <center><h1><?php echo $sknowledp['name']; ?></h1></center>
									 </div>
									<div class="clearfix"></div>
								</div>
              <div class="panel panel-default">
  <div class="panel-body">
    <div class="topic">
               <?php echo $sknowledp['o_valuer']; ?>
  </div>
									<div class="clearfix"></div>
								</div> </div>
                                   </div>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                    <form action="<?php url_site();  ?>/requests/kb_edit.php?pg=<?php echo $_GET['pg']; ?>" method="POST">
                    <table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th></th>
                            </tr>
						</thead>
						<tbody>
                         <?php

                          $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=1 ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":name", $_GET['pg']);
                 $storknow->bindParam(":o_mode", $_GET['pr']);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {

                       echo "<tr>
                              <th><input type=\"radio\" name=\"pg\" value=\"{$sknowled['id']}\" required>&nbsp;#{$sknowled['id']}</th>
							  <th>{$sknowled['o_valuer']}</a></th>

                            </tr>";

                          }
                          ?>
               </tbody>
					</table>

             <?php
             $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->bindParam(":name", $sknowled['o_mode']);
                 $stormt->execute();
                 $tpstorRow=$stormt->fetch(PDO::FETCH_ASSOC);
                    $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
             if((isset($_SESSION['user']) AND ($_SESSION['user']==$sknowledp['o_parent']) )
  OR (isset($_SESSION['user']) AND ($_SESSION['user']==$tpstorRow['o_parent']) )
  OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
    echo "<a href=\"javascript:void(0);\" data-toggle=\"modal\" data-target=\"#replacing\" class=\"btn btn-primary\" role=\"button\">
             <i class=\"fa fa-exchange\"></i>&nbsp;{$lang['replacing']}</a>";
               echo "<div class=\"modal fade\" id=\"replacing\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
                    <div class=\"modal-header\">
                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                             <h2>{$lang['aystqwbc']}</h2>
                    <button  type=\"submit\" name=\"pr\" value=\"{$_GET['pr']}\" class=\"btn btn-primary\" >
                          <i class=\"fa fa-check\" aria-hidden=\"true\"></i>
                    &nbsp;{$lang['confirm']}</button></button>
                                  </center>
                            <br>

							</div>
						</div>
					</div>
				</div>
			</div>";
             }

               ?>
                    </form>
                </div>
				</div>  <div class="clearfix"></div>

				</div>
				</div>
              <?php }else if(isset($_GET['pp']) AND isset($_GET['tt'])){
           $p_type = "knowledgebase";
                 $storknowp = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=0 ORDER BY `id` " );
                 $storknowp->bindParam(":o_type", $p_type);
                 $storknowp->bindParam(":name", $_GET['tt']);
                 $storknowp->bindParam(":o_mode", $_GET['pp']);
                 $storknowp->execute();
                 $sknowledp=$storknowp->fetch(PDO::FETCH_ASSOC);
 ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
				   <div class="panel panel-widget">
				  <div class="inbox-top">
                                       <div class="inbox-text">
                                       <center><h1><?php echo $sknowledp['name']; ?></h1></center>
									 </div>
									<div class="clearfix"></div>
								</div>
              <div class="panel panel-default">
  <div class="panel-body">
    <div class="topic">
               <?php echo $sknowledp['o_valuer']; ?>
  </div>
									<div class="clearfix"></div>
								</div> </div>
                                   </div>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                    <form action="<?php url_site();  ?>/requests/kb_edit.php?pg=<?php echo $_GET['tt']; ?>" method="POST">
                    <table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th></th>
                            </tr>
						</thead>
						<tbody>
                         <?php

                          $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=2 ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":name", $_GET['tt']);
                 $storknow->bindParam(":o_mode", $_GET['pp']);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {

                       echo "<tr>
                              <th><input type=\"radio\" name=\"pg\" value=\"{$sknowled['id']}\" required>&nbsp;#{$sknowled['id']}</th>
							  <th>{$sknowled['o_valuer']}</a></th>

                            </tr>";

                          }
                          ?>
               </tbody>
					</table>

             <?php
             $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->bindParam(":name", $sknowled['o_mode']);
                 $stormt->execute();
                 $tpstorRow=$stormt->fetch(PDO::FETCH_ASSOC);
                    $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
             if((isset($_SESSION['user']) AND ($_SESSION['user']==$sknowledp['o_parent']) )
  OR (isset($_SESSION['user']) AND ($_SESSION['user']==$tpstorRow['o_parent']) )
  OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
    echo "<a href=\"javascript:void(0);\" data-toggle=\"modal\" data-target=\"#recovery\" class=\"btn btn-primary\" role=\"button\">
             <i class=\"fa fa-undo\"></i>&nbsp;{$lang['recovery']}</a>";
               echo "<div class=\"modal fade\" id=\"recovery\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
                    <div class=\"modal-header\">
                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                             <h2>{$lang['aystqwbc']}</h2>
                    <button  type=\"submit\" name=\"pr\" value=\"{$_GET['pp']}\" class=\"btn btn-primary\" >
                    <i class=\"fa fa-check\" aria-hidden=\"true\"></i>
                    &nbsp;{$lang['confirm']}</button>
                                  </center>
                            <br>

							</div>
						</div>
					</div>
				</div>
			</div>";
             }

               ?>
                    </form>
                </div>
				</div>  <div class="clearfix"></div>

				</div>
				</div>
                <?php }else if(isset($_GET['knowledgebase'])){    ?>
<?php if(isset($admin_page) AND ($admin_page==1)){ ?>
<?php template_mine('admin/admin_header');  ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<?php }else{ ?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/03.jpg) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/exchange.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('list'); ?>&nbsp;<?php lang('exvisit'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b><?php lang('ctevbtexp'); ?></b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" onClick="window.open('visits.php?id=<?php user_row('id') ; ?>');" href="javascript:void(0);" >
          <i class="fa fa-exchange nav_icon"></i>&nbsp;<?php lang('exvisit'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions" >
      <a href="https://www.adstn.gq/kb/myads:<?php lang('list'); ?>&nbsp;<?php lang('exvisit'); ?>" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          <?php echo $lang['you_have']."&nbsp;"; user_row('vu'); echo "&nbsp;".$lang['ptvysa']; ?>&nbsp;|&nbsp;
          <?php echo $lang['yshbv']."&nbsp;:&nbsp;"; vu_state_row('visits','vu');  ?>
      </p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="<?php url_site();  ?>/promote?p=exchange" class="button secondary" style="color: #fff;" >
        <i class="fa fa-plus nav_icon"></i>&nbsp;
        <?php lang('add'); ?>
        </a>
        <!-- /BUTTON -->
      </div>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>
<div class="grid grid" >
<?php } ?>
	  <div class="grid-column" >
    <div class="widget-box" >
                     <center> <h1><b><i><?php echo $lang['knowledgebase'];  ?></i></b></h1></center><hr />
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
                              <th><?php lang('topics');  ?></th>
                              <th><?php lang('Store');  ?></th>
							  <th><?php lang('pending');  ?></th>
                            </tr>
						</thead>
						<tbody>
                         <?php
                          $k_type = "knowledgebase";
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE  o_type=:o_type AND o_order=0 ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {
                   $n_type = "knowledgebase";
                 $n_order = "1";
                 $tonknow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM options WHERE name=:name AND o_mode=:o_mode AND o_type=:o_type AND o_order=:o_order " );
                 $tonknow->bindParam(":o_type", $n_type);
                 $tonknow->bindParam(":o_order", $n_order);
                 $tonknow->bindParam(":o_mode", $sknowled['o_mode']);
                 $tonknow->bindParam(":name", $sknowled['name']);
                 $tonknow->execute();
                 $stonknow=$tonknow->fetch(PDO::FETCH_ASSOC);
                 $contknow= $stonknow['nbr'];
                       echo "<tr>
                              <th>#{$sknowled['id']}</th>
							  <th><a href=\"{$url_site}/kb/{$sknowled['o_mode']}:{$sknowled['name']}\">{$sknowled['name']}</a>
            <div class=\"modal fade\" id=\"{$sknowled['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                    {$sknowled['o_valuer']}
                                  </center>
                            <br>

							</div>
						</div>
					</div>
				</div>
			</div>
                              </th>
                              <th><a href=\"{$url_site}/producer/{$sknowled['o_mode']}\">{$sknowled['o_mode']}</a></th>
                              <th><span class=\"badge badge-info\"><font face=\"Comic Sans MS\"><b>{$contknow}</b><br></font></span></th>
                            </tr>";

                          }
                          ?>
               </tbody>
					</table>
                </div>
                </div>
				</div>
              <?php   } ?>
<?php }else{ echo"404"; }  ?>