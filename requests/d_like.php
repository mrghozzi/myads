<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "../dbconfig.php";
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    //  setting
   $stmt = $db_con->prepare("SELECT *  FROM setting   " );
 $stmt->execute();
 $stt=$stmt->fetch(PDO::FETCH_ASSOC);
  $url_site   = $stt['url'];

    if(isset($_GET['id'])){
          if($_GET['t'] == "f"){
           $bn_time        = time();
           $bn_id          = $_GET['id'];
           $f_like         = $_GET['f_like'];
           $data_reaction  = $_POST['data_reaction'];
           $bn_uid = $_COOKIE['user'];
           $bn_typ = 22;

            $uszunf = $db_con->prepare("SELECT *  FROM `like` WHERE uid=".$bn_uid." AND sid=".$bn_id." AND type=".$bn_typ  );
            $uszunf->execute();
            $susunf=$uszunf->fetch(PDO::FETCH_ASSOC);

    if(isset($susunf['sid']) AND ($susunf['sid']==$bn_id)){

          $o_parent = $susunf['id'];
          $o_type   = "data_reaction";
          $likeuscmr = $db_con->prepare("SELECT  * FROM `options` WHERE o_order='{$bn_uid}' AND o_parent='{$o_parent}' AND  o_type='{$o_type}' " );
          $likeuscmr->execute();
          $usliker=$likeuscmr->fetch(PDO::FETCH_ASSOC);
       if(isset($usliker)  AND ($usliker['o_parent']==$o_parent)){
           if(isset($usliker['o_valuer'])  AND ($usliker['o_valuer']==$data_reaction)){
            $catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid    = $sucat['uid'];
            $bn_time_t = $susunf['time_t'];

            $stmtl=$db_con->prepare("DELETE FROM `like` WHERE id=:id AND uid=:uid ");
        	$stmtl->execute(array(':id'=>$susunf['id'],':uid'=>$bn_uid));
            $stmto=$db_con->prepare("DELETE FROM `options` WHERE id=:id AND o_order=:uid AND o_type=:o_type ");
        	$stmto->execute(array(':id'=>$usliker['id'],':uid'=>$bn_uid,':o_type'=>$o_type));
            if($bn_sid!=$bn_uid){
            $stmtnft=$db_con->prepare("DELETE FROM `notif` WHERE uid=:id AND time=:time AND state=1 ");
        	$stmtnft->execute(array(':id'=>$bn_sid,':time'=>$bn_time_t));
            }
            echo "<svg class=\"post-option-icon icon-thumbs-up\"><use xlink:href=\"#svg-thumbs-up\" ></use></svg>";
           }else{
             $bn_tid = $usliker['id'];
             $o_type = "data_reaction";
             $name   = $data_reaction;
            $stmsbr  = $db_con->prepare("UPDATE options SET name=:name,o_valuer=:o_valuer
            WHERE id=:id AND o_type=:o_type AND o_order=:o_order ");
			$stmsbr->bindParam(":name",     $name);
			$stmsbr->bindParam(":o_type",   $o_type);
			$stmsbr->bindParam(":o_order",  $bn_uid);
            $stmsbr->bindParam(":o_valuer", $data_reaction);
            $stmsbr->bindParam(":id", $bn_tid);
            if($stmsbr->execute()){
      echo   "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/{$data_reaction}.png\"  width=\"30\" alt=\"reaction-{$data_reaction}\">";
              }
           }

     }else{
            $catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid    = $sucat['uid'];
            $bn_time_t = $susunf['time_t'];

            $stmtl=$db_con->prepare("DELETE FROM `like` WHERE id=:id AND uid=:uid ");
        	$stmtl->execute(array(':id'=>$susunf['id'],':uid'=>$bn_uid));
            $stmto=$db_con->prepare("DELETE FROM `options` WHERE id=:id AND o_order=:uid AND o_type=:o_type ");
        	$stmto->execute(array(':id'=>$usliker['id'],':uid'=>$bn_uid,':o_type'=>$o_type));
            if($bn_sid!=$bn_uid){
            $stmtnft=$db_con->prepare("DELETE FROM `notif` WHERE uid=:id AND time=:time AND state=1 ");
        	$stmtnft->execute(array(':id'=>$bn_sid,':time'=>$bn_time_t));
            }
            echo "<svg class=\"post-option-icon icon-thumbs-up\"><use xlink:href=\"#svg-thumbs-up\" ></use></svg>";
           }
    }else if($f_like=="like_up")   {
            $stmsb = $db_con->prepare("INSERT INTO `like` (uid,sid,type,time_t)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_typ);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_id);
            if($stmsb->execute()){
             $bn_tid = $db_con->lastInsertId();
             $o_type ="data_reaction";
             $name   =$data_reaction;
            $stmsbr = $db_con->prepare("INSERT INTO options (name,o_type,o_order,o_parent,o_valuer,o_mode)
            VALUES(:name,:o_type,:o_order,:o_parent,:o_valuer,:o_mode)");
			$stmsbr->bindParam(":name",     $name);
			$stmsbr->bindParam(":o_type",   $o_type);
			$stmsbr->bindParam(":o_order",  $bn_uid);
            $stmsbr->bindParam(":o_valuer", $data_reaction);
            $stmsbr->bindParam(":o_mode",   $bn_time);
            $stmsbr->bindParam(":o_parent", $bn_tid);
            if($stmsbr->execute()){
             $bn_nurl  = "dr".$bn_id;
             $bn_logo  = "thumbs-up";
             $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $catusz = $db_con->prepare("SELECT *  FROM `directory` WHERE statu=1 AND  id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid = $sucat['uid'];
            if(($bn_sid!=$bn_uid) AND ($bn_sid!=0)){
            $bn_name  = $sus['username']." reacted to your post.";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $bn_sid);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){
            $stmsb = $db_con->prepare("UPDATE users SET pts=pts+1
            WHERE id=:usid");
			$stmsb->bindParam(":usid", $bn_sid);
         	if($stmsb->execute()){
               $stmsc = $db_con->prepare("UPDATE users SET pts=pts+2
               WHERE id=:usid");
			   $stmsc->bindParam(":usid", $bn_uid);
         	   if($stmsc->execute()){  }
             }
             }
            }

      echo   "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/{$data_reaction}.png\"  width=\"30\" alt=\"reaction-{$data_reaction}\">";

            }
         	}
      }
  }
 }
}else{ echo"404"; }
?>