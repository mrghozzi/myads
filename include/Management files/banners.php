<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
     //  Banners ads List
   if(isset($_GET['b_list']))
{     $admin_page=1;
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`banner` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;   global  $db_con;  global  $url_site;
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
echo "<tr>
  <td>{$wt['id']}&nbsp;-&nbsp;<a href=\"{$url_site}/u/{$lusRow['id']}\">{$lusRow['username']}</a>
  <hr />
  <a href=\"admincp?b_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a>
  </td>
  <td>{$bnname}</td>
  <td>{$wt['vu']}<hr /><a href=\"admincp?state&ty=banner&id={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-link \"></i></a></td>
  <td>{$wt['clik']}<hr /><a href=\"admincp?state&ty=vu&id={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-bar-chart \"></i></a> </td>
  <td>{$wt['px']}</td>
  <td>{$fgft}</td>
  <td>
  </td>
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
                                    <center><a  href=\"admincp?b_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
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
 template_mine('b_list');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
   // Banners ADS edite
   if(isset($_GET['b_edit']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['b_edit'];
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM banner WHERE  id=:did ');
		$stmht_select->execute(array(':did'=>$id));
		$bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

    if($bnRow['id']==$id){
 function bnr_echo($name) {  global  $bnRow;
  echo $bnRow["{$name}"];
 }
 $slctRow  = $bnRow['px'];
 $statuRow = $bnRow['statu'];

 if(isset($_POST['bn_submit'])){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['url'];
           $bn_img = $_POST['img'];
           $bn_px = $_POST['bn_px'];
           $bn_statu = $_POST['statu'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}

         $bn_get= "?b_edit=".$id."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE banner SET name=:a_da,url=:opm,img=:ptdk,px=:bn_px,statu=:statu
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_img);
            $stmsb->bindParam(":bn_px", $bn_px);
            $stmsb->bindParam(":statu", $bn_statu);

            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?b_list");
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
 template_mine('b_edit');
 }
 template_mine('footer');

 }
   // Ban Banners
  if(isset($_GET['b_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['b_ban'];
   $stmt=$db_con->prepare("DELETE FROM banner WHERE id=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?b_list");

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
