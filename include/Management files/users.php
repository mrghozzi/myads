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
   //  admin users
   if(isset($_GET['users']))
{
   if($_COOKIE['admin']==$hachadmin)
{
   template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_users');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }

  // Ban user
  if(isset($_GET['us_ban']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['us_ban'];
	$stmt=$db_con->prepare("DELETE FROM users WHERE id=:id ");
    $stmt->bindParam(":id", $bn_id);
	$stmt->execute();
    header("Location: admincp?users");

 }else{
   header("Location: home");
 }
 }

  // User edite
   if(isset($_GET['us_edit']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['us_edit'];
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
        $stmht_select->bindParam(":id", $id);
		$stmht_select->execute();
		$usRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

         $stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` LIKE :o_order ");
$stausr->bindParam(":o_order", $id);
 $stausr->execute();
 $usrRow=$stausr->fetch(PDO::FETCH_ASSOC);


    if($usRow['id']==$id){
 function eus_echo($name) {  global  $usRow;
  echo $usRow["{$name}"];
 }
 function sus_echo($name) {  global  $usrRow;
  echo $usrRow["{$name}"];
 }
 function eus_selec() {  global  $usRow;
 echo " <option value=\"0\" ";
  if($usRow['ucheck']==0) {echo "selected"; }
  echo " >No</option>";
  echo " <option value=\"1\" ";
  if($usRow['ucheck']==1) {echo "selected"; }
  echo " >Yes</option>";
 }


 if(isset($_POST['ed_submit'])){

           $bn_name  = $_POST['name'];
           $us_mail  = $_POST['mail'];
           $us_pts   = $_POST['pts'];
           $us_uid   = $usRow['id'];
           $us_vu    = $_POST['vu'];
           $bn_nvu   = $_POST['nvu'];
           $us_nlink = $_POST['nlink'];
           $us_check = $_POST['check'];
           $us_slug = $_POST['slug'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($us_mail)){
			$bnerrMSG = "Please Enter Mail.";
		}
        if(empty($us_slug)){
			$bnerrMSG = "Please Enter User Slug.";
		}

         $bn_get= "?us_edit=".$id."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{



            $stmsb = $db_con->prepare("UPDATE users SET username=:a_da,email=:opm,pts=:ptdk ,vu=:vu ,nvu=:nvu ,nlink=:nlink , ucheck=:check
            WHERE id=:ertb");
			$stmsb->bindParam(":opm", $us_mail);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $us_pts);
            $stmsb->bindParam(":vu", $us_vu);
            $stmsb->bindParam(":nvu", $bn_nvu);
            $stmsb->bindParam(":nlink", $us_nlink);
            $stmsb->bindParam(":check", $us_check);

            $stmsb->bindParam(":ertb", $us_uid);
         	if($stmsb->execute()){
 $o_type = "user" ;
 $uid = $us_uid;
 $name = $bn_name;
 $string = urlencode(mb_ereg_replace('\s+', '-', $us_slug));
 $string = str_replace(array(' '),array('-'),$string);
        $ostmsbs = $db_con->prepare("UPDATE `options` SET `name` = :name, `o_valuer` = :a_daf, `o_type` = :o_type WHERE `o_order` = :uid ");
		$ostmsbs->bindParam(":uid", $uid);
        $ostmsbs->bindParam(":o_type", $o_type);
        $ostmsbs->bindParam(":a_daf", $string);
        $ostmsbs->bindParam(":name", $name);
        if($ostmsbs->execute())
		{ header("Location: admincp?us_edit={$us_uid}"); }

         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }
 if(isset($_POST['ps_submit'])){

           $bn_pass  = $_POST['n_pass'];
           $us_uid   = $usRow['id'];

           if(empty($bn_pass)){
			$bnerrMSG = "Please Enter New password.";
		}
        if (strlen($bn_pass) <= '8') {
        $bnerrMSG = "Your New password Must Contain At Least 8 Characters!";
        }

         $bn_get= "?us_edit=".$id."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{
            $passhach = password_hash($bn_pass, PASSWORD_DEFAULT);
            $stmsb = $db_con->prepare("UPDATE users SET pass=:passhach
            WHERE id=:id");
			$stmsb->bindParam(":passhach", $passhach);
            $stmsb->bindParam(":id", $us_uid);
         	if($stmsb->execute())
            {
              header("Location: admincp?us_edit={$us_uid}");
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
 template_mine('admin/admin_header');
 template_mine('admin/admin_us_edit');
 }
 template_mine('footer');

 }

}else{
 header("Location: .../404.php ") ;
}
  ?>
