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
 //  menu List
   if(isset($_GET['social_login']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 $login_ext = "ext_login_ext";

$statement = "`options` WHERE o_type=:o_type ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->bindParam(":o_type", $login_ext);
$results->execute();
function lnk_list() {  global  $results;  global  $db_con;
$con_lst = 0 ;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {

echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?e_social_login={$wt['id']}\">
  <tr>
  <td>{$wt['id']}</td>
  <td>{$wt['name']}</td>
  <td><input type=\"text\" class=\"form-control\" name=\"o_valuer\" value=\"{$wt['o_valuer']}\" autocomplete=\"off\" /></td>
  <td><input type=\"text\" class=\"form-control\" name=\"o_mode\" value=\"{$wt['o_mode']}\" autocomplete=\"off\" /></td>
  <td><div class=\"btn-group-vertical\">
  <button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-success\"><i class=\"fa fa-edit \"></i></button>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a>
  </div></td>
</tr>
</form> ";
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
                                    <center><a  href=\"admincp?social_login_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
            $con_lst ++ ;
   }if(isset($con_lst) AND ($con_lst == 0)){
     // Wasp TOP  //
     echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?a_social_login=WASP\">
  <tr>
  <td>&Oslash;</td>
  <td>WASP</td>
  <td><input type=\"text\" class=\"form-control\" name=\"o_valuer\"    required/></td>
  <td><input type=\"text\" class=\"form-control\" name=\"o_mode\"   required/></td>
  <td><div class=\"btn-group-vertical\">
  <button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-info\"><i class=\"fa fa-plus \"></i></button>
  <a href=\"https://www.adstn.gq/kb/myads:social_login:wasp\"  class='btn btn-warning' ><i class=\"fa fa-question-circle \"></i></a>
  </div></td>
</tr>
</form> ";

     // Wasp AND //
   }
       }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_social_login');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // menu List edite
   if(isset($_GET['e_social_login']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['e_social_login'];
		// select image from db to delete
	  if($_POST['ed_submit']){
           $eestatement = "`options` WHERE id=:id ";
           $estatement =$db_con->prepare("SELECT * FROM {$eestatement} ");
           $estatement->bindParam(":id", $id);
           $estatement->execute();
           $wte=$estatement->fetch(PDO::FETCH_ASSOC);
           $bn_name = $wte['name'];
           $bn_o_valuer = $_POST['o_valuer'];
           $bn_o_mode  = $_POST['o_mode'];

           if(empty($bn_o_valuer)){
			$bnerrMSG = "Please Enter App ID.";
		}
         if(empty($bn_o_mode)){
			$bnerrMSG = "Please Enter App secret.";
		}


           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE options SET o_valuer=:o_valuer,o_mode=:o_mode
            WHERE id=:ertb ");
			$stmsb->bindParam(":o_valuer", $bn_o_valuer);
			$stmsb->bindParam(":o_mode", $bn_o_mode);
            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
         	  $bn_x_type   = "login_ext";
              $bn_x_mode   = "{$bn_name}_login_ext";
              $bn_x_valuer = "<a class=\"social-link discord\" href=\"https://www.wasp.gq/oauth?app_id={$bn_o_valuer}\"><img src=\"{$url_site}/templates/_panel/img/icons/wasp.png\" /></a>";
             $stmsbx = $db_con->prepare("UPDATE options SET o_valuer=:o_valuer
            WHERE ( o_type=:o_type AND o_mode=:o_mode ) ");
			$stmsbx->bindParam(":o_valuer", $bn_x_valuer);
			$stmsbx->bindParam(":o_mode", $bn_x_mode);
            $stmsbx->bindParam(":o_type", $bn_x_type);
         	if($stmsbx->execute()){
              header("Location: admincp?social_login");
         	}
         	}



    }else{
      $bn_get= "?social_login&bnerrMSG=".$bnerrMSG;
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
      // menu List ADD
   if(isset($_GET['a_social_login']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name = $_GET['a_social_login'];
           $bn_o_valuer = $_POST['o_valuer'];
           $bn_o_mode  = $_POST['o_mode'];

           if(empty($bn_o_valuer)){
			$bnerrMSG = "Please Enter App ID.";
		}
         if(empty($bn_o_mode)){
			$bnerrMSG = "Please Enter App secret.";
		}


           if(!isset($bnerrMSG))
		{
            $bn_o_type = "ext_login_ext";
            $stmsb = $db_con->prepare("INSERT INTO options (name,o_valuer,o_mode,o_type)
            VALUES(:name,:o_valuer,:o_mode,:o_type) ");
			$stmsb->bindParam(":o_valuer", $bn_o_valuer);
			$stmsb->bindParam(":o_mode", $bn_o_mode);
            $stmsb->bindParam(":name", $bn_name);
            $stmsb->bindParam(":o_type", $bn_o_type);

            if($stmsb->execute()){
              $bn_x_type   = "login_ext";
              $bn_x_mode   = "{$bn_name}_login_ext";
              $bn_x_valuer = "<a class=\"social-link discord\" href=\"https://www.wasp.gq/oauth?app_id={$bn_o_valuer}\"><img src=\"{$url_site}/templates/_panel/img/icons/wasp.png\" /></a>";
           $stmsex = $db_con->prepare("INSERT INTO options (name,o_valuer,o_mode,o_type)
            VALUES(:name,:o_valuer,:o_mode,:o_type) ");
			$stmsex->bindParam(":o_valuer", $bn_x_valuer);
			$stmsex->bindParam(":o_mode", $bn_x_mode);
            $stmsex->bindParam(":name", $bn_name);
            $stmsex->bindParam(":o_type", $bn_x_type);

            if($stmsex->execute()){
           header("Location: admincp?social_login");
         	}
         	}



    }else{
      $bn_get= "?social_login&bnerrMSG=".$bnerrMSG;
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }
     // Ban menu List
  if(isset($_GET['social_login_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['social_login_ban'];
$dstatement = "`options` WHERE id=:id ";
$dresults =$db_con->prepare("SELECT * FROM {$dstatement} ");
$dresults->bindParam(":id", $bn_id);
$dresults->execute();
$wtd=$dresults->fetch(PDO::FETCH_ASSOC);
    $stmt=$db_con->prepare("DELETE FROM options WHERE id=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    $bn_d_type   = "login_ext";
    $bn_d_mode   = "{$wtd['name']}_login_ext";
    $stmtm=$db_con->prepare("DELETE FROM options WHERE o_mode=:o_mode AND o_type=:o_type  ");
	$stmtm->execute(array(':o_type'=>$bn_d_type,':o_mode'=>$bn_d_mode));
    header("Location: admincp?social_login");

 }else{
   header("Location: home");
 }
 }

}else{
 header("Location: .../404 ") ;
}
  ?>
