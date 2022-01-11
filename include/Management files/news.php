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

     //  news List
   if(isset($_GET['news']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`news` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
$results->execute();
function lnk_list() {  global  $results;  global  $statement; global  $per_page; global  $page;  global  $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$news_time=date('Y-m-d',$wt['date']);
echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?e_news={$wt['id']}\">
  <tr>
  <td>{$wt['id']}</td>
  <td>{$news_time}</td>
  <td><input type=\"text\" class=\"form-control\" name=\"name\" value=\"{$wt['name']}\" autocomplete=\"off\" /></td>
  <td><textarea type=\"text\" class=\"form-control\" name=\"txt\"  autocomplete=\"off\">{$wt['text']}</textarea></td>
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
                                    <center><a  href=\"admincp?news_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
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
 template_mine('admin_news');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // News List edite
   if(isset($_GET['e_news']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['e_news'];
		// select image from db to delete
	  if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_txt = $_POST['txt'];


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_txt)){
			$bnerrMSG = "Please Enter Text.";
		}

        $bn_get= "?news&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE news SET name=:a_da,text=:opm
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $bn_txt);
            $stmsb->bindParam(":a_da", $bn_name);


            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?news");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
      // news List ADD
   if(isset($_GET['a_news']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_txt = $_POST['txt'];
           $bn_date = time();


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_txt)){
			$bnerrMSG = "Please Enter Text.";
		}

        $bn_get= "?news&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("INSERT INTO news (name,date,text,statu)
            VALUES(:a_da,:dta,:opm,1) ");
			$stmsb->bindParam(":opm", $bn_txt);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":dta", $bn_date);

            if($stmsb->execute()){ 
            $nws_tid = $db_con->lastInsertId();
       
            $bn_type = "3";
            $bn_nuid = "1";
            $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
		$stmsbs->bindParam(":uid",   $bn_nuid );
            $stmsbs->bindParam(":opm", $bn_type);
            $stmsbs->bindParam(":a_da", $bn_date);
            $stmsbs->bindParam(":ptdk", $nws_tid);
            if($stmsbs->execute()){
             header("Location: admincp?news");
         	}
            }



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }
     // Ban news List
  if(isset($_GET['news_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['news_ban'];
   $stmt=$db_con->prepare("DELETE FROM news WHERE id=:id  ");
	$stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?news");

 }else{
   header("Location: home");
 }
 }

}else{
 header("Location: .../404.php ") ;
}
  ?>
