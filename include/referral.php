<?php
#####################################################################
##                                                                 ##
##                        My ads v1.2.x                            ##
##                 http://www.kariya-host.com                      ##
##                 e-mail: admin@kariya-host.com                   ##
##                                                                 ##
##                       copyright (c) 2018                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){ 
include_once('include/pagination.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$uidss = $_SESSION['user'];
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0)  $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`referral` WHERE uid={$uidss} ORDER BY `id` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$startpoint} , {$per_page} " );
$results->execute();
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {


$ssus = $db_con->prepare("SELECT * FROM users WHERE id=".$wt['ruid'] );
$ssus->execute();
$wus=$ssus->fetch(PDO::FETCH_ASSOC);

echo "<tr>
  <td>{$wus['id']}</td>
  <td><a href=\"{$url_site}/u/{$wus['id']}\">{$wus['username']}</a></td>
  <td>{$wt['date']}</td>
  <td>{$wus['pts']}</td>
</tr>";
   }echo pagination($statement,$per_page,$page);


}else{ echo"404"; }
 ?>