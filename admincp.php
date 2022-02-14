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
 include "dbconfig.php";
 include "include/function.php";
 include_once('include/pagination.php');
  if(md5($conf_us_log)==$md5_cook_us){

 $stadmin = $db_con->prepare("SELECT * FROM users WHERE id=1 ");
 $stadmin->execute();
 $adminusRow=$stadmin->fetch(PDO::FETCH_ASSOC);
 $conf_admin_log=$adminusRow['id'].$adminusRow['username'].$adminusRow['email'];
if(md5($conf_admin_log)==$_COOKIE['userha'])
{
  if(isset($_GET['cont'])) {  // admin login
    if($_COOKIE['user']==$uRow['id']) {  $eid =$hachadmin;  $nextWeek = time() + (365 * 24 * 60 * 60);  setcookie("admin", $eid, $nextWeek); $rpage ="admincp?home";   header("Location: {$rpage}"); }
    else{    header("Location: home");  }
   }
  if(isset($_GET['dcont'])) {  //  admin logout
   if($_COOKIE['user']==$uRow['id']) { setcookie("admin", "", time()-3600);    header("Location: home");
  }else{  header("Location: home");     }
 }
 $title_page = "Admin cP";
 //  admin Home
  if(isset($_GET['home']))
{
   if($_COOKIE['admin']==$hachadmin)
{
   template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin_home');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
    //  Link ads List
   if(isset($_GET['l_list']))
{
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`link` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function lnk_list() {  global  $results;  global  $statement;   global  $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
        $lus_id = $wt['uid'];
        $stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
		$stmht_select->execute(array(':id'=>$lus_id));
		$lusRow=$stmht_select->fetch(PDO::FETCH_ASSOC);
if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }

echo "<tr>
  <td>{$wt['id']}-{$lusRow['username']}</td>
  <td>{$wt['name']}</td>
  <td>{$wt['url']}</td>
  <td>{$wt['clik']}</td>
  <td>{$fgft}</td>
  <td><a href=\"admincp?state&ty=link&id={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-eye \"></i></a>
  <a href=\"admincp?state&ty=clik&id={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-bar-chart \"></i></a>
  <a href=\"admincp?l_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
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
 $slctRow = $bnRow['px'];

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

         $bn_get= "?l_edit=".$id."&bnerrMSG=".$bnerrMSG;
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
     //  Banners ads List
   if(isset($_GET['b_list']))
{
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`banner` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;   global  $db_con;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
        $lus_id = $wt['uid'];
        $stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
		$stmht_select->execute(array(':id'=>$lus_id));
		$lusRow=$stmht_select->fetch(PDO::FETCH_ASSOC);
if($wt['statu']=="1"){ $fgft="ON"; } else if($wt['statu']=="2"){ $fgft="OFF"; }

echo "<tr>
  <td>{$wt['id']}-{$lusRow['username']}</td>
  <td>{$wt['name']}</td>
  <td>{$wt['vu']}</td>
  <td>{$wt['clik']}</td>
  <td>{$wt['px']}</td>
  <td>{$fgft}</td>
  <td><a href=\"admincp?state&ty=banner&id={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-link \"></i></a>
  <a href=\"admincp?state&ty=vu&id={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-bar-chart \"></i></a>
  <a href=\"admincp?b_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
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
 $slctRow = $bnRow['px'];

 if(isset($_POST['ed_submit'])){

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



  include "include/Management files/settings.php";   //settings
  include "include/Management files/menu.php";   //menu
  include "include/Management files/advertisement.php";   //advertisement
  include "include/Management files/news.php";   //news
  include "include/Management files/state.php";   //state
  include "include/Management files/categories.php";   //categories
  include "include/Management files/report.php";   //report
  include "include/Management files/emojis.php";   //emojis
  include "include/Management files/plugins.php";  //plugins
  include "include/Management files/knowledgebase.php";  //plugins
  include "include/Management files/social_login.php";  //plugins
  include "include/Management files/updates.php";  //plugins
  include "include/Management files/users.php";  //plugins

  $o_type = "Management_files";
  $stmut = $db_con->prepare("SELECT *  FROM options WHERE o_type='{$o_type}' ORDER BY `o_order` DESC" );
  $stmut->execute();
while($mang_fil=$stmut->fetch(PDO::FETCH_ASSOC)){
    include $mang_fil['o_valuer'];
}

} //  END
else{ header("Location: 404"); }
} //  END
else{ header("Location: 404"); }

 ?>
