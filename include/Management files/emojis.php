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
          //  emojis List
   if(isset($_GET['emojis']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`emojis` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
$results->execute();
function emojis_list() {  global  $results;  global  $statement; global  $per_page; global  $page;  global  $db_con; global $f_awesome; global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {


echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?emojis_e={$wt['id']}\">
  <tr>
  <td>{$wt['id']}</td>
  <td><input type=\"text\" class=\"form-control\" name=\"name\" value=\"{$wt['name']}\" autocomplete=\"off\" /></td>
  <td><input type=\"text\" class=\"form-control\" name=\"img\" value=\"{$wt['img']}\" autocomplete=\"off\" /></td>
  <td><img src=\"{$wt['img']}\" width=\"23\" height=\"23\" /></td>
  <td><button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-success\"><i class=\"fa fa-edit \"></i></button>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
</tr>
</form> ";
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
                                    <center><a  href=\"admincp?emojis_b={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
   }$url=$url_site.$_SERVER["REQUEST_URI"]."&";
   echo pagination($statement,$per_page,$page,$url);
      }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin_emojis');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // emojis List edite
   if(isset($_GET['emojis_e']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['emojis_e'];
		// select image from db to delete
	  if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_img = $_POST['img'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_img)){
			$bnerrMSG = "Please Enter emojis.";
		}

        $bn_get= "?emojis&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE emojis SET name=:name,img=:img
            WHERE id=:ertb ");
			$stmsb->bindParam(":img", $bn_img);
            $stmsb->bindParam(":name", $bn_name);


            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?emojis");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
  // Ban emojis_b List
  if(isset($_GET['emojis_b']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['emojis_b'];
   $stmt=$db_con->prepare("DELETE FROM emojis WHERE id=:id  ");
   $stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?emojis");

 }else{
   header("Location: home");
 }
 }
       // emojis List ADD
   if(isset($_GET['emojis_a']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_img = $_POST['img'];


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_img)){
			$bnerrMSG = "Please Enter Emojis.";
		}


        $bn_get= "?emojis&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{
            if($bn_sub=="A"){
            $bn_sub="0";
           }

            $stmsb = $db_con->prepare("INSERT INTO emojis (name,img)
            VALUES(:name,:img) ");
            $stmsb->bindParam(":img", $bn_img);
            $stmsb->bindParam(":name", $bn_name);

            if($stmsb->execute()){
             header("Location: admincp?emojis");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
