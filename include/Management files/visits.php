<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
      //  Visits ex List
   if(isset($_GET['v_list']))
{
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`visits` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function lnk_list() {  global  $results;  global  $statement;   global  $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
        $lus_id = $wt['uid'];
        $stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
		$stmht_select->execute(array(':id'=>$lus_id));
		$lusRow=$stmht_select->fetch(PDO::FETCH_ASSOC);
if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }
$repvu = array("1","2","3","4");
$repvu_to = array("10s","20s","30s","60s");
$tims_vu = str_replace($repvu,$repvu_to,$wt['tims']);
echo "<tr>
  <td>{$wt['id']}-{$lusRow['username']}</td>
  <td>{$wt['name']}</td>
  <td>{$wt['url']}</td>
  <td>{$wt['vu']}</td>
  <td>{$tims_vu}</td>
  <td>{$fgft}</td>
  <td><a href=\"admincp?v_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
</tr>";
   echo "<div class=\"modal fade\" id=\"ban{$wt['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <h3>Delete !</h3>
									<p>Sure to Delete ID no {$wt['id']} ? </p><br />
                                    <center><a  href=\"admincp?v_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
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
 template_mine('v_list');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
    // Visits Ex edite
   if(isset($_GET['v_edit']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['v_edit'];
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM visits WHERE  id=:did ');
		$stmht_select->execute(array(':did'=>$id));
		$bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

    if($bnRow['id']==$id){
 function bnr_echo($name) {  global  $bnRow;
  echo $bnRow["{$name}"];
 }
 $slctRow = $bnRow['tims'];

 if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['url'];
           $bn_exch = $_POST['exch'];
           $bn_statu = $_POST['statu'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}

         $bn_get= "?v_edit=".$id."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE visits SET name=:a_da,url=:opm,tims=:ptdk,statu=:statu
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_exch);
            $stmsb->bindParam(":statu", $bn_statu);

            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?v_list");
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
 template_mine('v_edit');
 }
 template_mine('footer');

 }
    // Ban Visits Ex
  if(isset($_GET['v_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['v_ban'];
   $stmt=$db_con->prepare("DELETE FROM visits WHERE id=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?v_list");

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
