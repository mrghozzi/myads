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

include "dbconfig.php";
include "include/function.php";
$title_page = "Banners Ads";

$uidss = $_SESSION['user'];
   if(isset($_GET['id']) && !empty($_GET['id']))
	{
		$id = $_GET['id'];
		// select image from db to delete
		$stmht_select = $db_con->prepare('SELECT * FROM banner WHERE uid=:uid AND id=:did ');
		$stmht_select->execute(array(':uid'=>$uidss,':did'=>$id));
		$bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);

    if($bnRow['uid']==$uidss){
 function bnr_echo($name) {  global  $bnRow;
  echo $bnRow["{$name}"];
 }
 $slctRow = $bnRow['px'];

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
         $bn_get= "?id=".$id."&bnerrMSG=".$bnerrMSG;
           if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE banner SET name=:a_da,url=:opm,img=:ptdk,px=:bn_px
            WHERE id=:ertb AND uid=:uid");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_url);
            $stmsb->bindParam(":a_da", $bn_name);
            $stmsb->bindParam(":ptdk", $bn_img);
            $stmsb->bindParam(":bn_px", $bn_px);
            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: b_list.php");
         	}



    }else{
      header("Location: b_edit.php{$bn_get}");
    }
    }

 }else {  header("Location: 404.php");  }
 }else {  header("Location: 404.php");  }




 template_mine('header');
  if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('b_edit');
 }
 template_mine('footer');


?>

