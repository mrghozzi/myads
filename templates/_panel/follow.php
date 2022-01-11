<?php if($s_st=="buyfgeufb"){
if(isset($_GET['fl'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['fl'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$msgusid=$sus['id'];
 ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('Followers'); ?> @<?php echo $sus['username']; ?></h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                 <table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th></th>
                              <th>Username</th>
							  <th>Time</th>
                            </tr>
						</thead>
						<tbody>
<?php include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 20; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`like` WHERE sid='{$msgusid}' AND type=1 ORDER BY `time_t` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$fgft=$wt['uid'];
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time_t']);
if($catussen['id'] != ""){
echo "<tr>
<td>#{$wt['id']}</td>
  <td><a href=\"{$url_site}/u/{$catussen['id']}\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catussen['img']}\" style=\"width: 35px;\" alt=\"user image\"></a></td>
  <td><b><a href=\"{$url_site}/u/{$catussen['id']}\">{$catussen['username']}</a></b>";
  online_us($catussen['id'])." "; check_us($catussen['id']);
  echo"</td>
  <td><a href=\"{$url_site}/u/{$catussen['id']}\">{$time_cmt}</a></td>
</tr>";
 } }$url=$url_site."/user?fl=".$_GET['fl']."&";
    echo pagination($statement,$per_page,$page,$url);
      ?>
               </tbody>
					</table>
                </div>
				</div> <div class="clearfix"></div>
				</div>
				</div>
<?php }else if(isset($_GET['fg'])){
  $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$_GET['fg'] );
$usz->execute();
$sus=$usz->fetch(PDO::FETCH_ASSOC);
$msgusid=$sus['id'];
 ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('Following'); ?> @<?php echo $sus['username']; ?></h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                 <table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th></th>
                              <th>Username</th>
							  <th>Time</th>
                            </tr>
						</thead>
						<tbody>
<?php include_once('include/pagination.php');
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 20; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`like` WHERE uid='{$msgusid}' AND type=1 ORDER BY `time_t` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$fgft=$wt['sid'];
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$fgft}' ");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($wt['time_t']);
if($catussen['id'] != ""){
echo "<tr>
<td>#{$wt['id']}</td>
  <td><a href=\"{$url_site}/u/{$catussen['id']}\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catussen['img']}\" style=\"width: 35px;\" alt=\"user image\"></a></td>
  <td><b><a href=\"{$url_site}/u/{$catussen['id']}\">{$catussen['username']}</a></b>";
  online_us($catussen['id'])." "; check_us($catussen['id']);
  echo"</td>
  <td><a href=\"{$url_site}/u/{$catussen['id']}\">{$time_cmt}</a></td>
</tr>";
 } }$url=$url_site."/user?fg=".$_GET['fg']."&";
    echo pagination($statement,$per_page,$page,$url);
      ?>
               </tbody>
					</table>
                </div>
				</div> <div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo "404"; }
 }else{ echo "404"; }  ?>