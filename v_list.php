<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "dbconfig.php";
include "include/function.php";
$title_page = $lang['list']."&nbsp;".$lang['exvisit'];

$uidss = $_SESSION['user'];
if(isset($_GET['ban'])){
  $id = $_GET['ban'];
	$stmt=$db_con->prepare("DELETE FROM visits WHERE id=:id AND uid=:uid ");
	$stmt->execute(array(':id'=>$id,':uid'=>$uidss));
    header("Location: v_list.php");
}


$statement = "`visits` WHERE uid={$uidss} ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function lnk_list() {  global  $results;  global  $statement; global  $lang; global $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }
$repvu = array("1","2","3","4");
$repvu_to = array("10s","20s","30s","60s");
$tims_vu = str_replace($repvu,$repvu_to,$wt['tims']);
$str_name = mb_strlen($wt['name'], 'utf8');
if($str_name > 25){
   $bnname = substr($wt['name'],0,25)."&nbsp;...";
 }else{
   $bnname = $wt['name'];
 }
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$bnname}</td>
  <td>{$wt['vu']}</td>
  <td>{$tims_vu}</td>
  <td>{$fgft}</td>
  <td><a href=\"v_edit.php?id={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
</tr>";
   echo "<div class=\"modal fade\"  id=\"ban{$wt['id']}\" aria-hidden=\"true\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content content-grid\">
                        <div class=\"popup-close-button popup-event-creation-trigger\" data-dismiss=\"modal\" aria-label=\"Close\">
                        <!-- POPUP CLOSE BUTTON ICON -->
                        <svg class=\"popup-close-button-icon icon-cross\">
                        <use xlink:href=\"#svg-cross\"></use>
                        </svg>
                        <!-- /POPUP CLOSE BUTTON ICON -->
                        </div>
                        <p class=\"popup-box-title\"><h2><i class=\"fa fa-trash \"></i>&nbsp;{$lang['delete']}&nbsp;!</h2></p>
                        <hr />
                        <p class=\"popup-event-text\"><b>{$lang['aysywtd']}&nbsp;<kbd>{$wt['name']}</kbd>&nbsp;? </b></p>
                        &nbsp;
                        <a class=\"popup-event-button button tertiary popup-event-information-trigger\" href=\"{$url_site}/v_list?ban={$wt['id']}\"  >{$lang['delete']}</a>
                        &nbsp;
                        <p class=\"popup-event-button button  popup-event-information-trigger\" class=\"btn btn-default\" data-dismiss=\"modal\">
                        <span aria-hidden=\"true\">&times;</span>&nbsp;{$lang['close']}
                        </p>
                    </div>
				</div>
			</div>
          </div>
         ";
   }
}

 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('v_list');
 }
 template_mine('footer');


?>

