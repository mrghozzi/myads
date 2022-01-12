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
$title_page = $lang['Promotysite'];
        
        if(isset($_POST['le_submit'])){

           $le_name = $_POST['name'];
           $le_url = $_POST['url'];
           $le_type = $_POST['type'];
           $le_desc = $_POST['desc'];
           $le_exch = $_POST['exch'];
           $le_uid = $uRow['id'];

           if(empty($le_name)){
			$errMSG = "Please Enter name.";
		}
         if(empty($le_url)){
			$errMSG = "Please Enter Url.";
		}
        if(empty($le_type)){
			$errMSG = "Please Enter select type ADS .";
		}
         $le_get= "?le_name=".$le_name."&le_url=".$le_url."&le_desc=".$le_desc."&le_exch=".$le_exch."&errMSG=".$errMSG;
           if(!isset($errMSG))
		{
          if(isset($le_type) AND ($le_type =="L")){
            $stms = $db_con->prepare("INSERT INTO `link` (uid,name,url,txt,clik)
            VALUES(:uid,:a_da,:opm,:ptdk,0)");
			$stms->bindParam(":uid", $le_uid);
            $stms->bindParam(":opm", $le_url);
            $stms->bindParam(":a_da", $le_name);
            $stms->bindParam(":ptdk", $le_desc);
         	if($stms->execute()){
             header("Location: l_list.php");
         	}
            }
          if(isset($le_type) AND ($le_type=="E")){
            $stms = $db_con->prepare("INSERT INTO visits (uid,name,url,tims,vu)
            VALUES(:uid,:a_da,:opm,:ptdk,0)");
			$stms->bindParam(":uid", $le_uid);
            $stms->bindParam(":opm", $le_url);
            $stms->bindParam(":a_da", $le_name);
            $stms->bindParam(":ptdk", $le_exch);
         	if($stms->execute()){

             header("Location: v_list.php");
         	}}
    }else{
      header("Location: promote.php{$le_get}");
    }   }
    if(isset($_POST['bn_submit'])){

           $bn_name = $_POST['name'];
           $bn_url = $_POST['url'];
           $bn_img = $_POST['img'];
           $bn_px = $_POST['bn_px'];
           $bn_uid = $uRow['id'];

           if(empty($bn_name)){
			$bnerrMSG = "Please Enter name.";
		}
         if(empty($bn_url)){
			$bnerrMSG = "Please Enter Url.";
		}
        if(empty($bn_img)){
			$bnerrMSG = "Please Enter Image Link.";
		}
         $bn_get= "?bn_name=".$bn_name."&bn_url=".$bn_url."&bn_img=".$bn_img."&bn_px=".$bn_px."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("INSERT INTO banner (uid,name,url,img,px,vu)
            VALUES(:uid,:a_da,:opm,:ptdk,:bn_px,0)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_img);
            $stmsb->bindParam(":bn_px", $bn_px);
         	if($stmsb->execute()){
             header("Location: b_list.php");
         	}



    }else{
      header("Location: promote.php{$bn_get}");
    }
    }
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 $title_page = "404";
 template_mine('404');
}else{
 template_mine('promote');
 }
 template_mine('footer');


?>

