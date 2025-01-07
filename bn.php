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
require_once "dbconfig.php";
include "include/function.php";
$user= $_GET['ID'];
$b_px= $_GET['px'];

  if($b_px=="728"){  $w_px =728; $h_px =90; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; 
  }else if($b_px=="300"){  $w_px =300; $h_px =250; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; 
  }else if($b_px=="160"){  $w_px =160; $h_px =600; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; 
  }else if($b_px=="468"){  $w_px =468; $h_px =60; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; 
  }else if($b_px=="responsive"){  $if_rand = rand(0, 3);   $if_type = 1;
                  if(isset($if_rand) AND ($if_rand==0)){
                     $w_px =468; $h_px =60; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px;
                  }else if(isset($if_rand) AND ($if_rand==1)){
                     $w_px =160; $h_px =600; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px;
                  }else if(isset($if_rand) AND ($if_rand==2)){
                     $w_px =300; $h_px =250; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px;
                  }else if(isset($if_rand) AND ($if_rand==3)){
                     $w_px =728; $h_px =90; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px;
                  }
}

if(isset($w_px) AND is_numeric($user)){
    $stt = $db_con->prepare("SELECT * FROM users where  id=:id " );
     $stt->execute(array(':id'=>$user));
     $userRow=$stt->fetch(PDO::FETCH_ASSOC);
       $ids  = $userRow['id'] ;
     if($_GET['ID']==$userRow['id'])
	{
		$id = $_GET['ID'];

      $stmt = $db_con->prepare("UPDATE users SET pts=pts+1, nvu=nvu+.5 WHERE id=:id");
      $stmt->bindParam(":id", $id);
        if($stmt->execute())
		{   }

     $stm = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM banner where ( uid IN(
  SELECT id FROM users where  nvu >=1 AND NOT(id = '%{$ids}%')
) AND statu=1 ) AND px=:px ORDER BY m " );
     $stm->execute(array(':px'=>$w_px));
     $ab=$stm->fetch(PDO::FETCH_ASSOC);
     echo "document.write(\"" ;
  if (isset($ab['id']))
{
     if(isset($if_type) AND ($if_type==1)){
           echo "<style>.banner_r_$ab[id] {background-image: url('$ab[img]');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_r_$ab[id] a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_r_icon_$ab[id] {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: 728px) {.banner_r_$ab[id] {width: 100%;}}</style><div class='banner_r_$ab[id]'><a href='{$url_site}/show.php?ads=$ab[id]&vu=$userRow[id]' target='_blank'><div class='banner_r_icon_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a><a href='{$url_site}/report?banner={$ab['id']}' target='_blank'><img src='{$url_site}/templates/_panel/img/Alert-icon.png' alt='Report'></a></div></a></div>";
     }else{
           echo "<style>.banner_$ab[id] {background-image: url('$ab[img]');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_$ab[id] a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_icon_$ab[id] {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: {$w_px}px) {.banner_$ab[id] {width: 100%;}}</style><div class='banner_$ab[id]'><a href='{$url_site}/show.php?ads=$ab[id]&vu=$userRow[id]' target='_blank'><div class='banner_icon_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a><a href='{$url_site}/report?banner={$ab['id']}' target='_blank'><img src='{$url_site}/templates/_panel/img/Alert-icon.png' alt='Report'></a></div></a></div>";
     }

 	  $ids = $ab['id'];
      $idu = $ab['uid'];
      $stmo = $db_con->prepare("UPDATE banner SET vu=vu+1  WHERE id=:ids");
      $stmo->bindParam(":ids", $ids);
        if($stmo->execute())
		{     }
      $stmv = $db_con->prepare("UPDATE users SET nvu=nvu-1 WHERE id=:id");
      $stmv->bindParam(":id", $idu);
        if($stmv->execute())
		{     }
            if(empty($_SERVER['HTTP_REFERER'])==NULL){
              $bn_lnk =  $_SERVER['HTTP_REFERER'];
            }else{
              $bn_lnk =  "N";
            }
		    $bn_nm = "banner";
            $bn_dt = time();
            $bn_ag = $_SERVER['HTTP_USER_AGENT'];
            $bn_ip = $_SERVER['REMOTE_ADDR'];
		    $stmsb = $db_con->prepare("INSERT INTO state (sid,pid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $id);
            $stmsb->bindParam(":a_db", $ids);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
         	if($stmsb->execute()){ }
}
else
{
   if(isset($if_type) AND ($if_type==1)){
      echo "<style>.banner_r_$ab[id] {background-image: url('{$url_site}/bnr/{$d_px}.gif');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_r_$ab[id] a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_r_icon_$ab[id] {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: 728px) {.banner_r_$ab[id] {width: 100%;}}</style><div class='banner_r_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><div class='banner_r_icon_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a></div></a></div>";
     }else{
      echo "<style>.banner_$ab[id] {background-image: url('{$url_site}/bnr/{$d_px}.gif');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_$ab[id] a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_icon_$ab[id] {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: {$w_px}px) {.banner_$ab[id] {width: 100%;}}</style><div class='banner_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><div class='banner_icon_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a></div>";
     }     
}

  }else
{
  if(isset($if_type) AND ($if_type==1)){
    echo "<style>.banner_r_$ab[id] {background-image: url('{$url_site}/bnr/{$d_px}.gif');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_r_$ab[id] a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_r_icon_$ab[id] {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: 728px) {.banner_r_$ab[id] {width: 100%;}}</style><div class='banner_r_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><div class='banner_r_icon_$ab[id]'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a></div></a></div>";
   }else{
    echo "<style>.banner_$user {background-image: url('{$url_site}/bnr/{$d_px}.gif');height: {$hh_px}px;width: {$w_px}px;background-size: cover;background-position: center;position: relative;}.banner_$user a {display: block;height: 100%;width: 100%;text-decoration: none;}.banner_icon_$user {position: absolute;top: 0;left: 0;padding: 5px;color: white;background-color: rgba(0, 0, 0, 0.5);}@media screen and (max-width: {$w_px}px) {.banner_$user {width: 100%;}}</style><div class='banner_$user'><a href='{$url_site}?ref={$user}' target='_blank'><div class='banner_icon_$user'><a href='{$url_site}?ref={$user}' target='_blank'><img src='{$url_site}/bnr/logo_w.png' width='16' height='16' alt='{$title_s}'></a></div>";
    }
}
 echo "\");";
}
?>