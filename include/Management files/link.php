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
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
      //  Link ads List
   if(isset($_GET['l_list']))
{     $admin_page=1;
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`link` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function lnk_list() {  global  $results;  global  $statement;   global  $db_con;  global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
        $lus_id = $wt['uid'];
        $stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
		$stmht_select->execute(array(':id'=>$lus_id));
		$lusRow=$stmht_select->fetch(PDO::FETCH_ASSOC);
if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }
$str_name = mb_strlen($wt['name'], 'utf8');
if($str_name > 25){
   $bnname = substr($wt['name'],0,25)."&nbsp;...";
 }else{
   $bnname = $wt['name'];
 }
  $ty_id = $wt['id'];
 $ty2="link";
$statementv = " `state` WHERE  pid='{$ty_id}' AND t_name='{$ty2}' ";
$resultsv = $db_con->prepare("SELECT COUNT(id) as nbr FROM {$statementv} ");
$resultsv->execute();
$wtv=$resultsv->fetch(PDO::FETCH_ASSOC);
echo "<tr>
  <td>{$wt['id']}&nbsp;-&nbsp;<a href=\"{$url_site}/u/{$lusRow['id']}\">{$lusRow['username']}</a>
  <hr />
  <a href=\"admincp?l_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
  <td>{$bnname}</td>
  <td><a href=\"admincp?state&ty=link&id={$wt['id']}\" class='btn btn-warning' >{$wtv['nbr']}</a></td>
  <td><a href=\"admincp?state&ty=clik&id={$wt['id']}\" class='btn btn-primary' >{$wt['clik']}</a></td>
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
                                    <center><a  href=\"admincp?l_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
   }
      }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('l_list');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
  // Link ADS edite
   if(isset($_GET['l_edit']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['l_edit'];
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM link WHERE  id=:did ');
		$stmht_select->execute(array(':did'=>$id));
		$bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

    if($bnRow['id']==$id){
 function bnr_echo($name) {  global  $bnRow;
  echo $bnRow["{$name}"];
 }
 $statuRow = $bnRow['statu'];
 if(isset($_POST['ed_submit'])){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['url'];
           $bn_desc = $_POST['desc'];
           $bn_statu = $_POST['statu'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}
    if(isset($bnerrMSG))
		{
         $bn_get= "?l_edit=".$id."&bnerrMSG=".$bnerrMSG;
    }
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE link SET name=:a_da,url=:opm,txt=:ptdk,statu=:statu
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_desc);
            $stmsb->bindParam(":statu", $bn_statu);

            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?l_list");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }else {  header("Location: 404");  }
 //  template
   template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('l_edit');
 }
 template_mine('footer');

 }
  // Ban Link
  if(isset($_GET['l_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['l_ban'];
   $stmt=$db_con->prepare("DELETE FROM link WHERE id=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?l_list");

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
