<?PHP
#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2021                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
require_once "dbconfig.php";
include "include/function.php";
$user= $_GET['ID'];
$b_px= $_GET['px'];

  if($b_px=="728"){  $w_px =728; $h_px =90; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
  if($b_px=="300"){  $w_px =300; $h_px =250; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
  if($b_px=="160"){  $w_px =160; $h_px =600; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
  if($b_px=="468"){  $w_px =468; $h_px =60; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
  if($b_px=="responsive"){  $if_rand = rand(0, 3);   $if_type = 1;
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


    $stt = $db_con->prepare("SELECT *FROM users where  id=:id " );
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
           echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'><link rel='stylesheet' href='{$url_site}/templates/linkads.css'><div><div class='blog-card' style='box-sizing: border-box;'><div class='meta'><div class='photo' href='{$url_site}/show.php?ads=$ab[id]&vu=$userRow[id]' style='background-image: url(".$ab['img'].");'></div><ul class='details'><li ><a href='{$url_site}/show.php?ads=$ab[id]&vu=$userRow[id]'><h1>'$ab[name]'</h1></a></li></ul></div><div class='description'><p class='adstn'>ADS by  <a href='{$url_site}?ref={$user}'>{$title_s}</a></p></div></div></div>";
     }else{
           echo "<table border=0 cellpadding=0 cellspacing=0 width={$w_px}><tr><td width={$w_px} valign='top' align='left'><a target='_top' href='{$url_site}/show.php?ads=$ab[id]&vu=$userRow[id]'><img border=0 src='$ab[img]' width={$w_px} height={$hh_px}></a></td></tr><tr><td  width={$w_px} height=8  bgcolor='#0099ff'><a class='attribution' href='{$url_site}?ref={$user}' target='_blank'><font style='font-size:12px; font-family:verdana,arial,sans-serif; line-height:13px;color:#FFFFFF; text-decoration:none' color='#FFFFFF'>&copy;{$title_s}</font></a></td></tr></table>";

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
            $bn_ip=$_SERVER['REMOTE_ADDR'];
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
{ echo "<table border=0 cellpadding=0 cellspacing=0 width={$w_px}><tr><td width={$w_px} valign='top' align='left'><a target='_top' href='{$url_site}?ref={$user}'><img border=0 src='{$url_site}/bnr/{$d_px}.gif' width={$w_px} height={$hh_px} ></a></td></tr><tr><td  width={$w_px} height=8  bgcolor='#0099ff'><a class='attribution' href='{$url_site}?ref={$user}' target='_blank'><font style='font-size:12px; font-family:verdana,arial,sans-serif; line-height:13px;color:#FFFFFF; text-decoration:none' color='#FFFFFF'>&copy;{$title_s}</font></a></td></tr></table>";
}

  }else
{ echo "<table border=0 cellpadding=0 cellspacing=0 width={$w_px}><tr><td width={$w_px} valign='top' align='left'><a target='_top' href='{$url_site}?ref={$user}'><img border=0 src='{$url_site}/bnr/{$d_px}.gif' width={$w_px} height={$hh_px} ></a></td></tr><tr><td  width={$w_px} height=8  bgcolor='#0099ff'><a class='attribution' href='{$url_site}?ref={$user}' target='_blank'><font style='font-size:12px; font-family:verdana,arial,sans-serif; line-height:13px;color:#FFFFFF; text-decoration:none' color='#FFFFFF'>&copy;{$title_s}</font></a></td></tr></table>";
}
 echo "\");";
?>