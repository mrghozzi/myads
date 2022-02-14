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
 if($_GET['ads'])
 {
$bn= $_GET['ads'];

    if($_GET['vu'])
	{

    $query = $db_con->prepare("SELECT * FROM banner WHERE id=".$bn);
    $query->execute();
    $banner=$query->fetch(PDO::FETCH_ASSOC);

     $stmt = $db_con->prepare("UPDATE banner SET clik=clik+1 WHERE id=:id");
     $stmt->bindParam(":id", $bn);
     if($stmt->execute())
		{     }

       $id = $_GET['vu'];
      $stmt = $db_con->prepare("UPDATE users SET pts=pts+2 WHERE id=:id");
      $stmt->bindParam(":id", $id);
        if($stmt->execute())
		{     }
     $bn_nm = "vu";
            if(empty($_SERVER['HTTP_REFERER'])==NULL){
              $bn_lnk =  $_SERVER['HTTP_REFERER'];
            }else{
              $bn_lnk =  "N";
            }
            $bn_dt = time();
            $bn_ag = $_SERVER['HTTP_USER_AGENT'];
            $bn_ip=$_SERVER['REMOTE_ADDR'];
            $idp="0";
		    $stmsb = $db_con->prepare("INSERT INTO state (pid,sid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $bn);
            $stmsb->bindParam(":a_db", $id);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
         	if($stmsb->execute()){ }
 }
  @header("Location: {$banner['url']}");
   }
 if($_GET['link'])
 {
$bn= $_GET['link'];

    if($_GET['clik'])
	{

    $query = $db_con->prepare("SELECT * FROM link WHERE id=".$bn);
    $query->execute();
    $link=$query->fetch(PDO::FETCH_ASSOC);
     $stmt = $db_con->prepare("UPDATE link SET clik=clik+1 WHERE id=:id");
     $stmt->bindParam(":id", $bn);
     if($stmt->execute())
		{     }

      $id = $_GET['clik'];
      $stmt = $db_con->prepare("UPDATE users SET pts=pts+2,nlink=nlink+.5 WHERE id=:id");
      $stmt->bindParam(":id", $id);
        if($stmt->execute())
		{     }
      $bn_nm = "clik";
            if(empty($_SERVER['HTTP_REFERER'])==NULL){
              $bn_lnk =  $_SERVER['HTTP_REFERER'];
            }else{
              $bn_lnk =  "N";
            }
            $bn_dt = time();
            $bn_ag = $_SERVER['HTTP_USER_AGENT'];
            $bn_ip=$_SERVER['REMOTE_ADDR'];
            $idp="0";
		    $stmsb = $db_con->prepare("INSERT INTO state (pid,sid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $bn);
            $stmsb->bindParam(":a_db", $id);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
         	if($stmsb->execute()){ }
         }
  @header("Location: {$link['url']}");
   }

?>