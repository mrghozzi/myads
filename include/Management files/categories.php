<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){

        //  f_cat List
   if(isset($_GET['f_cat']))
{
   if(isset($_COOKIE['admin']) AND isset($hachadmin) AND ($_COOKIE['admin']==$hachadmin))
{

  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_f_cat');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // f_cat List edite
   if(isset($_GET['f_cat_e']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['f_cat_e'];
		// select image from db to delete
	  if($_POST['ed_submit']){

           $bn_name     = $_POST['name'];
           $bn_icons    = $_POST['icons'];
           $bn_ordercat = $_POST['ordercat'];
           if(isset($_POST['txt'])){
            $bn_txt      = $_POST['txt'];
           }else{
            $bn_txt      = "";
           }


           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_icons)){
			$bnerrMSG = "Please Enter Icons.";
		}
      if(empty($bn_ordercat)){
			$bn_ordercat = "0";
		}
      if(isset($bnerrMSG))
		{
        $bn_get= "?f_cat&bnerrMSG=".$bnerrMSG;
      }
      
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE f_cat SET name=:a_da,icons=:opm,txt=:txt,ordercat=:ordercat
            WHERE id=:ertb ");
			   $stmsb->bindParam(":opm", $bn_icons);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":txt", $bn_txt);
            $stmsb->bindParam(":ordercat", $bn_ordercat);


            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?f_cat");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
  // Ban f_cat_b List
  if(isset($_GET['f_cat_b']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['f_cat_b'];
   $stmt=$db_con->prepare("DELETE FROM f_cat WHERE id=:id  ");
   $stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?f_cat");

 }else{
   header("Location: home");
 }
 }
    // f_cat List ADD
   if(isset($_GET['f_cat_a']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name     = $_POST['name'];
           $bn_icons    = $_POST['icons'];
           $bn_ordercat = $_POST['ordercat'];
           if(isset($_POST['txt'])){
            $bn_txt      = $_POST['txt'];
           }else{
            $bn_txt      = "";
           }

         if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_icons)){
			$bnerrMSG = "Please Enter Icons.";
		}
      if(empty($bn_ordercat)){
			$bn_ordercat = "0";
		}
      if(isset($bnerrMSG))
		{
        $bn_get= "?f_cat&bnerrMSG=".$bnerrMSG;
      }
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("INSERT INTO f_cat (name,icons,txt,ordercat)
            VALUES(:a_da,:opm,:txt,:ordercat) ");
			$stmsb->bindParam(":opm", $bn_icons);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":txt", $bn_txt);
            $stmsb->bindParam(":ordercat", $bn_ordercat);

            if($stmsb->execute()){
             header("Location: admincp?f_cat");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
 }
         //  d_cat List
   if(isset($_GET['d_cat']))
{
   if($_COOKIE['admin']==$hachadmin)
{  
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_d_cat');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
        // f_cat List edite
   if(isset($_GET['d_cat_e']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['d_cat_e'];
		// select image from db to delete
	  if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_sub = $_POST['sub'];
           $bn_ordercat = $_POST['ordercat'];
           if($bn_sub==0){
            $bn_sub="A";
           }
           if($bn_ordercat==0){
            $bn_ordercat="A";
           }

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_sub)){
			$bnerrMSG = "Please Enter Folder.";
		}
         if(empty($bn_ordercat)){
			$bnerrMSG = "Please Enter Order.";
		}
      if(isset($bnerrMSG))
		{
        $bn_get= "?d_cat&bnerrMSG=".$bnerrMSG;
      }
           if(!isset($bnerrMSG))
		{
            if($bn_sub=="A"){
            $bn_sub="0";
           }
           if($bn_ordercat=="A"){
            $bn_ordercat="0";
           }
            $stmsb = $db_con->prepare("UPDATE cat_dir SET name=:name,sub=:sub,ordercat=:ordercat
            WHERE id=:ertb ");
			$stmsb->bindParam(":ordercat", $bn_ordercat);
            $stmsb->bindParam(":sub", $bn_sub);
            $stmsb->bindParam(":name", $bn_name);


            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?d_cat");
         	}



    }else{
      header("Location: admincp{$bn_get}");
    }
    }

 }else {  header("Location: 404");  }
}
  // Ban d_cat_b List
  if(isset($_GET['d_cat_b']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  $bn_id = $_GET['d_cat_b'];
   $stmt=$db_con->prepare("DELETE FROM cat_dir WHERE id=:id  ");
   $stmt->execute(array(':id'=>$bn_id));
    header("Location: admincp?d_cat");

 }else{
   header("Location: home");
 }
 }
       // d_cat List ADD
   if(isset($_GET['d_cat_a']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 if($_POST['ed_submit']){

           $bn_name = $_POST['name'];
           $bn_sub = $_POST['sub'];
           $bn_ordercat = $_POST['ordercat'];

          if($bn_sub==0){
            $bn_sub="A";
           }
           if($bn_ordercat==0){
            $bn_ordercat="A";
           }

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_sub)){
			$bnerrMSG = "Please Enter Folder.";
		}
         if(empty($bn_ordercat)){
			$bnerrMSG = "Please Enter Order.";
		}
      if(isset($bnerrMSG))
		{
        $bn_get= "?d_cat&bnerrMSG=".$bnerrMSG;
      }
           if(!isset($bnerrMSG))
		{
            if($bn_sub=="A"){
            $bn_sub="0";
           }
           if($bn_ordercat=="A"){
            $bn_ordercat="0";
           }
            $bn_statu = "1";
            $bn_txt   = "";
            $bn_metakeywords   = "";
            $stmsb = $db_con->prepare("INSERT INTO cat_dir (name,txt,metakeywords,sub,ordercat,statu)
            VALUES(:name,:txt,:metakeywords,:sub,:ordercat,:statu) ");
            $stmsb->bindParam(":statu", $bn_statu);
			$stmsb->bindParam(":ordercat", $bn_ordercat);
            $stmsb->bindParam(":sub", $bn_sub);
            $stmsb->bindParam(":txt", $bn_txt);
            $stmsb->bindParam(":metakeywords", $bn_metakeywords);
            $stmsb->bindParam(":name", $bn_name);

            if($stmsb->execute()){
             header("Location: admincp?d_cat");
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
