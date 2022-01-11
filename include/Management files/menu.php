<?php
#####################################################################
##                                                                 ##
##                        My ads v2.x.x                            ##
##                      http://www.krhost.ga                       ##
##                 e-mail: admin@kariya-host.com                   ##
##                                                                 ##
##                       copyright (c) 2019                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
 //  menu List
   if(isset($_GET['menu']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`menu` WHERE id_m ORDER BY `id_m` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
$results->execute();
function lnk_list() {  global  $results;  global  $statement; global  $per_page; global  $page;  global  $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {

echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?e_menu={$wt['id_m']}\">
  <tr>
  <td>{$wt['id_m']}</td>
  <td><input type=\"text\" class=\"form-control\" name=\"name\" value=\"{$wt['name']}\" autocomplete=\"off\" /></td>
  <td><input type=\"text\" class=\"form-control\" name=\"dir\" value=\"{$wt['dir']}\" autocomplete=\"off\" /></td>
  <td><button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-success\"><i class=\"fa fa-edit \"></i></button>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id_m']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
</tr>
</form> ";
   echo "<div class=\"modal fade\" id=\"ban{$wt['id_m']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <h3>Delete !</h3>
									<p>Sure to Delete ID no {$wt['id_m']} ? </p><br />
                                    <center><a  href=\"admincp?menu_ban={$wt['id_m']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
   }if(isset($_SERVER["HTTP_REFERER"])){
    $url_site =$_SERVER["HTTP_REFERER"];
   }else{
     $url_site = "";
   }
   $url=$url_site.$_SERVER["REQUEST_URI"]."&";
   echo pagination($statement,$per_page,$page,$url);
      }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin_menu');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // menu List edite
   if(isset($_GET['e_menu']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['e_menu'];
		// select image from db to delete
	  if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['dir'];


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}

        $bn_get= "?menu&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE menu SET name=:a_da,dir=:opm
            WHERE id_m=:ertb ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);


            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?menu");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
      // menu List ADD
   if(isset($_GET['a_menu']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['dir'];


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}

        $bn_get= "?menu&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("INSERT INTO menu (name,dir)
            VALUES(:a_da,:opm) ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);

            if($stmsb->execute()){
             header("Location: admincp?menu");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }
     // Ban menu List
  if(isset($_GET['menu_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['menu_ban'];
   $stmt=$db_con->prepare("DELETE FROM menu WHERE id_m=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?menu");

 }else{
   header("Location: home");
 }
 }

}else{
 header("Location: .../404 ") ;
}
  ?>
