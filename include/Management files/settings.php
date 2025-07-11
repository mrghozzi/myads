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
  // settings edite
   if(isset($_GET['settings']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = 1;
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM setting WHERE  id=:did ');
		$stmht_select->execute(array(':did'=>$id));
		$bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

    if($bnRow['id']==$id){
   function bnr_echo($name) {  global  $bnRow;
  echo $bnRow["{$name}"];
 }

 if(isset($_POST['ed_submit'])){

           $bn_name     = $_POST['name'];
           $bn_url      = $_POST['url'];
           $bn_desc     = $_POST['desc'];
           $bn_styles   = $_POST['styles'];
           $bn_lang     = $_POST['lang'];
           $bn_timezone = $_POST['timezone'];
           $bn_a_mail   = $_POST['a_mail'];
           $bn_e_links   = $_POST['e_links'];
           $bn_facebook = "#";
           $bn_twitter  = "#";
           $bn_linkedin = "#";

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter site name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}
        if(empty($bn_styles)){
			$bnerrMSG = "Please Enter Template.";
		}
        if(empty($bn_lang)){
			$bnerrMSG = "Please Enter Language Default.";
		}

    if(isset($bnerrMSG))
		{
         $bn_get= "?settings&bnerrMSG=".$bnerrMSG;
    }
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE setting SET titer=:a_da,url=:opm,description=:ptdk,styles=:styles
            ,lang=:lang ,timezone=:timezone ,a_mail=:a_mail ,e_links=:e_links ,facebook=:facebook ,twitter=:twitter ,linkedin=:linkedin
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_desc);
            $stmsb->bindParam(":styles", $bn_styles);
            $stmsb->bindParam(":lang", $bn_lang);
            $stmsb->bindParam(":timezone", $bn_timezone);
            $stmsb->bindParam(":a_mail", $bn_a_mail);
            $stmsb->bindParam(":e_links", $bn_e_links);
            $stmsb->bindParam(":facebook", $bn_facebook);
            $stmsb->bindParam(":twitter", $bn_twitter);
            $stmsb->bindParam(":linkedin", $bn_linkedin);

            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?settings");
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
 template_mine('admin/admin_settings');
 }
 template_mine('footer');

 }

}else{
 header("Location: .../404.php ") ;
}
  ?>
