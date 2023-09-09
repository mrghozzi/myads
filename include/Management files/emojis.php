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
          //  emojis List
   if(isset($_GET['emojis']))
{
   if($_COOKIE['admin']==$hachadmin)
{

$statement = "`emojis` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function emojis_list() {  global  $results;  global  $statement; global  $per_page; global  $page;  global  $db_con; global $f_awesome; global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {


echo "
  <tr>
  <td><center><b>{$wt['id']}</b></center></td>
  <td><center><b>{$wt['name']}</b></center></td>
  <td><center><img src=\"{$wt['img']}\" width=\"23\" height=\"23\" /></center></td>
  <td><center><a href=\"#\" data-toggle=\"modal\" data-target=\"#ed{$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></center></td>
</tr>  ";
echo "<div class=\"modal fade\" id=\"ed{$wt['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
 <form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp.php?emojis_e={$wt['id']}\">
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Emoji Shortcut</span>
  <input type=\"text\" class=\"form-control\" name=\"name\" value=\"{$wt['name']}\" autocomplete=\"off\" />
  </div>
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Emojis Icon Link</span>
  <input type=\"text\" class=\"form-control\" name=\"img\" value=\"{$wt['img']}\" autocomplete=\"off\" /></div>
  <div class=\"input-group\">
 <center><button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-info\"><i class=\"fa fa-plus \"></i></button></center>
 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button
 </div>
                                    </form>
                             </div>
						</div>
					</div>
				</div>
			</div>  </div>";
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
                                    <center><a  href=\"admincp?emojis_b={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
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
 template_mine('admin/admin_header');
 template_mine('admin/admin_emojis');
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
