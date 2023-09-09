<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "dbconfig.php";
include "include/function.php";
$title_page = $lang['list']."&nbsp;".$lang['bannads'];

$uidss = $_SESSION['user'];
if(isset($_GET['ban'])){
  $id = $_GET['ban'];
	$stmt=$db_con->prepare("DELETE FROM banner WHERE id=:id AND uid=:uid ");
	$stmt->execute(array(':id'=>$id,':uid'=>$uidss));
    header("Location: b_list.php");
}


$statement = "`banner` WHERE uid={$uidss} ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;


while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$str_name = mb_strlen($wt['name'], 'utf8');
if($str_name > 25){
   $bnname = substr($wt['name'],0,25)."&nbsp;...";
 }else{
   $bnname = $wt['name'];
 }
 if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$bnname}<hr />
   <a href=\"b_edit.php?id={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
   <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a>
  </td>
  <td><a href=\"state.php?ty=banner&id={$wt['id']}\" class='btn btn-warning' >{$wt['vu']}</a></td>
  <td><a href=\"state.php?ty=vu&id={$wt['id']}\" class='btn btn-primary' >{$wt['clik']}</a></td>
  <td>{$wt['px']}</td>
  <td>{$fgft}</td>
</tr>";
   echo "<div class=\"modal fade\" id=\"ban{$wt['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <h3>Delete !</h3>
									<p>Sure to Delete ID no {$wt['id']} ? </p><br />
                                    <center><a  href=\"b_list.php?ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
   }
   }   


 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('b_list');
 }
 template_mine('footer');


?>

