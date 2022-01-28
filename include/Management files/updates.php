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
   if(isset($_GET['updates']))
{
   if($_COOKIE['admin']==$hachadmin)
{


  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin_updates');
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
